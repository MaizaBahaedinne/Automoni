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
            'user_id'    => $user->id,
            'user_email' => $user->email,
            'user_name'  => $user->first_name . ' ' . $user->last_name,
            'user_role'  => $user->role,
            'logged_in'  => true,
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
