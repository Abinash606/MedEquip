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
                                        #WO-<?= str_pad($wo['id'], 4, '0', STR_PAD_LEFT) ?> •
                                        <?= esc($wo['site_name']) ?> •
                                        Created: <?= date('m/d/Y', strtotime($wo['created_at'])) ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <?php if ($wo['status'] === 'in progress'): ?>
                                        <span class="badge bg-warning text-dark">In Progress</span>
                                    <?php elseif ($wo['status'] === 'open'): ?>
                                        <span class="badge bg-info text-dark">Open</span>
                                    <?php elseif ($wo['status'] === 'on hold'): ?>
                                        <span class="badge bg-secondary">On Hold</span>
                                    <?php endif; ?>
                                    <br>
                                    <?php if ($wo['priority'] === 'critical'): ?>
                                        <span class="badge bg-danger mt-1">Critical</span>
                                    <?php elseif ($wo['priority'] === 'high'): ?>
                                        <span class="badge bg-danger mt-1">High</span>
                                    <?php elseif ($wo['priority'] === 'medium'): ?>
                                        <span class="badge bg-warning mt-1">Medium</span>
                                    <?php else: ?>
                                        <span class="badge bg-success mt-1">Low</span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-inbox fa-3x mb-3"></i>
                        <p>No open work orders at the moment.</p>
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
                                        <?= esc($history['site_name']) ?>
                                        <?php if (!empty($history['make']) && !empty($history['model'])): ?>
                                            – <?= esc($history['make']) ?> <?= esc($history['model']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="small text-muted">
                                    <?= date('M d, Y', strtotime($history['completed_at'] ?? $history['updated_at'])) ?>
                                </span>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="workOrderForm" method="post" action="<?= site_url('technician/service-history/create') ?>">
                <?= csrf_field() ?>
                <input type="hidden" id="workorder-id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title" id="addWorkOrderModalLabel">Add Work Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Site Selection - REQUIRED -->
                        <div class="col-12">
                            <label for="workorder-site" class="form-label">Site <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="workorder-site" name="site_id" required>
                                <option value="">-- Select Site --</option>
                                <?php if (!empty($sites)): ?>
                                    <?php foreach ($sites as $site): ?>
                                        <option value="<?= $site['id'] ?>"><?= esc($site['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="workorder-title" class="form-label">Title <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="workorder-title" name="title" required>
                        </div>

                        <div class="col-12">
                            <label for="workorder-sn" class="form-label">S/N <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="workorder-sn" name="sn" required>
                        </div>

                        <div class="col-md-12">
                            <label for="workorder-equipment" class="form-label">Equipment</label>
                            <select class="form-select" id="workorder-equipment" name="equipment_id">
                                <option value="">-- Select Equipment --</option>
                                <?php if (!empty($equipment)): ?>
                                    <?php foreach ($equipment as $eq): ?>
                                        <option value="<?= $eq['id'] ?>"><?= esc($eq['asset_tag']) ?> - <?= esc($eq['make']) ?>
                                            <?= esc($eq['model']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="workorder-status" class="form-label">Status <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="workorder-status" name="status" required>
                                <option value="">-- Select Status --</option>
                                <option value="open">Open</option>
                                <option value="in progress">In Progress</option>
                                <option value="on hold">On Hold</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="workorder-priority" class="form-label">Priority <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="workorder-priority" name="priority" required>
                                <option value="">-- Select Priority --</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>

                        <!-- Display assigned technician (read-only) -->
                        <div class="col-12">
                            <label class="form-label">Assigned To</label>
                            <input type="text" class="form-control" value="<?= esc($current_user['name'] ?? 'You') ?>"
                                readonly>
                            <small class="text-muted">This work order will be assigned to you</small>
                        </div>

                        <div class="col-md-6">
                            <label for="workorder-start-date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="workorder-start-date" name="start_date">
                        </div>

                        <div class="col-md-6">
                            <label for="workorder-end-date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="workorder-end-date" name="end_date">
                        </div>

                        <div class="col-12">
                            <label for="workorder-description" class="form-label">Description</label>
                            <textarea class="form-control" id="workorder-description" name="description"
                                rows="4"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="workOrderSubmitBtn">Save Work Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 Notifications Script -->
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
</script>

<?= $this->endSection() ?>