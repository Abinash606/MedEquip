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
        $siteModel = new SiteModel();
        $companyId = $this->session->get('company_id');

        $rules = [
            'name'        => 'required|max_length[255]',
            'customer_id' => 'required|integer',
            'address'     => 'permit_empty|max_length[255]',
            'city'        => 'permit_empty|max_length[100]',
            'state'       => 'permit_empty|max_length[50]',
            'zip'         => 'permit_empty|max_length[20]',
            'contact_name' => 'permit_empty|max_length[255]',
            'email'       => 'permit_empty|valid_email|max_length[255]',
            'phone'       => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
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

        $siteModel->insert($siteData);

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

        // Update the site
        $model->update($id, $siteData);

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
        $equipmentModel = new EquipmentModel();
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

        // Get inspections for this site.  We join with equipment and users to
        // retrieve additional columns needed for the dynamic inspection
        // workflow (make, model, device type, serial number, etc.).
        // IMPORTANT: Include equipment.est and equipment.cal from Equipment Settings table
        $allInspections = $inspectionModel
            ->select('inspections.*, users.full_name as technician_name, equipment.make as equipment_make, equipment.model as equipment_model, equipment.device_type, equipment.serial_number, equipment.asset_tag, equipment.department, equipment.location, equipment.est, equipment.cal')
            ->join('users', 'users.id = inspections.technician_id', 'left')
            ->join('equipment', 'equipment.id = inspections.equipment_id', 'left')
            ->where('inspections.site_id', $id)
            ->where('inspections.company_id', $companyId)
            ->orderBy('inspections.group_id DESC, inspections.id DESC')
            ->findAll();

        // Build a list of unique inspections based on group_id.  This array
        // will be used for the dashboard view of the inspection workflow.
        $inspections = [];
        $seenGroups = [];
        foreach ($allInspections as $inspection) {
            if (!in_array($inspection['group_id'], $seenGroups)) {
                $inspections[] = $inspection;
                $seenGroups[] = $inspection['group_id'];
            }
        }

        // Compute dynamic lists for the inspection workflow.  We
        // categorize equipment into not inspected, inspected and archived.
        $inspectionList = $inspections; // alias for clarity
        $notInspected = [];
        $inspectedItems = [];
        $archivedItems = [];
        // Index equipment by ID for quick lookup
        $equipmentById = [];
        foreach ($equipment as $eq) {
            $equipmentById[$eq['id']] = $eq;
        }
        // Track equipment IDs that have at least one completed inspection
        $inspectedEquipmentIds = [];
        // Build a map of equipment_id -> group_id so legacy records with NULL
        // group_id can be back-filled from the unique inspection list.
        $equipmentGroupMap = [];
        foreach ($inspections as $insp) {
            if (!empty($insp['equipment_id']) && !empty($insp['group_id'])) {
                $equipmentGroupMap[$insp['equipment_id']] = $insp['group_id'];
            }
        }

        foreach ($allInspections as $record) {
            // A completed inspection means completed_at is not null OR
            // status is a known result value (Pass, Fail, Repair, completed).
            $statusLower = strtolower($record['status'] ?? '');
            $isDone = !empty($record['completed_at'])
                   || $statusLower === 'completed'
                   || $statusLower === 'pass'
                   || $statusLower === 'fail'
                   || $statusLower === 'repair';
            if ($isDone) {
                $inspectedEquipmentIds[$record['equipment_id']] = true;
                // Back-fill group_id for legacy records that stored NULL in the DB.
                $resolvedGroupId = !empty($record['group_id'])
                    ? $record['group_id']
                    : ($equipmentGroupMap[$record['equipment_id']] ?? '');
                // Build inspected item row.  Each inspection record
                // corresponds to one row in the Inspected Items list.
                // EST/CAL now come from equipment table via JOIN (current values)
                $inspectedItems[] = [
                    'id'             => $record['id'],
                    'group_id'       => $resolvedGroupId,
                    'equipment_id'   => $record['equipment_id'],
                    'make'           => $record['equipment_make'],
                    'model'          => $record['equipment_model'],
                    'device_type'    => $record['device_type'],
                    'serial_number'  => $record['serial_number'],
                    'asset_tag'      => $record['asset_tag'],
                    'department'     => $record['department'],
                    'location'       => $record['location'],
                    'technician'     => $record['technician_name'],
                    'est'            => $record['est'] ?? '0', // From equipment table via JOIN
                    'cal'            => $record['cal'] ?? '0', // From equipment table via JOIN
                    'result'         => $record['status'],
                    'notes'          => $record['notes'],
                    'inspection_date'=> $record['completed_at'] ?? $record['scheduled_at'],
                ];
            }
        }
        // Determine not inspected and archived items
        // Also mark the inspection_status for each equipment for dynamic inventory display
        foreach ($equipment as &$eq) {
            // Flag archived if equipment is out of service
            if (isset($eq['status']) && strtolower($eq['status']) === 'out_of_service') {
                $archivedItems[] = $eq;
                // Set a status flag for the inventory table
                $eq['inspection_status'] = 'Archived';
                continue;
            }
            if (isset($inspectedEquipmentIds[$eq['id']])) {
                // Equipment has at least one completed inspection
                $eq['inspection_status'] = 'Inspected';
                // Already counted as inspected; skip adding to notInspected
                continue;
            }
            // Otherwise, equipment has not been inspected yet
            $notInspected[] = $eq;
            $eq['inspection_status'] = 'Not Inspected';
        }
        unset($eq); // break reference

        // Get work orders for this site
        $workOrders = $workOrderModel
            ->select('work_orders.*, work_orders.group_id, equipment.asset_tag, equipment.serial_number, 
                  technician_user.full_name as assigned_to_name')
            ->join('equipment', 'equipment.id = work_orders.equipment_id', 'left')
            ->join('technicians', 'technicians.id = work_orders.assigned_to', 'left')
            ->join('users as technician_user', 'technician_user.id = technicians.user_id', 'left')
            ->where('work_orders.site_id', $id)
            ->where('work_orders.company_id', $companyId)
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
