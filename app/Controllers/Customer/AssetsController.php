<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\SiteModel;

class AssetsController extends BaseController
{
    public function index()
    {
        $siteModel = new SiteModel();
        $equipModel = new EquipmentModel();
        $companyId = $this->session->get('company_id');
        // Get all sites for the company
        $sites = $siteModel
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->findAll();

        // Get all equipment for these sites
        $siteIds = array_column($sites, 'id');
        $equipment = [];

        if (!empty($siteIds)) {
            $equipment = $equipModel
                ->whereIn('site_id', $siteIds)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            log_message('debug', 'Equipment found: ' . count($equipment));
        }

        $data['sites'] = $sites;
        $data['equipment'] = $equipment;

        return view('customer/assets/index', $data);
    }


    /**
     * Display the details of a site including equipment, inspections and work orders.
     */
    public function show(int $id)
    {
        $siteModel   = new SiteModel();
        $equipModel  = new \App\Models\EquipmentModel();
        $inspModel   = new \App\Models\InspectionModel();
        $workModel   = new \App\Models\WorkOrderModel();
        $companyId   = $this->session->get('company_id');
        $customerId  = $this->session->get('customer_id');
        $site = $siteModel->where('company_id', $companyId)
            ->where('customer_id', $customerId)
            ->where('id', $id)
            ->first();
        if (! $site) {
            return redirect()->to('/customer/sites');
        }
        $data['site']       = $site;
        $data['equipment']  = $equipModel->where('site_id', $id)->findAll();
        $data['inspections'] = $inspModel->where('site_id', $id)->findAll();
        $data['workOrders'] = $workModel->where('site_id', $id)->findAll();
        return view('customer/sites/show', $data);
    }


    public function store()
    {
        $equipModel = new EquipmentModel();
        $companyId = $this->session->get('company_id');

        // Backend validation
        $validation = \Config\Services::validation();

        $validation->setRules([
            'serial_number' => 'required|min_length[1]',
            'make' => 'required|min_length[2]',
            'model' => 'required|min_length[2]',
            'device_type' => 'required',
            'status' => 'required|in_list[Operational,Needs Attention,Out of Service]',
        ], [
            'serial_number' => [
                'required' => 'Serial number is required.'
            ],
            'make' => [
                'required' => 'Make is required.',
                'min_length' => 'Make must be at least 2 characters.'
            ],
            'model' => [
                'required' => 'Model is required.',
                'min_length' => 'Model must be at least 2 characters.'
            ],
            'device_type' => [
                'required' => 'Device type is required.'
            ],
            'status' => [
                'required' => 'Status is required.',
                'in_list' => 'Invalid status selected.'
            ]
        ]);

        if (!$validation->run($this->request->getPost())) {
            $errors = $validation->getErrors();
            $errorMsg = implode(', ', $errors);
            return redirect()->back()->with('error', $errorMsg);
        }

        // Get asset tag from form or auto-generate
        $assetTag = trim($this->request->getPost('asset_tag'));

        if (empty($assetTag)) {
            // Auto-generate asset tag
            $assetTag = $this->generateAssetTag($companyId);
        } else {
            // Check if manually entered asset tag already exists
            $existing = $equipModel
                ->where('company_id', $companyId)
                ->where('asset_tag', $assetTag)
                ->where('deleted_at', null)
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->with('error', 'Asset Tag already exists for this company');
            }
        }

        $data = [
            'company_id' => $companyId,
            'site_id' => $this->request->getPost('site_id'),
            'asset_tag' => $assetTag,
            'serial_number' => $this->request->getPost('serial_number'),
            'make' => $this->request->getPost('make'),
            'model' => $this->request->getPost('model'),
            'device_type' => $this->request->getPost('device_type'),
            'department' => $this->request->getPost('department'),
            'location' => $this->request->getPost('location'),
            'status' => $this->request->getPost('status'),
        ];

        if ($equipModel->insert($data)) {
            return redirect()->to('/customer/assets')
                ->with('success', 'Equipment added successfully!');
        }

        return redirect()->back()
            ->with('error', 'Failed to add equipment. Please try again.');
    }

    /**
     * Generate auto-increment asset tag in format ASSET-1000, ASSET-1001, etc.
     */
    private function generateAssetTag(int $companyId): string
    {
        $equipModel = new EquipmentModel();

        // Get the last asset tag for this company that matches ASSET-XXXX pattern
        $lastEquipment = $equipModel
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->like('asset_tag', 'ASSET-', 'after') // Only get tags starting with ASSET-
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastEquipment && preg_match('/ASSET-(\d+)/', $lastEquipment['asset_tag'], $matches)) {
            // Extract the number and increment
            $lastNumber = (int)$matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            // Start from 1000 if no previous asset tag exists
            $newNumber = 1000;
        }

        return 'ASSET-' . $newNumber;
    }
}