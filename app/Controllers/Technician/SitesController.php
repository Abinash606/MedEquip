<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use Config\Database;

class SitesController extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        $userId    = session()->get('user_id');
        $companyId = session()->get('company_id');

        $states = $this->getTechnicianStates($db, $userId);

        if (empty($states)) {
            return view('technician/site/index', [
                'sites' => []
            ]);
        }

        $customerIds = $this->getAllowedCustomerIds($db, $companyId, $states);

        if (empty($customerIds)) {
            return view('technician/site/index', [
                'sites' => []
            ]);
        }

        $sites = $db->table('sites')
            ->select('
                sites.id,
                sites.name AS site_name,
                sites.address AS site_address,
                sites.contact_name AS site_contact_name,
                sites.phone AS site_phone,
                sites.email AS site_email,
                customers.name AS customer_name
            ')
            ->join(
                'customers',
                'customers.id = sites.customer_id AND customers.deleted_at IS NULL',
                'left'
            )
            ->where('sites.company_id', $companyId)
            ->whereIn('sites.customer_id', $customerIds)
            ->where('sites.deleted_at IS NULL', null, false)
            ->orderBy('sites.name', 'ASC')
            ->get()
            ->getResultArray();

        return view('technician/site/index', [
            'sites' => $sites
        ]);
    }
    public function view($id)
    {
        $db = Database::connect();

        $userId    = session()->get('user_id');
        $companyId = session()->get('company_id');

        $states = $this->getTechnicianStates($db, $userId);

        if (empty($states)) {
            return redirect()->to('technician/sites')->with('error', 'No technician state assigned.');
        }

        $customerIds = $this->getAllowedCustomerIds($db, $companyId, $states);

        if (empty($customerIds)) {
            return redirect()->to('technician/sites')->with('error', 'No accessible customers found for your assigned state.');
        }

        $site = $db->table('sites')
            ->select('
                sites.*,
                customers.name AS customer_name,
                customers.logo_path AS customer_logo
            ')
            ->join(
                'customers',
                'customers.id = sites.customer_id AND customers.deleted_at IS NULL',
                'left'
            )
            ->where('sites.id', $id)
            ->where('sites.company_id', $companyId)
            ->whereIn('sites.customer_id', $customerIds)
            ->where('sites.deleted_at IS NULL', null, false)
            ->get()
            ->getRowArray();

        if (!$site) {
            return redirect()->to('technician/sites')->with('error', 'Site not found or access denied.');
        }

        $equipment = $db->table('equipment')
            ->select('equipment.*')
            ->where('equipment.site_id', $id)
            ->where('equipment.company_id', $companyId)
            ->where('equipment.deleted_at IS NULL', null, false)
            ->orderBy('equipment.asset_tag', 'ASC')
            ->get()
            ->getResultArray();

        $allInspections = $db->table('inspections')
            ->select('
                inspections.*,
                users.full_name AS technician_name,
                equipment.asset_tag AS equipment_asset_tag
            ')
            ->join('users', 'users.id = inspections.technician_id', 'left')
            ->join('equipment', 'equipment.id = inspections.equipment_id', 'left')
            ->where('inspections.site_id', $id)
            ->where('inspections.company_id', $companyId)
            ->where('inspections.deleted_at IS NULL', null, false)
            ->orderBy('inspections.group_id', 'DESC')
            ->orderBy('inspections.id', 'DESC')
            ->get()
            ->getResultArray();

        $inspections = [];
        $seenGroups = [];

        foreach ($allInspections as $inspection) {
            $groupId = $inspection['group_id'] ?? $inspection['id'];

            if (!in_array($groupId, $seenGroups)) {
                $inspections[] = $inspection;
                $seenGroups[] = $groupId;
            }
        }

        $workOrders = $db->table('work_orders')
            ->select('
                work_orders.*,
                equipment.asset_tag AS equipment_asset_tag,
                equipment.serial_number AS equipment_serial,
                tech_users.full_name AS technician_name
            ')
            ->join('equipment', 'equipment.id = work_orders.equipment_id', 'left')
            ->join('technicians', 'technicians.id = work_orders.assigned_to', 'left')
            ->join('users AS tech_users', 'tech_users.id = technicians.user_id', 'left')
            ->where('work_orders.site_id', $id)
            ->where('work_orders.company_id', $companyId)
            ->where('work_orders.deleted_at IS NULL', null, false)
            ->orderBy('work_orders.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $customer = [
            'name'      => $site['customer_name'] ?? 'Unknown Customer',
            'logo_path' => $site['customer_logo'] ?? null
        ];

        return view('technician/site/details', [
            'site'            => $site,
            'customer'        => $customer,
            'equipment'       => $equipment,
            'inspections'     => $inspections,
            'workOrders'      => $workOrders,
            'equipmentCount'  => count($equipment),
            'inspectionCount' => count($inspections),
            'workOrderCount'  => count($workOrders)
        ]);
    }

    /**
     * Get technician assigned states as array
     */
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

    /**
     * Get customer IDs allowed for technician states
     */
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
