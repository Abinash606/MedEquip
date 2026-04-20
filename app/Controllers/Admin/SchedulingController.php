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

        // Scheduled inspections for tab (grouped by group_id)
        // IMPORTANT:
        // - Only real closure statuses count as completed (Closed/Complete style statuses)
        // - Pass / Fail / Repair remain visible as their own statuses until the inspection
        //   is actually closed/completed by the user.
        $scheduledInspections = $db->query("
            SELECT
                MIN(i.id) AS id,
                i.group_id,
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
                MAX(i.next_due_date) AS next_due_date,
                COALESCE(MAX(NULLIF(i.inspection_type, '')), 'Inspection') AS inspection_type,
                MAX(s.name) AS site_name,
                MAX(c.name) AS customer_name,
                MAX(u.full_name) AS tech_name
            FROM inspections i
            LEFT JOIN sites s       ON s.id = i.site_id
            LEFT JOIN customers c   ON c.id = s.customer_id
            LEFT JOIN technicians t ON t.id = i.technician_id
            LEFT JOIN users u       ON u.id = t.user_id
            WHERE i.company_id = ?
              AND i.deleted_at IS NULL
              AND i.group_id IS NOT NULL
            GROUP BY i.group_id
            ORDER BY MIN(i.scheduled_at) DESC, MAX(i.id) DESC
            LIMIT 100
        ", [$companyId])->getResultArray();

        return view('admin/scheduling/index', [
            'sites'                 => $sites,
            'equipment'             => $equipment,
            'technicians'           => $technicians,
            'upcomingWO'            => $upcomingWO,
            'techSchedule'          => array_values($techSchedule),
            'scheduledInspections'  => $scheduledInspections,
        ]);
    }

    public function events()
    {
        $companyId    = session()->get('company_id');
        $showCompleted = $this->request->getGet('show_completed') === '1';
        $db           = \Config\Database::connect();

        // ── Work Orders ──────────────────────────────────────────────
        $woSql = "
            SELECT wo.*, s.name AS site_name, c.name AS customer_name, u.full_name AS tech_name
            FROM work_orders wo
            LEFT JOIN sites s       ON s.id  = wo.site_id
            LEFT JOIN customers c   ON c.id  = s.customer_id
            LEFT JOIN technicians t ON t.id  = wo.assigned_to
            LEFT JOIN users u       ON u.id  = t.user_id
            WHERE wo.company_id = ?
              AND wo.deleted_at IS NULL";
        if (!$showCompleted) {
            $woSql .= " AND LOWER(TRIM(COALESCE(wo.status, ''))) NOT IN ('closed','completed','complete','done','resolved')";
        }
        $workOrders = $db->query($woSql, [$companyId])->getResultArray();

        // ── Inspections ──────────────────────────────────────────────
        $inspSql = "
            SELECT
                MIN(i.id) AS id,
                i.group_id,
                MIN(i.site_id) AS site_id,
                MIN(i.technician_id) AS technician_id,
                MIN(i.scheduled_at) AS scheduled_at,
                MAX(i.completed_at) AS completed_at,
                MAX(i.next_due_date) AS next_due_date,
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
                MAX(s.name) AS site_name,
                MAX(c.name) AS customer_name,
                MAX(u.full_name) AS tech_name
            FROM inspections i
            LEFT JOIN sites s       ON s.id = i.site_id
            LEFT JOIN customers c   ON c.id = s.customer_id
            LEFT JOIN technicians t ON t.id = i.technician_id
            LEFT JOIN users u       ON u.id = t.user_id
            WHERE i.company_id = ?
              AND i.deleted_at IS NULL
              AND i.group_id IS NOT NULL
            GROUP BY i.group_id";
        if (!$showCompleted) {
            $inspSql .= " HAVING is_completed = 0";
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
                'color' => '#3b82f6',          // blue = work order
                'extendedProps' => [
                    'event_type' => 'work_order',
                    'status'     => $wo['status'],
                    'priority'   => $wo['priority'],
                    'tech'       => $wo['tech_name'] ?? 'Unassigned',
                    'site_name'  => $wo['site_name'] ?? '—',
                    'customer_name' => $wo['customer_name'] ?? '—',
                ],
            ];
        }

        // Inspection events (GREEN = active, PURPLE = scheduled/next-due)
        $today = date('Y-m-d');
        foreach ($inspections as $insp) {
            $start = !empty($insp['scheduled_at'])
                ? substr($insp['scheduled_at'], 0, 10)
                : substr($insp['completed_at'] ?? date('Y-m-d H:i:s'), 0, 10);
            $label = ($insp['inspection_type'] ?? 'Inspection') . ($insp['site_name'] ? ' - ' . $insp['site_name'] : '');
            $statusLow = strtolower($insp['status'] ?? '');
            // Color: overdue = orange, fail/repair = red, closed = grey, active = green
            if (in_array($statusLow, ['closed/complete','closed','complete','completed'])) {
                $color = '#6b7280'; // grey = completed
            } elseif (in_array($statusLow, ['fail','repair'])) {
                $color = '#ef4444'; // red = fail/repair
            } elseif (!empty($insp['scheduled_at']) && substr($insp['scheduled_at'],0,10) < $today) {
                $color = '#f97316'; // orange = overdue
            } else {
                $color = '#10b981'; // green = active/in-progress
            }
            $events[] = [
                'id'    => 'insp-' . $insp['group_id'],
                'title' => $label,
                'start' => $start,
                'color' => $color,
                'editable' => true,
                'extendedProps' => [
                    'event_type'    => 'inspection',
                    'group_id'      => $insp['group_id'],
                    'status'        => $insp['status'],
                    'priority'      => '',
                    'tech'          => $insp['tech_name'] ?? 'Unassigned',
                    'site_name'     => $insp['site_name'] ?? '',
                    'customer_name' => $insp['customer_name'] ?? '',
                    'next_due_date' => $insp['next_due_date'] ?? '',
                ],
            ];

            // Add a SEPARATE next-due-date event (PURPLE) showing the customer name
            // This is the auto-scheduled follow-up inspection 12 months (or pm_frequency) out
            if (!empty($insp['next_due_date']) && $insp['next_due_date'] > $today) {
                $dueLabel = ($insp['customer_name'] ?? 'Customer') . ' - Next Due';
                if (!empty($insp['site_name'])) $dueLabel .= ' (' . $insp['site_name'] . ')';
                // Color: if due within 30 days = orange warning, else = purple scheduled
                $daysUntilDue = (int)((strtotime($insp['next_due_date']) - time()) / 86400);
                $dueColor = $daysUntilDue <= 30 ? '#f97316' : '#8b5cf6'; // orange if soon, purple if future
                $events[] = [
                    'id'    => 'due-' . $insp['group_id'],
                    'title' => $dueLabel,
                    'start' => substr($insp['next_due_date'], 0, 10),
                    'color' => $dueColor,
                    'editable' => true,
                    'extendedProps' => [
                        'event_type'    => 'next_due',
                        'group_id'      => $insp['group_id'],
                        'status'        => 'Scheduled',
                        'priority'      => '',
                        'tech'          => $insp['tech_name'] ?? 'Unassigned',
                        'site_name'     => $insp['site_name'] ?? '',
                        'customer_name' => $insp['customer_name'] ?? '',
                        'days_until_due' => $daysUntilDue,
                    ],
                ];
            }
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

    /**
     * POST admin/scheduling/reschedule
     * Called by FullCalendar eventDrop to persist a dragged event's new date.
     */
    public function reschedule()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid method']);
        }
        $companyId = session()->get('company_id');
        $db        = \Config\Database::connect();
        $eventId   = trim((string) $this->request->getPost('event_id'));
        $newDate   = trim((string) $this->request->getPost('new_date'));

        if (!$newDate) return $this->response->setJSON(['success' => false, 'message' => 'Missing date']);

        if (strpos($eventId, 'wo-') === 0) {
            $woId = (int) substr($eventId, 3);
            $db->table('work_orders')->where('id', $woId)->where('company_id', $companyId)
               ->update(['start_date' => $newDate, 'updated_at' => date('Y-m-d H:i:s')]);
        } elseif (strpos($eventId, 'insp-') === 0) {
            $groupId = substr($eventId, 5);
            $db->table('inspections')->where('group_id', $groupId)->where('company_id', $companyId)
               ->update(['scheduled_at' => $newDate . ' 00:00:00', 'updated_at' => date('Y-m-d H:i:s')]);
        } elseif (strpos($eventId, 'due-') === 0) {
            $groupId = substr($eventId, 4);
            $db->table('inspections')->where('group_id', $groupId)->where('company_id', $companyId)
               ->update(['next_due_date' => $newDate, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        return $this->response->setJSON(['success' => true, 'new_date' => $newDate]);
    }

    /**
     * POST admin/scheduling/send-reminder
     * Sends an email reminder to the customer and/or technician for a scheduled event.
     */
    public function sendReminder()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid method']);
        }
        $companyId = session()->get('company_id');
        $db        = \Config\Database::connect();
        $groupId   = trim((string) $this->request->getPost('group_id'));
        $eventType = trim((string) $this->request->getPost('event_type')); // inspection or work_order

        if (!$groupId) return $this->response->setJSON(['success' => false, 'message' => 'Missing group_id']);

        // Get inspection details + customer email
        $row = $db->query("
            SELECT i.group_id, i.next_due_date, i.inspection_type,
                   s.name AS site_name, c.name AS customer_name, c.email AS customer_email,
                   u.full_name AS tech_name, ue.email AS tech_email
            FROM inspections i
            LEFT JOIN sites s ON s.id = i.site_id
            LEFT JOIN customers c ON c.id = s.customer_id
            LEFT JOIN technicians t ON t.id = i.technician_id
            LEFT JOIN users u ON u.id = t.user_id
            LEFT JOIN users ue ON ue.id = t.user_id
            WHERE i.company_id = ? AND i.group_id = ? AND i.deleted_at IS NULL
            LIMIT 1
        ", [$companyId, $groupId])->getRowArray();

        if (!$row) return $this->response->setJSON(['success' => false, 'message' => 'Record not found']);

        $sent = [];
        $subject = 'Upcoming Inspection Reminder: ' . ($row['site_name'] ?? 'Your Site');
        $dueDate = !empty($row['next_due_date']) ? date('F j, Y', strtotime($row['next_due_date'])) : 'soon';
        $body    = '<p>Dear ' . htmlspecialchars($row['customer_name'] ?? 'Customer') . ',</p>'
            . '<p>This is a reminder that an inspection is scheduled for <strong>' . htmlspecialchars($row['site_name'] ?? '') . '</strong> on <strong>' . $dueDate . '</strong>.</p>'
            . '<p>Inspection Type: ' . htmlspecialchars($row['inspection_type'] ?? 'Equipment Inspection') . '</p>'
            . '<p>Technician: ' . htmlspecialchars($row['tech_name'] ?? 'TBD') . '</p>'
            . '<p>Please ensure the equipment is accessible on the scheduled date.</p>'
            . '<p>Thank you.</p>';

        $email = \Config\Services::email();
        if (!empty($row['customer_email'])) {
            $email->clear();
            $email->setTo($row['customer_email']);
            $email->setSubject($subject);
            $email->setMessage($body);
            $email->setMailType('html');
            if ($email->send(false)) $sent[] = $row['customer_email'];
        }
        if (!empty($row['tech_email']) && $row['tech_email'] !== $row['customer_email']) {
            $techBody = '<p>Hi ' . htmlspecialchars($row['tech_name'] ?? 'Technician') . ',</p>'
                . '<p>Reminder: Inspection at <strong>' . htmlspecialchars($row['site_name'] ?? '') . '</strong> for customer <strong>' . htmlspecialchars($row['customer_name'] ?? '') . '</strong> is due on <strong>' . $dueDate . '</strong>.</p>';
            $email->clear();
            $email->setTo($row['tech_email']);
            $email->setSubject('[Technician] ' . $subject);
            $email->setMessage($techBody);
            $email->setMailType('html');
            if ($email->send(false)) $sent[] = $row['tech_email'];
        }

        $msg = empty($sent) ? 'No email addresses found. Please update customer/technician records.' : 'Reminder sent to: ' . implode(', ', $sent);
        return $this->response->setJSON(['success' => !empty($sent), 'message' => $msg, 'sent_to' => $sent]);
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
