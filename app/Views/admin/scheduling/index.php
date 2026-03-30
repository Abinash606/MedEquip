<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 topbar">
    <div>
        <h3 class="fw-bold mb-0">Master Schedule</h3>
        <p class="text-muted small mb-0">Manage appointments, routes, and technician availability.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <div class="btn-group" role="group">
            <button id="btnTimeline" class="btn btn-outline-secondary active" onclick="switchView('timeline')">Timeline</button>
            <button id="btnCalendar" class="btn btn-outline-secondary"        onclick="switchView('calendar')">Calendar</button>
        </div>
        <div class="btn-group" role="group">
            <button id="btnDay"   class="btn btn-outline-secondary active" onclick="switchPeriod('day')">Day</button>
            <button id="btnWeek"  class="btn btn-outline-secondary"        onclick="switchPeriod('week')">Week</button>
            <button id="btnMonth" class="btn btn-outline-secondary"        onclick="switchPeriod('month')">Month</button>
        </div>
        <button class="btn btn-primary btn-new btn-sm" data-bs-toggle="modal" data-bs-target="#appointmentModal">
            <i class="fa-solid fa-plus"></i> Add Appointment
        </button>
    </div>
</div>

<div class="content">
<div class="glass-card">

    <!-- Date navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom p-3 flex-wrap gap-2" style="border-color:rgba(255,255,255,.08)!important;">
        <div class="d-flex gap-2 align-items-center">
            <button class="btn btn-light btn-sm" id="btnPrev"><i class="fa-solid fa-chevron-left"></i></button>
            <span class="fw-bold mx-2" id="currentDateLabel">Today, <?= date('M j, Y') ?></span>
            <button class="btn btn-light btn-sm" id="btnNext"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success me-1">Ready</span>
            <span class="badge bg-warning me-1">Needs Attention</span>
            <span class="badge bg-danger">Past Due</span>
        </div>
    </div>

    <!-- Timeline View -->
    <div id="timelineView" class="schedule-view active px-3 pb-3">
        <div class="gantt-container">
            <!-- Hour header -->
            <div class="d-flex ms-5 ps-5 mb-2 text-muted small">
                <div style="width:12.5%">8 AM</div>
                <div style="width:12.5%">10 AM</div>
                <div style="width:12.5%">12 PM</div>
                <div style="width:12.5%">2 PM</div>
                <div style="width:12.5%">4 PM</div>
                <div style="width:12.5%">6 PM</div>
            </div>

            <?php if (!empty($techSchedule)): ?>
                <?php foreach ($techSchedule as $idx => $tech): ?>
                <div class="gantt-row">
                    <div class="gantt-label d-flex align-items-center gap-2">
                        <div class="avatar-circle" style="font-size:10px;"><?= esc($tech['initials'] ?: 'UN') ?></div>
                        <span class="small fw-semibold"><?= esc(explode(' ', $tech['name'])[0]) ?> <?= esc(substr(explode(' ', $tech['name'])[1] ?? '', 0, 1)) ?>.</span>
                    </div>
                    <div class="gantt-timeline position-relative" style="height:36px;">
                        <?php
                        $colors = ['bg-primary','bg-success','bg-danger','bg-warning','bg-info'];
                        $wos = array_slice($tech['work_orders'], 0, 4);
                        $totalWOs = count($tech['work_orders']);
                        foreach ($wos as $wi => $wo):
                            $left  = ($wi * 22) + 2;
                            $width = min(28, 96 - $left);
                            $color = $colors[$wi % count($colors)];
                            if (strtolower($wo['priority'] ?? '') === 'critical') $color = 'bg-danger';
                            $label = ($wo['site_name'] ?? $wo['title']) . ($wo['make'] ? ' (' . $wo['make'] . ')' : '');
                        ?>
                        <div class="gantt-bar <?= $color ?>" style="left:<?= $left ?>%;width:<?= $width ?>%;" title="<?= esc($wo['title'] . ' — ' . ($wo['site_name'] ?? '')) ?>">
                            <?= esc(mb_substr($label, 0, 22)) ?>
                        </div>
                        <?php endforeach; ?>
                        <?php if ($totalWOs === 0): ?>
                            <div class="text-muted small d-flex align-items-center h-100 ps-2" style="font-size:11px;">No appointments today</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-calendar-xmark fa-2x mb-2 opacity-25"></i>
                    <p class="mb-0">No technicians or appointments found. <a href="<?= site_url('admin/technicians') ?>">Add a technician</a> to get started.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Calendar View (FullCalendar) -->
    <div id="calendarView" class="schedule-view px-3 pb-3">
        <!-- Legend & Toggle -->
        <div class="d-flex align-items-center gap-3 py-2 mb-1 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <span class="badge px-2 py-1" style="background:#10b981;"><i class="fa-solid fa-clipboard-check me-1"></i>Inspections</span>
                <span class="badge px-2 py-1" style="background:#ef4444;"><i class="fa-solid fa-wrench me-1"></i>Work Orders</span>
            </div>
            <div class="form-check form-switch ms-auto mb-0">
                <input class="form-check-input" type="checkbox" id="toggleCompleted">
                <label class="form-check-label small text-muted" for="toggleCompleted">Show Completed</label>
            </div>
        </div>
        <div id="fcCalendar"></div>
    </div>

</div>

<!-- Upcoming Appointments Table -->
<div class="glass-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">All Scheduled Work Orders</h5>
        <span class="badge" style="background:rgba(34,211,238,.15);color:rgba(34,211,238,.9);"><?= count($upcomingWO ?? []) ?> total</span>
    </div>
    <div class="table-responsive">
        <table id="schedTable" class="table service-table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Site / Customer</th>
                    <th>Equipment</th>
                    <th>Technician</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($upcomingWO)): ?>
                    <?php foreach ($upcomingWO as $wo): ?>
                    <?php
                        $priClass = match(strtolower($wo['priority'] ?? '')) {
                            'critical' => 'bg-danger',
                            'high'     => 'bg-warning',
                            'medium'   => 'bg-info',
                            'low'      => 'bg-success',
                            default    => 'bg-secondary',
                        };
                        $stClass = match(strtolower($wo['status'] ?? '')) {
                            'closed','completed' => 'bg-success',
                            'in_progress'        => 'bg-warning',
                            'open'               => 'bg-primary',
                            default              => 'bg-secondary',
                        };
                        $equip = trim(($wo['make'] ?? '') . ' ' . ($wo['model'] ?? '')) ?: '—';
                    ?>
                    <tr>
                        <td><span class="t-pill">#WO-<?= str_pad($wo['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                        <td class="fw-medium"><?= esc($wo['title']) ?></td>
                        <td>
                            <div><?= esc($wo['site_name'] ?? '—') ?></div>
                            <div class="text-muted small"><?= esc($wo['customer_name'] ?? '') ?></div>
                        </td>
                        <td><?= esc($equip) ?></td>
                        <td><?= esc($wo['tech_name'] ?? '— Unassigned —') ?></td>
                        <td><span class="badge <?= $priClass ?>"><?= esc(ucfirst($wo['priority'] ?? '—')) ?></span></td>
                        <td><span class="badge <?= $stClass ?>"><?= esc(ucwords(str_replace('_', ' ', $wo['status'] ?? '—'))) ?></span></td>
                        <td class="text-muted small"><?= !empty($wo['start_date']) ? esc(date('M j, Y', strtotime($wo['start_date']))) : '—' ?></td>
                        <td class="text-muted small"><?= !empty($wo['end_date'])   ? esc(date('M j, Y', strtotime($wo['end_date'])))   : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No work orders scheduled yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- Calendar Event Detail Modal -->
<div class="modal fade" id="calEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-white" id="calEventTitle">Event Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="calEventBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('admin/scheduling/store') ?>" method="POST" id="appointmentForm">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-calendar-plus me-2"></i>Add Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required placeholder="e.g. MRI Scanner PM Inspection">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Site <span class="text-danger">*</span></label>
                            <select class="form-select" name="site_id" required>
                                <option value="">-- Select Site --</option>
                                <?php foreach ($sites as $site): ?>
                                    <option value="<?= $site['id'] ?>"><?= esc($site['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Equipment</label>
                            <select class="form-select" name="equipment_id">
                                <option value="">-- Optional --</option>
                                <?php foreach ($equipment as $eq): ?>
                                    <option value="<?= $eq['id'] ?>"><?= esc(($eq['make'] ?? '') . ' ' . ($eq['model'] ?? '') . ' (' . ($eq['asset_tag'] ?? '') . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assign Technician</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">-- Unassigned --</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>"><?= esc($tech['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priority</label>
                            <select class="form-select" name="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical / Emergency</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" class="form-control" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" class="form-control" name="end_date">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Describe the appointment..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Save Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ── View switching ──────────────────────────────────────────────────────
function switchView(v) {
    document.getElementById('timelineView').classList.remove('active');
    document.getElementById('calendarView').classList.remove('active');
    document.getElementById('btn' + v.charAt(0).toUpperCase() + v.slice(1)).classList.add('active');
    document.getElementById(v + 'View').classList.add('active');
    if (v === 'calendar' && window._fcCalendar) window._fcCalendar.render();
}

function switchPeriod(p) {
    ['Day','Week','Month'].forEach(function(x) {
        document.getElementById('btn' + x).classList.remove('active');
    });
    document.getElementById('btn' + p.charAt(0).toUpperCase() + p.slice(1)).classList.add('active');
    if (window._fcCalendar) {
        var viewMap = { day: 'timeGridDay', week: 'timeGridWeek', month: 'dayGridMonth' };
        window._fcCalendar.changeView(viewMap[p] || 'dayGridMonth');
    }
}

// ── DataTable for work orders list ─────────────────────────────────────
$(function() {
    $('#schedTable').DataTable({ pageLength: 15, order: [[7, 'asc']] });
});

// ── FullCalendar ────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('fcCalendar');
    if (!calendarEl || typeof FullCalendar === 'undefined') return;

    var showCompleted = false;

    function fetchEvents(fetchInfo, successCallback, failureCallback) {
        fetch('<?= site_url('admin/scheduling/events') ?>?show_completed=' + (showCompleted ? '1' : '0'))
            .then(function(r){ return r.json(); })
            .then(function(data){ successCallback(data); })
            .catch(function(err){ failureCallback(err); });
    }

    window._fcCalendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
        themeSystem: 'bootstrap5',
        events: fetchEvents,
        eventClick: function(info) {
            var p   = info.event.extendedProps;
            var isInsp = (p.event_type === 'inspection');
            var typeBadge = isInsp
                ? '<span class="badge bg-success me-1"><i class="fa-solid fa-clipboard-check me-1"></i>Inspection</span>'
                : '<span class="badge bg-danger me-1"><i class="fa-solid fa-wrench me-1"></i>Work Order</span>';
            var rows = '<tr><th class="text-muted pe-3" style="white-space:nowrap">Type</th><td>' + typeBadge + '</td></tr>'
                + '<tr><th class="text-muted pe-3">Status</th><td>' + (p.status || '—') + '</td></tr>'
                + (!isInsp ? '<tr><th class="text-muted pe-3">Priority</th><td>' + (p.priority || '—') + '</td></tr>' : '')
                + '<tr><th class="text-muted pe-3">Technician</th><td>' + (p.tech || '—') + '</td></tr>'
                + '<tr><th class="text-muted pe-3">Site</th><td>' + (p.site_name || '—') + '</td></tr>';
            document.getElementById('calEventTitle').textContent = info.event.title || 'Event Details';
            document.getElementById('calEventBody').innerHTML = '<table class="table table-sm table-borderless mb-0">' + rows + '</table>';
            new bootstrap.Modal(document.getElementById('calEventModal')).show();
        },
        eventDidMount: function(info) {
            info.el.title = info.event.title;
        },
    });
    window._fcCalendar.render();

    var togEl = document.getElementById('toggleCompleted');
    if (togEl) {
        togEl.addEventListener('change', function() {
            showCompleted = this.checked;
            window._fcCalendar.refetchEvents();
        });
    }
});

// AJAX form submit for appointment
document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
    // allow standard POST — no AJAX needed here
});
</script>
<?= $this->endSection() ?>
