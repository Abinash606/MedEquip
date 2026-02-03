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

}
