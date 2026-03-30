<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\InspectionModel;
use App\Models\SiteModel;
use App\Models\EquipmentModel;

class InspectionsController extends BaseController
{
    public function index()
    {
        $companyId  = $this->session->get('company_id');
        $customerId = $this->session->get('customer_id');
        $db         = \Config\Database::connect();

        // Resolve the site IDs for this specific customer
        $siteSql    = "SELECT id FROM sites WHERE company_id = ? AND deleted_at IS NULL";
        $siteParams = [$companyId];
        if ($customerId) {
            $siteSql    .= " AND customer_id = ?";
            $siteParams[] = $customerId;
        }
        $siteRows = $db->query($siteSql, $siteParams)->getResultArray();
        $siteIds  = array_column($siteRows, 'id');

        if (empty($siteIds)) {
            return view('customer/inspections/index', [
                'upcomingInspections' => [],
                'inspectionHistory'   => [],
                'sites'               => [],
            ]);
        }

        $inList = implode(',', array_map('intval', $siteIds));

        // Upcoming = open/in-progress inspections for this customer's sites
        $upcoming = $db->query("
            SELECT i.id, i.group_id, i.status, i.scheduled_at, i.completed_at,
                   i.next_due_date, i.inspection_type,
                   e.make, e.model, e.device_type, e.asset_tag,
                   s.name AS site_name
            FROM inspections i
            LEFT JOIN equipment e ON e.id = i.equipment_id
            LEFT JOIN sites s     ON s.id = i.site_id
            WHERE i.site_id IN ($inList)
              AND i.status NOT IN ('Pass','Fail','Repair','pass','fail','repair','completed','Closed/Complete')
            GROUP BY i.group_id
            ORDER BY i.scheduled_at ASC
        ")->getResultArray();

        // History = completed inspections
        $history = $db->query("
            SELECT i.id, i.group_id, i.status, i.scheduled_at, i.completed_at,
                   i.next_due_date, i.inspection_type,
                   e.make, e.model, e.device_type, e.asset_tag,
                   s.name AS site_name,
                   u.full_name AS technician_name
            FROM inspections i
            LEFT JOIN equipment e   ON e.id = i.equipment_id
            LEFT JOIN sites s       ON s.id = i.site_id
            LEFT JOIN technicians t ON t.id = i.technician_id
            LEFT JOIN users u       ON u.id = t.user_id
            WHERE i.site_id IN ($inList)
              AND i.status IN ('Pass','Fail','Repair','pass','fail','repair','completed','Closed/Complete')
            GROUP BY i.group_id
            ORDER BY i.completed_at DESC
            LIMIT 50
        ")->getResultArray();

        // Site list for filtering dropdown
        $sites = $db->query(
            "SELECT id, name FROM sites WHERE id IN ($inList) ORDER BY name"
        )->getResultArray();

        return view('customer/inspections/index', [
            'upcomingInspections' => $upcoming,
            'inspectionHistory'   => $history,
            'sites'               => $sites,
        ]);
    }
}
