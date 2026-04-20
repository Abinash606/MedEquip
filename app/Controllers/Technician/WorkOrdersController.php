<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;
use Config\Database;

class WorkOrdersController extends BaseController
{
    private function resolveTechnicianId(): int
    {
        $companyId = (int) $this->session->get('company_id');
        $userId    = (int) $this->session->get('user_id');

        $row = Database::connect()->table('technicians')
            ->select('id')
            ->where('company_id', $companyId)
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        return !empty($row['id']) ? (int) $row['id'] : 0;
    }

    public function index()
    {
        $db           = Database::connect();
        $companyId    = (int) $this->session->get('company_id');
        $technicianId = $this->resolveTechnicianId();

        $builder = $db->table('work_orders wo')
            ->select("wo.*, s.name AS site_name, c.name AS customer_name,
                      se.asset_tag, se.make, se.model")
            ->join('sites s',         's.id  = wo.site_id  AND s.deleted_at IS NULL',  'left')
            ->join('customers c',     'c.id  = s.customer_id AND c.deleted_at IS NULL','left')
            ->join('site_equipment se','se.id = wo.equipment_id AND se.deleted_at IS NULL','left')
            ->where('wo.company_id', $companyId)
            ->where('wo.deleted_at', null)
            ->orderBy('wo.id', 'DESC');

        if ($technicianId <= 0) {
            $workOrders = [];
        } else {
            $builder->where('wo.assigned_to', $technicianId);
            $workOrders = $builder->get()->getResultArray();
        }

        return view('technician/work_orders/index', ['workOrders' => $workOrders]);
    }

    public function show(int $id)
    {
        $db           = Database::connect();
        $companyId    = (int) $this->session->get('company_id');
        $technicianId = $this->resolveTechnicianId();

        $workOrder = $db->table('work_orders wo')
            ->select("wo.*, s.name AS site_name, c.name AS customer_name,
                      se.asset_tag, se.make, se.model, se.serial_number,
                      u.full_name AS assigned_to_name")
            ->join('sites s',          's.id  = wo.site_id  AND s.deleted_at IS NULL',   'left')
            ->join('customers c',      'c.id  = s.customer_id AND c.deleted_at IS NULL',  'left')
            ->join('site_equipment se','se.id = wo.equipment_id AND se.deleted_at IS NULL','left')
            ->join('technicians t',    't.id  = wo.assigned_to',                           'left')
            ->join('users u',          'u.id  = t.user_id',                                'left')
            ->where('wo.company_id', $companyId)
            ->where('wo.id', $id)
            ->where('wo.deleted_at', null);

        if ($technicianId > 0) {
            $workOrder->where('wo.assigned_to', $technicianId);
        }

        $workOrder = $workOrder->get()->getRowArray();

        if (!$workOrder) {
            return redirect()->to(site_url('technician/work-orders'))
                ->with('error', 'Work order not found.');
        }

        return view('technician/work_orders/show', ['workOrder' => $workOrder]);
    }
}
