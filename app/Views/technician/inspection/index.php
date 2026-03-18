<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>


<section id="inspections" class="view-section active">
    <div id="tech-inspection-workflow">
        <div id="view-dashboard">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Inspections</h3>
                    <p class="text-muted small mb-0">Manage and track equipment safety checks.</p>
                </div>
                <button class="btn btn-primary" onclick="techStartInspection()">
                    <i class="fa-solid fa-plus me-2"></i>Add Inspection
                </button>
            </div>

            <div class="glass-card mb-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Upcoming Inspections</h5>
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
                            <div class="text-muted small">X-Ray Machines Inspection</div>
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

            <div class="glass-card p-4">
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

        <div id="view-inspection" style="display:none;">
            <a href="#" class="text-decoration-none text-muted small mb-2 d-inline-block" onclick="techShowDashboard()">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Inspections List
            </a>

            <div class="d-flex justify-content-between align-items-start mt-2 mb-4">
                <div>
                    <div class="d-inline-flex align-items-center gap-2 mb-2 px-3 py-2 rounded-3"
                        style="background:rgba(99,102,241,0.12);border:1.5px solid rgba(99,102,241,0.5);">
                        <i class="fa-solid fa-location-dot" style="color:#6366f1;"></i>
                        <span class="fw-bold" id="tech-insp-site-label" style="font-size:1rem;color:#1e293b;">Site:
                            —</span>
                    </div>
                    <h2 class="fw-bold" id="tech-insp-title">—</h2>
                    <p class="text-muted">
                        <span id="tech-insp-id-label">—</span> •
                        <span class="text-primary" id="tech-insp-technician">—</span>
                    </p>
                </div>
                <div class="text-end">
                    <label class="d-block small text-muted mb-1">Current Status</label>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" id="techStatusDropdown"
                            type="button">
                            <i class="fa-solid fa-rotate"></i> In Progress
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">In Progress</a></li>
                            <li><a class="dropdown-item" href="#">Closed/Complete</a></li>
                        </ul>
                    </div>
                    <div class="small text-muted mt-1 fst-italic">Mark as Closed when done</div>
                </div>
            </div>

            <div class="glass-card p-3 mb-3">
                <h5 class="fw-bold mb-3">Start With Asset Number <span class="text-danger">Not</span> Serial Number</h5>
                <div class="d-flex justify-content-center">
                    <div class="input-group" style="max-width:600px">
                        <input class="form-control" id="techAssetInput" placeholder="Scan or Enter Barcode..."
                            type="text">
                        <button class="btn btn-primary" onclick="techHandleAssetGo()" type="button">
                            <i class="fa-solid fa-arrow-right"></i> Go
                        </button>
                    </div>
                </div>
                <p class="text-muted small mt-2">Devices only move to Inspected Items after the inspection result is
                    recorded.</p>
            </div>

            <!-- Tabs -->
            <div class="card-custom">
                <div class="card-header border-0 pt-3 pb-0">
                    <ul class="nav nav-tabs" id="techInspTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-target="#tech-not-inspected" data-bs-toggle="tab"
                                role="tab" type="button">
                                Not Inspected
                                <span class="badge rounded-pill ms-1" id="tech-not-inspected-count">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link disabled" data-bs-target="#tech-inspect-device" data-bs-toggle="tab"
                                id="tech-passfail-tab" role="tab" type="button" disabled>
                                Pass/Fail
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#tech-inspected" data-bs-toggle="tab" role="tab"
                                type="button">
                                Inspected Items
                                <span class="badge bg-success rounded-pill ms-1" id="tech-inspected-count">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#tech-inventory" data-bs-toggle="tab" role="tab"
                                type="button">
                                All Inventory
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#tech-archived" data-bs-toggle="tab" role="tab"
                                type="button">
                                Archived Items
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#tech-work-orders" data-bs-toggle="tab"
                                id="tech-wo-tab" role="tab" type="button">
                                Work Orders
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#tech-insp-reports" data-bs-toggle="tab"
                                id="tech-reports-tab" role="tab" type="button">
                                Inspection Reports
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content">

                        <!-- ── Not Inspected ──────────────────────────────────────── -->
                        <div class="tab-pane fade p-4 show active" id="tech-not-inspected" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <div class="fw-semibold">Site Inventory Pending Inspection</div>
                                    <div class="text-muted small" id="tech-not-inspected-msg">Loading...</div>
                                </div>
                                <div class="tech-export-bar">
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('copy','techNotInspBody')">Copy</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('csv','techNotInspBody','not-inspected')">CSV</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('excel','techNotInspBody','not-inspected')">Excel</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('pdf','techNotInspBody','not-inspected')">PDF</button>
                                </div>
                                <div class="input-group" style="max-width:300px">
                                    <span class="input-group-text border-end-0"><i
                                            class="fa-solid fa-search text-muted"></i></span>
                                    <input class="form-control border-start-0 ps-0" id="techNotInspSearch"
                                        placeholder="Search pending items..." type="text">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Asset #</th>
                                            <th>Model</th>
                                            <th>Type</th>
                                            <th>Dept / Room</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="techNotInspBody">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Select a site to load
                                                inventory.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ── Pass / Fail form ───────────────────────────────────── -->
                        <div class="tab-pane fade p-4" id="tech-inspect-device" role="tabpanel">
                            <div id="techInspectEmpty" class="text-muted">
                                Select a device to inspect (scan an Asset # above or click <strong>Inspect</strong> from
                                the Not Inspected tab).
                            </div>
                            <div class="d-none" id="techInspectFormWrapper">
                                <!-- Device info card -->
                                <div class="glass-card mb-4">
                                    <div class="card-body">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-3 fw-semibold">Customer:</div>
                                            <div class="col-md-9 fw-semibold" id="techInspCustomer">—</div>
                                            <div class="col-md-3 fw-semibold">Model:</div>
                                            <div class="col-md-9" id="techInspModel">—</div>
                                            <div class="col-md-3 fw-semibold">Department:</div>
                                            <div class="col-md-9"><input class="form-control" id="techInspDept"
                                                    placeholder="Department" type="text"></div>
                                            <div class="col-md-3 fw-semibold">Room:</div>
                                            <div class="col-md-9"><input class="form-control" id="techInspRoom"
                                                    placeholder="Room" type="text"></div>
                                            <div class="col-md-3 fw-semibold">Serial #:</div>
                                            <div class="col-md-9"><input class="form-control" id="techInspSerial"
                                                    placeholder="Serial #" type="text"></div>
                                            <div class="col-md-3 fw-semibold">Asset ID:</div>
                                            <div class="col-md-9"><input class="form-control" id="techInspAsset"
                                                    readonly type="text"></div>
                                            <div class="col-md-3 fw-semibold">PM Frequency:</div>
                                            <div class="col-md-9">
                                                <select class="form-select" id="techInspPMFreq" style="max-width:240px">
                                                    <option value="12 Month">12 Month</option>
                                                    <option value="6 Month">6 Month</option>
                                                    <option value="3 Month">3 Month</option>
                                                    <option value="24 Month">24 Month</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 fw-semibold">Action Performed:</div>
                                            <div class="col-md-9">
                                                <select class="form-select" id="techInspAction" style="max-width:240px">
                                                    <option value="Annual Performance Inspection">Annual Performance
                                                        Inspection</option>
                                                    <option value="Preventive Maintenance">Preventive Maintenance
                                                    </option>
                                                    <option value="Safety Inspection">Safety Inspection</option>
                                                    <option value="Compliance Check">Compliance Check</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Notes + outcome buttons -->
                                <div class="glass-card">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="fw-semibold" style="min-width:70px;padding-top:6px;">
                                                        Notes:</div>
                                                    <textarea class="form-control" id="techInspNotes"
                                                        placeholder="Enter service notes..." rows="4"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                                                <button class="btn btn-success px-4" id="techBtnPass" type="button">Pass
                                                    Inspection</button>
                                                <button class="btn btn-danger px-4" id="techBtnFail" type="button">Fail
                                                    Inspection</button>
                                                <button class="btn btn-warning px-4 text-white" id="techBtnFailWO"
                                                    type="button">Fail Inspection & Open Work Order</button>
                                                <button class="btn btn-primary px-4" id="techBtnRepair"
                                                    type="button">Repair Inspection</button>
                                                <button class="btn btn-secondary ms-auto" id="techBtnCancel"
                                                    type="button">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Inspected Items ────────────────────────────────────── -->
                        <div class="tab-pane fade p-4" id="tech-inspected" role="tabpanel">

                            <!-- Section Header -->
                            <div class="tech-inspected-topbar mb-4">
                                <div class="tech-inspected-left">
                                    <h5 class="mb-1">Inspected Items</h5>
                                    <p class="mb-0" id="tech-inspected-subtitle">
                                        These items have been inspected.
                                    </p>
                                </div>

                                <div class="tech-inspected-center">
                                    <div class="tech-export-bar">
                                        <button class="tech-export-btn"
                                            onclick="techExportTable('copy','techInspectedBody')">Copy</button>
                                        <button class="tech-export-btn"
                                            onclick="techExportTable('csv','techInspectedBody','inspected-items')">CSV</button>
                                        <button class="tech-export-btn"
                                            onclick="techExportTable('excel','techInspectedBody','inspected-items')">Excel</button>
                                        <button class="tech-export-btn"
                                            onclick="techExportTable('pdf','techInspectedBody','inspected-items')">PDF</button>
                                    </div>
                                </div>

                                <div class="tech-inspected-right">
                                    <div class="input-group tech-inspected-search">
                                        <span class="input-group-text border-end-0 bg-transparent">
                                            <i class="fa-solid fa-search text-muted"></i>
                                        </span>
                                        <input class="form-control border-start-0 ps-0" id="techInspectedSearch"
                                            placeholder="Search inspected..." type="text">
                                    </div>
                                </div>
                            </div>

                            <!-- Device Type Counter -->
                            <div class="tech-counter-card mb-4">
                                <div class="tech-counter-title">Device Type Counter</div>

                                <div class="table-responsive">
                                    <table class="table tech-counter-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Device Type</th>
                                                <th>Total</th>
                                                <th>EST</th>
                                                <th>CAL</th>
                                            </tr>
                                        </thead>
                                        <tbody id="techDeviceCountBody">
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">No inspected items
                                                    yet.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Inspected Items Table -->
                            <div class="tech-items-table-wrap">
                                <div class="table-responsive">
                                    <table class="table table-custom tech-inspected-table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:120px;">Actions</th>
                                                <th>Model</th>
                                                <th>Type</th>
                                                <th>S/N</th>
                                                <th>Asset #</th>
                                                <th>Dept / Room</th>
                                                <th>Result</th>
                                                <th>Notes</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="techInspectedBody">
                                            <tr id="techInspectedEmpty">
                                                <td colspan="9" class="text-center text-muted py-4">No inspected items
                                                    yet.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ── All Inventory ──────────────────────────────────────── -->
                        <div class="tab-pane fade p-4" id="tech-inventory" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <div class="fw-semibold">All Inventory</div>
                                    <div class="text-muted small">Full list of equipment in the site inventory.</div>
                                </div>
                                <div class="tech-export-bar">
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('copy','techInventoryBody')">Copy</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('csv','techInventoryBody','all-inventory')">CSV</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('excel','techInventoryBody','all-inventory')">Excel</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('pdf','techInventoryBody','all-inventory')">PDF</button>
                                </div>
                                <div class="input-group" style="max-width:300px">
                                    <span class="input-group-text border-end-0"><i
                                            class="fa-solid fa-search text-muted"></i></span>
                                    <input class="form-control border-start-0 ps-0" id="techInventorySearch"
                                        placeholder="Search inventory..." type="text">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Asset #</th>
                                            <th>Model</th>
                                            <th>Type</th>
                                            <th>Dept / Room</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="techInventoryBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Select a site to load
                                                inventory.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <!-- ── Archived Items ──────────────────────────────────────── -->
                        <div class="tab-pane fade p-4" id="tech-archived" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <div class="fw-semibold">Archived Items</div>
                                    <div class="text-muted small">Archived equipment is kept for history and reporting.
                                    </div>
                                </div>
                                <div class="tech-export-bar">
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('copy','techArchivedBody')">Copy</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('csv','techArchivedBody','archived-items')">CSV</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('excel','techArchivedBody','archived-items')">Excel</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('pdf','techArchivedBody','archived-items')">PDF</button>
                                </div>
                                <div class="input-group" style="max-width:300px">
                                    <span class="input-group-text border-end-0"><i
                                            class="fa-solid fa-search text-muted"></i></span>
                                    <input class="form-control border-start-0 ps-0" id="techArchivedSearch"
                                        placeholder="Search archived..." type="text">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Asset #</th>
                                            <th>Model</th>
                                            <th>Type</th>
                                            <th>Dept / Room</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="techArchivedBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No archived items found.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ── Work Orders ────────────────────────────────────────── -->
                        <div class="tab-pane fade p-4" id="tech-work-orders" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h5 class="fw-bold mb-1">Work Orders</h5>
                                    <p class="text-muted small mb-0">Manage and track work orders for this site.</p>
                                </div>
                                <button class="btn btn-primary" onclick="techOpenWorkOrderModal()" type="button">
                                    <i class="fa-solid fa-plus me-2"></i>Add Work Order
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                                <div class="tech-export-bar">
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('copy','techWOBody')">Copy</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('csv','techWOBody','work-orders')">CSV</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('excel','techWOBody','work-orders')">Excel</button>
                                    <button class="tech-export-btn"
                                        onclick="techExportTable('pdf','techWOBody','work-orders')">PDF</button>
                                </div>
                                <div class="input-group" style="max-width:300px">
                                    <span class="input-group-text border-end-0"><i
                                            class="fa-solid fa-search text-muted"></i></span>
                                    <input class="form-control border-start-0 ps-0" id="techWOSearch"
                                        placeholder="Search work orders..." type="text">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-hover" id="techWOTable">
                                    <thead>
                                        <tr>
                                            <th>Actions</th>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Asset #</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody id="techWOBody">
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-3">No work orders found.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ── Inspection Reports ─────────────────────────────────── -->
                        <div class="tab-pane fade p-4" id="tech-insp-reports" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <div>
                                    <h5 class="fw-bold mb-1">Inspection Reports</h5>
                                    <p class="text-muted small mb-0">Preview and export inspection summaries.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary" id="techPreviewReportBtn" type="button">
                                        <i class="fa-solid fa-file-lines me-2"></i>Preview
                                    </button>
                                    <button class="btn btn-primary" id="techDownloadReportBtn" type="button">
                                        <i class="fa-solid fa-download me-2"></i>Download PDF
                                    </button>
                                </div>
                            </div>
                            <div id="techReportsTabContent">
                                <p class="text-muted fst-italic">Complete an inspection to load the report here, or
                                    click the report icon on an existing inspection row.</p>
                            </div>
                        </div>

                    </div><!-- /tab-content -->
                </div>
            </div><!-- /card-custom -->
        </div><!-- /view-inspection -->

        <!-- ══════════════════════════════════════════════════════════════════════
     WORK ORDER MODAL (Technician)
     ══════════════════════════════════════════════════════════════════════ -->
        <div class="modal fade" id="techWorkOrderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="techWOForm">
                        <input type="hidden" id="techWOId" value="">
                        <input type="hidden" id="techWOEquipmentId" value="">
                        <input type="hidden" id="techWOFailedAssetTag" value="">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="techWOModalTitle">Add Work Order</h5>
                            <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="techWOError"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input class="form-control" id="techWOTitle" required type="text"
                                        placeholder="e.g., Equipment Failure - Urgent Repair">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Asset #</label>
                                    <input class="form-control" id="techWOAsset" placeholder="Enter asset to auto-fill"
                                        type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Make</label>
                                    <input class="form-control" id="techWOMake" placeholder="Auto-filled" readonly
                                        type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Model</label>
                                    <input class="form-control" id="techWOModel" placeholder="Auto-filled" readonly
                                        type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Serial #</label>
                                    <input class="form-control" id="techWOSerial" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Priority</label>
                                    <select class="form-select" id="techWOPriority">
                                        <option value="Low">Low</option>
                                        <option value="Medium" selected>Medium</option>
                                        <option value="High">High</option>
                                        <option value="Urgent">Urgent</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="techWOStatus">
                                        <option value="Open">Open</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input class="form-control" id="techWOStartDate" type="date">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input class="form-control" id="techWOEndDate" type="date">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" id="techWODescription" rows="3"
                                        placeholder="Describe the issue and required repairs..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                type="button">Cancel</button>
                            <button class="btn btn-primary" type="submit" id="techWOSaveBtn">Save Work Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /techWorkOrderModal -->

        <!-- ══════════════════════════════════════════════════════════════════════
     INSPECTION REPORT MODAL (Technician)
     ══════════════════════════════════════════════════════════════════════ -->
        <div class="modal fade" id="techInspReportModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Inspection Report</h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <div class="modal-body">
                        <div id="techReportContent"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-outline-primary" id="techPreviewPDFBtn" type="button">
                            <i class="fa-solid fa-file-lines me-2"></i>Preview
                        </button>
                        <button class="btn btn-primary" id="techDownloadPDFBtn" type="button">
                            <i class="fa-solid fa-download me-2"></i>Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </div><!-- /techInspReportModal -->
        <div class="modal fade" id="techNewInspModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-clipboard-check me-2"></i>New Inspection
                        </h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <div class="modal-body">

                        <!-- Step 1: Choose site -->
                        <div id="techNewStep1">
                            <h6 class="fw-semibold mb-3">Step 1: Select Customer Site</h6>
                            <div class="mb-3">
                                <label class="form-label">Customer Site</label>
                                <select class="form-select" id="techNewSiteSelect">
                                    <option value="" disabled selected>Select site…</option>
                                    <?php if (!empty($sites ?? [])): ?>
                                    <?php foreach ($sites as $site): ?>
                                    <option value="<?= esc($site['id']) ?>"><?= esc($site['name']) ?></option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <p class="text-muted small">After selecting the site, you will enter the inspection detail
                                view where you can scan asset barcodes and record results.</p>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-primary" id="techNewStartBtn" type="button">Start Inspection</button>
                    </div>
                </div>
            </div>
        </div><!-- /techNewInspModal -->
        <div class="modal fade" id="techAddDeviceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" style="max-height:90vh;">
                <div class="modal-content" style="max-height:90vh;display:flex;flex-direction:column;">
                    <div class="modal-header flex-shrink-0">
                        <div>
                            <h5 class="modal-title fw-bold">Add New Device</h5>
                            <div class="text-muted small">Asset not found — search the equipment database and add to
                                this inspection.</div>
                        </div>
                        <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <form id="techAddDeviceForm" style="display:flex;flex-direction:column;flex:1;overflow:hidden;">
                        <div class="modal-body" style="overflow-y:auto;flex:1;">
                            <div class="alert alert-warning d-none" id="techAddDeviceError"></div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Asset #</label>
                                    <input class="form-control" id="techAddAsset" required type="text">
                                </div>
                                <div class="col-md-4 position-relative">
                                    <label class="form-label">Model Number</label>
                                    <input autocomplete="off" class="form-control" id="techAddModel"
                                        placeholder="Start typing..." type="text">
                                    <div class="list-group position-absolute w-100 d-none" id="techModelSuggestions"
                                        style="z-index:2000;max-height:250px;overflow-y:auto;top:calc(100% + 2px);left:0;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Serial #</label>
                                    <input class="form-control" id="techAddSerial" placeholder="Optional" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Manufacturer</label>
                                    <input class="form-control" id="techAddManufacturer" placeholder="Start typing..."
                                        type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Type</label>
                                    <input class="form-control" id="techAddType" placeholder="Optional" type="text">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <input class="form-control" id="techAddDescription"
                                        placeholder="Auto-filled when model is selected" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <input class="form-control" id="techAddDept" placeholder="e.g. ICU" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Room</label>
                                    <input class="form-control" id="techAddRoom" placeholder="e.g. Room 2" type="text">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">PM Frequency</label>
                                    <select class="form-select" id="techAddPMFreq">
                                        <option value="12 Month">12 Month</option>
                                        <option value="6 Month">6 Month</option>
                                        <option value="3 Month">3 Month</option>
                                        <option value="24 Month">24 Month</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">EST</label>
                                    <select class="form-select" id="techAddEST">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">CAL</label>
                                    <select class="form-select" id="techAddCAL">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" id="techAddNotes" placeholder="Optional"
                                        rows="3"></textarea>
                                </div>
                                <!-- Action Performed inside Add Device modal -->
                                <div class="col-12">
                                    <label class="form-label">Action Performed</label>
                                    <select class="form-select" id="techAddAction">
                                        <option value="Annual Performance Inspection">Annual Performance Inspection
                                        </option>
                                        <option value="Preventive Maintenance">Preventive Maintenance</option>
                                        <option value="Safety Inspection">Safety Inspection</option>
                                        <option value="Compliance Check">Compliance Check</option>
                                    </select>
                                </div>
                                <!-- Inspection Result selector -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Inspection Result <span
                                            class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-success" id="techAddBtnPass" type="button">
                                            <i class="fa-solid fa-check me-1"></i>Pass
                                        </button>
                                        <button class="btn btn-danger" id="techAddBtnFail" type="button">
                                            <i class="fa-solid fa-xmark me-1"></i>Fail
                                        </button>
                                        <button class="btn btn-warning text-white" id="techAddBtnRepair" type="button">
                                            <i class="fa-solid fa-wrench me-1"></i>Repair
                                        </button>
                                    </div>
                                    <input type="hidden" id="techAddResult" value="Pass">
                                    <div class="mt-2">
                                        <span class="badge bg-success" id="techAddResultBadge">Selected: Pass</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Footer pinned at bottom — never scrolls away ──────── -->
                        <div class="modal-footer flex-shrink-0"
                            style="border-top:1px solid rgba(255,255,255,0.1);background:inherit;">
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                type="button">Cancel</button>
                            <button class="btn btn-primary px-4" type="submit" id="techAddSaveBtn">
                                <i class="fa-solid fa-plus me-2"></i>Save &amp; Record Inspection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /techAddDeviceModal -->

        <!-- Toast container -->
        <div class="toast-container position-fixed top-0 end-0 p-3" id="techToastContainer" style="z-index:2000;"></div>

    </div><!-- /tech-inspection-workflow -->
</section>

<style>
.tech-export-bar {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.tech-export-btn {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.4;
    border-radius: 6px;
    border: 1.5px solid rgba(99, 102, 241, 0.35);
    background: rgba(99, 102, 241, 0.08);
    color: #6366f1;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
    white-space: nowrap;
    text-decoration: none;
}

.tech-export-btn:hover {
    background: rgba(99, 102, 241, 0.18);
    border-color: rgba(99, 102, 241, 0.6);
    color: #4f46e5;
}

.tech-export-btn:active {
    background: rgba(99, 102, 241, 0.28);
}

/* ── Attention Required Badge ───────────────────────────────────────────── */
.attention-required {
    background-color: #fee2e2;
    border-left: 4px solid #dc2626;
    padding: 8px 12px;
    border-radius: 4px;
    font-weight: 600;
    color: #991b1b;
}

.attention-required::before {
    content: '⚠ ';
    margin-right: 6px;
}

.tech-inspected-topbar {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: start;
    gap: 16px;
}

.tech-inspected-left {
    text-align: left;
}

.tech-inspected-left h5 {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.tech-inspected-left p {
    font-size: 14px;
    color: #6c757d;
}

.tech-inspected-center {
    display: flex;
    justify-content: center;
    align-items: center;
}

.tech-inspected-right {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.tech-inspected-search {
    width: 100%;
    max-width: 360px;
}

.tech-export-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

@media (max-width: 991px) {
    .tech-inspected-topbar {
        grid-template-columns: 1fr;
    }

    .tech-inspected-center {
        justify-content: flex-start;
    }

    .tech-inspected-right {
        justify-content: flex-start;
    }

    .tech-inspected-search {
        max-width: 100%;
    }
}
</style>

<!-- ════════════════════════════════════════════════════════════════════════════
     JAVASCRIPT
     ════════════════════════════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    /* ── URL constants ──────────────────────────────────────────────── */
    const URL_GET_EQUIPMENT = '<?= site_url('technician/inspections/getEquipment') ?>';
    const URL_SEARCH_MODEL = '<?= site_url('technician/inspections/searchByModel') ?>';
    const URL_RECORD = '<?= site_url('technician/inspections/record') ?>';
    const URL_GROUP_ITEMS = '<?= site_url('technician/inspections/groupItems') ?>';
    const URL_DELETE_RECORD = '<?= site_url('technician/inspections/deleteRecord') ?>';
    const URL_SITE_EQUIPMENT = '<?= site_url('admin/site-inspection/getEquipment') ?>';
    const CSRF_NAME = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    /* ── State ──────────────────────────────────────────────────────── */
    let CURRENT_GROUP_ID = null;
    let CURRENT_SITE_ID = null;
    let CURRENT_SITE_NAME = '';
    let CURRENT_ASSET_TAG = null;
    let CURRENT_EQUIPMENT_ID = null;
    let FAILED_ASSET_TAG = null;
    const savedGroupId = sessionStorage.getItem('tech_current_group_id');
    const savedSiteId = sessionStorage.getItem('tech_current_site_id');
    const savedSiteName = sessionStorage.getItem('tech_current_site_name');

    if (savedGroupId && savedSiteId) {
        CURRENT_GROUP_ID = savedGroupId;
        CURRENT_SITE_ID = parseInt(savedSiteId);
        CURRENT_SITE_NAME = savedSiteName || '';

        techShowInspectionView(
            'Equipment Inspection',
            savedGroupId,
            'New Inspection',
            CURRENT_SITE_NAME,
            savedGroupId
        );

        document.getElementById('tech-insp-id-label').textContent = 'Inspection #' + CURRENT_GROUP_ID;

        loadSiteInventory(CURRENT_SITE_ID);
        loadInspectedItems(CURRENT_GROUP_ID);
        loadWorkOrders(CURRENT_GROUP_ID);
    }
    /* ── Toast ──────────────────────────────────────────────────────– */
    function toast(msg, type = 'success') {
        const c = document.getElementById('techToastContainer');
        const div = document.createElement('div');
        div.className = `alert alert-${type} shadow fade show d-flex align-items-center gap-2`;
        div.style.cssText = 'min-width:260px;';
        div.textContent = msg;
        c.appendChild(div);
        setTimeout(() => div.remove(), 3500);
    }

    /* ── Escape HTML ────────────────────────────────────────────────── */
    function esc(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    /* ═══════════════════════════════════════════════════════════════════
       DASHBOARD  ↔  DETAIL VIEW SWITCHING
       ═══════════════════════════════════════════════════════════════════ */
    window.techShowDashboard = function() {
        document.getElementById('view-dashboard').style.display = '';
        document.getElementById('view-inspection').style.display = 'none';
    };

    window.techStartInspection = function() {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('techNewInspModal')).show();
    };

    document.getElementById('techNewStartBtn')?.addEventListener('click', function() {
        const siteId = document.getElementById('techNewSiteSelect').value;
        const siteName = document.getElementById('techNewSiteSelect').selectedOptions[0]?.text || '';

        if (!siteId) {
            alert('Please select a site first.');
            return;
        }

        bootstrap.Modal.getInstance(document.getElementById('techNewInspModal')).hide();

        sessionStorage.removeItem('tech_current_group_id');
        sessionStorage.removeItem('tech_current_site_id');
        sessionStorage.removeItem('tech_current_site_name');

        CURRENT_GROUP_ID = null;
        CURRENT_SITE_ID = parseInt(siteId);
        CURRENT_SITE_NAME = siteName;

        techShowInspectionView('New Inspection', 'NEW', 'New Inspection', siteName, '');
        loadSiteInventory(CURRENT_SITE_ID);
        loadWorkOrders('');
    });

    window.techViewInspection = function(groupId, siteName, inspType, inspDisplayId, techName, siteId) {
        CURRENT_GROUP_ID = groupId;
        CURRENT_SITE_ID = siteId;
        CURRENT_SITE_NAME = siteName;

        techShowInspectionView(inspType, inspDisplayId, techName, siteName, groupId);
        loadSiteInventory(siteId);

        if (groupId) {
            loadInspectedItems(groupId);
            loadWorkOrders(groupId);
        } else if (siteId) {
            loadWorkOrders('');
        }
    };

    function techShowInspectionView(inspType, inspDisplayId, techName, siteName, groupId) {
        document.getElementById('view-dashboard').style.display = 'none';
        document.getElementById('view-inspection').style.display = '';

        const siteLabel = document.getElementById('tech-insp-site-label');
        if (siteLabel) {
            siteLabel.textContent = siteName ? ('Site: ' + siteName) : 'Site: —';
            siteLabel.style.color = '#1e293b';
            siteLabel.style.fontWeight = '700';
        }

        document.getElementById('tech-insp-title').textContent = inspType || 'Equipment Inspection';
        document.getElementById('tech-insp-id-label').textContent = 'Inspection #' + (inspDisplayId ||
            groupId || '—');
        document.getElementById('tech-insp-technician').textContent = techName || '—';
        document.getElementById('tech-inspection-workflow')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    /* ═══════════════════════════════════════════════════════════════════
       ASSET BARCODE LOOKUP
       ═══════════════════════════════════════════════════════════════════ */
    window.techHandleAssetGo = function() {
        const assetTag = document.getElementById('techAssetInput').value.trim();
        if (!assetTag) {
            toast('Please enter an asset number.', 'warning');
            return;
        }
        if (!CURRENT_SITE_ID) {
            toast('No site selected.', 'warning');
            return;
        }

        const url = URL_GET_EQUIPMENT + '?asset_tag=' + encodeURIComponent(assetTag) + '&site_id=' +
            CURRENT_SITE_ID;

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.found) {
                    openPassFailTab(data);
                } else {
                    document.getElementById('techAddAsset').value = assetTag;
                    document.getElementById('techAddSerial').value = '';
                    document.getElementById('techAddModel').value = '';
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('techAddDeviceModal'))
                        .show();
                }
            })
            .catch(() => toast('Error looking up asset.', 'danger'));
    };

    document.getElementById('techAssetInput')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') techHandleAssetGo();
    });

    window.techInspectFromNotInspected = function(assetTag) {
        document.getElementById('techAssetInput').value = assetTag;
        techHandleAssetGo();
    };

    /* ═══════════════════════════════════════════════════════════════════
       PASS / FAIL TAB
       ═══════════════════════════════════════════════════════════════════ */
    function openPassFailTab(equipment) {
        CURRENT_ASSET_TAG = equipment.asset_tag;
        CURRENT_EQUIPMENT_ID = equipment.id;

        document.getElementById('techInspCustomer').textContent = CURRENT_SITE_NAME;
        document.getElementById('techInspModel').textContent = [equipment.make, equipment.model].filter(Boolean)
            .join(' — ') || '—';
        document.getElementById('techInspDept').value = equipment.department || '';
        document.getElementById('techInspRoom').value = equipment.location || '';
        document.getElementById('techInspSerial').value = equipment.serial_number || '';
        document.getElementById('techInspAsset').value = equipment.asset_tag || '';

        document.getElementById('techInspectEmpty').classList.add('d-none');
        document.getElementById('techInspectFormWrapper').classList.remove('d-none');

        const pfTab = document.getElementById('tech-passfail-tab');
        pfTab.classList.remove('disabled');
        pfTab.removeAttribute('disabled');
        bootstrap.Tab.getOrCreateInstance(pfTab).show();

        document.getElementById('techInspectFormWrapper').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function recordInspection(result) {
        return new Promise((resolve, reject) => {
            if (!CURRENT_ASSET_TAG || !CURRENT_SITE_ID) {
                toast('Missing device or site information.', 'danger');
                reject('Missing device or site information.');
                return;
            }

            const outcomeButtons = ['techBtnPass', 'techBtnFail', 'techBtnFailWO', 'techBtnRepair'];
            outcomeButtons.forEach(id => {
                const b = document.getElementById(id);
                if (b) {
                    b.disabled = true;
                    b.style.opacity = '0.6';
                }
            });

            function reenableButtons() {
                outcomeButtons.forEach(id => {
                    const b = document.getElementById(id);
                    if (b) {
                        b.disabled = false;
                        b.style.opacity = '';
                    }
                });
            }

            const body = new URLSearchParams({
                [CSRF_NAME]: csrfHash,
                site_id: CURRENT_SITE_ID,
                asset_tag: CURRENT_ASSET_TAG,
                result: result,
                notes: document.getElementById('techInspNotes').value.trim(),
                department: document.getElementById('techInspDept').value.trim(),
                room: document.getElementById('techInspRoom').value.trim(),
                serial_number: document.getElementById('techInspSerial').value.trim(),
                action_performed: document.getElementById('techInspAction').value,
                pm_frequency: document.getElementById('techInspPMFreq').value,
                est: '0',
                cal: '0',
                group_id: CURRENT_GROUP_ID || '',
                asset_not_found: '0',
            });

            fetch(URL_RECORD, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: body.toString()
                })
                .then(r => {
                    if (!r.ok) {
                        return r.text().then(t => {
                            throw new Error('Server error ' + r.status + ': ' + t.substring(
                                0, 200));
                        });
                    }
                    return r.json();
                })
                .then(data => {
                    reenableButtons();

                    if (data.success) {
                        if (!CURRENT_GROUP_ID) {
                            CURRENT_GROUP_ID = data.group_id;
                        }

                        document.getElementById('tech-insp-id-label').textContent = 'Inspection #' +
                            CURRENT_GROUP_ID;

                        sessionStorage.setItem('tech_current_group_id', CURRENT_GROUP_ID);
                        sessionStorage.setItem('tech_current_site_id', CURRENT_SITE_ID);
                        sessionStorage.setItem('tech_current_site_name', CURRENT_SITE_NAME);

                        toast(
                            'Inspection recorded (' + result + ').',
                            result === 'Pass' ? 'success' : result === 'Fail' ? 'danger' :
                            'warning'
                        );

                        appendInspectedRow({
                            asset_tag: CURRENT_ASSET_TAG,
                            model: document.getElementById('techInspModel').textContent,
                            serial_number: document.getElementById('techInspSerial').value
                                .trim(),
                            department: document.getElementById('techInspDept').value
                                .trim(),
                            location: document.getElementById('techInspRoom').value.trim(),
                            result: result,
                            notes: document.getElementById('techInspNotes').value.trim(),
                        });

                        removeNotInspectedRow(CURRENT_ASSET_TAG);

                        const savedAssetTag = CURRENT_ASSET_TAG;
                        const savedGroupId = CURRENT_GROUP_ID;

                        resetPassFailForm();

                        resolve({
                            group_id: savedGroupId,
                            asset_tag: savedAssetTag
                        });
                    } else {
                        toast(data.message || 'Failed to save inspection.', 'danger');
                        reject(data.message || 'Failed to save inspection.');
                    }
                })
                .catch(err => {
                    reenableButtons();
                    toast('Error: ' + err.message, 'danger');
                    reject(err.message);
                });
        });
    }

    document.getElementById('techBtnPass')?.addEventListener('click', () => recordInspection('Pass'));
    document.getElementById('techBtnFail')?.addEventListener('click', () => recordInspection('Fail'));
    document.getElementById('techBtnFailWO')?.addEventListener('click', function() {
        const assetTagBeforeSave = CURRENT_ASSET_TAG;

        recordInspection('Fail')
            .then((res) => {
                if (res.group_id) {
                    CURRENT_GROUP_ID = res.group_id;

                    document.getElementById('tech-insp-id-label').textContent = 'Inspection #' +
                        CURRENT_GROUP_ID;

                    sessionStorage.setItem('tech_current_group_id', CURRENT_GROUP_ID);
                    sessionStorage.setItem('tech_current_site_id', CURRENT_SITE_ID);
                    sessionStorage.setItem('tech_current_site_name', CURRENT_SITE_NAME);
                }

                techOpenWorkOrderModal(assetTagBeforeSave);
            })
            .catch(() => {
                // inspection save failed
            });
    });
    document.getElementById('techBtnRepair')?.addEventListener('click', () => recordInspection('Repair'));
    document.getElementById('techBtnCancel')?.addEventListener('click', resetPassFailForm);

    function resetPassFailForm() {
        document.getElementById('techInspNotes').value = '';
        document.getElementById('techInspSerial').value = '';
        document.getElementById('techInspDept').value = '';
        document.getElementById('techInspRoom').value = '';
        document.getElementById('techAssetInput').value = '';
        document.getElementById('techInspectEmpty').classList.remove('d-none');
        document.getElementById('techInspectFormWrapper').classList.add('d-none');

        const pfTab = document.getElementById('tech-passfail-tab');
        pfTab.classList.add('disabled');
        pfTab.setAttribute('disabled', '');
        bootstrap.Tab.getOrCreateInstance(
            document.querySelector('[data-bs-target="#tech-not-inspected"]')
        ).show();
        CURRENT_ASSET_TAG = null;
    }

    /* ═══════════════════════════════════════════════════════════════════
       SITE INVENTORY LOADER
       ═══════════════════════════════════════════════════════════════════ */
    function loadSiteInventory(siteId) {
        document.getElementById('tech-not-inspected-count').textContent = '...';
        document.getElementById('tech-not-inspected-msg').textContent = 'Loading inventory for this site…';

        fetch('<?= site_url('technician/inspections/siteInventory') ?>?site_id=' + siteId, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    renderNotInspected([]);
                    renderAllInventory([]);
                    renderArchivedItems([]);
                    return;
                }
                renderNotInspected(data.equipment || []);
                renderAllInventory(data.equipment || []);
                renderArchivedItems(data.equipment || []);
            })
            .catch(() => {
                renderNotInspected([]);
                renderAllInventory([]);
                renderArchivedItems([]);
            });
    }

    function renderNotInspected(items) {
        const tbody = document.getElementById('techNotInspBody');
        const msg = document.getElementById('tech-not-inspected-msg');
        const badge = document.getElementById('tech-not-inspected-count');
        if (!items.length) {
            tbody.innerHTML =
                '<tr><td colspan="6" class="text-center text-muted">No equipment pending inspection.</td></tr>';
            badge.textContent = '0';
            msg.textContent = '0 item(s) remaining.';
            return;
        }
        badge.textContent = items.length;
        msg.textContent = items.length + ' item(s) remaining in site inventory pending inspection.';
        tbody.innerHTML = items.map(eq => `
            <tr data-asset="${esc(eq.asset_tag)}">
                <td>
                    <button class="btn btn-sm btn-primary" onclick="techInspectFromNotInspected('${esc(eq.asset_tag)}')">
                        <i class="fa-solid fa-arrow-right me-1"></i> Inspect
                    </button>
                </td>
                <td>${esc(eq.asset_tag)}</td>
                <td>${esc(eq.model || eq.make || '')}</td>
                <td>${esc(eq.device_type || '')}</td>
                <td>${esc(eq.department || '')}${eq.location ? ' / ' + esc(eq.location) : ''}</td>
                <td><span class="badge bg-secondary">Not Inspected</span></td>
            </tr>`).join('');
    }

    function renderAllInventory(items) {
        const tbody = document.getElementById('techInventoryBody');
        if (!items.length) {
            tbody.innerHTML =
                '<tr><td colspan="5" class="text-center text-muted">No inventory found.</td></tr>';
            return;
        }
        tbody.innerHTML = items.map(eq => `
            <tr>
                <td>${esc(eq.asset_tag)}</td>
                <td>${esc(eq.model || eq.make || '')}</td>
                <td>${esc(eq.device_type || '')}</td>
                <td>${esc(eq.department || '')}${eq.location ? ' / ' + esc(eq.location) : ''}</td>
                <td><span class="badge bg-secondary">${esc(eq.status || 'Unknown')}</span></td>
            </tr>`).join('');
    }

    /* ═══════════════════════════════════════════════════════════════════
       INSPECTED ITEMS
       ═══════════════════════════════════════════════════════════════════ */
    let inspectedItems = [];

    function loadInspectedItems(groupId) {
        fetch(URL_GROUP_ITEMS + '?group_id=' + encodeURIComponent(groupId), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                inspectedItems = data.data || [];
                renderInspectedTable(inspectedItems);
                updateDeviceCounter(inspectedItems);
                updateInspectedBadge(inspectedItems.length);
            })
            .catch(() => {});
    }

    function appendInspectedRow(item) {
        inspectedItems.push(item);
        renderInspectedTable(inspectedItems);
        updateDeviceCounter(inspectedItems);
        updateInspectedBadge(inspectedItems.length);
    }

    function renderInspectedTable(items) {
        const tbody = document.getElementById('techInspectedBody');
        const empty = document.getElementById('techInspectedEmpty');
        if (!items.length) {
            empty.style.display = '';
            tbody.querySelectorAll('tr:not(#techInspectedEmpty)').forEach(r => r.remove());
            return;
        }
        empty.style.display = 'none';
        tbody.querySelectorAll('tr:not(#techInspectedEmpty)').forEach(r => r.remove());
        items.forEach((item, idx) => {
            const tr = document.createElement('tr');
            tr.setAttribute('data-idx', idx);
            tr.innerHTML =
                `
                <td>
                    <button class="btn btn-sm btn-danger tech-del-inspected" data-id="${esc(item.id || '')}" data-idx="${idx}" title="Delete">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
                <td><strong>${esc(item.model || item.make || '')}</strong></td>
                <td>${esc(item.device_type || '')}</td>
                <td>${esc(item.serial_number || 'N/A')}</td>
                <td><span class="badge text-dark border">${esc(item.asset_tag || '')}</span></td>
                <td>${esc(item.department || '')}${item.location ? '<br><span class="text-muted small">' + esc(item.location) + '</span>' : ''}</td>
                <td>${resultBadge(item.result || item.status)}</td>
                <td class="small text-muted">${esc(item.notes || '')}</td>
                <td><span class="text-muted small">${item.completed_at ? item.completed_at.substring(0,10) : (item.created_at ? item.created_at.substring(0,10) : '—')}</span></td>`;
            tbody.appendChild(tr);
        });
    }

    function updateDeviceCounter(items) {
        const counts = {};
        items.forEach(item => {
            const t = item.device_type || 'Unknown';
            if (!counts[t]) counts[t] = {
                total: 0,
                est: 0,
                cal: 0
            };
            counts[t].total++;
            const ev = String(item.est || '0').toLowerCase();
            const cv = String(item.cal || '0').toLowerCase();
            if (ev === '1' || ev === 'yes') counts[t].est++;
            if (cv === '1' || cv === 'yes') counts[t].cal++;
        });
        const tbody = document.getElementById('techDeviceCountBody');
        const types = Object.keys(counts);
        if (!types.length) {
            tbody.innerHTML =
                '<tr><td colspan="4" class="text-center text-muted py-3">No inspected items yet.</td></tr>';
            return;
        }
        let totals = {
            total: 0,
            est: 0,
            cal: 0
        };
        tbody.innerHTML = types.map(t => {
                totals.total += counts[t].total;
                totals.est += counts[t].est;
                totals.cal += counts[t].cal;
                return `<tr><td>${esc(t)}</td><td>${counts[t].total}</td><td>${counts[t].est}</td><td>${counts[t].cal}</td></tr>`;
            }).join('') +
            `<tr class="fw-bold table-secondary"><td>Total</td><td>${totals.total}</td><td>${totals.est}</td><td>${totals.cal}</td></tr>`;
    }

    function updateInspectedBadge(count) {
        document.getElementById('tech-inspected-count').textContent = count;
    }

    function removeNotInspectedRow(assetTag) {
        const row = document.querySelector('#techNotInspBody tr[data-asset="' + assetTag + '"]');
        if (row) {
            row.remove();
            const remaining = document.querySelectorAll('#techNotInspBody tr[data-asset]').length;
            document.getElementById('tech-not-inspected-count').textContent = remaining;
            document.getElementById('tech-not-inspected-msg').textContent = remaining + ' item(s) remaining.';
        }
    }

    function resultBadge(res) {
        const r = String(res || '').trim();
        if (r === 'Pass')
            return '<span class="text-success fw-semibold"><i class="fa-solid fa-check me-1"></i>Pass</span>';
        if (r === 'Fail')
            return '<span class="text-danger fw-semibold"><i class="fa-solid fa-xmark me-1"></i>Fail</span>';
        if (r === 'Repair')
            return '<span class="text-warning fw-semibold"><i class="fa-solid fa-wrench me-1"></i>Repair</span>';
        return '<span class="text-muted">—</span>';
    }

    document.getElementById('techInspectedBody')?.addEventListener('click', function(e) {
        const btn = e.target.closest('.tech-del-inspected');
        if (!btn) return;
        const id = btn.dataset.id;
        const idx = parseInt(btn.dataset.idx, 10);
        if (!confirm('Remove this inspection record? This cannot be undone.')) return;

        if (id) {
            fetch(URL_DELETE_RECORD + '/' + encodeURIComponent(id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: CSRF_NAME + '=' + encodeURIComponent(csrfHash)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.csrf_hash) csrfHash = data.csrf_hash;
                })
                .catch(() => {});
        }
        inspectedItems.splice(idx, 1);
        renderInspectedTable(inspectedItems);
        updateDeviceCounter(inspectedItems);
        updateInspectedBadge(inspectedItems.length);
        toast('Record removed.', 'warning');
    });

    /* ═══════════════════════════════════════════════════════════════════
       WORK ORDER MODAL
       ═══════════════════════════════════════════════════════════════════ */
    window.techOpenWorkOrderModal = function(assetTag = '') {
        const modal = document.getElementById('techWorkOrderModal');
        if (!modal) {
            toast('Work Order modal not found.', 'danger');
            return;
        }

        document.getElementById('techWOTitle').value = '';
        document.getElementById('techWOAsset').value = assetTag || '';
        document.getElementById('techWOMake').value = '';
        document.getElementById('techWOModel').value = '';
        document.getElementById('techWOSerial').value = '';
        document.getElementById('techWODescription').value = '';
        document.getElementById('techWOStartDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('techWOEndDate').value = '';
        document.getElementById('techWOPriority').value = 'High';
        document.getElementById('techWOStatus').value = 'Open';
        document.getElementById('techWOId').value = '';
        document.getElementById('techWOEquipmentId').value = '';
        document.getElementById('techWOFailedAssetTag').value = assetTag || '';
        document.getElementById('techWOError').classList.add('d-none');
        document.getElementById('techWOModalTitle').textContent = assetTag ?
            'Create Work Order for Failed Equipment' : 'Add Work Order';

        bootstrap.Modal.getOrCreateInstance(modal).show();

        if (assetTag) {
            fetchEquipmentForWorkOrder(assetTag);
        }
    };

    function fetchEquipmentForWorkOrder(assetTag) {
        const errEl = document.getElementById('techWOError');

        if (!assetTag || !CURRENT_SITE_ID) {
            document.getElementById('techWOEquipmentId').value = '';
            document.getElementById('techWOMake').value = '';
            document.getElementById('techWOModel').value = '';
            return;
        }

        fetch(URL_GET_EQUIPMENT + '?asset_tag=' + encodeURIComponent(assetTag) + '&site_id=' +
                CURRENT_SITE_ID, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
            .then(r => r.json())
            .then(data => {
                if (data.found) {
                    document.getElementById('techWOEquipmentId').value = data.id || '';
                    document.getElementById('techWOMake').value = data.make || '';
                    document.getElementById('techWOModel').value = data.model || '';
                    document.getElementById('techWOSerial').value = data.serial_number || '';
                    errEl.classList.add('d-none');
                } else {
                    document.getElementById('techWOEquipmentId').value = '';
                    document.getElementById('techWOMake').value = '';
                    document.getElementById('techWOModel').value = '';
                    errEl.textContent = 'Asset not found for this site.';
                    errEl.classList.remove('d-none');
                }
            })
            .catch(() => {
                document.getElementById('techWOEquipmentId').value = '';
                document.getElementById('techWOMake').value = '';
                document.getElementById('techWOModel').value = '';
            });
    }

    document.getElementById('techWOAsset')?.addEventListener('change', function() {
        const assetTag = this.value.trim();
        fetchEquipmentForWorkOrder(assetTag);
    });

    document.getElementById('techWOForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const saveBtn = document.getElementById('techWOSaveBtn');
        const errEl = document.getElementById('techWOError');
        errEl.classList.add('d-none');

        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving…';

        const equipmentId = document.getElementById('techWOEquipmentId').value.trim();

        if (!equipmentId) {
            errEl.textContent =
                'Please select a valid asset so the equipment can be linked to this work order.';
            errEl.classList.remove('d-none');
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Work Order';
            return;
        }

        const body = new URLSearchParams({
            [CSRF_NAME]: csrfHash,
            site_id: CURRENT_SITE_ID,
            group_id: CURRENT_GROUP_ID || '',
            equipment_id: equipmentId,
            title: document.getElementById('techWOTitle').value.trim(),
            asset_tag: document.getElementById('techWOAsset').value.trim(),
            serial_number: document.getElementById('techWOSerial').value.trim(),
            priority: document.getElementById('techWOPriority').value,
            status: document.getElementById('techWOStatus').value,
            start_date: document.getElementById('techWOStartDate').value,
            end_date: document.getElementById('techWOEndDate').value,
            description: document.getElementById('techWODescription').value.trim(),
        });

        const URL_WO_CREATE = '<?= site_url('technician/work-orders/create') ?>';

        fetch(URL_WO_CREATE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body.toString()
            })
            .then(r => r.json())
            .then(data => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Work Order';

                if (data.csrf_hash) csrfHash = data.csrf_hash;

                if (data.success || data.id) {
                    bootstrap.Modal.getInstance(document.getElementById('techWorkOrderModal'))
                        .hide();
                    toast('Work order created successfully.', 'success');
                    loadWorkOrders(CURRENT_GROUP_ID || '');
                } else {
                    errEl.textContent = data.message || 'Failed to save work order.';
                    errEl.classList.remove('d-none');
                }
            })
            .catch(err => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Work Order';
                toast('Error: ' + err.message, 'danger');
            });
    });

    /* ═══════════════════════════════════════════════════════════════════
       WORK ORDERS (load and render)
       ═══════════════════════════════════════════════════════════════════ */
    let techWorkOrders = [];

    function loadWorkOrders(groupId) {
        if (!CURRENT_SITE_ID) return;

        fetch('<?= site_url('technician/inspections/groupWorkOrders') ?>?group_id=' +
                encodeURIComponent(groupId || '') + '&site_id=' + CURRENT_SITE_ID, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
            .then(r => r.json())
            .then(data => {
                techWorkOrders = data.data || data.work_orders || [];
                renderWorkOrders(techWorkOrders);
            })
            .catch(() => {
                techWorkOrders = [];
                renderWorkOrders([]);
            });
    }

    function renderWorkOrders(items) {
        const tbody = document.getElementById('techWOBody');
        if (!items.length) {
            tbody.innerHTML =
                '<tr><td colspan="10" class="text-center text-muted py-3">No work orders found.</td></tr>';
            return;
        }
        tbody.innerHTML = items.map((wo) => `
            <tr data-id="${esc(wo.id || '')}">
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-danger tech-wo-delete" data-id="${esc(wo.id || '')}" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </td>
                <td>${esc(wo.id || '')}</td>
                <td>${esc(wo.title || '')}</td>
                <td>${wo.asset_tag ? '<span class="badge text-dark border">' + esc(wo.asset_tag) + '</span>' : '—'}</td>
                <td><span class="badge ${wo.priority === 'High' || wo.priority === 'Urgent' ? 'bg-danger' : wo.priority === 'Medium' ? 'bg-warning text-dark' : 'bg-secondary'}">${esc(wo.priority || '')}</span></td>
                <td>${esc(wo.status || '')}</td>
                <td>${wo.start_date ? esc(wo.start_date.substring(0,10)) : '<span class="text-muted">—</span>'}</td>
                <td>${wo.end_date ? esc(wo.end_date.substring(0,10)) : '<span class="text-muted">—</span>'}</td>
                <td class="small text-muted">${esc(wo.description || '')}</td>
            </tr>`).join('');
    }

    document.getElementById('techWOBody')?.addEventListener('click', function(e) {
        const btn = e.target.closest('.tech-wo-delete');
        if (!btn) return;
        const id = btn.dataset.id;
        if (!confirm('Delete this work order? This cannot be undone.')) return;

        const URL_WO_DELETE = '<?= site_url('technician/work-orders/delete') ?>';

        fetch(URL_WO_DELETE + '/' + id, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(() => {
                btn.closest('tr').remove();
                toast('Work order deleted.', 'warning');
            })
            .catch(() => toast('Delete failed.', 'danger'));
    });

    /* ═══════════════════════════════════════════════════════════════════
       ADD DEVICE MODAL – Result Buttons
       ═══════════════════════════════════════════════════════════════════ */
    const _resultMap = {
        techAddBtnPass: 'Pass',
        techAddBtnFail: 'Fail',
        techAddBtnRepair: 'Repair'
    };
    const _badgeClass = {
        Pass: 'bg-success',
        Fail: 'bg-danger',
        Repair: 'bg-warning text-dark'
    };
    ['techAddBtnPass', 'techAddBtnFail', 'techAddBtnRepair'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function() {
            const res = _resultMap[id];
            document.getElementById('techAddResult').value = res;
            const badge = document.getElementById('techAddResultBadge');
            if (badge) {
                badge.className = 'badge ' + (_badgeClass[res] || 'bg-secondary');
                badge.textContent = 'Selected: ' + res;
            }
            ['techAddBtnPass', 'techAddBtnFail', 'techAddBtnRepair'].forEach(bid => {
                const b = document.getElementById(bid);
                if (b) b.style.outline = (bid === id) ? '3px solid #fff' : '';
            });
        });
    });

    document.getElementById('techAddDeviceForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const result = document.getElementById('techAddResult').value || 'Pass';

        const saveBtn = document.getElementById('techAddSaveBtn');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Saving…';
        }

        function resetSaveBtn() {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fa-solid fa-plus me-2"></i>Save &amp; Record Inspection';
            }
        }

        const body = new URLSearchParams({
            [CSRF_NAME]: csrfHash,
            site_id: CURRENT_SITE_ID,
            asset_tag: document.getElementById('techAddAsset').value.trim(),
            result: result,
            notes: document.getElementById('techAddNotes').value.trim(),
            department: document.getElementById('techAddDept').value.trim(),
            room: document.getElementById('techAddRoom').value.trim(),
            serial_number: document.getElementById('techAddSerial').value.trim(),
            action_performed: document.getElementById('techAddAction')?.value ||
                'Annual Performance Inspection',
            pm_frequency: document.getElementById('techAddPMFreq').value,
            est: document.getElementById('techAddEST').value === 'Yes' ? '1' : '0',
            cal: document.getElementById('techAddCAL').value === 'Yes' ? '1' : '0',
            group_id: CURRENT_GROUP_ID || '',
            asset_not_found: '1',
            manufacturer: document.getElementById('techAddManufacturer').value.trim(),
            model_name: document.getElementById('techAddModel').value.trim(),
            description: document.getElementById('techAddDescription').value.trim(),
        });

        fetch(URL_RECORD, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body.toString()
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (!CURRENT_GROUP_ID) CURRENT_GROUP_ID = data.group_id;
                    resetSaveBtn();
                    bootstrap.Modal.getInstance(document.getElementById('techAddDeviceModal'))
                        .hide();
                    toast('New device added & inspected (' + result + ').', result === 'Pass' ?
                        'success' : 'warning');
                    appendInspectedRow({
                        asset_tag: document.getElementById('techAddAsset').value.trim(),
                        model: document.getElementById('techAddModel').value.trim(),
                        make: document.getElementById('techAddManufacturer').value.trim(),
                        device_type: document.getElementById('techAddType').value.trim(),
                        serial_number: document.getElementById('techAddSerial').value
                            .trim(),
                        department: document.getElementById('techAddDept').value.trim(),
                        location: document.getElementById('techAddRoom').value.trim(),
                        result: result,
                        notes: document.getElementById('techAddNotes').value.trim(),
                        est: document.getElementById('techAddEST').value === 'Yes' ? '1' :
                            '0',
                        cal: document.getElementById('techAddCAL').value === 'Yes' ? '1' :
                            '0',
                    });
                    document.getElementById('techAddDeviceForm').reset();
                } else {
                    resetSaveBtn();
                    const errEl = document.getElementById('techAddDeviceError');
                    let msg = data.message || 'Failed to save.';
                    if (msg.toLowerCase().includes('duplicate') || msg.toLowerCase().includes(
                            'asset_company')) {
                        msg =
                            "Asset # already exists in this site's inventory. Use a different Asset # or scan the existing asset barcode directly from the inspection screen.";
                    }
                    errEl.textContent = msg;
                    errEl.classList.remove('d-none');
                    errEl.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            })
            .catch(err => {
                resetSaveBtn();
                toast('Network error: ' + err.message, 'danger');
            });
    });

    /* ═══════════════════════════════════════════════════════════════════
       TABLE EXPORT
       ═══════════════════════════════════════════════════════════════════ */
    window.techExportTable = function(type, tbodyId, filename) {
        filename = filename || 'inspection-export';
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => r.style.display !== 'none');
        const matrix = rows.map(row =>
            Array.from(row.querySelectorAll('td')).map(td => td.innerText.replace(/\n/g, ' ').trim())
        );

        if (type === 'copy') {
            const text = matrix.map(r => r.join('\t')).join('\n');
            navigator.clipboard.writeText(text).then(() => toast('Copied to clipboard!', 'success'));
            return;
        }

        if (type === 'csv') {
            const csv = matrix.map(r => r.map(c => '"' + c.replace(/"/g, '""') + '"').join(',')).join('\n');
            techDownloadFile(csv, filename + '.csv', 'text/csv');
            return;
        }

        if (type === 'excel') {
            let xml =
                '<?xml version="1.0"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><Worksheet ss:Name="Sheet1"><Table>';
            matrix.forEach(row => {
                xml += '<Row>' + row.map(c => '<Cell><Data ss:Type="String">' + c.replace(/&/g,
                        '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</Data></Cell>')
                    .join('') + '</Row>';
            });
            xml += '</Table></Worksheet></Workbook>';
            techDownloadFile(xml, filename + '.xls', 'application/vnd.ms-excel');
            return;
        }

        if (type === 'pdf') {
            const groupId = CURRENT_GROUP_ID || TECH_CURRENT_REPORT_GROUP_ID;
            if (groupId) {
                window.open(TECH_REPORT_PREVIEW_URL + '/' + encodeURIComponent(groupId), '_blank');
            } else {
                toast('No inspection loaded for PDF export.', 'warning');
            }
        }
    };

    function techDownloadFile(content, filename, mimeType) {
        const blob = new Blob([content], {
            type: mimeType
        });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(a.href);
    }

    /* ═══════════════════════════════════════════════════════════════════
       SEARCH FILTERS
       ═══════════════════════════════════════════════════════════════════ */
    function filterTable(inputId, tbodyId) {
        document.getElementById(inputId)?.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#' + tbodyId + ' tr').forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
    filterTable('techNotInspSearch', 'techNotInspBody');
    filterTable('techInspectedSearch', 'techInspectedBody');
    filterTable('techInventorySearch', 'techInventoryBody');
    filterTable('techArchivedSearch', 'techArchivedBody');
    filterTable('techWOSearch', 'techWOBody');

    /* ═══════════════════════════════════════════════════════════════════
       ARCHIVED ITEMS
       ═══════════════════════════════════════════════════════════════════ */
    function renderArchivedItems(items) {
        const tbody = document.getElementById('techArchivedBody');
        const archived = items.filter(eq =>
            (eq.status || '').toLowerCase() === 'archived' ||
            (eq.deleted_at && eq.deleted_at !== null && eq.deleted_at !== '')
        );
        if (!archived.length) {
            tbody.innerHTML =
                '<tr><td colspan="5" class="text-center text-muted">No archived items found.</td></tr>';
            return;
        }
        tbody.innerHTML = archived.map(eq => `
            <tr>
                <td>${esc(eq.asset_tag)}</td>
                <td>${esc(eq.model || eq.make || '')}</td>
                <td>${esc(eq.device_type || '')}</td>
                <td>${esc(eq.department || '')}${eq.location ? ' / ' + esc(eq.location) : ''}</td>
                <td><span class="badge bg-dark">Archived</span></td>
            </tr>`).join('');
    }

    /* ═══════════════════════════════════════════════════════════════════
       INSPECTION REPORTS
       ═══════════════════════════════════════════════════════════════════ */
    const TECH_REPORT_DATA_URL = '<?= site_url('technician/inspections/reportData') ?>';
    const TECH_REPORT_PDF_URL = '<?= site_url('technician/inspections/reportPdf') ?>';
    const TECH_REPORT_PREVIEW_URL = '<?= site_url('technician/inspections/reportPreview') ?>';
    let TECH_CURRENT_REPORT_GROUP_ID = null;

    document.getElementById('tech-reports-tab')?.addEventListener('shown.bs.tab', function() {
        const groupId = CURRENT_GROUP_ID || TECH_CURRENT_REPORT_GROUP_ID;
        if (!groupId) {
            document.getElementById('techReportsTabContent').innerHTML =
                '<div class="alert alert-info">No inspection loaded yet. Please open or complete an inspection first.</div>';
            return;
        }
        loadTechReport(groupId);
    });

    function loadTechReport(groupId) {
        TECH_CURRENT_REPORT_GROUP_ID = groupId;
        const container = document.getElementById('techReportsTabContent');
        container.innerHTML =
            '<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>' +
            '<p class="mt-3 text-muted">Loading report data…</p></div>';

        fetch(TECH_REPORT_DATA_URL + '?group_id=' + encodeURIComponent(groupId), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    container.innerHTML =
                        '<div class="alert alert-warning">No report data found for this inspection.</div>';
                    return;
                }
                const html = techGenerateReportHTML(data.latest, data.rows, data.group_id || groupId);
                container.innerHTML = html;
                const rc = document.getElementById('techReportContent');
                if (rc) rc.innerHTML = html;
            })
            .catch(err => {
                container.innerHTML = '<div class="alert alert-danger">Error loading report: ' + esc(err
                    .message) + '</div>';
            });
    }

    window.techOpenInspectionReport = function(groupId) {
        TECH_CURRENT_REPORT_GROUP_ID = groupId;
        const reportContent = document.getElementById('techReportContent');
        if (reportContent) {
            reportContent.innerHTML =
                '<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>' +
                '<p class="mt-3 text-muted">Loading report…</p></div>';
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('techInspReportModal')).show();
        fetch(TECH_REPORT_DATA_URL + '?group_id=' + encodeURIComponent(groupId), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                const html = data.success ?
                    techGenerateReportHTML(data.latest, data.rows, data.group_id || groupId) :
                    '<div class="alert alert-warning">No report data found.</div>';
                if (reportContent) reportContent.innerHTML = html;
                const container = document.getElementById('techReportsTabContent');
                if (container) container.innerHTML = html;
            })
            .catch(() => {
                if (reportContent) reportContent.innerHTML =
                    '<div class="alert alert-danger">Failed to load report.</div>';
            });
    };

    document.getElementById('techPreviewReportBtn')?.addEventListener('click', function() {
        const groupId = CURRENT_GROUP_ID || TECH_CURRENT_REPORT_GROUP_ID;
        if (!groupId) {
            toast('No inspection loaded.', 'warning');
            return;
        }
        window.open(TECH_REPORT_PREVIEW_URL + '/' + encodeURIComponent(groupId), '_blank');
    });

    document.getElementById('techDownloadReportBtn')?.addEventListener('click', function() {
        const groupId = CURRENT_GROUP_ID || TECH_CURRENT_REPORT_GROUP_ID;
        if (!groupId) {
            toast('No inspection loaded.', 'warning');
            return;
        }
        window.open(TECH_REPORT_PDF_URL + '/' + encodeURIComponent(groupId), '_blank');
    });

    document.getElementById('techPreviewPDFBtn')?.addEventListener('click', function() {
        const groupId = TECH_CURRENT_REPORT_GROUP_ID || CURRENT_GROUP_ID;
        if (groupId) window.open(TECH_REPORT_PREVIEW_URL + '/' + encodeURIComponent(groupId), '_blank');
    });

    document.getElementById('techDownloadPDFBtn')?.addEventListener('click', function() {
        const groupId = TECH_CURRENT_REPORT_GROUP_ID || CURRENT_GROUP_ID;
        if (groupId) window.open(TECH_REPORT_PDF_URL + '/' + encodeURIComponent(groupId), '_blank');
    });

    function techGenerateReportHTML(latest, rows, groupId) {
        const rowsArr = (rows || []).slice().sort((a, b) => {
            const order = {
                Fail: 0,
                Repair: 1,
                Pass: 2
            };
            return (order[a.result || a.status] ?? 3) - (order[b.result || b.status] ?? 3);
        });

        const groups = {};
        const groupOrder = [];
        rowsArr.forEach(r => {
            const st = r.result || r.status || 'Unknown';
            if (!groups[st]) {
                groups[st] = [];
                groupOrder.push(st);
            }
            groups[st].push(r);
        });

        const groupMeta = {
            Fail: {
                label: 'Failed - ⚠ Attention Required',
                cls: 'bg-danger',
                icon: 'fa-xmark'
            },
            Repair: {
                label: 'Repair',
                cls: 'bg-warning text-dark',
                icon: 'fa-wrench'
            },
            Pass: {
                label: 'Passed',
                cls: 'bg-success',
                icon: 'fa-check'
            },
        };

        let rowsHtml = '';
        if (!rowsArr.length) {
            rowsHtml =
                '<tr><td colspan="12" class="text-center text-muted py-3">No inspections found.</td></tr>';
        } else {
            groupOrder.forEach(st => {
                const m = groupMeta[st] || {
                    label: st,
                    cls: 'bg-secondary',
                    icon: 'fa-circle'
                };
                rowsHtml += `<tr style="background:#f8fafc;"><td colspan="12" class="py-2 px-3">
                    <span class="badge ${m.cls} me-2"><i class="fa-solid ${m.icon} me-1"></i>${m.label}</span>
                    <span class="text-muted small">${groups[st].length} device${groups[st].length !== 1 ? 's' : ''}</span>
                    </td></tr>`;
                groups[st].forEach(r => {
                    const badge = r.result === 'Pass' || r.status === 'Pass' ?
                        '<span class="text-success fw-semibold"><i class="fa-solid fa-check me-1"></i>Pass</span>' :
                        r.result === 'Fail' || r.status === 'Fail' ?
                        '<span class="text-danger fw-semibold"><i class="fa-solid fa-xmark me-1"></i>Fail</span>' :
                        '<span class="text-warning fw-semibold"><i class="fa-solid fa-wrench me-1"></i>' +
                        esc(r.result || r.status) + '</span>';

                    const attentionBadge = (r.result === 'Fail' || r.status === 'Fail') ?
                        '<div class="attention-required mt-1">Attention Required - Equipment Failure</div>' :
                        '';

                    rowsHtml += `<tr>
                        <td>${badge}</td>
                        <td>${esc(r.customer_name || r.site_name || '—')}</td>
                        <td>${esc(r.model || '—')}</td>
                        <td>${esc(r.device_type || '—')}</td>
                        <td>${esc(r.serial_number || 'N/A')}</td>
                        <td>${esc(r.action_performed || '—')}</td>
                        <td>${esc(r.asset_tag || '—')}</td>
                        <td>${esc(r.dept || '—')}</td>
                        <td>${esc(r.room || '—')}</td>
                        <td>${esc(r.technician_name || 'N/A')}</td>
                        <td class="small">${r.inspection_date ? r.inspection_date.substring(0,10) : '—'}</td>
                        <td class="small text-muted">${esc(r.notes || '')}</td>
                    </tr>${attentionBadge}`;
                });
            });
        }

        return `<section><h5 class="fw-semibold mb-3">Inspection Report Overview</h5>
            <div class="alert alert-danger" role="alert">
                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                <strong>Critical:</strong> Review all failed items marked as "Attention Required" below. Failed equipment requires immediate attention and work order creation.
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-striped">
                    <thead><tr>
                        <th>Result</th><th>Customer</th><th>Model</th><th>Type</th><th>S/N</th>
                        <th>Action Performed</th><th>Asset #</th><th>Dept</th><th>Room</th>
                        <th>Tech</th><th>Date</th><th>Notes</th>
                    </tr></thead>
                    <tbody>${rowsHtml}</tbody>
                </table>
            </div>
            </section>`;
    }

});
</script>

<?= $this->endSection() ?>