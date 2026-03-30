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
                i.status           AS result,
                i.notes,
                i.inspection_type  AS action_performed,
                i.completed_at     AS inspection_date,
                i.next_due_date,
                i.est,
                i.cal,
                i.pm_frequency,
                e.asset_tag,
                e.make,
                e.model,
                e.device_type,
                e.serial_number,
                e.department,
                e.location         AS room,
                s.name             AS site_name,
                c.name             AS customer_name,
                u.full_name        AS technician_name
            FROM inspections i
            LEFT JOIN equipment e  ON e.id = i.equipment_id
            LEFT JOIN sites s      ON s.id = i.site_id
            LEFT JOIN customers c  ON c.id = s.customer_id
            LEFT JOIN users u      ON u.id = i.technician_id
            WHERE i.company_id = ?
            ORDER BY i.id DESC
            LIMIT 1000
        ", [$companyId])->getResultArray();

        return $this->response->setJSON(['data' => $rows]);
    }
}
