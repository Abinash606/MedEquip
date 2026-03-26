<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Dashboard</h3>
        <p class="text-muted small mb-0">Welcome back, <?= esc(session('username') ?? 'Technician') ?></p>
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
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentInspections)): ?>
                    <?php foreach ($recentInspections as $insp): ?>
                    <?php
                        $st = strtolower($insp['status'] ?? '');
                        $badge = $st === 'pass' ? 'bg-success' : ($st === 'fail' ? 'bg-danger' : 'bg-warning');
                    ?>
                    <tr>
                        <td class="fw-medium small"><?= esc($insp['group_id'] ? substr($insp['group_id'], 0, 18) : '#INS-' . $insp['id']) ?></td>
                        <td><?= esc($insp['site_name'] ?? '—') ?></td>
                        <td><?= esc($insp['customer_name'] ?? '—') ?></td>
                        <td><?= esc($insp['asset_tag'] ?? ($insp['make'] ?? '') . ' ' . ($insp['model'] ?? '')) ?></td>
                        <td class="text-muted small"><?= esc($insp['completed_at'] ? substr($insp['completed_at'], 0, 10) : '—') ?></td>
                        <td><span class="badge <?= $badge ?>"><?= esc(ucfirst($insp['status'])) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No recent inspections found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
<?= $this->endSection() ?>
