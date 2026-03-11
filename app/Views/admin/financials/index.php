<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="mb-4 topbar">
<h3 class="fw-bold mb-4">Financial Performance</h3>
</div>
  <div class="content">
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="glass-card p-3 mb-3">
                    <h5 class="fw-bold mb-3">Revenue vs Costs</h5>
                    <!-- Replace static placeholder with a simple bar representation of revenue vs costs. -->
                    <div class="p-4  rounded">
                        <p class="small mb-2">Revenue vs Costs</p>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: 70%;">Revenue $70k</div>
                            <div class="progress-bar bg-danger" style="width: 30%;">Costs $30k</div>
                        </div>
                        <small class="text-muted d-block mt-2">This simplified bar compares total revenue against costs.</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="glass-card p-3">
                    <h5 class="fw-bold mb-3">Profitability by Customer</h5>
                    <table class="table table-sm">
                        <thead><tr><th>Customer</th><th>Revenue</th><th>Margin</th></tr></thead>
                        <tbody>
                            <tr><td>Mercy Hospital</td><td>$52,000</td><td class="text-success">22%</td></tr>
                            <tr><td>City Clinic</td><td>$14,500</td><td class="text-success">18%</td></tr>
                            <tr><td>Rural Health</td><td>$8,200</td><td class="text-warning">8%</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>
<?= $this->endSection() ?>
