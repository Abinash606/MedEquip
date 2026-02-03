<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>
 <!-- Inspections view -->
    <section id="inspections" class="view-section active">
        <div class="row g-4">
            <div class="col-12">
                <div class="glass-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Upcoming Inspections</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleInspectionModal"><i class="fa-solid fa-plus me-2"></i> Add Inspection</button>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold">Oct&nbsp;20,&nbsp;2025</div>
                                <div class="text-muted small">MRI Scanner Calibration</div>
                            </div>
                            <span class="badge bg-info text-dark">Scheduled</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold">Nov&nbsp;12,&nbsp;2025</div>
                                <div class="text-muted small">X‑Ray Machines Inspection</div>
                            </div>
                            <span class="badge bg-warning text-dark">Due Soon</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-bold">Dec&nbsp;01,&nbsp;2025</div>
                                <div class="text-muted small">Defibrillator Compliance Check</div>
                            </div>
                            <span class="badge bg-success">Booked</span>
                        </li>
                    </ul>
                </div>
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">Inspection History</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold">Aug&nbsp;25,&nbsp;2025</div>
                                <div class="text-muted small">Infusion Pump – Pass</div>
                            </div>
                            <span class="badge bg-success">Pass</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-bold">Jul&nbsp;10,&nbsp;2025</div>
                                <div class="text-muted small">CT Scanner – Fail</div>
                            </div>
                            <span class="badge bg-danger">Fail</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>