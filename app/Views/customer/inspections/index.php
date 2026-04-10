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
</div>

<div class="content">

<!-- Summary Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="glass-card p-3 text-center">
            <div class="text-muted small text-uppercase fw-bold mb-1">Total Inspections</div>
            <h3 class="fw-bold mb-0"><?= esc($inspectionsCount ?? 0) ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3 text-center">
            <div class="text-muted small text-uppercase fw-bold mb-1">Sites</div>
            <h3 class="fw-bold mb-0"><?= esc($sitesCount ?? 0) ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3 text-center">
            <div class="text-muted small text-uppercase fw-bold mb-1">Open</div>
            <h3 class="fw-bold mb-0"><?= esc($openCount ?? 0) ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3 text-center">
            <div class="text-muted small text-uppercase fw-bold mb-1">Completed</div>
            <h3 class="fw-bold mb-0"><?= esc($completedCount ?? 0) ?></h3>
        </div>
    </div>
</div>

<!-- Inspection Report Table -->
<div class="glass-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Inspection Report Overview</h5>
        <div class="d-flex gap-2 align-items-center">
            <?php if (!empty($sites)): ?>
            <select id="custSiteFilter" class="form-select form-select-sm" style="min-width:160px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:#E9EDFF;border-radius:8px;">
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

    <!-- Filters -->
    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" id="filterSiteName" class="form-control form-control-sm" placeholder="Filter by site...">
        </div>
        <div class="col-md-3">
            <select id="filterResult" class="form-select form-select-sm">
                <option value="">All Results</option>
                <option value="Pass">Pass</option>
                <option value="Fail">Fail</option>
                <option value="Repair">Repair</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" id="filterDateFrom" class="form-control form-control-sm" title="From date">
        </div>
        <div class="col-md-3">
            <input type="date" id="filterDateTo" class="form-control form-control-sm" title="To date">
        </div>
    </div>

    <div class="table-responsive">
        <table id="custInspTable" class="table table-hover service-table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>Inspection ID</th>
                    <th>Pass/Fail</th>
                    <th>Site</th>
                    <th>Equipment</th>
                    <th>Type</th>
                    <th>S/N</th>
                    <th>Asset #</th>
                    <th>Scheduled</th>
                    <th>Completed</th>
                    <th>Next Due</th>
                    <th>Report</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Build site ID lookup
                $siteIdMap = [];
                foreach ($sites as $s) { $siteIdMap[$s['id']] = $s['name']; }

                // Open inspections
                foreach ($upcomingInspections as $insp):
                    $inspId = $insp['group_id'] ?? '—'; // Use real group_id from DB
                    $equip  = trim(($insp['make'] ?? '') . ' ' . ($insp['model'] ?? '')) ?: ($insp['device_type'] ?? '—');
                ?>
                <tr data-status="open" data-site-id="<?= esc($insp['site_id'] ?? '') ?>">
                    <td><span class="t-pill" style="font-size:11px;"><?= esc($inspId) ?></span></td>
                    <td><span class="badge bg-warning text-dark">In Progress</span></td>
                    <td><?= esc($insp['site_name'] ?? '—') ?></td>
                    <td><?= esc($equip) ?></td>
                    <td><?= esc($insp['inspection_type'] ?? 'Equipment Inspection') ?></td>
                    <td><?= esc($insp['serial_number'] ?? '—') ?></td>
                    <td><?= esc($insp['asset_tag'] ?? '—') ?></td>
                    <td class="text-muted small"><?= !empty($insp['scheduled_at']) ? date('M j, Y', strtotime($insp['scheduled_at'])) : '—' ?></td>
                    <td class="text-muted small">—</td>
                    <td class="text-muted small"><?= !empty($insp['next_due_date']) ? date('M j, Y', strtotime($insp['next_due_date'])) : '—' ?></td>
                    <td>
                        <?php if (!empty($insp['group_id'])): ?>
                        <button class="btn btn-sm btn-outline-primary" onclick="previewCustReport('<?= esc($insp['group_id']) ?>')">
                            <i class="fa-solid fa-file-pdf"></i>
                        </button>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php
                // Closed/completed inspections
                foreach ($inspectionHistory as $insp):
                    $inspId = $insp['group_id'] ?? '—'; // Use real group_id from DB
                    $equip  = trim(($insp['make'] ?? '') . ' ' . ($insp['model'] ?? '')) ?: ($insp['device_type'] ?? '—');
                    $stLow  = strtolower($insp['status'] ?? '');
                    $stCls  = ($stLow === 'pass') ? 'bg-success' : (($stLow === 'fail') ? 'bg-danger' : 'bg-warning text-dark');
                ?>
                <tr data-status="closed" data-site-id="<?= esc($insp['site_id'] ?? '') ?>">
                    <td><span class="t-pill" style="font-size:11px;"><?= esc($inspId) ?></span></td>
                    <td><span class="badge <?= $stCls ?>"><?= esc(ucfirst($insp['status'] ?? '—')) ?></span></td>
                    <td><?= esc($insp['site_name'] ?? '—') ?></td>
                    <td><?= esc($equip) ?></td>
                    <td><?= esc($insp['inspection_type'] ?? 'Equipment Inspection') ?></td>
                    <td><?= esc($insp['serial_number'] ?? '—') ?></td>
                    <td><?= esc($insp['asset_tag'] ?? '—') ?></td>
                    <td class="text-muted small"><?= !empty($insp['scheduled_at'])  ? date('M j, Y', strtotime($insp['scheduled_at']))  : '—' ?></td>
                    <td class="text-muted small"><?= !empty($insp['completed_at'])  ? date('M j, Y', strtotime($insp['completed_at']))  : '—' ?></td>
                    <td class="text-muted small"><?= !empty($insp['next_due_date']) ? date('M j, Y', strtotime($insp['next_due_date'])) : '—' ?></td>
                    <td>
                        <?php if (!empty($insp['group_id'])): ?>
                        <button class="btn btn-sm btn-outline-primary" onclick="previewCustReport('<?= esc($insp['group_id']) ?>')">
                            <i class="fa-solid fa-file-pdf"></i>
                        </button>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
var _cit = null, _cim = 'open', _cis = '';

$(function() {
    _cit = $('#custInspTable').DataTable({
        pageLength: 25,
        order: [[7, 'desc']],
        columnDefs: [{ orderable: false, targets: 10 }]
    });

    custInspFilter('open');

    $('#custSiteFilter').on('change', function() {
        _cis = this.value;
        _aif();
    });

    $('#filterSiteName, #filterResult, #filterDateFrom, #filterDateTo').on('input change', _aif);
});

function custInspFilter(m) {
    _cim = m;
    ['btnCustInspOpen','btnCustInspAll','btnCustInspClosed'].forEach(function(id) {
        document.getElementById(id).className = 'btn btn-sm btn-outline-secondary';
    });
    var map = {open:'btnCustInspOpen', all:'btnCustInspAll', closed:'btnCustInspClosed'};
    if (map[m]) document.getElementById(map[m]).className = 'btn btn-sm btn-primary active';
    _aif();
}

function _aif() {
    $.fn.dataTable.ext.search = [];
    var siteName = ($('#filterSiteName').val() || '').toLowerCase();
    var result   = ($('#filterResult').val() || '').toLowerCase();
    var from     = $('#filterDateFrom').val();
    var to       = $('#filterDateTo').val();

    $.fn.dataTable.ext.search.push(function(s, d, i) {
        var row = _cit.row(i).node();
        var st  = $(row).data('status') || '';
        var sid = String($(row).data('site-id') || '');

        if (_cim === 'open'   && st !== 'open')   return false;
        if (_cim === 'closed' && st !== 'closed') return false;
        if (_cis && sid !== String(_cis))          return false;

        var rowData = _cit.row(i).data();
        if (!rowData) return true;

        // Site name text filter
        var siteCell = (d[2] || '').toLowerCase();
        if (siteName && !siteCell.includes(siteName)) return false;

        // Result filter — check the badge text in column 1
        if (result) {
            var resultCell = (d[1] || '').toLowerCase();
            if (!resultCell.includes(result)) return false;
        }

        // Date filters on scheduled date (col 7)
        var dt = (d[7] || '').trim();
        if (from && dt) {
            var dtParsed = new Date(dt);
            var fromParsed = new Date(from);
            if (dtParsed < fromParsed) return false;
        }
        if (to && dt) {
            var dtParsed2 = new Date(dt);
            var toParsed = new Date(to);
            if (dtParsed2 > toParsed) return false;
        }
        return true;
    });
    _cit.draw();
}

// ── Report Preview ─────────────────────────────────────────────────────
function previewCustReport(groupId) {
    var modal = new bootstrap.Modal(document.getElementById('custInspReportModal'));
    document.getElementById('custInspReportBody').innerHTML =
        '<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3 text-muted">Loading report...</p></div>';
    modal.show();
    fetch('<?= site_url('customer/inspections/reportPdf') ?>/' + groupId)
        .then(function(r) { return r.text(); })
        .then(function(html) {
            var iframe = document.createElement('iframe');
            iframe.style.cssText = 'width:100%;height:72vh;border:none;';
            document.getElementById('custInspReportBody').innerHTML = '';
            document.getElementById('custInspReportBody').appendChild(iframe);
            iframe.contentDocument.open();
            iframe.contentDocument.write(html);
            iframe.contentDocument.close();
        }).catch(function() {
            document.getElementById('custInspReportBody').innerHTML =
                '<div class="alert alert-danger m-3">Failed to load report.</div>';
        });
    document.getElementById('custInspReportDownloadBtn').onclick = function() {
        window.open('<?= site_url('customer/inspections/reportPdf') ?>/' + groupId, '_blank');
    };
}
</script>

<!-- Customer Inspection Report Preview Modal -->
<div class="modal fade" id="custInspReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fa-solid fa-file-pdf me-2"></i>Inspection Report Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="custInspReportBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="custInspReportDownloadBtn">
                    <i class="fa-solid fa-download me-1"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
