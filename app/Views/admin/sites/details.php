<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    #viewInspectionModal .modal-header {
    background: #6c757d;
    color: #fff;
}

#viewInspectionModal .modal-body {
    max-height: 400px;
    overflow-y: auto;
}

#viewInspectionModal table th, #viewInspectionModal table td {
    text-align: center;
}


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
    
    .status-ready, .status-completed, .status-pass {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-need-attention, .status-fail {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .status-pending, .status-open {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-scheduled, .status-in-progress, .status-repair {
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

    .btn-complete {
        background-color: #7c3aed;
        color: #fff;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
    }
    .btn-complete:hover { background-color: #6d28d9; }

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

    /* Inspection Queue Styles */
    .inspection-queue {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        max-height: 300px;
        overflow-y: auto;
    }

    .inspection-queue-item {
        background: white;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .inspection-queue-item:last-child {
        margin-bottom: 0;
    }

    .queue-item-info {
        flex: 1;
    }

    .queue-item-model {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
    }

    .queue-item-details {
        font-size: 0.82rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    .queue-item-remove {
        background: #ef4444;
        color: white;
        border: none;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        cursor: pointer;
    }

    .queue-item-remove:hover {
        background: #dc2626;
    }

    .queue-count {
        background: #7c3aed;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 0.5rem;
    }

    /* Grouped Inspection View Styles */
    .grouped-inspection {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .grouped-inspection-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .group-id-badge {
        background: #7c3aed;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .group-date {
        color: #64748b;
        font-size: 0.9rem;
    }

    .grouped-inspection-items {
        display: grid;
        gap: 0.75rem;
    }

    .grouped-item {
        background: white;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 0.75rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.5rem;
    }

    .grouped-item-field {
        display: flex;
        flex-direction: column;
    }

    .grouped-item-label {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .grouped-item-value {
        font-size: 0.9rem;
        color: #1e293b;
        font-weight: 500;
    }
    .site-avatar img {
    width: 100%;             /* Makes sure the image is responsive */
    height: 100%;            /* Ensures the image fills the container */
    object-fit: cover;       /* Ensures the image scales and stays within the circle */
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
                $logoPath = $customer['logo_path']; // Get the logo file name
                $logoUrl = base_url('uploads/logos/' . $logoPath); // Construct the URL assuming the logos are in the 'uploads/logos' directory
               
                // Check if the logo exists (optional)
                if (file_exists(FCPATH . 'uploads/logos/' . $logoPath) && !empty($logoPath)) {
                    echo '<img src="' . $logoUrl . '" alt="Customer Logo" />'; // Display the logo image
                } else {
                    $nameParts = explode(' ', $customer['name']);
                    $initials = '';
                    if (count($nameParts) >= 2) {
                        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
                    } else {
                        $initials = strtoupper(substr($customer['name'], 0, 2));
                    }
                    echo $initials; // Display initials if logo doesn't exist
                }
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
                        <th>Inspection ID</th>
                        <th>Scheduled Date</th>
                        <!-- <th>Status</th> -->
                        <th>Technician</th>
                        <th>Next Due Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inspections)): ?>
                        <?php foreach ($inspections as $insp): ?>
                            <tr>
                                <!-- Equipment Name -->
                                <td><?= esc($insp['group_id'] ?? 'N/A') ?></td>

                                <!-- Scheduled Date -->
                                <td><?= date('M d, Y', strtotime($insp['scheduled_at'])) ?></td>

                                

                                <!-- Status -->
                                <!-- <td>
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
                                </td> -->

                                <!-- Technician -->
                                <td><?= esc($insp['technician_name'] ?? 'Unassigned') ?></td>

                                <!-- Next Due Date -->
                                <td><?= $insp['next_due_date'] ? date('M d, Y', strtotime($insp['next_due_date'])) : '-' ?></td>

                                <!-- Actions -->
                                <td>

                                 <!-- View Button -->
                                <button class="btn btn-sm btn-info btn-action view-inspection-btn"
                                        data-group_id="<?= esc($insp['group_id']) ?>"
                                        data-id="<?= $insp['id'] ?>"
                                        data-equipment_id="<?= esc($insp['equipment_id']) ?>"
                                        data-scheduled_at="<?= esc($insp['scheduled_at']) ?>"
                                        data-completed_at="<?= esc($insp['completed_at'] ?? '') ?>"
                                        data-status="<?= esc($insp['status'], 'attr') ?>"
                                        data-technician_id="<?= esc($insp['technician_id'] ?? '') ?>"
                                        data-findings="<?= esc($insp['findings'] ?? '', 'attr') ?>"
                                        data-notes="<?= esc($insp['notes'] ?? '', 'attr') ?>"
                                        data-next_due_date="<?= esc($insp['next_due_date'] ?? '') ?>"
                                        >
                                    <i class="fa fa-eye"></i> View
                                </button>

                                    <!-- Edit Button -->
                                    <!-- <button class="btn btn-sm btn-info btn-action edit-inspection-btn"
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
                                </button> -->


                                    <!-- Delete Button -->
                                    <a href="<?= site_url('admin/inspections/delete/' . $insp['group_id']) ?>" 
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
<!-- <div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
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
</div> -->

<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="equipmentForm" method="post" action="<?= site_url('admin/equipment/create') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="site_id" value="<?= $site['id'] ?>">
                <input type="hidden" id="equipment-id" name="id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentModalLabel">Add/Edit Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Asset Tag -->
                        <div class="col-md-6">
                            <label for="equipment-asset-tag" class="form-label">Asset Tag</label>
                            <input type="text" class="form-control" id="equipment-asset-tag" name="asset_tag" placeholder=" add Asset Tag" required> 
                        </div>
                        
                        <!-- Serial Number -->
                        <div class="col-md-6">
                            <label for="equipment-serial-number" class="form-label">Serial Number</label>
                            <input type="text" class="form-control" id="equipment-serial-number" name="serial_number" placeholder="" required>
                        </div>
                        
                        <!-- Make -->
                        <div class="col-md-6">
                            <label for="equipment-make" class="form-label">Make</label>
                            <input type="text" class="form-control" id="equipment-make" name="make" placeholder="e.g., Philips, GE" required >
                        </div>
                        
                        <!-- Model -->
                        <div class="col-md-6">
                            <label for="equipment-model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="equipment-model" name="model" placeholder="Enter model" required>
                        </div>
                        
                        <!-- Device Type -->
                        <div class="col-md-6">
                            <label for="equipment-device-type" class="form-label">Device Type</label>
                            <input type="text" class="form-control" id="equipment-device-type" name="device_type" placeholder="e.g., MRI, CT, Ultrasound" required>
                        </div>
                        
                        <!-- Department -->
                        <div class="col-md-6">
                            <label for="equipment-department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="equipment-department" name="department" placeholder="e.g., Radiology">
                        </div>
                        
                        <!-- Room/Location -->
                        <div class="col-md-6">
                            <label for="equipment-location" class="form-label">Room/Location</label>
                            <input type="text" class="form-control" id="equipment-location" name="location" placeholder="e.g., Room 101">
                        </div>
                        
                        <!-- Device Status -->
                        <div class="col-md-6">
                            <label for="equipment-status" class="form-label">Device Status</label>
                            <select class="form-select" id="equipment-status" name="status">
                                <option value="Ready">Ready</option>
                                <option value="Need Attention">Need Attention</option>
                                <option value="Repair">Repair</option>
                                <option value="Out of Service">Out of Service</option>
                            </select>
                        </div>
                        
                        <!-- PM Kit -->
                        <div class="col-md-12">
                            <label for="equipment-pm-kit" class="form-label">PM Kit</label>
                            <select class="form-select" id="equipment-pm-kit" name="pm_kit">
                                <option value="">Select PM Kit</option>
                                <option value="Kit A">Kit A</option>
                                <option value="Kit B">Kit B</option>
                                <option value="Kit C">Kit C</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>
                        
                        <!-- Fast Notes -->
                        <div class="col-md-12">
                            <label for="equipment-fast-notes" class="form-label">Fast Notes</label>
                            <textarea class="form-control" id="equipment-fast-notes" name="fast_notes" rows="2" placeholder="Short note for fast entry"></textarea>
                        </div>
                        
                        <!-- Customer Location -->
                        <div class="col-md-12">
                            <label for="equipment-customer-location" class="form-label">Customer Location</label>
                            <select class="form-select" id="equipment-customer-location" name="site_id">
                                <option value="<?= $site['id'] ?>" selected><?= esc($site['name']) ?></option>
                                <?php if (isset($sites) && is_array($sites)): ?>
                                    <?php foreach ($sites as $siteOption): ?>
                                        <?php if ($siteOption['id'] != $site['id']): ?>
                                            <option value="<?= $siteOption['id'] ?>"><?= esc($siteOption['name']) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <!-- Installation Date -->
                        <div class="col-md-6">
                            <label for="equipment-installation-date" class="form-label">Installation Date</label>
                            <input type="date" class="form-control" id="equipment-installation-date" name="installation_date">
                        </div>
                        
                        <!-- Warranty Expires -->
                        <div class="col-md-6">
                            <label for="equipment-warranty-expires" class="form-label">Warranty Expires</label>
                            <input type="date" class="form-control" id="equipment-warranty-expires" name="warranty_expires">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="equipmentSubmitBtn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================================================
     3-STEP INSPECTION WIZARD MODAL
     ================================================ -->
<!-- ================================================
     3-STEP INSPECTION WIZARD MODAL (Updated for Serial Number & Multiple Inspections)
     ================================================ -->
<div class="modal fade" id="inspectionWizardModal" tabindex="-1" aria-labelledby="inspectionWizardLabel" aria-hidden="true">
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
                <input type="hidden" id="wiz-site-id" name="site_id" value="<?= $site['id'] ?>">
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
                    <p class="site-label"><strong>Site:</strong> <?= esc($site['name']) ?></p>

                    <label for="wiz-serial-number" class="form-label">Serial Number</label>
                    <input type="text"
                           class="form-control"
                           id="wiz-serial-number"
                           placeholder="Enter or scan serial number"
                           autocomplete="off">
                    <p class="helper-text">
                        Enter the serial number from the equipment label. If found, details will be automatically filled.
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
                        Serial number not found. Please search for the device model in the equipment database.
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
                            <input type="text" class="form-control" id="wiz-s2-serial" readonly>
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
                            <input type="date" class="form-control" id="wiz-s3-inspdate" name="scheduled_at" value="<?= date('Y-m-d') ?>">
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
                    <button type="button" class="btn-next-device" id="wizBtnNextDevice">Add to Queue & Next Device</button>

                    <!-- Footer with Back / Cancel / Complete -->
                    <div class="wizard-footer">
                        <div class="left-btns">
                            <button type="button" class="btn btn-outline-secondary" id="wizStep3Back">Back</button>
                        </div>
                        <div class="right-btns">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="wizBtnComplete" style="display:none;">
                                <i class="fa fa-check-double me-1"></i>Complete Inspections
                            </button>
                        </div>
                    </div>
                </div>

            </div><!-- end modal-body -->
        </div><!-- end modal-content -->
    </div><!-- end modal-dialog -->
</div>


<!-- View Inspection Modal -->
<!-- View Inspection Modal -->
<div class="modal fade" id="viewInspectionModal" tabindex="-1" aria-labelledby="viewInspectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewInspectionModalLabel">Inspection Group Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="inspection-details-group">
                    <!-- Inspection Group Header -->
                    <h5>Inspection Report Overview</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Pass/Fail</th>
                                    <th>Customer Site</th>
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
                                    <th>Inspection Date</th>
                                    <th>Battery Expiration Date</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody id="inspection-details-body">
                                <!-- Inspection details will be populated here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- ================================================
     EDIT INSPECTION 3-STEP WIZARD MODAL (NEW)
     This is separate from the add inspection wizard
     ================================================ -->
<div class="modal fade" id="editInspectionWizardModal" tabindex="-1" aria-labelledby="editInspectionWizardLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Wizard Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="editInspectionWizardLabel">Edit Inspection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Hidden fields for tracking -->
                <input type="hidden" id="edit-wiz-inspection-id">
                <input type="hidden" id="edit-wiz-site-id" value="<?= $site['id'] ?>">
                <input type="hidden" id="edit-wiz-group-id">
                <input type="hidden" id="edit-wiz-equipment-id">
                <input type="hidden" id="edit-wiz-asset-not-found" value="0">

                <!-- ═════════════════════════════════════════
                     STEP 1: Select Site & Equipment
                     ═════════════════════════════════════════ -->
                <div class="wizard-step active" id="edit-wiz-step1">
                    <h5>Step 1: Select Location & Equipment</h5>
                    <p class="step-subtitle">Choose the site and search for equipment</p>

                    <!-- Site Selection -->
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <label for="edit-wiz-s1-site" class="form-label">Customer Site <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-wiz-s1-site" name="site_id">
                                <option value="<?= $site['id'] ?>" selected><?= esc($site['name']) ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Search by Serial Number -->
                    <div class="row g-3 mt-3">
                        <div class="col-12">
                            <label for="edit-wiz-s1-serial" class="form-label">Search Equipment by Serial # <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="edit-wiz-s1-serial" 
                                   placeholder="Enter serial number"
                                   autocomplete="off">
                            <div class="helper-text">Type the serial number and press Enter or click Search</div>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" id="edit-wiz-s1-search-btn">
                                <i class="fa fa-search me-1"></i> Search
                            </button>
                        </div>
                    </div>

                    <!-- Alert if asset not found -->
                    <div id="edit-wiz-asset-not-found-alert" class="asset-not-found-alert" style="display:none;">
                        <i class="fa fa-exclamation-triangle me-1"></i>
                        Asset not found! Please enter details manually in Step 2.
                    </div>

                    <!-- Footer buttons -->
                    <div class="wizard-footer">
                        <div class="left-btns"></div>
                        <div class="right-btns">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="edit-wiz-step1-next">
                                Next <i class="fa fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ═════════════════════════════════════════
                     STEP 2: Enter/Confirm Equipment Details
                     ═════════════════════════════════════════ -->
                <div class="wizard-step" id="edit-wiz-step2">
                    <h5>Step 2: Equipment Details</h5>
                    <p class="step-subtitle">Review or enter equipment information</p>

                    <div class="row g-3 mt-2">
                        <!-- Manufacturer -->
                        <div class="col-md-6">
                            <label for="edit-wiz-s2-manufacturer" class="form-label">Manufacturer</label>
                            <input type="text" class="form-control" id="edit-wiz-s2-manufacturer" name="manufacturer">
                        </div>
                        
                        <!-- Model -->
                        <div class="col-md-6">
                            <label for="edit-wiz-s2-model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="edit-wiz-s2-model" name="model_name">
                        </div>

                        <!-- Description/Device Type -->
                        <div class="col-md-6">
                            <label for="edit-wiz-s2-description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="edit-wiz-s2-description" name="description">
                        </div>

                        <!-- Serial Number -->
                        <div class="col-md-6">
                            <label for="edit-wiz-s2-serial" class="form-label">Serial #</label>
                            <input type="text" class="form-control" id="edit-wiz-s2-serial" name="serial_number">
                        </div>

                        <!-- Asset ID -->
                        <div class="col-md-4">
                            <label for="edit-wiz-s2-assetid" class="form-label">Asset ID</label>
                            <input type="text" class="form-control" id="edit-wiz-s2-assetid" name="asset_tag">
                        </div>

                        <!-- Department -->
                        <div class="col-md-4">
                            <label for="edit-wiz-s2-department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="edit-wiz-s2-department" name="department">
                        </div>

                        <!-- Location/Room -->
                        <div class="col-md-4">
                            <label for="edit-wiz-s2-location" class="form-label">Location / Room</label>
                            <input type="text" class="form-control" id="edit-wiz-s2-location" name="location">
                        </div>
                    </div>

                    <!-- Footer buttons -->
                    <div class="wizard-footer">
                        <div class="left-btns">
                            <button type="button" class="btn btn-outline-secondary" id="edit-wiz-step2-back">
                                <i class="fa fa-arrow-left me-1"></i> Back
                            </button>
                        </div>
                        <div class="right-btns">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="edit-wiz-step2-next">
                                Next <i class="fa fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ═════════════════════════════════════════
                     STEP 3: Enter Inspection Details
                     ═════════════════════════════════════════ -->
                <div class="wizard-step" id="edit-wiz-step3">
                    <h5>Step 3: Enter Inspection Details</h5>
                    <p class="step-subtitle">Complete inspection information</p>

                    <!-- Row 1: Model | Description | Serial # (readonly from Step 2) -->
                    <div class="row g-3 mt-2 step3-readonly-row">
                        <div class="col-md-4">
                            <label class="form-label">Model</label>
                            <input type="text" class="form-control" id="edit-wiz-s3-model-ro" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" id="edit-wiz-s3-desc-ro" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Serial #</label>
                            <input type="text" class="form-control" id="edit-wiz-s3-serial-ro" readonly>
                        </div>
                    </div>

                    <!-- Row 2: Asset ID | Department | Location/Room (readonly from Step 2) -->
                    <div class="row g-3 mt-1 step3-readonly-row">
                        <div class="col-md-4">
                            <label class="form-label">Asset ID</label>
                            <input type="text" class="form-control" id="edit-wiz-s3-assetid-ro" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" id="edit-wiz-s3-dept-ro" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Location / Room</label>
                            <input type="text" class="form-control" id="edit-wiz-s3-location-ro" readonly>
                        </div>
                    </div>

                    <!-- Row 3: PM Service Frequency | Inspection Type -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="edit-wiz-s3-pmfreq" class="form-label">PM Service Frequency</label>
                            <select class="form-select" id="edit-wiz-s3-pmfreq" name="pm_frequency">
                                <option value="">Select frequency</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Semi-Annual">Semi-Annual</option>
                                <option value="Annual">Annual</option>
                                <option value="As Needed">As Needed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-wiz-s3-insptype" class="form-label">Inspection Type</label>
                            <select class="form-select" id="edit-wiz-s3-insptype" name="inspection_type">
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
                            <label for="edit-wiz-s3-technician" class="form-label">Technician</label>
                            <select class="form-select" id="edit-wiz-s3-technician" name="technician_id">
                                <option value="">-- Select Technician --</option>
                                <?php if (!empty($technicians)): ?>
                                    <?php foreach ($technicians as $tech): ?>
                                        <option value="<?= $tech['id'] ?>"><?= esc($tech['full_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-wiz-s3-inspdate" class="form-label">Inspection Date</label>
                            <input type="date" class="form-control" id="edit-wiz-s3-inspdate" name="scheduled_at">
                        </div>
                    </div>

                    <!-- Row 5: Service Notes (full width) -->
                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <label for="edit-wiz-s3-notes" class="form-label">Service Notes</label>
                            <textarea class="form-control" id="edit-wiz-s3-notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Row 6: Status | Device Complete -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="edit-wiz-s3-status" class="form-label">Status</label>
                            <select class="form-select" id="edit-wiz-s3-status" name="status">
                                <option value="Pass">Pass</option>
                                <option value="Fail">Fail</option>
                                <option value="Repair">Repair</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit-wiz-s3-devicecomplete" class="form-label">Device Complete</label>
                            <select class="form-select" id="edit-wiz-s3-devicecomplete" name="device_complete">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Helper text -->
                    <p class="text-center text-muted mt-3 mb-1" style="font-size:0.85rem;">
                        Update device notes and complete status.
                    </p>

                    <!-- Outcome buttons: Pass | Fail | Repair -->
                    <div class="inspection-outcome-btns">
                        <button type="button" class="btn-pass" id="edit-wizBtnPass">Pass Inspection</button>
                        <button type="button" class="btn-fail" id="edit-wizBtnFail">Fail Inspection</button>
                        <button type="button" class="btn-repair" id="edit-wizBtnRepair">Repair Inspection</button>
                    </div>

                    <!-- Footer with Back / Cancel / Update -->
                    <div class="wizard-footer">
                        <div class="left-btns">
                            <button type="button" class="btn btn-outline-secondary" id="edit-wiz-step3-back">
                                <i class="fa fa-arrow-left me-1"></i> Back
                            </button>
                        </div>
                        <div class="right-btns">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="edit-wizBtnUpdate">
                                <i class="fa fa-save me-1"></i> Update Inspection
                            </button>
                        </div>
                    </div>
                </div>

            </div><!-- end modal-body -->
        </div><!-- end modal-content -->
    </div><!-- end modal-dialog -->
</div>

<!-- ================================================
     UPDATE TO VIEW INSPECTION MODAL - ADD EDIT BUTTONS
     ================================================ -->
<script>
// Update the existing viewInspectionModal table to include Edit button
// This should be added to your existing JavaScript section

// Add this function to handle viewing inspection details with edit buttons
function viewInspectionGroup(groupId) {
    $.ajax({
        url: '<?= site_url('admin/inspections/getByGroupId') ?>',
        type: 'GET',
        data: { group_id: groupId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let tbody = $('#inspection-details-body');
                tbody.empty();
                
                response.data.forEach(function(inspection) {
                    let row = `
                        <tr>
                            <td>
                                <button class="btn btn-sm btn-primary btn-edit-inspection" 
                                        data-inspection-id="${inspection.inspections_id}"
                                        data-group-id="${inspection.group_id}">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                            </td>
                            <td><span class="status-badge status-${inspection.status.toLowerCase()}">${inspection.status || 'N/A'}</span></td>
                            <td>${inspection.customer_site || 'N/A'}</td>
                            <td>${inspection.model || 'N/A'}</td>
                            <td>${inspection.device_type || 'N/A'}</td>
                            <td>${inspection.serial_number || 'N/A'}</td>
                            <td>${inspection.inspection_type || 'N/A'}</td>
                            <td>${inspection.asset_tag || 'N/A'}</td>
                            <td>${inspection.department || 'N/A'}</td>
                            <td>${inspection.location || 'N/A'}</td>
                            <td>${inspection.technician_name || 'N/A'}</td>
                            <td>N/A</td>
                            <td>N/A</td>
                            <td>${inspection.scheduled_at ? new Date(inspection.scheduled_at).toLocaleDateString() : 'N/A'}</td>
                            <td>N/A</td>
                            <td>${inspection.notes || 'N/A'}</td>
                            
                        </tr>
                    `;
                    tbody.append(row);
                });
                
                $('#viewInspectionModal').modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to fetch inspection details');
        }
    });
}

// ================================================
// EDIT INSPECTION WIZARD JAVASCRIPT
// ================================================

$(document).ready(function() {
    
    // ─────────────────────────────────────────────
    // Handle Edit Button Click from View Modal
    // ─────────────────────────────────────────────
    $(document).on('click', '.btn-edit-inspection', function() {
        const inspectionId = $(this).data('inspection-id');
        const groupId = $(this).data('group-id');
        
        // Load inspection data
        loadInspectionForEdit(inspectionId, groupId);
    });
    
    // ─────────────────────────────────────────────
    // Load Inspection Data for Editing
    // ─────────────────────────────────────────────
    function loadInspectionForEdit(inspectionId, groupId) {
        $.ajax({
            url: '<?= site_url('admin/inspections/getInspectionById') ?>/' + inspectionId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Store inspection ID and group ID
                    $('#edit-wiz-inspection-id').val(data.id);
                    $('#edit-wiz-group-id').val(data.group_id);
                    $('#edit-wiz-equipment-id').val(data.equipment_id);
                    
                    // Populate Step 1
                    $('#edit-wiz-s1-serial').val(data.serial_number || '');
                    
                    // Populate Step 2
                    $('#edit-wiz-s2-manufacturer').val(data.make || '');
                    $('#edit-wiz-s2-model').val(data.model || '');
                    $('#edit-wiz-s2-description').val(data.device_type || '');
                    $('#edit-wiz-s2-serial').val(data.serial_number || '');
                    $('#edit-wiz-s2-assetid').val(data.asset_tag || '');
                    $('#edit-wiz-s2-department').val(data.department || '');
                    $('#edit-wiz-s2-location').val(data.location || '');
                    
                    // Populate Step 3 readonly fields
                    $('#edit-wiz-s3-model-ro').val(data.model || '');
                    $('#edit-wiz-s3-desc-ro').val(data.device_type || '');
                    $('#edit-wiz-s3-serial-ro').val(data.serial_number || '');
                    $('#edit-wiz-s3-assetid-ro').val(data.asset_tag || '');
                    $('#edit-wiz-s3-dept-ro').val(data.department || '');
                    $('#edit-wiz-s3-location-ro').val(data.location || '');
                    
                    // Populate Step 3 editable fields
                    $('#edit-wiz-s3-pmfreq').val(data.pm_frequency || '');
                    $('#edit-wiz-s3-insptype').val(data.inspection_type || '');
                    $('#edit-wiz-s3-technician').val(data.technician_id || '');
                    $('#edit-wiz-s3-inspdate').val(data.scheduled_at ? data.scheduled_at.split(' ')[0] : '');
                    $('#edit-wiz-s3-notes').val(data.notes || '');
                    $('#edit-wiz-s3-status').val(data.status || 'Pass');
                    $('#edit-wiz-s3-devicecomplete').val(data.device_complete || 'Yes');
                    
                    // Hide view modal and show edit wizard
                    $('#viewInspectionModal').modal('hide');
                    $('#editInspectionWizardModal').modal('show');
                    
                    // Reset to step 1
                    editWizardGoToStep(1);
                }
            },
            error: function() {
                alert('Failed to load inspection data');
            }
        });
    }
    
    // ─────────────────────────────────────────────
    // Edit Wizard Navigation
    // ─────────────────────────────────────────────
    let editCurrentStep = 1;
    
    function editWizardGoToStep(stepNum) {
        $('#editInspectionWizardModal .wizard-step').removeClass('active');
        $(`#edit-wiz-step${stepNum}`).addClass('active');
        editCurrentStep = stepNum;
    }
    
    // Step 1: Search Equipment
    $('#edit-wiz-s1-search-btn, #edit-wiz-s1-serial').on('keypress click', function(e) {
        if (e.type === 'click' || (e.type === 'keypress' && e.which === 13)) {
            e.preventDefault();
            const serialNumber = $('#edit-wiz-s1-serial').val().trim();
            
            if (serialNumber) {
                searchEditEquipmentBySerial(serialNumber);
            }
        }
    });
    
    function searchEditEquipmentBySerial(serial) {
        $.ajax({
            url: '<?= site_url('admin/inspections/searchBySerial') ?>',
            type: 'GET',
            data: { 
                serial_number: serial,
                site_id: $('#edit-wiz-site-id').val()
            },
            dataType: 'json',
            success: function(result) {
                if (result.found) {
                    // Found - populate step 2
                    $('#edit-wiz-equipment-id').val(result.id);
                    $('#edit-wiz-asset-not-found').val('0');
                    $('#edit-wiz-asset-not-found-alert').hide();
                    
                    $('#edit-wiz-s2-manufacturer').val(result.make || '');
                    $('#edit-wiz-s2-model').val(result.model || '');
                    $('#edit-wiz-s2-description').val(result.device_type || '');
                    $('#edit-wiz-s2-serial').val(result.serial_number || '');
                    $('#edit-wiz-s2-assetid').val(result.asset_tag || '');
                    $('#edit-wiz-s2-department').val(result.department || '');
                    $('#edit-wiz-s2-location').val(result.location || '');
                } else {
                    // Not found - allow manual entry
                    $('#edit-wiz-equipment-id').val('');
                    $('#edit-wiz-asset-not-found').val('1');
                    $('#edit-wiz-asset-not-found-alert').show();
                    
                    $('#edit-wiz-s2-serial').val(serial);
                }
            }
        });
    }
    
    // Step 1 Next
    $('#edit-wiz-step1-next').click(function() {
        editWizardGoToStep(2);
    });
    
    // Step 2 Back
    $('#edit-wiz-step2-back').click(function() {
        editWizardGoToStep(1);
    });
    
    // Step 2 Next
    $('#edit-wiz-step2-next').click(function() {
        // Sync step 2 data to step 3 readonly fields
        $('#edit-wiz-s3-model-ro').val($('#edit-wiz-s2-model').val());
        $('#edit-wiz-s3-desc-ro').val($('#edit-wiz-s2-description').val());
        $('#edit-wiz-s3-serial-ro').val($('#edit-wiz-s2-serial').val());
        $('#edit-wiz-s3-assetid-ro').val($('#edit-wiz-s2-assetid').val());
        $('#edit-wiz-s3-dept-ro').val($('#edit-wiz-s2-department').val());
        $('#edit-wiz-s3-location-ro').val($('#edit-wiz-s2-location').val());
        
        editWizardGoToStep(3);
    });
    
    // Step 3 Back
    $('#edit-wiz-step3-back').click(function() {
        editWizardGoToStep(2);
    });
    
    // ─────────────────────────────────────────────
    // Step 3: Outcome Buttons (Pass/Fail/Repair)
    // ─────────────────────────────────────────────
    $('#edit-wizBtnPass').click(function() {
        $('#edit-wiz-s3-status').val('Pass');
    });
    
    $('#edit-wizBtnFail').click(function() {
        $('#edit-wiz-s3-status').val('Fail');
    });
    
    $('#edit-wizBtnRepair').click(function() {
        $('#edit-wiz-s3-status').val('Repair');
    });
    
    // ─────────────────────────────────────────────
    // Update Inspection
    // ─────────────────────────────────────────────
    $('#edit-wizBtnUpdate').click(function() {
        const inspectionId = $('#edit-wiz-inspection-id').val();
        const groupId = $('#edit-wiz-group-id').val();
        
        const formData = {
            inspection_id: inspectionId,
            equipment_id: $('#edit-wiz-equipment-id').val(),
            site_id: $('#edit-wiz-site-id').val(),
            asset_not_found: $('#edit-wiz-asset-not-found').val(),
            
            // Step 2 data
            manufacturer: $('#edit-wiz-s2-manufacturer').val(),
            model_name: $('#edit-wiz-s2-model').val(),
            description: $('#edit-wiz-s2-description').val(),
            serial_number: $('#edit-wiz-s2-serial').val(),
            asset_tag: $('#edit-wiz-s2-assetid').val(),
            department: $('#edit-wiz-s2-department').val(),
            location: $('#edit-wiz-s2-location').val(),
            
            // Step 3 data
            pm_frequency: $('#edit-wiz-s3-pmfreq').val(),
            inspection_type: $('#edit-wiz-s3-insptype').val(),
            technician_id: $('#edit-wiz-s3-technician').val(),
            scheduled_at: $('#edit-wiz-s3-inspdate').val(),
            notes: $('#edit-wiz-s3-notes').val(),
            status: $('#edit-wiz-s3-status').val(),
            device_complete: $('#edit-wiz-s3-devicecomplete').val()
        };
        
        $.ajax({
            url: '<?= site_url('admin/inspections/updateInspection') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Inspection updated successfully!');
                    $('#editInspectionWizardModal').modal('hide');
                    
                    // Reopen the view modal with updated data
                    viewInspectionGroup(groupId);
                    
                    // Optionally reload the page
                    // location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Failed to update inspection');
            }
        });
    });
    
    // ─────────────────────────────────────────────
    // Reset wizard when modal is closed
    // ─────────────────────────────────────────────
    $('#editInspectionWizardModal').on('hidden.bs.modal', function() {
        editWizardGoToStep(1);
        $('#edit-wiz-asset-not-found-alert').hide();
        $('#editInspectionWizardModal form').trigger('reset');
    });
    
});
</script>

<!-- end inspectionWizardModal -->


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
                                <option value="pending">Pending</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
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
                                <option value="open">Open</option>
                                <option value="in progress">In Progress</option>
                                <option value="on hold">On Hold</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="workorder-priority" name="priority" required>
                                <option value="">-- Select Priority --</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
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
   
    // Handle Edit Equipment button click
    $(document).on('click', '.edit-equipment-btn', function() {
        var equipmentId = $(this).data('id');
        
        // Change modal title
        $('#equipmentModalLabel').text('Edit Equipment');
        
        // Change form action to update
        $('#equipmentForm').attr('action', '<?= site_url('admin/equipment/update/') ?>' + equipmentId);
        
        // Fetch equipment data via AJAX
        $.ajax({
            url: '<?= site_url('admin/equipment/show/') ?>' + equipmentId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var data = response.data;
                    
                    // Populate form fields
                    $('#equipment-id').val(data.id);
                    $('#equipment-asset-tag').val(data.asset_tag);
                    $('#equipment-serial-number').val(data.serial_number);
                    $('#equipment-make').val(data.make);
                    $('#equipment-model').val(data.model);
                    $('#equipment-device-type').val(data.device_type);
                    $('#equipment-department').val(data.department);
                    $('#equipment-location').val(data.location);
                    $('#equipment-status').val(data.status);
                    $('#equipment-pm-kit').val(data.pm_kit);
                    $('#equipment-fast-notes').val(data.fast_notes);
                    $('#equipment-customer-location').val(data.site_id);
                    $('#equipment-installation-date').val(data.installation_date);
                    $('#equipment-warranty-expires').val(data.warranty_expires);
                    
                    // Show modal
                    $('#addEquipmentModal').modal('show');
                } else {
                    alert('Error loading equipment data');
                }
            },
            error: function() {
                alert('Failed to fetch equipment data');
            }
        });
    });
    
    // Reset form when adding new equipment
    $('#addEquipmentBtn').on('click', function() {
        $('#equipmentModalLabel').text('Add Equipment');
        $('#equipmentForm')[0].reset();
        $('#equipment-id').val('');
        $('#equipmentForm').attr('action', '<?= site_url('admin/equipment/create') ?>');
        // Set default site_id
        $('#equipment-customer-location').val('<?= $site['id'] ?>');
    });
    
    // Handle form submission
    $('#equipmentForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var actionUrl = $(this).attr('action');
        
        $('#equipmentSubmitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Saving...');
        
        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#addEquipmentModal').modal('hide');
                location.reload(); // Reload to show updated data
            },
            error: function(xhr) {
                alert('Error saving equipment. Please try again.');
                console.error(xhr.responseText);
                $('#equipmentSubmitBtn').prop('disabled', false).html('Save changes');
            }
        });
    });
    
    // Reset form when modal is closed
    $('#addEquipmentModal').on('hidden.bs.modal', function() {
        $('#equipmentForm')[0].reset();
        $('#equipment-id').val('');
        $('#equipmentModalLabel').text('Add Equipment');
        $('#equipmentForm').attr('action', '<?= site_url('admin/equipment/create') ?>');
        $('#equipmentSubmitBtn').prop('disabled', false).html('Save changes');
    });


    $('#edit-inspection-completed-at').val(formatDate($(this).data('completed_at')));
    $('#edit-inspection-next-due-date').val(formatDate($(this).data('next_due_date')));

    function formatDate(dateStr) {
        if (!dateStr) return '';
        var date = new Date(dateStr);
        var year = date.getFullYear();
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var day = ("0" + date.getDate()).slice(-2);
        return `${year}-${month}-${day}`;
    }

    // ─── Edit Inspection (existing records — classic single modal) ───
    $(document).on('click', '.edit-inspection-btn', function() {
        var id = $(this).data('id');
        $('#edit-inspection-id').val(id);
        $('#edit-inspection-equipment').val($(this).data('equipment_id'));
        $('#edit-inspection-scheduled-at').val($(this).data('scheduled_at'));
        $('#edit-inspection-completed-at').val(formatDate($(this).data('completed_at')));
        $('#edit-inspection-status').val($(this).data('status'));
        $('#edit-inspection-technician').val($(this).data('technician_id'));
        $('#edit-inspection-findings').val($(this).data('findings'));
        $('#edit-inspection-notes').val($(this).data('notes'));
        $('#edit-inspection-next-due-date').val(formatDate($(this).data('next_due_date')));

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
        $('#workorder-equipment').val($(this).data('equipment_id'));
        
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

    // ═══════════════════════════════════════════════════════════════
//  UPDATED 3-STEP INSPECTION WIZARD LOGIC WITH SERIAL NUMBER & QUEUE
// ═══════════════════════════════════════════════════════════════

// Base URLs for AJAX calls - CHANGE searchByAssetTag to searchBySerial
var WIZ_URL_SEARCH_SERIAL = '<?= site_url('admin/inspections/searchBySerial') ?>';
var WIZ_URL_SEARCH_MODEL = '<?= site_url('admin/inspections/searchByModel') ?>';
var WIZ_SITE_ID = '<?= $site['id'] ?>';

// Internal state for the wizard
var wizardAssetFound = false;
var wizardMatchedEquip = null;
var inspectionQueue = [];
var groupId = '';

// Generate unique group ID
function generateGroupId() {
    return 'INSP-' + new Date().toISOString().split('T')[0].replace(/-/g, '') + '-' + Math.random().toString(36).substr(2, 9).toUpperCase();
}

// ─── Helper: show only one step ─────────────────────
function showStep(stepNum) {
    $('#wizardStep1, #wizardStep2, #wizardStep3').removeClass('active');
    $('#wizardStep' + stepNum).addClass('active');
    
    // Show/hide Complete button
    if (stepNum === 3 && inspectionQueue.length > 0) {
        $('#wizBtnComplete').show();
    } else {
        $('#wizBtnComplete').hide();
    }
}

// ─── Helper: reset the entire wizard ────────────────
function resetWizard() {
    wizardAssetFound = false;
    wizardMatchedEquip = null;

    // Step 1 - CHANGED: wiz-asset-barcode to wiz-serial-number
    $('#wiz-serial-number').val('');

    // Step 2
    $('#wiz-search-model').val('');
    $('#wiz-model-dropdown').remove();
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
    $('#wiz-s3-inspdate').val('<?= date('Y-m-d') ?>');
    $('#wiz-s3-notes').val('');
    $('#wiz-s3-status').val('Pass');
    $('#wiz-s3-devicecomplete').val('Yes');

    showStep(1);
}

// ─── Update Queue Display ─────────────────────
function updateQueueDisplay() {
    var $container = $('#inspectionQueue');
    $container.empty();
    
    if (inspectionQueue.length === 0) {
        $('#inspectionQueueContainer').hide();
        $('#wizBtnComplete').hide();
        return;
    }

    $('#inspectionQueueContainer').show();
    $('#queueCount').text(inspectionQueue.length);
    
    inspectionQueue.forEach(function(item, index) {
        var $queueItem = $('<div class="queue-item">');
        
        var $info = $('<div class="queue-item-info">');
        var modelText = (item.make || '') + ' ' + (item.model || '');
        $info.append('<div class="queue-item-model">' + modelText.trim() + '</div>');
        $info.append('<div class="queue-item-details">S/N: ' + (item.serial_number || 'N/A') + ' | Status: ' + item.status + '</div>');
        
        var $removeBtn = $('<button class="queue-item-remove" data-index="' + index + '">Remove</button>');
        
        $queueItem.append($info).append($removeBtn);
        $container.append($queueItem);
    });

    if (inspectionQueue.length > 0) {
        $('#wizBtnComplete').show();
    }
}

// Remove item from queue
$(document).on('click', '.queue-item-remove', function() {
    var index = $(this).data('index');
    inspectionQueue.splice(index, 1);
    updateQueueDisplay();
});

// ─── Open wizard ─────────────────────────────────────
$('#openInspectionWizardBtn').on('click', function() {
    inspectionQueue = [];
    groupId = generateGroupId();
    $('#wiz-group-id').val(groupId);
    resetWizard();
    updateQueueDisplay();
    $('#inspectionWizardModal').modal('show');
});

// ─── Reset wizard when modal is closed ──────────────
$('#inspectionWizardModal').on('hidden.bs.modal', function() {
    inspectionQueue = [];
    resetWizard();
    updateQueueDisplay();
});

// ─── STEP 1 ► Next (UPDATED FOR SERIAL NUMBER) ───
$('#wizStep1Next').on('click', function() {
    // CHANGED: get serial number instead of barcode
    var serialNumber = $.trim($('#wiz-serial-number').val());
    if (serialNumber === '') {
        alert('Please enter a serial number.');
        return;
    }

    $('#wizStep1Next').prop('disabled', true).text('Searching…');

    $.ajax({
        url: WIZ_URL_SEARCH_SERIAL,  // CHANGED URL
        method: 'GET',
        data: { serial_number: serialNumber, site_id: WIZ_SITE_ID },  // CHANGED parameter name
        success: function(res) {
            $('#wizStep1Next').prop('disabled', false).text('Next');

            if (res.found) {
                wizardAssetFound = true;
                wizardMatchedEquip = res;
                populateStep3FromEquipment(res);
                showStep(3);
            } else {
                wizardAssetFound = false;
                wizardMatchedEquip = null;
                // CHANGED: set serial number in step 2
                $('#wiz-s2-serial').val(serialNumber);
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

// ─── STEP 2 ► Live model search ────
var modelSearchTimer = null;

$('#wiz-search-model').on('keyup', function() {
    var val = $.trim($(this).val());

    if (val.length < 2) {
        $('#wiz-model-dropdown').remove();
        return;
    }

    clearTimeout(modelSearchTimer);
    modelSearchTimer = setTimeout(function() {
        $.ajax({
            url: WIZ_URL_SEARCH_MODEL,
            method: 'GET',
            data: { keyword: val },
            success: function(results) {
                $('#wiz-model-dropdown').remove();

                if (!results || results.length === 0) return;

                var $wrap = $('#wiz-search-model').parent();
                $wrap.css('position', 'relative');

                var html = '<div id="wiz-model-dropdown" style="position:absolute; top:100%; left:0; right:0; z-index:9999; background:#fff; border:1px solid #cbd5e1; border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,0.12); max-height:220px; overflow-y:auto; margin-top:2px;">';

                $.each(results, function(i, item) {
                    var label = (item.make || '') + ' ' + (item.model || '');
                    label = $.trim(label) || item.device_type || 'Unknown';

                    html += '<div class="wiz-model-option" data-make="' + (item.make || '') + '" data-model="' + (item.model || '') + '" data-serial_number="' + (item.serial_number || '') + '" data-device_type="' + (item.device_type || '') + '" style="padding:0.55rem 0.75rem; cursor:pointer; border-bottom:1px solid #f1f5f9;" onmouseover="$(this).css(\'background\',\'#eef2ff\')" onmouseout="$(this).css(\'background\',\'#fff\')">';
                    html += '<strong>' + label + '</strong>';
                    if (item.serial_number) {
                        html += ' &nbsp;<span style="color:#64748b; font-size:0.82rem;">S/N: ' + item.serial_number + '</span>';
                    }
                    html += '</div>';
                });

                html += '</div>';
                $('#wiz-search-model').after(html);
            }
        });
    }, 350);
});

// ─── STEP 2 ► Pick model from dropdown ───
$(document).on('click', '.wiz-model-option', function() {
    $('#wiz-s2-manufacturer').val($(this).data('make'));
    $('#wiz-s2-model').val($(this).data('model'));
    $('#wiz-s2-description').val($(this).data('device_type'));
    $('#wiz-search-model').val($.trim($(this).data('make') + ' ' + $(this).data('model')));
    $('#wiz-model-dropdown').remove();
});

// Close dropdown on outside click
$(document).on('click', function(e) {
    if (!$(e.target).is('#wiz-search-model') && !$(e.target).closest('#wiz-model-dropdown').length) {
        $('#wiz-model-dropdown').remove();
    }
});

// ─── STEP 2 ► Next ───
$('#wizStep2Next').on('click', function() {
    $('#wiz-s3-model').val($('#wiz-s2-model').val());
    $('#wiz-s3-description').val($('#wiz-s2-description').val());
    $('#wiz-s3-serial').val($('#wiz-s2-serial').val());
    $('#wiz-s3-assetid, #wiz-s3-department, #wiz-s3-location').val('');
    $('#wiz-equipment-id').val('');
    showStep(3);
});

// ─── STEP 3 ► Back ───
$('#wizStep3Back').on('click', function() {
    if (wizardAssetFound) {
        showStep(1);
    } else {
        showStep(2);
    }
});

// ─── Populate Step 3 from matched equipment ───
function populateStep3FromEquipment(eq) {
    $('#wiz-equipment-id').val(eq.id);
    $('#wiz-s3-model').val(eq.model);
    $('#wiz-s3-description').val(eq.device_type);
    $('#wiz-s3-serial').val(eq.serial_number);
    $('#wiz-s3-assetid').val(eq.asset_tag);
    $('#wiz-s3-department').val(eq.department);
    $('#wiz-s3-location').val(eq.location);
}

// ─── Add to Queue Function (NEW) ───
function addToQueue(status) {
    var inspectionData = {
        site_id: '<?= $site['id'] ?>',
        equipment_id: $('#wiz-equipment-id').val(),
        scheduled_at: $('#wiz-s3-inspdate').val(),
        status: status,
        technician_id: $('#wiz-s3-technician').val(),
        notes: $('#wiz-s3-notes').val(),
        pm_frequency: $('#wiz-s3-pmfreq').val(),
        inspection_type: $('#wiz-s3-insptype').val(),
        device_complete: $('#wiz-s3-devicecomplete').val(),
        
        // Equipment details for display
        make: wizardMatchedEquip ? (wizardMatchedEquip.make || '') : ($('#wiz-s2-manufacturer').val() || ''),
        model: $('#wiz-s3-model').val(),
        serial_number: $('#wiz-s3-serial').val(),
        device_type: $('#wiz-s3-description').val(),
        asset_tag: $('#wiz-s3-assetid').val(),
        department: $('#wiz-s3-department').val(),
        location: $('#wiz-s3-location').val()
    };

    if (!wizardAssetFound) {
        inspectionData.manufacturer = $('#wiz-s2-manufacturer').val();
        inspectionData.model_name = $('#wiz-s2-model').val();
        inspectionData.description = $('#wiz-s2-description').val();
        inspectionData.asset_not_found = '1';
    }

    inspectionQueue.push(inspectionData);
    updateQueueDisplay();
}

// ─── STEP 3 ► Outcome Buttons (UPDATED) ───
$('#wizBtnPass').on('click', function() {
    addToQueue('Pass');
    alert('Inspection added to queue. Click "Add to Queue & Next Device" to continue or "Complete Inspections" to finish.');
});

$('#wizBtnFail').on('click', function() {
    addToQueue('Fail');
    alert('Inspection added to queue. Click "Add to Queue & Next Device" to continue or "Complete Inspections" to finish.');
});

$('#wizBtnRepair').on('click', function() {
    addToQueue('Repair');
    alert('Inspection added to queue. Click "Add to Queue & Next Device" to continue or "Complete Inspections" to finish.');
});

// ─── Add to Queue & Next Device ───
$('#wizBtnNextDevice').on('click', function() {
    resetWizard();
    updateQueueDisplay();
});

// ─── Complete All Inspections (NEW) ───
$('#wizBtnComplete').on('click', function() {
    if (inspectionQueue.length === 0) {
        alert('No inspections in queue.');
        return;
    }

    console.log('Inspection Queue:', inspectionQueue);
    console.log('Group ID:', groupId);

    var formData = new FormData();
    var csrfName = $('input[name="csrf_token"]').first().attr('name');
    var csrfValue = $('input[name="csrf_token"]').first().val();
    
    if (csrfName && csrfValue) {
        formData.append(csrfName, csrfValue);
    }

    formData.append('group_id', groupId);
    formData.append('inspection_items', JSON.stringify(inspectionQueue));

    console.log('Sending data:', {
        group_id: groupId,
        inspection_items: JSON.stringify(inspectionQueue)
    });

    $('#wizBtnComplete').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

    $.ajax({
        url: '<?= site_url('admin/inspections/create') ?>',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log('Success response:', response);
            $('#inspectionWizardModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            console.error('Failed to save inspections', xhr);
            console.error('Response Text:', xhr.responseText);
            alert('Failed to save inspections. Check console for details.');
            $('#wizBtnComplete').prop('disabled', false).html('<i class="fa fa-check-double me-1"></i>Complete Inspections');
        }
    });
});


$(document).on('click', '.view-inspection-btn', function() {
    var groupId = $(this).data('group_id');  // Get the group_id from the button

    // Show loading indicator
    // $('#viewInspectionModal').find('.modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');

    // AJAX request to get inspections by group_id
    $.ajax({
        url: '<?= site_url('admin/inspections/getByGroupId') ?>', // Endpoint to fetch inspection data
        method: 'GET',
        data: { group_id: groupId },
        success: function(response) {
          
            if (response.success) {
                var inspections = response.data; // Assume response contains the inspections data

                // Generate HTML for the inspections list
                var inspectionHtml = '';
                inspections.forEach(function(inspec) {  
               
                    inspectionHtml += '<tr>';
                    inspectionHtml += '<td> <button class="btn btn-sm btn-primary btn-edit-inspection" data-inspection-id="'+inspec.inspections_id+'" data-group-id="'+inspec.group_id+'"><i class="fa fa-edit"></i> Edit</button></td>';
                    inspectionHtml += '<td>' + (inspec.status === 'Pass' ? '<span class="badge bg-success">Pass</span>' : '<span class="badge bg-danger">Fail</span>') + '</td>';
                    inspectionHtml += '<td>' + inspec.customer_site + '</td>';
                    inspectionHtml += '<td>' + inspec.model + '</td>';
                    inspectionHtml += '<td>' + inspec.device_type + '</td>';
                    inspectionHtml += '<td>' + inspec.serial_number + '</td>';
                    inspectionHtml += '<td>' + inspec.inspection_type + '</td>';
                    inspectionHtml += '<td>' + inspec.asset_tag + '</td>';
                    inspectionHtml += '<td>' + inspec.department + '</td>';
                    inspectionHtml += '<td>' + inspec.room + '</td>';
                    inspectionHtml += '<td>' + inspec.est + '</td>';
                    inspectionHtml += '<td>' + inspec.cal + '</td>';
                    inspectionHtml += '<td>' + inspec.technician_name + '</td>';
                    inspectionHtml += '<td>' + inspec.updated_at + '</td>';
                    inspectionHtml += '<td>' + inspec.battery_expiration_date + '</td>';
                    inspectionHtml += '<td>' + inspec.notes + '</td>';
                    inspectionHtml += '</tr>';
                });
                // Inject the generated HTML into the modal body
                $('#inspection-details-body').html(inspectionHtml);

                // Show the modal
                $('#viewInspectionModal').modal('show');
            } else {
                $('#viewInspectionModal').find('.modal-body').html('<div class="text-center text-danger">No inspections found for this group.</div>');
            }
        },
        error: function() {
            $('#viewInspectionModal').find('.modal-body').html('<div class="text-center text-danger">An error occurred while fetching the data.</div>');
        }
    });
});






});
</script>

<?= $this->endSection() ?>