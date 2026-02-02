<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Operational Overview</h3>
            <div class="input-group" style="width: 50%;">
                <input type="search" class="form-control" placeholder="Search for customers, assets, parts...">
                <button class="btn btn-primary" type="button"><i class="fa-solid fa-search"></i></button>
            </div>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newWorkOrderModal"><i class="fa-solid fa-plus me-2"></i> New Request</button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="glass-card stat-card d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="text-muted small fw-bold uppercase">All Customer Inspection Status</div>
                        <div class="fs-2 fw-bold text-dark"><?= esc($customersCount ?? 0) ?></div>
                        <div class="small text-danger"><i class="fa-solid fa-circle-exclamation"></i> 4 Critical</div>
                    </div>
                    <div class="fs-1 text-primary opacity-25"><i class="fa-solid fa-clipboard-list"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card stat-card d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="text-muted small fw-bold uppercase">All Devices or Equipment Status</div>
                        <div class="fs-2 fw-bold text-dark">88%</div>
                        <div class="small text-success"><i class="fa-solid fa-arrow-up"></i> 5% vs last week</div>
                    </div>
                    <div class="fs-1 text-info opacity-25"><i class="fa-solid fa-user-clock"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card stat-card d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="text-muted small fw-bold uppercase">Pending Invoices</div>
                        <div class="fs-2 fw-bold text-dark">$12.4k</div>
                        <div class="small text-muted">7 awaiting approval</div>
                    </div>
                    <div class="fs-1 text-warning opacity-25"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card stat-card d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="text-muted small fw-bold uppercase">Compliance Score</div>
                        <div class="fs-2 fw-bold text-dark">98%</div>
                        <div class="small text-success">Audit Ready</div>
                    </div>
                    <div class="fs-1 text-success opacity-25"><i class="fa-solid fa-shield-check"></i></div>
                </div>
            </div>
        </div>

        <!-- Summary cards row showing overview metrics for work orders, inspections and inventory -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="glass-card stat-card d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="text-muted small fw-bold uppercase">Work Order Overview</div>
                        <div class="fs-2 fw-bold text-dark">50</div>
                        <div class="small text-muted"><span class="fw-bold text-success">40</span> Completed • <span class="fw-bold text-warning">10</span> In Progress</div>
                    </div>
                    <div class="fs-1 text-primary opacity-25"><i class="fa-solid fa-clipboard-check"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card stat-card d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="text-muted small fw-bold uppercase">Inspection Overview</div>
                        <div class="fs-2 fw-bold text-dark">70</div>
                        <div class="small text-muted"><span class="fw-bold text-success">50</span> Completed • <span class="fw-bold text-warning">20</span> In Progress</div>
                    </div>
                    <div class="fs-1 text-info opacity-25"><i class="fa-solid fa-clipboard-search"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card stat-card d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="text-muted small fw-bold uppercase">Inventory Overview</div>
                        <div class="fs-2 fw-bold text-dark">26</div>
                        <div class="small text-muted"><span class="fw-bold text-warning">5</span> Low Stock • <span class="fw-bold text-danger">1</span> Out of Stock</div>
                    </div>
                    <div class="fs-1 text-warning opacity-25"><i class="fa-solid fa-box-open"></i></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">Live Service Feed</h5>
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
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
                            <tr>
                                <td>#WO-1024</td>
                                <td>Mercy Hospital</td>
                                <td>MRI Scanner (Philips)</td>
                                <td><img src="https://ui-avatars.com/api/?name=John+Doe" class="avatar-circle me-1"> J. Doe</td>
                                <td><span class="badge bg-warning text-dark">In Progress</span></td>
                                <td class="text-muted small">2h 15m</td>
                            </tr>
                            <tr>
                                <td>#WO-1025</td>
                                <td>Downtown Clinic</td>
                                <td>Autoclave</td>
                                <td><img src="https://ui-avatars.com/api/?name=Sarah+Lee" class="avatar-circle me-1"> S. Lee</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td class="text-muted small">15m ago</td>
                            </tr>
                            <tr>
                                <td>#WO-1026</td>
                                <td>Westside Imaging</td>
                                <td>X-Ray Unit</td>
                                <td><span class="text-muted fst-italic">-- Unassigned --</span></td>
                                <td><span class="badge bg-danger">Emergency</span></td>
                                <td class="text-muted small fw-bold text-danger">New!</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Technician workload overview replaces the map placeholder -->
                <div class="glass-card bg-light p-4" style="min-height: 300px;">
                    <h5 class="fw-bold mb-3 text-center">Technician Workload</h5>
                    <p class="text-muted small mb-4 text-center">Overview of work order distribution across statuses.</p>
                    <!-- Status breakdown rows with labels and progress bars -->
                    <div class="mb-3 d-flex align-items-center">
                        <span class="me-2 text-muted small" style="width: 80px;">Completed</span>
                        <div class="progress flex-grow-1" style="height: 0.75rem;">
                            <div class="progress-bar bg-success" style="width: 60%" role="progressbar" aria-label="Completed work orders"></div>
                        </div>
                        <span class="ms-2 text-muted small">60%</span>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <span class="me-2 text-muted small" style="width: 80px;">In Progress</span>
                        <div class="progress flex-grow-1" style="height: 0.75rem;">
                            <div class="progress-bar bg-warning" style="width: 30%" role="progressbar" aria-label="In progress work orders"></div>
                        </div>
                        <span class="ms-2 text-muted small">30%</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-2 text-muted small" style="width: 80px;">New</span>
                        <div class="progress flex-grow-1" style="height: 0.75rem;">
                            <div class="progress-bar bg-danger" style="width: 10%" role="progressbar" aria-label="New work orders"></div>
                        </div>
                        <span class="ms-2 text-muted small">10%</span>
                    </div>
                </div>
            </div>
        </div>

<?= $this->endSection() ?>