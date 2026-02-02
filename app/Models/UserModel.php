<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UserModel
 *
 * Provides helper methods for interacting with the users table and related
 * authentication data. Passwords are hashed using PHP's password_hash
 * function and verified with password_verify. See CodeIgniter's
 * documentation for additional guidance on security best practices.
 */
class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'company_id', 'role_id', 'full_name', 'email', 'password_hash',
        'phone', 'status', 'created_at', 'updated_at', 'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';


    /**
     * Retrieve a user and their role by email address.
     */
    public function getByEmail(string $email): ? array
    {
        return $this->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.email', $email)
            ->where('users.deleted_at', null)
            ->first();
    }

  
public function getByEmailSimple(string $email): ?array
    {
        return $this->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.email', $email)
            ->first();
    }
    /**
     * Store a password reset token. Any existing tokens for this email are
     * removed before inserting the new one.
     */
    public function createPasswordReset(string $email, string $token): void
    {
        $db = db_connect();
        // Delete any existing tokens for this email
        $db->table('password_resets')->where('email', $email)->delete();
        // Insert new reset record
        $db->table('password_resets')->insert([
            'email'      => $email,
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'expired_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ]);
    }

    /**
     * Validate a password reset token and return the associated email if
     * the token is valid and not expired or previously used.
     */
    public function validatePasswordResetToken(string $token): ?string
    {
        $row = db_connect()->table('password_resets')
            ->where('token', $token)
            ->where('used_at', null)
            ->where('expired_at >=', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();
        return $row['email'] ?? null;
    }

    /**
     * Update a user's password by email address. The new password is
     * hashed before storage.
     */
    public function updatePasswordByEmail(string $email, string $password): void
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->where('email', $email)->set('password_hash', $hash)->update();
    }

    /**
     * Mark a password reset token as used.
     */
    public function markPasswordResetUsed(string $token): void
    {
        db_connect()->table('password_resets')
            ->where('token', $token)
            ->update(['used_at' => date('Y-m-d H:i:s')]);
    }
}