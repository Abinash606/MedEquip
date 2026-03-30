<?= $this->extend('layouts/customer-header') ?>
<?= $this->section('content') ?>
<style>
.modal-content{background:#0E1630!important;border:1px solid rgba(255,255,255,.12)!important;color:#E9EDFF!important;border-radius:16px!important;}
.modal-header{background:linear-gradient(135deg,rgba(124,58,237,.9),rgba(34,211,238,.8))!important;border-bottom:none!important;border-radius:16px 16px 0 0!important;}
.modal-footer{border-top:1px solid rgba(255,255,255,.08)!important;background:rgba(7,10,18,.4)!important;border-radius:0 0 16px 16px!important;}
.modal-body{background:rgba(14,22,48,.6)!important;}
table.dataTable thead th{background:rgba(7,10,18,.6)!important;color:rgba(233,237,255,.55)!important;border-bottom:1px solid rgba(255,255,255,.08)!important;font-size:11px;letter-spacing:.12em;text-transform:uppercase;}
table.dataTable tbody tr{background:transparent!important;}
table.dataTable tbody tr:hover{background:rgba(255,255,255,.04)!important;}
table.dataTable tbody td{border-bottom:1px solid rgba(255,255,255,.05)!important;color:#E9EDFF!important;}
.dataTables_wrapper .dataTables_filter input,.dataTables_wrapper .dataTables_length select{background:rgba(255,255,255,.06)!important;border:1px solid rgba(255,255,255,.12)!important;color:#E9EDFF!important;border-radius:8px!important;padding:4px 8px;}
.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{color:rgba(233,237,255,.6)!important;}
.dataTables_wrapper .dataTables_paginate .paginate_button{color:#E9EDFF!important;}
.dataTables_wrapper .dataTables_paginate .paginate_button.current{background:linear-gradient(135deg,rgba(124,58,237,.8),rgba(34,211,238,.6))!important;border-color:transparent!important;color:#fff!important;}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Inspections</h3>
        <p class="text-muted small mb-0">Track equipment inspections across your sites.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <?php if (!empty($sites)): ?>
        <select id="custSiteFilter" class="form-select form-select-sm" style="min-width:180px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:#E9EDFF;border-radius:10px;">
            <option value="">All Sites</option>
            <?php foreach ($sites as $s): ?>
                <option value="<?= esc($s['id']) ?>"><?= esc($s['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" id="btnCustInspOpen"   class="btn btn-primary active"    onclick="custInspFilter('open')">Open</button>
            <button type="button" id="btnCustInspAll"    class="btn btn-outline-secondary" onclick="custInspFilter('all')">All</button>
            <button type="button" id="btnCustInspClosed" class="btn btn-outline-secondary" onclick="custInspFilter('closed')">Closed</button>
        </div>
    </div>
</div>

<div class="content">
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="glass-card p-3 text-center"><div class="text-muted small text-uppercase fw-bold mb-1">Open</div><h3 class="fw-bold mb-0"><?= count($upcomingInspections) ?></h3></div></div>
    <div class="col-6 col-md-3"><div class="glass-card p-3 text-center"><div class="text-muted small text-uppercase fw-bold mb-1">Completed</div><h3 class="fw-bold mb-0"><?= count($inspectionHistory) ?></h3></div></div>
    <div class="col-6 col-md-3"><div class="glass-card p-3 text-center"><div class="text-muted small text-uppercase fw-bold mb-1">Sites</div><h3 class="fw-bold mb-0"><?= count($sites) ?></h3></div></div>
    <div class="col-6 col-md-3"><div class="glass-card p-3 text-center"><div class="text-muted small text-uppercase fw-bold mb-1">Total</div><h3 class="fw-bold mb-0"><?= count($upcomingInspections) + count($inspectionHistory) ?></h3></div></div>
</div>

<div class="glass-card p-3">
    <div class="table-responsive">
        <table id="custInspTable" class="table table-hover service-table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>Inspection ID</th><th>Type</th><th>Equipment</th><th>Site</th>
                    <th>Status</th><th>Scheduled</th><th>Completed</th><th>Next Due</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcomingInspections as $insp):
                    $inspId = 'INSP-'.(isset($insp['scheduled_at']) ? date('Ymd',strtotime($insp['scheduled_at'])) : date('Ymd')).
                              '-'.strtoupper(substr(md5($insp['group_id'] ?? $insp['id']),0,8));
                    $equip  = trim(($insp['make'] ?? '').' '.($insp['model'] ?? '')) ?: ($insp['device_type'] ?? '—');
                ?>
                <tr data-status="open" data-site-id="<?= esc($insp['site_id'] ?? '') ?>">
                    <td><span class="t-pill"><?= esc($inspId) ?></span></td>
                    <td><?= esc($insp['inspection_type'] ?? 'Equipment Inspection') ?></td>
                    <td><?= esc($equip) ?></td>
                    <td><?= esc($insp['site_name'] ?? '—') ?></td>
                    <td><span class="badge bg-warning text-dark">In Progress</span></td>
                    <td class="text-muted small"><?= !empty($insp['scheduled_at']) ? date('M j, Y',strtotime($insp['scheduled_at'])) : '—' ?></td>
                    <td class="text-muted small">—</td>
                    <td class="text-muted small"><?= !empty($insp['next_due_date']) ? date('M j, Y',strtotime($insp['next_due_date'])) : '—' ?></td>
                </tr>
                <?php endforeach; ?>
                <?php foreach ($inspectionHistory as $insp):
                    $inspId = 'INSP-'.(isset($insp['scheduled_at']) ? date('Ymd',strtotime($insp['scheduled_at'])) : date('Ymd')).
                              '-'.strtoupper(substr(md5($insp['group_id'] ?? $insp['id']),0,8));
                    $equip  = trim(($insp['make'] ?? '').' '.($insp['model'] ?? '')) ?: ($insp['device_type'] ?? '—');
                    $stLow  = strtolower($insp['status'] ?? '');
                    $stCls  = ($stLow==='pass') ? 'bg-success' : (($stLow==='fail') ? 'bg-danger' : 'bg-warning text-dark');
                ?>
                <tr data-status="closed" data-site-id="<?= esc($insp['site_id'] ?? '') ?>">
                    <td><span class="t-pill"><?= esc($inspId) ?></span></td>
                    <td><?= esc($insp['inspection_type'] ?? 'Equipment Inspection') ?></td>
                    <td><?= esc($equip) ?></td>
                    <td><?= esc($insp['site_name'] ?? '—') ?></td>
                    <td><span class="badge <?= $stCls ?>"><?= esc(ucfirst($insp['status'] ?? '—')) ?></span></td>
                    <td class="text-muted small"><?= !empty($insp['scheduled_at']) ? date('M j, Y',strtotime($insp['scheduled_at'])) : '—' ?></td>
                    <td class="text-muted small"><?= !empty($insp['completed_at']) ? date('M j, Y',strtotime($insp['completed_at'])) : '—' ?></td>
                    <td class="text-muted small"><?= !empty($insp['next_due_date']) ? date('M j, Y',strtotime($insp['next_due_date'])) : '—' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
var _cit=null,_cim='open',_cis='';
$(function(){
    _cit=$('#custInspTable').DataTable({pageLength:25,order:[[5,'desc']]});
    custInspFilter('open');
    $('#custSiteFilter').on('change',function(){_cis=this.value;_aif();});
});
function custInspFilter(m){
    _cim=m;
    ['btnCustInspOpen','btnCustInspAll','btnCustInspClosed'].forEach(function(id){document.getElementById(id).className='btn btn-outline-secondary';});
    var map={open:'btnCustInspOpen',all:'btnCustInspAll',closed:'btnCustInspClosed'};
    if(map[m])document.getElementById(map[m]).className='btn btn-primary active';
    _aif();
}
function _aif(){
    $.fn.dataTable.ext.search=[];
    $.fn.dataTable.ext.search.push(function(s,d,i){
        var r=_cit.row(i).node(),st=$(r).data('status')||'',si=String($(r).data('site-id')||'');
        if(_cim==='open'&&st!=='open')return false;
        if(_cim==='closed'&&st!=='closed')return false;
        if(_cis&&si!==String(_cis))return false;
        return true;
    });
    _cit.draw();
}
</script>
<?= $this->endSection() ?>
