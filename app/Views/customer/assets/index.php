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
        <h4 class="fw-bold mb-0">Assets</h4>
        <div class="text-muted small">View your equipment inventory by site.</div>
    </div>
</div>

<div class="content">

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Site Filter -->
    <div class="glass-card mb-3 p-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Filter by Site</label>
                <select id="siteFilter" class="form-select form-select-sm">
                    <option value="">All Sites</option>
                    <?php foreach ($sites as $site): ?>
                        <option value="<?= esc($site['id']) ?>"><?= esc($site['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Search</label>
                <input type="text" id="assetSearch" class="form-control form-control-sm" placeholder="Asset tag, model, serial...">
            </div>
            <div class="col-md-4 text-end">
                <span class="text-muted small" id="assetCountLabel"></span>
            </div>
        </div>
    </div>

    <!-- Site Stats Cards -->
    <div class="row g-3 mb-3" id="siteStatsRow">
        <?php
        // Count equipment per site
        $equipBySite = [];
        foreach ($equipment as $eq) {
            $sid = $eq['site_id'] ?? 0;
            $equipBySite[$sid] = ($equipBySite[$sid] ?? 0) + 1;
        }
        foreach ($sites as $site):
            $cnt = $equipBySite[$site['id']] ?? 0;
        ?>
        <div class="col-md-3">
            <div class="glass-card p-3 site-stat-card h-100" data-site-id="<?= $site['id'] ?>" style="cursor:pointer;">
                <div class="text-muted small text-uppercase fw-bold mb-1"><?= esc($site['name']) ?></div>
                <h3 class="fw-bold mb-0"><?= $cnt ?></h3>
                <div class="small text-muted mt-1">Assets</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Assets Table -->
    <div class="glass-card p-3">
        <div class="table-responsive">
            <table id="assetsTable" class="table custom-table table-hover align-middle mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>Asset Tag</th>
                        <th>Serial Number</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Device Type</th>
                        <th>Department</th>
                        <th>Site</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipment)): ?>
                        <?php
                        // Build site name lookup
                        $siteMap = [];
                        foreach ($sites as $s) { $siteMap[$s['id']] = $s['name']; }
                        foreach ($equipment as $eq):
                        ?>
                        <tr data-site-id="<?= esc($eq['site_id'] ?? '') ?>">
                            <td class="fw-medium"><?= esc($eq['asset_tag']) ?></td>
                            <td><?= esc($eq['serial_number'] ?? '—') ?></td>
                            <td><?= esc($eq['make'] ?? '—') ?></td>
                            <td><?= esc($eq['model'] ?? '—') ?></td>
                            <td><?= esc($eq['device_type'] ?? '—') ?></td>
                            <td><?= esc($eq['department'] ?? '—') ?></td>
                            <td><?= esc($siteMap[$eq['site_id'] ?? 0] ?? '—') ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger report-issue-btn d-flex gap-2 align-items-center"
                                    data-asset-tag="<?= esc($eq['asset_tag'], 'attr') ?>"
                                    data-make="<?= esc($eq['make'] ?? '', 'attr') ?>"
                                    data-model="<?= esc($eq['model'] ?? '', 'attr') ?>"
                                    data-serial="<?= esc($eq['serial_number'] ?? '', 'attr') ?>"
                                    data-type="<?= esc($eq['device_type'] ?? '', 'attr') ?>"
                                    data-equipment-id="<?= esc($eq['id']) ?>">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Report Issue
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No assets found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Report Issue Modal -->
<div class="modal fade" id="reportIssueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="reportIssueForm" action="<?= site_url('customer/assets/report-issue') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="equipment_id" id="issue-equipment-id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Report Issue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Asset Tag</label>
                            <input type="text" class="form-control" id="issue-asset-tag" name="asset_tag" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Device Type</label>
                            <input type="text" class="form-control" id="issue-type" name="device_type" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Make</label>
                            <input type="text" class="form-control" id="issue-make" name="make" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Model</label>
                            <input type="text" class="form-control" id="issue-model" name="model" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Serial Number</label>
                            <input type="text" class="form-control" id="issue-serial" name="serial_number" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Issue Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="issue_description" rows="4" required placeholder="Describe the issue in detail..."></textarea>
                        </div>
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

<?= $this->endSection() ?>
