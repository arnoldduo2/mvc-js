<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;
use App\App\Core\Validator;
use App\App\Core\CSRF;
use App\App\Models\User;

/**
 * Authentication Controller
 *
 * Handles user authentication operations including login, logout, and registration
 */
class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm(): void
    {
        // Redirect if already authenticated
        if (isset($_SESSION['user_id'])) {
            redirect(url('/users'));
            return;
        }

        $this->view('auth/login', [
            'title' => 'Login - ' . APP_NAME
        ]);
    }

    /**
     * Process login request
     */
    public function login(): void
    {
        // Verify CSRF token
        if (!CSRF::verify($_POST)) {
            $_SESSION['errors'] = ['_token' => ['Invalid CSRF token. Please try again.']];
            redirect(url('/login'));
            return;
        }

        // Validate form data
        $validator = Validator::make($_POST, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->errors();
            $_SESSION['old'] = $_POST;
            redirect(url('/login'));
            return;
        }

        $data = $validator->validated();

        // Find user by email
        $user = User::where('email', $data['email'])->first();

        // Verify user exists and password is correct
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $_SESSION['errors'] = ['email' => ['Invalid email or password.']];
            $_SESSION['old'] = ['email' => $data['email']];
            redirect(url('/login'));
            return;
        }

        // Check if account is active
        if (!$user['is_active']) {
            $_SESSION['errors'] = ['email' => ['Your account is not active. Please contact support.']];
            $_SESSION['old'] = ['email' => $data['email']];
            redirect(url('/login'));
            return;
        }

        // Create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        $_SESSION['authenticated'] = true;

        // Set success message
        $_SESSION['success'] = 'Welcome back, ' . htmlspecialchars($user['name']) . '!';

        // Redirect to return URL or default to users page
        $returnUrl = $_POST['return_url'] ?? '/users';
        redirect(url($returnUrl));
    }

    /**
     * Process logout request
     */
    public function logout(): void
    {
        // Store user name for goodbye message
        $userName = $_SESSION['user']['name'] ?? 'User';

        // Clear session
        unset($_SESSION['user_id']);
        unset($_SESSION['user']);
        unset($_SESSION['authenticated']);

        // Set success message
        $_SESSION['success'] = 'You have been logged out successfully. Goodbye, ' . htmlspecialchars($userName) . '!';

        // Redirect to login
        redirect(url('/login'));
    }

    /**
     * Show the registration form
     */
    public function showRegisterForm(): void
    {
        // Redirect if already authenticated
        if (isset($_SESSION['user_id'])) {
            redirect(url('/users'));
            return;
        }

        $this->view('auth/register', [
            'title' => 'Register - ' . APP_NAME
        ]);
    }

    /**
     * Process registration request
     */
    public function register(): void
    {
        // Verify CSRF token
        if (!CSRF::verify($_POST)) {
            $_SESSION['errors'] = ['_token' => ['Invalid CSRF token. Please try again.']];
            redirect(url('/register'));
            return;
        }

        // Validate form data
        $validator = Validator::make($_POST, [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->errors();
            $_SESSION['old'] = $_POST;
            redirect(url('/register'));
            return;
        }

        // Create user
        $data = $validator->validated();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['role'] = 'user'; // Default role
        $data['is_active'] = 1; // Active by default

        // Remove password_confirmation
        unset($data['password_confirmation']);

        $user = User::create($data);

        // Auto-login after registration
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        $_SESSION['authenticated'] = true;

        // Set success message
        $_SESSION['success'] = 'Registration successful! Welcome, ' . htmlspecialchars($user['name']) . '!';

        // Redirect to users page
        redirect(url('/users'));
    }
}
