<?php

namespace App\Controllers\Technician;

use App\Controllers\BaseController;

class SchedulingController extends BaseController
{
    public function index()
    {
        $companyId = (int) session('company_id');
        $userId    = (int) session('user_id');
        $db        = \Config\Database::connect();

        // Resolve this technician's ID
        $tech   = $db->query(
            "SELECT id FROM technicians WHERE user_id = ? AND company_id = ? AND deleted_at IS NULL LIMIT 1",
            [$userId, $companyId]
        )->getRow();
        $techId = $tech ? (int)$tech->id : 0;

        // Work orders assigned to this technician
        $workOrders = $techId ? $db->query("
            SELECT wo.id, wo.title, wo.status, wo.priority, wo.start_date, wo.end_date, wo.created_at,
                   s.name AS site_name, c.name AS customer_name,
                   e.make, e.model
            FROM work_orders wo
            LEFT JOIN sites s     ON s.id = wo.site_id
            LEFT JOIN customers c ON c.id = s.customer_id
            LEFT JOIN equipment e ON e.id = wo.equipment_id
            WHERE wo.company_id = ? AND wo.assigned_to = ? AND wo.deleted_at IS NULL
            ORDER BY wo.start_date ASC, wo.id DESC
        ", [$companyId, $techId])->getResultArray() : [];

        // Inspections assigned to this technician (one row per group)
        $inspections = $techId ? $db->query("
            SELECT i.group_id, i.inspection_type, i.status,
                   i.scheduled_at, i.completed_at, i.next_due_date,
                   s.id AS site_id, s.name AS site_name, c.name AS customer_name,
                   COUNT(i.id) AS device_count
            FROM inspections i
            LEFT JOIN sites s     ON s.id = i.site_id
            LEFT JOIN customers c ON c.id = s.customer_id
            WHERE i.company_id = ? AND i.technician_id = ? AND i.group_id IS NOT NULL
            GROUP BY i.group_id
            ORDER BY COALESCE(i.scheduled_at, i.created_at) DESC
            LIMIT 60
        ", [$companyId, $techId])->getResultArray() : [];

        return view('technician/scheduling/index', [
            'workOrders'  => $workOrders,
            'inspections' => $inspections,
            'techId'      => $techId,
        ]);
    }

    /**
     * GET technician/scheduling/events
     * Returns FullCalendar-compatible JSON events for this technician's WOs and inspections.
     */
    public function events()
    {
        $companyId = (int) session('company_id');
        $userId    = (int) session('user_id');
        $db        = \Config\Database::connect();

        $tech   = $db->query(
            "SELECT id FROM technicians WHERE user_id = ? AND company_id = ? AND deleted_at IS NULL LIMIT 1",
            [$userId, $companyId]
        )->getRow();
        $techId = $tech ? (int)$tech->id : 0;

        $events = [];

        if ($techId) {
            // Work orders — RED
            $wos = $db->query("
                SELECT wo.id, wo.title, wo.status, wo.priority, wo.start_date, wo.end_date, s.name AS site_name
                FROM work_orders wo
                LEFT JOIN sites s ON s.id = wo.site_id
                WHERE wo.company_id = ? AND wo.assigned_to = ? AND wo.deleted_at IS NULL
            ", [$companyId, $techId])->getResultArray();

            foreach ($wos as $wo) {
                $start = !empty($wo['start_date']) ? $wo['start_date'] : substr($wo['created_at'] ?? date('Y-m-d'), 0, 10);
                $end   = !empty($wo['end_date'])   ? $wo['end_date']   : $start;
                $events[] = [
                    'id'    => 'wo-' . $wo['id'],
                    'title' => ($wo['title'] ?? 'Work Order') . ($wo['site_name'] ? ' · ' . $wo['site_name'] : ''),
                    'start' => $start,
                    'end'   => $end,
                    'color' => '#ef4444',
                    'extendedProps' => [
                        'event_type' => 'work_order',
                        'status'     => $wo['status'],
                        'priority'   => $wo['priority'],
                        'site_name'  => $wo['site_name'] ?? '—',
                    ],
                ];
            }

            // Inspections — GREEN
            $insps = $db->query("
                SELECT i.group_id, i.inspection_type, i.status, i.scheduled_at, s.name AS site_name
                FROM inspections i
                LEFT JOIN sites s ON s.id = i.site_id
                WHERE i.company_id = ? AND i.technician_id = ? AND i.group_id IS NOT NULL
                GROUP BY i.group_id
            ", [$companyId, $techId])->getResultArray();

            foreach ($insps as $insp) {
                $start = !empty($insp['scheduled_at']) ? substr($insp['scheduled_at'], 0, 10) : date('Y-m-d');
                $events[] = [
                    'id'    => 'insp-' . $insp['group_id'],
                    'title' => ($insp['inspection_type'] ?? 'Inspection') . ($insp['site_name'] ? ' · ' . $insp['site_name'] : ''),
                    'start' => $start,
                    'color' => '#10b981',
                    'extendedProps' => [
                        'event_type' => 'inspection',
                        'status'     => $insp['status'],
                        'site_name'  => $insp['site_name'] ?? '—',
                    ],
                ];
            }
        }

        return $this->response->setJSON($events);
    }
}
