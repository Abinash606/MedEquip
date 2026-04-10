<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <h3 class="fw-bold mb-0">Operational Summary</h3>
    <div class="text-muted small">Operational estimate only. This screen is not an invoice, accounting, or QuickBooks module.</div>
</div>

<div class="content">

<div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
    <strong>Scope note:</strong> This screen is limited to operational estimates based on inspections and work orders. It is intentionally not an invoice engine, labor code screen, rate card module, or QuickBooks integration.
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="glass-card p-3">
            <div class="text-muted small text-uppercase fw-bold mb-1">Estimated Service Value</div>
            <h2 class="fw-bold mb-0 text-success">$<?= number_format($estimatedServiceValue) ?></h2>
            <div class="small text-muted mt-1"><?= esc($totalInspections) ?> inspections completed</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3">
            <div class="text-muted small text-uppercase fw-bold mb-1">Estimated Service Load</div>
            <h2 class="fw-bold mb-0 text-danger">$<?= number_format($estimatedServiceLoad) ?></h2>
            <div class="small text-muted mt-1"><?= esc($totalWorkOrders) ?> work orders used for service load estimation</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3">
            <div class="text-muted small text-uppercase fw-bold mb-1">Estimated Net Capacity</div>
            <h2 class="fw-bold mb-0 <?= $estimatedNetCapacity >= 0 ? 'text-success' : 'text-danger' ?>">
                $<?= number_format(abs($estimatedNetCapacity)) ?>
            </h2>
            <div class="small text-muted mt-1">Operational margin: <?= esc($operationalMargin) ?>%</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3">
            <div class="text-muted small text-uppercase fw-bold mb-1">Compliance Rate</div>
            <h2 class="fw-bold mb-0 <?= $complianceRate >= 80 ? 'text-success' : ($complianceRate >= 60 ? 'text-warning' : 'text-danger') ?>">
                <?= esc($complianceRate) ?>%
            </h2>
            <div class="small text-muted mt-1"><?= esc($equipCount) ?> total equipment</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Service Value vs Service Load Chart -->
    <div class="col-lg-6">
        <div class="glass-card p-3">
            <h5 class="fw-bold mb-3">Service Value vs Service Load</h5>
            <?php
            $total = $estimatedServiceValue + $estimatedServiceLoad;
            $revPct = $total > 0 ? round(($estimatedServiceValue / $total) * 100) : 70;
            $costPct = 100 - $revPct;
            ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small text-muted">Service Value</span>
                    <span class="small fw-bold text-success">$<?= number_format($estimatedServiceValue) ?></span>
                </div>
                <div class="progress" style="height:20px;border-radius:10px;">
                    <div class="progress-bar bg-success" style="width:<?= $revPct ?>%;border-radius:10px 0 0 10px;">
                        <?= $revPct ?>%
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small text-muted">Service Load</span>
                    <span class="small fw-bold text-danger">$<?= number_format($estimatedServiceLoad) ?></span>
                </div>
                <div class="progress" style="height:20px;border-radius:10px;">
                    <div class="progress-bar bg-danger" style="width:<?= $costPct ?>%;border-radius:10px 0 0 10px;">
                        <?= $costPct ?>%
                    </div>
                </div>
            </div>
            <div class="pt-3 border-top mt-3">
                <div class="d-flex justify-content-between">
                    <span class="small text-muted">Estimated Net Capacity</span>
                    <span class="fw-bold <?= $estimatedNetCapacity >= 0 ? 'text-success' : 'text-danger' ?>">
                        $<?= number_format(abs($estimatedNetCapacity)) ?> (<?= $operationalMargin ?>% margin)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="col-lg-6">
        <div class="glass-card p-3">
            <h5 class="fw-bold mb-3">Monthly Inspection Trend</h5>
            <?php if (!empty($monthlyTrend)): ?>
                <canvas id="monthlyChart" height="160"></canvas>
            <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-chart-line fa-2x mb-2 opacity-25"></i>
                    <p class="mb-0 small">No monthly data available yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Per-Customer Profitability Table -->
<div class="glass-card p-3">
    <h5 class="fw-bold mb-3">Operational Mix by Customer</h5>
    <div class="table-responsive">
        <table class="table service-table align-middle" id="finTable" style="width:100%">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th class="text-center">Sites</th>
                    <th class="text-center">Equipment</th>
                    <th class="text-center">Inspections</th>
                    <th class="text-center">Work Orders</th>
                    <th class="text-end">Estimated Service Value</th>
                    <th class="text-end">Estimated Service Load</th>
                    <th class="text-end">Operational Margin</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customerStats)): ?>
                    <?php foreach ($customerStats as $cs):
                        $rev  = $cs['inspection_count'] * $serviceValuePerInspection;
                        $cost = $cs['wo_count'] * $serviceLoadPerWorkOrder;
                        $prof = $rev - $cost;
                        $mgn  = $rev > 0 ? round(($prof / $rev) * 100) : 0;
                    ?>
                    <tr>
                        <td class="fw-medium"><?= esc($cs['customer_name']) ?></td>
                        <td class="text-center"><?= esc($cs['site_count']) ?></td>
                        <td class="text-center"><?= esc($cs['equipment_count']) ?></td>
                        <td class="text-center"><?= esc($cs['inspection_count']) ?></td>
                        <td class="text-center"><?= esc($cs['wo_count']) ?></td>
                        <td class="text-end text-success">$<?= number_format($rev) ?></td>
                        <td class="text-end text-danger">$<?= number_format($cost) ?></td>
                        <td class="text-end">
                            <span class="badge <?= $mgn >= 20 ? 'bg-success' : ($mgn >= 0 ? 'bg-warning' : 'bg-danger') ?>">
                                <?= $mgn ?>%
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center text-muted py-3">No customer data found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="text-muted small mt-2 fst-italic">
        * Operational estimate only. Values are derived from internal dashboard assumptions and are not invoice totals or accounting exports.
    </div>
</div>

</div>

<?php if (!empty($monthlyTrend)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    var labels  = <?= json_encode(array_column($monthlyTrend, 'month')) ?>;
    var totals  = <?= json_encode(array_map('intval', array_column($monthlyTrend, 'total'))) ?>;
    var passed  = <?= json_encode(array_map('intval', array_column($monthlyTrend, 'passed'))) ?>;
    var failed  = <?= json_encode(array_map('intval', array_column($monthlyTrend, 'failed'))) ?>;

    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Pass', data: passed, backgroundColor: 'rgba(34,197,94,.7)', borderRadius: 6 },
                { label: 'Fail', data: failed, backgroundColor: 'rgba(239,68,68,.7)', borderRadius: 6 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { labels: { color: '#E9EDFF' } } },
            scales: {
                x: { stacked: true, ticks: { color: 'rgba(233,237,255,.55)' }, grid: { color: 'rgba(255,255,255,.06)' } },
                y: { stacked: true, ticks: { color: 'rgba(233,237,255,.55)' }, grid: { color: 'rgba(255,255,255,.06)' } }
            }
        }
    });
})();
</script>
<?php endif; ?>
<?= $this->endSection() ?>
