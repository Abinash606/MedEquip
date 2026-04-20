<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center gap-3 mb-4 topbar">
    <a href="<?= site_url('technician/work-orders') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
    <h3 class="fw-bold mb-0">Work Order #WO-<?= esc($workOrder['id']) ?></h3>
</div>

<div class="content">
<div class="glass-card p-4">
    <div class="row g-4">
        <div class="col-md-8">
            <h5 class="fw-semibold mb-3"><?= esc($workOrder['title']) ?></h5>
            <div class="mb-3">
                <span class="text-muted small">Description</span>
                <p class="mb-0 mt-1"><?= nl2br(esc($workOrder['description'] ?? '—')) ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <table class="table table-sm service-table">
                <tr>
                    <th>Status</th>
                    <td>
                        <?php
                        $s = strtolower($workOrder['status'] ?? '');
                        $sClass = $s === 'open' ? 'success' : ($s === 'in_progress' ? 'primary' : 'secondary');
                        ?>
                        <span class="badge bg-<?= $sClass ?>"><?= esc(ucwords(str_replace('_',' ',$workOrder['status'] ?? ''))) ?></span>
                    </td>
                </tr>
                <tr>
                    <th>Priority</th>
                    <td>
                        <?php
                        $p = strtolower($workOrder['priority'] ?? '');
                        $pClass = $p === 'critical' ? 'danger' : ($p === 'high' ? 'warning' : ($p === 'normal' ? 'info' : 'secondary'));
                        ?>
                        <span class="badge bg-<?= $pClass ?>"><?= esc(ucfirst($workOrder['priority'] ?? '')) ?></span>
                    </td>
                </tr>
                <tr><th>Site</th><td><?= esc($workOrder['site_name'] ?? '—') ?></td></tr>
                <tr><th>Customer</th><td><?= esc($workOrder['customer_name'] ?? '—') ?></td></tr>
                <tr><th>Asset Tag</th><td><?= esc($workOrder['asset_tag'] ?? '—') ?></td></tr>
                <tr><th>Equipment</th><td><?= esc(($workOrder['make'] ?? '') . ' ' . ($workOrder['model'] ?? '')) ?></td></tr>
                <tr><th>Assigned To</th><td><?= esc($workOrder['assigned_to_name'] ?? 'Unassigned') ?></td></tr>
                <tr><th>Start Date</th><td><?= esc($workOrder['start_date'] ? date('M d, Y', strtotime($workOrder['start_date'])) : '—') ?></td></tr>
                <tr><th>End Date</th><td><?= esc($workOrder['end_date']   ? date('M d, Y', strtotime($workOrder['end_date']))   : '—') ?></td></tr>
            </table>
        </div>
    </div>
</div>
</div>

<?= $this->endSection() ?>
