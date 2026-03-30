<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WorkOrderModel;
use App\Models\SiteModel;
use App\Models\EquipmentModel;
use App\Models\TechnicianModel;

class SchedulingController extends BaseController
{
    public function index()
    {
        $companyId  = session()->get('company_id');
        $db         = \Config\Database::connect();

        $sites       = (new SiteModel())->where('company_id', $companyId)->where('deleted_at', null)->findAll();
        $equipment   = (new EquipmentModel())->where('company_id', $companyId)->where('deleted_at', null)->findAll();

        $technicians = $db->query("
            SELECT t.id, t.user_id, u.full_name
            FROM technicians t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE t.company_id = ? AND t.deleted_at IS NULL
            ORDER BY u.full_name
        ", [$companyId])->getResultArray();

        // Work orders — ordered by start_date ascending (scheduled order)
        $upcomingWO = $db->query("
            SELECT
                wo.id, wo.title, wo.status, wo.priority,
                wo.start_date, wo.end_date, wo.created_at,
                'work_order' AS event_type,
                s.name AS site_name,
                c.name AS customer_name,
                e.make, e.model,
                u.full_name AS tech_name,
                t.id AS tech_id
            FROM work_orders wo
            LEFT JOIN sites s       ON s.id = wo.site_id
            LEFT JOIN customers c   ON c.id = s.customer_id
            LEFT JOIN equipment e   ON e.id = wo.equipment_id
            LEFT JOIN technicians t ON t.id = wo.assigned_to
            LEFT JOIN users u       ON u.id = t.user_id
            WHERE wo.company_id = ? AND wo.deleted_at IS NULL
            ORDER BY COALESCE(wo.start_date, wo.created_at) ASC, wo.id DESC
            LIMIT 100
        ", [$companyId])->getResultArray();

        // Inspections — scheduled but not yet completed, ordered by scheduled_at
        $upcomingInspections = $db->query("
            SELECT
                i.id, i.group_id AS title_id,
                CONCAT('Inspection: ', COALESCE(e.make,''), ' ', COALESCE(e.model,''), ' @ ', COALESCE(s.name,'')) AS title,
                i.status,
                'inspection' AS event_type,
                i.scheduled_at AS start_date,
                i.completed_at AS end_date,
                i.created_at,
                s.name AS site_name,
                c.name AS customer_name,
                e.make, e.model, e.asset_tag,
                u.full_name AS tech_name,
                i.technician_id AS tech_id
            FROM inspections i
            LEFT JOIN equipment e   ON e.id = i.equipment_id
            LEFT JOIN sites s       ON s.id = i.site_id
            LEFT JOIN customers c   ON c.id = s.customer_id
            LEFT JOIN users u       ON u.id = i.technician_id
            WHERE i.company_id = ? AND i.deleted_at IS NULL
            ORDER BY i.scheduled_at ASC, i.id DESC
            LIMIT 100
        ", [$companyId])->getResultArray();

        // Merge & sort all events by date
        $allEvents = array_merge($upcomingWO, $upcomingInspections);
        usort($allEvents, function($a, $b) {
            $da = $a['start_date'] ?? $a['created_at'] ?? '9999-12-31';
            $db_ = $b['start_date'] ?? $b['created_at'] ?? '9999-12-31';
            return strcmp($da, $db_);
        });

        // Group by technician for timeline
        $techSchedule = [];
        foreach ($upcomingWO as $wo) {
            $key  = $wo['tech_id'] ?? 0;
            $name = $wo['tech_name'] ?? 'Unassigned';
            if (!isset($techSchedule[$key])) {
                $initials = strtoupper(substr($name, 0, 1) . (strpos($name, ' ') !== false ? substr($name, strpos($name, ' ') + 1, 1) : ''));
                $techSchedule[$key] = ['name' => $name, 'initials' => $initials, 'work_orders' => []];
            }
            $techSchedule[$key]['work_orders'][] = $wo;
        }

        // Completed work orders count for dashboard widget
        $completedWO = (int)($db->query(
            "SELECT COUNT(*) AS cnt FROM work_orders WHERE company_id = ? AND status IN ('completed','closed')",
            [$companyId]
        )->getRow()->cnt ?? 0);

        return view('admin/scheduling/index', [
            'sites'               => $sites,
            'equipment'           => $equipment,
            'technicians'         => $technicians,
            'upcomingWO'          => $upcomingWO,
            'upcomingInspections' => $upcomingInspections,
            'allEvents'           => $allEvents,
            'techSchedule'        => array_values($techSchedule),
            'completedWO'         => $completedWO,
        ]);
    }

    public function events()
    {
        $companyId    = session()->get('company_id');
        $showAll      = $this->request->getGet('show_completed') === '1';
        $db           = \Config\Database::connect();

        $events = [];

        // Work Orders — RED on calendar
        // Default: open + in_progress only. Toggle includes completed/cancelled.
        $woStatusWhere = $showAll
            ? ''
            : " AND wo.status NOT IN ('completed','cancelled','closed')";

        $workOrders = $db->query("
            SELECT wo.*, s.name AS site_name, u.full_name AS tech_name
            FROM work_orders wo
            LEFT JOIN sites s       ON s.id  = wo.site_id
            LEFT JOIN technicians t ON t.id  = wo.assigned_to
            LEFT JOIN users u       ON u.id  = t.user_id
            WHERE wo.company_id = ? AND wo.deleted_at IS NULL $woStatusWhere
        ", [$companyId])->getResultArray();

        foreach ($workOrders as $wo) {
            $start = !empty($wo['start_date']) ? $wo['start_date'] : substr($wo['created_at'] ?? date('Y-m-d'), 0, 10);
            $end   = !empty($wo['end_date'])   ? $wo['end_date']   : $start;
            $events[] = [
                'id'             => 'wo-' . $wo['id'],
                'title'          => '#WO-' . str_pad($wo['id'], 4, '0', STR_PAD_LEFT) . ' ' . ($wo['title'] ?? 'Work Order'),
                'start'          => $start,
                'end'            => $end,
                'backgroundColor'=> '#ef4444',   // red
                'borderColor'    => '#dc2626',
                'textColor'      => '#fff',
                'extendedProps'  => [
                    'type'     => 'work_order',
                    'status'   => $wo['status'],
                    'priority' => $wo['priority'],
                    'tech'     => $wo['tech_name'] ?? 'Unassigned',
                    'site'     => $wo['site_name'] ?? '',
                ],
            ];
        }

        // Inspections — GREEN on calendar
        // Default: not yet passed/failed (scheduled/pending). Toggle shows all.
        $inspStatusWhere = $showAll
            ? ''
            : " AND (i.status IS NULL OR i.status = '' OR i.status NOT IN ('Pass','Fail','Repair'))";

        $inspections = $db->query("
            SELECT i.id, i.group_id, i.status, i.scheduled_at, i.completed_at,
                   e.make, e.model, e.asset_tag,
                   s.name AS site_name,
                   u.full_name AS tech_name
            FROM inspections i
            LEFT JOIN equipment e   ON e.id = i.equipment_id
            LEFT JOIN sites s       ON s.id = i.site_id
            LEFT JOIN users u       ON u.id = i.technician_id
            WHERE i.company_id = ? AND i.deleted_at IS NULL $inspStatusWhere
        ", [$companyId])->getResultArray();

        foreach ($inspections as $insp) {
            $start = $insp['scheduled_at'] ?? date('Y-m-d');
            $end   = $insp['completed_at'] ?? $start;
            $label = trim(($insp['make'] ?? '') . ' ' . ($insp['model'] ?? ''));
            if (!$label) $label = 'Inspection';
            $events[] = [
                'id'             => 'insp-' . $insp['id'],
                'title'          => $label . ' @ ' . ($insp['site_name'] ?? ''),
                'start'          => $start,
                'end'            => $end,
                'backgroundColor'=> '#16a34a',   // green
                'borderColor'    => '#15803d',
                'textColor'      => '#fff',
                'extendedProps'  => [
                    'type'     => 'inspection',
                    'status'   => $insp['status'],
                    'tech'     => $insp['tech_name'] ?? 'Unassigned',
                    'site'     => $insp['site_name'] ?? '',
                    'asset'    => $insp['asset_tag'] ?? '',
                ],
            ];
        }

        return $this->response->setJSON($events);
    }

    public function store()
    {
        $companyId = session()->get('company_id');
        $userId    = session()->get('user_id');
        $db        = \Config\Database::connect();

        $db->table('work_orders')->insert([
            'company_id'   => $companyId,
            'site_id'      => $this->request->getPost('site_id'),
            'equipment_id' => $this->request->getPost('equipment_id') ?: 0,
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'priority'     => $this->request->getPost('priority') ?: 'normal',
            'status'       => 'open',
            'assigned_to'  => $this->request->getPost('assigned_to') ?: null,
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date')   ?: null,
            'created_by'   => $userId,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->to('admin/scheduling')->with('success', 'Appointment created successfully');
    }
}
