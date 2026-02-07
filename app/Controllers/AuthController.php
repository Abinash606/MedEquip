<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CredentialsModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Email\Email;


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
            // First check in credentials table for customer
            $credentialModel = new CredentialsModel();
            $credential = $credentialModel->getCustomerWithCompanyByEmail($email);

            if ($credential && password_verify($password, $credential['password'])) {
                // If a customer is found, store necessary customer info in session
                $session->set([
                    'user_id'    => $credential['id'],
                    'company_id' => $credential['company_id'],
                    'role'       => 'customer',  // Set the role as 'customer'
                    'isLoggedIn' => true,
                ]);

                return redirect()->to('/customer/dashboard');
            }
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
    /**
     * Send password reset email
     */
    public function sendResetPasswordEmail($userEmail, $username, $resetLink, $companyName, $companyLogo)
    {
        // Prepare the subject and from details
        $fromEmail = '1easyecommerce@gmail.com';
        $fromName = esc($companyName);
        $subject = 'Password Reset Request - Asset IQ';

        // Data for user email
        $data = [
            'username' => esc($username),
            'reset_link' => $resetLink,
            'company_name' => esc($companyName),
            'company_logo' => $companyLogo  // Company logo path
        ];

        // Generate the email content by rendering the view template
        $message = view('emails/reset_password', $data);

        // Headers for the email
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n";

        // Send the email
        if (!mail($userEmail, $subject, $message, $headers)) {
            log_message('error', 'Failed to send password reset email to: ' . $userEmail);
            return false;
        }
        return true;
    }
    /**
     * Handle forgot password request for both Users and Customers
     */
    public function forgot()
    {
        if (!$this->request->isAJAX()) {
            return view('auth/forgot');
        }

        try {
            $emailInput = trim($this->request->getPost('email'));

            if (empty($emailInput)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Please provide an email address.'
                ]);
            }

            $userModel        = new UserModel();
            $credentialsModel = new CredentialsModel();
            $emailSent        = false;

            // ==========================
            // CHECK USERS TABLE FIRST
            // ==========================
            $user = $userModel->getByEmailSimple($emailInput);

            if ($user) {
                // Generate reset token (32 characters)
                $token = bin2hex(random_bytes(16));

                // Store token in password_resets table
                $userModel->createPasswordReset($user['email'], $token);

                // Create reset link for regular users - USE site_url() instead of base_url()
                $resetLink = site_url('reset/' . $token);

                // Send email
                $emailSent = $this->sendResetPasswordEmail(
                    $user['email'],
                    $user['full_name'] ?? 'User',
                    $resetLink,
                    'Asset IQ',
                    $user['company_logo'] ?? 'https://i.ibb.co/vx28x4g7/assets-logo.png'
                );
            } else {
                // ==========================
                // CHECK CREDENTIALS TABLE
                // ==========================
                $credential = $credentialsModel
                    ->where('email', $emailInput)
                    ->first();

                if ($credential) {
                    // Generate reset token (32 characters - same as CustomersController)
                    $token = bin2hex(random_bytes(16));

                    // Use database builder directly to bypass model validation
                    $db = \Config\Database::connect();
                    $db->table('credentials')
                        ->where('id', $credential['id'])
                        ->update([
                            'reset_token' => $token,
                            'reset_token_expiration' => date('Y-m-d H:i:s', strtotime('+1 hour'))
                        ]);

                    // Create reset link for customers - USE site_url() instead of base_url()
                    $resetLink = site_url('customer/reset-password/' . $token);                    // Send email
                    $emailSent = $this->sendResetPasswordEmail(
                        $credential['email'],
                        $credential['username'] ?? 'Customer',
                        $resetLink,
                        'Asset IQ',
                        'https://i.ibb.co/vx28x4g7/assets-logo.png'
                    );
                }
            }

            // Always return success message for security (don't reveal if email exists)
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'If this email exists in our system, a password reset link has been sent.'
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Forgot password error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'An error occurred. Please try again later.'
            ]);
        }
    }
    public function reset(string $token)
    {
        $userModel = new UserModel();

        $data = [
            'token' => $token,
        ];

        // When form submitted
        if ($this->request->getMethod() === 'POST') {

            $password        = $this->request->getPost('password');
            $passwordConfirm = $this->request->getPost('password_confirm');

            // Check password match
            if ($password !== $passwordConfirm) {
                $data['error'] = 'Passwords do not match.';
                return view('auth/reset', $data);
            }

            // Validate token
            $email = $userModel->validatePasswordResetToken($token);

            if (! $email) {
                $data['error'] = 'Invalid or expired token.';
                return view('auth/reset', $data);
            }

            $userModel->updatePasswordByEmail($email, $password);

            $userModel->markPasswordResetUsed($token);


            return redirect()->to('/login')
                ->with('message', 'Password reset successful. Please login.');
        }

        // GET request → validate token before showing page
        $email = $userModel->validatePasswordResetToken($token);
        if (!$email) {
            $data['error'] = 'This reset link is invalid or has expired. Password reset links are valid for 1 hour only. Please request a new password reset.';
        }

        return view('auth/reset', $data);
    }



    public function resetPassword(string $resetToken)
    {
        $credentialsModel = new CredentialsModel();

        // Find the customer by reset token
        $credential = $credentialsModel->where('reset_token', $resetToken)->first();

        // Check if the token exists and is not expired
        if ($credential && !empty($credential['reset_token_expiration']) && strtotime($credential['reset_token_expiration']) > time()) {
            // Token is valid and not expired - use the SAME view as users
            return view('auth/customer_reset_password', ['resetToken' => $resetToken]);
        } else {
            // Token is invalid or expired
            return redirect()->to('/login')
                ->with('error', 'This reset link is invalid or has expired. Password reset links are valid for 1 hour only. Please request a new password reset.');
        }
    }


    public function updatePassword()
    {
        // Get the form data
        $resetToken = $this->request->getPost('reset_token');
        $newPassword = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Check if passwords match
        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        // Validate the token again (optional, for security)
        $credentialsModel = new CredentialsModel();
        $user = $credentialsModel->where('reset_token', $resetToken)->first();

        if ($user && strtotime($user['reset_token_expiration']) > time()) {
            // Token is valid, update the password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the credentials table
            $data = [
                'password' => $hashedPassword,
                'reset_token' => null,  // Clear the reset token after password reset
                'reset_token_expiration' => null  // Clear the reset token expiration
            ];

            $credentialsModel->update($user['id'], $data);

            // Redirect to login page or another page after successful reset
            return redirect()->to('login')->with('success', 'Password has been reset successfully.');
        } else {
            // Token is invalid or expired
            return redirect()->to('login')->with('error', 'Invalid or expired reset token');
        }
    }
}
