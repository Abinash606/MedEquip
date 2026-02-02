<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Authentication controller
 *
 * Handles login, logout, forgot password and password reset. This
 * controller demonstrates a simple role-based authentication flow.
 */
class AuthController extends Controller
{
    /**
     * Show the login form and handle login submissions.
     */
    public function login()
    {
        $session = session();
        $data = [];
       
        if ($this->request->getMethod() === 'POST') {
            $email    = trim($this->request->getPost('email'));
            $password = $this->request->getPost('password');

            $userModel = new UserModel();
            $user      = $userModel->getByEmailSimple($email);
      
            if ($user && password_verify($password, $user['password_hash'])) {
                // Store necessary user info in session
                $session->set([
                    'user_id'    => $user['id'],
                    'company_id' => $user['company_id'],
                    'role'       => $user['role_name'],
                    'isLoggedIn' => true,
                ]);

                // Redirect based on role
                switch ($user['role_name']) {
                    case 'super_admin':
                        return redirect()->to('/admin/dashboard');
                    case 'customer':
                        return redirect()->to('/customer/dashboard');
                    case 'technician':
                        return redirect()->to('/technician/dashboard');
                    default:
                        // Unknown role
                        return redirect()->to('/login')->with('error', 'Invalid role.');
                }
            }

            $data['error'] = 'Invalid email or password.';
        }
        return view('auth/login', $data);
    }

    /**
     * Log the current user out and destroy the session.
     */
    public function logout(): RedirectResponse
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }

    /**
     * Show the forgot password form and handle submissions. For demonstration
     * purposes this simply records a reset token in the database. In a real
     * application you would send the token to the user's email address.
     */
    public function forgot()
    {
        $data = [];
        if ($this->request->getMethod() === 'post') {
            $email = trim($this->request->getPost('email'));
            $userModel = new UserModel();
            $user = $userModel->getByEmail($email);
            if ($user) {
                // Generate token and store in password_resets table
                $token = bin2hex(random_bytes(16));
                $userModel->createPasswordReset($email, $token);
                $data['message'] = 'An email with password reset instructions has been sent if the address exists in our system.';
            } else {
                $data['message'] = 'An email with password reset instructions has been sent if the address exists in our system.';
            }
        }
        return view('auth/forgot', $data);
    }

    /**
     * Reset password using a token. Displays the reset form and updates the
     * password when submitted.
     */
    public function reset(string $token)
    {
        $userModel = new UserModel();
        $data = [
            'token' => $token,
        ];

        if ($this->request->getMethod() === 'post') {
            $password        = $this->request->getPost('password');
            $passwordConfirm = $this->request->getPost('password_confirm');
            if ($password !== $passwordConfirm) {
                $data['error'] = 'Passwords do not match.';
                return view('auth/reset', $data);
            }
            $email = $userModel->validatePasswordResetToken($token);
            if ($email) {
                $userModel->updatePasswordByEmail($email, $password);
                $userModel->markPasswordResetUsed($token);
                return redirect()->to('/login')->with('message', 'Your password has been reset. You may now log in.');
            }
            $data['error'] = 'Invalid or expired token.';
        }
        return view('auth/reset', $data);
    }
}