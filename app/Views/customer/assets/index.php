<?= $this->extend('layouts/customer-header') ?>

<?= $this->section('content') ?>
<!-- Assets section -->
<section id="inventory" class="view-section active">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Assets</h4>
            <div class="text-muted small">Search, sort, and export your asset list.</div>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEquipmentModal"><i
                class="fa-solid fa-plus me-2"></i> Add Asset</button>
    </div>

    <div class="glass-card">
        <div class="table-responsive">
            <table id="assetsTable" class="table table-hover align-middle" style="width:100%">
                <thead class="bg-light">
                    <tr>
                        <th>Asset Tag</th>
                        <th>Serial Number</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Device Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipment)): ?>
                        <?php foreach ($equipment as $eq): ?>
                            <tr>
                                <td><?= esc($eq['asset_tag']) ?></td>
                                <td><?= esc($eq['serial_number']) ?></td>
                                <td><?= esc($eq['make']) ?></td>
                                <td><?= esc($eq['model']) ?></td>
                                <td><?= esc($eq['device_type']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No assets found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Add Equipment Modal -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add New Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <form id="addEquipmentForm" method="post" action="<?= base_url('customer/assets/store') ?>" novalidate>
                    <?= csrf_field() ?>

                    <!-- hidden site id -->
                    <input type="hidden" name="site_id" value="<?= $sites[0]['id'] ?? '' ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Asset Tag </label>
                        <input type="text" name="asset_tag" id="asset_tag" class="form-control bg-light border-0"
                            placeholder="Leave blank for auto-generate.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Serial Number <span
                                class="text-danger">*</span></label>
                        <input type="text" name="serial_number" id="serial_number"
                            class="form-control bg-light border-0" placeholder="Enter serial number" required>
                        <div class="invalid-feedback">
                            Serial number is required.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Make <span
                                class="text-danger">*</span></label>
                        <input type="text" name="make" id="make" class="form-control bg-light border-0"
                            placeholder="Enter manufacturer" required minlength="2">
                        <div class="invalid-feedback">
                            Make is required (minimum 2 characters).
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Model <span
                                class="text-danger">*</span></label>
                        <input type="text" name="model" id="model" class="form-control bg-light border-0"
                            placeholder="Enter model" required minlength="2">
                        <div class="invalid-feedback">
                            Model is required (minimum 2 characters).
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Device Type <span
                                class="text-danger">*</span></label>
                        <input type="text" name="device_type" id="device_type" class="form-control bg-light border-0"
                            placeholder="Enter device type" required>
                        <div class="invalid-feedback">
                            Device type is required.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Department</label>
                        <input type="text" name="department" id="department" class="form-control bg-light border-0"
                            placeholder="Enter department">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Location</label>
                        <input type="text" name="location" id="location" class="form-control bg-light border-0"
                            placeholder="Room Number or Location">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Status <span
                                class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select bg-light border-0" required>
                            <option value="">Select status</option>
                            <option value="Operational">Operational</option>
                            <option value="Needs Attention">Needs Attention</option>
                            <option value="Out of Service">Out of Service</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a status.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-wow w-100">Save Equipment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('addEquipmentForm');
        const modal = document.getElementById('addEquipmentModal');
        const bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);

        // Frontend validation with Bootstrap
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Check form validity
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            // Additional custom validation
            const serialNumber = document.getElementById('serial_number').value.trim();
            const make = document.getElementById('make').value.trim();
            const model = document.getElementById('model').value.trim();
            const deviceType = document.getElementById('device_type').value.trim();
            const status = document.getElementById('status').value;

            if (!serialNumber || !make || !model || !deviceType || !status) {
                form.classList.add('was-validated');
                return;
            }

            // All validation passed, submit the form
            form.classList.remove('was-validated');
            form.submit();
        });

        // Show simple success message from session
        <?php if (session()->getFlashdata('success')): ?>
            bsModal.hide();
            Swal.fire({
                title: 'Success!',
                text: '<?= session()->getFlashdata('success') ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        <?php endif; ?>

        // Show simple error message from session
        <?php if (session()->getFlashdata('error')): ?>
            bsModal.show();
            Swal.fire({
                title: 'Error!',
                text: '<?= session()->getFlashdata('error') ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        // Reset form when modal closes
        modal.addEventListener('hidden.bs.modal', function() {
            form.reset();
            form.classList.remove('was-validated');
        });
    });
</script>

<?= $this->endSection() ?>