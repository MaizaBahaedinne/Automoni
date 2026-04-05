<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class LinkedInController extends BaseController
{
    private const AUTH_URL     = 'https://www.linkedin.com/oauth/v2/authorization';
    private const TOKEN_URL    = 'https://www.linkedin.com/oauth/v2/accessToken';
    private const USERINFO_URL = 'https://api.linkedin.com/v2/userinfo';
    private const ME_URL       = 'https://api.linkedin.com/v2/me';

    // ──────────────────────────────────────────────────────────────────────────
    // Redirect the user to LinkedIn's OAuth authorisation page
    // ──────────────────────────────────────────────────────────────────────────
    public function connect(): RedirectResponse
    {
        $clientId = env('LINKEDIN_CLIENT_ID');
        if (empty($clientId)) {
            return redirect()->to('profile/edit')
                             ->with('error', lang('App.linkedin_not_configured'));
        }

        // CSRF protection: generate a random state token and store it
        $state = bin2hex(random_bytes(16));
        session()->set('linkedin_oauth_state', $state);
        session()->set('linkedin_oauth_source', 'import'); // import vs login

        $params = http_build_query([
            'response_type' => 'code',
            'client_id'     => $clientId,
            'redirect_uri'  => base_url('linkedin/callback'),
            'state'         => $state,
            'scope'         => 'openid profile email',
        ]);

        return redirect()->to(self::AUTH_URL . '?' . $params);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Start LinkedIn Login / Signup flow (public — no auth required)
    // ──────────────────────────────────────────────────────────────────────────
    public function loginConnect(): RedirectResponse
    {
        $clientId = env('LINKEDIN_CLIENT_ID');
        if (empty($clientId)) {
            return redirect()->to('login')
                             ->with('error', lang('App.linkedin_not_configured'));
        }

        $state = bin2hex(random_bytes(16));
        session()->set('linkedin_oauth_state', $state);
        session()->set('linkedin_oauth_source', 'login'); // distinguish from import

        // Store the requested role if provided (for signup flow)
        $role = $this->request->getGet('role');
        if (in_array($role, ['job_seeker', 'recruiter'], true)) {
            session()->set('linkedin_signup_role', $role);
        }

        $params = http_build_query([
            'response_type' => 'code',
            'client_id'     => $clientId,
            'redirect_uri'  => base_url('linkedin/callback'),
            'state'         => $state,
            'scope'         => 'openid profile email',
        ]);

        return redirect()->to(self::AUTH_URL . '?' . $params);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Handle the OAuth callback from LinkedIn
    // ──────────────────────────────────────────────────────────────────────────
    public function callback(): RedirectResponse
    {
        $source = session()->get('linkedin_oauth_source') ?? 'login';
        $errDest = ($source === 'import') ? 'profile/edit' : 'login';

        // User cancelled the authorisation dialog
        if ($this->request->getGet('error')) {
            return redirect()->to($errDest)
                             ->with('error', 'LinkedIn connection was cancelled.');
        }

        $code  = $this->request->getGet('code');
        $state = $this->request->getGet('state');

        // Validate CSRF state
        $storedState = session()->get('linkedin_oauth_state');
        if (empty($state) || empty($storedState)) {
            return redirect()->to('login')
                             ->with('error', 'Your session expired. Please try LinkedIn login again.');
        }
        if ($state !== $storedState) {
            return redirect()->to($errDest)
                             ->with('error', 'Invalid OAuth state — please try again.');
        }
        session()->remove('linkedin_oauth_state');
        session()->remove('linkedin_oauth_source');

        if (empty($code)) {
            return redirect()->to($errDest)
                             ->with('error', 'No authorisation code received from LinkedIn.');
        }

        // Exchange code for token
        $token = $this->getAccessToken($code);
        if ($token === null) {
            return redirect()->to($errDest)
                             ->with('error', 'Failed to obtain LinkedIn access token — please try again.');
        }

        // Fetch user info (name, email, picture, sub)
        $userInfo = $this->fetchUserInfo($token);
        if ($userInfo === null) {
            return redirect()->to($errDest)
                             ->with('error', 'Failed to fetch LinkedIn profile data.');
        }

        // ── Route to correct handler based on source ──────────────────────────
        if ($source === 'import') {
            $meData   = $this->fetchMe($token);
            $imported = $this->importProfile($userInfo, $meData);
            return redirect()->to('profile/edit')
                             ->with('success', lang('App.linkedin_import_success') . ' ' . implode(', ', $imported) . '.');
        }

        // ── Login / Signup flow ───────────────────────────────────────────────
        return $this->loginWithLinkedIn($userInfo);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Find or create a user from LinkedIn data, then log them in
    // ──────────────────────────────────────────────────────────────────────────
    private function loginWithLinkedIn(array $li): RedirectResponse
    {
        $userModel    = model(\App\Models\UserModel::class);
        $profileModel = model(\App\Models\ProfileModel::class);

        $linkedinId = $li['sub'] ?? null;
        $email      = $li['email'] ?? null;

        if (empty($linkedinId) || empty($email)) {
            return redirect()->to('login')
                             ->with('error', 'LinkedIn did not return the required profile data.');
        }

        // 1. Find by linkedin_id (returning user)
        $user = $userModel->where('linkedin_id', $linkedinId)->first();

        // 2. Find by email (existing account without linkedin_id linked yet)
        if (!$user) {
            $user = $userModel->findByEmail($email);
            if ($user) {
                // Link the linkedin_id to the existing account
                $userModel->update($user->id, ['linkedin_id' => $linkedinId]);
                $user = $userModel->find($user->id);
            }
        }

        // 3. Create new account
        if (!$user) {
            $role = session()->get('linkedin_signup_role') ?? 'job_seeker';
            session()->remove('linkedin_signup_role');

            $firstName = $li['given_name']  ?? 'User';
            $lastName  = $li['family_name'] ?? '';

            // Generate a random secure password (user logs in via LinkedIn, not password)
            $randomPassword = bin2hex(random_bytes(16));

            $userId = $userModel->insert([
                'first_name'     => $firstName,
                'last_name'      => $lastName,
                'email'          => $email,
                'password'       => $randomPassword,
                'role'           => $role,
                'email_verified' => 1,
                'linkedin_id'    => $linkedinId,
            ]);

            // Create profile and download avatar
            $profileData = ['user_id' => $userId];
            if (!empty($li['picture'])) {
                $avatar = $this->downloadAvatar($li['picture'], $userId);
                if ($avatar) {
                    $profileData['avatar'] = $avatar;
                    $userModel->update($userId, ['avatar' => $avatar]);
                }
            }
            $profileModel->insert($profileData);

            $user = $userModel->find($userId);
        }

        if ($user->status !== 'active') {
            return redirect()->to('login')
                             ->with('error', 'Your account has been suspended.');
        }

        // Start session
        session()->regenerate(true);
        session()->set([
            'user_id'    => $user->id,
            'user_email' => $user->email,
            'user_name'  => trim($user->first_name . ' ' . $user->last_name),
            'user_role'  => $user->role,
            'logged_in'  => true,
        ]);

        $redirect = session()->getFlashdata('redirect_url') ?? '/dashboard';
        return redirect()->to($redirect)
                         ->with('success', lang('App.linkedin_login_success') . ' ' . $user->first_name . '!');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Exchange the authorisation code for an access token
    // ──────────────────────────────────────────────────────────────────────────
    private function getAccessToken(string $code): ?string
    {
        try {
            $curl     = service('curlrequest');
            $response = $curl->post(self::TOKEN_URL, [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'code'          => $code,
                    'redirect_uri'  => base_url('linkedin/callback'),
                    'client_id'     => env('LINKEDIN_CLIENT_ID'),
                    'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
                ],
                'http_errors' => false,
            ]);

            $body = json_decode($response->getBody(), true);
            return $body['access_token'] ?? null;
        } catch (\Exception $e) {
            log_message('error', '[LinkedIn] Token exchange failed: ' . $e->getMessage());
            return null;
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Fetch basic profile from OpenID Connect /v2/userinfo
    // Returns: sub, name, given_name, family_name, email, picture, locale
    // ──────────────────────────────────────────────────────────────────────────
    private function fetchUserInfo(string $token): ?array
    {
        try {
            $curl     = service('curlrequest');
            $response = $curl->get(self::USERINFO_URL, [
                'headers'     => ['Authorization' => 'Bearer ' . $token],
                'http_errors' => false,
            ]);

            $data = json_decode($response->getBody(), true);
            return (is_array($data) && empty($data['error'])) ? $data : null;
        } catch (\Exception $e) {
            log_message('error', '[LinkedIn] /v2/userinfo fetch failed: ' . $e->getMessage());
            return null;
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Fetch additional data from /v2/me (headline, vanityName)
    // Note: requires r_basicprofile scope for full data; may return limited info
    //       for standard apps. Failure here is non-fatal.
    // ──────────────────────────────────────────────────────────────────────────
    private function fetchMe(string $token): ?array
    {
        try {
            $curl     = service('curlrequest');
            $response = $curl->get(self::ME_URL . '?projection=(id,vanityName,headline)', [
                'headers'     => ['Authorization' => 'Bearer ' . $token],
                'http_errors' => false,
            ]);

            $data = json_decode($response->getBody(), true);
            return (is_array($data) && empty($data['error'])) ? $data : null;
        } catch (\Exception $e) {
            log_message('error', '[LinkedIn] /v2/me fetch failed: ' . $e->getMessage());
            return null;
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Merge the LinkedIn data into the local user & profile records
    // Returns an array of human-readable field names that were updated
    // ──────────────────────────────────────────────────────────────────────────
    private function importProfile(array $li, ?array $me): array
    {
        $userId       = (int) session()->get('user_id');
        $imported     = [];
        $userModel    = model(\App\Models\UserModel::class);
        $profileModel = model(\App\Models\ProfileModel::class);

        // ── 1. Update users table (name) ──────────────────────────────────────
        $user       = $userModel->find($userId);
        $updateUser = [];

        if (!empty($li['given_name']) && empty(trim((string) $user->first_name))) {
            $updateUser['first_name'] = $li['given_name'];
        }
        if (!empty($li['family_name']) && empty(trim((string) $user->last_name))) {
            $updateUser['last_name'] = $li['family_name'];
        }

        if (!empty($updateUser)) {
            $userModel->update($userId, $updateUser);
            $first = $updateUser['first_name'] ?? $user->first_name;
            $last  = $updateUser['last_name']  ?? $user->last_name;
            session()->set('user_name', trim($first . ' ' . $last));
            $imported[] = 'name';
        }

        // ── 2. Update profiles table ──────────────────────────────────────────
        $profile     = $profileModel->where('user_id', $userId)->first();
        $profileData = [];

        // Headline — from /v2/me (localized object format)
        if (!empty($me['headline'])) {
            $hl = null;
            if (is_array($me['headline'])) {
                // LinkedIn v2 localized object: {"localized":{"en_US":"..."},...}
                $localized = $me['headline']['localized'] ?? $me['headline'];
                if (is_array($localized)) {
                    $hl = reset($localized); // first locale value
                }
            } elseif (is_string($me['headline'])) {
                $hl = $me['headline'];
            }

            if (!empty($hl) && empty($profile?->headline)) {
                $profileData['headline'] = $hl;
                $imported[] = 'headline';
            }
        }

        // LinkedIn profile URL — constructed from vanityName
        if (!empty($me['vanityName'])) {
            $liUrl = 'https://www.linkedin.com/in/' . $me['vanityName'];
            if (empty($profile?->linkedin)) {
                $profileData['linkedin'] = $liUrl;
                $imported[] = 'LinkedIn URL';
            }
        }

        // Profile photo — download and store locally
        if (!empty($li['picture']) && empty($profile?->avatar)) {
            $avatarFile = $this->downloadAvatar($li['picture'], $userId);
            if ($avatarFile !== null) {
                $profileData['avatar'] = $avatarFile;
                $imported[] = 'photo';
            }
        }

        if (!empty($profileData)) {
            if ($profile) {
                $profileModel->update($profile->id, $profileData);
            } else {
                $profileData['user_id'] = $userId;
                $profileModel->insert($profileData);
            }
        }

        return $imported ?: ['basic info checked'];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Download the LinkedIn profile picture and save it to writable/uploads/
    // Returns the filename on success, null on failure
    // ──────────────────────────────────────────────────────────────────────────
    private function downloadAvatar(string $url, int $userId): ?string
    {
        try {
            $curl     = service('curlrequest');
            $response = $curl->get($url, ['http_errors' => false]);
            $body     = $response->getBody();

            // Sanity-check: must be a non-trivial image payload
            if (empty($body) || strlen($body) < 500) {
                return null;
            }

            $contentType = $response->getHeaderLine('Content-Type');
            $ext = str_contains($contentType, 'png') ? 'png' : 'jpg';

            $filename = 'avatar_li_' . $userId . '_' . time() . '.' . $ext;
            $path     = WRITEPATH . 'uploads/' . $filename;

            file_put_contents($path, $body);
            return $filename;
        } catch (\Exception $e) {
            log_message('error', '[LinkedIn] Avatar download failed: ' . $e->getMessage());
            return null;
        }
    }
}
