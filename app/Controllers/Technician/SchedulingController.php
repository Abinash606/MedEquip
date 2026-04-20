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
        // Pass / Fail / Repair remain visible as their own statuses until the
        // inspection is actually marked Closed/Complete.
        $inspections = $techId ? $db->query("
            SELECT
                i.group_id,
                COALESCE(MAX(NULLIF(i.inspection_type, '')), 'Inspection') AS inspection_type,
                MAX(CASE
                    WHEN LOWER(TRIM(COALESCE(i.status, ''))) IN ('closed/complete','closed_complete','closed','complete','completed')
                    THEN 1 ELSE 0 END) AS is_completed,
                CASE
                    WHEN MAX(CASE
                        WHEN LOWER(TRIM(COALESCE(i.status, ''))) IN ('closed/complete','closed_complete','closed','complete','completed')
                        THEN 1 ELSE 0 END) = 1
                    THEN 'Closed/Complete'
                    WHEN MAX(CASE
                        WHEN LOWER(TRIM(COALESCE(i.result, ''))) = 'repair'
                          OR LOWER(TRIM(COALESCE(i.status, ''))) = 'repair'
                        THEN 1 ELSE 0 END) = 1
                    THEN 'Repair'
                    WHEN MAX(CASE
                        WHEN LOWER(TRIM(COALESCE(i.result, ''))) = 'fail'
                          OR LOWER(TRIM(COALESCE(i.status, ''))) = 'fail'
                        THEN 1 ELSE 0 END) = 1
                    THEN 'Fail'
                    WHEN MAX(CASE
                        WHEN LOWER(TRIM(COALESCE(i.result, ''))) = 'pass'
                          OR LOWER(TRIM(COALESCE(i.status, ''))) = 'pass'
                        THEN 1 ELSE 0 END) = 1
                    THEN 'Pass'
                    ELSE 'In Progress'
                END AS status,
                MIN(i.scheduled_at) AS scheduled_at,
                MAX(i.completed_at) AS completed_at,
                MAX(i.next_due_date) AS next_due_date,
                s.id AS site_id, MAX(s.name) AS site_name, MAX(c.name) AS customer_name,
                COUNT(i.id) AS device_count
            FROM inspections i
            LEFT JOIN sites s     ON s.id = i.site_id
            LEFT JOIN customers c ON c.id = s.customer_id
            WHERE i.company_id = ? AND i.technician_id = ? AND i.group_id IS NOT NULL AND i.deleted_at IS NULL
            GROUP BY i.group_id, s.id
            ORDER BY COALESCE(MIN(i.scheduled_at), MAX(i.created_at)) DESC
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
            $showCompleted = $this->request->getGet('show_completed') === '1';
            // Work orders — RED
            $woSql = "
                SELECT wo.id, wo.title, wo.status, wo.priority, wo.start_date, wo.end_date, wo.created_at,
                       s.name AS site_name, c.name AS customer_name
                FROM work_orders wo
                LEFT JOIN sites s ON s.id = wo.site_id
                LEFT JOIN customers c ON c.id = s.customer_id
                WHERE wo.company_id = ? AND wo.assigned_to = ? AND wo.deleted_at IS NULL
            ";
            if (!$showCompleted) {
                $woSql .= " AND LOWER(TRIM(COALESCE(wo.status, ''))) NOT IN ('closed','completed','complete','done','resolved')";
            }
            $wos = $db->query($woSql, [$companyId, $techId])->getResultArray();

            foreach ($wos as $wo) {
                $start = !empty($wo['start_date']) ? $wo['start_date'] : substr($wo['created_at'] ?? date('Y-m-d'), 0, 10);
                $end   = !empty($wo['end_date'])   ? $wo['end_date']   : $start;
                $events[] = [
                    'id'    => 'wo-' . $wo['id'],
                    'title' => ($wo['title'] ?? 'Work Order') . ($wo['site_name'] ? ' · ' . $wo['site_name'] : ''),
                    'start' => $start,
                    'end'   => $end,
                    'color' => '#3b82f6',
                    'extendedProps' => [
                        'event_type' => 'work_order',
                        'status'     => $wo['status'],
                        'priority'   => $wo['priority'],
                        'site_name'  => $wo['site_name'] ?? '—',
                        'customer_name' => $wo['customer_name'] ?? '—',
                    ],
                ];
            }

            // Inspections — GREEN
            $inspSql = "
                SELECT
                    i.group_id,
                    COALESCE(MAX(NULLIF(i.inspection_type, '')), 'Inspection') AS inspection_type,
                    MAX(CASE
                        WHEN LOWER(TRIM(COALESCE(i.status, ''))) IN ('closed/complete','closed_complete','closed','complete','completed')
                        THEN 1 ELSE 0 END) AS is_completed,
                    CASE
                        WHEN MAX(CASE
                            WHEN LOWER(TRIM(COALESCE(i.status, ''))) IN ('closed/complete','closed_complete','closed','complete','completed')
                            THEN 1 ELSE 0 END) = 1
                        THEN 'Closed/Complete'
                        WHEN MAX(CASE
                            WHEN LOWER(TRIM(COALESCE(i.result, ''))) = 'repair'
                              OR LOWER(TRIM(COALESCE(i.status, ''))) = 'repair'
                            THEN 1 ELSE 0 END) = 1
                        THEN 'Repair'
                        WHEN MAX(CASE
                            WHEN LOWER(TRIM(COALESCE(i.result, ''))) = 'fail'
                              OR LOWER(TRIM(COALESCE(i.status, ''))) = 'fail'
                            THEN 1 ELSE 0 END) = 1
                        THEN 'Fail'
                        WHEN MAX(CASE
                            WHEN LOWER(TRIM(COALESCE(i.result, ''))) = 'pass'
                              OR LOWER(TRIM(COALESCE(i.status, ''))) = 'pass'
                            THEN 1 ELSE 0 END) = 1
                        THEN 'Pass'
                        ELSE 'In Progress'
                    END AS status,
                    MIN(i.scheduled_at) AS scheduled_at,
                    MAX(s.name) AS site_name,
                    MAX(c.name) AS customer_name
                FROM inspections i
                LEFT JOIN sites s ON s.id = i.site_id
                LEFT JOIN customers c ON c.id = s.customer_id
                WHERE i.company_id = ? AND i.technician_id = ? AND i.group_id IS NOT NULL AND i.deleted_at IS NULL
                GROUP BY i.group_id
            ";
            if (!$showCompleted) {
                $inspSql .= " HAVING is_completed = 0";
            }
            $insps = $db->query($inspSql, [$companyId, $techId])->getResultArray();

            $today = date('Y-m-d');
            foreach ($insps as $insp) {
                $start = !empty($insp['scheduled_at']) ? substr($insp['scheduled_at'], 0, 10) : date('Y-m-d');
                $statusLow = strtolower($insp['status'] ?? '');
                if (in_array($statusLow, ['closed/complete','closed','complete','completed'])) {
                    $color = '#6b7280';
                } elseif (in_array($statusLow, ['fail','repair'])) {
                    $color = '#ef4444';
                } elseif ($start < $today) {
                    $color = '#f97316';
                } else {
                    $color = '#10b981';
                }
                $events[] = [
                    'id'    => 'insp-' . $insp['group_id'],
                    'title' => ($insp['inspection_type'] ?? 'Inspection') . ($insp['site_name'] ? ' - ' . $insp['site_name'] : ''),
                    'start' => $start,
                    'color' => $color,
                    'editable' => false,
                    'extendedProps' => [
                        'event_type'    => 'inspection',
                        'group_id'      => $insp['group_id'],
                        'status'        => $insp['status'],
                        'site_name'     => $insp['site_name'] ?? '',
                        'customer_name' => $insp['customer_name'] ?? '',
                        'next_due_date' => $insp['next_due_date'] ?? '',
                    ],
                ];
                // Next-due-date purple event showing customer name
                if (!empty($insp['next_due_date']) && $insp['next_due_date'] > $today) {
                    $dueLabel = ($insp['customer_name'] ?? 'Customer') . ' - Next Due';
                    if (!empty($insp['site_name'])) $dueLabel .= ' (' . $insp['site_name'] . ')';
                    $daysUntilDue = (int)((strtotime($insp['next_due_date']) - time()) / 86400);
                    $dueColor = $daysUntilDue <= 30 ? '#f97316' : '#8b5cf6';
                    $events[] = [
                        'id'    => 'due-' . $insp['group_id'],
                        'title' => $dueLabel,
                        'start' => substr($insp['next_due_date'], 0, 10),
                        'color' => $dueColor,
                        'editable' => false,
                        'extendedProps' => [
                            'event_type'    => 'next_due',
                            'group_id'      => $insp['group_id'],
                            'status'        => 'Scheduled',
                            'site_name'     => $insp['site_name'] ?? '',
                            'customer_name' => $insp['customer_name'] ?? '',
                            'days_until_due' => $daysUntilDue,
                        ],
                    ];
                }
            }
        }

        return $this->response->setJSON($events);
    }
}
