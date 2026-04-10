<?= $this->extend('layouts/customer-header') ?>
<?= $this->section('content') ?>
<style>
.modal-content{background:#0E1630!important;border:1px solid rgba(255,255,255,.12)!important;color:#E9EDFF!important;border-radius:16px!important;}
.modal-header{background:linear-gradient(135deg,rgba(124,58,237,.9),rgba(34,211,238,.8))!important;border-bottom:none!important;border-radius:16px 16px 0 0!important;}
.modal-footer{border-top:1px solid rgba(255,255,255,.08)!important;background:rgba(7,10,18,.4)!important;border-radius:0 0 16px 16px!important;}
.modal-body{background:rgba(14,22,48,.6)!important;}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Documents &amp; Reports</h3>
        <p class="text-muted small mb-0">Access inspection reports and compliance documents for your sites.</p>
    </div>
</div>

<div class="content">
<div class="glass-card p-3">

    <?php if (empty($reports)): ?>
    <div class="text-center py-5 text-muted">
        <i class="fa-solid fa-file-circle-xmark fa-2x mb-3 opacity-25"></i>
        <p class="mb-0">No inspection reports found for your sites.</p>
    </div>
    <?php else: ?>

    <ul class="list-unstyled mb-0">
        <?php foreach ($reports as $i => $rpt):
            $dateStr  = !empty($rpt['completed_at']) ? $rpt['completed_at'] : ($rpt['scheduled_at'] ?? '');
            $inspId   = $rpt['group_id'] ?? '—'; // Use real group_id from DB
            $dateLabel = $dateStr ? date('M j, Y', strtotime($dateStr)) : '—';
            $isLast   = ($i === count($reports) - 1);
            $stLow    = strtolower($rpt['status'] ?? '');
            $stCls    = ($stLow === 'pass') ? 'bg-success' : (($stLow === 'fail') ? 'bg-danger' : 'bg-warning text-dark');
            $stLabel  = ($stLow === 'pass') ? 'Pass' : (($stLow === 'fail') ? 'Fail' : ucfirst($rpt['status'] ?? 'In Progress'));
        ?>
        <li class="d-flex justify-content-between align-items-center py-3 <?= !$isLast ? 'border-bottom' : '' ?>">
            <div>
                <!-- Customer / Site -->
                <div class="fw-semibold" style="font-size:.95rem;">
                    <?= esc($rpt['customer_name'] ?? '—') ?>
                    <?php if (!empty($rpt['site_name'])): ?>
                    <span class="text-muted fw-normal"> / <?= esc($rpt['site_name']) ?></span>
                    <?php endif; ?>
                </div>
                <!-- Inspection ID -->
                <div class="mt-1">
                    <span class="t-pill" style="font-size:11px;"><?= esc($inspId) ?></span>
                </div>
                <!-- Date + type + devices + status -->
                <div class="text-muted small mt-1">
                    <?= esc($dateLabel) ?>
                    &nbsp;•&nbsp; <?= esc($rpt['inspection_type'] ?? 'Inspection') ?>
                    &nbsp;•&nbsp; <?= esc($rpt['device_count']) ?> device<?= $rpt['device_count'] != 1 ? 's' : '' ?>
                    &nbsp;•&nbsp; <span class="badge <?= $stCls ?> badge-sm"><?= esc($stLabel) ?></span>
                </div>
            </div>
            <!-- Preview & Download button -->
            <button class="btn btn-sm btn-outline-primary ms-3 flex-shrink-0"
                    onclick="previewCustDocReport('<?= esc($rpt['group_id']) ?>')"
                    title="Preview & Download">
                <i class="fa-solid fa-file-pdf me-1"></i>
                <span class="d-none d-md-inline">Preview</span>
                <i class="fa-solid fa-download ms-1"></i>
            </button>
        </li>
        <?php endforeach; ?>
    </ul>

    <?php endif; ?>
</div>
</div>

<!-- ── Report Preview Modal ────────────────────────────────────── -->
<div class="modal fade" id="custDocReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fa-solid fa-file-pdf me-2"></i>Inspection Report Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="custDocReportBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="custDocReportDownloadBtn">
                    <i class="fa-solid fa-download me-1"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function previewCustDocReport(groupId) {
    var modal = new bootstrap.Modal(document.getElementById('custDocReportModal'));
    document.getElementById('custDocReportBody').innerHTML =
        '<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>' +
        '<p class="mt-3 text-muted">Loading report...</p></div>';
    modal.show();

    fetch('<?= site_url('customer/inspections/reportPdf') ?>/' + groupId)
        .then(function(r) { return r.text(); })
        .then(function(html) {
            var iframe = document.createElement('iframe');
            iframe.style.cssText = 'width:100%;height:72vh;border:none;';
            document.getElementById('custDocReportBody').innerHTML = '';
            document.getElementById('custDocReportBody').appendChild(iframe);
            iframe.contentDocument.open();
            iframe.contentDocument.write(html);
            iframe.contentDocument.close();
        })
        .catch(function() {
            document.getElementById('custDocReportBody').innerHTML =
                '<div class="alert alert-danger m-3">Failed to load report. Please try again.</div>';
        });

    document.getElementById('custDocReportDownloadBtn').onclick = function() {
        window.open('<?= site_url('customer/inspections/reportPdf') ?>/' + groupId, '_blank');
    };
}
</script>

<?= $this->endSection() ?>
