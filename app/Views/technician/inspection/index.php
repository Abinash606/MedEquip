<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<!-- Inspections view -->
<section id="inspections" class="view-section active">
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Upcoming Inspections</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#inspectionWizardModal">
                        <i class="fa-solid fa-plus me-2"></i> Add Inspection
                    </button>
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

<!-- ================================================
     INSPECTION WIZARD MODAL (Static Version)
     ================================================ -->
<div class="modal fade" id="inspectionWizardModal" tabindex="-1" aria-labelledby="inspectionWizardLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Wizard Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-check me-2"></i>
                    <span id="wizardTitle">New Inspection</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <!-- Hidden fields for form submission -->
                <input type="hidden" id="wiz-equipment-id" name="equipment_id" value="">
                <input type="hidden" id="wiz-site-id" name="site_id" value="1">
                <input type="hidden" id="wiz-group-id" name="group_id" value="">

                <!-- Inspection Queue Display -->
                <div id="inspectionQueueContainer" style="display:none;">
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-weight:600;">
                                <i class="fa fa-list me-2"></i>
                                <span id="queueCount">0</span> device(s) in queue
                            </span>
                        </div>
                        <div id="inspectionQueue" style="max-height:200px; overflow-y:auto;"></div>
                    </div>
                </div>

                <!-- ─── STEP 1: Enter Serial Number ─── -->
                <div class="wizard-step active" id="wizardStep1">
                    <h5>Step 1: Enter Serial Number</h5>
                    <p class="site-label"><strong>Site:</strong> Sample Hospital</p>

                    <label for="wiz-serial-number" class="form-label">Serial Number</label>
                    <input type="text" class="form-control" id="wiz-serial-number"
                        placeholder="Enter or scan serial number" autocomplete="off">
                    <p class="helper-text mt-2 text-muted small">
                        Enter the serial number from the equipment label. If found, details will be automatically
                        filled.
                    </p>

                    <div class="wizard-footer mt-4">
                        <div class="d-flex justify-content-between">
                            <div class="left-btns"></div>
                            <div class="right-btns">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary ms-2" id="wizStep1Next">Next</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── STEP 2: Asset Verification (Not Found) ─── -->
                <div class="wizard-step" id="wizardStep2" style="display:none;">
                    <h5>Step 2: Asset Verification (Not Found)</h5>
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        Serial number not found. Please search for the device model in the equipment database.
                    </div>

                    <label for="wiz-search-model" class="form-label">Search Model</label>
                    <input type="text" class="form-control mb-3" id="wiz-search-model" placeholder="Search for model..."
                        autocomplete="off">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="wiz-s2-manufacturer" class="form-label">Manufacturer</label>
                            <input type="text" class="form-control" id="wiz-s2-manufacturer">
                        </div>
                        <div class="col-md-6">
                            <label for="wiz-s2-model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="wiz-s2-model">
                        </div>
                        <div class="col-md-6">
                            <label for="wiz-s2-description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="wiz-s2-description">
                        </div>
                        <div class="col-md-6">
                            <label for="wiz-s2-serial" class="form-label">Serial #</label>
                            <input type="text" class="form-control" id="wiz-s2-serial" readonly>
                        </div>
                    </div>

                    <div class="wizard-footer mt-4">
                        <div class="d-flex justify-content-between">
                            <div class="left-btns">
                                <button type="button" class="btn btn-outline-secondary" id="wizStep2Back">Back</button>
                            </div>
                            <div class="right-btns">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary ms-2" id="wizStep2Next">Next</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── STEP 3: Enter Inspection Details ─── -->
                <div class="wizard-step" id="wizardStep3" style="display:none;">
                    <h5>Step 3: Enter Inspection Details</h5>

                    <!-- Row 1: Model | Description | Serial # (read-only, pre-filled) -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label for="wiz-s3-model" class="form-label">Model</label>
                            <input type="text" class="form-control bg-light" id="wiz-s3-model" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="wiz-s3-description" class="form-label">Description</label>
                            <input type="text" class="form-control bg-light" id="wiz-s3-description" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="wiz-s3-serial" class="form-label">Serial #</label>
                            <input type="text" class="form-control bg-light" id="wiz-s3-serial" readonly>
                        </div>
                    </div>

                    <!-- Row 2: Asset ID | Department | Location / Room (read-only, pre-filled) -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label for="wiz-s3-assetid" class="form-label">Asset ID</label>
                            <input type="text" class="form-control bg-light" id="wiz-s3-assetid" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="wiz-s3-department" class="form-label">Department</label>
                            <input type="text" class="form-control bg-light" id="wiz-s3-department" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="wiz-s3-location" class="form-label">Location / Room</label>
                            <input type="text" class="form-control bg-light" id="wiz-s3-location" readonly>
                        </div>
                    </div>

                    <!-- Row 3: PM Service Frequency | Inspection Type -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="wiz-s3-pmfreq" class="form-label">PM Service Frequency</label>
                            <select class="form-select" id="wiz-s3-pmfreq" name="pm_frequency">
                                <option value="">Select frequency</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Semi-Annual">Semi-Annual</option>
                                <option value="Annual">Annual</option>
                                <option value="As Needed">As Needed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="wiz-s3-insptype" class="form-label">Inspection Type</label>
                            <select class="form-select" id="wiz-s3-insptype" name="inspection_type">
                                <option value="">Select type</option>
                                <option value="Preventive Maintenance">Preventive Maintenance</option>
                                <option value="Corrective Maintenance">Corrective Maintenance</option>
                                <option value="Safety Inspection">Safety Inspection</option>
                                <option value="Compliance Check">Compliance Check</option>
                                <option value="Initial Setup">Initial Setup</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Technician | Inspection Date -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="wiz-s3-technician" class="form-label">Technician</label>
                            <select class="form-select" id="wiz-s3-technician" name="technician_id">
                                <option value="">-- Select Technician --</option>
                                <option value="1">John Smith</option>
                                <option value="2">Jane Doe</option>
                                <option value="3">Mike Johnson</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="wiz-s3-inspdate" class="form-label">Inspection Date</label>
                            <input type="date" class="form-control" id="wiz-s3-inspdate" name="scheduled_at"
                                value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <!-- Row 5: Service Notes (full width) -->
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <label for="wiz-s3-notes" class="form-label">Service Notes</label>
                            <textarea class="form-control" id="wiz-s3-notes" name="notes" rows="3"
                                placeholder="Enter any service notes or observations..."></textarea>
                        </div>
                    </div>

                    <!-- Row 6: Status | Device Complete -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="wiz-s3-status" class="form-label">Status</label>
                            <select class="form-select" id="wiz-s3-status" name="status">
                                <option value="Pass">Pass</option>
                                <option value="Fail">Fail</option>
                                <option value="Repair">Repair</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="wiz-s3-devicecomplete" class="form-label">Device Complete</label>
                            <select class="form-select" id="wiz-s3-devicecomplete" name="device_complete">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Helper text -->
                    <p class="text-center text-muted mt-3 mb-2" style="font-size:0.9rem;">
                        Add device notes and complete status, or use quick action buttons below.
                    </p>

                    <!-- Outcome buttons: Pass | Fail | Repair -->
                    <div class="d-flex gap-2 justify-content-center mb-3">
                        <button type="button" class="btn btn-success" id="wizBtnPass">
                            <i class="fa fa-check me-1"></i> Pass Inspection
                        </button>
                        <button type="button" class="btn btn-danger" id="wizBtnFail">
                            <i class="fa fa-times me-1"></i> Fail Inspection
                        </button>
                        <button type="button" class="btn btn-warning" id="wizBtnRepair">
                            <i class="fa fa-wrench me-1"></i> Repair Inspection
                        </button>
                    </div>

                    <!-- Enter Next Device button -->
                    <div class="d-flex justify-content-center mb-3">
                        <button type="button" class="btn btn-primary" id="wizBtnNextDevice">
                            <i class="fa fa-plus-circle me-1"></i> Add to Queue & Next Device
                        </button>
                    </div>

                    <!-- Footer with Back / Cancel / Complete -->
                    <div class="wizard-footer mt-4">
                        <div class="d-flex justify-content-between">
                            <div class="left-btns">
                                <button type="button" class="btn btn-outline-secondary" id="wizStep3Back">Back</button>
                            </div>
                            <div class="right-btns">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-success ms-2" id="wizBtnComplete"
                                    style="display:none;">
                                    <i class="fa fa-check-double me-1"></i>Complete Inspections
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- end modal-body -->
        </div><!-- end modal-content -->
    </div><!-- end modal-dialog -->
</div>

<script>
    // Simple wizard navigation (static version)
    document.addEventListener('DOMContentLoaded', function() {
        const steps = document.querySelectorAll('.wizard-step');
        let currentStep = 0;

        // Step 1 Next button
        document.getElementById('wizStep1Next')?.addEventListener('click', function() {
            const serialNumber = document.getElementById('wiz-serial-number').value.trim();

            if (!serialNumber) {
                alert('Please enter a serial number');
                return;
            }

            // Hide step 1, show step 2 (in real app, this would check if serial exists)
            steps[0].style.display = 'none';
            steps[1].style.display = 'block';
            steps[1].classList.add('active');

            // Populate serial in step 2
            document.getElementById('wiz-s2-serial').value = serialNumber;
        });

        // Step 2 Back button
        document.getElementById('wizStep2Back')?.addEventListener('click', function() {
            steps[1].style.display = 'none';
            steps[0].style.display = 'block';
        });

        // Step 2 Next button
        document.getElementById('wizStep2Next')?.addEventListener('click', function() {
            steps[1].style.display = 'none';
            steps[2].style.display = 'block';

            // Copy values to step 3 readonly fields
            document.getElementById('wiz-s3-model').value = document.getElementById('wiz-s2-model').value;
            document.getElementById('wiz-s3-description').value = document.getElementById(
                'wiz-s2-description').value;
            document.getElementById('wiz-s3-serial').value = document.getElementById('wiz-s2-serial').value;
            document.getElementById('wiz-s3-assetid').value = '';
            document.getElementById('wiz-s3-department').value = '';
            document.getElementById('wiz-s3-location').value = '';
        });

        // Step 3 Back button
        document.getElementById('wizStep3Back')?.addEventListener('click', function() {
            steps[2].style.display = 'none';
            steps[1].style.display = 'block';
        });

        // Quick action buttons
        document.getElementById('wizBtnPass')?.addEventListener('click', function() {
            document.getElementById('wiz-s3-status').value = 'Pass';
            document.getElementById('wiz-s3-devicecomplete').value = 'Yes';
        });

        document.getElementById('wizBtnFail')?.addEventListener('click', function() {
            document.getElementById('wiz-s3-status').value = 'Fail';
            document.getElementById('wiz-s3-devicecomplete').value = 'No';
        });

        document.getElementById('wizBtnRepair')?.addEventListener('click', function() {
            document.getElementById('wiz-s3-status').value = 'Repair';
            document.getElementById('wiz-s3-devicecomplete').value = 'No';
        });

        // Next Device button
        document.getElementById('wizBtnNextDevice')?.addEventListener('click', function() {
            alert('Device added to queue! (This will work dynamically later)');
            // Reset to step 1
            steps[2].style.display = 'none';
            steps[0].style.display = 'block';
            document.getElementById('wiz-serial-number').value = '';
        });

        // Complete button
        document.getElementById('wizBtnComplete')?.addEventListener('click', function() {
            alert('Inspection(s) completed! (This will submit the form dynamically later)');
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('inspectionWizardModal'));
            modal?.hide();
        });

        // Reset wizard when modal closes
        document.getElementById('inspectionWizardModal')?.addEventListener('hidden.bs.modal', function() {
            steps.forEach((step, index) => {
                step.style.display = index === 0 ? 'block' : 'none';
            });
            document.getElementById('wiz-serial-number').value = '';
        });
    });
</script>

<style>
    .wizard-step {
        padding: 1rem 0;
    }

    .helper-text {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .site-label {
        margin-bottom: 1rem;
        padding: 0.5rem;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
    }

    .bg-light {
        background-color: #e9ecef !important;
    }
</style>

<?= $this->endSection() ?>