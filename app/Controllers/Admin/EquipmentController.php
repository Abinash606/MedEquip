<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\SiteModel;

class EquipmentController extends BaseController
{
    protected EquipmentModel $equipmentModel;
    protected SiteModel $siteModel;

    public function __construct()
    {
        $this->equipmentModel = new EquipmentModel();
        $this->siteModel      = new SiteModel();
    }

    public function index()
    {
        $companyId = (int) session('company_id');

        // Equipment list (with site name for "Customer Location")
        $equipment = $this->equipmentModel
            ->select('equipment.*, sites.name as site_name')
            ->join('sites', 'sites.id = equipment.site_id', 'left')
            ->where('equipment.company_id', $companyId)
            ->where('equipment.deleted_at', null)
            ->orderBy('equipment.id', 'DESC')
            ->findAll();

        // Sites dropdown for Customer Location
        $sites = $this->siteModel
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('admin/equipment/index', [
            'equipment' => $equipment,
            'sites'     => $sites,
        ]);
    }

    public function show($id)
    {
        $companyId = (int) session('company_id');

        $row = $this->equipmentModel
            ->where('company_id', $companyId)
            ->find((int)$id);

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Equipment not found']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $row]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $equipmentModel = new EquipmentModel();
            $companyId = (int) session('company_id');
            $data = [
                'company_id' => $companyId,
                'asset_tag' => $this->request->getPost('asset_tag'),
                'make' => $this->request->getPost('make'),
                'model' => $this->request->getPost('model'),
                'serial_number' => $this->request->getPost('serial_number'),
                'device_type' => $this->request->getPost('device_type'),
                'location' => $this->request->getPost('location'),
                'department' => $this->request->getPost('department'),
                'status' => $this->request->getPost('status') ?: 'active',
                'site_id' => $this->request->getPost('site_id')
            ];
            $inserted = $equipmentModel->insert($data);

            // Return JSON for AJAX requests
            $wantsJson = $this->request->isAJAX()
                || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false
                || stripos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;

            if ($wantsJson) {
                if ($inserted) {
                    $newId = $equipmentModel->getInsertID();
                    return $this->response->setJSON([
                        'success'     => true,
                        'message'     => 'Equipment added successfully',
                        'id'          => $newId,
                        'asset_tag'   => $data['asset_tag'],
                        'make'        => $data['make'],
                        'model'       => $data['model'],
                        'serial_number' => $data['serial_number'],
                        'device_type' => $data['device_type'],
                        'department'  => $data['department'],
                        'location'    => $data['location'],
                        'site_id'     => $data['site_id'],
                    ]);
                }
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to add equipment',
                ]);
            }

            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
        }
    }

    public function edit($id)
    {
        $equipmentModel = new EquipmentModel();
        $data['equipment'] = $equipmentModel->find($id);
        return view('admin/equipment/edit', $data);
    }

    public function update($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $equipmentModel = new EquipmentModel();
            $companyId = (int) session('company_id');
            $data = [
                'company_id' => $companyId,
                'asset_tag' => $this->request->getPost('asset_tag'),
                'make' => $this->request->getPost('make'),
                'model' => $this->request->getPost('model'),
                'serial_number' => $this->request->getPost('serial_number'),
                'device_type' => $this->request->getPost('device_type'),
                'location' => $this->request->getPost('location'),
                'department' => $this->request->getPost('department'),
                'status' => $this->request->getPost('status'),
            ];
            $equipmentModel->update($id, $data);
            return redirect()->to('/admin/sites/' . $this->request->getPost('site_id'));
        }
    }

    public function delete($id)
    {
        $equipmentModel = new EquipmentModel();
        $equipmentModel->delete($id);
        return redirect()->back();
    }


    /* ================= ADD + UPDATE ================= */


    // public function save()
    // {
    //     $companyId = (int) session('company_id');
    //     $id        = (int) ($this->request->getPost('id') ?? 0);

    //     $rules = [
    //         'make'        => 'permit_empty|max_length[100]',
    //         'model'       => 'permit_empty|max_length[100]',
    //         'device_type' => 'permit_empty|max_length[100]',
    //     ];

    //     if (! $this->validate($rules)) {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'errors' => $this->validator->getErrors()
    //         ]);
    //     }

    //     // 🔹 Asset tag auto-generate
    //     $assetTag = trim((string) $this->request->getPost('asset_tag'));
    //     if ($assetTag === '') {
    //         $assetTag = 'AT-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    //     }

    //     // 🔹 Existing record (for edit)
    //     $existing = $id ? $this->equipmentModel->find($id) : [];

    //     /** =========================
    //      *  FILE UPLOADS
    //      *  ========================= */
    //     $pmPath    = $existing['pm_manual_path'] ?? null;
    //     $photoPath = $existing['photo_path'] ?? null;

    //     // 📄 PM MANUAL
    //     $pmFile = $this->request->getFile('pm_manual');
    //     if ($pmFile && $pmFile->isValid()) {
    //         $newName = $pmFile->getRandomName();
    //         $pmFile->move(FCPATH . 'uploads/pm_manuals', $newName);
    //         $pmPath = 'uploads/pm_manuals/' . $newName;
    //     }

    //     // 🖼 PHOTO
    //     $photoFile = $this->request->getFile('photo');
    //     if ($photoFile && $photoFile->isValid()) {
    //         $newName = $photoFile->getRandomName();
    //         $photoFile->move(FCPATH . 'uploads/equipment_photos', $newName);
    //         $photoPath = 'uploads/equipment_photos/' . $newName;
    //     }

    //     $payload = [
    //         'company_id'      => $companyId,
    //         'site_id'         => 1,
    //         'make'            => trim($this->request->getPost('make')),
    //         'model'           => trim($this->request->getPost('model')),
    //         'device_type'     => trim($this->request->getPost('device_type')),
    //         'serial_number'   => trim($this->request->getPost('serial_number')),
    //         'asset_tag'       => $assetTag,
    //         'status'          => 'ready',
    //         'pm_manual_path'  => $pmPath,
    //         'photo_path'      => $photoPath,
    //     ];

    //     if ($id > 0) {
    //         $this->equipmentModel->update($id, $payload);
    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'Equipment updated',
    //             'data' => $payload
    //         ]);
    //     }

    //     $newId = $this->equipmentModel->insert($payload);

    //     return $this->response->setJSON([
    //         'status' => 'success',
    //         'message' => 'Equipment added',
    //         'id' => $newId,
    //         'data' => $payload
    //     ]);
    // }

    public function save()
    {
        $companyId = (int) session('company_id');
        $id        = (int) ($this->request->getPost('id') ?? 0);

        $rules = [
            'make'            => 'permit_empty|max_length[100]',
            'model'           => 'permit_empty|max_length[100]',
            'device_type'     => 'permit_empty|max_length[100]',
            'pm_manual'       => 'permit_empty|max_size[pm_manual,5120]',
            'service_manual'  => 'permit_empty|max_size[service_manual,5120]',
            'photo'           => 'permit_empty|max_size[photo,5120]',
        ];

        // if (! $this->validate($rules)) {
        //     return $this->response->setJSON([
        //         'status' => 'error',
        //         'errors' => $this->validator->getErrors()
        //     ]);
        // }

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode("\n", $this->validator->getErrors())
            ]);
        }


        // 🔹 Auto-generate asset tag
        $assetTag = 'AT-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        // Existing record (edit case)
        $existing = $id ? $this->equipmentModel->find($id) : [];

        $pmPath      = $existing['pm_manual_path'] ?? null;
        $servicePath = $existing['service_manual_path'] ?? null;
        $photoPath   = $existing['photo_path'] ?? null;

        // 📄 PM Manual
        $pmFile = $this->request->getFile('pm_manual');
        if ($pmFile && $pmFile->isValid()) {
            $newName = $pmFile->getRandomName();
            $pmFile->move(FCPATH . 'uploads/pm_manuals', $newName);
            $pmPath = 'uploads/pm_manuals/' . $newName;
        }

        // 📘 Service Manual (PDF)
        $serviceFile = $this->request->getFile('service_manual');
        if ($serviceFile && $serviceFile->isValid()) {
            $newName = $serviceFile->getRandomName();
            $serviceFile->move(FCPATH . 'uploads/service_manuals', $newName);
            $servicePath = 'uploads/service_manuals/' . $newName;
        }

        // 🖼 Photo
        $photoFile = $this->request->getFile('photo');
        if ($photoFile && $photoFile->isValid()) {
            $newName = $photoFile->getRandomName();
            $photoFile->move(FCPATH . 'uploads/equipment_photos', $newName);
            $photoPath = 'uploads/equipment_photos/' . $newName;
        }

        $payload = [
            'company_id'            => $companyId,
            'site_id'               => 1,
            'make'                  => trim($this->request->getPost('make')),
            'model'                 => trim($this->request->getPost('model')),
            'device_type'           => trim($this->request->getPost('device_type')),
            'serial_number'         => trim($this->request->getPost('serial_number')),
            'asset_tag'             => $assetTag,
            'status'                => 'ready',
            'pm_manual_path'        => $pmPath,
            'service_manual_path'   => $servicePath,
            'photo_path'            => $photoPath,
        ];

        if ($id > 0) {
            $this->equipmentModel->update($id, $payload);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Equipment updated'
            ]);
        }

        $this->equipmentModel->insert($payload);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Equipment added'
        ]);
    }


    /* ================= DELETE ================= */
    public function deletedb($id)
    {
        $this->equipmentModel->delete((int) $id);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Equipment deleted'
        ]);
    }
}
