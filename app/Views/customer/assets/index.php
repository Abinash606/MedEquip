<?= $this->extend('layouts/customer-header') ?>

<?= $this->section('content') ?>
<!-- Assets section -->
    <section id="inventory" class="view-section active">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-0">Assets</h4>
                <div class="text-muted small">Search, sort, and export your asset list.</div>
            </div>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEquipmentModal"><i class="fa-solid fa-plus me-2"></i> Add Asset</button>
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
                        <tr>
                            <td>EQ-001</td>
                            <td>SN-12345</td>
                            <td>Philips</td>
                            <td>Ingenia</td>
                            <td>MRI Scanner</td>
                        </tr>
                        <tr>
                            <td>EQ-002</td>
                            <td>SN-67890</td>
                            <td>GE</td>
                            <td>Optima</td>
                            <td>CT Scanner</td>
                        </tr>
                        <tr>
                            <td>EQ-003</td>
                            <td>SN-24680</td>
                            <td>Siemens</td>
                            <td>Acuson</td>
                            <td>Ultrasound</td>
                        </tr>
                        <tr>
                            <td>EQ-004</td>
                            <td>SN-13579</td>
                            <td>Mindray</td>
                            <td>iMEC</td>
                            <td>Patient Monitor</td>
                        </tr>
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
                <form>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Equipment Name</label>
                        <input type="text" class="form-control bg-light border-0" placeholder="Enter equipment name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Department</label>
                        <input type="text" class="form-control bg-light border-0" placeholder="Enter department">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Location</label>
                        <input type="text" class="form-control bg-light border-0" placeholder="Enter location">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Model / Serial #</label>
                        <input type="text" class="form-control bg-light border-0" placeholder="Model or serial number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Last Inspection Date</label>
                        <input type="date" class="form-control bg-light border-0">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Status</label>
                        <select class="form-select bg-light border-0">
                            <option>Operational</option>
                            <option>Needs Attention</option>
                            <option>Out of Service</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-wow w-100">Save Equipment</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>