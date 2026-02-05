<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;


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
        if (! $this->request->isAJAX()) {
            return view('auth/forgot');
        }

        try {
            $emailInput = trim($this->request->getPost('email'));
            $userModel  = new UserModel();
            $user       = $userModel->getByEmail($emailInput);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $userModel->createPasswordReset($user['email'], $token);

                $this->sendResetEmail(
                    $user['email'],
                    $user['full_name'] ?? 'Customer',
                    base_url('reset/' . $token)
                );
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'A reset link has been sent to your Email.',
            ]);
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Server error. Check logs.',
                ]);
        }
    }

    // private function sendResetEmail(string $to, string $name, string $resetUrl): bool
    // {
    //     $email = Services::email();

    //     $body = view('emails/reset_password_email', [
    //         'customer_name' => $name,
    //         'reset_url'     => $resetUrl,
    //     ]);

    //     $email->clear();
    //     $email->setTo($to);
    //     $email->setSubject('Reset Your Password – Asset IQ');
    //     $email->setMessage($body);
    //     $email->setMailType('html');

    //     return $email->send();
    // }

    private function sendResetEmail(string $to, string $name, string $resetUrl): bool
    {
        $email = \Config\Services::email();

        $body = view('emails/reset_password_email', [
            'customer_name' => $name,
            'reset_url'     => $resetUrl,
        ]);

        $email->clear(true); // clear attachments + headers
        $email->setTo($to);
        $email->setSubject('Reset Your Password – Asset IQ');
        $email->setMessage($body);
        $email->setMailType('html');

        if (! $email->send()) {
            log_message('error', 'Password reset email failed: ' . print_r($email->printDebugger(['headers']), true));
            return false;
        }

        return true;
    }


    public function reset(string $token)
    {
        $userModel = new UserModel();
        $data = [
            'token' => $token,
        ];

        if ($this->request->getMethod() === 'POST') {
            $password        = $this->request->getPost('password');
            $passwordConfirm = $this->request->getPost('password_confirm');

            if ($password !== $passwordConfirm) {
                $data['error'] = 'Passwords do not match.';
                return view('auth/reset', $data);
            }

            $email = $userModel->validatePasswordResetToken($token);

            if (! $email) {
                $data['error'] = 'Invalid or expired token.';
                return view('auth/reset', $data);
            }

            $userModel->updatePasswordByEmail($email, $password);
            $userModel->markPasswordResetUsed($token);

            $user = $userModel->getByEmail($email);

            // Send confirmation email
            $emailSent = $this->sendResetConfirmationEmail(
                $email,
                $user['full_name'] ?? 'Customer'
            );

            // Log if email failed to send
            if (!$emailSent) {
                log_message('error', 'Failed to send password reset confirmation to: ' . $email);
            }

            return redirect()->to('/login')
                ->with('message', 'Your password has been reset successfully.');
        }
        $email = $userModel->validatePasswordResetToken($token);
        if (!$email) {
            $data['error'] = 'This reset link is invalid or has expired. Password reset links are valid for 1 hour only. Please request a new password reset.';
        }
        return view('auth/reset', $data);
    }

    private function sendResetConfirmationEmail(string $to, string $name): bool
    {
        $email = Services::email();

        // Use a proper password reset confirmation template
        $body = view('emails/welcome_email', [
            'customer_name' => $name,
        ]);

        $email->clear();
        $email->setTo($to);
        $email->setSubject('Your Password Has Been Reset –Asset IQ');
        $email->setMessage($body);
        $email->setMailType('html');

        if (!$email->send()) {
            log_message('error', 'Password reset confirmation email failed: ' . $email->printDebugger(['headers']));
            return false;
        }

        log_message('info', 'Password reset confirmation sent to: ' . $to);
        return true;
    }
}
