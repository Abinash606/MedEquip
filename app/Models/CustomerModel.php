<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table      = 'customers';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id', 'name', 'billing_address', 'billing_city', 'billing_state',
        'billing_zip', 'contact_name', 'email', 'phone', 'fax', 'website',
        'logo_path', 'internal_labor_rate_notes',   // ← ADDED
        'created_by', 'created_at', 'updated_at', 'deleted_at',
    ];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $returnType     = 'array';
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';

    // Save or update customer credentials
    public function saveCredentials($customerId, $credentials)
    {
        foreach ($credentials as $cred) {
            $this->db->table('credentials')->insert([
                'customer_id' => $customerId,
                'username'    => $cred['username'],
                'email'       => $cred['email'],
                'password'    => password_hash($cred['password'], PASSWORD_DEFAULT),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }
    }

    // Get all credentials for a customer
    public function getCredentialsByCustomerId($customerId)
    {
        return $this->db->table('credentials')
            ->where('customer_id', $customerId)
            ->get()
            ->getResultArray();
    }

    // Delete existing credentials for a customer
    public function deleteCredentials($customerId)
    {
        return $this->db->table('credentials')
            ->where('customer_id', $customerId)
            ->delete();
    }
}
