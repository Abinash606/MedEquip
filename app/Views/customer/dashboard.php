<?= $this->extend('layouts/customer-header') ?>
<?= $this->section('content') ?>
<style>
/* ===== DARK THEME: Modals & DataTables ===== */
.modal-content {
    background: #0E1630 !important;
    border: 1px solid rgba(255,255,255,.12) !important;
    color: #E9EDFF !important;
    border-radius: 16px !important;
}
.modal-header {
    background: linear-gradient(135deg, rgba(124,58,237,.9), rgba(34,211,238,.8)) !important;
    border-bottom: none !important;
    border-radius: 16px 16px 0 0 !important;
}
.modal-footer {
    border-top: 1px solid rgba(255,255,255,.08) !important;
    background: rgba(7,10,18,.4) !important;
    border-radius: 0 0 16px 16px !important;
}
.modal-body { background: rgba(14,22,48,.6) !important; }
.modal-title, .modal-body .form-label, .modal-body label,
.modal-body h5, .modal-body p { color: #E9EDFF !important; }
.modal-body .form-control, .modal-body .form-select {
    background: rgba(255,255,255,.06) !important;
    border: 1px solid rgba(255,255,255,.14) !important;
    color: #E9EDFF !important;
    border-radius: 10px !important;
}
.modal-body .form-control::placeholder { color: rgba(233,237,255,.35) !important; }
.modal-body .form-select option { color: #000 !important; background: #fff !important; }
.modal-body .form-control[readonly] { background: rgba(255,255,255,.03) !important; color: rgba(233,237,255,.5) !important; }
.modal-body .alert { border-radius: 10px !important; }
/* DataTables */
table.dataTable thead th {
    background: rgba(7,10,18,.6) !important;
    color: rgba(233,237,255,.55) !important;
    border-bottom: 1px solid rgba(255,255,255,.08) !important;
    font-size: 11px; letter-spacing: .12em; text-transform: uppercase;
}
table.dataTable tbody tr { background: transparent !important; }
table.dataTable tbody tr:hover { background: rgba(255,255,255,.04) !important; }
table.dataTable tbody td {
    border-bottom: 1px solid rgba(255,255,255,.05) !important;
    color: #E9EDFF !important;
}
table.dataTable.stripe tbody tr.odd,
table.dataTable.stripe tbody tr.even { background: transparent !important; }
.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
    background: rgba(255,255,255,.06) !important;
    border: 1px solid rgba(255,255,255,.12) !important;
    color: #E9EDFF !important; border-radius: 8px !important; padding: 4px 8px;
}
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter { color: rgba(233,237,255,.6) !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button { color: #E9EDFF !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg,rgba(124,58,237,.8),rgba(34,211,238,.6)) !important;
    border-color: transparent !important; color: #fff !important;
}
/* Buttons in modals */
.modal .btn-primary { background: linear-gradient(90deg,rgba(34,211,238,.9),rgba(124,58,237,.8)) !important; border: none !important; color: #fff !important; }
.modal .btn-danger  { background: rgba(239,68,68,.85) !important; border: none !important; }
.modal .btn-outline-secondary { color: rgba(233,237,255,.7) !important; border-color: rgba(255,255,255,.2) !important; }
/* ===== END DARK THEME ===== */
</style>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Overview</h3>
        <p class="text-muted small mb-0">Equipment compliance dashboard</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <select id="siteFilterDash" class="form-select form-select-sm" style="min-width:220px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:#E9EDFF;border-radius:10px;">
            <option value="">All Sites</option>
            <?php foreach ($sites as $site): ?>
                <option value="<?= esc($site['id']) ?>"><?= esc($site['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-sm btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#serviceRequestModal">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> Report Issue
        </button>
    </div>
</div>

<div class="content">

<!-- Summary Stat Cards -->
<div class="row g-4 mb-4" id="summaryCards">
    <div class="col-md-3">
        <div class="glass-card p-3">
            <div class="text-muted small fw-bold text-uppercase mb-2">Total Assets</div>
            <h2 class="fw-bold mb-0" id="statTotalAssets"><?= esc($totalAssets) ?></h2>
            <div class="small text-muted mt-1">Across <?= count($sites) ?> site(s)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3" style="border-left: 4px solid rgba(245,158,11,.6);">
            <div class="text-muted small fw-bold text-uppercase mb-2">Compliance Alert</div>
            <h2 class="fw-bold mb-0 text-warning" id="statDueSoon"><?= esc($dueSoon) ?></h2>
            <div class="small text-muted mt-1">Inspections due in 7 days</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3">
            <div class="text-muted small fw-bold text-uppercase mb-2">Active Repairs</div>
            <h2 class="fw-bold mb-0" id="statOpenWO"><?= esc($totalOpenWO) ?></h2>
            <div class="small text-muted mt-1">Open work orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-bold text-uppercase mb-2">Compliance</div>
                <h2 class="fw-bold mb-0 <?= $overallCompliance >= 90 ? 'text-success' : ($overallCompliance >= 75 ? 'text-warning' : 'text-danger') ?>" id="statCompliance"><?= esc($overallCompliance) ?>%</h2>
                <div class="small text-muted mt-1">Pass rate</div>
            </div>
            <div id="complianceCircle" style="width:60px;height:60px;border-radius:50%;
                background:conic-gradient(<?= $overallCompliance >= 90 ? '#22c55e' : ($overallCompliance >= 75 ? '#f59e0b' : '#ef4444') ?> <?= $overallCompliance ?>%, rgba(255,255,255,.08) 0%);
                display:flex;align-items:center;justify-content:center;">
                <div style="width:46px;height:46px;background:var(--panel,#0E1630);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;">
                    <?= esc($overallCompliance) ?>%
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Per-Site Asset Cards -->
<div class="row g-3 mb-4" id="siteCards">
    <?php foreach ($siteStats as $stat): ?>
    <div class="col-md-4 site-card" data-site-id="<?= esc($stat['id']) ?>">
        <div class="glass-card p-3" style="cursor:pointer;" onclick="filterBySite(<?= $stat['id'] ?>)">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="fw-semibold" style="font-size:.9rem;"><?= esc($stat['name']) ?></div>
                <span class="badge" style="background:rgba(34,211,238,.15);color:rgba(34,211,238,.9);border-color:rgba(34,211,238,.2);">
                    <?= $stat['compliance_pct'] ?>% compliant
                </span>
            </div>
            <div class="row g-2 text-center mt-2">
                <div class="col-4">
                    <a href="<?= site_url('customer/assets') ?>" class="text-decoration-none text-white">
                        <div class="fw-bold fs-5 hover-highlight"><?= $stat['asset_count'] ?></div>
                        <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;">Assets</div>
                    </a>
                </div>
                <div class="col-4">
                    <a href="<?= site_url('customer/inspections') ?>" class="text-decoration-none text-white">
                        <div class="fw-bold fs-5 hover-highlight"><?= $stat['inspection_count'] ?></div>
                        <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;">Inspections</div>
                    </a>
                </div>
                <div class="col-4">
                    <div class="fw-bold fs-5 <?= $stat['open_wo'] > 0 ? 'text-warning' : '' ?>"><?= $stat['open_wo'] ?></div>
                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;">Open WO</div>
                </div>
            </div>
            <div class="mt-3">
                <div class="progress" style="height:4px;">
                    <?php
                    $pct = $stat['compliance_pct'];
                    $clr = $pct >= 90 ? '#22c55e' : ($pct >= 75 ? '#f59e0b' : '#ef4444');
                    ?>
                    <div class="progress-bar" style="width:<?= $pct ?>%;background:<?= $clr ?>;border-radius:99px;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted mt-1" style="font-size:.7rem;">
                    <span>Compliance</span>
                    <span><?= $pct ?>%</span>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($siteStats)): ?>
    <div class="col-12">
        <div class="glass-card p-4 text-center text-muted">
            <i class="fa-solid fa-sitemap fa-2x mb-3 opacity-25"></i>
            <p class="mb-0">No sites found. Contact your service provider to get started.</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Inspections Table -->
<div class="glass-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Recent Inspections</h5>
        <a href="<?= site_url('customer/inspections') ?>" class="btn btn-sm btn-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table custom-table align-middle" id="dashInspTable" style="width:100%">
            <thead>
                <tr>
                    <th>Inspection ID</th>
                    <th>Result</th>
                    <th>Site</th>
                    <th>Asset</th>
                    <th>Model</th>
                    <th>Date</th>
                    <th>Tech</th>
                    <th>Report</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentInspections as $row):
                    $badgeClass = strtolower($row['status']) === 'pass' ? 'bg-success'
                        : (strtolower($row['status']) === 'fail' ? 'bg-danger' : 'bg-warning');
                    $inspDisplayId = !empty($row['group_id']) ? substr($row['group_id'], 0, 18) : '#INS-' . $row['id'];
                ?>
                    <tr class="insp-row" data-site-id="<?= (int)($row['site_id'] ?? 0) ?>">
                        <td class="fw-medium small"><?= esc($inspDisplayId) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= esc(ucfirst($row['status'])) ?></span></td>
                        <td><?= esc($row['site_name'] ?? '—') ?></td>
                        <td class="fw-medium"><?= esc($row['asset_tag'] ?? '—') ?></td>
                        <td><?= esc(($row['make'] ? $row['make'] . ' ' : '') . ($row['model'] ?? '')) ?></td>
                        <td><?= esc($row['completed_at'] ? substr($row['completed_at'], 0, 10) : '—') ?></td>
                        <td><?= esc($row['tech_name'] ?? '—') ?></td>
                        <td>
                            <?php if (!empty($row['group_id'])): ?>
                            <button class="btn btn-sm btn-outline-primary" title="Preview Report"
                                onclick="previewCustDashReport('<?= esc($row['group_id']) ?>')">
                                <i class="fa-solid fa-file-pdf"></i>
                            </button>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentInspections)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No inspection records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div><!-- .content -->

<!-- Service Request / Report Issue Modal -->
<div class="modal fade" id="serviceRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= site_url('customer/assets/report-issue') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Report Issue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Site</label>
                        <select class="form-select" name="site_id">
                            <option value="">-- Select Site (optional) --</option>
                            <?php foreach ($sites as $site): ?>
                                <option value="<?= esc($site['id']) ?>"><?= esc($site['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Asset Tag (if known)</label>
                        <input type="text" class="form-control" name="asset_tag" placeholder="e.g. ASSET-1234">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Issue Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="issue_description" rows="4" required placeholder="Describe the issue in detail..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Priority</label>
                        <select class="form-select" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-paper-plane me-1"></i>Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Per-site filter on dashboard
var allSiteStats = <?= json_encode($siteStats) ?>;

function filterBySite(siteId) {
    var sel = document.getElementById('siteFilterDash');
    if (sel) sel.value = siteId;
    applySiteFilter(siteId);
}

document.getElementById('siteFilterDash').addEventListener('change', function() {
    applySiteFilter(this.value);
});

function applySiteFilter(siteId) {
    siteId = parseInt(siteId) || 0;

    // Show/hide site cards
    document.querySelectorAll('.site-card').forEach(function(card) {
        card.style.display = (!siteId || parseInt(card.dataset.siteId) === siteId) ? '' : 'none';
    });

    // Filter Recent Inspections table by site
    var visibleCount = 0;
    document.querySelectorAll('#dashInspTable tbody .insp-row').forEach(function(row) {
        var show = !siteId || parseInt(row.dataset.siteId) === siteId;
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    var emptyRow = document.getElementById('inspEmptyRow');
    if (emptyRow) emptyRow.style.display = visibleCount === 0 ? '' : 'none';

    var complianceEl = document.getElementById('statCompliance');
    var dueSoonEl    = document.getElementById('statDueSoon');
    var circleEl     = document.getElementById('complianceCircle');

    function applyComplianceVisual(pct) {
        pct = parseInt(pct || 0, 10) || 0;
        if (!complianceEl || !circleEl) return;
        complianceEl.textContent = pct + '%';
        complianceEl.className = 'fw-bold mb-0 ' + (pct >= 90 ? 'text-success' : (pct >= 75 ? 'text-warning' : 'text-danger'));
        var clr = pct >= 90 ? '#22c55e' : (pct >= 75 ? '#f59e0b' : '#ef4444');
        circleEl.style.background = 'conic-gradient(' + clr + ' ' + pct + '%, rgba(255,255,255,.08) 0%)';
        var inner = circleEl.querySelector('div');
        if (inner) inner.textContent = pct + '%';
    }

    if (!siteId) {
        document.getElementById('statTotalAssets').textContent = <?= $totalAssets ?>;
        document.getElementById('statOpenWO').textContent      = <?= $totalOpenWO ?>;
        if (dueSoonEl) dueSoonEl.textContent = <?= $dueSoon ?>;
        applyComplianceVisual(<?= $overallCompliance ?>);
    } else {
        var stat = allSiteStats.find(function(s) { return s.id == siteId; });
        if (stat) {
            document.getElementById('statTotalAssets').textContent = stat.asset_count;
            document.getElementById('statOpenWO').textContent      = stat.open_wo;
            if (dueSoonEl) dueSoonEl.textContent = stat.due_soon || 0;
            applyComplianceVisual(stat.compliance_pct);
        }
    }
}

// ── Customer Dashboard Report Preview ────────────────────────────────
function previewCustDashReport(groupId) {
    var modal = new bootstrap.Modal(document.getElementById('custDashReportModal'));
    document.getElementById('custDashReportBody').innerHTML =
        '<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3 text-muted">Loading report...</p></div>';
    modal.show();
    fetch('<?= site_url('customer/inspections/reportPdf') ?>/' + groupId)
        .then(function(r){ return r.text(); })
        .then(function(html) {
            var iframe = document.createElement('iframe');
            iframe.style.cssText = 'width:100%;height:72vh;border:none;';
            document.getElementById('custDashReportBody').innerHTML = '';
            document.getElementById('custDashReportBody').appendChild(iframe);
            iframe.contentDocument.open();
            iframe.contentDocument.write(html);
            iframe.contentDocument.close();
        }).catch(function() {
            document.getElementById('custDashReportBody').innerHTML =
                '<div class="alert alert-danger m-3">Failed to load report.</div>';
        });
    document.getElementById('custDashReportDownloadBtn').onclick = function() {
        window.open('<?= site_url('customer/inspections/reportPdf') ?>/' + groupId, '_blank');
    };
}
</script>

<!-- Customer Dashboard Report Preview Modal -->
<div class="modal fade" id="custDashReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fa-solid fa-file-pdf me-2"></i>Inspection Report Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="custDashReportBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="custDashReportDownloadBtn">
                    <i class="fa-solid fa-download me-1"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
