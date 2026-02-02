<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

/**
 * Debug Controller - DELETE THIS AFTER TESTING
 * 
 * Access this via: /debug/testLogin
 */
class DebugController extends Controller
{
    public function testLogin()
    {
        echo "<h2>Login Debug Test</h2>";
        echo "<style>pre { background: #f4f4f4; padding: 10px; } .success { color: green; } .error { color: red; }</style>";
        
        $email = 'easyecommerce@gmail.com';
        
        // Test 1: Database Connection
        echo "<h3>1. Database Connection Test</h3>";
        try {
            $db = \Config\Database::connect();
            echo "<p class='success'>✓ Database connected successfully</p>";
        } catch (\Exception $e) {
            echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
            return;
        }
        
        // Test 2: Check if roles table exists
        echo "<h3>2. Roles Table Test</h3>";
        try {
            $rolesExist = $db->tableExists('roles');
            if ($rolesExist) {
                echo "<p class='success'>✓ Roles table exists</p>";
                
                $roles = $db->table('roles')->get()->getResultArray();
                echo "<p>Found " . count($roles) . " roles:</p>";
                echo "<pre>" . print_r($roles, true) . "</pre>";
            } else {
                echo "<p class='error'>✗ Roles table does NOT exist</p>";
            }
        } catch (\Exception $e) {
            echo "<p class='error'>✗ Error checking roles table: " . $e->getMessage() . "</p>";
        }
        
        // Test 3: Direct query without model
        echo "<h3>3. Direct Query Test (No Model)</h3>";
        try {
            $query = $db->table('users')
                ->select('users.*, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('users.email', $email)
                ->get();
            
            $user = $query->getRowArray();
            
            if ($user) {
                echo "<p class='success'>✓ User found with direct query</p>";
                echo "<pre>" . print_r($user, true) . "</pre>";
            } else {
                echo "<p class='error'>✗ User NOT found with direct query</p>";
            }
            
            // Show the actual SQL query
            echo "<p><strong>SQL Query:</strong></p>";
            echo "<pre>" . $db->getLastQuery() . "</pre>";
            
        } catch (\Exception $e) {
            echo "<p class='error'>✗ Direct query failed: " . $e->getMessage() . "</p>";
        }
        
        // Test 4: Query without JOIN
        echo "<h3>4. Simple Query Test (No JOIN)</h3>";
        try {
            $user = $db->table('users')
                ->where('email', $email)
                ->get()
                ->getRowArray();
            
            if ($user) {
                echo "<p class='success'>✓ User found without JOIN</p>";
                echo "<pre>" . print_r($user, true) . "</pre>";
            } else {
                echo "<p class='error'>✗ User NOT found</p>";
            }
        } catch (\Exception $e) {
            echo "<p class='error'>✗ Simple query failed: " . $e->getMessage() . "</p>";
        }
        
        // Test 5: Using the UserModel
        echo "<h3>5. UserModel Test</h3>";
        try {
            $userModel = new UserModel();
            $user = $userModel->getByEmail($email);
            
            if ($user) {
                echo "<p class='success'>✓ User found using UserModel</p>";
                echo "<pre>" . print_r($user, true) . "</pre>";
            } else {
                echo "<p class='error'>✗ User NOT found using UserModel</p>";
            }
            
            // Show the actual SQL query from model
            echo "<p><strong>SQL Query from Model:</strong></p>";
            echo "<pre>" . $userModel->getLastQuery() . "</pre>";
            
        } catch (\Exception $e) {
            echo "<p class='error'>✗ UserModel query failed: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        
        // Test 6: Password verification
        echo "<h3>6. Password Verification Test</h3>";
        $storedHash = '$2y$10$Tjr6fTrcQZ0exDgrpJyh8O8ANT0vKBb5wNJj0vXkiY/wKPS4vpRPy';
        $testPasswords = ['admin', 'password', 'admin123', '123456', 'easyecommerce'];
        
        foreach ($testPasswords as $testPass) {
            $result = password_verify($testPass, $storedHash);
            $status = $result ? "✓ MATCH" : "✗ no match";
            $class = $result ? "success" : "error";
            echo "<p class='$class'>Password: <strong>$testPass</strong> - $status</p>";
        }
        
        echo "<hr>";
        echo "<p style='color: red;'><strong>DELETE THIS CONTROLLER AFTER TESTING!</strong></p>";
    }
}