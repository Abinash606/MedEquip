<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <h3 class="fw-bold mb-0">Financial Performance</h3>
    <div class="text-muted small">Based on <?= esc($totalInspections) ?> inspections × $<?= esc($revenuePerInspection) ?>/inspection rate</div>
</div>

<div class="content">

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="glass-card p-3">
            <div class="text-muted small text-uppercase fw-bold mb-1">Est. Revenue</div>
            <h2 class="fw-bold mb-0 text-success">$<?= number_format($totalRevenue) ?></h2>
            <div class="small text-muted mt-1"><?= esc($totalInspections) ?> inspections completed</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3">
            <div class="text-muted small text-uppercase fw-bold mb-1">Est. Costs</div>
            <h2 class="fw-bold mb-0 text-danger">$<?= number_format($totalCosts) ?></h2>
            <div class="small text-muted mt-1"><?= esc($totalWorkOrders) ?> work orders @ $<?= esc($costPerWorkOrder) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3">
            <div class="text-muted small text-uppercase fw-bold mb-1">Est. Profit</div>
            <h2 class="fw-bold mb-0 <?= $totalProfit >= 0 ? 'text-success' : 'text-danger' ?>">
                $<?= number_format(abs($totalProfit)) ?>
            </h2>
            <div class="small text-muted mt-1">Margin: <?= esc($margin) ?>%</div>
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
    <!-- Revenue vs Costs Chart -->
    <div class="col-lg-6">
        <div class="glass-card p-3">
            <h5 class="fw-bold mb-3">Revenue vs Costs</h5>
            <?php
            $total = $totalRevenue + $totalCosts;
            $revPct = $total > 0 ? round(($totalRevenue / $total) * 100) : 70;
            $costPct = 100 - $revPct;
            ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small text-muted">Revenue</span>
                    <span class="small fw-bold text-success">$<?= number_format($totalRevenue) ?></span>
                </div>
                <div class="progress" style="height:20px;border-radius:10px;">
                    <div class="progress-bar bg-success" style="width:<?= $revPct ?>%;border-radius:10px 0 0 10px;">
                        <?= $revPct ?>%
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small text-muted">Costs</span>
                    <span class="small fw-bold text-danger">$<?= number_format($totalCosts) ?></span>
                </div>
                <div class="progress" style="height:20px;border-radius:10px;">
                    <div class="progress-bar bg-danger" style="width:<?= $costPct ?>%;border-radius:10px 0 0 10px;">
                        <?= $costPct ?>%
                    </div>
                </div>
            </div>
            <div class="pt-3 border-top mt-3">
                <div class="d-flex justify-content-between">
                    <span class="small text-muted">Net Profit</span>
                    <span class="fw-bold <?= $totalProfit >= 0 ? 'text-success' : 'text-danger' ?>">
                        $<?= number_format(abs($totalProfit)) ?> (<?= $margin ?>% margin)
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
    <h5 class="fw-bold mb-3">Profitability by Customer</h5>
    <div class="table-responsive">
        <table class="table service-table align-middle" id="finTable" style="width:100%">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th class="text-center">Sites</th>
                    <th class="text-center">Equipment</th>
                    <th class="text-center">Inspections</th>
                    <th class="text-center">Work Orders</th>
                    <th class="text-end">Est. Revenue</th>
                    <th class="text-end">Est. Costs</th>
                    <th class="text-end">Margin</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customerStats)): ?>
                    <?php foreach ($customerStats as $cs):
                        $rev  = $cs['inspection_count'] * $revenuePerInspection;
                        $cost = $cs['wo_count'] * $costPerWorkOrder;
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
        * Revenue estimated at $<?= esc($revenuePerInspection) ?>/inspection, Costs at $<?= esc($costPerWorkOrder) ?>/work order.
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
