<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Inspection Reports controller for admin.
 */
class InspectionReportsController extends BaseController
{
    public function index()
    {
        $companyId = (int) $this->session->get('company_id');
        $customerModel   = new \App\Models\CustomerModel();
        $siteModel       = new \App\Models\SiteModel();
        $equipmentModel  = new \App\Models\EquipmentModel();
        $workModel       = new \App\Models\WorkOrderModel();
        $db = \Config\Database::connect();

        $inspectionsCount = (int) ($db->query(
            "SELECT COUNT(*) AS cnt
             FROM (
                SELECT COALESCE(NULLIF(group_id, ''), CONCAT('ROW-', id)) AS group_key
                FROM inspections
                WHERE company_id = ?
                  AND deleted_at IS NULL
                GROUP BY COALESCE(NULLIF(group_id, ''), CONCAT('ROW-', id))
             ) g",
            [$companyId]
        )->getRow()->cnt ?? 0);

        $data = [
            'customersCount'   => $customerModel->where('company_id', $companyId)->countAllResults(),
            'sitesCount'       => $siteModel->where('company_id', $companyId)->countAllResults(),
            'equipmentCount'   => $equipmentModel->where('company_id', $companyId)->countAllResults(),
            'inspectionsCount' => $inspectionsCount,
            'workOrdersCount'  => $workModel->where('company_id', $companyId)->countAllResults(),
        ];
        return view('admin/inspections/index', $data);
    }

    /**
     * Return grouped inspection data for the reports DataTable.
     * Route: GET /admin/inspection-reports/list
     */
    public function listData()
    {
        $companyId = (int) $this->session->get('company_id');
        if (!$companyId) {
            return $this->response->setJSON(['data' => []]);
        }

        $db = \Config\Database::connect();

        $rows = $db->query("
            SELECT
                i.id,
                i.site_id,
                g.group_key AS group_id,
                g.result,
                i.notes,
                COALESCE(NULLIF(i.action_performed, ''), NULLIF(i.inspection_type, ''), '—') AS action_performed,
                i.completed_at AS inspection_date,
                i.next_due_date,
                i.est,
                i.cal,
                i.pm_frequency,
                COALESCE(i.asset_tag, e.asset_tag) AS asset_tag,
                COALESCE(i.make, e.make) AS make,
                COALESCE(i.model, e.model) AS model,
                COALESCE(i.device_type, e.device_type) AS device_type,
                COALESCE(i.serial_number, e.serial_number) AS serial_number,
                COALESCE(i.department, e.department) AS department,
                COALESCE(i.location, e.location) AS room,
                s.name AS site_name,
                c.name AS customer_name,
                COALESCE(u_tech.full_name, u_direct.full_name) AS technician_name
            FROM (
                SELECT
                    COALESCE(NULLIF(group_id, ''), CONCAT('ROW-', id)) AS group_key,
                    MAX(id) AS latest_id,
                    CASE
                        WHEN MAX(CASE
                            WHEN status IS NULL OR status = ''
                              OR LOWER(status) NOT IN ('pass','fail','repair','completed','closed/complete')
                            THEN 1 ELSE 0 END) = 1
                        THEN 'In Progress'
                        ELSE MAX(CASE
                            WHEN LOWER(COALESCE(status, '')) IN ('pass','fail','repair','completed','closed/complete')
                            THEN status ELSE '' END)
                    END AS result
                FROM inspections
                WHERE company_id = ?
                  AND deleted_at IS NULL
                GROUP BY COALESCE(NULLIF(group_id, ''), CONCAT('ROW-', id))
            ) g
            INNER JOIN inspections i ON i.id = g.latest_id
            LEFT JOIN site_equipment e ON e.id = i.equipment_id AND e.deleted_at IS NULL
            LEFT JOIN sites s      ON s.id = i.site_id
            LEFT JOIN customers c  ON c.id = s.customer_id
            LEFT JOIN technicians t ON t.id = i.technician_id
            LEFT JOIN users u_tech ON u_tech.id = t.user_id
            LEFT JOIN users u_direct ON u_direct.id = i.technician_id
            WHERE i.company_id = ?
              AND i.deleted_at IS NULL
            ORDER BY i.id DESC
            LIMIT 1000
        ", [$companyId, $companyId])->getResultArray();

        return $this->response->setJSON(['data' => $rows]);
    }
}
