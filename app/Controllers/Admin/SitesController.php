<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiteModel;
use App\Models\CustomerModel;
use App\Models\EquipmentModel;
use App\Models\InspectionModel;
use App\Models\WorkOrderModel;
use App\Models\TechnicianModel;


class SitesController extends BaseController
{
    /**
     * Display list of sites
     */
    public function index()
    {
        $siteModel     = new SiteModel();
        $customerModel = new CustomerModel();
        $companyId     = $this->session->get('company_id');

        // Read customer filter from session (set by filterSites) then clear it
        $activeCustomerId = session()->get('admin_site_customer_filter');
        session()->remove('admin_site_customer_filter');

        $data['sites']            = $siteModel->where('company_id', $companyId)->findAll();
        $data['customers']        = $customerModel->where('company_id', $companyId)->findAll();
        $data['active_customer_id'] = $activeCustomerId;

        return view('admin/sites/index', $data);
    }

    /**
     * Add a new site
     */
    public function add()
    {
        $isAjax = $this->request->isAJAX()
            || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false;

        $siteModel = new SiteModel();
        $companyId = $this->session->get('company_id');

        $rules = [
            'name'         => 'required|max_length[255]',
            'customer_id'  => 'required|integer',
            'address'      => 'permit_empty|max_length[255]',
            'city'         => 'permit_empty|max_length[100]',
            'state'        => 'permit_empty|max_length[50]',
            'zip'          => 'permit_empty|max_length[20]',
            'contact_name' => 'permit_empty|max_length[255]',
            'email'        => 'permit_empty|valid_email|max_length[255]',
            'phone'        => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $msg    = implode(' ', $errors);
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $siteData = [
            'company_id'   => $companyId,
            'customer_id'  => $this->request->getPost('customer_id'),
            'name'         => $this->request->getPost('name'),
            'address'      => $this->request->getPost('address'),
            'city'         => $this->request->getPost('city'),
            'state'        => $this->request->getPost('state'),
            'zip'          => $this->request->getPost('zip'),
            'contact_name' => $this->request->getPost('contact_name'),
            'email'        => $this->request->getPost('email'),
            'phone'        => $this->request->getPost('phone'),
        ];

        // Check for duplicate site name within same company before inserting
        $existing = $siteModel
            ->where('company_id', $companyId)
            ->where('name', $siteData['name'])
            ->where('deleted_at', null)
            ->first();
        if ($existing) {
            $msg = 'A site with this name already exists. Please use a unique site name.';
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->withInput()->with('errors', ['name' => $msg]);
        }

        try {
            $siteModel->insert($siteData);
        } catch (\Throwable $e) {
            $msg = stripos($e->getMessage(), 'Duplicate') !== false
                ? 'A site with this name already exists. Please use a unique site name.'
                : 'Failed to save site. Please try again.';
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->withInput()->with('errors', ['name' => $msg]);
        }

        if ($isAjax) {
            return $this->response->setJSON(['success' => true, 'message' => 'Site added successfully.']);
        }

        return redirect()->to('admin/sites')->with('success', 'Site added successfully');
    }

    /**
     * Update an existing site
     * @param int $id
     */
    public function update($id)
    {
        $model = new SiteModel();

        // Validate input
        $rules = [
            'name' => 'required|max_length[255]',
            'customer_id' => 'required',
            'address' => 'permit_empty|max_length[255]',
            'city' => 'permit_empty|max_length[255]',
            'state' => 'permit_empty|max_length[255]',
            'zip' => 'permit_empty|max_length[20]',
            'contact_name' => 'permit_empty|max_length[255]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'phone' => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get the existing site data
        $siteData = [
            'name' => $this->request->getPost('name'),
            'customer_id' => $this->request->getPost('customer_id'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'zip' => $this->request->getPost('zip'),
            'contact_name' => $this->request->getPost('contact_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ];

        // Check for duplicate site name within same company (excluding this site)
        $companyId = $this->session->get('company_id');
        $duplicate = $model
            ->where('company_id', $companyId)
            ->where('name', $siteData['name'])
            ->where('id !=', $id)
            ->where('deleted_at', null)
            ->first();
        if ($duplicate) {
            return redirect()->back()->withInput()
                ->with('errors', ['name' => 'A site with this name already exists. Please use a unique site name.']);
        }

        // Update the site
        try {
            $model->update($id, $siteData);
        } catch (\Throwable $e) {
            $msg = stripos($e->getMessage(), 'Duplicate') !== false
                ? 'A site with this name already exists. Please use a unique site name.'
                : 'Failed to update site. Please try again.';
            return redirect()->back()->withInput()->with('errors', ['name' => $msg]);
        }

        return redirect()->to('admin/sites')->with('success', 'Site updated successfully');
    }


    /**
     * Delete a site
     * @param int $id
     */
    public function delete($id)
    {
        $siteModel = new SiteModel();
        $companyId = $this->session->get('company_id');

        $site = $siteModel->where('company_id', $companyId)->find($id);
        if (!$site) {
            return redirect()->to('admin/sites')->with('error', 'Site not found');
        }

        $siteModel->delete($id);

        return redirect()->to('admin/sites')->with('success', 'Site deleted successfully');
    }

    /**
     * View site details with equipment, inspections, and work orders
     * @param int $id
     */
    public function view($id)
    {
        $siteModel = new SiteModel();
        $customerModel = new CustomerModel();
        $equipmentModel = new \App\Models\SiteEquipmentModel();
        $inspectionModel = new InspectionModel();
        $workOrderModel = new WorkOrderModel();
        $technicianModel = new TechnicianModel();

        $companyId = $this->session->get('company_id');

        // Get site details
        $site = $siteModel->where('company_id', $companyId)->find($id);
        if (!$site) {
            return redirect()->to('admin/sites')->with('error', 'Site not found');
        }

        // Get customer details with null check
        $customer = $customerModel->find($site['customer_id']);
        if (!$customer) {
            $customer = [
                'name' => 'Unknown Customer',
                'logo_path' => null
            ];
        }

        // Get all sites for dropdown
        $sites = $siteModel->where('company_id', $companyId)->findAll();

        // Get equipment for this site
        $equipment = $equipmentModel
            ->where('site_id', $id)
            ->where('company_id', $companyId)
            ->findAll();

        // Get inspections for this site, preferring inspection snapshot fields
        // so inspection-only devices still display correctly.
        $allInspections = $inspectionModel
            ->select([
                'inspections.*',
                'COALESCE(inspections.make, site_equipment.make) AS equipment_make',
                'COALESCE(inspections.model, site_equipment.model) AS equipment_model',
                'COALESCE(inspections.device_type, site_equipment.device_type) AS device_type',
                'COALESCE(inspections.serial_number, site_equipment.serial_number) AS serial_number',
                'COALESCE(inspections.asset_tag, site_equipment.asset_tag) AS asset_tag',
                'COALESCE(inspections.department, site_equipment.department) AS department',
                'COALESCE(inspections.location, site_equipment.location) AS location',
                'COALESCE(inspections.est, site_equipment.est) AS est',
                'COALESCE(inspections.cal, site_equipment.cal) AS cal',
                'COALESCE(u_tech.full_name, users.full_name) AS technician_name',
            ])
            ->join('users', 'users.id = inspections.technician_id', 'left')
            ->join('technicians t', 't.id = inspections.technician_id', 'left')
            ->join('users u_tech', 'u_tech.id = t.user_id', 'left')
            ->join('site_equipment', 'site_equipment.id = inspections.equipment_id', 'left')
            ->where('inspections.site_id', $id)
            ->where('inspections.company_id', $companyId)
            ->where('inspections.deleted_at', null)
            ->orderBy('inspections.group_id DESC, inspections.id DESC')
            ->findAll();

        // Build a list of unique inspections based on group_id.
        $inspections = [];
        $seenGroups = [];
        foreach ($allInspections as $inspection) {
            if (!in_array($inspection['group_id'], $seenGroups, true)) {
                $inspections[] = $inspection;
                $seenGroups[] = $inspection['group_id'];
            }
        }

        $inspectionList = array_map(function ($inspection) {
            return [
                'group_id'        => $inspection['group_id'],
                'scheduled_at'    => $inspection['scheduled_at'] ?? $inspection['created_at'] ?? date('Y-m-d H:i:s'),
                'inspection_type' => $inspection['inspection_type'] ?? '',
                'title'           => $inspection['title'] ?? '',
                'technician_name' => $inspection['technician_name'] ?? 'N/A',
                'next_due_date'   => $inspection['next_due_date'] ?? null,
                'status'          => $inspection['status'] ?? '',
                'completed_at'    => $inspection['completed_at'] ?? null,
            ];
        }, $inspections);

        $notInspected = [];
        $inspectedItems = [];
        $archivedItems = [];
        $inspectedEquipmentIds  = []; // keyed by equipment_id (master)
        $inspectedAssetTags     = []; // keyed by asset_tag (catches site-only equipment)
        $equipmentGroupMap = [];
        foreach ($inspections as $insp) {
            if (!empty($insp['equipment_id']) && !empty($insp['group_id'])) {
                $equipmentGroupMap[$insp['equipment_id']] = $insp['group_id'];
            }
        }

        foreach ($allInspections as $record) {
            $statusLower = strtolower($record['status'] ?? '');
            $isDone = !empty($record['completed_at']) || in_array($statusLower, ['completed', 'pass', 'fail', 'repair'], true);
            if (!$isDone) {
                continue;
            }
            // Track by master equipment_id AND by asset_tag so site-only
            // equipment (no master link) is also marked as Inspected.
            if (!empty($record['equipment_id'])) {
                $inspectedEquipmentIds[$record['equipment_id']] = true;
            }
            if (!empty($record['asset_tag'])) {
                $inspectedAssetTags[strtolower(trim($record['asset_tag']))] = true;
            }
            $resolvedGroupId = !empty($record['group_id']) ? $record['group_id'] : ($equipmentGroupMap[$record['equipment_id']] ?? '');
            $inspectedItems[] = [
                'id'              => $record['id'],
                'group_id'        => $resolvedGroupId,
                'equipment_id'    => $record['equipment_id'],
                'make'            => $record['equipment_make'],
                'model'           => $record['equipment_model'],
                'device_type'     => $record['device_type'],
                'serial_number'   => $record['serial_number'],
                'asset_tag'       => $record['asset_tag'],
                'department'      => $record['department'],
                'location'        => $record['location'],
                'technician'      => $record['technician_name'],
                'est'             => $record['est'] ?? '0',
                'cal'             => $record['cal'] ?? '0',
                'result'          => $record['status'],
                'notes'           => $record['notes'],
                'inspection_date' => $record['completed_at'] ?? $record['scheduled_at'],
            ];
        }

        foreach ($equipment as &$eq) {
            $st = strtolower($eq['status'] ?? '');
            if ($st === 'out_of_service' || $st === 'archived') {
                $archivedItems[] = $eq;
                $eq['inspection_status'] = 'Archived';
                continue;
            }
            // Check by master equipment_id OR asset_tag (for site-only equipment)
            $isInspected = isset($inspectedEquipmentIds[$eq['id']])
                || isset($inspectedAssetTags[strtolower(trim($eq['asset_tag'] ?? ''))]);
            if ($isInspected) {
                $eq['inspection_status'] = 'Inspected';
                continue;
            }
            $notInspected[] = $eq;
            $eq['inspection_status'] = 'Not Inspected';
        }
        unset($eq);

        // Get work orders for this site
       $workOrders = $workOrderModel
        ->select("
            work_orders.*,
            work_orders.group_id,
            se.asset_tag,
            se.serial_number,
            se.make,
            se.model,
            technician_user.full_name AS assigned_to_name
        ")
        ->join(
            'site_equipment se',
            'se.id = work_orders.equipment_id AND se.deleted_at IS NULL',
            'left'
        )
        ->join('technicians', 'technicians.id = work_orders.assigned_to', 'left')
        ->join('users AS technician_user', 'technician_user.id = technicians.user_id', 'left')
        ->where('work_orders.site_id', $id)
        ->where('work_orders.company_id', $companyId)
        ->where('work_orders.deleted_at', null)
        ->findAll();

        // Fetch all technicians
        $technicians = $technicianModel
            ->select('technicians.*, users.full_name')
            ->join('users', 'users.id = technicians.user_id', 'left')
            ->where('technicians.company_id', $companyId)
            ->findAll();

        $data = [
            'site'            => $site,
            'customer'        => $customer,
            'sites'           => $sites,
            'equipment'       => $equipment,
            'inspections'     => $inspections,
            'inspectionList'  => $inspectionList,
            'notInspected'    => $notInspected,
            'inspectedItems'  => $inspectedItems,
            'archivedItems'   => $archivedItems,
            'workOrders'      => $workOrders,
            'technicians'     => $technicians,
            'users'           => $technicians,
        ];

        return view('admin/sites/details', $data);
    }
}
    /**
     * AJAX endpoint: return equipment list for a site as JSON.
     * GET admin/sites/equipment-data/:id
     */
    public function equipmentData($id)
    {
        $companyId = $this->session->get('company_id');
        $equipmentModel = new \App\Models\SiteEquipmentModel();

        $equipment = $equipmentModel
            ->where('site_id', $id)
            ->where('company_id', $companyId)
            ->findAll();

        $rows = [];
        foreach ($equipment as $eq) {
            $rawStatus = trim($eq['status'] ?? '');
            $statusMap = [
                'ready'          => 'Ready',
                'need_attention' => 'Need Attention',
                'repair'         => 'Repair',
                'out_of_service' => 'Out of Service',
            ];
            $rows[] = [
                'id'            => $eq['id'],
                'asset_tag'     => $eq['asset_tag'] ?? '',
                'make'          => $eq['make'] ?? 'N/A',
                'model'         => $eq['model'] ?? 'N/A',
                'serial_number' => $eq['serial_number'] ?? 'N/A',
                'device_type'   => $eq['device_type'] ?? 'N/A',
                'location'      => $eq['location'] ?? 'N/A',
                'department'    => $eq['department'] ?? 'N/A',
                'status'        => $rawStatus,
                'status_label'  => $statusMap[$rawStatus] ?? ($rawStatus ?: 'No Status'),
            ];
        }

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    /**
     * AJAX endpoint: return work orders list for a site as JSON.
     * GET admin/sites/work-orders-data/:id
     */
    public function workOrdersData($id)
    {
        $companyId = $this->session->get('company_id');
        $workOrderModel = new WorkOrderModel();

        $workOrders = $workOrderModel
            ->select("
                work_orders.*,
                se.asset_tag,
                se.serial_number,
                se.make,
                se.model,
                technician_user.full_name AS assigned_to_name
            ")
            ->join('site_equipment se', 'se.id = work_orders.equipment_id AND se.deleted_at IS NULL', 'left')
            ->join('technicians', 'technicians.id = work_orders.assigned_to', 'left')
            ->join('users AS technician_user', 'technician_user.id = technicians.user_id', 'left')
            ->where('work_orders.site_id', $id)
            ->where('work_orders.company_id', $companyId)
            ->where('work_orders.deleted_at', null)
            ->orderBy('work_orders.id', 'DESC')
            ->findAll();

        $rows = [];
        foreach ($workOrders as $wo) {
            $rows[] = [
                'id'                => $wo['id'],
                'title'             => $wo['title'],
                'asset_tag'         => $wo['asset_tag'] ?? 'N/A',
                'serial_number'     => $wo['serial_number'] ?? '',
                'make'              => $wo['make'] ?? '',
                'model'             => $wo['model'] ?? '',
                'status'            => $wo['status'],
                'priority'          => $wo['priority'],
                'assigned_to'       => $wo['assigned_to'] ?? null,
                'assigned_to_name'  => $wo['assigned_to_name'] ?? 'Unassigned',
                'start_date'        => $wo['start_date'] ?? '',
                'end_date'          => $wo['end_date'] ?? '',
                'description'       => $wo['description'] ?? '',
                'equipment_id'      => $wo['equipment_id'] ?? null,
                'group_id'          => $wo['group_id'] ?? '',
            ];
        }

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }


