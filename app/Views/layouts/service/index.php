<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>
<!-- Service History view -->
    <section id="serviceHistory" class="view-section active">
        <div class="row g-4">
            <div class="col-12">
                <div class="glass-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Open Work Orders</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#workOrderModal"><i class="fa-solid fa-plus me-2"></i> Add Work Order</button>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold">Repair Autoclave</div>
                                <div class="text-muted small">#WO-1022 • Created: 10/20/2025</div>
                            </div>
                            <span class="badge bg-warning text-dark">In Progress</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-bold">Replace Battery Pack</div>
                                <div class="text-muted small">#WO-1023 • Created: 10/18/2025</div>
                            </div>
                            <span class="badge bg-secondary text-dark">Scheduled</span>
                        </li>
                    </ul>
                </div>
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">Recent Service History</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold">Maintenance Completed</div>
                                <div class="text-muted small">MRI Scanner – Radiology</div>
                            </div>
                            <span class="small text-muted">Oct&nbsp;10,&nbsp;2025</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-bold">Service Request Logged</div>
                                <div class="text-muted small">Broken Display – Bed&nbsp;4 Monitor</div>
                            </div>
                            <span class="small text-muted">Oct&nbsp;01,&nbsp;2025</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>