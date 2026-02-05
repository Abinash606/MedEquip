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
	
	$fromEmail = '1easyecommerce@gmail.com';
    $fromName = 'Asset IQ';
    $subject = 'Reset Your Password – Asset IQ';

    // Data for customer email
    $data = [
       'customer_name' => $name,
        'reset_url'     => $resetUrl,
    ];

    // Generate the email content by rendering the view template
    $message = view('emails/reset_password_email', $data);

    // Headers for the email
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n";

//echo $to;

    // Send the email using PHP's mail() function
    if (mail($to, $subject, $message, $headers)) {
		//echo 'mail sent';
		return true;
        
    }else
	{
		
		echo 'Password reset email failed: ' . print_r($headers, true);
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
    // Prepare the subject and from details
    $fromEmail = '1easyecommerce@gmail.com';
    $fromName = 'Asset IQ';
    $subject = 'Your Password Has Been Reset – Asset IQ';

    // Data for the reset confirmation email
    $body = view('emails/welcome_email', [
        'customer_name' => $name,
    ]);

    // Headers for the email
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n";

    // Send the email using PHP's mail() function
    if (!mail($to, $subject, $body, $headers)) {
        log_message('error', 'Password reset confirmation email failed: ' . print_r($headers, true));
        return false;
    }

    log_message('info', 'Password reset confirmation sent to: ' . $to);
    return true;
}

public function resetPassword(string $resetToken)
    {
        $credentialsModel = new CredentialsModel();

		// Find the user by reset token
		$user = $credentialsModel->where('reset_token', $resetToken)->first();

		// Check if the token exists and is not expired
		if ($user && strtotime($user['reset_token_expiration']) > time()) {
			// Token is valid and not expired
			return view('auth/customer_reset_password', ['resetToken' => $resetToken]);
		} else {
			// Token is invalid or expired
			return redirect()->to('login')->with('error', 'Invalid or expired reset token');
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
