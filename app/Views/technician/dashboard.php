<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>
 <!-- Dashboard view -->
    <section id="dashboard" class="view-section active">
    
    <header class="d-flex justify-content-between align-items-center mb-5">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
            <div>
                <h2 class="fw-bold mb-0">Welcome back, Dr. Smith</h2>
                <p class="text-muted mb-0">Mercy General Hospital - Main Wing</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="input-group d-none d-md-flex" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0 rounded-start-4 ps-3"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control border-start-0 rounded-end-4 shadow-sm" placeholder="Search asset tag or serial...">
            </div>
            <div class="position-relative cursor-pointer">
                <i class="fa-regular fa-bell fs-5 text-secondary"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <img src="https://ui-avatars.com/api/?name=Dr+Smith&background=EBF4FF&color=2563EB" class="rounded-circle" width="45" alt="Profile">
                <div class="d-none d-md-block">
                    <div class="fw-bold small">Dr. Sarah Smith</div>
                    <div class="text-muted small" style="font-size: 0.75rem;">Facility Manager</div>
                </div>
            </div>
        </div>
    </header>

    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-1">Total Equipment</div>
                        <h3 class="fw-bold mb-0">142</h3>
                        <span class="text-success small"><i class="fa-solid fa-arrow-up"></i> 3 New this month</span>
                    </div>
                    <div class="stat-icon-wrapper bg-blue-100 text-primary" style="background-color: #dbeafe;">
                        <i class="fa-solid fa-stethoscope"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Operational</span>
                        <span class="text-success fw-bold">138</span>
                    </div>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 95%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="glass-card border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-1">Action Required</div>
                        <h3 class="fw-bold mb-0 text-danger">4</h3>
                        <span class="text-muted small">Due for Inspection</span>
                    </div>
                    <div class="stat-icon-wrapper text-danger" style="background-color: #fee2e2;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
                <div class="mt-3">
                     <button class="btn btn-sm btn-outline-danger w-100 rounded-pill">View Schedule</button>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-1">Open Requests</div>
                        <h3 class="fw-bold mb-0">2</h3>
                        <span class="text-muted small">In Progress</span>
                    </div>
                    <div class="stat-icon-wrapper text-warning" style="background-color: #fef3c7;">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark">MRI Scan</span>
                    <span class="badge bg-info text-dark">X-Ray 02</span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="glass-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase mb-1">Compliance Health</div>
                    <h4 class="fw-bold mb-1">Excellent</h4>
                    <p class="text-muted small mb-0">Last audit: Oct 24</p>
                </div>
                <div class="progress-circle">
                    <div class="progress-circle-inner">98%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-12">
            <div class="glass-card">
                <h6 class="fw-bold mb-4">Recent Activity</h6>
                
                <div class="d-flex gap-3 mb-4">
                    <div class="d-flex flex-column align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-check small"></i>
                        </div>
                        <div class="h-100 border-start mt-2" style="border-color: #f1f5f9 !important;"></div>
                    </div>
                    <div>
                        <div class="fw-bold small">Maintenance Completed</div>
                        <div class="text-muted small mb-1">MRI Scanner - Radiology</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Today, 10:30 AM</div>
                        <a href="#" class="badge bg-light text-primary text-decoration-none mt-1"><i class="fa-solid fa-download"></i> Report</a>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-4">
                    <div class="d-flex flex-column align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-calendar-check small"></i>
                        </div>
                        <div class="h-100 border-start mt-2" style="border-color: #f1f5f9 !important;"></div>
                    </div>
                    <div>
                        <div class="fw-bold small">Inspection Scheduled</div>
                        <div class="text-muted small mb-1">X-Ray Machines (All)</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Nov 12, 2025</div>
                    </div>
                </div>

                 <div class="d-flex gap-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-file-signature small"></i>
                        </div>
                    </div>
                    <div>
                        <div class="fw-bold small">Service Request Logged</div>
                        <div class="text-muted small mb-1">Broken Display - Bed 4 Monitor</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Nov 01, 2025</div>
                    </div>
                </div>

            </div>
        </div>
        </div>
    </section>
<?= $this->endSection() ?>