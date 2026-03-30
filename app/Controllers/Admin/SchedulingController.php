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

        // Get technicians with user names
        $technicians = $db->query("
            SELECT t.id, t.user_id, u.full_name
            FROM technicians t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE t.company_id = ?
            AND t.deleted_at IS NULL
            ORDER BY u.full_name
        ", [$companyId])->getResultArray();

        // Upcoming work orders for timeline (next 30 days + recent)
        $upcomingWO = $db->query("
            SELECT
                wo.id, wo.title, wo.status, wo.priority,
                wo.start_date, wo.end_date, wo.created_at,
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
            WHERE wo.company_id = ?
              AND wo.deleted_at IS NULL
            ORDER BY wo.start_date ASC, wo.id DESC
            LIMIT 100
        ", [$companyId])->getResultArray();

        // Group work orders by technician for timeline
        $techSchedule = [];
        foreach ($upcomingWO as $wo) {
            $key = $wo['tech_id'] ?? 0;
            $name = $wo['tech_name'] ?? 'Unassigned';
            if (!isset($techSchedule[$key])) {
                $techSchedule[$key] = ['name' => $name, 'initials' => strtoupper(substr($name, 0, 1) . (strpos($name, ' ') ? substr($name, strpos($name, ' ') + 1, 1) : '')), 'work_orders' => []];
            }
            $techSchedule[$key]['work_orders'][] = $wo;
        }

        return view('admin/scheduling/index', [
            'sites'        => $sites,
            'equipment'    => $equipment,
            'technicians'  => $technicians,
            'upcomingWO'   => $upcomingWO,
            'techSchedule' => array_values($techSchedule),
        ]);
    }

    public function events()
    {
        $companyId    = session()->get('company_id');
        $showCompleted = $this->request->getGet('show_completed') === '1';
        $db           = \Config\Database::connect();

        // ── Work Orders ──────────────────────────────────────────────
        $woSql = "
            SELECT wo.*, s.name AS site_name, u.full_name AS tech_name
            FROM work_orders wo
            LEFT JOIN sites s       ON s.id  = wo.site_id
            LEFT JOIN technicians t ON t.id  = wo.assigned_to
            LEFT JOIN users u       ON u.id  = t.user_id
            WHERE wo.company_id = ?
              AND wo.deleted_at IS NULL";
        if (!$showCompleted) {
            $woSql .= " AND wo.status NOT IN ('closed','completed')";
        }
        $workOrders = $db->query($woSql, [$companyId])->getResultArray();

        // ── Inspections ──────────────────────────────────────────────
        $inspSql = "
            SELECT i.id, i.group_id, i.site_id, i.technician_id,
                   i.scheduled_at, i.completed_at, i.status, i.inspection_type,
                   s.name AS site_name,
                   u.full_name AS tech_name
            FROM inspections i
            LEFT JOIN sites s       ON s.id = i.site_id
            LEFT JOIN technicians t ON t.id = i.technician_id
            LEFT JOIN users u       ON u.id = t.user_id
            WHERE i.company_id = ?
            GROUP BY i.group_id";
        if (!$showCompleted) {
            $inspSql .= " HAVING MAX(CASE WHEN i.status NOT IN ('Pass','Fail','Repair','pass','fail','repair','completed','Closed/Complete') THEN 1 ELSE 0 END) = 1";
        }
        $inspections = $db->query($inspSql, [$companyId])->getResultArray();

        $events = [];

        // Work order events — RED
        foreach ($workOrders as $wo) {
            $start = !empty($wo['start_date']) ? $wo['start_date'] : substr($wo['created_at'] ?? date('Y-m-d H:i:s'), 0, 10);
            $end   = !empty($wo['end_date'])   ? $wo['end_date']   : $start;
            $events[] = [
                'id'    => 'wo-' . $wo['id'],
                'title' => ($wo['title'] ?? 'Work Order') . ($wo['site_name'] ? ' · ' . $wo['site_name'] : ''),
                'start' => $start,
                'end'   => $end,
                'color' => '#ef4444',          // red = work order
                'extendedProps' => [
                    'event_type' => 'work_order',
                    'status'     => $wo['status'],
                    'priority'   => $wo['priority'],
                    'tech'       => $wo['tech_name'] ?? 'Unassigned',
                    'site_name'  => $wo['site_name'] ?? '—',
                ],
            ];
        }

        // Inspection events — GREEN
        foreach ($inspections as $insp) {
            $start = !empty($insp['scheduled_at'])
                ? substr($insp['scheduled_at'], 0, 10)
                : substr($insp['completed_at'] ?? date('Y-m-d H:i:s'), 0, 10);
            $label = ($insp['inspection_type'] ?? 'Inspection') . ($insp['site_name'] ? ' · ' . $insp['site_name'] : '');
            $events[] = [
                'id'    => 'insp-' . $insp['group_id'],
                'title' => $label,
                'start' => $start,
                'color' => '#10b981',          // green = inspection
                'extendedProps' => [
                    'event_type' => 'inspection',
                    'status'     => $insp['status'],
                    'priority'   => '—',
                    'tech'       => $insp['tech_name'] ?? 'Unassigned',
                    'site_name'  => $insp['site_name'] ?? '—',
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
            'equipment_id' => $this->request->getPost('equipment_id') ?: null,
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'priority'     => $this->request->getPost('priority') ?: 'medium',
            'status'       => 'open',
            'assigned_to'  => $this->request->getPost('assigned_to') ?: null,
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date')   ?: null,
            'created_by'   => $userId,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Appointment created']);
        }
        return redirect()->to('admin/scheduling')->with('success', 'Appointment created successfully');
    }

    private function getPriorityColor($priority): string
    {
        return match ($priority) {
            'critical' => '#ef4444',
            'high'     => '#f97316',
            'medium'   => '#3b82f6',
            'low'      => '#10b981',
            default    => '#6b7280',
        };
    }
}
