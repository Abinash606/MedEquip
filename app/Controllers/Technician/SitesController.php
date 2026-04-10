<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use Config\Database;

class SitesController extends BaseController
{
    public function index()
    {
        $db        = Database::connect();
        $userId    = session()->get('user_id');
        $companyId = session()->get('company_id');

        // ── Read filter from session then immediately clear it ───
        $customerId = session()->get('site_customer_filter');
        session()->remove('site_customer_filter');

        $states = $this->getTechnicianStates($db, $userId);

        if (empty($states)) {
            return view('technician/site/index', [
                'sites'              => [],
                'customers'          => [],
                'active_customer_id' => null,
            ]);
        }

        $customerIds = $this->getAllowedCustomerIds($db, $companyId, $states);

        if (empty($customerIds)) {
            return view('technician/site/index', [
                'sites'              => [],
                'customers'          => [],
                'active_customer_id' => null,
            ]);
        }

        $query = $db->table('sites')
            ->select('
            sites.id,
            sites.name          AS site_name,
            sites.address       AS site_address,
            sites.contact_name  AS site_contact_name,
            sites.phone         AS site_phone,
            sites.email         AS site_email,
            customers.name      AS customer_name,
            customers.id        AS customer_id
        ')
            ->join('customers', 'customers.id = sites.customer_id AND customers.deleted_at IS NULL', 'left')
            ->where('sites.company_id', $companyId)
            ->whereIn('sites.customer_id', $customerIds)
            ->where('sites.deleted_at IS NULL', null, false)
            ->orderBy('sites.name', 'ASC');

        // ── Apply customer filter if coming from customer page ───
        if (!empty($customerId)) {
            $query->where('sites.customer_id', $customerId);
        }

        $sites = $query->get()->getResultArray();

        // ── Customers for Add Site dropdown ──────────────────────
        $customers = $db->table('customers')
            ->select('id, name')
            ->where('company_id', $companyId)
            ->whereIn('id', $customerIds)
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('technician/site/index', [
            'sites'              => $sites,
            'customers'          => $customers,
            'active_customer_id' => $customerId, // pre-selects filter dropdown
        ]);
    }

    public function view($id)
    {
        $db        = Database::connect();
        $userId    = session()->get('user_id');
        $companyId = session()->get('company_id');

        $states = $this->getTechnicianStates($db, $userId);
        if (empty($states)) {
            return redirect()->to('technician/sites')->with('error', 'No technician state assigned.');
        }
        $customerIds = $this->getAllowedCustomerIds($db, $companyId, $states);
        if (empty($customerIds)) {
            return redirect()->to('technician/sites')->with('error', 'No accessible customers.');
        }

        $siteModel       = new \App\Models\SiteModel();
        $customerModel   = new \App\Models\CustomerModel();
        $equipmentModel  = new \App\Models\SiteEquipmentModel();
        $inspectionModel = new \App\Models\InspectionModel();
        $workOrderModel  = new \App\Models\WorkOrderModel();
        $technicianModel = new \App\Models\TechnicianModel();

        // ── Site ─────────────────────────────────────────────────
        $site = $db->table('sites')
            ->select('sites.*, customers.name AS customer_name, customers.logo_path AS customer_logo')
            ->join('customers', 'customers.id = sites.customer_id AND customers.deleted_at IS NULL', 'left')
            ->where('sites.id', $id)
            ->where('sites.company_id', $companyId)
            ->whereIn('sites.customer_id', $customerIds)
            ->where('sites.deleted_at IS NULL', null, false)
            ->get()->getRowArray();

        if (!$site) {
            return redirect()->to('technician/sites')->with('error', 'Site not found or access denied.');
        }

        $customer = $customerModel->find($site['customer_id'])
            ?? ['name' => 'Unknown Customer', 'logo_path' => null];

        // ── Equipment — ALL for this site ─────────────────────────
        $equipment = $equipmentModel
            ->where('site_id',    $id)
            ->where('company_id', $companyId)
            ->findAll();

        // ── All inspections with snapshot fields preferred ───────────────────
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
                'COALESCE(users.full_name, users_direct.full_name) AS technician_name',
            ])
            ->join('technicians t', 't.id = inspections.technician_id', 'left')
            ->join('users', 'users.id = t.user_id', 'left')
            ->join('users AS users_direct', 'users_direct.id = inspections.technician_id', 'left')
            ->join('site_equipment', 'site_equipment.id = inspections.equipment_id', 'left')
            ->where('inspections.site_id',    $id)
            ->where('inspections.company_id', $companyId)
            ->where('inspections.deleted_at', null)
            ->orderBy('inspections.group_id DESC, inspections.id DESC')
            ->findAll();

        $inspections = [];
        $seenGroups  = [];
        foreach ($allInspections as $insp) {
            if (!in_array($insp['group_id'], $seenGroups, true)) {
                $inspections[] = $insp;
                $seenGroups[]  = $insp['group_id'];
            }
        }

        $inspectionList = array_map(function ($insp) {
            return [
                'group_id'        => $insp['group_id'],
                'scheduled_at'    => $insp['scheduled_at']    ?? $insp['created_at'] ?? date('Y-m-d H:i:s'),
                'inspection_type' => $insp['inspection_type'] ?? '',
                'title'           => $insp['title'] ?? '',
                'technician_name' => $insp['technician_name'] ?? 'N/A',
                'next_due_date'   => $insp['next_due_date']   ?? null,
                'status'          => $insp['status']          ?? '',
                'completed_at'    => $insp['completed_at']    ?? null,
            ];
        }, $inspections);

        $inspectedItems    = [];
        $equipmentGroupMap = [];
        foreach ($inspections as $insp) {
            if (!empty($insp['equipment_id']) && !empty($insp['group_id'])) {
                $equipmentGroupMap[$insp['equipment_id']] = $insp['group_id'];
            }
        }
        foreach ($allInspections as $record) {
            $statusLower = strtolower($record['status'] ?? '');
            $isDone = !empty($record['completed_at']) || in_array($statusLower, ['pass', 'fail', 'repair', 'completed'], true);
            if (!$isDone) continue;

            $resolvedGroupId = !empty($record['group_id'])
                ? $record['group_id']
                : ($equipmentGroupMap[$record['equipment_id']] ?? '');

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

        // Build two lookup maps: by master equipment_id AND by asset_tag.
        // Site-only equipment (no master_equipment_id) only matches by asset_tag.
        $globallyInspectedIds      = [];
        $globallyInspectedAssetTags = [];
        foreach ($allInspections as $record) {
            $doneStatuses = ['pass', 'fail', 'repair', 'completed'];
            if (!in_array(strtolower($record['status'] ?? ''), $doneStatuses, true)) continue;
            if (!empty($record['equipment_id'])) {
                $globallyInspectedIds[$record['equipment_id']] = true;
            }
            if (!empty($record['asset_tag'])) {
                $globallyInspectedAssetTags[strtolower(trim($record['asset_tag']))] = true;
            }
        }

        $notInspected = array_values(array_filter($equipment, function ($eq) use ($globallyInspectedIds, $globallyInspectedAssetTags) {
            $st = strtolower($eq['status'] ?? '');
            if ($st === 'out_of_service' || $st === 'archived') return false;
            if (isset($globallyInspectedIds[$eq['id']])) return false;
            if (isset($globallyInspectedAssetTags[strtolower(trim($eq['asset_tag'] ?? ''))])) return false;
            return true;
        }));

        $archivedItems = array_values(array_filter($equipment, function ($eq) {
            $st = strtolower($eq['status'] ?? '');
            return $st === 'out_of_service' || $st === 'archived';
        }));

        foreach ($equipment as &$eq) {
            $st = strtolower($eq['status'] ?? '');
            $isInspected = isset($globallyInspectedIds[$eq['id']])
                || isset($globallyInspectedAssetTags[strtolower(trim($eq['asset_tag'] ?? ''))]);
            if ($st === 'out_of_service' || $st === 'archived') {
                $eq['inspection_status'] = 'Archived';
            } elseif ($isInspected) {
                $eq['inspection_status'] = 'Inspected';
            } else {
                $eq['inspection_status'] = 'Not Inspected';
            }
        }
        unset($eq);

        // ── Work orders ───────────────────────────────────────────
        $workOrders = $workOrderModel
        ->select("
            work_orders.*,
            work_orders.group_id,
            se.asset_tag,
            se.serial_number,
            se.make,
            se.model,
            tech_user.full_name AS assigned_to_name
        ")
        ->join(
            'site_equipment se',
            'se.id = work_orders.equipment_id AND se.deleted_at IS NULL',
            'left'
        )
        ->join('technicians', 'technicians.id = work_orders.assigned_to', 'left')
        ->join('users AS tech_user', 'tech_user.id = technicians.user_id', 'left')
        ->where('work_orders.site_id', $id)
        ->where('work_orders.company_id', $companyId)
        ->where('work_orders.deleted_at', null)
        ->findAll();

        // ── Technicians list ──────────────────────────────────────
        $technicians = $technicianModel
            ->select('technicians.*, users.full_name')
            ->join('users', 'users.id = technicians.user_id', 'left')
            ->where('technicians.company_id', $companyId)
            ->findAll();

        // Resolve the logged-in technician's technicians.id for pre-selection
        $loggedInTechId = null;
        $userId = (int) session('user_id');
        if ($userId) {
            $techRow = $technicianModel->where('user_id', $userId)->where('company_id', $companyId)->first();
            $loggedInTechId = $techRow['id'] ?? null;
        }

        return view('technician/site/details', [
            'site'              => $site,
            'customer'          => $customer,
            'equipment'         => $equipment,
            'inspections'       => $inspections,
            'inspectionList'    => $inspectionList,
            'notInspected'      => $notInspected,
            'inspectedItems'    => $inspectedItems,
            'archivedItems'     => $archivedItems,
            'workOrders'        => $workOrders,
            'technicians'       => $technicians,
            'users'             => $technicians,
            'equipmentCount'    => count($equipment),
            'inspectionCount'   => count($inspections),
            'workOrderCount'    => count($workOrders),
            'loggedInTechId'    => $loggedInTechId,
        ]);
    }
    public function siteCreate()
    {
        $db        = Database::connect();
        $companyId = session()->get('company_id');

        $data = [
            'customer_id'  => $this->request->getPost('customer_id'),
            'name'         => $this->request->getPost('name'),
            'address'      => $this->request->getPost('address'),
            'city'         => $this->request->getPost('city'),
            'state'        => $this->request->getPost('state'),
            'zip'          => $this->request->getPost('zip'),
            'contact_name' => $this->request->getPost('contact_name'),
            'email'        => $this->request->getPost('email'),
            'phone'        => $this->request->getPost('phone'),
            'company_id'   => $companyId,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        // Check for duplicate site name within same company
        $existing = $db->table('sites')
            ->where('company_id', $companyId)
            ->where('name', $data['name'])
            ->where('deleted_at IS NULL', null, false)
            ->get()->getRowArray();
        if ($existing) {
            $msg = 'A site with this name already exists. Please use a unique site name.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        try {
            $db->table('sites')->insert($data);
        } catch (\Throwable $e) {
            $msg = stripos($e->getMessage(), 'Duplicate') !== false
                ? 'A site with this name already exists. Please use a unique site name.'
                : 'Failed to save site. Please try again.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        return redirect()->back()->with('success', 'Site added successfully.');
    }
    // ══════════════════════════════════════════════════════════════
    // EQUIPMENT — show (AJAX JSON for edit modal)
    // GET  technician/equipment/show/:id
    // ══════════════════════════════════════════════════════════════
    public function equipmentShow($id)
    {
        $db        = Database::connect();
        $companyId = session()->get('company_id');

        $eq = $db->table('site_equipment')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getRowArray();

        if (!$eq) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Equipment not found.']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $eq]);
    }

    /**
     * GET technician/equipment/dropdown-options
     * Returns distinct makes, models, device_types for autocomplete in the edit modal.
     */
    public function equipmentDropdownOptions()
    {
        $companyId = (int) session('company_id');
        $db        = Database::connect();

        // Union master equipment catalogue with site_equipment so that
        // devices added directly to a site (without a master record) also
        // appear in the autocomplete suggestions.
        $makes = array_column($db->query(
            "SELECT DISTINCT make FROM (
                SELECT make FROM equipment WHERE make IS NOT NULL AND make != '' AND company_id = ? AND deleted_at IS NULL
                UNION
                SELECT make FROM site_equipment WHERE make IS NOT NULL AND make != '' AND company_id = ? AND deleted_at IS NULL
            ) AS combined ORDER BY make",
            [$companyId, $companyId]
        )->getResultArray(), 'make');

        $models = array_column($db->query(
            "SELECT DISTINCT model FROM (
                SELECT model FROM equipment WHERE model IS NOT NULL AND model != '' AND company_id = ? AND deleted_at IS NULL
                UNION
                SELECT model FROM site_equipment WHERE model IS NOT NULL AND model != '' AND company_id = ? AND deleted_at IS NULL
            ) AS combined ORDER BY model",
            [$companyId, $companyId]
        )->getResultArray(), 'model');

        $deviceTypes = array_column($db->query(
            "SELECT DISTINCT device_type FROM (
                SELECT device_type FROM equipment WHERE device_type IS NOT NULL AND device_type != '' AND company_id = ? AND deleted_at IS NULL
                UNION
                SELECT device_type FROM site_equipment WHERE device_type IS NOT NULL AND device_type != '' AND company_id = ? AND deleted_at IS NULL
            ) AS combined ORDER BY device_type",
            [$companyId, $companyId]
        )->getResultArray(), 'device_type');

        return $this->response->setJSON([
            'success'      => true,
            'makes'        => $makes,
            'models'       => $models,
            'device_types' => $deviceTypes,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // EQUIPMENT — create
    // POST technician/equipment/create
    // ══════════════════════════════════════════════════════════════
    public function equipmentCreate()
    {
        $db        = Database::connect();
        $companyId = session()->get('company_id');
        $isAjax    = $this->request->isAJAX()
            || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false;

        $assetTag = trim((string) $this->request->getPost('asset_tag'));

        // ── Duplicate asset tag check ─────────────────────────────────
        $duplicate = $db->table('site_equipment')
            ->where('company_id', $companyId)
            ->where('asset_tag',  $assetTag)
            ->where('deleted_at IS NULL', null, false)
            ->countAllResults();

        if ($duplicate > 0) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Asset Tag "' . esc($assetTag) . '" is already registered. Please use a different Asset Tag.',
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Duplicate Asset Tag.');
        }
        $statusMap = [
            'ready'           => 'ready',
            'need_attention'  => 'need_attention',
            'repair'          => 'repair',
            'out_of_service'  => 'out_of_service',
        ];
        $statusInput = trim((string) $this->request->getPost('status') ?? 'ready');
        $status = $statusMap[$statusInput] ?? 'ready';
        $data = [
            'site_id'           => $this->request->getPost('site_id'),
            'asset_tag'         => $assetTag,
            'serial_number'     => $this->request->getPost('serial_number'),
            'make'              => $this->request->getPost('make'),
            'model'             => $this->request->getPost('model'),
            'device_type'       => $this->request->getPost('device_type'),
            'department'        => $this->request->getPost('department'),
            'location'          => $this->request->getPost('location'),
            'status'            => $status,
            'pm_kit'            => $this->request->getPost('pm_kit'),
            'fast_notes'        => $this->request->getPost('fast_notes'),
            'installation_date' => $this->request->getPost('installation_date') ?: null,
            'warranty_expires'  => $this->request->getPost('warranty_expires')  ?: null,
            'company_id'        => $companyId,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        try {
            $db->table('site_equipment')->insert($data);
            $newId      = $db->insertID();
            $data['id'] = $newId;

            if ($isAjax) {
                return $this->response->setJSON(['success' => true, 'data' => $data]);
            }
            return redirect()->back()->with('success', 'Equipment added successfully.');
        } catch (\Throwable $e) {
            // Catch any remaining DB constraint violations (race conditions, etc.)
            $userMessage = (stripos($e->getMessage(), 'Duplicate') !== false || stripos($e->getMessage(), 'uk_equipment') !== false)
                ? 'Asset Tag "' . esc($assetTag) . '" is already registered. Please use a different Asset Tag.'
                : 'Failed to save equipment. Please try again.';

            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $userMessage,
                ]);
            }
            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    // ══════════════════════════════════════════════════════════════
    // EQUIPMENT — update
    // POST technician/equipment/update/:id
    // ══════════════════════════════════════════════════════════════
    public function equipmentUpdate($id)
    {
        $db        = Database::connect();
        $companyId = session()->get('company_id');
        $isAjax    = $this->request->isAJAX()
            || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false;

        // ── Step 1: Verify ownership ──────────────────────────────────
        $exists = $db->table('site_equipment')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->where('deleted_at IS NULL', null, false)
            ->countAllResults();

        if (!$exists) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Equipment not found.',
                ]);
            }
            return redirect()->back()->with('error', 'Equipment not found.');
        }

        // ── Step 2: Get asset tag from POST ───────────────────────────
        $assetTag = trim((string) $this->request->getPost('asset_tag'));

        // ── Step 3: Duplicate check (exclude current record) ─────────
        $duplicate = $db->table('site_equipment')
            ->where('company_id', $companyId)
            ->where('asset_tag',  $assetTag)
            ->where('id !=',      $id)
            ->where('deleted_at IS NULL', null, false)
            ->countAllResults();

        if ($duplicate > 0) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Asset Tag "' . esc($assetTag) . '" is already registered on another device. Please use a unique Asset Tag.',
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Duplicate Asset Tag.');
        }
        $statusMap = [
            'ready'           => 'ready',
            'need_attention'  => 'need_attention',
            'repair'          => 'repair',
            'out_of_service'  => 'out_of_service',
        ];
        $statusInput = trim((string) $this->request->getPost('status') ?? 'ready');
        $status = $statusMap[$statusInput] ?? 'ready';
        // ── Step 4: Build data array ──────────────────────────────────
        $data = [
            'asset_tag'         => $assetTag,
            'serial_number'     => $this->request->getPost('serial_number'),
            'make'              => $this->request->getPost('make'),
            'model'             => $this->request->getPost('model'),
            'device_type'       => $this->request->getPost('device_type'),
            'department'        => $this->request->getPost('department'),
            'location'          => $this->request->getPost('location'),
            'status'            => $status,
            'pm_kit'            => $this->request->getPost('pm_kit'),
            'fast_notes'        => $this->request->getPost('fast_notes'),
            'installation_date' => $this->request->getPost('installation_date') ?: null,
            'warranty_expires'  => $this->request->getPost('warranty_expires')  ?: null,
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        // ── Step 5: Update with try/catch for any race condition ──────
        try {
            $db->table('site_equipment')->where('id', $id)->update($data);

            if ($isAjax) {
                return $this->response->setJSON(['success' => true]);
            }
            return redirect()->back()->with('success', 'Equipment updated successfully.');
        } catch (\Throwable $e) {
            $isDuplicate = stripos($e->getMessage(), 'Duplicate') !== false
                || stripos($e->getMessage(), 'uk_equipment') !== false;

            $userMessage = $isDuplicate
                ? 'Asset Tag "' . esc($assetTag) . '" is already registered on another device. Please use a unique Asset Tag.'
                : 'Failed to update equipment. Please try again.';

            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $userMessage,
                ]);
            }
            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }

    // ══════════════════════════════════════════════════════════════
    // EQUIPMENT — delete (soft)
    // GET technician/equipment/delete/:id
    // ══════════════════════════════════════════════════════════════
    public function equipmentDelete($id)
    {
        $db        = Database::connect();
        $companyId = session()->get('company_id');

        $db->table('site_equipment')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);

        return redirect()->back()->with('success', 'Equipment deleted.');
    }

    // ══════════════════════════════════════════════════════════════
    // WORK ORDERS — create
    // POST technician/work-orders/create
    // ══════════════════════════════════════════════════════════════
    public function workOrderCreate()
    {
        $db        = Database::connect();
        $companyId = (int) session()->get('company_id');
        $siteId    = (int) $this->request->getPost('site_id');
        $title     = trim((string) $this->request->getPost('title'));
        $groupId   = trim((string) $this->request->getPost('group_id'));

        if ($siteId <= 0 || $title === '') {
            $msg = 'Site and title are required.';
            return $this->woWantsJson()
                ? $this->response->setJSON(['success' => false, 'message' => $msg, 'csrf_hash' => csrf_hash()])
                : redirect()->back()->with('error', $msg);
        }

        $postedEquipId  = (int) $this->request->getPost('equipment_id');
        $postedAssetTag = trim((string) $this->request->getPost('asset_tag'));

        // Resolve site_equipment.id — nullable, no master equipment lookups needed
        if ($postedEquipId <= 0 && $postedAssetTag !== '') {
            $seRow = $db->table('site_equipment')
                ->select('id')
                ->where('company_id', $companyId)
                ->where('site_id',    $siteId)
                ->where('asset_tag',  $postedAssetTag)
                ->where('deleted_at', null)
                ->get()->getRowArray();
            if ($seRow) $postedEquipId = (int) $seRow['id'];
        }

        // Confirm it's a valid site_equipment row (or null — both are acceptable)
        $resolvedEquipId = null;
        if ($postedEquipId > 0) {
            $se = $db->table('site_equipment')->select('id')
                ->where('id', $postedEquipId)->where('company_id', $companyId)
                ->where('deleted_at', null)->get()->getRowArray();
            if ($se) $resolvedEquipId = (int) $se['id'];
        }

        $data = [
            'site_id'      => $siteId,
            'equipment_id' => $resolvedEquipId, // site_equipment.id or null
            'title'        => $title,
            'description'  => trim((string) $this->request->getPost('description')),
            'status'       => $this->woNormalizeStatus($this->request->getPost('status')),
            'priority'     => $this->woNormalizePriority($this->request->getPost('priority')),
            'assigned_to'  => ($tmp = (int) $this->request->getPost('assigned_to')) > 0 ? $tmp : null,
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'group_id'     => $groupId !== '' ? $groupId : null,
            'company_id'   => $companyId,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $existing = null;
        if ($groupId !== '') {
            $b = $db->table('work_orders')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('group_id', $groupId)
                ->where('deleted_at', null);
            if ($resolvedEquipId) $b->where('equipment_id', $resolvedEquipId);
            $existing = $b->get()->getRowArray();
        }

        if ($existing) {
            $update = $data;
            unset($update['created_at']);
            $db->table('work_orders')->where('id', (int) $existing['id'])->update($update);
            $id = (int) $existing['id'];
        } else {
            $db->table('work_orders')->insert($data);
            $id = (int) $db->insertID();
        }

        $row = $this->fetchWorkOrderRow($companyId, $id);

        if ($this->woWantsJson()) {
            return $this->response->setJSON([
                'success'          => true,
                'message'          => $existing ? 'Work order updated successfully' : 'Work order created successfully',
                'work_order_id'    => $id,
                'title'            => $row['title'] ?? '',
                'priority'         => $row['priority'] ?? '',
                'status'           => $row['status'] ?? '',
                'assigned_to_name' => $row['assigned_to_name'] ?? 'N/A',
                'assigned_to'      => $row['assigned_to'] ?? null,
                'start_date'       => $row['start_date'] ?? '',
                'end_date'         => $row['end_date'] ?? '',
                'description'      => $row['description'] ?? '',
                'asset_tag'        => $row['asset_tag'] ?? '',
                'serial_number'    => $row['serial_number'] ?? '',
                'equipment_id'     => $row['equipment_id'] ?? null,
                'site_equipment_id'=> $row['site_equipment_id'] ?? null,
                'group_id'         => $row['group_id'] ?? '',
                'csrf_hash'        => csrf_hash(),
            ]);
        }

        return redirect()->back()->with('success', $existing ? 'Work order updated.' : 'Work order created.');
    }

    /**
     * GET technician/work-orders/findByGroup
     * Returns the auto-created WO for a given group_id + asset_tag so
     * the Fail+WO modal can UPDATE it instead of INSERTing a duplicate.
     */
    public function workOrderFindByGroup()
    {
        $companyId = (int) session()->get('company_id');
        $groupId   = trim((string) $this->request->getGet('group_id'));
        $assetTag  = trim((string) $this->request->getGet('asset_tag'));
        $siteId    = (int) $this->request->getGet('site_id');

        if ($groupId === '' || $assetTag === '') {
            return $this->response->setJSON(['success' => false]);
        }

        $db = Database::connect();

        // Try to find WO by group + asset_tag via site_equipment join
        $wo = $db->query(
            "SELECT wo.id, wo.title, wo.description, wo.status, wo.priority, wo.assigned_to, wo.start_date, wo.end_date
             FROM work_orders wo
             LEFT JOIN site_equipment se ON se.id = wo.equipment_id AND se.deleted_at IS NULL
             WHERE wo.company_id = ? AND wo.group_id = ? AND wo.deleted_at IS NULL
               AND (se.asset_tag = ? OR wo.site_id = ?)
             ORDER BY wo.id DESC LIMIT 1",
            [$companyId, $groupId, $assetTag, $siteId]
        )->getRowArray();

        if (!$wo) {
            $wo = $db->table('work_orders')
                ->where('company_id', $companyId)
                ->where('group_id', $groupId)
                ->where('site_id', $siteId)
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->get()->getRowArray();
        }

        if (!$wo) {
            return $this->response->setJSON(['success' => false]);
        }

        return $this->response->setJSON([
            'success'       => true,
            'work_order_id' => $wo['id'],
            'title'         => $wo['title'] ?? '',
            'description'   => $wo['description'] ?? '',
            'status'        => $wo['status'] ?? 'open',
            'priority'      => $wo['priority'] ?? 'normal',
            'assigned_to'   => $wo['assigned_to'] ?? null,
            'start_date'    => $wo['start_date'] ?? '',
            'end_date'      => $wo['end_date'] ?? '',
        ]);
    }

    public function workOrderShow($id)
    {
        $companyId = (int) session()->get('company_id');
        $row = $this->fetchWorkOrderRow($companyId, (int) $id);

        return $this->response->setJSON([
            'success' => (bool) $row,
            'data'    => $row,
            'message' => $row ? '' : 'Work order not found.',
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // WORK ORDERS — update   POST technician/work-orders/update/:id
    // ══════════════════════════════════════════════════════════════
   public function workOrderUpdate($id)
    {
        $db        = Database::connect();
        $companyId = (int) session()->get('company_id');

        $existingRow = $this->fetchWorkOrderRow($companyId, (int) $id);
        if (!$existingRow) {
            if ($this->woWantsJson()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work order not found.',
                    'csrf_hash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->with('error', 'Work order not found.');
        }

        $siteId = (int) ($this->request->getPost('site_id') ?: $existingRow['site_id']);
        $postedEquipmentId = $this->request->getPost('equipment_id');

        $postedAssetTagUpd = trim((string) $this->request->getPost('asset_tag'));
        // Resolve site_equipment.id — keep existing if nothing valid posted
        $equipmentId = !empty($existingRow['equipment_id']) ? (int) $existingRow['equipment_id'] : null;
        $lookupId    = (int) $postedEquipmentId;

        if ($lookupId <= 0 && $postedAssetTagUpd !== '') {
            $r = $db->table('site_equipment')->select('id')
                ->where('company_id', $companyId)->where('site_id', $siteId)
                ->where('asset_tag', $postedAssetTagUpd)->where('deleted_at', null)
                ->get()->getRowArray();
            if ($r) $lookupId = (int) $r['id'];
        }
        if ($lookupId > 0) {
            $se = $db->table('site_equipment')->select('id')
                ->where('id', $lookupId)->where('company_id', $companyId)
                ->where('deleted_at', null)->get()->getRowArray();
            if ($se) $equipmentId = (int) $se['id'];
        }

        $data = [
            'equipment_id' => $equipmentId,
            'title'        => trim((string) $this->request->getPost('title')),
            'description'  => trim((string) $this->request->getPost('description')),
            'status'       => $this->woNormalizeStatus($this->request->getPost('status')),
            'priority'     => $this->woNormalizePriority($this->request->getPost('priority')),
            'assigned_to'  => ($tmp = (int) $this->request->getPost('assigned_to')) > 0 ? $tmp : null,
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $groupId = trim((string) $this->request->getPost('group_id'));
        if ($groupId !== '') {
            $data['group_id'] = $groupId;
        }

        $db->table('work_orders')->where('id', (int) $id)->update($data);

        // If serial_number was submitted, update it on the linked site_equipment row
        $postedSerial = trim((string) $this->request->getPost('serial_number'));
        if ($postedSerial !== '' && $equipmentId) {
            $db->table('site_equipment')
                ->where('id', $equipmentId)->where('company_id', $companyId)
                ->update(['serial_number' => $postedSerial, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $row = $this->fetchWorkOrderRow($companyId, (int) $id);

        if ($this->woWantsJson()) {
            return $this->response->setJSON([
                'success'          => true,
                'message'          => 'Work order updated successfully',
                'work_order_id'    => (int) $id,
                'title'            => $row['title'] ?? '',
                'priority'         => $row['priority'] ?? '',
                'status'           => $row['status'] ?? '',
                'assigned_to_name' => $row['assigned_to_name'] ?? 'N/A',
                'assigned_to'      => $row['assigned_to'] ?? null,
                'start_date'       => $row['start_date'] ?? '',
                'end_date'         => $row['end_date'] ?? '',
                'description'      => $row['description'] ?? '',
                'asset_tag'        => $row['asset_tag'] ?? '',
                'serial_number'    => $row['serial_number'] ?? '',
                'equipment_id'     => $row['equipment_id'] ?? null,
                'site_equipment_id'=> $row['site_equipment_id'] ?? null,
                'group_id'         => $row['group_id'] ?? '',
                'csrf_hash'        => csrf_hash(),
            ]);
        }

        return redirect()->back()->with('success', 'Work order updated.');
    }
    // ══════════════════════════════════════════════════════════════
    // WORK ORDERS — delete (soft)
    // GET technician/work-orders/delete/:id
    // ══════════════════════════════════════════════════════════════
    public function workOrderDelete($id)
    {
        $db        = Database::connect();
        $companyId = (int) session()->get('company_id');

        $exists = $db->table('work_orders')
            ->where('id', (int) $id)
            ->where('company_id', $companyId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$exists) {
            if ($this->woWantsJson()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work order not found.',
                    'csrf_hash' => csrf_hash(),
                ]);
            }
            return redirect()->back()->with('error', 'Work order not found.');
        }

        $db->table('work_orders')
            ->where('id', (int) $id)
            ->where('company_id', $companyId)
            ->update([
                'deleted_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($this->woWantsJson()) {
            return $this->response->setJSON([
                'success'       => true,
                'message'       => 'Work order deleted successfully',
                'work_order_id' => (int) $id,
                'csrf_hash'     => csrf_hash(),
            ]);
        }

        return redirect()->back()->with('success', 'Work order deleted.');
    }

    // ══════════════════════════════════════════════════════════════
    // Private helpers (unchanged from original)
    // ══════════════════════════════════════════════════════════════

    private function getTechnicianStates($db, $userId): array
    {
        $technician = $db->table('technicians')
            ->select('state')
            ->where('user_id', $userId)
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getRow();

        if (!$technician || empty($technician->state)) {
            return [];
        }

        $states = array_map('trim', explode(',', $technician->state));
        $states = array_filter($states);

        return array_values(array_unique($states));
    }

    private function getAllowedCustomerIds($db, $companyId, array $states): array
    {
        if (empty($states)) {
            return [];
        }

        $customers = $db->table('customers')
            ->select('id')
            ->where('company_id', $companyId)
            ->whereIn('billing_state', $states)
            ->where('deleted_at IS NULL', null, false)
            ->get()
            ->getResultArray();

        return array_column($customers, 'id');
    }

    private function woWantsJson(): bool
    {
        return $this->request->isAJAX()
            || stripos($this->request->getHeaderLine('Accept'), 'application/json') !== false
            || stripos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;
    }

    private function woNormalizeStatus(?string $status): string
    {
        $key = strtolower(trim((string) $status));
        $key = str_replace(['-', ' '], '_', $key);

        return match ($key) {
            'completed' => 'completed',
            'cancelled', 'canceled' => 'cancelled',
            'in_progress', 'inprogress' => 'in_progress',
            default => 'open',
        };
    }

    private function woNormalizePriority(?string $priority): string
    {
        $key = strtolower(trim((string) $priority));

        return match ($key) {
            'low' => 'low',
            'high', 'critical' => 'high',
            default => 'normal',
        };
    }

   private function extractAssetTagFromText(?string $text): string
    {
        $text = (string) $text;

        if (preg_match('/^Asset tag:\s*(.+)$/mi', $text, $m)) {
            return trim($m[1]);
        }

        return '';
    }

    private function resolveWorkOrderEquipmentReference(
        int $companyId,
        int $siteId,
        $postedEquipmentId,
        string $fallbackAssetTag = ''
    ): ?array {
        $postedEquipmentId = (int) $postedEquipmentId;
        $db = Database::connect();

        if ($postedEquipmentId > 0) {
            // 1) direct site_equipment.id
            $siteEq = $db->table('site_equipment')
                ->select('id, asset_tag, serial_number, make, model, device_type')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('id', $postedEquipmentId)
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();

            if ($siteEq) {
                return [
                    'site_equipment_id' => (int) $siteEq['id'],
                    'equipment_id'      => (int) $siteEq['id'],
                    'asset_tag'         => $siteEq['asset_tag'] ?? '',
                    'serial_number'     => $siteEq['serial_number'] ?? '',
                    'make'              => $siteEq['make'] ?? '',
                    'model'             => $siteEq['model'] ?? '',
                    'device_type'       => $siteEq['device_type'] ?? '',
                ];
            }

            // 2) legacy master_equipment_id
            $siteEq = $db->table('site_equipment')
                ->select('id, asset_tag, serial_number, make, model, device_type')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('master_equipment_id', $postedEquipmentId)
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if ($siteEq) {
                return [
                    'site_equipment_id' => (int) $siteEq['id'],
                    'equipment_id'      => (int) $siteEq['id'],
                    'asset_tag'         => $siteEq['asset_tag'] ?? '',
                    'serial_number'     => $siteEq['serial_number'] ?? '',
                    'make'              => $siteEq['make'] ?? '',
                    'model'             => $siteEq['model'] ?? '',
                    'device_type'       => $siteEq['device_type'] ?? '',
                ];
            }
        }

        if ($fallbackAssetTag !== '') {
            $siteEq = $db->table('site_equipment')
                ->select('id, asset_tag, serial_number, make, model, device_type')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('asset_tag', $fallbackAssetTag)
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if ($siteEq) {
                return [
                    'site_equipment_id' => (int) $siteEq['id'],
                    'equipment_id'      => (int) $siteEq['id'],
                    'asset_tag'         => $siteEq['asset_tag'] ?? '',
                    'serial_number'     => $siteEq['serial_number'] ?? '',
                    'make'              => $siteEq['make'] ?? '',
                    'model'             => $siteEq['model'] ?? '',
                    'device_type'       => $siteEq['device_type'] ?? '',
                ];
            }
        }

        return null;
    }

    private function fetchWorkOrderRow(int $companyId, int $id): ?array
    {
        $db = Database::connect();

        $row = $db->table('work_orders')
            ->select("
                work_orders.*,
                tech_user.full_name AS assigned_to_name
            ")
            ->join('technicians', 'technicians.id = work_orders.assigned_to', 'left')
            ->join('users AS tech_user', 'tech_user.id = technicians.user_id', 'left')
            ->where('work_orders.company_id', $companyId)
            ->where('work_orders.id', $id)
            ->where('work_orders.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        $siteId = (int) ($row['site_id'] ?? 0);
        $eqRef  = null;

        if (!empty($row['equipment_id'])) {
            $eqRef = $this->resolveWorkOrderEquipmentReference($companyId, $siteId, (int) $row['equipment_id']);
        }

        if (!$eqRef && !empty($row['group_id'])) {
            $insp = $db->table('inspections')
                ->select('asset_tag')
                ->where('company_id', $companyId)
                ->where('site_id', $siteId)
                ->where('group_id', $row['group_id'])
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if (!empty($insp['asset_tag'])) {
                $eqRef = $this->resolveWorkOrderEquipmentReference($companyId, $siteId, 0, trim((string) $insp['asset_tag']));
            }
        }

        if (!$eqRef) {
            $descAsset = $this->extractAssetTagFromText($row['description'] ?? '');
            if ($descAsset !== '') {
                $eqRef = $this->resolveWorkOrderEquipmentReference($companyId, $siteId, 0, $descAsset);
            }
        }

        $row['site_equipment_id'] = $eqRef['site_equipment_id'] ?? null;
        $row['asset_tag']         = $eqRef['asset_tag'] ?? '';
        $row['serial_number']     = $eqRef['serial_number'] ?? '';
        $row['make']              = $eqRef['make'] ?? '';
        $row['model']             = $eqRef['model'] ?? '';
        $row['device_type']       = $eqRef['device_type'] ?? '';

        return $row;
    }


}
    /**
     * AJAX: equipment list for a site as JSON
     * GET technician/sites/equipment-data/:id
     */
    public function equipmentData($id)
    {
        $companyId = session()->get('company_id');
        $equipmentModel = new \App\Models\SiteEquipmentModel();

        $equipment = $equipmentModel
            ->where('site_id', $id)
            ->where('company_id', $companyId)
            ->findAll();

        $statusMap = [
            'ready' => 'Ready', 'need_attention' => 'Need Attention',
            'repair' => 'Repair', 'out_of_service' => 'Out of Service',
        ];
        $rows = [];
        foreach ($equipment as $eq) {
            $s = trim($eq['status'] ?? '');
            $rows[] = [
                'id'            => $eq['id'],
                'asset_tag'     => $eq['asset_tag'] ?? '',
                'make'          => $eq['make'] ?? 'N/A',
                'model'         => $eq['model'] ?? 'N/A',
                'serial_number' => $eq['serial_number'] ?? 'N/A',
                'device_type'   => $eq['device_type'] ?? 'N/A',
                'location'      => $eq['location'] ?? 'N/A',
                'department'    => $eq['department'] ?? 'N/A',
                'status'        => $s,
                'status_label'  => $statusMap[$s] ?? ($s ?: 'No Status'),
            ];
        }
        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    /**
     * AJAX: work orders list for a site as JSON
     * GET technician/sites/work-orders-data/:id
     */
    public function workOrdersData($id)
    {
        $companyId = session()->get('company_id');
        $db = Database::connect();

        $rows = $db->table('work_orders wo')
            ->select("wo.*, se.asset_tag, se.serial_number, se.make, se.model,
                      tech_user.full_name AS assigned_to_name")
            ->join('site_equipment se', 'se.id = wo.equipment_id AND se.deleted_at IS NULL', 'left')
            ->join('technicians', 'technicians.id = wo.assigned_to', 'left')
            ->join('users AS tech_user', 'tech_user.id = technicians.user_id', 'left')
            ->where('wo.site_id', $id)
            ->where('wo.company_id', $companyId)
            ->where('wo.deleted_at', null)
            ->orderBy('wo.id', 'DESC')
            ->get()->getResultArray();

        $out = [];
        foreach ($rows as $wo) {
            $out[] = [
                'id'               => $wo['id'],
                'title'            => $wo['title'],
                'asset_tag'        => $wo['asset_tag'] ?? 'N/A',
                'serial_number'    => $wo['serial_number'] ?? '',
                'status'           => $wo['status'],
                'priority'         => $wo['priority'],
                'assigned_to'      => $wo['assigned_to'] ?? null,
                'assigned_to_name' => $wo['assigned_to_name'] ?? 'N/A',
                'start_date'       => $wo['start_date'] ?? '',
                'end_date'         => $wo['end_date'] ?? '',
                'description'      => $wo['description'] ?? '',
                'equipment_id'     => $wo['equipment_id'] ?? null,
                'group_id'         => $wo['group_id'] ?? '',
            ];
        }
        return $this->response->setJSON(['success' => true, 'data' => $out]);
    }


