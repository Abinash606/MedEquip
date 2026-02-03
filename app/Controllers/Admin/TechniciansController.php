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
}
