<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
    }

    // ─── Register ────────────────────────────────────────────────────────────

    public function registerForm(): string
    {
        return view('auth/register', ['title' => 'Create Account']);
    }

    public function register(): RedirectResponse
    {
        $rules = [
            'first_name'       => 'required|min_length[2]|max_length[100]',
            'last_name'        => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'role'             => 'required|in_list[job_seeker,recruiter]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'email'      => $this->request->getPost('email'),
            'password'   => $this->request->getPost('password'),
            'role'       => $this->request->getPost('role'),
        ];

        $userId = $this->userModel->insert($data);

        // Create an empty profile for job seekers
        if ($data['role'] === 'job_seeker') {
            model(\App\Models\ProfileModel::class)->insert(['user_id' => $userId]);
        }

        return redirect()->to('/login')->with('success', 'Account created! Please log in.');
    }

    // ─── Login ───────────────────────────────────────────────────────────────

    public function loginForm(): string|RedirectResponse
    {
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login', ['title' => 'Login']);
    }

    public function login(): RedirectResponse
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user->password)) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
        }

        if ($user->status !== 'active') {
            return redirect()->back()->with('error', 'Your account has been suspended.');
        }

        // Regenerate session ID to prevent session fixation
        session()->regenerate(true);
        session()->set([
            'user_id'     => $user->id,
            'user_email'  => $user->email,
            'user_name'   => $user->first_name . ' ' . $user->last_name,
            'user_role'   => $user->role,
            'user_avatar' => $user->avatar ?? null,
            'logged_in'   => true,
        ]);

        // Handle "remember me"
        if ($this->request->getPost('remember')) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->update($user->id, ['remember_token' => $token]);
            $response = service('response');
            $response->setCookie('remember_token', $token, 3600 * 24 * 30, '', '/', '', true, true);
        }

        $redirect = session()->getFlashdata('redirect_url') ?? '/dashboard';
        return redirect()->to($redirect)->with('success', 'Welcome back, ' . $user->first_name . '!');
    }

    // ─── Admin: Switch role for testing ──────────────────────────────────────

    public function switchRole(string $role): RedirectResponse
    {
        // Only real admins may use this feature
        $realRole = session()->get('user_real_role') ?? session()->get('user_role');
        if ($realRole !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Accès refusé.');
        }

        $allowed = ['admin', 'recruiter', 'job_seeker'];
        if (!in_array($role, $allowed, true)) {
            return redirect()->back()->with('error', 'Rôle invalide.');
        }

        // Persist the original admin role so we can always restore it
        session()->set('user_real_role', 'admin');
        session()->set('user_role', $role);

        $labels = ['admin' => 'Admin', 'recruiter' => 'Recruteur', 'job_seeker' => 'Chercheur d\'emploi'];
        return redirect()->to('/dashboard')->with('success', 'Mode simulation : ' . $labels[$role]);
    }

    // ─── Logout ──────────────────────────────────────────────────────────────

    public function logout(): RedirectResponse
    {
        // Clear remember token from DB before destroying session
        $userId = session()->get('user_id');
        if ($userId) {
            $this->userModel->update($userId, ['remember_token' => null]);
        }

        // Must set flash BEFORE destroying the session
        session()->setFlashdata('success', 'You have been logged out.');
        session()->destroy();
        delete_cookie('remember_token');

        return redirect()->to('/login');
    }
}
