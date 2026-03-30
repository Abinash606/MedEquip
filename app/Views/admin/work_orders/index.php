<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Work Orders</h3>
        <p class="text-muted small mb-0">All work orders across all sites.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" id="btnOpen" class="btn btn-primary active" onclick="filterStatus('open')">Open</button>
            <button type="button" id="btnAll"  class="btn btn-outline-secondary" onclick="filterStatus('all')">All</button>
            <button type="button" id="btnClosed" class="btn btn-outline-secondary" onclick="filterStatus('closed')">Closed</button>
        </div>
    </div>
</div>

<div class="content">
<div class="glass-card p-3">
    <div class="table-responsive">
        <table id="workOrdersTable" class="table table-hover service-table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Site / Customer</th>
                    <th>Technician</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($workOrders as $wo):
                    $priClass = match(strtolower($wo['priority'] ?? '')) {
                        'critical' => 'bg-danger', 'high' => 'bg-warning',
                        'medium'   => 'bg-info',   'low'  => 'bg-success',
                        default    => 'bg-secondary',
                    };
                    $stClass = match(strtolower($wo['status'] ?? '')) {
                        'closed','completed' => 'bg-success',
                        'in_progress'        => 'bg-warning',
                        'open'               => 'bg-primary',
                        default              => 'bg-secondary',
                    };
                ?>
                <tr data-status="<?= esc(strtolower($wo['status'] ?? 'open')) ?>">
                    <td><span class="t-pill">#WO-<?= str_pad($wo['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                    <td class="fw-medium"><?= esc($wo['title']) ?></td>
                    <td>
                        <div><?= esc($wo['site_name'] ?? '—') ?></div>
                        <div class="text-muted small"><?= esc($wo['customer_name'] ?? '') ?></div>
                    </td>
                    <td><?= esc($wo['tech_name'] ?? '— Unassigned —') ?></td>
                    <td><span class="badge <?= $priClass ?>"><?= esc(ucfirst($wo['priority'] ?? '—')) ?></span></td>
                    <td><span class="badge <?= $stClass ?>"><?= esc(ucwords(str_replace('_',' ',$wo['status'] ?? '—'))) ?></span></td>
                    <td class="text-muted small"><?= !empty($wo['start_date']) ? date('M j, Y', strtotime($wo['start_date'])) : '—' ?></td>
                    <td class="text-muted small"><?= !empty($wo['end_date'])   ? date('M j, Y', strtotime($wo['end_date']))   : '—' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
var _woTable = null;
$(function() {
    _woTable = $('#workOrdersTable').DataTable({ pageLength: 25, order: [[6,'asc']] });
    filterStatus('open');
});

function filterStatus(mode) {
    ['btnOpen','btnAll','btnClosed'].forEach(function(id){
        document.getElementById(id).className = 'btn btn-outline-secondary';
    });
    var activeBtn = {open:'btnOpen', all:'btnAll', closed:'btnClosed'}[mode];
    if (activeBtn) document.getElementById(activeBtn).className = 'btn btn-primary active';

    $.fn.dataTable.ext.search = [];
    if (mode !== 'all') {
        $.fn.dataTable.ext.search.push(function(settings, data, idx) {
            var row = _woTable.row(idx).node();
            var st  = $(row).data('status') || '';
            if (mode === 'open')   return st === 'open' || st === 'in_progress';
            if (mode === 'closed') return st === 'closed' || st === 'completed';
            return true;
        });
    }
    _woTable.draw();
}
</script>
<?= $this->endSection() ?>