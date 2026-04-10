<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<?php
// Pre-process data for view
$woCount   = count($workOrders ?? []);
$inspCount = count($inspections ?? []);
?>

<!-- FullCalendar v5 — loaded inline so it works regardless of which layout scripts are included -->
<link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>

<style>
    /* Gantt / Schedule */
    .schedule-view          { display:none; }
    .schedule-view.active   { display:block; }
    .gantt-row              { display:flex;align-items:center;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.06); }
    .gantt-label            { width:150px;font-size:.88rem;font-weight:500;flex-shrink:0; }
    .gantt-timeline         { flex-grow:1;height:36px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.06);border-radius:6px;position:relative; }
    .gantt-bar              { position:absolute;height:100%;border-radius:6px;font-size:.74rem;color:#fff;display:flex;align-items:center;padding-left:8px;white-space:nowrap;overflow:hidden;cursor:pointer; }
    .gantt-bar.bg-primary   { background:linear-gradient(90deg,rgba(34,211,238,.92),rgba(124,58,237,.70))!important; }
    .gantt-bar.bg-danger    { background:linear-gradient(90deg,rgba(239,68,68,.95),rgba(249,115,22,.72))!important; }
    .gantt-bar.bg-warning   { background:linear-gradient(90deg,rgba(245,158,11,.95),rgba(249,115,22,.80))!important; }
    .gantt-bar.bg-success   { background:linear-gradient(90deg,rgba(34,197,94,.95),rgba(34,211,238,.65))!important; }
    .gantt-bar.bg-info      { background:linear-gradient(90deg,rgba(6,182,212,.95),rgba(59,130,246,.72))!important; }
    /* FullCalendar dark-theme overrides */
    .fc { color:#e2e8f0; }
    .fc .fc-toolbar-title   { color:#e2e8f0; font-size:1.1rem; }
    .fc .fc-button           { background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.15);color:#e2e8f0; }
    .fc .fc-button:hover     { background:rgba(124,58,237,.35); }
    .fc .fc-button-active,
    .fc .fc-button-primary:not(:disabled).fc-button-active { background:rgba(124,58,237,.6);border-color:rgba(124,58,237,.5); }
    .fc .fc-col-header-cell  { background:rgba(255,255,255,.04);color:#94a3b8; }
    .fc .fc-daygrid-day      { background:rgba(255,255,255,.02); }
    .fc .fc-daygrid-day:hover{ background:rgba(255,255,255,.05); }
    .fc .fc-daygrid-day-number { color:#94a3b8; }
    .fc .fc-scrollgrid       { border-color:rgba(255,255,255,.08); }
    .fc td, .fc th           { border-color:rgba(255,255,255,.06)!important; }
    .fc .fc-today-button     { background:rgba(124,58,237,.4);border-color:rgba(124,58,237,.3); }
    .fc .fc-daygrid-day.fc-day-today { background:rgba(124,58,237,.08); }
    .fc .fc-list-event       { background:transparent; }
    .fc .fc-list-day-cushion { background:rgba(255,255,255,.04); }
    .fc .fc-list-table td    { border-color:rgba(255,255,255,.06); }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 topbar">
    <div>
        <h3 class="fw-bold mb-0">My Schedule</h3>
        <p class="text-muted small mb-0">Your assigned work orders and inspections.</p>
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
    </div>
</div>

<div class="content">
<div class="glass-card">

    <!-- Date navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom p-3 flex-wrap gap-2"
         style="border-color:rgba(255,255,255,.08)!important;">
        <div class="d-flex gap-2 align-items-center">
            <button class="btn btn-light btn-sm" onclick="shiftDate(-1)"><i class="fa-solid fa-chevron-left"></i></button>
            <span class="fw-bold mx-2" id="currentDateLabel">Today, <?= date('M j, Y') ?></span>
            <button class="btn btn-light btn-sm" onclick="shiftDate(1)"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success me-1">Ready</span>
            <span class="badge bg-warning me-1">Needs Attention</span>
            <span class="badge bg-danger">Past Due</span>
        </div>
    </div>

    <!-- ── TIMELINE VIEW ── -->
    <div id="timelineView" class="schedule-view active px-3 pb-3">
        <div class="gantt-container">
            <div class="d-flex ms-5 ps-5 mb-2 text-muted small">
                <div style="width:12.5%">8 AM</div><div style="width:12.5%">10 AM</div>
                <div style="width:12.5%">12 PM</div><div style="width:12.5%">2 PM</div>
                <div style="width:12.5%">4 PM</div><div style="width:12.5%">6 PM</div>
            </div>
            <?php
            $colors = ['bg-primary','bg-success','bg-danger','bg-warning','bg-info'];
            $me = session('username') ?? 'Tech';
            $init = strtoupper(substr($me,0,1).substr(strrchr($me,' ')??' ',1,1));
            if (!empty($workOrders)):
                foreach (array_slice($workOrders,0,8) as $wi => $wo):
                    $left  = ($wi*20)+2; $width = min(30,96-$left);
                    $color = $colors[$wi % count($colors)];
                    if (strtolower($wo['priority']??'') === 'critical') $color='bg-danger';
                    $label = $wo['site_name'] ?? $wo['title'];
                    $safeTitle  = htmlspecialchars(addslashes($wo['title']),ENT_QUOTES);
                    $safeStatus = htmlspecialchars(addslashes($wo['status']??''),ENT_QUOTES);
                    $safePri    = htmlspecialchars(addslashes($wo['priority']??''),ENT_QUOTES);
                    $safeSite   = htmlspecialchars(addslashes($wo['site_name']??''),ENT_QUOTES);
                    $safeCust   = htmlspecialchars(addslashes($wo['customer_name']??''),ENT_QUOTES);
                    $equip      = trim(($wo['make']??'').' '.($wo['model']??'')) ?: '—';
                    $safeEquip  = htmlspecialchars(addslashes($equip),ENT_QUOTES);
            ?>
            <div class="gantt-row">
                <div class="gantt-label d-flex align-items-center gap-2">
                    <div class="avatar-circle" style="font-size:10px;flex-shrink:0;"><?= esc($init) ?></div>
                    <span class="small fw-semibold text-truncate" style="max-width:100px;"><?= esc(mb_substr($wo['title'],0,14)) ?></span>
                </div>
                <div class="gantt-timeline">
                    <div class="gantt-bar <?= $color ?>" style="left:<?= $left ?>%;width:<?= $width ?>%;"
                         onclick="showSchedModal('wo','#WO-<?= str_pad($wo['id'],4,'0',STR_PAD_LEFT) ?>','<?= $safeTitle ?>','<?= $safeStatus ?>','<?= $safePri ?>','<?= esc($init) ?>','<?= $safeSite ?>','<?= $safeCust ?>','<?= $safeEquip ?>','<?= esc($wo['start_date']??'') ?>','<?= esc($wo['end_date']??'') ?>')"
                         title="<?= esc($wo['title']) ?> — <?= esc($wo['site_name']??'') ?>">
                        <?= esc(mb_substr($label,0,24)) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="text-center text-muted py-4">
                <i class="fa-solid fa-calendar-xmark fa-2x mb-2 opacity-25 d-block"></i>
                No work orders assigned to you.
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── CALENDAR VIEW ── -->
    <div id="calendarView" class="schedule-view px-3 pb-3">
        <div class="d-flex align-items-center gap-3 py-2 mb-2 flex-wrap">
            <span class="badge px-2 py-1" style="background:#10b981;"><i class="fa-solid fa-clipboard-check me-1"></i>Inspections</span>
            <span class="badge px-2 py-1" style="background:#ef4444;"><i class="fa-solid fa-wrench me-1"></i>Work Orders</span>
        </div>
        <div id="fcCalendar" style="min-height:400px;"></div>
    </div>

</div><!-- /.glass-card -->

<!-- ── TABLES ── -->
<div class="glass-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <ul class="nav nav-tabs border-0 gap-1" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold px-3 py-1" data-bs-toggle="tab" data-bs-target="#tabWO" type="button">
                    <i class="fa-solid fa-wrench me-1 text-danger"></i> Work Orders
                    <span class="badge bg-secondary ms-1"><?= $woCount ?></span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold px-3 py-1" data-bs-toggle="tab" data-bs-target="#tabInsp" type="button">
                    <i class="fa-solid fa-clipboard-check me-1 text-success"></i> Inspections
                    <span class="badge bg-secondary ms-1"><?= $inspCount ?></span>
                </button>
            </li>
        </ul>
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="schedShowCompleted">
            <label class="form-check-label small text-muted" for="schedShowCompleted">Show Completed</label>
        </div>
    </div>

    <div class="tab-content">
        <!-- Work Orders -->
        <div class="tab-pane fade show active" id="tabWO">
            <div class="table-responsive">
                <table id="schedTableWO" class="table service-table align-middle" style="width:100%">
                    <thead><tr>
                        <th>#</th><th>Title</th><th>Site / Customer</th>
                        <th>Equipment</th><th>Priority</th><th>Status</th>
                        <th>Start Date</th><th>End Date</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($workOrders as $wo):
                        $priCls = match(strtolower($wo['priority']??'')) {
                            'critical'=>'bg-danger','high'=>'bg-warning',
                            'medium'=>'bg-info','low'=>'bg-success',default=>'bg-secondary'
                        };
                        $stCls = match(strtolower($wo['status']??'')) {
                            'closed','completed'=>'bg-success','in_progress'=>'bg-warning',
                            'open'=>'bg-primary',default=>'bg-secondary'
                        };
                        $equip = trim(($wo['make']??'').' '.($wo['model']??'')) ?: '—';
                        $safeT = htmlspecialchars(addslashes($wo['title']),ENT_QUOTES);
                        $safeS = htmlspecialchars(addslashes($wo['status']??''),ENT_QUOTES);
                        $safeP = htmlspecialchars(addslashes($wo['priority']??''),ENT_QUOTES);
                        $safeSi= htmlspecialchars(addslashes($wo['site_name']??''),ENT_QUOTES);
                        $safeCu= htmlspecialchars(addslashes($wo['customer_name']??''),ENT_QUOTES);
                        $safeE = htmlspecialchars(addslashes($equip),ENT_QUOTES);
                    ?>
                    <tr class="sched-row" data-status="<?= esc(strtolower($wo['status']??'')) ?>" style="cursor:pointer;"
                        onclick="showSchedModal('wo','#WO-<?= str_pad($wo['id'],4,'0',STR_PAD_LEFT) ?>','<?= $safeT ?>','<?= $safeS ?>','<?= $safeP ?>','','<?= $safeSi ?>','<?= $safeCu ?>','<?= $safeE ?>','<?= esc($wo['start_date']??'') ?>','<?= esc($wo['end_date']??'') ?>')">
                        <td><span class="t-pill">#WO-<?= str_pad($wo['id'],4,'0',STR_PAD_LEFT) ?></span></td>
                        <td class="fw-medium"><?= esc($wo['title']) ?></td>
                        <td><div><?= esc($wo['site_name']??'—') ?></div><small class="text-muted"><?= esc($wo['customer_name']??'') ?></small></td>
                        <td><?= esc($equip) ?></td>
                        <td><span class="badge <?= $priCls ?>"><?= esc(ucfirst($wo['priority']??'—')) ?></span></td>
                        <td><span class="badge <?= $stCls ?>"><?= esc(ucwords(str_replace('_',' ',$wo['status']??'—'))) ?></span></td>
                        <td class="text-muted small"><?= !empty($wo['start_date'])?date('M j, Y',strtotime($wo['start_date'])):'—' ?></td>
                        <td class="text-muted small"><?= !empty($wo['end_date'])?date('M j, Y',strtotime($wo['end_date'])):'—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($workOrders)): ?><tr><td colspan="8" class="text-center text-muted py-3">No work orders assigned to you.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Inspections -->
        <div class="tab-pane fade" id="tabInsp">
            <div class="table-responsive">
                <table id="schedTableInsp" class="table service-table align-middle" style="width:100%">
                    <thead><tr>
                        <th>Inspection ID</th><th>Type</th><th>Site</th>
                        <th>Customer</th><th>Status</th><th>Scheduled</th><th>Next Due</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($inspections as $insp):
                        $d  = $insp['scheduled_at'] ?? date('Y-m-d');
                        $id = $insp['group_id'] ?? '—'; // Use real group_id from DB
                        $sl = strtolower($insp['status']??'');
                        $sc = $sl==='pass'?'bg-success':($sl==='fail'?'bg-danger':'bg-warning text-dark');
                        $isDone = in_array($sl,['pass','fail','repair','completed']);
                        $safeId = htmlspecialchars(addslashes($id),ENT_QUOTES);
                        $safeTy = htmlspecialchars(addslashes($insp['inspection_type']??'Inspection'),ENT_QUOTES);
                        $safeSt = htmlspecialchars(addslashes($insp['status']??''),ENT_QUOTES);
                        $safeSi = htmlspecialchars(addslashes($insp['site_name']??''),ENT_QUOTES);
                        $safeCu = htmlspecialchars(addslashes($insp['customer_name']??''),ENT_QUOTES);
                    ?>
                    <tr class="sched-row" data-status="<?= $isDone?'closed':'open' ?>" style="cursor:pointer;"
                        onclick="showSchedModal('insp','<?= $safeId ?>','<?= $safeTy ?>','<?= $safeSt ?>','','','<?= $safeSi ?>','<?= $safeCu ?>','','<?= esc($insp['scheduled_at']??'') ?>','<?= esc($insp['next_due_date']??'') ?>')">
                        <td><span class="t-pill"><?= esc($id) ?></span></td>
                        <td><?= esc($insp['inspection_type']??'Equipment Inspection') ?></td>
                        <td><?= esc($insp['site_name']??'—') ?></td>
                        <td><?= esc($insp['customer_name']??'—') ?></td>
                        <td><span class="badge <?= $sc ?>"><?= esc(ucfirst($insp['status']??'In Progress')) ?></span></td>
                        <td class="text-muted small"><?= !empty($insp['scheduled_at'])?date('M j, Y',strtotime($insp['scheduled_at'])):'—' ?></td>
                        <td class="text-muted small"><?= !empty($insp['next_due_date'])?date('M j, Y',strtotime($insp['next_due_date'])):'—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($inspections)): ?><tr><td colspan="7" class="text-center text-muted py-3">No inspections assigned to you.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div><!-- /.content -->

<!-- ── Schedule Item Detail Modal (dark theme) ── -->
<div class="modal fade" id="schedItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#0E1630;border:1px solid rgba(255,255,255,.15);border-radius:14px;">
            <div class="modal-header" style="background:linear-gradient(135deg,rgba(124,58,237,.9),rgba(34,211,238,.8));border-bottom:none;border-radius:14px 14px 0 0;">
                <h5 class="modal-title fw-bold text-white" id="schedItemModalTitle">Item Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="schedItemModalBody"></div>
            <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,.08);border-radius:0 0 14px 14px;">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
var _schedDate = new Date();
var _currentPeriod = 'day';

function _fmtDate(d) {
    var today = new Date(); today.setHours(0,0,0,0);
    var cmp = new Date(d); cmp.setHours(0,0,0,0);
    var prefix = cmp.getTime()===today.getTime() ? 'Today, ' : '';
    return prefix + d.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
}

function shiftDate(delta) {
    var steps = {day:1,week:7,month:30};
    _schedDate.setDate(_schedDate.getDate() + delta*(steps[_currentPeriod]||1));
    document.getElementById('currentDateLabel').textContent = _fmtDate(_schedDate);
    if (window._fcCalendar) { if(delta>0) window._fcCalendar.next(); else window._fcCalendar.prev(); }
}

function switchView(v) {
    ['timeline','calendar'].forEach(function(x){
        document.getElementById(x+'View').classList.toggle('active', x===v);
    });
    document.getElementById('btnTimeline').classList.toggle('active', v==='timeline');
    document.getElementById('btnCalendar').classList.toggle('active', v==='calendar');
    if (v==='calendar' && window._fcCalendar) {
        setTimeout(function(){ window._fcCalendar.render(); window._fcCalendar.updateSize(); }, 50);
    }
}

function switchPeriod(p) {
    _currentPeriod = p;
    ['Day','Week','Month'].forEach(function(x){ document.getElementById('btn'+x).classList.remove('active'); });
    document.getElementById('btn'+p.charAt(0).toUpperCase()+p.slice(1)).classList.add('active');
    if (window._fcCalendar) {
        var m={day:'timeGridDay',week:'timeGridWeek',month:'dayGridMonth'};
        window._fcCalendar.changeView(m[p]||'dayGridMonth');
    }
}

function showSchedModal(type,id,title,status,priority,tech,site,customer,equipment,date1,date2) {
    var isWO = type==='wo';
    var badge = isWO
        ? '<span class="badge me-1" style="background:#ef4444"><i class="fa-solid fa-wrench me-1"></i>Work Order</span>'
        : '<span class="badge me-1" style="background:#10b981"><i class="fa-solid fa-clipboard-check me-1"></i>Inspection</span>';
    var stLow = (status||'').toLowerCase();
    var stStyle = stLow==='open'||stLow==='in_progress' ? 'background:#3b82f6'
        : stLow==='pass' ? 'background:#10b981'
        : stLow==='fail' ? 'background:#ef4444' : 'background:#6b7280';
    var rows = ''
        +'<tr><th style="color:#94a3b8;white-space:nowrap;padding-right:16px;">Type</th><td>'+badge+'</td></tr>'
        +'<tr><th style="color:#94a3b8;padding-right:16px;">'+(isWO?'WO #':'ID')+'</th><td class="fw-semibold">'+id+'</td></tr>'
        +'<tr><th style="color:#94a3b8;padding-right:16px;">Title</th><td>'+title+'</td></tr>'
        +'<tr><th style="color:#94a3b8;padding-right:16px;">Status</th><td><span class="badge" style="'+stStyle+'">'+status+'</span></td></tr>'
        +(isWO&&priority?'<tr><th style="color:#94a3b8;padding-right:16px;">Priority</th><td>'+priority+'</td></tr>':'')
        +'<tr><th style="color:#94a3b8;padding-right:16px;">Site</th><td>'+(site||'—')+'</td></tr>'
        +'<tr><th style="color:#94a3b8;padding-right:16px;">Customer</th><td>'+(customer||'—')+'</td></tr>'
        +(isWO&&equipment&&equipment!=='—'?'<tr><th style="color:#94a3b8;padding-right:16px;">Equipment</th><td>'+equipment+'</td></tr>':'')
        +(date1?'<tr><th style="color:#94a3b8;padding-right:16px;">'+(isWO?'Start':'Scheduled')+'</th><td>'+date1.substring(0,10)+'</td></tr>':'')
        +(date2?'<tr><th style="color:#94a3b8;padding-right:16px;">'+(isWO?'End':'Next Due')+'</th><td>'+date2.substring(0,10)+'</td></tr>':'');
    document.getElementById('schedItemModalTitle').textContent = title||'Item Details';
    document.getElementById('schedItemModalBody').innerHTML =
        '<table class="table table-sm table-borderless mb-0" style="color:#e2e8f0;">'+rows+'</table>';
    new bootstrap.Modal(document.getElementById('schedItemModal')).show();
}

var _dtWO=null, _dtInsp=null, _schedShowCompleted=false;
$(function(){
    _dtWO   = $('#schedTableWO').DataTable({pageLength:15,order:[[6,'asc']]});
    _dtInsp = $('#schedTableInsp').DataTable({pageLength:15,order:[[5,'asc']]});
    applySchedFilter();
    $('#schedShowCompleted').on('change',function(){ _schedShowCompleted=this.checked; applySchedFilter(); });
});

function applySchedFilter(){
    $.fn.dataTable.ext.search=[];
    if(!_schedShowCompleted){
        $.fn.dataTable.ext.search.push(function(settings,data,idx){
            var tid=settings.nTable.id;
            if(tid!=='schedTableWO'&&tid!=='schedTableInsp') return true;
            var row=(tid==='schedTableWO'?_dtWO:_dtInsp).row(idx).node();
            var st=$(row).data('status')||'';
            return st!=='closed'&&st!=='completed';
        });
    }
    if(_dtWO) _dtWO.draw();
    if(_dtInsp) _dtInsp.draw();
}

// Init FullCalendar after everything loaded
window.addEventListener('load', function() {
    var calendarEl = document.getElementById('fcCalendar');
    if (!calendarEl) return;
    if (typeof FullCalendar === 'undefined') {
        calendarEl.innerHTML = '<div class="alert alert-warning m-3">FullCalendar failed to load. Please check your internet connection.</div>';
        return;
    }
    window._fcCalendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,listWeek' },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('<?= site_url('technician/scheduling/events') ?>')
                .then(function(r){ return r.json(); })
                .then(function(data){ successCallback(data); })
                .catch(function(err){ failureCallback(err); });
        },
        datesSet: function(info) {
            _schedDate = new Date(info.view.currentStart);
            document.getElementById('currentDateLabel').textContent = _fmtDate(_schedDate);
        },
        eventClick: function(info) {
            var p = info.event.extendedProps;
            showSchedModal(
                p.event_type==='inspection'?'insp':'wo',
                info.event.id||'—',
                info.event.title||'—',
                p.status||'—',
                p.priority||'',
                '',
                p.site_name||'—',
                '', '', '', ''
            );
        },
        eventDidMount: function(info) { info.el.title = info.event.title; }
    });
    window._fcCalendar.render();
});
</script>

<?= $this->endSection() ?>
