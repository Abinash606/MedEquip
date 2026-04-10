<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Dashboard</h3>
        <p class="text-muted small mb-0">Welcome back, <?= esc(session('username') ?? 'Technician') ?></p>
    </div>
    <div class="position-relative" style="width:40%;min-width:240px;">
        <i class="fa-solid fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:rgba(233,237,255,.4);pointer-events:none;z-index:1;"></i>
        <input type="search" id="techDashSearchInput" class="form-control ps-4"
               placeholder="Search sites, equipment, inspections..."
               autocomplete="off"
               style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:#E9EDFF;border-radius:10px;">
        <div id="techDashSearchResults" class="position-absolute w-100 mt-1 shadow-lg"
             style="z-index:9999;display:none;background:#0E1630;border:1px solid rgba(255,255,255,.15);border-radius:12px;max-height:360px;overflow-y:auto;"></div>
    </div>
</div>

<div class="content">

<!-- 3 Stat Cards: Action Required, Open Requests, Compliance Health -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="glass-card p-3" style="border-left:4px solid rgba(239,68,68,.6);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-bold text-uppercase mb-1">Action Required</div>
                    <h3 class="fw-bold mb-0 text-danger" id="actionRequiredCount"><?= esc($actionRequired ?? 0) ?></h3>
                    <span class="text-muted small">Equipment due for inspection</span>
                </div>
                <div style="width:50px;height:50px;border-radius:14px;background:rgba(239,68,68,.15);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#EF4444;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="<?= site_url('technician/sites') ?>" class="btn btn-sm btn-outline-danger w-100 rounded-pill">View Sites</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-card p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small fw-bold text-uppercase mb-1">Open Requests</div>
                    <h3 class="fw-bold mb-0" id="openRequestsCount"><?= esc($openRequests ?? 0) ?></h3>
                    <span class="text-muted small">Work orders in progress</span>
                </div>
                <div style="width:50px;height:50px;border-radius:14px;background:rgba(245,158,11,.15);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#F59E0B;">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="<?= site_url('technician/service-history') ?>" class="btn btn-sm btn-outline-warning w-100 rounded-pill">View Work Orders</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-card p-3 d-flex align-items-center justify-content-between h-100">
            <div>
                <div class="text-muted small fw-bold text-uppercase mb-1">Compliance Health</div>
                <h4 class="fw-bold mb-1"><?= esc($complianceLabel ?? '—') ?></h4>
                <p class="text-muted small mb-0"><?= esc($compliancePct ?? 0) ?>% pass rate</p>
            </div>
            <?php
                $pct = (int)($compliancePct ?? 0);
                $color = $pct >= 90 ? '#22c55e' : ($pct >= 75 ? '#f59e0b' : '#ef4444');
            ?>
            <div style="width:70px;height:70px;border-radius:50%;background:conic-gradient(<?= $color ?> <?= $pct ?>%, rgba(255,255,255,.08) 0%);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <div style="width:55px;height:55px;background:var(--panel,#0E1630);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;">
                    <?= $pct ?>%
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Inspections Table -->
<div class="glass-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Recent Inspections</h5>
        <a href="<?= site_url('technician/inspections') ?>" class="btn btn-sm btn-primary">View All Reports</a>
    </div>
    <div class="table-responsive">
        <table class="table service-table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>Inspection ID</th>
                    <th>Site</th>
                    <th>Customer</th>
                    <th>Asset</th>
                    <th>Date</th>
                    <th>Result</th>
                    <th>Report</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentInspections)): ?>
                    <?php foreach ($recentInspections as $insp): ?>
                    <?php
                        $st = strtolower($insp['status'] ?? '');
                        $badge = $st === 'pass' ? 'bg-success' : ($st === 'fail' ? 'bg-danger' : 'bg-warning');
                        $inspDisplayId = $insp['group_id'] ? substr($insp['group_id'], 0, 18) : '#INS-' . $insp['id'];
                    ?>
                    <tr>
                        <td class="fw-medium small"><?= esc($inspDisplayId) ?></td>
                        <td><?= esc($insp['site_name'] ?? '—') ?></td>
                        <td><?= esc($insp['customer_name'] ?? '—') ?></td>
                        <td><?= esc($insp['asset_tag'] ?? ($insp['make'] ?? '') . ' ' . ($insp['model'] ?? '')) ?></td>
                        <td class="text-muted small"><?= esc($insp['completed_at'] ? substr($insp['completed_at'], 0, 10) : '—') ?></td>
                        <td><span class="badge <?= $badge ?>"><?= esc(ucfirst($insp['status'])) ?></span></td>
                        <td>
                            <?php if (!empty($insp['group_id'])): ?>
                            <button class="btn btn-sm btn-outline-primary" title="Preview Report"
                                onclick="previewTechReport('<?= esc($insp['group_id']) ?>')">
                                <i class="fa-solid fa-file-pdf"></i>
                            </button>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No recent inspections found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<!-- Technician Report Preview Modal -->
<div class="modal fade" id="techReportPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-file-pdf me-2"></i>Inspection Report Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="techReportPreviewBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="techReportDownloadBtn">
                    <i class="fa-solid fa-download me-1"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function previewTechReport(groupId) {
    var modal = new bootstrap.Modal(document.getElementById('techReportPreviewModal'));
    document.getElementById('techReportPreviewBody').innerHTML =
        '<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3 text-muted">Loading report...</p></div>';
    modal.show();
    fetch('<?= site_url('technician/inspections/reportPdf') ?>/' + groupId)
        .then(function(r){ return r.text(); })
        .then(function(html) {
            var iframe = document.createElement('iframe');
            iframe.style.cssText = 'width:100%;height:70vh;border:none;';
            document.getElementById('techReportPreviewBody').innerHTML = '';
            document.getElementById('techReportPreviewBody').appendChild(iframe);
            iframe.contentDocument.open();
            iframe.contentDocument.write(html);
            iframe.contentDocument.close();
        }).catch(function() {
            document.getElementById('techReportPreviewBody').innerHTML = '<div class="alert alert-danger m-3">Failed to load report.</div>';
        });
    document.getElementById('techReportDownloadBtn').onclick = function() {
        window.open('<?= site_url('technician/inspections/reportPdf') ?>/' + groupId, '_blank');
    };
}

// ── Global Search ─────────────────────────────────────────────────────
(function() {
    var inp   = document.getElementById('techDashSearchInput');
    var box   = document.getElementById('techDashSearchResults');
    var timer = null;
    var SEARCH_URL = '<?= site_url('technician/search') ?>';

    var iconMap = {
        'Site'      : 'fa-sitemap',
        'Equipment' : 'fa-boxes-stacked',
        'Inspection': 'fa-clipboard-list',
    };

    function renderResults(results) {
        if (!results.length) {
            box.innerHTML = '<div class="p-3 text-center" style="color:rgba(233,237,255,.5);font-size:.9rem;">No results found.</div>';
            box.style.display = 'block';
            return;
        }
        var html = '';
        results.forEach(function(r, idx) {
            html += '<a href="' + r.url + '" data-result-idx="' + idx + '" class="d-flex align-items-center gap-3 px-3 py-2 text-decoration-none" ' +
                'style="color:#E9EDFF;border-bottom:1px solid rgba(255,255,255,.06);transition:background .15s;" ' +
                'onmouseover="this.style.background=\'rgba(124,58,237,.2)\'" ' +
                'onmouseout="this.style.background=\'transparent\'">' +
                '<span style="width:28px;text-align:center;opacity:.6;flex-shrink:0;">' +
                    '<i class="fa-solid ' + (iconMap[r.type] || 'fa-file') + '"></i>' +
                '</span>' +
                '<span class="flex-grow-1" style="overflow:hidden;">' +
                    '<span class="d-block fw-semibold" style="font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escH(r.label) + '</span>' +
                    (r.subtitle ? '<span class="d-block" style="font-size:.78rem;color:rgba(233,237,255,.5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escH(r.subtitle) + '</span>' : '') +
                '</span>' +
                '<span class="badge ms-2 flex-shrink-0" style="background:rgba(124,58,237,.35);font-size:.7rem;">' + escH(r.type) + '</span>' +
            '</a>';
        });
        box.innerHTML = html;
        box.style.display = 'block';

        // Intercept clicks: store nav intent in sessionStorage so the site details
        // page can open the right tab + inspection without polluting the URL.
        box._resultData = results;
        box.querySelectorAll('a[data-result-idx]').forEach(function(a) {
            a.addEventListener('click', function(e) {
                var idx = parseInt(a.getAttribute('data-result-idx'), 10);
                var r = box._resultData[idx];
                if (r && r.nav) {
                    e.preventDefault();
                    sessionStorage.setItem('siteNavIntent', JSON.stringify(r.nav));
                    window.location.href = r.url;
                }
            });
        });
    }

    function escH(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    function doSearch(q) {
        if (q.length < 2) { box.style.display = 'none'; return; }
        fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) { renderResults(data.results || []); })
        .catch(function() { box.style.display = 'none'; });
    }

    if (inp) {
        inp.addEventListener('input', function() {
            clearTimeout(timer);
            var q = inp.value.trim();
            if (q.length < 2) { box.style.display = 'none'; return; }
            timer = setTimeout(function() { doSearch(q); }, 280);
        });
        inp.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { box.style.display = 'none'; inp.value = ''; }
        });
        document.addEventListener('click', function(e) {
            if (!inp.contains(e.target) && !box.contains(e.target)) {
                box.style.display = 'none';
            }
        });
    }
})();
</script>
<?= $this->endSection() ?>
