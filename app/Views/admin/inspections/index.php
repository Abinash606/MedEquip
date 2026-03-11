<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 topbar">
            <h3 class="fw-bold mb-0">Inspection Reports</h3>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addInspectionModal"><i class="fa-solid fa-plus me-2"></i> New Inspection</button>
        </div>
        <!-- Download area for reports and history -->
         <div class="content">
        <div class="glass-card mb-4 p-3">
            <h5 class="fw-bold mb-2">Reports &amp; History</h5>
            <p class="text-muted small mb-3">Download inspection reports and repair work order history for each site.</p>
            <button class="btn btn-primary"><i class="fa-solid fa-download me-2"></i> Download Site Reports</button>
        </div>
        <!-- Latest added device summary -->
        <div class="glass-card mb-4 p-3">
            <h5 class="fw-bold mb-2">Latest Added Device</h5>
            <p class="text-muted small mb-3">Review the most recently added device to an inspection and make edits.</p>
            <div class="table-responsive">
                <table class="table table-sm table-hover service-table align-middle">
                    <thead class="">
                        <tr>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Type</th>
                            <th>S/N</th>
                            <th>Action Performed</th>
                            <th>Asset #</th>
                            <th>Department</th>
                            <th>Room</th>
                            <th>Tech</th>
                            <th>EST</th>
                            <th>CAL</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><a href="#" class="text-primary"><i class="fa-solid fa-pen-to-square"></i></a></td>
                            <td>Midmark - M9</td>
                            <td>Autoclave</td>
                            <td>1234567890</td>
                            <td>Installed PM Kit</td>
                            <td>7645867</td>
                            <td>Pediatrics</td>
                            <td>9</td>
                            <td>Tony Robinson</td>
                            <td>Yes</td>
                            <td>Yes</td>
                            <td>Installed PM Kit. Completed EST/Manufacturer PM Procedure. Passed and returned to service.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Inspection overview table -->
        <div class="glass-card p-3">
            <h5 class="fw-bold mb-2">Inspection Report Overview</h5>
            <p class="text-muted small mb-3">Detailed summary of inspection reports for your records.</p>
            <div class="table-responsive">
                <table class="table table-sm service-table align-middle">
                    <thead class="">
                        <tr>
                            <th>Pass/Fail</th>
                            <th>Customer Site</th>
                            <th>Model</th>
                            <th>Type</th>
                            <th>S/N</th>
                            <th>Action Performed</th>
                            <th>Asset #</th>
                            <th>Department</th>
                            <th>Room</th>
                            <th>EST</th>
                            <th>CAL</th>
                            <th>Tech</th>
                            <th>Inspection Date</th>
                            <th>Battery Expiration Date</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-success">Pass</span></td>
                            <td>AFC Urgent Care - Phoenixville</td>
                            <td>Health O Meter - 500KL</td>
                            <td>Digital Adult Scale</td>
                            <td>5020232341158</td>
                            <td>Adult Scale Calibration</td>
                            <td>12794158</td>
                            <td>A Hallway</td>
                            <td>—</td>
                            <td>Yes</td>
                            <td>Yes</td>
                            <td>Anthony Wright</td>
                            <td>11/26/2024</td>
                            <td>—</td>
                            <td>Performed linear calibration check. Scale passed within ±0.1% whichever is greater.</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-success">Pass</span></td>
                             <td>AFC Urgent Care - Phoenixville</td>
                            <td>LUMF Medical - 4040-450-100</td>
                            <td>Exam Bed</td>
                            <td>554233-8938</td>
                            <td>EST</td>
                            <td>127941305</td>
                            <td>Exam A1</td>
                            <td>—</td>
                            <td>Yes</td>
                            <td>Yes</td>
                            <td>Anthony Wright</td>
                            <td>11/26/2024</td>
                            <td>—</td>
                            <td>Completed annual inspection; system passed.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
		</div>
		<!-- Modal for Inspection Workflow -->
<div class="modal fade" id="addInspectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">New Inspection Workflow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Asset Information -->
                <div class="inspection-step" id="inspection-step-1">
                    <h5 class="fw-bold">Step 1: Enter Asset Information</h5>
                    <!-- Added customer site selection to enable site‑level inventory lookups -->
                    <div class="mb-3">
                        <label for="inspection-site" class="form-label">Customer Site</label>
                        <select class="form-select" id="inspection-site">
                            <option value="" disabled selected>Select site</option>
                            <!-- Options will be populated via JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="asset-barcode" class="form-label">Asset/Barcode Number</label>
                        <input type="text" class="form-control" id="asset-barcode" placeholder="Enter or scan asset/barcode">
                    </div>
                    <p class="text-muted small">The system will search the selected site's inventory for a matching asset.</p>
                </div>

                <!-- Step 2: Verification (Not Found) -->
                <div class="inspection-step" id="inspection-step-2" style="display: none;">
                    <h5 class="fw-bold">Step 2: Asset Verification (Not Found)</h5>
                    <p class="text-danger">Asset not found. Please enter the model number to proceed.</p>
                    <div class="mb-3">
                        <label for="model-search" class="form-label">Search Model</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="model-search" placeholder="Search for model...">
                            <button class="btn btn-outline-secondary" type="button">Search</button>
                        </div>
                        <div class="list-group mt-2" style="max-height: 150px; overflow-y: auto;">
                            <!-- Search results will populate here dynamically based on user input -->
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Manufacturer</label><input type="text" class="form-control" id="new-manufacturer" value="American Optical Company" readonly></div>
                        <div class="col-md-6"><label class="form-label">Model</label><input type="text" class="form-control" id="new-model" readonly></div>
                        <div class="col-md-6"><label class="form-label">Model Type</label><input type="text" class="form-control" id="new-model-type" value="Microscope" readonly></div>
                        <div class="col-md-6"><label class="form-label">Serial #</label><input type="text" class="form-control" placeholder="Enter Serial Number"></div>
                    </div>
                </div>

                <!-- Step 3: Inspection Details -->
                <div class="inspection-step" id="inspection-step-3" style="display: none;">
                    <h5 class="fw-bold">Step 3: Enter Inspection Details</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Customer</label><input type="text" id="inspection-customer" class="form-control" value="AFC Urgent Care - Phoenixville" readonly></div>
                        <div class="col-md-6"><label class="form-label">Model</label><input type="text" class="form-control" value="Defibtech - DDU-C2300EN - AED" readonly></div>
                        <div class="col-md-6"><label class="form-label">Department</label><input type="text" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Room</label><input type="text" class="form-control" value="Nurse Area"></div>
                        <div class="col-md-6"><label class="form-label">Serial #</label><input type="text" class="form-control" value="102048177" readonly></div>
                        <div class="col-md-6"><label class="form-label">Asset ID</label><input type="text" class="form-control" value="13076251" readonly></div>
                        <div class="col-md-6"><label class="form-label">Manufacture PM Frequency (Days)</label><select class="form-select"><option>12 Month</option></select></div>
                        <div class="col-md-6"><label class="form-label">Action Performed</label><select class="form-select"><option>Annual Performance Inspection</option></select></div>
                        <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" rows="3">Verified Pad and battery expiration date.\nVerified energy delivered using defib analyzer.\nSystem Passed.</textarea></div>
                    </div>
                    <div class="mt-3 text-center">
                        <p class="small text-muted">Please add detailed description of actions taken, recommended parts and applicable photos.</p>
                        <div class="btn-group">
                           <button type="button" class="btn btn-success" data-bs-dismiss="modal">Pass Inspection</button>
                           <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fail Inspection</button>
                           <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Fail Inspection & Open Work Order</button>
                           <button type="button" class="btn btn-info" data-bs-dismiss="modal">Repair Inspection</button>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <button type="button" class="btn btn-outline-primary" onclick="goToStep(1)">Enter Next Device</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                     <button type="button" class="btn btn-secondary" id="inspection-back-btn" onclick="handleBack()" style="display: none;">Back</button>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="inspection-next-btn" onclick="handleNext()">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>