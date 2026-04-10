<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TechnicianModel;
use App\Models\UserModel;

class TechniciansController extends BaseController
{
    protected $userModel;
    protected $technicianModel;
    protected $db;

    public function __construct()
    {
        $this->userModel       = new UserModel();
        $this->technicianModel = new TechnicianModel();
        $this->db              = \Config\Database::connect();
    }

    public function index()
    {
        $companyId = $this->session->get('company_id');
        // Join technicians with users to display technician name and email
        $builder = \Config\Database::connect()->table('technicians');
        $technicians = $builder
            ->select('technicians.*, users.full_name as name, users.email as user_email')
            ->join('users', 'users.id = technicians.user_id')
            ->where('technicians.company_id', $companyId)
            ->where('users.deleted_at', null)
            ->get()
            ->getResultArray();
        $data['technicians'] = $technicians;
        return view('admin/technicians/index', $data);
    }
    // Get all technicians (DataTables AJAX)
    public function getData()
    {
        try {
            $companyId = session()->get('company_id');

            if (!$companyId) {
                return $this->response->setJSON([
                    'data' => [],
                    'error' => 'No company ID in session'
                ]);
            }

            $technicians = $this->technicianModel
                ->select('technicians.*, users.full_name, users.email, users.username, users.phone')
                ->join('users', 'users.id = technicians.user_id')
                ->where('technicians.company_id', $companyId)
                ->where('technicians.deleted_at', null)
                ->where('users.deleted_at', null)
                ->findAll();

            return $this->response->setJSON(['data' => $technicians]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'data' => [],
                'error' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // Get single technician
    public function show($id)
    {
        try {
            $technician = $this->technicianModel
                ->select('technicians.*, users.full_name, users.email, users.username, users.phone')
                ->join('users', 'users.id = technicians.user_id')
                ->where('technicians.company_id', session()->get('company_id'))
                ->find($id);

            if (!$technician) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Technician not found'
                ])->setStatusCode(404);
            }

            // Convert comma-separated states to array
            $technician['states'] = !empty($technician['state'])
                ? explode(',', $technician['state'])
                : [];

            return $this->response->setJSON([
                'success' => true,
                'data' => $technician
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function store()
    {
        try {

            $rules = [
                'firstname' => 'required|min_length[2]',
                'lastname'  => 'required|min_length[2]',
                'email'     => 'permit_empty|valid_email|is_unique[users.email]',
                'username'  => 'required|alpha_numeric_punct|is_unique[users.username]',
                'password'  => 'required|min_length[6]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $this->validator->getErrors()
                ])->setStatusCode(400);
            }

            // Get company_id from session
            $companyId = session()->get('company_id');
            if (!$companyId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No company ID found in session'
                ])->setStatusCode(400);
            }

            // Validate states
            $states = $this->request->getPost('states');
            if (empty($states) || !is_array($states)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['states' => 'Please select at least one state']
                ])->setStatusCode(400);
            }

            $this->db->transStart();

            $fullName = trim($this->request->getPost('firstname') . ' ' . $this->request->getPost('lastname'));
            $email = $this->request->getPost('email');
            $phone = $this->request->getPost('phone');

            // Create User
            $userData = [
                'company_id'    => $companyId,
                'role_id'       => 3, // Technician role id
                'full_name'     => $fullName,
                'username'      => $this->request->getPost('username'),
                'email'         => !empty($email) ? $email : null,
                'phone'         => !empty($phone) ? $phone : null,
                'status'        => 'active',
                'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            ];
            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                $errors = $this->userModel->errors();
                throw new \Exception('Failed to create user: ' . json_encode($errors));
            }
            // Convert states array to comma-separated string
            $stateString = implode(',', $states);

            // Create Technician
            $technicianData = [
                'company_id' => $companyId,
                'user_id'    => $userId,
                'phone'      => !empty($phone) ? $phone : null,
                'email'      => !empty($email) ? $email : null,
                'state'      => $stateString,
            ];

            $technicianId = $this->technicianModel->insert($technicianData);

            if (!$technicianId) {
                $errors = $this->technicianModel->errors();
                throw new \Exception('Failed to create technician: ' . json_encode($errors));
            }


            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }


            return $this->response->setJSON([
                'success' => true,
                'message' => 'Technician added successfully'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create technician: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function update($id)
    {
        try {

            $technician = $this->technicianModel
                ->where('company_id', session()->get('company_id'))
                ->find($id);

            if (!$technician) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Technician not found'
                ])->setStatusCode(404);
            }

            // Get the user_id to exclude from unique validation
            $userId = $technician['user_id'];

            $rules = [
                'firstname' => 'required|min_length[2]',
                'lastname'  => 'required|min_length[2]',
                'email'     => "permit_empty|valid_email|is_unique[users.email,id,{$userId}]",
                'username'  => "required|alpha_numeric_punct|is_unique[users.username,id,{$userId}]",
                'password'  => 'permit_empty|min_length[6]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $this->validator->getErrors()
                ])->setStatusCode(400);
            }

            // Validate states
            $states = $this->request->getPost('states');
            if (empty($states) || !is_array($states)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['states' => 'Please select at least one state']
                ])->setStatusCode(400);
            }

            $this->db->transStart();

            $fullName = trim($this->request->getPost('firstname') . ' ' . $this->request->getPost('lastname'));
            $email = $this->request->getPost('email');
            $phone = $this->request->getPost('phone');

            // Update User
            $updateData = [
                'full_name' => $fullName,
                'username'  => $this->request->getPost('username'),
                'phone'     => !empty($phone) ? $phone : null,
                'email'     => !empty($email) ? $email : null,
            ];

            // Only update password if provided
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $updateData['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
            }

            $this->userModel->update($userId, $updateData);

            // Convert states array to comma-separated string
            $stateString = implode(',', $states);

            // Update Technician
            $technicianUpdateData = [
                'phone' => !empty($phone) ? $phone : null,
                'email' => !empty($email) ? $email : null,
                'state' => $stateString,
            ];

            $this->technicianModel->update($id, $technicianUpdateData);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Technician updated successfully'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update technician: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function delete($id)
    {
        try {
            $technician = $this->technicianModel
                ->where('company_id', session()->get('company_id'))
                ->find($id);

            if (!$technician) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Technician not found'
                ])->setStatusCode(404);
            }

            $this->db->transStart();

            // Soft delete technician
            $this->technicianModel->delete($id);

            // Soft delete user
            $this->userModel->delete($technician['user_id']);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Technician deleted successfully'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete technician: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    public function states()
    {
        try {
            $states = $this->db->table('us_states')->orderBy('name')->get()->getResultArray();
            return $this->response->setJSON(['data' => $states]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ])->setStatusCode(500);
        }
    }

    /**
     * POST admin/technicians/login-as/{id}
     *
     * Generates a short-lived one-time token so an admin can open the
     * technician portal as that technician in a new browser tab.
     *
     * Token is stored in the CI4 session (scoped by company) and expires
     * after 60 seconds — just long enough for the new tab to open and
     * consume it. Only the currently logged-in admin can generate a token
     * for their own company's technicians.
     */
    public function loginAs(int $techId)
    {
        $companyId = (int) session('company_id');
        $adminRole = session('role');

        if (!in_array($adminRole, ['super_admin', 'admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $row = $this->db->query("
            SELECT t.id AS tech_id, t.user_id,
                   u.full_name, u.username, u.company_id,
                   r.name AS role_name
            FROM technicians t
            JOIN users u ON u.id = t.user_id
            JOIN roles r ON r.id = u.role_id
            WHERE t.id = ? AND t.company_id = ?
              AND t.deleted_at IS NULL AND u.deleted_at IS NULL
            LIMIT 1
        ", [$techId, $companyId])->getRow();

        if (!$row) {
            return $this->response->setJSON(['success' => false, 'message' => 'Technician not found.'])->setStatusCode(404);
        }

        $token   = bin2hex(random_bytes(32));
        $payload = json_encode([
           'user_id'    => (int) $row->user_id,
           'tech_id'    => (int) $row->tech_id,
           'company_id' => (int) $row->company_id,
           'role'       => $row->role_name,
           'full_name'  => $row->full_name,
           'username'   => $row->username,

          // store current admin session so it can be restored later
          'admin_restore' => [
          'user_id'    => (int) session('user_id'),
          'company_id' => (int) session('company_id'),
          'role'       => (string) session('role'),
          'full_name'  => (string) session('full_name'),
          'username'   => (string) session('username'),
          'isLoggedIn' => (bool) session('isLoggedIn'),
         ],
       ]);
      

        // Ensure the impersonate_tokens table exists — CREATE first, then clean up
        try {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS impersonate_tokens (
                    token      VARCHAR(128) NOT NULL PRIMARY KEY,
                    payload    TEXT         NOT NULL,
                    expires_at DATETIME     NOT NULL,
                    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            // Clean up any expired tokens
            $this->db->query("DELETE FROM impersonate_tokens WHERE expires_at < NOW()");
        } catch (\Throwable $e) {
            log_message('error', '[loginAs] Table setup failed: ' . $e->getMessage());
        }

        $this->db->query(
            "INSERT INTO impersonate_tokens (token, payload, expires_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 2 MINUTE))
             ON DUPLICATE KEY UPDATE payload=VALUES(payload), expires_at=VALUES(expires_at)",
            [$token, $payload]
        );

        return $this->response->setJSON([
            'success'   => true,
            'token'     => $token,
            'tech_name' => $row->full_name,
            'url'       => site_url('admin/technicians/open-as/' . $token),
        ]);
    }

    /**
     * GET admin/technicians/open-as/{token}
     * Reads token from DB, starts a FRESH session for the new tab
     * so the admin's original tab session is completely unaffected.
     */
    public function openAs(string $token)
    {
        // Read token from DB
        $row = null;
        try {
            $row = $this->db->query(
                "SELECT payload, expires_at FROM impersonate_tokens WHERE token = ? LIMIT 1",
                [$token]
            )->getRow();
        } catch (\Throwable $e) {
            log_message('error', '[openAs] DB read failed: ' . $e->getMessage());
        }

        if (!$row || strtotime($row->expires_at) < time()) {
            if ($row) {
                try { $this->db->query("DELETE FROM impersonate_tokens WHERE token = ?", [$token]); } catch (\Throwable $e) {}
            }
            return redirect()->to(site_url('login'))->with('error', 'Login link expired. Please try again.');
        }

        // Consume token — one-use only
        try { $this->db->query("DELETE FROM impersonate_tokens WHERE token = ?", [$token]); } catch (\Throwable $e) {}

       $data = json_decode($row->payload, true);
        if (!$data) {
          return redirect()->to(site_url('login'))->with('error', 'Invalid token data.');
       }

       if (!empty($data['admin_restore']) && is_array($data['admin_restore'])) {
         session()->set('admin_restore_session', $data['admin_restore']);
       }

        // Use CI4's native session to set technician data.
        // Because openAs is opened in a NEW BROWSER TAB, the browser sends the SAME
        // session cookie. The trick is: we ONLY write the technician keys — we never
        // destroy the session. The admin's data is overwritten in this tab only;
        // the admin tab still has its own JavaScript context and will re-read its
        // session on the next request from its own tab. When the technician tab closes
        // and the admin tab makes a request, that request re-sends the original cookie
        // which (if the admin tab has not navigated away) still points to the admin session.
        //
        // For full isolation we write the new data and redirect immediately.
        session()->set([
            'user_id'               => (int)($data['user_id']    ?? 0),
            'company_id'            => (int)($data['company_id'] ?? 0),
            'role'                  => $data['role']       ?? 'technician',
            'full_name'             => $data['full_name']  ?? '',
            'username'              => $data['username']   ?? '',
            'isLoggedIn'            => true,
            'impersonated_by_admin' => true,
        ]);

        return redirect()->to(site_url('technician/dashboard'));
    }
    public function restoreAdminSession()
{
    if (!session('impersonated_by_admin')) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'  => true,
                'redirect' => site_url('admin/dashboard'),
            ]);
        }
        return redirect()->to(site_url('admin/dashboard'));
    }

    $restore = session('admin_restore_session');

    if (!is_array($restore) || empty($restore['user_id']) || empty($restore['role'])) {
        session()->destroy();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'  => false,
                'redirect' => site_url('login'),
                'message'  => 'Admin session could not be restored. Please log in again.',
            ]);
        }

        return redirect()->to(site_url('login'))
            ->with('error', 'Admin session could not be restored. Please log in again.');
    }

    session()->set([
        'user_id'    => (int) ($restore['user_id'] ?? 0),
        'company_id' => (int) ($restore['company_id'] ?? 0),
        'role'       => (string) ($restore['role'] ?? 'super_admin'),
        'full_name'  => (string) ($restore['full_name'] ?? ''),
        'username'   => (string) ($restore['username'] ?? ''),
        'isLoggedIn' => (bool) ($restore['isLoggedIn'] ?? true),
    ]);

    session()->remove([
        'impersonated_by_admin',
        'admin_restore_session',
    ]);

    if ($this->request->isAJAX()) {
        return $this->response->setJSON([
            'success'  => true,
            'redirect' => site_url('admin/dashboard'),
        ]);
    }

    return redirect()->to(site_url('admin/dashboard'));
}
}
