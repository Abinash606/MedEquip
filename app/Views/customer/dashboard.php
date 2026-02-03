<?= $this->extend('layouts/customer-header') ?>
<?= $this->section('content') ?>
<section id="overview" class="view-section active">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-0">Mercy General Hospital</h2>
                <p class="text-muted mb-0">Customer Portal • Main Campus</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex flex-column" style="min-width: 220px;">
                    <label for="customerFilter" class="small text-muted fw-bold mb-1">Filter by Customer</label>
                    <select id="customerFilter" class="form-select form-select-sm" fdprocessedid="nwhtih">
                        <option selected="">Mercy General Hospital</option>
                        <option>Downtown Clinic</option>
                        <option>Westside Imaging</option>
                    </select>
                </div>

                <button class="btn btn-danger rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#serviceRequestModal" fdprocessedid="bnjdec">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Report Issue
                </button>
                <img src="https://ui-avatars.com/api/?name=Admin+User&amp;background=eff6ff&amp;color=2563eb" class="rounded-circle" width="45" alt="User">
            </div>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="glass-card">
                    <div class="text-muted small fw-bold text-uppercase mb-2">Total Assets</div>
                    <h2 class="fw-bold mb-0">482</h2>
                    <div class="small text-muted mt-1">Across 3 Departments</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card border-start border-4 border-warning">
                    <div class="text-muted small fw-bold text-uppercase mb-2">Compliance Alert</div>
                    <h2 class="fw-bold mb-0 text-warning">5</h2>
                    <div class="small text-muted mt-1">Inspections due in 7 days</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card">
                    <div class="text-muted small fw-bold text-uppercase mb-2">Active Repairs</div>
                    <h2 class="fw-bold mb-0">2</h2>
                    <div class="small text-muted mt-1"><span class="text-primary">Est. completion: Today</span></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card bg-success bg-opacity-10 border-success border-opacity-25">
                    <div class="text-success small fw-bold text-uppercase mb-2">Uptime Score</div>
                    <h2 class="fw-bold mb-0 text-success">99.4%</h2>
                    <div class="small text-success mt-1">Last 30 Days</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Equipment Status</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" style="width: 150px;" fdprocessedid="0rhwrn">
                                <option>All Locations</option>
                                <option>Radiology</option>
                                <option>ICU</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary" fdprocessedid="wvf03u"><i class="fa-solid fa-download"></i> Export Assets</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table table-hover">
                            <thead>
                                <tr>
                                    <th>Equipment / Tag</th>
                                    <th>Department</th>
                                    <th>Compliance Status</th>
                                    <th>Next Inspection</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="fw-bold">CT Scanner Model X</div>
                                        <div class="small text-muted">#TAG-9921</div>
                                    </td>
                                    <td>Radiology</td>
                                    <td><i class="fa-solid fa-circle-check text-success me-1"></i> Compliant</td>
                                    <td>Nov 15, 2025</td>
                                    <td><span class="status-badge status-operational">Operational</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-bold">Patient Monitor Z5</div>
                                        <div class="small text-muted">#TAG-1002</div>
                                    </td>
                                    <td>ER - Bed 4</td>
                                    <td><i class="fa-solid fa-circle-exclamation text-warning me-1"></i> Due Soon</td>
                                    <td><span class="text-danger fw-bold">Oct 30, 2025</span></td>
                                    <td><span class="status-badge status-operational">Operational</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="fw-bold">Ventilator Series 9</div>
                                        <div class="small text-muted">#TAG-8832</div>
                                    </td>
                                    <td>ICU</td>
                                    <td><i class="fa-solid fa-circle-xmark text-danger me-1"></i> Missing Info</td>
                                    <td>--</td>
                                    <td><span class="status-badge status-service">Under Repair</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass-card mb-4">
                    <h6 class="fw-bold mb-3">Recent Reports</h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3 p-2 hover-bg rounded">
                            <div class="doc-icon bg-danger bg-opacity-10 text-danger"><i class="fa-solid fa-file-pdf"></i></div>
                            <div class="flex-grow-1">
                                <div class="small fw-bold">Q3 Inspection Summary</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Oct 12 • 2.4 MB</div>
                            </div>
                            <button class="btn btn-sm btn-light" fdprocessedid="lx66fo"><i class="fa-solid fa-download"></i></button>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-2 hover-bg rounded">
                            <div class="doc-icon bg-primary bg-opacity-10 text-primary"><i class="fa-solid fa-file-invoice"></i></div>
                            <div class="flex-grow-1">
                                <div class="small fw-bold">Invoice #INV-2025-001</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Oct 10 • Paid</div>
                            </div>
                            <button class="btn btn-sm btn-light" fdprocessedid="rw7c4j"><i class="fa-solid fa-download"></i></button>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm w-100 mt-3" fdprocessedid="vostr8">View All Documents</button>
                </div>

                <div class="glass-card bg-dark text-white" style="background: #1e293b;">
                    <h6 class="fw-bold mb-3"><i class="fa-regular fa-calendar me-2"></i>Upcoming Visits</h6>
                    <div class="border-start border-primary border-2 ps-3 mb-3">
                        <div class="small opacity-75">Next Tuesday, 9:00 AM</div>
                        <div class="fw-bold">Preventive Maintenance</div>
                        <div class="small badge bg-primary mt-1">Confirmed</div>
                    </div>
                    <div class="border-start border-secondary border-2 ps-3">
                        <div class="small opacity-75">Nov 15, 2025</div>
                        <div class="fw-bold">Calibration - Radiology</div>
                        <div class="small badge bg-secondary mt-1">Scheduled</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
	<!-- Service Request Modal -->
<div class="modal fade" id="serviceRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">New Service Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Select Location</label>
                        <select class="form-select rounded-3 bg-light border-0">
                            <option>Radiology</option>
                            <option>ICU</option>
                            <option>ER - Bed 4</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Equipment Asset Tag</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fa-solid fa-barcode"></i></span>
                            <input type="text" class="form-control bg-light border-0" placeholder="Scan or type tag...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Issue Description</label>
                        <textarea class="form-control bg-light border-0 rounded-3" rows="3" placeholder="Describe the problem..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Priority</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="priority" id="low" checked>
                            <label class="btn btn-outline-success btn-sm rounded-pill px-3" for="low">Routine</label>
                            
                            <input type="radio" class="btn-check" name="priority" id="high">
                            <label class="btn btn-outline-danger btn-sm rounded-pill px-3" for="high">Emergency</label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-wow w-100">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>