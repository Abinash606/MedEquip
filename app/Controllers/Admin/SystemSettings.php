<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SystemSettingsModel;
use App\Models\UserModel;
use App\Models\IqNoteModel;
use App\Models\EquipmentSettingModel;

class SystemSettings extends BaseController
{
    public function index()
    {
        $companyId = $this->session->get('company_id');
        if (!$companyId) {
            return redirect()->to(site_url('login'));
        }

        $settingsModel = new SystemSettingsModel();
        $settings      = $settingsModel->getOrCreateByCompany($companyId);
        //print_r($settings);
        return view('admin/settings/index', [
            'settings' => $settings
        ]);
    }

    // ---------- GENERAL + NOTIFICATION UPDATE ----------
    public function update()
    {
        $companyId = (int) session('company_id');
        if (!$companyId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $settingsModel = new SystemSettingsModel();
        $current       = $settingsModel->getOrCreateByCompany($companyId);

        $data = [
            'company_name'        => trim((string) $this->request->getPost('company_name')),
            'time_zone'           => trim((string) $this->request->getPost('time_zone')),
            'maintenance_mode'    => $this->request->getPost('maintenance_mode') ? 1 : 0,

            'email_notifications' => $this->request->getPost('email_notifications') ? 1 : 0,
            'sms_notifications'   => $this->request->getPost('sms_notifications') ? 1 : 0,
            'push_notifications'  => $this->request->getPost('push_notifications') ? 1 : 0,
        ];

        // Basic validation
        if ($data['company_name'] === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Company Name is required'])->setStatusCode(422);
        }
        if ($data['time_zone'] === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Time Zone is required'])->setStatusCode(422);
        }

        $settingsModel->update($current['id'], $data);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Settings updated']);
    }

    // ---------- ADMINS: DATATABLE LIST ----------
    public function adminsList()
    {
        $companyId = (int) session('company_id');
        if (!$companyId) {
            return $this->response->setJSON(['data' => []])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $admins = $userModel
            ->select('id, full_name, email, role_id, status')
            ->where('company_id', $companyId)
            ->whereIn('role_id', [1, 2, 3]) // adjust if needed
            ->orderBy('id', 'DESC')
            ->findAll();

        // map role_id to text
        $roleMap = [
            1 => 'Super Admin',
            2 => 'Admin',
            3 => 'Viewer',
        ];

        $rows = [];
        foreach ($admins as $a) {
            $rows[] = [
                'id'       => $a['id'],
                'username' => $a['full_name'],
                'email'    => $a['email'],
                'role'     => $roleMap[(int)$a['role_id']] ?? 'Admin',
                'status'   => $a['status'] ?? 'active',
            ];
        }

        return $this->response->setJSON(['data' => $rows]);
    }

    // ---------- ADMINS: GET ONE ----------
    public function adminGet($id)
    {
        $companyId = (int) session('company_id');
        if (!$companyId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $row = $userModel->where('company_id', $companyId)->find((int)$id);

        if (!$row) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Admin not found'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'id'       => $row['id'],
                'full_name' => $row['full_name'],
                'email'    => $row['email'],
                'role_id'  => $row['role_id'],
                'status'   => $row['status'] ?? 'active',
            ]
        ]);
    }

    // ---------- ADMINS: SAVE (ADD/EDIT) ----------
    public function adminSave()
    {
        $companyId = (int) session('company_id');
        if (!$companyId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $userModel = new UserModel();

        $id        = (int) ($this->request->getPost('id') ?? 0);
        $fullName  = trim((string) $this->request->getPost('full_name'));
        $email     = trim((string) $this->request->getPost('email'));
        $roleId    = (int) ($this->request->getPost('role_id') ?? 2);
        $password  = (string) $this->request->getPost('password');
        $status    = (string) ($this->request->getPost('status') ?? 'active');

        if ($fullName === '' || $email === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Name and Email are required'])->setStatusCode(422);
        }

        // email unique within same company (excluding current id)
        $exists = $userModel->where('company_id', $companyId)->where('email', $email);
        if ($id > 0) $exists->where('id !=', $id);
        if ($exists->first()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email already exists'])->setStatusCode(409);
        }

        $data = [
            'company_id' => $companyId,
            'full_name'  => $fullName,
            'email'      => $email,
            'role_id'    => $roleId,
            'status'     => $status,
        ];

        // only update password if provided
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if ($id > 0) {
            // ensure belongs to company
            $row = $userModel->where('company_id', $companyId)->find($id);
            if (!$row) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Admin not found'])->setStatusCode(404);
            }
            $userModel->update($id, $data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Admin updated']);
        }

        $userModel->insert($data);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Admin created']);
    }

    // ---------- ADMINS: DELETE ----------
    public function adminDelete($id)
    {
        $companyId = (int) session('company_id');
        if (!$companyId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $userModel = new UserModel();
        $row = $userModel->where('company_id', $companyId)->find((int)$id);
        if (!$row) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Admin not found'])->setStatusCode(404);
        }

        $userModel->delete((int)$id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Admin deleted']);
    }


    // ---------- IQ NOTES LIST ----------
    public function iqNotes()
    {
        $companyId = session('company_id');
        $model = new IqNoteModel();

        $rows = $model->where('company_id', $companyId)->orderBy('id', 'DESC')->findAll();
        return $this->response->setJSON(['data' => $rows]);
    }

    // ---------- IQ NOTE GET ----------
    public function iqNoteGet($id)
    {
        $model = new IqNoteModel();
        $row = $model->find($id);
        return $this->response->setJSON(['data' => $row]);
    }

    // ---------- IQ NOTE SAVE ----------
    public function iqNoteSave()
    {
        $model = new IqNoteModel();

        $id = $this->request->getPost('id');
        $data = [
            'company_id' => session('company_id'),
            'note'       => $this->request->getPost('note')
        ];

        if ($id) {
            $model->update($id, $data);
            return $this->response->setJSON(['message' => 'Note updated']);
        }

        $model->insert($data);
        return $this->response->setJSON(['message' => 'Note added']);
    }


    public function iqNoteDelete()
    {
        $id = (int) $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON(['message' => 'Invalid ID'])->setStatusCode(400);
        }

        $model = new IqNoteModel();
        $model->delete($id);

        return $this->response->setJSON(['message' => 'Note deleted']);
    }

    public function equipmentList()
    {
        $model = new EquipmentSettingModel();
        return $this->response->setJSON([
            'data' => $model->findAll()
        ]);
    }

    public function equipmentSave()
    {
        $model = new EquipmentSettingModel();

        $id = $this->request->getPost('id');

        $data = [
            'description' => $this->request->getPost('description'),
            'est' => $this->request->getPost('est') ? 1 : 0,
            'cal' => $this->request->getPost('cal') ? 1 : 0,
        ];

        if ($id) {
            $model->update($id, $data);
            $msg = "Equipment updated successfully";
        } else {
            $model->insert($data);
            $msg = "Equipment added successfully";
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function equipmentDelete($id)
    {
        $model = new EquipmentSettingModel();
        $model->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Equipment deleted'
        ]);
    }
}
