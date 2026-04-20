<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">My Work Orders</h3>
        <p class="text-muted small mb-0">Your assigned work orders.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" id="btnOpen"   class="btn btn-primary active"    onclick="filterStatus('open')">Open</button>
            <button type="button" id="btnAll"    class="btn btn-outline-secondary" onclick="filterStatus('all')">All</button>
            <button type="button" id="btnClosed" class="btn btn-outline-secondary" onclick="filterStatus('closed')">Closed</button>
        </div>
    </div>
</div>

<div class="content">
<div class="glass-card p-3">
    <div class="table-responsive">
        <table id="woTable" class="table table-hover service-table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Site / Customer</th>
                    <th>Asset Tag</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th class="no-sort"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($workOrders as $wo): ?>
                <?php
                    $st = strtolower($wo['status'] ?? '');
                    $p  = strtolower($wo['priority'] ?? '');
                    $pClass = $p === 'critical' ? 'danger' : ($p === 'high' ? 'warning' : ($p === 'normal' ? 'info' : 'secondary'));
                    $sClass = $st === 'open' ? 'success' : ($st === 'in_progress' ? 'primary' : 'secondary');
                ?>
                <tr data-status="<?= esc($st) ?>">
                    <td>WO-<?= esc($wo['id']) ?></td>
                    <td><?= esc($wo['title']) ?></td>
                    <td>
                        <?= esc($wo['site_name'] ?? '—') ?>
                        <?php if (!empty($wo['customer_name'])): ?>
                            <br><small class="text-muted"><?= esc($wo['customer_name']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($wo['asset_tag'] ?? '—') ?></td>
                    <td><span class="badge bg-<?= $pClass ?>"><?= esc(ucfirst($p ?: '—')) ?></span></td>
                    <td><span class="badge bg-<?= $sClass ?>"><?= esc(ucwords(str_replace('_',' ',$wo['status'] ?? ''))) ?></span></td>
                    <td><?= esc($wo['start_date'] ? date('M d, Y', strtotime($wo['start_date'])) : '—') ?></td>
                    <td><?= esc($wo['end_date']   ? date('M d, Y', strtotime($wo['end_date']))   : '—') ?></td>
                    <td><a href="<?= site_url('technician/work-orders/view/' . $wo['id']) ?>" class="btn btn-sm btn-primary">View</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($workOrders)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">No work orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<style>
#woTable, #woTable thead th, #woTable tbody td {
    --bs-table-bg: transparent !important;
    --bs-table-striped-bg: rgba(255,255,255,.03) !important;
    --bs-table-color: #e9edff !important;
    background: transparent !important;
    color: #e9edff !important;
    border-color: rgba(255,255,255,.06) !important;
}
/* DataTables dark overrides */
.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_filter label,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate .paginate_button {
    color: #e9edff !important;
}
.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
    background: rgba(255,255,255,.06) !important;
    border: 1px solid rgba(255,255,255,.15) !important;
    color: #e9edff !important;
    border-radius: 6px;
    padding: 4px 8px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(90deg,rgba(34,211,238,.8),rgba(124,58,237,.7)) !important;
    border: none !important;
    color: #fff !important;
    border-radius: 6px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: rgba(255,255,255,.08) !important;
    border: none !important;
    color: #fff !important;
    border-radius: 6px;
}
</style>

<script>
var _allRows = [];
var _woTable = null;

document.addEventListener('DOMContentLoaded', function() {
    // Init DataTable
    if ($.fn.DataTable) {
        _woTable = $('#woTable').DataTable({
            pageLength: 25,
            responsive: true,
            columnDefs: [{ orderable: false, targets: -1 }],
            language: { search: 'Search:', lengthMenu: 'Show _MENU_ entries' }
        });
    }
    // Default: show open only
    filterStatus('open');
});

function filterStatus(status) {
    // Update button styles
    var map = { open:'btnOpen', all:'btnAll', closed:'btnClosed' };
    Object.keys(map).forEach(function(k) {
        var b = document.getElementById(map[k]);
        if (!b) return;
        b.className = k === status
            ? 'btn btn-sm btn-primary active'
            : 'btn btn-sm btn-outline-secondary';
    });

    if (_woTable) {
        // Use DataTables search on the Status column (index 5) via regex
        if (status === 'all') {
            _woTable.column(5).search('').draw();
        } else if (status === 'closed') {
            _woTable.column(5).search('Closed|Completed', true, false).draw();
        } else {
            _woTable.column(5).search('^Open$', true, false).draw();
        }
    } else {
        // Fallback: CSS visibility
        document.querySelectorAll('#woTable tbody tr[data-status]').forEach(function(row) {
            var s = row.getAttribute('data-status');
            if (status === 'all') { row.style.display = ''; return; }
            if (status === 'closed') { row.style.display = (s==='closed'||s==='completed') ? '':' none'; return; }
            row.style.display = s === status ? '' : 'none';
        });
    }
}
</script>

<?= $this->endSection() ?>
