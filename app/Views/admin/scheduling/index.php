<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 topbar">
    <div>
        <h3 class="fw-bold mb-0">Master Schedule</h3>
        <p class="text-muted small mb-0">Manage appointments, routes, and technician availability.</p>
    </div>
    <div class="d-flex gap-3 align-items-center">
        <div class="glass-card px-3 py-2 text-center" style="min-width:110px;">
            <div class="small text-muted fw-bold text-uppercase" style="font-size:10px;">Completed WOs</div>
            <div class="fs-4 fw-bold text-success"><?= esc($completedWO ?? 0) ?></div>
        </div>
        <div class="glass-card px-3 py-2 text-center" style="min-width:110px;">
            <div class="small text-muted fw-bold text-uppercase" style="font-size:10px;">Scheduled Insp.</div>
            <div class="fs-4 fw-bold" style="color:rgba(34,211,238,.9);"><?= count($upcomingInspections ?? []) ?></div>
        </div>
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
        <button id="btnToggleCompleted" class="btn btn-outline-secondary btn-sm" onclick="toggleCompleted()" title="Show/hide completed events">
            <i class="fa-solid fa-eye-slash me-1"></i> Show Completed
        </button>
        <div class="form-check form-switch d-flex align-items-center gap-2 mb-0">
            <input class="form-check-input" type="checkbox" id="toggleCompleted" style="cursor:pointer;">
            <label class="form-check-label small text-muted" for="toggleCompleted">Show Completed</label>
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
        <div class="d-flex gap-2 align-items-center">
            <span class="badge me-1" style="background:#16a34a;"><i class="fa-solid fa-clipboard-check me-1"></i>Inspection</span>
            <span class="badge me-1" style="background:#ef4444;"><i class="fa-solid fa-wrench me-1"></i>Work Order</span>
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
        <div id="fcCalendar"></div>
    </div>

</div>

<!-- Upcoming Appointments Table -->
<div class="glass-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">All Scheduled Events <span class="text-muted small fw-normal">(sorted by date)</span></h5>
        <div class="d-flex gap-2">
            <span class="badge" style="background:rgba(22,163,74,.2);color:#4ade80;"><?= count($upcomingInspections ?? []) ?> inspections</span>
            <span class="badge" style="background:rgba(239,68,68,.2);color:#f87171;"><?= count($upcomingWO ?? []) ?> work orders</span>
        </div>
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
    if ($('#inspTable').length) { $('#inspTable').DataTable({ pageLength: 15, order: [[4, 'asc']] }); }
});

// ── FullCalendar ────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('fcCalendar');
    if (!calendarEl || typeof FullCalendar === 'undefined') return;

    var _showCompleted = false;
    document.getElementById('toggleCompleted')?.addEventListener('change', function() {
        _showCompleted = this.checked;
        if (window._fcCalendar) {
            window._fcCalendar.setOption('events', '<?= site_url("admin/scheduling/events") ?>?show_completed=' + (_showCompleted ? '1' : '0'));
            window._fcCalendar.refetchEvents();
        }
    });

    window._fcCalendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
        themeSystem: 'bootstrap5',
        events: function(info, successCallback, failureCallback) {
            var url = '<?= site_url('admin/scheduling/events') ?>';
            if (window._showCompleted) url += '?show_completed=1';
            fetch(url).then(r => r.json()).then(successCallback).catch(failureCallback);
        },
        eventClick: function(info) {
            var p = info.event.extendedProps;
            var isInsp = (p.type === 'inspection');
            document.getElementById('calEventTitle').textContent    = info.event.title;
            document.getElementById('calEventType').textContent     = isInsp ? 'Inspection' : 'Work Order';
            document.getElementById('calEventType').className       = 'badge fw-bold ' + (isInsp ? 'bg-success' : 'bg-danger');
            document.getElementById('calEventStatus').textContent   = p.status || '—';
            document.getElementById('calEventPriority').textContent = p.priority || (isInsp ? 'N/A' : '—');
            document.getElementById('calEventTech').textContent     = p.tech || 'Unassigned';
            document.getElementById('calEventSite').textContent     = p.site || '—';
            document.getElementById('calEventDate').textContent     = (info.event.startStr || '').substring(0,10) || '—';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('calEventModal')).show();
        },
        eventColor: '#7C3AED',
    });
    window._fcCalendar.render();
});

// AJAX form submit for appointment
document.getElementById('appointmentForm')?.addEventListener('submit', function(e) {
    // allow standard POST — no AJAX needed here
});
</script>

<!-- Calendar Event Detail Modal -->
<div class="modal fade" id="calEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,rgba(124,58,237,.9),rgba(34,211,238,.8));border-radius:12px 12px 0 0;border:none;">
                <div>
                    <span id="calEventType" class="badge fw-bold me-2">Event</span>
                    <span id="calEventTitle" class="text-white fw-semibold" style="font-size:14px;"></span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#0E1630;color:#E9EDFF;">
                <table class="table table-sm mb-0" style="color:#E9EDFF;">
                    <tr>
                        <th style="width:35%;color:rgba(233,237,255,.5);font-size:12px;text-transform:uppercase;border:none;padding:8px 0;">Date</th>
                        <td id="calEventDate" class="fw-medium" style="border:none;padding:8px 0;"></td>
                    </tr>
                    <tr>
                        <th style="color:rgba(233,237,255,.5);font-size:12px;text-transform:uppercase;border:none;padding:8px 0;">Site</th>
                        <td id="calEventSite" class="fw-medium" style="border:none;padding:8px 0;"></td>
                    </tr>
                    <tr>
                        <th style="color:rgba(233,237,255,.5);font-size:12px;text-transform:uppercase;border:none;padding:8px 0;">Status</th>
                        <td id="calEventStatus" style="border:none;padding:8px 0;"></td>
                    </tr>
                    <tr>
                        <th style="color:rgba(233,237,255,.5);font-size:12px;text-transform:uppercase;border:none;padding:8px 0;">Priority</th>
                        <td id="calEventPriority" style="border:none;padding:8px 0;"></td>
                    </tr>
                    <tr>
                        <th style="color:rgba(233,237,255,.5);font-size:12px;text-transform:uppercase;border:none;padding:8px 0;">Technician</th>
                        <td id="calEventTech" style="border:none;padding:8px 0;"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer" style="background:rgba(7,10,18,.4);border-top:1px solid rgba(255,255,255,.08);border-radius:0 0 12px 12px;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Inspections Table -->
<div class="glass-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            Scheduled Inspections
            <span class="badge ms-2" style="background:rgba(22,163,74,.2);color:#4ade80;font-size:12px;">
                <?= count($upcomingInspections ?? []) ?>
            </span>
        </h5>
    </div>
    <div class="table-responsive">
        <table id="inspTable" class="table service-table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>Equipment</th>
                    <th>Site / Customer</th>
                    <th>Technician</th>
                    <th>Status</th>
                    <th>Scheduled</th>
                    <th>Completed</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($upcomingInspections ?? [])): ?>
                    <?php foreach ($upcomingInspections as $insp): ?>
                    <?php
                        $st = strtolower($insp['status'] ?? '');
                        $stBadge = $st === 'pass' ? 'bg-success' : ($st === 'fail' ? 'bg-danger' : ($st === 'repair' ? 'bg-warning' : 'bg-secondary'));
                        $equip = trim(($insp['make'] ?? '') . ' ' . ($insp['model'] ?? '')) ?: 'Unknown';
                        if (!empty($insp['asset_tag'])) $equip .= ' (' . $insp['asset_tag'] . ')';
                    ?>
                    <tr>
                        <td class="fw-medium"><?= esc($equip) ?></td>
                        <td>
                            <div><?= esc($insp['site_name'] ?? '--') ?></div>
                            <div class="text-muted small"><?= esc($insp['customer_name'] ?? '') ?></div>
                        </td>
                        <td><?= esc($insp['tech_name'] ?? '-- Unassigned --') ?></td>
                        <td>
                            <?php if ($insp['status']): ?>
                                <span class="badge <?= $stBadge ?>"><?= esc(ucfirst($insp['status'])) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Scheduled</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?= !empty($insp['start_date']) ? esc(date('M j, Y', strtotime($insp['start_date']))) : '--' ?>
                        </td>
                        <td class="text-muted small">
                            <?= !empty($insp['end_date']) ? esc(date('M j, Y', strtotime($insp['end_date']))) : '<span class="text-warning small">Pending</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No inspections found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>



<script>
window._showCompleted = false;
function toggleCompleted() {
    window._showCompleted = !window._showCompleted;
    var btn = document.getElementById('btnToggleCompleted');
    if (window._showCompleted) {
        btn.innerHTML = '<i class="fa-solid fa-eye me-1"></i> Hide Completed';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-outline-warning');
    } else {
        btn.innerHTML = '<i class="fa-solid fa-eye-slash me-1"></i> Show Completed';
        btn.classList.remove('btn-outline-warning');
        btn.classList.add('btn-outline-secondary');
    }
    if (window._fcCalendar) window._fcCalendar.refetchEvents();
}
</script>
<?= $this->endSection() ?>
