<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<!-- Service History view -->
<section id="serviceHistory" class="view-section active">
    <div class="row g-4">
        <div class="col-12">
            <!-- Open Work Orders -->
            <div class="glass-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Open Work Orders</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addWorkOrderModal">
                        <i class="fa-solid fa-plus me-2"></i> Add Work Order
                    </button>
                </div>

                <?php if (!empty($open_work_orders)): ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($open_work_orders as $index => $wo): ?>
                            <li
                                class="d-flex justify-content-between align-items-center py-2 <?= $index < count($open_work_orders) - 1 ? 'border-bottom' : '' ?>">
                                <div>
                                    <div class="fw-bold"><?= esc($wo['title']) ?></div>

                                    <div class="text-muted small">
                                        #WO-<?= str_pad($wo['id'], 4, '0', STR_PAD_LEFT) ?>

                                        <?php if (!empty($wo['site_name'])): ?>
                                            • <?= esc($wo['site_name']) ?>
                                        <?php endif; ?>

                                        <?php if (!empty($wo['created_at'])): ?>
                                            • Created: <?= date('m/d/Y', strtotime($wo['created_at'])) ?>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($wo['asset_tag']) || !empty($wo['make']) || !empty($wo['model'])): ?>
                                        <div class="text-muted small mt-1">
                                            <?php if (!empty($wo['asset_tag'])): ?>
                                                <?= esc($wo['asset_tag']) ?>
                                            <?php endif; ?>

                                            <?php if (!empty($wo['make']) || !empty($wo['model'])): ?>
                                                <?= !empty($wo['asset_tag']) ? ' - ' : '' ?>
                                                <?= esc(trim(($wo['make'] ?? '') . ' ' . ($wo['model'] ?? ''))) ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="text-end">
                                    <span class="badge bg-info text-dark">Open</span>
                                    <br>

                                    <?php if (($wo['priority'] ?? '') === 'critical'): ?>
                                        <span class="badge bg-danger mt-1">Critical</span>
                                    <?php elseif (($wo['priority'] ?? '') === 'high'): ?>
                                        <span class="badge bg-danger mt-1">High</span>
                                    <?php elseif (($wo['priority'] ?? '') === 'medium'): ?>
                                        <span class="badge bg-warning mt-1">Medium</span>
                                    <?php elseif (($wo['priority'] ?? '') === 'low'): ?>
                                        <span class="badge bg-success mt-1">Low</span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-light text-dark mt-1"><?= esc(ucwords($wo['priority'] ?? 'N/A')) ?></span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-inbox fa-3x mb-3"></i>
                        <p>No open work orders available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Service History -->
            <div class="glass-card">
                <h5 class="fw-bold mb-3">Recent Service History</h5>

                <?php if (!empty($recent_history)): ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($recent_history as $index => $history): ?>
                            <li
                                class="d-flex justify-content-between align-items-center py-2 <?= $index < count($recent_history) - 1 ? 'border-bottom' : '' ?>">
                                <div>
                                    <div class="fw-bold"><?= esc($history['title']) ?></div>
                                    <div class="text-muted small">
                                        <?= esc($history['site_name'] ?? 'N/A') ?>
                                        <?php if (!empty($history['make']) && !empty($history['model'])): ?>
                                            – <?= esc($history['make']) ?> <?= esc($history['model']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success mb-1">Completed</span><br>
                                    <span class="small text-muted">
                                        <?php
                                        $historyDate = !empty($history['completed_at'])
                                            ? $history['completed_at']
                                            : ($history['updated_at'] ?? null);
                                        ?>
                                        <?= !empty($historyDate) ? date('M d, Y', strtotime($historyDate)) : 'N/A' ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-history fa-3x mb-3"></i>
                        <p>No service history available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Work Order Modal -->
<div class="modal fade" id="addWorkOrderModal" tabindex="-1" aria-labelledby="addWorkOrderModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <form id="workOrderForm" method="post" action="<?= site_url('technician/service-history/create') ?>">
                <?= csrf_field() ?>
                <input type="hidden" id="workorder-id" name="id">

                <div class="modal-header border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="addWorkOrderModalLabel">Add Work Order</h5>
                        <p class="text-muted small mb-0">Create a new work order for the selected site and equipment.
                        </p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="workorder-site" class="form-label fw-semibold">
                                Site <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="workorder-site" name="site_id" required>
                                <option value="">-- Select Site --</option>
                                <?php if (!empty($sites)): ?>
                                    <?php foreach ($sites as $site): ?>
                                        <option value="<?= $site['id'] ?>"><?= esc($site['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="workorder-equipment" class="form-label fw-semibold">Equipment</label>
                            <select class="form-select" id="workorder-equipment" name="equipment_id">
                                <option value="">-- Select Equipment --</option>
                                <?php if (!empty($equipment)): ?>
                                    <?php foreach ($equipment as $eq): ?>
                                        <option value="<?= $eq['id'] ?>">
                                            <?= esc($eq['asset_tag']) ?> - <?= esc($eq['make']) ?> <?= esc($eq['model']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label for="workorder-title" class="form-label fw-semibold">
                                Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="workorder-title" name="title"
                                placeholder="Enter work order title" required>
                        </div>

                        <div class="col-md-4">
                            <label for="workorder-sn" class="form-label fw-semibold">
                                S/N <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="workorder-sn" name="sn"
                                placeholder="Enter serial number" required>
                        </div>

                        <div class="col-md-6">
                            <label for="workorder-status" class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="workorder-status" name="status" required>
                                <option value="">-- Select Status --</option>
                                <option value="open" selected>Open</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="workorder-priority" class="form-label fw-semibold">
                                Priority <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="workorder-priority" name="priority" required>
                                <option value="">-- Select Priority --</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Assigned To</label>
                            <input type="text" class="form-control bg-light"
                                value="<?= esc($current_user['name'] ?? 'You') ?>" readonly>
                            <small class="text-muted">This work order will be assigned to you.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="workorder-start-date" class="form-label fw-semibold">Start Date</label>
                            <input type="date" class="form-control" id="workorder-start-date" name="start_date">
                        </div>

                        <div class="col-md-6">
                            <label for="workorder-end-date" class="form-label fw-semibold">End Date</label>
                            <input type="date" class="form-control" id="workorder-end-date" name="end_date">
                        </div>

                        <div class="col-12">
                            <label for="workorder-description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="workorder-description" name="description" rows="4"
                                placeholder="Enter work order description"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="workOrderSubmitBtn">Save Work Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    <?php if (session()->getFlashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= session()->getFlashdata('success') ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#28a745'
        });
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?= session()->getFlashdata('error') ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
    <?php endif; ?>

    document.getElementById('workOrderForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('workOrderSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Saving...';
    });
</script>

<?= $this->endSection() ?>