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
                'status' => $this->request->getPost('status'),
                'site_id' => $this->request->getPost('site_id')
            ];
            $equipmentModel->insert($data);
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
    //         'make'          => 'permit_empty|max_length[100]',
    //         'model'         => 'permit_empty|max_length[100]',
    //         'device_type'   => 'permit_empty|max_length[100]',
    //         'serial_number' => 'permit_empty|max_length[100]',
    //         'asset_tag'     => 'permit_empty|max_length[100]',
    //         'status'        => 'permit_empty|max_length[50]',
    //     ];

    //     if (! $this->validate($rules)) {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'errors' => $this->validator->getErrors()
    //         ]);
    //     }


    //     // ✅ ASSET TAG AUTO GENERATE
    //     $assetTag = trim((string) $this->request->getPost('asset_tag'));
    //     if ($assetTag === '') {
    //         // Example: AT-000123
    //         $assetTag = 'AT-' . str_pad((string) rand(1, 999999), 6, '0', STR_PAD_LEFT);
    //     }

    //     $payload = [
    //         'company_id'    => $companyId,
    //         'site_id'       => 1,
    //         'make'          => trim((string) $this->request->getPost('make')),
    //         'model'         => trim((string) $this->request->getPost('model')),
    //         'device_type'   => trim((string) $this->request->getPost('device_type')),
    //         'serial_number' => trim((string) $this->request->getPost('serial_number')),
    //         'asset_tag'     => $assetTag,
    //         'status'        => $this->request->getPost('status') ?: 'ready',
    //     ];

    //     // ✅ UPDATE
    //     if ($id > 0) {
    //         $this->equipmentModel->update($id, $payload);

    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'Equipment updated',
    //             'data' => $payload
    //         ]);
    //     }

    //     // ✅ INSERT
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
        'make'        => 'permit_empty|max_length[100]',
        'model'       => 'permit_empty|max_length[100]',
        'device_type' => 'permit_empty|max_length[100]',
    ];

    if (! $this->validate($rules)) {
        return $this->response->setJSON([
            'status' => 'error',
            'errors' => $this->validator->getErrors()
        ]);
    }

    // 🔹 Asset tag auto-generate
    $assetTag = trim((string) $this->request->getPost('asset_tag'));
    if ($assetTag === '') {
        $assetTag = 'AT-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    // 🔹 Existing record (for edit)
    $existing = $id ? $this->equipmentModel->find($id) : [];

    /** =========================
     *  FILE UPLOADS
     *  ========================= */
    $pmPath    = $existing['pm_manual_path'] ?? null;
    $photoPath = $existing['photo_path'] ?? null;

    // 📄 PM MANUAL
    $pmFile = $this->request->getFile('pm_manual');
    if ($pmFile && $pmFile->isValid()) {
        $newName = $pmFile->getRandomName();
        $pmFile->move(FCPATH . 'uploads/pm_manuals', $newName);
        $pmPath = 'uploads/pm_manuals/' . $newName;
    }

    // 🖼 PHOTO
    $photoFile = $this->request->getFile('photo');
    if ($photoFile && $photoFile->isValid()) {
        $newName = $photoFile->getRandomName();
        $photoFile->move(FCPATH . 'uploads/equipment_photos', $newName);
        $photoPath = 'uploads/equipment_photos/' . $newName;
    }

    $payload = [
        'company_id'      => $companyId,
        'site_id'         => 1,
        'make'            => trim($this->request->getPost('make')),
        'model'           => trim($this->request->getPost('model')),
        'device_type'     => trim($this->request->getPost('device_type')),
        'serial_number'   => trim($this->request->getPost('serial_number')),
        'asset_tag'       => $assetTag,
        'status'          => 'ready',
        'pm_manual_path'  => $pmPath,
        'photo_path'      => $photoPath,
    ];

    if ($id > 0) {
        $this->equipmentModel->update($id, $payload);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Equipment updated',
            'data' => $payload
        ]);
    }

    $newId = $this->equipmentModel->insert($payload);

    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Equipment added',
        'id' => $newId,
        'data' => $payload
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
