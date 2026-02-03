<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }
    
    .site-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 600;
        color: white;
    }
    
    .nav-tabs {
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6b7280;
        padding: 1rem 1.5rem;
        font-weight: 500;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }
    
    .nav-tabs .nav-link:hover {
        border-bottom: 3px solid #d1d5db;
    }
    
    .nav-tabs .nav-link.active {
        color: #2563eb;
        border-bottom: 3px solid #2563eb;
        background: transparent;
    }
    
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-block;
    }
    
    .status-ready, .status-completed {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-need-attention {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .status-pending, .status-open {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-scheduled, .status-in-progress {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .btn-action {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 4px;
        margin: 0 0.25rem;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    .dataTables_wrapper .dt-buttons {
        margin-bottom: 1rem;
    }
    
    .dt-button {
        background: white !important;
        border: 1px solid #d1d5db !important;
        color: #374151 !important;
        padding: 0.5rem 1rem !important;
        border-radius: 6px !important;
        margin-right: 0.5rem !important;
    }
    
    .dt-button:hover {
        background: #f3f4f6 !important;
    }

    /* ─── Multi-Step Wizard Styles ─── */
    .wizard-step {
        display: none;
    }
    .wizard-step.active {
        display: block;
    }

    .wizard-step h5 {
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: #1e293b;
    }

    .wizard-step .step-subtitle {
        color: #64748b;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .wizard-step .site-label {
        font-size: 0.9rem;
        color: #475569;
        margin-bottom: 1rem;
    }
    .wizard-step .site-label strong {
        color: #1e293b;
    }

    .wizard-step .helper-text {
        font-size: 0.82rem;
        color: #64748b;
        margin-top: 0.4rem;
    }

    .asset-not-found-alert {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .step3-readonly-row .form-control {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #475569;
    }

    .inspection-outcome-btns {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .btn-pass {
        background-color: #16a34a;
        color: #fff;
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
    }
    .btn-pass:hover { background-color: #15803d; }

    .btn-fail {
        background-color: #dc2626;
        color: #fff;
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
    }
    .btn-fail:hover { background-color: #b91c1c; }

    .btn-repair {
        background-color: #0ea5e9;
        color: #fff;
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
    }
    .btn-repair:hover { background-color: #0284c7; }

    .btn-next-device {
        display: block;
        margin: 1rem auto 0;
        background: transparent;
        border: 2px solid #334155;
        color: #334155;
        padding: 0.4rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
    }
    .btn-next-device:hover {
        background: #f1f5f9;
    }

    .wizard-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }
    .wizard-footer .left-btns { display: flex; gap: 0.5rem; }
    .wizard-footer .right-btns { display: flex; gap: 0.5rem; }

    .modal-header-wizard {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        border-radius: 0.375rem 0.375rem 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header-wizard h5 {
        margin: 0;
        color: #fff;
        font-weight: 600;
    }
    .modal-header-wizard .btn-close {
        filter: brightness(0) invert(1);
        margin: 0;
    }
</style>

<!-- Back Button -->
<button class="btn btn-secondary mb-3" onclick="window.location.href='<?= site_url('admin/sites') ?>'">
    <i class="fa fa-arrow-left me-2"></i> Back to Sites
</button>

<!-- Site Information Card -->
<div class="glass-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-auto me-4">
            <div class="site-avatar" id="site-details-logo">
                <?php 
                $nameParts = explode(' ', $site['name']);
                $initials = '';
                if (count($nameParts) >= 2) {
                    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
                } else {
                    $initials = strtoupper(substr($site['name'], 0, 2));
                }
                echo $initials;
                ?>
            </div>
        </div>
        <div class="col-md">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Site Name:</strong> <span id="site-details-name"><?= esc($site['name']) ?></span></p>
                    <p><strong>Site ID:</strong> <span id="site-details-id"><?= esc($site['id']) ?></span></p>
                    <p><strong>Site Contact Name:</strong> <span id="site-details-contact-name"><?= esc($site['contact_name'] ?? 'N/A') ?></span></p>
                    <p><strong>Site Email:</strong> <span id="site-details-email"><?= esc($site['email'] ?? 'N/A') ?></span></p>
                    <p><strong>Site Phone Number:</strong> <span id="site-details-phone"><?= esc($site['phone'] ?? 'N/A') ?></span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Customer Name:</strong> <span id="site-details-customer-name"><?= esc($customer['name'] ?? 'N/A') ?></span></p>
                    <p><strong>Site Address:</strong> <span id="site-details-address"><?= esc($site['address'] ?? 'N/A') ?>, <?= esc($site['city'] ?? '') ?></span></p>
                    <p><strong>State:</strong> <span id="site-details-state"><?= esc($site['state'] ?? 'N/A') ?></span></p>
                    <p><strong>Zip code:</strong> <span id="site-details-zip"><?= esc($site['zip'] ?? 'N/A') ?></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs" id="site-details-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#equipment" type="button" role="tab" aria-controls="equipment" aria-selected="true">
            <i class="fa fa-desktop me-2"></i> Equipment
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="inspections-tab" data-bs-toggle="tab" data-bs-target="#inspections" type="button" role="tab" aria-controls="inspections" aria-selected="false">
            <i class="fa fa-clipboard-check me-2"></i> Inspections
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="work-orders-tab" data-bs-toggle="tab" data-bs-target="#work-orders" type="button" role="tab" aria-controls="work-orders" aria-selected="false">
            <i class="fa fa-wrench me-2"></i> Work Orders
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="site-details-tabs-content">
    
    <!-- Equipment Tab -->
    <div class="tab-pane fade show active" id="equipment" role="tabpanel" aria-labelledby="equipment-tab">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Equipment List</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                    <i class="fa fa-plus me-2"></i> Add Equipment
                </button>
            </div>
            <table id="equipment-datatable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Asset Tag</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Serial Number</th>
                        <th>Device Type</th>
                        <th>Location or Room</th>
                        <th>Department</th>
                        <th>Device Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipment)): ?>
                        <?php foreach ($equipment as $eq): ?>
                            <tr>
                                <td><?= esc($eq['asset_tag']) ?></td>
                                <td><?= esc($eq['make'] ?? 'N/A') ?></td>
                                <td><?= esc($eq['model'] ?? 'N/A') ?></td>
                                <td><?= esc($eq['serial_number'] ?? 'N/A') ?></td>
                                <td><?= esc($eq['device_type'] ?? 'N/A') ?></td>
                                <td><?= esc($eq['location'] ?? 'N/A') ?></td>
                                <td><?= esc($eq['department'] ?? 'N/A') ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'status-badge ';
                                    $status = strtolower($eq['status'] ?? 'pending');
                                    if ($status === 'ready') {
                                        $statusClass .= 'status-ready';
                                    } elseif (str_contains($status, 'need') || str_contains($status, 'attention')) {
                                        $statusClass .= 'status-need-attention';
                                    } else {
                                        $statusClass .= 'status-pending';
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= esc($eq['status'] ?? 'Pending') ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-action edit-equipment-btn" 
                                            data-id="<?= $eq['id'] ?>"
                                            data-asset_tag="<?= esc($eq['asset_tag'], 'attr') ?>"
                                            data-make="<?= esc($eq['make'] ?? '', 'attr') ?>"
                                            data-model="<?= esc($eq['model'] ?? '', 'attr') ?>"
                                            data-serial_number="<?= esc($eq['serial_number'] ?? '', 'attr') ?>"
                                            data-device_type="<?= esc($eq['device_type'] ?? '', 'attr') ?>"
                                            data-location="<?= esc($eq['location'] ?? '', 'attr') ?>"
                                            data-department="<?= esc($eq['department'] ?? '', 'attr') ?>"
                                            data-status="<?= esc($eq['status'] ?? '', 'attr') ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <a href="<?= site_url('admin/equipment/delete/' . $eq['id']) ?>" 
                                       class="btn btn-sm btn-danger btn-action"
                                       onclick="return confirm('Are you sure you want to delete this equipment?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Inspections Tab -->
    <div class="tab-pane fade" id="inspections" role="tabpanel" aria-labelledby="inspections-tab">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Inspections List</h5>
                <button class="btn btn-primary" id="openInspectionWizardBtn">
                    <i class="fa fa-plus me-2"></i> Add Inspection
                </button>
            </div>
            <table id="inspections-datatable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Equipment</th>
                        <th>Scheduled Date</th>
                        <th>Completed Date</th>
                        <th>Status</th>
                        <th>Technician</th>
                        <th>Next Due Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inspections)): ?>
                        <?php foreach ($inspections as $insp): ?>
                            <tr>
                                <td><?= esc($insp['asset_tag'] ?? 'N/A') ?></td>
                                <td><?= date('M d, Y', strtotime($insp['scheduled_at'])) ?></td>
                                <td><?= $insp['completed_at'] ? date('M d, Y', strtotime($insp['completed_at'])) : '-' ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'status-badge ';
                                    $status = strtolower($insp['status']);
                                    if ($status === 'completed') {
                                        $statusClass .= 'status-completed';
                                    } elseif ($status === 'scheduled') {
                                        $statusClass .= 'status-scheduled';
                                    } else {
                                        $statusClass .= 'status-pending';
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= esc($insp['status']) ?></span>
                                </td>
                                <td><?= esc($insp['technician_name'] ?? 'Unassigned') ?></td>
                                <td><?= $insp['next_due_date'] ? date('M d, Y', strtotime($insp['next_due_date'])) : '-' ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-action edit-inspection-btn"
                                            data-id="<?= $insp['id'] ?>"
                                            data-equipment_id="<?= $insp['equipment_id'] ?>"
                                            data-scheduled_at="<?= $insp['scheduled_at'] ?>"
                                            data-completed_at="<?= $insp['completed_at'] ?? '' ?>"
                                            data-status="<?= esc($insp['status'], 'attr') ?>"
                                            data-technician_id="<?= $insp['technician_id'] ?? '' ?>"
                                            data-findings="<?= esc($insp['findings'] ?? '', 'attr') ?>"
                                            data-notes="<?= esc($insp['notes'] ?? '', 'attr') ?>"
                                            data-next_due_date="<?= $insp['next_due_date'] ?? '' ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <a href="<?= site_url('admin/inspections/delete/' . $insp['id']) ?>" 
                                       class="btn btn-sm btn-danger btn-action"
                                       onclick="return confirm('Are you sure you want to delete this inspection?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Work Orders Tab -->
    <div class="tab-pane fade" id="work-orders" role="tabpanel" aria-labelledby="work-orders-tab">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Work Orders List</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkOrderModal">
                    <i class="fa fa-plus me-2"></i> Add Work Order
                </button>
            </div>
            <table id="work-orders-datatable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Equipment</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($workOrders)): ?>
                        <?php foreach ($workOrders as $wo): ?>
                            <tr>
                                <td><?= esc($wo['title']) ?></td>
                                <td><?= esc($wo['asset_tag'] ?? 'N/A') ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'status-badge ';
                                    $status = strtolower($wo['status']);
                                    if ($status === 'completed') {
                                        $statusClass .= 'status-completed';
                                    } elseif ($status === 'in progress' || $status === 'in-progress') {
                                        $statusClass .= 'status-in-progress';
                                    } else {
                                        $statusClass .= 'status-open';
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= esc($wo['status']) ?></span>
                                </td>
                                <td><?= esc($wo['priority']) ?></td>
                                <td><?= esc($wo['assigned_to_name'] ?? 'Unassigned') ?></td>
                                <td><?= $wo['start_date'] ? date('M d, Y', strtotime($wo['start_date'])) : '-' ?></td>
                                <td><?= $wo['end_date'] ? date('M d, Y', strtotime($wo['end_date'])) : '-' ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-action edit-workorder-btn"
                                            data-id="<?= $wo['id'] ?>"
                                            data-equipment_id="<?= $wo['equipment_id'] ?? '' ?>"
                                            data-title="<?= esc($wo['title'], 'attr') ?>"
                                            data-description="<?= esc($wo['description'] ?? '', 'attr') ?>"
                                            data-status="<?= esc($wo['status'], 'attr') ?>"
                                            data-priority="<?= esc($wo['priority'], 'attr') ?>"
                                            data-assigned_to="<?= $wo['assigned_to'] ?? '' ?>"
                                            data-start_date="<?= $wo['start_date'] ?? '' ?>"
                                            data-end_date="<?= $wo['end_date'] ?? '' ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <a href="<?= site_url('admin/work-orders/delete/' . $wo['id']) ?>" 
                                       class="btn btn-sm btn-danger btn-action"
                                       onclick="return confirm('Are you sure you want to delete this work order?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================================================
     ADD EQUIPMENT MODAL (unchanged)
     ================================================ -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="equipmentForm" method="post" action="<?= site_url('admin/equipment/create') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="site_id" value="<?= $site['id'] ?>">
                <input type="hidden" id="equipment-id" name="id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentModalLabel">Add Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="equipment-asset-tag" class="form-label">Asset Tag <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="equipment-asset-tag" name="asset_tag" required>
                        </div>
                        <div class="col-md-6">
                            <label for="equipment-make" class="form-label">Make</label>
                            <input type="text" class="form-control" id="equipment-make" name="make">
                        </div>
                        <div class="col-md-6">
                            <label for="equipment-model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="equipment-model" name="model">
                        </div>
                        <div class="col-md-6">
                            <label for="equipment-serial-number" class="form-label">Serial Number</label>
                            <input type="text" class="form-control" id="equipment-serial-number" name="serial_number">
                        </div>
                        <div class="col-md-6">
                            <label for="equipment-device-type" class="form-label">Device Type</label>
                            <input type="text" class="form-control" id="equipment-device-type" name="device_type">
                        </div>
                        <div class="col-md-6">
                            <label for="equipment-location" class="form-label">Location or Room</label>
                            <input type="text" class="form-control" id="equipment-location" name="location">
                        </div>
                        <div class="col-md-6">
                            <label for="equipment-department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="equipment-department" name="department">
                        </div>
                        <div class="col-md-6">
                            <label for="equipment-status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="equipment-status" name="status" required>
                                <option value="">-- Select Status --</option>
                                <option value="Ready">Ready</option>
                                <option value="Need Attention">Need Attention</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="equipmentSubmitBtn">Save Equipment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================================================
     3-STEP INSPECTION WIZARD MODAL
     ================================================ -->
<div class="modal fade" id="inspectionWizardModal" tabindex="-1" aria-labelledby="inspectionWizardLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Wizard Header -->
            <div class="modal-header-wizard">
                <h5>New Site Inspection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">

                <!-- ─── STEP 1: Enter Asset Information ─── -->
                <div class="wizard-step active" id="wizardStep1">
                    <h5>Step 1: Enter Asset Information</h5>
                    <p class="site-label"><strong>Site:</strong> <?= esc($site['name']) ?></p>

                    <label for="wiz-asset-barcode" class="form-label">Asset/Barcode Number</label>
                    <input type="text"
                           class="form-control"
                           id="wiz-asset-barcode"
                           placeholder="Enter or scan asset/barcode"
                           autocomplete="off">
                    <p class="helper-text">
                        Enter the asset or barcode number. If the asset exists in the site's inventory, details will be automatically filled.
                    </p>

                    <div class="wizard-footer">
                        <div class="left-btns"></div>
                        <div class="right-btns">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="wizStep1Next">Next</button>
                        </div>
                    </div>
                </div>

                <!-- ─── STEP 2: Asset Verification (Not Found) ─── -->
                <div class="wizard-step" id="wizardStep2">
                    <h5>Step 2: Asset Verification (Not Found)</h5>
                    <div class="asset-not-found-alert">
                        Asset not found. Please search for the device model in the equipment database.
                    </div>

                    <label for="wiz-search-model" class="form-label">Search Model</label>
                    <input type="text"
                           class="form-control mb-3"
                           id="wiz-search-model"
                           placeholder="Search for model..."
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
                            <input type="text" class="form-control" id="wiz-s2-serial" placeholder="Enter Serial Number">
                        </div>
                    </div>

                    <div class="wizard-footer">
                        <div class="left-btns">
                            <button type="button" class="btn btn-outline-secondary" id="wizStep2Back">Back</button>
                        </div>
                        <div class="right-btns">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="wizStep2Next">Next</button>
                        </div>
                    </div>
                </div>

                <!-- ─── STEP 3: Enter Inspection Details ─── -->
                <div class="wizard-step" id="wizardStep3">
                    <h5>Step 3: Enter Inspection Details</h5>

                    <!-- Hidden fields for form submission -->
                    <input type="hidden" id="wiz-equipment-id" name="equipment_id" value="">
                    <input type="hidden" id="wiz-site-id" name="site_id" value="<?= $site['id'] ?>">

                    <!-- Row 1: Model | Description | Serial # (read-only, pre-filled) -->
                    <div class="row g-3 step3-readonly-row">
                        <div class="col-md-4">
                            <label for="wiz-s3-model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="wiz-s3-model" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="wiz-s3-description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="wiz-s3-description" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="wiz-s3-serial" class="form-label">Serial #</label>
                            <input type="text" class="form-control" id="wiz-s3-serial" readonly>
                        </div>
                    </div>

                    <!-- Row 2: Asset ID | Department | Location / Room (read-only, pre-filled) -->
                    <div class="row g-3 mt-1 step3-readonly-row">
                        <div class="col-md-4">
                            <label for="wiz-s3-assetid" class="form-label">Asset ID</label>
                            <input type="text" class="form-control" id="wiz-s3-assetid" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="wiz-s3-department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="wiz-s3-department" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="wiz-s3-location" class="form-label">Location / Room</label>
                            <input type="text" class="form-control" id="wiz-s3-location" readonly>
                        </div>
                    </div>

                    <!-- Row 3: PM Service Frequency | Inspection Type -->
                    <div class="row g-3 mt-1">
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
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="wiz-s3-technician" class="form-label">Technician</label>
                            <select class="form-select" id="wiz-s3-technician" name="technician_id">
                                <option value="">-- Select Technician --</option>
                                <?php if (!empty($technicians)): ?>
                                    <?php foreach ($technicians as $tech): ?>
                                        <option value="<?= $tech['id'] ?>"><?= esc($tech['full_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="wiz-s3-inspdate" class="form-label">Inspection Date</label>
                            <input type="date" class="form-control" id="wiz-s3-inspdate" name="scheduled_at">
                        </div>
                    </div>

                    <!-- Row 5: Service Notes (full width) -->
                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <label for="wiz-s3-notes" class="form-label">Service Notes</label>
                            <textarea class="form-control" id="wiz-s3-notes" name="notes" rows="3" placeholder=""></textarea>
                        </div>
                    </div>

                    <!-- Row 6: Status | Device Complete -->
                    <div class="row g-3 mt-1">
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
                    <p class="text-center text-muted mt-3 mb-1" style="font-size:0.85rem;">
                        Add device notes and complete status.
                    </p>

                    <!-- Outcome buttons: Pass | Fail | Repair -->
                    <div class="inspection-outcome-btns">
                        <button type="button" class="btn-pass" id="wizBtnPass">Pass Inspection</button>
                        <button type="button" class="btn-fail" id="wizBtnFail">Fail Inspection</button>
                        <button type="button" class="btn-repair" id="wizBtnRepair">Repair Inspection</button>
                    </div>

                    <!-- Enter Next Device button -->
                    <button type="button" class="btn-next-device" id="wizBtnNextDevice">Enter Next Device</button>

                    <!-- Footer with Back / Cancel -->
                    <div class="wizard-footer">
                        <div class="left-btns">
                            <button type="button" class="btn btn-outline-secondary" id="wizStep3Back">Back</button>
                        </div>
                        <div class="right-btns">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>

            </div><!-- end modal-body -->
        </div><!-- end modal-content -->
    </div><!-- end modal-dialog -->
</div><!-- end inspectionWizardModal -->

<!-- ================================================
     EDIT INSPECTION MODAL (legacy single-form, for editing existing records)
     ================================================ -->
<div class="modal fade" id="editInspectionModal" tabindex="-1" aria-labelledby="editInspectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editInspectionForm" method="post" action="">
                <?= csrf_field() ?>
                <input type="hidden" name="site_id" value="<?= $site['id'] ?>">
                <input type="hidden" id="edit-inspection-id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title" id="editInspectionModalLabel">Edit Inspection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit-inspection-equipment" class="form-label">Equipment <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-inspection-equipment" name="equipment_id" required>
                                <option value="">-- Select Equipment --</option>
                                <?php foreach ($equipment as $eq): ?>
                                    <option value="<?= $eq['id'] ?>"><?= esc($eq['asset_tag']) ?> - <?= esc($eq['make']) ?> <?= esc($eq['model']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-inspection-scheduled-at" class="form-label">Scheduled Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="edit-inspection-scheduled-at" name="scheduled_at" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-inspection-status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-inspection-status" name="status" required>
                                <option value="">-- Select Status --</option>
                                <option value="Pending">Pending</option>
                                <option value="Scheduled">Scheduled</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-inspection-technician" class="form-label">Technician</label>
                            <select class="form-select" id="edit-inspection-technician" name="technician_id">
                                <option value="">-- Select Technician --</option>
                                <?php if (!empty($technicians)): ?>
                                    <?php foreach ($technicians as $tech): ?>
                                        <option value="<?= $tech['id'] ?>"><?= esc($tech['full_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-inspection-completed-at" class="form-label">Completed Date</label>
                            <input type="datetime-local" class="form-control" id="edit-inspection-completed-at" name="completed_at">
                        </div>
                        <div class="col-md-6">
                            <label for="edit-inspection-next-due-date" class="form-label">Next Due Date</label>
                            <input type="date" class="form-control" id="edit-inspection-next-due-date" name="next_due_date">
                        </div>
                        <div class="col-12">
                            <label for="edit-inspection-findings" class="form-label">Findings</label>
                            <textarea class="form-control" id="edit-inspection-findings" name="findings" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="edit-inspection-notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit-inspection-notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Inspection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================================================
     ADD WORK ORDER MODAL (unchanged)
     ================================================ -->
<div class="modal fade" id="addWorkOrderModal" tabindex="-1" aria-labelledby="addWorkOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="workOrderForm" method="post" action="<?= site_url('admin/work-orders/create') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="site_id" value="<?= $site['id'] ?>">
                <input type="hidden" id="workorder-id" name="id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="workOrderModalLabel">Add Work Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="workorder-title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="workorder-title" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-equipment" class="form-label">Equipment</label>
                            <select class="form-select" id="workorder-equipment" name="equipment_id">
                                <option value="">-- Select Equipment --</option>
                                <?php foreach ($equipment as $eq): ?>
                                    <option value="<?= $eq['id'] ?>"><?= esc($eq['asset_tag']) ?> - <?= esc($eq['make']) ?> <?= esc($eq['model']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="workorder-status" name="status" required>
                                <option value="">-- Select Status --</option>
                                <option value="Open">Open</option>
                                <option value="In Progress">In Progress</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="workorder-priority" name="priority" required>
                                <option value="">-- Select Priority --</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-assigned-to" class="form-label">Assigned To</label>
                            <select class="form-select" id="workorder-assigned-to" name="assigned_to">
                                <option value="">-- Select User --</option>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>"><?= esc($user['full_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-start-date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="workorder-start-date" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-end-date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="workorder-end-date" name="end_date">
                        </div>
                        <div class="col-12">
                            <label for="workorder-description" class="form-label">Description</label>
                            <textarea class="form-control" id="workorder-description" name="description" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="workOrderSubmitBtn">Save Work Order</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ================================================
     JAVASCRIPT
     ================================================ -->
<script>
// Base URLs used by the wizard AJAX calls
var WIZ_URL_SEARCH_ASSET = '<?= site_url('admin/inspections/searchByAssetTag') ?>';
var WIZ_URL_SEARCH_MODEL = '<?= site_url('admin/inspections/searchByModel') ?>';
var WIZ_SITE_ID          = '<?= $site['id'] ?>';

$(document).ready(function() {

    // ─── DataTables Init ─────────────────────────────────
    $('#equipment-datatable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf'],
        responsive: true,
        pageLength: 10,
        order: [[0, 'asc']]
    });

    $('#inspections-datatable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf'],
        responsive: true,
        pageLength: 10,
        order: [[1, 'desc']]
    });

    $('#work-orders-datatable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf'],
        responsive: true,
        pageLength: 10,
        order: [[5, 'desc']]
    });

    // ─── Equipment Modal (unchanged) ─────────────────────
    $('#addEquipmentModal').on('hidden.bs.modal', function () {
        resetEquipmentForm();
    });

    function resetEquipmentForm() {
        $('#equipmentForm')[0].reset();
        $('#equipment-id').val('');
        $('#equipmentModalLabel').text('Add Equipment');
        $('#equipmentSubmitBtn').text('Save Equipment');
        $('#equipmentForm').attr('action', '<?= site_url('admin/equipment/create') ?>');
    }

    $(document).on('click', '.edit-equipment-btn', function() {
        var id = $(this).data('id');
        $('#equipment-id').val(id);
        $('#equipment-asset-tag').val($(this).data('asset_tag'));
        $('#equipment-make').val($(this).data('make'));
        $('#equipment-model').val($(this).data('model'));
        $('#equipment-serial-number').val($(this).data('serial_number'));
        $('#equipment-device-type').val($(this).data('device_type'));
        $('#equipment-location').val($(this).data('location'));
        $('#equipment-department').val($(this).data('department'));
        $('#equipment-status').val($(this).data('status'));

        $('#equipmentModalLabel').text('Edit Equipment');
        $('#equipmentSubmitBtn').text('Update Equipment');
        $('#equipmentForm').attr('action', '<?= site_url('admin/equipment/update/') ?>' + id);
        $('#addEquipmentModal').modal('show');
    });

    // ─── Edit Inspection (existing records — classic single modal) ───
    $(document).on('click', '.edit-inspection-btn', function() {
        var id = $(this).data('id');
        $('#edit-inspection-id').val(id);
        $('#edit-inspection-equipment').val($(this).data('equipment_id'));
        $('#edit-inspection-scheduled-at').val($(this).data('scheduled_at'));
        $('#edit-inspection-completed-at').val($(this).data('completed_at'));
        $('#edit-inspection-status').val($(this).data('status'));
        $('#edit-inspection-technician').val($(this).data('technician_id'));
        $('#edit-inspection-findings').val($(this).data('findings'));
        $('#edit-inspection-notes').val($(this).data('notes'));
        $('#edit-inspection-next-due-date').val($(this).data('next_due_date'));

        $('#editInspectionForm').attr('action', '<?= site_url('admin/inspections/update/') ?>' + id);
        $('#editInspectionModal').modal('show');
    });

    // ─── Work Order Modal (unchanged) ────────────────────
    $('#addWorkOrderModal').on('hidden.bs.modal', function () {
        resetWorkOrderForm();
    });

    function resetWorkOrderForm() {
        $('#workOrderForm')[0].reset();
        $('#workorder-id').val('');
        $('#workOrderModalLabel').text('Add Work Order');
        $('#workOrderSubmitBtn').text('Save Work Order');
        $('#workOrderForm').attr('action', '<?= site_url('admin/work-orders/create') ?>');
    }

    $(document).on('click', '.edit-workorder-btn', function() {
        var id = $(this).data('id');
        $('#workorder-id').val(id);
        $('#workorder-title').val($(this).data('title'));
        $('#workorder-description').val($(this).data('description'));
        $('#workorder-status').val($(this).data('status'));
        $('#workorder-priority').val($(this).data('priority'));
        $('#workorder-assigned-to').val($(this).data('assigned_to'));
        $('#workorder-start-date').val($(this).data('start_date'));
        $('#workorder-end-date').val($(this).data('end_date'));

        $('#workOrderModalLabel').text('Edit Work Order');
        $('#workOrderSubmitBtn').text('Update Work Order');
        $('#workOrderForm').attr('action', '<?= site_url('admin/work-orders/update/') ?>' + id);
        $('#addWorkOrderModal').modal('show');
    });

    // ─── Delete handlers (unchanged) ─────────────────────
    $(document).on('click', '.delete-equipment-btn', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = '<?= site_url('admin/equipment/delete/') ?>' + id;
            }
        });
    });

    $(document).on('click', '.delete-inspection-btn', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = '<?= site_url('admin/inspections/delete/') ?>' + id;
            }
        });
    });

    $(document).on('click', '.delete-workorder-btn', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = '<?= site_url('admin/work-orders/delete/') ?>' + id;
            }
        });
    });


    // ═══════════════════════════════════════════════════════════════
    //  3-STEP INSPECTION WIZARD LOGIC
    // ═══════════════════════════════════════════════════════════════

    // Internal state for the wizard
    var wizardAssetFound = false;       // did step 1 find a matching asset?
    var wizardMatchedEquip = null;      // the matched equipment object (or null)

    // ─── Helper: show only one step ─────────────────────
    function showStep(stepNum) {
        $('#wizardStep1, #wizardStep2, #wizardStep3').removeClass('active');
        $('#wizardStep' + stepNum).addClass('active');
    }

    // ─── Helper: reset the entire wizard ────────────────
    function resetWizard() {
        wizardAssetFound  = false;
        wizardMatchedEquip = null;

        $('#wiz-asset-barcode').val('');

        // Step 2
        $('#wiz-search-model').val('');
        $('#wiz-model-dropdown').remove();   // ← close any open search dropdown
        $('#wiz-s2-manufacturer').val('');
        $('#wiz-s2-model').val('');
        $('#wiz-s2-description').val('');
        $('#wiz-s2-serial').val('');

        // Step 3 — read-only display fields
        $('#wiz-s3-model').val('');
        $('#wiz-s3-description').val('');
        $('#wiz-s3-serial').val('');
        $('#wiz-s3-assetid').val('');
        $('#wiz-s3-department').val('');
        $('#wiz-s3-location').val('');

        // Step 3 — editable fields
        $('#wiz-equipment-id').val('');
        $('#wiz-s3-pmfreq').val('');
        $('#wiz-s3-insptype').val('');
        $('#wiz-s3-technician').val('');
        $('#wiz-s3-inspdate').val('');
        $('#wiz-s3-notes').val('');
        $('#wiz-s3-status').val('Pass');
        $('#wiz-s3-devicecomplete').val('Yes');

        showStep(1);
    }

    // ─── Open wizard ─────────────────────────────────────
    $('#openInspectionWizardBtn').on('click', function() {
        resetWizard();
        $('#inspectionWizardModal').modal('show');
    });

    // ─── Reset wizard when modal is closed ──────────────
    $('#inspectionWizardModal').on('hidden.bs.modal', function() {
        resetWizard();
    });

    // ─── STEP 1 ► Next ───────────────────────────────────
    $('#wizStep1Next').on('click', function() {
        var barcode = $.trim($('#wiz-asset-barcode').val());
        if (barcode === '') {
            alert('Please enter an asset or barcode number.');
            return;
        }

        // Disable button while request is in-flight
        $('#wizStep1Next').prop('disabled', true).text('Searching…');

        $.ajax({
            url:    WIZ_URL_SEARCH_ASSET,
            method: 'GET',
            data:   { asset_tag: barcode, site_id: WIZ_SITE_ID },
            success: function(res) {
                $('#wizStep1Next').prop('disabled', false).text('Next');

                if (res.found) {
                    // ── Asset FOUND ── jump straight to Step 3
                    wizardAssetFound   = true;
                    wizardMatchedEquip = res;
                    populateStep3FromEquipment(res);
                    showStep(3);
                } else {
                    // ── Asset NOT FOUND ── go to Step 2
                    wizardAssetFound   = false;
                    wizardMatchedEquip = null;
                    showStep(2);
                }
            },
            error: function() {
                $('#wizStep1Next').prop('disabled', false).text('Next');
                alert('Search failed. Please try again.');
            }
        });
    });

    // ─── STEP 2 ► Back ───────────────────────────────────
    $('#wizStep2Back').on('click', function() {
        showStep(1);
    });

    // ─── STEP 2 ► Live model search (debounced keyup) ────
    var modelSearchTimer = null;

    $('#wiz-search-model').on('keyup', function() {
        var val = $.trim($(this).val());

        // Clear dropdown if input is too short
        if (val.length < 2) {
            $('#wiz-model-dropdown').remove();
            return;
        }

        // Debounce — wait 350 ms after user stops typing
        clearTimeout(modelSearchTimer);
        modelSearchTimer = setTimeout(function() {
            $.ajax({
                url:    WIZ_URL_SEARCH_MODEL,
                method: 'GET',
                data:   { keyword: val },
                success: function(results) {
                    $('#wiz-model-dropdown').remove(); // remove any previous dropdown

                    if (!results || results.length === 0) return;

                    // Build the dropdown list
                    var $wrap  = $('#wiz-search-model').parent();
                    var $input = $('#wiz-search-model');

                    // Ensure wrapper is positioned so dropdown can absolute-position inside it
                    $wrap.css('position', 'relative');

                    var html = '<div id="wiz-model-dropdown" style="'
                        + 'position:absolute; top:100%; left:0; right:0; z-index:9999;'
                        + 'background:#fff; border:1px solid #cbd5e1; border-radius:6px;'
                        + 'box-shadow:0 4px 12px rgba(0,0,0,0.12); max-height:220px;'
                        + 'overflow-y:auto; margin-top:2px;">';

                    $.each(results, function(i, item) {
                        var label = (item.make || '') + ' ' + (item.model || '');
                        label = $.trim(label) || item.device_type || 'Unknown';

                        html += '<div class="wiz-model-option" '
                            + 'data-make="'          + (item.make          || '') + '" '
                            + 'data-model="'         + (item.model         || '') + '" '
                            + 'data-serial_number="' + (item.serial_number || '') + '" '
                            + 'data-device_type="'   + (item.device_type   || '') + '" '
                            + 'style="padding:0.55rem 0.75rem; cursor:pointer; border-bottom:1px solid #f1f5f9;"'
                            + ' onmouseover="$(this).css(\'background\',\'#eef2ff\')"'
                            + ' onmouseout="$(this).css(\'background\',\'#fff\')">'
                            + '<strong>' + label + '</strong>'
                            + (item.serial_number ? ' &nbsp;<span style="color:#64748b; font-size:0.82rem;">S/N: ' + item.serial_number + '</span>' : '')
                            + '</div>';
                    });

                    html += '</div>';
                    $input.after(html);
                }
            });
        }, 350);
    });

    // ─── STEP 2 ► Pick an item from the model dropdown ──
    $(document).on('click', '.wiz-model-option', function() {
        $('#wiz-s2-manufacturer').val($(this).data('make'));
        $('#wiz-s2-model').val($(this).data('model'));
        $('#wiz-s2-description').val($(this).data('device_type'));
        $('#wiz-s2-serial').val($(this).data('serial_number'));

        // Put the selected label back into the search box and close dropdown
        $('#wiz-search-model').val($.trim($(this).data('make') + ' ' + $(this).data('model')));
        $('#wiz-model-dropdown').remove();
    });

    // ─── Close model dropdown when clicking elsewhere ────
    $(document).on('click', function(e) {
        if (!$(e.target).is('#wiz-search-model') && !$(e.target).closest('#wiz-model-dropdown').length) {
            $('#wiz-model-dropdown').remove();
        }
    });
    $('#wizStep2Next').on('click', function() {
        // Carry Step 2 manual entries into Step 3 read-only display
        $('#wiz-s3-model').val($('#wiz-s2-model').val());
        $('#wiz-s3-description').val($('#wiz-s2-description').val());
        $('#wiz-s3-serial').val($('#wiz-s2-serial').val());
        // Asset ID / Department / Location are unknown for a new (not-found) device
        $('#wiz-s3-assetid').val('');
        $('#wiz-s3-department').val('');
        $('#wiz-s3-location').val('');
        $('#wiz-equipment-id').val('');   // no linked equipment record

        showStep(3);
    });

    // ─── STEP 3 ► Back ───────────────────────────────────
    $('#wizStep3Back').on('click', function() {
        if (wizardAssetFound) {
            // Came from Step 1 directly — go back to Step 1
            showStep(1);
        } else {
            // Came through Step 2
            showStep(2);
        }
    });

    // ─── STEP 3 ► Outcome Buttons (Pass / Fail / Repair) ─
    // Each button sets the status dropdown to the matching value, then submits.
    $('#wizBtnPass').on('click', function() {
        $('#wiz-s3-status').val('Pass');
        submitInspectionWizard();
    });
    $('#wizBtnFail').on('click', function() {
        $('#wiz-s3-status').val('Fail');
        submitInspectionWizard();
    });
    $('#wizBtnRepair').on('click', function() {
        $('#wiz-s3-status').val('Repair');
        submitInspectionWizard();
    });

    // ─── STEP 3 ► Enter Next Device ──────────────────────
    // Submits the current inspection and resets wizard back to Step 1
    $('#wizBtnNextDevice').on('click', function() {
        submitInspectionWizard(true);   // true = reset to step 1 after submit
    });

    // ─── Populate Step 3 from a matched equipment record ─
    function populateStep3FromEquipment(eq) {
        $('#wiz-equipment-id').val(eq.id);
        $('#wiz-s3-model').val(eq.model);
        $('#wiz-s3-description').val(eq.device_type);
        $('#wiz-s3-serial').val(eq.serial_number);
        $('#wiz-s3-assetid').val(eq.asset_tag);
        $('#wiz-s3-department').val(eq.department);
        $('#wiz-s3-location').val(eq.location);
    }

    // ─── Submit inspection via AJAX POST ─────────────────
    function submitInspectionWizard(resetAfter) {
        var siteId       = '<?= $site['id'] ?>';
        var equipId      = $('#wiz-equipment-id').val();
        var inspDate     = $('#wiz-s3-inspdate').val();
        var status       = $('#wiz-s3-status').val();
        var techId       = $('#wiz-s3-technician').val();
        var notes        = $('#wiz-s3-notes').val();
        var pmFreq       = $('#wiz-s3-pmfreq').val();
        var inspType     = $('#wiz-s3-insptype').val();
        var devComplete  = $('#wiz-s3-devicecomplete').val();

        // ── Minimal validation ──
        if (inspDate === '') {
            alert('Please select an Inspection Date.');
            return;
        }

        // Build form data
        var formData = new FormData();
        // CI4 CSRF token — grab from any existing csrf_field on the page
        var csrfName  = $('input[name="csrf_token"]').first().attr('name');
        var csrfValue = $('input[name="csrf_token"]').first().val();
        if (csrfName && csrfValue) {
            formData.append(csrfName, csrfValue);
        }

        formData.append('site_id',          siteId);
        formData.append('equipment_id',     equipId);
        formData.append('scheduled_at',     inspDate);
        formData.append('status',           status);
        formData.append('technician_id',    techId);
        formData.append('notes',            notes);
        formData.append('pm_frequency',     pmFreq);
        formData.append('inspection_type',  inspType);
        formData.append('device_complete',  devComplete);

        // If asset was NOT found, pass along Step-2 details so the controller
        // can optionally create an equipment record or store them in findings.
        if (!wizardAssetFound) {
            formData.append('manufacturer',  $('#wiz-s2-manufacturer').val());
            formData.append('model_name',    $('#wiz-s2-model').val());
            formData.append('description',   $('#wiz-s2-description').val());
            formData.append('serial_number', $('#wiz-s2-serial').val());
            formData.append('asset_not_found', '1');
        }

        $.ajax({
            url:         '<?= site_url('admin/inspections/create') ?>',
            method:      'POST',
            data:        formData,
            contentType: false,
            processData: false,
            success: function() {
                if (resetAfter) {
                    // "Enter Next Device" — keep modal open, go back to step 1
                    resetWizard();
                    // Refresh the inspections datatable (soft — reload page after a short delay)
                    setTimeout(function() { location.reload(); }, 800);
                } else {
                    // Pass / Fail / Repair — close modal and reload
                    $('#inspectionWizardModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                console.error('Inspection create failed', xhr);
                alert('Failed to save inspection. Please try again.');
            }
        });
    }

});
</script>

<?= $this->endSection() ?>