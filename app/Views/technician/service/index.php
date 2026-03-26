<?= $this->extend('layouts/technician-header') ?>
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

<!-- Service History view -->
 <div class="content">
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

                                <div class="text-end d-flex flex-wrap align-items-end gap-1">
                                    <?php
                                        $wostatus = strtolower($wo['status'] ?? 'open');
                                        $statusBadge = $wostatus === 'closed' ? 'bg-success' : ($wostatus === 'in_progress' ? 'bg-warning' : 'bg-info text-dark');
                                        $statusLabel = ucwords(str_replace('_',' ',$wostatus));
                                    ?>
                                    <span class="badge <?= $statusBadge ?>"><?= esc($statusLabel) ?></span>
                                    <?php if (($wo['priority'] ?? '') === 'critical'): ?>
                                        <span class="badge bg-danger">Critical</span>
                                    <?php elseif (($wo['priority'] ?? '') === 'high'): ?>
                                        <span class="badge bg-danger">High</span>
                                    <?php elseif (($wo['priority'] ?? '') === 'medium'): ?>
                                        <span class="badge bg-warning">Medium</span>
                                    <?php elseif (($wo['priority'] ?? '') === 'low'): ?>
                                        <span class="badge bg-success">Low</span>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-primary mt-1 update-wo-btn"
                                        data-id="<?= esc($wo['id']) ?>"
                                        data-title="<?= esc($wo['title'] ?? '', 'attr') ?>"
                                        data-status="<?= esc($wostatus, 'attr') ?>"
                                        data-priority="<?= esc($wo['priority'] ?? 'medium', 'attr') ?>"
                                        data-description="<?= esc($wo['description'] ?? '', 'attr') ?>">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Update
                                    </button>
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
</div>
<!-- Update Work Order Status Modal -->
<div class="modal fade" id="updateWOModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="updateWOForm">
                <div class="modal-header" style="background:linear-gradient(135deg,rgba(124,58,237,.9),rgba(34,211,238,.8));border-radius:12px 12px 0 0;">
                    <h5 class="modal-title text-white fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Update Work Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" id="updateWoId" name="wo_id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Work Order Title</label>
                        <input type="text" class="form-control" id="updateWoTitle" name="title" readonly
                            style="background:rgba(255,255,255,.05);color:inherit;border:1px solid rgba(255,255,255,.15);">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="updateWoStatus" name="status" required>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="closed">Closed / Completed</option>
                            <option value="on_hold">On Hold</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Priority</label>
                        <select class="form-select" id="updateWoPriority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes / Description</label>
                        <textarea class="form-control" id="updateWoDescription" name="description" rows="3"
                            placeholder="Add update notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Open update modal and populate fields
$(document).on('click', '.update-wo-btn', function() {
    var btn = $(this);
    $('#updateWoId').val(btn.data('id'));
    $('#updateWoTitle').val(btn.data('title'));
    $('#updateWoStatus').val(btn.data('status') || 'open');
    $('#updateWoPriority').val(btn.data('priority') || 'medium');
    $('#updateWoDescription').val(btn.data('description') || '');
    $('#updateWOModal').modal('show');
});

// Submit update via AJAX
$('#updateWOForm').on('submit', function(e) {
    e.preventDefault();
    var woId = $('#updateWoId').val();
    var data = $(this).serialize();

    $.ajax({
        url: '<?= site_url('technician/work-orders/update') ?>/' + woId,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(res) {
            $('#updateWOModal').modal('hide');
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'Updated!', text: 'Work order status has been updated.', timer: 1800, showConfirmButton: false })
                    .then(function() { location.reload(); });
            } else {
                alert('Work order updated successfully.');
                location.reload();
            }
        },
        error: function() {
            alert('Failed to update work order. Please try again.');
        }
    });
});
</script>

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