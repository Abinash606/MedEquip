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
        $equipmentModel  = new \App\Models\EquipmentModel();
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

        // ── All inspections with joins ────────────────────────────
        // Join technicians → users to get full_name (FK: technician_id → technicians.id)
        $allInspections = $inspectionModel
            ->select('inspections.*,
                      users.full_name        AS technician_name,
                      equipment.make         AS equipment_make,
                      equipment.model        AS equipment_model,
                      equipment.device_type,
                      equipment.serial_number,
                      equipment.asset_tag,
                      equipment.department,
                      equipment.location,
                      equipment.est,
                      equipment.cal')
            ->join('technicians t', 't.id   = inspections.technician_id',      'left')
            ->join('users',         'users.id = t.user_id',                    'left')
            ->join('equipment',     'equipment.id = inspections.equipment_id', 'left')
            ->where('inspections.site_id',    $id)
            ->where('inspections.company_id', $companyId)
            ->orderBy('inspections.group_id DESC, inspections.id DESC')
            ->findAll();

        // ── One row per group (for dashboard list) ────────────────
        $inspections = [];
        $seenGroups  = [];
        foreach ($allInspections as $insp) {
            if (!in_array($insp['group_id'], $seenGroups)) {
                $inspections[] = $insp;
                $seenGroups[]  = $insp['group_id'];
            }
        }

        // ── inspectionList — shape for the partial's dashboard tab ─
        $inspectionList = array_map(function ($insp) {
            return [
                'group_id'        => $insp['group_id'],
                'scheduled_at'    => $insp['scheduled_at']    ?? $insp['created_at'] ?? date('Y-m-d H:i:s'),
                'inspection_type' => $insp['inspection_type'] ?? '',
                'technician_name' => $insp['technician_name'] ?? 'N/A',
                'next_due_date'   => $insp['next_due_date']   ?? null,
            ];
        }, $inspections);

        // ── inspectedItems — ALL completed records ────────────────
        // Each row carries its group_id so the JS can filter by group.
        $inspectedItems    = [];
        $equipmentGroupMap = [];
        foreach ($inspections as $insp) {
            if (!empty($insp['equipment_id']) && !empty($insp['group_id'])) {
                $equipmentGroupMap[$insp['equipment_id']] = $insp['group_id'];
            }
        }
        foreach ($allInspections as $record) {
            $statusLower = strtolower($record['status'] ?? '');
            $isDone = !empty($record['completed_at'])
                || in_array($statusLower, ['pass', 'fail', 'repair', 'completed']);
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

        // ── notInspected — ALL non-archived equipment ─────────────
        //
        // IMPORTANT: We do NOT filter by "has ever been inspected".
        // The admin does this but it causes equipment to vanish from
        // Not Inspected permanently after the first inspection.
        //
        // Instead we pass ALL equipment here. The JS function
        // filterNotInspectedByGroup(groupId) dynamically hides rows
        // that already appear in the currently-open group's inspected
        // items. For a NEW inspection session, everything shows.
        $notInspected = array_values(array_filter($equipment, function ($eq) {
            $st = strtolower($eq['status'] ?? '');
            return $st !== 'out_of_service' && $st !== 'archived';
        }));

        // ── archivedItems ─────────────────────────────────────────
        $archivedItems = array_values(array_filter($equipment, function ($eq) {
            $st = strtolower($eq['status'] ?? '');
            return $st === 'out_of_service' || $st === 'archived';
        }));

        // ── inspection_status flag for All Inventory tab ──────────
        $globallyInspectedIds = [];
        foreach ($allInspections as $record) {
            if (in_array(strtolower($record['status'] ?? ''), ['pass', 'fail', 'repair', 'completed'])) {
                $globallyInspectedIds[$record['equipment_id']] = true;
            }
        }
        foreach ($equipment as &$eq) {
            $st = strtolower($eq['status'] ?? '');
            if ($st === 'out_of_service' || $st === 'archived') {
                $eq['inspection_status'] = 'Archived';
            } elseif (isset($globallyInspectedIds[$eq['id']])) {
                $eq['inspection_status'] = 'Inspected';
            } else {
                $eq['inspection_status'] = 'Not Inspected';
            }
        }
        unset($eq);

        // ── Work orders ───────────────────────────────────────────
        $workOrders = $workOrderModel
            ->select('work_orders.*, work_orders.group_id,
                      equipment.asset_tag, equipment.serial_number,
                      tech_user.full_name AS assigned_to_name')
            ->join('equipment',         'equipment.id = work_orders.equipment_id',   'left')
            ->join('technicians',       'technicians.id = work_orders.assigned_to',  'left')
            ->join('users AS tech_user', 'tech_user.id = technicians.user_id',        'left')
            ->where('work_orders.site_id',    $id)
            ->where('work_orders.company_id', $companyId)
            ->findAll();

        // ── Technicians list ──────────────────────────────────────
        $technicians = $technicianModel
            ->select('technicians.*, users.full_name')
            ->join('users', 'users.id = technicians.user_id', 'left')
            ->where('technicians.company_id', $companyId)
            ->findAll();

        return view('technician/site/details', [
            'site'            => $site,
            'customer'        => $customer,
            'equipment'       => $equipment,
            'inspections'     => $inspections,
            'inspectionList'  => $inspectionList,
            'notInspected'    => $notInspected,
            'inspectedItems'  => $inspectedItems,
            'archivedItems'   => $archivedItems,
            'workOrders'      => $workOrders,
            'technicians'     => $technicians,
            'users'           => $technicians,
            'equipmentCount'  => count($equipment),
            'inspectionCount' => count($inspections),
            'workOrderCount'  => count($workOrders),
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

        $db->table('sites')->insert($data);

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

        $eq = $db->table('equipment')
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
        $duplicate = $db->table('equipment')
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

        $data = [
            'site_id'           => $this->request->getPost('site_id'),
            'asset_tag'         => $assetTag,
            'serial_number'     => $this->request->getPost('serial_number'),
            'make'              => $this->request->getPost('make'),
            'model'             => $this->request->getPost('model'),
            'device_type'       => $this->request->getPost('device_type'),
            'department'        => $this->request->getPost('department'),
            'location'          => $this->request->getPost('location'),
            'status'            => 'ready',
            'pm_kit'            => $this->request->getPost('pm_kit'),
            'fast_notes'        => $this->request->getPost('fast_notes'),
            'installation_date' => $this->request->getPost('installation_date') ?: null,
            'warranty_expires'  => $this->request->getPost('warranty_expires')  ?: null,
            'company_id'        => $companyId,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        try {
            $db->table('equipment')->insert($data);
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
        $exists = $db->table('equipment')
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
        $duplicate = $db->table('equipment')
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

        // ── Step 4: Build data array ──────────────────────────────────
        $data = [
            'asset_tag'         => $assetTag,
            'serial_number'     => $this->request->getPost('serial_number'),
            'make'              => $this->request->getPost('make'),
            'model'             => $this->request->getPost('model'),
            'device_type'       => $this->request->getPost('device_type'),
            'department'        => $this->request->getPost('department'),
            'location'          => $this->request->getPost('location'),
            'status'            => 'ready',
            'pm_kit'            => $this->request->getPost('pm_kit'),
            'fast_notes'        => $this->request->getPost('fast_notes'),
            'installation_date' => $this->request->getPost('installation_date') ?: null,
            'warranty_expires'  => $this->request->getPost('warranty_expires')  ?: null,
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        // ── Step 5: Update with try/catch for any race condition ──────
        try {
            $db->table('equipment')->where('id', $id)->update($data);

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

        $db->table('equipment')
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
        $companyId = session()->get('company_id');

        // ── FIX: save group_id so the WO is linked to the inspection ──
        $groupId = trim((string) $this->request->getPost('group_id'));

        $data = [
            'site_id'      => $this->request->getPost('site_id'),
            'equipment_id' => $this->request->getPost('equipment_id') ?: null,
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'status'       => $this->request->getPost('status')      ?: 'open',
            'priority'     => $this->request->getPost('priority')    ?: 'medium',
            'assigned_to'  => $this->request->getPost('assigned_to') ?: null,
            'start_date'   => $this->request->getPost('start_date')  ?: null,
            'end_date'     => $this->request->getPost('end_date')    ?: null,
            'group_id'     => $groupId !== '' ? $groupId : null, // ← NEW
            'company_id'   => $companyId,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $db->table('work_orders')->insert($data);
        $newId = $db->insertID();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'       => true,
                'work_order_id' => $newId,
                'csrf_hash'     => csrf_hash(), // ← refresh CSRF for next call
            ]);
        }
        return redirect()->back()->with('success', 'Work order created.');
    }

    // ══════════════════════════════════════════════════════════════
    // WORK ORDERS — update   POST technician/work-orders/update/:id
    // ══════════════════════════════════════════════════════════════
    public function workOrderUpdate($id)
    {
        $db        = Database::connect();
        $companyId = session()->get('company_id');

        $exists = $db->table('work_orders')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->where('deleted_at IS NULL', null, false)
            ->countAllResults();

        if (!$exists) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Work order not found.',
                ]);
            }
            return redirect()->back()->with('error', 'Work order not found.');
        }

        $groupId = trim((string) $this->request->getPost('group_id'));

        $data = [
            'equipment_id' => $this->request->getPost('equipment_id') ?: null,
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'status'       => $this->request->getPost('status')      ?: 'open',
            'priority'     => $this->request->getPost('priority')    ?: 'medium',
            'assigned_to'  => $this->request->getPost('assigned_to') ?: null,
            'start_date'   => $this->request->getPost('start_date')  ?: null,
            'end_date'     => $this->request->getPost('end_date')    ?: null,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        // Only update group_id if provided (don't overwrite existing link)
        if ($groupId !== '') {
            $data['group_id'] = $groupId;
        }

        $db->table('work_orders')->where('id', $id)->update($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'csrf_hash' => csrf_hash(),
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
        $companyId = session()->get('company_id');

        $db->table('work_orders')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);

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
}
