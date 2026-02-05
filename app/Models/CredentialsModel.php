<?php

namespace App\Models;

use CodeIgniter\Model;

class CredentialsModel extends Model
{
    // Define the table that the model will interact with
    protected $table = 'credentials';
    protected $primaryKey = 'id';

    // Define which fields can be mass-assigned
    protected $allowedFields = [
        'customer_id', 'username', 'email', 'password', 'reset_token', 'reset_token_expiration', 'created_at', 'updated_at'
    ];

    // Define timestamps for automatic handling
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Optionally, set the validation rules for the fields (if needed)
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[255]',
        'email' => 'required|valid_email|is_unique[credentials.email]',
        'password' => 'required|min_length[8]'
    ];

    // Optionally, set custom error messages for validation
    protected $validationMessages = [
        'email' => [
            'is_unique' => 'The email is already in use by another user.'
        ]
    ];

    // Function to save credentials (used in the controller)
    public function saveCredentials($customerId, $credentials)
    {
        // Insert each credential
        foreach ($credentials as $cred) {
            $data = [
                'customer_id' => $customerId,
                'username' => $cred['username'],
                'email' => $cred['email'],
                'password' => password_hash($cred['password'], PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Insert each credential into the database
            $this->insert($data);
        }
    }

    // Function to get credentials by customer ID (if needed for viewing or other purposes)
    public function getCredentialsByCustomerId($customerId)
    {
        return $this->where('customer_id', $customerId)->findAll();
    }

    // Function to check if reset token exists and is valid
    public function getByResetToken($resetToken)
    {
        return $this->where('reset_token', $resetToken)->first();
    }

    // Function to update the reset token for a given user
    public function updateResetToken($email, $resetToken, $resetTokenExpiration)
    {
        $data = [
            'reset_token' => $resetToken,
            'reset_token_expiration' => $resetTokenExpiration
        ];

        return $this->where('email', $email)->update($data);
    }
	// Method to get customer info along with company_id by email
    public function getCustomerWithCompanyByEmail($email)
    {
        // Join the credentials table with the customers table to get the company_id
        return $this->select('credentials.*, customers.company_id')
                    ->join('customers', 'customers.id = credentials.customer_id')
                    ->where('credentials.email', $email)
                    ->first();
    }

}
