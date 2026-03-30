<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <h3 class="fw-bold mb-0">Operational Overview</h3>
    <div class="search" style="width:40%;">
        <i class="bi bi-search"></i>
        <input type="search" class="form-control" placeholder="Search for customers, assets, parts...">
    </div>
    <button class="btn btn-primary shadow-sm btn-new" data-bs-toggle="modal" data-bs-target="#newWorkOrderModal">
        <i class="fa-solid fa-plus me-2"></i> New Request
    </button>
</div>

<div class="content">

<!-- Row 1: KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <a href="<?= site_url('admin/inspection-reports') ?>" class="text-decoration-none">
        <div class="glass-card stat-card metric-card d-flex justify-content-between p-3" style="cursor:pointer;">
            <div>
                <div class="text-muted small fw-bold uppercase">All Customer Inspection Status</div>
                <div class="fs-2 fw-bold text-white"><?= esc($totalInspections) ?></div>
                <div class="small text-danger g-text">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= esc($criticalWO) ?> Critical WO</span>
                </div>
            </div>
            <div class="fs-1 text-primary opacity-25"><i class="fa-solid fa-clipboard-list"></i></div>
        </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="glass-card stat-card metric-card d-flex justify-content-between p-3">
            <div>
                <div class="text-muted small fw-bold uppercase">All Devices or Equipment Status</div>
                <div class="fs-2 fw-bold text-white"><?= esc($equipPct) ?>%</div>
                <div class="small text-success g-text">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span>Operational Rate</span>
                </div>
            </div>
            <div class="fs-1 text-info opacity-25"><i class="fa-solid fa-user-clock"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="<?= site_url('admin/work-orders') ?>" class="text-decoration-none">
        <div class="glass-card stat-card metric-card d-flex justify-content-between p-3" style="cursor:pointer;">
            <div>
                <div class="text-muted small fw-bold uppercase">Open Work Orders</div>
                <div class="fs-2 fw-bold text-white"><?= esc($pendingWO) ?></div>
                <div class="small text-muted"><?= esc($pendingWO) ?> awaiting action</div>
            </div>
            <div class="fs-1 text-warning opacity-25"><i class="fa-solid fa-file-invoice-dollar"></i></div>
        </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="glass-card stat-card metric-card d-flex justify-content-between p-3">
            <div>
                <div class="text-muted small fw-bold uppercase">Compliance Score</div>
                <div class="fs-2 fw-bold text-white"><?= esc($complianceScore) ?>%</div>
                <div class="small text-success g-text">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?= $complianceScore >= 90 ? 'Audit Ready' : ($complianceScore >= 75 ? 'Good Standing' : 'Needs Attention') ?></span>
                </div>
            </div>
            <div class="fs-1 text-success opacity-25"><i class="fa fa-shield"></i></div>
        </div>
    </div>
</div>

<!-- Row 2: Overview Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-4">
        <a href="<?= site_url('admin/work-orders') ?>" class="text-decoration-none">
        <div class="glass-card stat-card d-flex justify-content-between align-items-center p-3" style="cursor:pointer;">
            <div>
                <div class="text-muted small fw-bold uppercase">Work Order Overview</div>
                <div class="fs-2 fw-bold text-white"><?= esc($woTotal) ?></div>
                <div class="small text-muted d-flex gap-2">
                    <span class="fw-bold text-success"><?= esc($woCompleted) ?></span> Completed •
                    <span class="fw-bold text-warning"><?= esc($woInProgress) ?></span> In Progress
                </div>
            </div>
            <div class="fs-1 text-primary opacity-25"><i class="fa-solid fa-clipboard-check"></i></div>
        </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="<?= site_url('admin/inspection-reports') ?>" class="text-decoration-none">
        <div class="glass-card stat-card d-flex justify-content-between align-items-center p-3" style="cursor:pointer;">
            <div>
                <div class="text-muted small fw-bold uppercase">Inspection Overview</div>
                <div class="fs-2 fw-bold text-white"><?= esc($totalInspections) ?></div>
                <div class="small text-muted d-flex gap-2">
                    <span class="fw-bold text-success"><?= esc($inspCompleted) ?></span> Completed •
                    <span class="fw-bold text-warning"><?= esc($inspInProgress) ?></span> Pending
                </div>
            </div>
            <div class="fs-1 text-info opacity-25"><i class="fas fa-clipboard"></i></div>
        </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="glass-card stat-card d-flex justify-content-between align-items-center p-3">
            <div>
                <div class="text-muted small fw-bold uppercase">Inventory Overview</div>
                <div class="fs-2 fw-bold text-white"><?= esc($invTotal) ?></div>
                <div class="small text-muted d-flex gap-2">
                    <span class="fw-bold text-warning"><?= esc($invLowStock) ?></span> Low Stock •
                    <span class="fw-bold text-danger"><?= esc($invOutOfStock) ?></span> Out of Stock
                </div>
            </div>
            <div class="fs-1 text-warning opacity-25"><i class="fa-solid fa-box-open"></i></div>
        </div>
    </div>
</div>

<!-- Row 3: Live Feed + Technician Workload -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="glass-card p-1">
            <h6 class="fw-bold mb-3 ps-3 pt-3">Live Service Feed</h6>
            <table class="table table-hover align-middle service-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Equipment</th>
                        <th>Tech</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($liveServiceFeed)): ?>
                        <?php foreach ($liveServiceFeed as $wo): ?>
                        <?php
                            $statusClass = match(strtolower($wo['status'] ?? '')) {
                                'in_progress' => 'bg-info text-dark',
                                'closed', 'completed' => 'bg-success',
                                'open' => 'bg-primary',
                                default => 'bg-secondary'
                            };
                            $statusLabel = match(strtolower($wo['status'] ?? '')) {
                                'in_progress' => 'In Progress',
                                'closed', 'completed' => 'Completed',
                                'open' => 'New',
                                default => ucfirst($wo['status'] ?? '')
                            };
                            if (strtolower($wo['priority'] ?? '') === 'critical') {
                                $statusClass = 'bg-danger';
                                $statusLabel = 'Emergency';
                            }
                            $equip = trim(($wo['make'] ?? '') . ' ' . ($wo['model'] ?? ''));
                            if (empty($equip)) $equip = $wo['device_type'] ?? $wo['title'] ?? '—';
                            $techName = $wo['tech_name'] ?? '-- Unassigned --';
                            $initials  = $techName !== '-- Unassigned --'
                                ? strtoupper(substr($techName, 0, 1) . (strpos($techName,' ') ? substr($techName, strpos($techName,' ')+1, 1) : ''))
                                : '';
                            $timeAgo = '';
                            if (!empty($wo['updated_at'])) {
                                $diff = time() - strtotime($wo['updated_at']);
                                if ($diff < 3600)      $timeAgo = round($diff/60) . 'm ago';
                                elseif ($diff < 86400) $timeAgo = round($diff/3600) . 'h ' . round(($diff%3600)/60) . 'm';
                                else                   $timeAgo = date('M j', strtotime($wo['updated_at']));
                            }
                        ?>
                        <tr>
                            <td><span class="t-pill">#WO-<?= str_pad($wo['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                            <td><?= esc($wo['customer_name'] ?? $wo['site_name'] ?? '—') ?></td>
                            <td><?= esc($equip) ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($initials): ?>
                                        <div class="avatar-circle"><?= esc($initials) ?></div>
                                    <?php endif; ?>
                                    <span class="<?= $techName === '-- Unassigned --' ? 'text-muted fst-italic' : '' ?>">
                                        <?= esc($techName) ?>
                                    </span>
                                </div>
                            </td>
                            <td><span class="badge <?= $statusClass ?>"><?= esc($statusLabel) ?></span></td>
                            <td class="text-muted small"><?= esc($timeAgo) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No work orders found. <a href="<?= site_url('admin/sites') ?>">Create one</a>.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="glass-card p-3 workload-card">
            <h5 class="text-muted fw-bold uppercase mb-1">Technician Workload</h5>
            <p class="work-sub mb-4">Overview of work order distribution across statuses.</p>

            <?php if (!empty($techWorkload)): ?>
                <?php foreach ($techWorkload as $tech): ?>
                <?php
                    $pctCompleted  = $maxWO > 0 ? round(($tech['completed']  / $maxWO) * 100) : 0;
                    $pctInProgress = $maxWO > 0 ? round(($tech['in_progress'] / $maxWO) * 100) : 0;
                    $pctNew        = $maxWO > 0 ? round(($tech['new_wo']      / $maxWO) * 100) : 0;
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold"><?= esc($tech['full_name'] ?? 'Unassigned') ?></span>
                        <span class="small text-muted"><?= esc($tech['total_wo']) ?> WOs</span>
                    </div>
                    <div class="progress mb-1" style="height:6px;">
                        <div class="progress-bar bg-success" style="width:<?= $pctCompleted ?>%" title="Completed: <?= $tech['completed'] ?>"></div>
                        <div class="progress-bar bg-warning" style="width:<?= $pctInProgress ?>%" title="In Progress: <?= $tech['in_progress'] ?>"></div>
                        <div class="progress-bar bg-danger"  style="width:<?= $pctNew ?>%" title="New: <?= $tech['new_wo'] ?>"></div>
                    </div>
                    <div class="d-flex gap-3 text-muted" style="font-size:10px;">
                        <span><span class="text-success fw-bold"><?= $tech['completed'] ?></span> Done</span>
                        <span><span class="text-warning fw-bold"><?= $tech['in_progress'] ?></span> Active</span>
                        <span><span class="text-danger fw-bold"><?= $tech['new_wo'] ?></span> New</span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php
                // Show static labels if no technicians yet
                foreach ([['label'=>'Completed','pct'=>60,'cls'=>'bg-success'],['label'=>'In Progress','pct'=>30,'cls'=>'bg-warning'],['label'=>'New','pct'=>10,'cls'=>'bg-danger']] as $row):
                ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="small"><?= $row['label'] ?></span>
                    <div class="progress flex-grow-1 mx-3" style="height:6px;">
                        <div class="progress-bar <?= $row['cls'] ?>" style="width:<?= $row['pct'] ?>%"></div>
                    </div>
                    <span class="small text-muted"><?= $row['pct'] ?>%</span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</div><!-- .content -->

<!-- New Work Order Quick Modal -->
<div class="modal fade" id="newWorkOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= site_url('admin/work-orders/create') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-plus me-2"></i>New Work Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required placeholder="e.g. MRI Scanner Maintenance">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Site <span class="text-danger">*</span></label>
                        <select class="form-select" name="site_id" required>
                            <option value="">-- Select Site --</option>
                            <?php
                            $siteModel = new \App\Models\SiteModel();
                            $allSites  = $siteModel->where('company_id', session('company_id'))->findAll();
                            foreach ($allSites as $s):
                            ?>
                            <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select class="form-select" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Describe the work required..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
