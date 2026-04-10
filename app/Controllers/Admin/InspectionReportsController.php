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
        $companyId = $this->session->get('company_id');
        $customerModel   = new \App\Models\CustomerModel();
        $siteModel       = new \App\Models\SiteModel();
        $equipmentModel  = new \App\Models\EquipmentModel();
        $inspectionModel = new \App\Models\InspectionModel();
        $workModel       = new \App\Models\WorkOrderModel();

        $data = [
            'customersCount'   => $customerModel->where('company_id', $companyId)->countAllResults(),
            'sitesCount'       => $siteModel->where('company_id', $companyId)->countAllResults(),
            'equipmentCount'   => $equipmentModel->where('company_id', $companyId)->countAllResults(),
            'inspectionsCount' => $inspectionModel->where('company_id', $companyId)->countAllResults(),
            'workOrdersCount'  => $workModel->where('company_id', $companyId)->countAllResults(),
        ];
        return view('admin/inspections/index', $data);
    }

    /**
     * Return real inspection data for the reports DataTable.
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
                i.group_id,
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM inspections sub
                        WHERE sub.group_id = i.group_id
                          AND sub.company_id = i.company_id
                          AND (sub.status IS NULL OR sub.status = '' OR sub.status NOT IN ('Pass','Fail','Repair','pass','fail','repair','completed'))
                    ) THEN 'In Progress'
                    ELSE i.status
                END                AS result,
                i.notes,
                i.inspection_type  AS action_performed,                
                i.completed_at     AS inspection_date,
                i.next_due_date,
                i.est,
                i.cal,
                i.pm_frequency,
                COALESCE(e.asset_tag, i.asset_tag_snapshot) AS asset_tag,
                COALESCE(e.make, i.make_snapshot) AS make,
                COALESCE(e.model, i.model_snapshot) AS model,
                COALESCE(e.device_type, i.device_type_snapshot) AS device_type,
                COALESCE(e.serial_number, i.serial_number_snapshot) AS serial_number,
                COALESCE(e.department, i.department_snapshot) AS department,
                COALESCE(e.location, i.location_snapshot) AS location,
                s.name             AS site_name,
                c.name             AS customer_name,
                u.full_name        AS technician_name
            FROM inspections i
            LEFT JOIN site_equipment e ON e.id = i.equipment_id
            LEFT JOIN sites s      ON s.id = i.site_id
            LEFT JOIN customers c  ON c.id = s.customer_id
            LEFT JOIN users u      ON u.id = i.technician_id
            WHERE i.company_id = ?
            GROUP BY i.group_id
            ORDER BY i.id DESC
            LIMIT 1000
        ", [$companyId])->getResultArray();

        return $this->response->setJSON(['data' => $rows]);
    }
}
