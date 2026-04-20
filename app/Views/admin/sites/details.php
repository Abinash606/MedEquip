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

    #viewInspectionModal table th,
    #viewInspectionModal table td {
        text-align: center;
    }

    /* 
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    } */

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
        color: #fff;
        border-bottom: 3px solid #2563eb;
        background: linear-gradient(90deg, rgba(34, 211, 238, .92), rgba(124, 58, 237, .70)) !important;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-block;
    }

    .status-ready,
    .status-completed,
    .status-pass {
        background: #d1fae5;
        color: #065f46;
    }

    .status-need-attention,
    .status-fail {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-pending,
    .status-open {
        background: #fef3c7;
        color: #92400e;
    }

    .status-scheduled,
    .status-in-progress,
    .status-repair {
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

    .btn-pass:hover {
        background-color: #15803d;
    }

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

    .btn-fail:hover {
        background-color: #b91c1c;
    }

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

    .btn-repair:hover {
        background-color: #0284c7;
    }

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

    .btn-complete:hover {
        background-color: #6d28d9;
    }

    .wizard-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .wizard-footer .left-btns {
        display: flex;
        gap: 0.5rem;
    }

    .wizard-footer .right-btns {
        display: flex;
        gap: 0.5rem;
    }

    /* -----------------------------------------------------------------
       Custom Styles for the Site Inspection Workflow

       These styles are scoped to the #site-inspection-workflow container
       included in the Inspections tab.  They set up the colour palette,
       card appearance, buttons, navigation tabs, table design and
       utility classes needed to mirror the design of the provided
       inspection workflow snippet.  Because they are prefixed with
       #site-inspection-workflow, they will not leak into other parts
       of the site details page.
    ----------------------------------------------------------------- */
    #site-inspection-workflow {
        --primary-color: #2563eb;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --bg-color: #f1f5f9;
        --card-bg: #ffffff;
        --border-radius: 12px;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* #site-inspection-workflow {
        font-family: "Inter", sans-serif;
        background-color: var(--bg-color);
        color: #1e293b;
        padding-bottom: 50px;
    } */
    /* #site-inspection-workflow .card-custom {
        background: var(--card-bg);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
    } */
    #site-inspection-workflow .btn-custom-primary {
        background-color: var(--primary-color);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: var(--shadow-sm);
    }

    #site-inspection-workflow .btn-custom-primary:hover {
        background-color: #1d4ed8;
        box-shadow: var(--shadow-md);
    }

    #site-inspection-workflow .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        /* border: 1px solid #e2e8f0;
        background: white;
        color: var(--secondary-color); */
        transition: all 0.2s;
    }

    #site-inspection-workflow .btn-icon:hover {
        background-color: #f8fafc;
        color: var(--primary-color);
    }

    #site-inspection-workflow .export-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    /* #site-inspection-workflow .export-btn {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        color: var(--secondary-color);
        font-weight: 500;
    } */
    #site-inspection-workflow .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    #site-inspection-workflow .status-in-progress {
        background-color: #eff6ff;
        color: var(--primary-color);
        border: 1px solid #bfdbfe;
    }

    #site-inspection-workflow .status-closed {
        background-color: #ecfdf5;
        color: var(--success-color);
        border: 1px solid #a7f3d0;
    }

    /* #site-inspection-workflow .asset-input-wrapper {
        background: white;
        padding: 30px;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        text-align: center;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    } */
    #site-inspection-workflow .big-input {
        font-size: 1.25rem;
        padding: 15px;
        border-radius: 8px 0 0 8px;
        border: 1px solid #cbd5e1;
    }

    #site-inspection-workflow .big-btn {
        padding: 0 30px;
        font-size: 1.1rem;
        border-radius: 0 8px 8px 0;
    }

    #site-inspection-workflow .nav-tabs {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }

    #site-inspection-workflow .nav-link {
        color: var(--secondary-color);
        font-weight: 500;
        border: none;
        padding: 12px 14px;
        font-size: 14px;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }

    #site-inspection-workflow .nav-link:hover {
        color: var(--primary-color);
        border-color: transparent;
    }

    #site-inspection-workflow .nav-link.active {
        color: #fff !important;
        border-bottom: 2px solid var(--primary-color) !important;
        background: linear-gradient(135deg, rgba(124, 58, 237, 1), rgba(34, 211, 238, 1)) !important !important;
    }

    #site-inspection-workflow .table-custom th {
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 8px;
    }

    #site-inspection-workflow .table-custom td {
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 12px 8px;
        border-bottom: 1px solid #f1f5f9;
    }

    #site-inspection-workflow .fade-in {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #site-inspection-workflow .d-none-view {
        display: none !important;
    }

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
        width: 100%;
        /* Makes sure the image is responsive */
        height: 100%;
        /* Ensures the image fills the container */
        object-fit: cover;
        /* Ensures the image scales and stays within the circle */
    }

    .suggest-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 9999;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-height: 200px;
        overflow-y: auto;
        padding: 0;
        margin: 2px 0 0 0;
        list-style: none;
    }

    .suggest-dropdown li {
        padding: 8px 12px;
        cursor: pointer;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }

    .suggest-dropdown li:last-child {
        border-bottom: none;
    }

    .suggest-dropdown li:hover,
    .suggest-dropdown li.active {
        background: #eef2ff;
        color: #2563eb;
    }
</style>

<!-- Back Button -->
<div class="mb-3 topbar">
    <button class="btn btn-primary mb-3" onclick="window.location.href='<?= site_url('admin/sites') ?>'">
        <i class="fa fa-arrow-left me-2"></i> Back to Sites
    </button>
</div>
<div class="content">
    <!-- Site Information Card (collapsible) -->
    <div class="glass-card mb-4 p-0" id="siteInfoCard">

        <!-- Clickable header bar — always visible, toggles the body -->
        <div id="siteInfoToggleBar"
            style="cursor:pointer; padding:10px 18px; display:flex; align-items:center; justify-content:space-between; border-radius:inherit;"
            onclick="toggleSiteInfo()">
            <span class="fw-semibold text-secondary" style="font-size:0.85rem; letter-spacing:0.04em;">
                <i class="fa-solid fa-circle-info me-2 text-primary"></i>
                Site Information &mdash; <span style="font-weight:400;"><?= esc($site['name']) ?></span>
            </span>
            <span id="siteInfoChevron" style="transition:transform 0.3s ease; display:inline-block;">
                <i class="fa-solid fa-chevron-up"></i>
            </span>
        </div>

        <!-- Collapsible body -->
        <div id="siteInfoBody"
            style="overflow:hidden; transition:max-height 0.35s ease, opacity 0.3s ease; max-height:400px; opacity:1; padding:0 18px 16px;">
            <div class="row align-items-center">
                <div class="col-md-auto me-4">
                    <div class="site-avatar" id="site-details-logo">
                        <?php
                        $logoPath = $customer['logo_path'];
                        $logoUrl  = base_url('uploads/logos/' . $logoPath);
                        if (file_exists(FCPATH . 'uploads/logos/' . $logoPath) && !empty($logoPath)) {
                            echo '<img src="' . $logoUrl . '" alt="Customer Logo" />';
                        } else {
                            $nameParts = explode(' ', $customer['name']);
                            $initials  = '';
                            if (count($nameParts) >= 2) {
                                $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
                            } else {
                                $initials = strtoupper(substr($customer['name'], 0, 2));
                            }
                            echo $initials;
                        }
                        ?>
                    </div>

                </div>
                <div class="col-md">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Site Name:</strong> <span id="site-details-name"><?= esc($site['name']) ?></span>
                            </p>
                            <p><strong>Site ID:</strong> <span id="site-details-id"><?= esc($site['id']) ?></span></p>
                            <p><strong>Site Contact Name:</strong> <span
                                    id="site-details-contact-name"><?= esc($site['contact_name'] ?? 'N/A') ?></span></p>
                            <p><strong>Site Email:</strong> <span
                                    id="site-details-email"><?= esc($site['email'] ?? 'N/A') ?></span></p>
                            <p><strong>Site Phone Number:</strong> <span
                                    id="site-details-phone"><?= esc($site['phone'] ?? 'N/A') ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Customer Name:</strong> <span
                                    id="site-details-customer-name"><?= esc($customer['name'] ?? 'N/A') ?></span></p>                            
                            <p><strong>Site Address:</strong> <span
                                    id="site-details-address"><?= esc($site['address'] ?? 'N/A') ?>,
                                    <?= esc($site['city'] ?? '') ?></span></p>
                            <p><strong>State:</strong> <span
                                    id="site-details-state"><?= esc($site['state'] ?? 'N/A') ?></span></p>
                            <p><strong>Zip Code:</strong> <span
                                    id="site-details-zip"><?= esc($site['zip'] ?? 'N/A') ?></span></p>
                            <p><strong>Internal Labor Rate Notes:</strong> <span
                                    id="site-details-customer-name"><?= esc($customer['internal_labor_rate_notes'] ?? 'N/A') ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var _siteInfoOpen = true; // default: open

            window.toggleSiteInfo = function() {
                var body = document.getElementById('siteInfoBody');
                var chevron = document.getElementById('siteInfoChevron');
                if (!body) return;
                if (_siteInfoOpen) {
                    // Collapse: measure current height, then animate to 0
                    body.style.maxHeight = body.scrollHeight + 'px';
                    requestAnimationFrame(function() {
                        body.style.maxHeight = '0px';
                        body.style.opacity = '0';
                        body.style.paddingBottom = '0';
                    });
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    // Expand
                    body.style.maxHeight = body.scrollHeight + 400 + 'px'; // generous ceiling
                    body.style.opacity = '1';
                    body.style.paddingBottom = '16px';
                    chevron.style.transform = 'rotate(0deg)';
                }
                _siteInfoOpen = !_siteInfoOpen;
            };
        })();
    </script>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="site-details-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#equipment"
                type="button" role="tab" aria-controls="equipment" aria-selected="true">
                <i class="fa fa-desktop me-2"></i> Equipment
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="inspections-tab" data-bs-toggle="tab" data-bs-target="#inspections"
                type="button" role="tab" aria-controls="inspections" aria-selected="false">
                <i class="fa fa-clipboard-check me-2"></i> Inspections
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="work-orders-tab" data-bs-toggle="tab" data-bs-target="#work-orders-main"
                type="button" role="tab" aria-controls="work-orders" aria-selected="false">
                <i class="fa fa-wrench me-2"></i> Work Orders
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="site-details-tabs-content">

        <!-- Equipment Tab -->
        <div class="tab-pane fade show active" id="equipment" role="tabpanel" aria-labelledby="equipment-tab">
            <div class="glass-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Equipment List</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#adminBulkImportModal">
                            <i class="fa-solid fa-file-excel me-1"></i> Import Excel
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                            <i class="fa fa-plus me-2"></i> Add Equipment
                        </button>
                    </div>
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
                                        $rawStatus = trim($eq['status'] ?? '');
                                        switch ($rawStatus) {
                                            case 'ready':
                                                echo '<span class="badge" style="background:#16a34a;color:#fff;padding:5px 10px;border-radius:20px;">Ready</span>';
                                                break;
                                            case 'need_attention':
                                                echo '<span class="badge" style="background:#d97706;color:#fff;padding:5px 10px;border-radius:20px;">Need Attention</span>';
                                                break;
                                            case 'repair':
                                                echo '<span class="badge" style="background:#0ea5e9;color:#fff;padding:5px 10px;border-radius:20px;">Repair</span>';
                                                break;
                                            case 'out_of_service':
                                                echo '<span class="badge" style="background:#dc2626;color:#fff;padding:5px 10px;border-radius:20px;">Out of Service</span>';
                                                break;
                                            default:
                                                echo '<span class="badge" style="background:#6b7280;color:#fff;padding:5px 10px;border-radius:20px;">' . esc($rawStatus ?: 'No Status') . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-action edit-equipment-btn"
                                            data-id="<?= $eq['id'] ?>" data-asset_tag="<?= esc($eq['asset_tag'], 'attr') ?>"
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
            <!--
            The Inspections tab previously contained a DataTables listing of site
            inspections.  This has been replaced by a custom inspection
            workflow component.  The component markup lives in a partial view
            (admin/sites/site_inspection_workflow.php) to keep this file
            maintainable.  Including the partial here injects the entire
            workflow UI into the tab.  CSS scoped to #site-inspection-workflow
            and supporting JavaScript functions are defined elsewhere in this
            file.
        -->
            <?= $this->include('admin/sites/site_inspection_workflow') ?>
        </div>

        <!-- Work Orders Tab -->
        <div class="tab-pane fade" id="work-orders-main" role="tabpanel" aria-labelledby="work-orders-main-tab">
            <div class="glass-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Work Orders List</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkOrderModal">
                        <i class="fa fa-plus me-2"></i> Add Work Order
                    </button>
                </div>
                <table id="work-orders-datatable" class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Equipment</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Technician</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($workOrders)): ?>
                            <?php foreach ($workOrders as $wo): ?>
                                <tr>
                                    <td>WO-<?= esc($wo['id']) ?></td>
                                    <td><?= esc($wo['title']) ?></td>
                                    <td><?= esc($wo['asset_tag'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'status-badge ';
                                        $status = strtolower($wo['status']);
                                        $statusLabel = ucwords(str_replace('_', ' ', $wo['status']));
                                        if ($status === 'completed') {
                                            $statusClass .= 'status-completed';
                                        } elseif ($status === 'in_progress' || $status === 'in progress' || $status === 'in-progress') {
                                            $statusClass .= 'status-in-progress';
                                        } elseif ($status === 'cancelled') {
                                            $statusClass .= 'status-need-attention';
                                        } else {
                                            $statusClass .= 'status-open';
                                        }
                                        ?>
                                        <span class="<?= $statusClass ?>"><?= esc($statusLabel) ?></span>
                                    </td>
                                    <td><?= esc($wo['priority']) ?></td>
                                    <td><?= esc($wo['assigned_to_name'] ?? 'Unassigned') ?></td>
                                    <td><?= $wo['start_date'] ? date('M d, Y', strtotime($wo['start_date'])) : '-' ?></td>
                                    <td><?= $wo['end_date'] ? date('M d, Y', strtotime($wo['end_date'])) : '-' ?></td>
                                    <td>
                                        <div class="action-btns d-flex align-items-center gap-1 flex-wrap">
                                            <button class="btn btn-sm btn-info btn-action edit-workorder-btn"
                                                data-id="<?= $wo['id'] ?>" data-equipment_id="<?= $wo['equipment_id'] ?? '' ?>"
                                                data-site_equipment_id="<?= $wo['equipment_id'] ?? '' ?>"
                                                data-serial_number="<?= $wo['serial_number'] ?? '' ?>"
                                                data-title="<?= esc($wo['title'], 'attr') ?>"
                                                data-description="<?= esc($wo['description'] ?? '', 'attr') ?>"
                                                data-status="<?= esc($wo['status'], 'attr') ?>"
                                                data-priority="<?= esc($wo['priority'], 'attr') ?>"
                                                data-assigned_to="<?= $wo['assigned_to'] ?? '' ?>"
                                                data-start_date="<?= $wo['start_date'] ?? '' ?>"
                                                data-end_date="<?= $wo['end_date'] ?? '' ?>">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>

                                            <!-- Actions dropdown: Download Invoice / Packing Slip / Delete -->
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle btn-action"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v"></i> Actions
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <h6 class="dropdown-header ">
                                                            <i class="fas fa-file-invoice me-1"></i> Documents
                                                        </h6>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item site-wo-invoice-btn" href="#"
                                                            data-wo-id="<?= $wo['id'] ?>"
                                                            data-wo-title="<?= esc($wo['title'], 'attr') ?>">
                                                            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                                                            Download Invoice
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="<?= site_url('admin/work-orders/' . $wo['id'] . '/packing-slip/download') ?>"
                                                            target="_blank">
                                                            <i class="fas fa-box me-2 text-success"></i>
                                                            Download Packing Slip
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item text-danger delete-workorder-btn"
                                                            data-id="<?= $wo['id'] ?>">
                                                            <i class="fa fa-trash me-2"></i> Delete
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
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


    <div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel"
        aria-hidden="true">
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
                                <input type="text" class="form-control" id="equipment-asset-tag" name="asset_tag"
                                    placeholder=" add Asset Tag" required>
                            </div>

                            <!-- Serial Number -->
                            <div class="col-md-6">
                                <label for="equipment-serial-number" class="form-label">Serial Number</label>
                                <input type="text" class="form-control" id="equipment-serial-number"
                                    name="serial_number" placeholder="" required>
                            </div>
                            <!-- Make -->
                            <div class="col-md-6">
                                <label for="equipment-make" class="form-label">Make <span
                                        class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" class="form-control equipment-suggest" id="equipment-make"
                                        name="make" placeholder="Select or type make" autocomplete="off" required
                                        data-list="make-list">
                                    <ul class="suggest-dropdown d-none" id="make-list-dropdown"></ul>
                                </div>
                            </div>

                            <!-- Model -->
                            <div class="col-md-6">
                                <label for="equipment-model" class="form-label">Model <span
                                        class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" class="form-control equipment-suggest" id="equipment-model"
                                        name="model" placeholder="Select or type model" autocomplete="off" required
                                        data-list="model-list">
                                    <ul class="suggest-dropdown d-none" id="model-list-dropdown"></ul>
                                </div>
                            </div>

                            <!-- Device Type -->
                            <div class="col-md-6">
                                <label for="equipment-device-type" class="form-label">Device Type <span
                                        class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" class="form-control equipment-suggest" id="equipment-device-type"
                                        name="device_type" placeholder="Select or type device type" autocomplete="off"
                                        required data-list="device-type-list">
                                    <ul class="suggest-dropdown d-none" id="device-type-list-dropdown"></ul>
                                </div>
                            </div>

                            <!-- Department -->
                            <div class="col-md-6">
                                <label for="equipment-department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="equipment-department" name="department"
                                    placeholder="e.g., Radiology">
                            </div>

                            <!-- Room/Location -->
                            <div class="col-md-6">
                                <label for="equipment-location" class="form-label">Room/Location</label>
                                <input type="text" class="form-control" id="equipment-location" name="location"
                                    placeholder="e.g., Room 101">
                            </div>

                            <!-- Device Status -->
                            <div class="col-md-6">
                                <label for="equipment-status" class="form-label">Device Status</label>
                                <select class="form-select" id="equipment-status" name="status">
                                    <option value="ready">Ready</option>
                                    <option value="need_attention">Need Attention</option>
                                    <option value="repair">Repair</option>
                                    <option value="out_of_service">Out of Service</option>
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
                                <textarea class="form-control" id="equipment-fast-notes" name="fast_notes" rows="2"
                                    placeholder="Short note for fast entry"></textarea>
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
                                <input type="date" class="form-control" id="equipment-installation-date"
                                    name="installation_date">
                            </div>

                            <!-- Warranty Expires -->
                            <div class="col-md-6">
                                <label for="equipment-warranty-expires" class="form-label">Warranty Expires</label>
                                <input type="date" class="form-control" id="equipment-warranty-expires"
                                    name="warranty_expires">
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
                        <input type="text" class="form-control" id="wiz-serial-number"
                            placeholder="Enter or scan serial number" autocomplete="off">
                        <p class="helper-text">
                            Enter the serial number from the equipment label. If found, details will be automatically
                            filled.
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
                        <input type="text" class="form-control mb-3" id="wiz-search-model"
                            placeholder="Search for model..." autocomplete="off">

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
                                <input type="date" class="form-control" id="wiz-s3-inspdate" name="scheduled_at"
                                    value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <!-- Row 5: Service Notes (full width) -->
                        <div class="row g-3 mt-1">
                            <div class="col-12">
                                <label for="wiz-s3-notes" class="form-label">Service Notes</label>
                                <textarea class="form-control" id="wiz-s3-notes" name="notes" rows="3"
                                    placeholder=""></textarea>
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
                        <button type="button" class="btn-next-device" id="wizBtnNextDevice">Add to Queue & Next
                            Device</button>

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
    <div class="modal fade" id="viewInspectionModal" tabindex="-1" aria-labelledby="viewInspectionModalLabel"
        aria-hidden="true">
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
    <div class="modal fade" id="editInspectionWizardModal" tabindex="-1" aria-labelledby="editInspectionWizardLabel"
        aria-hidden="true">
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
                                <label for="edit-wiz-s1-site" class="form-label">Customer Site <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="edit-wiz-s1-site" name="site_id">
                                    <option value="<?= $site['id'] ?>" selected><?= esc($site['name']) ?></option>
                                </select>
                            </div>
                        </div>

                        <!-- Search by Serial Number -->
                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <label for="edit-wiz-s1-serial" class="form-label">Search Equipment by Serial # <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-wiz-s1-serial"
                                    placeholder="Enter serial number" autocomplete="off">
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
                                <input type="text" class="form-control" id="edit-wiz-s2-manufacturer"
                                    name="manufacturer">
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
                data: {
                    group_id: groupId
                },
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
                            <td><span class="status-badge status-${inspection.inspection_status.toLowerCase()}">${inspection.inspection_status || 'N/A'}</span></td>
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

            // ── Restore active main tab after page reload (e.g. after Add Inspection / Pass/Fail) ──
            var savedTab = sessionStorage.getItem('siteDetailsActiveTab');
            if (savedTab) {
                sessionStorage.removeItem('siteDetailsActiveTab');
                var tabEl = document.getElementById(savedTab);
                if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();
            }
            var savedSubTab = sessionStorage.getItem('siteDetailsActiveSubTab');
            if (savedSubTab) {
                sessionStorage.removeItem('siteDetailsActiveSubTab');
                // Sub-tab lives inside site_inspection_workflow — delay slightly for it to render
                setTimeout(function() {
                    var subTabEl = document.getElementById(savedSubTab);
                    if (subTabEl) bootstrap.Tab.getOrCreateInstance(subTabEl).show();
                }, 300);
            }

            // ── Inspections tab click: always refresh all data & re-filter ──────
            // When the user clicks the main "Inspections" tab, fetch fresh HTML from
            // the server and rebuild the table bodies, tab badges, and device counter.
            // If an inspection group is already open, re-apply the group filter after
            // the refresh so counts and rows stay correct.
            var inspTabBtn = document.getElementById('inspections-tab');
            if (inspTabBtn) {
                inspTabBtn.addEventListener('shown.bs.tab', function() {
                    if (typeof backgroundRefreshTabs === 'function') {
                        backgroundRefreshTabs(function() {
                            // If currently viewing an inspection (not the dashboard list),
                            // re-apply the group filter to restore filtered state.
                            if (window.CURRENT_INSPECTION_GROUP_ID &&
                                typeof filterInspectedByGroup === 'function') {
                                filterInspectedByGroup(window.CURRENT_INSPECTION_GROUP_ID);
                            }
                            // If reports tab is active, silently refresh tab content (no modal)
                            var reportsTabActive = document.querySelector(
                                '#inspection-reports.active, #inspection-reports.show');
                            if (reportsTabActive && window.CURRENT_REPORT_GROUP_ID &&
                                typeof loadReportIntoContainer === 'function') {
                                var rtEl = document.getElementById('reportsTabContent');
                                if (rtEl && !rtEl.querySelector('iframe')) {
                                    loadReportIntoContainer(window.CURRENT_REPORT_GROUP_ID, rtEl);
                                }
                            }
                        });
                    }
                });
            }

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
                            $('#edit-wiz-s3-inspdate').val(data.scheduled_at ? data.scheduled_at.split(
                                ' ')[0] : '');
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
            // ── Load equipment dropdowns from DB ─────────────────────────
            // Store lists globally
            var equipmentLists = {
                make: [],
                model: [],
                'device-type': []
            };

            function loadEquipmentDropdownOptions(selectedMake, selectedModel, selectedDeviceType) {
                $.ajax({
                    url: '<?= site_url('admin/equipment/dropdown-options') ?>',
                    method: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (!res.success) return;
                        equipmentLists.make = res.makes;
                        equipmentLists.model = res.models;
                        equipmentLists['device-type'] = res.device_types;
                        // Only set values when explicitly passed (Add mode).
                        // In Edit mode the values are already set directly before
                        // this AJAX call, so we must NOT overwrite them here.
                        if (selectedMake)       $('#equipment-make').val(selectedMake);
                        if (selectedModel)      $('#equipment-model').val(selectedModel);
                        if (selectedDeviceType) $('#equipment-device-type').val(selectedDeviceType);
                    }
                });
            }
            // Expose to window so workflow partial can access it
            window.loadEquipmentDropdownOptions = loadEquipmentDropdownOptions;

            // ── Suggest dropdown logic ────────────────────────────────────
            function showSuggestions($input, items) {
                var listId = $input.attr('id').replace('equipment-', '') + '-list-dropdown';
                // handle device-type specially
                if ($input.attr('id') === 'equipment-device-type') {
                    listId = 'device-type-list-dropdown';
                } else if ($input.attr('id') === 'equipment-make') {
                    listId = 'make-list-dropdown';
                } else if ($input.attr('id') === 'equipment-model') {
                    listId = 'model-list-dropdown';
                }

                var $dropdown = $('#' + listId);
                var val = $input.val().toLowerCase();
                var filtered = val.length === 0 ?
                    items :
                    items.filter(function(i) {
                        return i.toLowerCase().indexOf(val) !== -1;
                    });

                $dropdown.empty();

                if (filtered.length === 0) {
                    $dropdown.addClass('d-none');
                    return;
                }

                $.each(filtered, function(i, item) {
                    $dropdown.append(
                        $('<li>').text(item).on('mousedown', function(e) {
                            e.preventDefault();
                            $input.val(item);
                            $dropdown.addClass('d-none');
                        })
                    );
                });

                $dropdown.removeClass('d-none');
            }

            // Show on focus
            $(document).on('focus', '#equipment-make', function() {
                showSuggestions($(this), equipmentLists.make);
            });
            $(document).on('focus', '#equipment-model', function() {
                showSuggestions($(this), equipmentLists.model);
            });
            $(document).on('focus', '#equipment-device-type', function() {
                showSuggestions($(this), equipmentLists['device-type']);
            });

            // Filter on keyup
            $(document).on('keyup', '#equipment-make', function() {
                showSuggestions($(this), equipmentLists.make);
            });
            $(document).on('keyup', '#equipment-model', function() {
                showSuggestions($(this), equipmentLists.model);
            });
            $(document).on('keyup', '#equipment-device-type', function() {
                showSuggestions($(this), equipmentLists['device-type']);
            });

            // Hide on blur
            $(document).on('blur', '#equipment-make, #equipment-model, #equipment-device-type', function() {
                var id = $(this).attr('id');
                setTimeout(function() {
                    if (id === 'equipment-make') $('#make-list-dropdown').addClass('d-none');
                    if (id === 'equipment-model') $('#model-list-dropdown').addClass('d-none');
                    if (id === 'equipment-device-type') $('#device-type-list-dropdown').addClass(
                        'd-none');
                }, 200);
            });

            // Hide all dropdowns on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#addEquipmentModal').length) {
                    $('.suggest-dropdown').addClass('d-none');
                }
            });

            // Load dropdown options when modal opens in Add mode only.
            // In Edit mode the equipment-id hidden field is already set, so we
            // skip the empty call that would race against and blank out the
            // values that the edit AJAX just populated.
            $('#addEquipmentModal').on('show.bs.modal', function() {
                if (!$('#equipment-id').val()) {
                    loadEquipmentDropdownOptions('', '', '');
                }
            });
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Read navigation intent from sessionStorage (set by dashboard search) ──
    // Falls back to URL params for any legacy bookmarks / direct links.
    var intentJson = sessionStorage.getItem('siteNavIntent');
    var intent = null;
    if (intentJson) {
        sessionStorage.removeItem('siteNavIntent'); // consume immediately
        try { intent = JSON.parse(intentJson); } catch(e) {}
    }

    // Legacy URL-param fallback
    if (!intent) {
        var params  = new URLSearchParams(window.location.search);
        var openTab = (params.get('open_tab') || '').trim();
        if (openTab) {
            intent = {
                open_tab:         openTab,
                group_id:         params.get('group_id')         || '',
                inspection_title: params.get('inspection_title') || 'Inspection',
                asset_tag:        params.get('asset_tag')        || '',
                equipment_id:     params.get('equipment_id')     || '',
            };
        }
    }

    if (!intent) return;

    var openTab = (intent.open_tab || '').trim();

    var equipmentTabBtn   = document.getElementById('equipment-tab');
    var inspectionsTabBtn = document.getElementById('inspections-tab');
    var workOrdersTabBtn  = document.getElementById('workorders-tab');

    // 1) Open correct main tab
    if (openTab === 'equipment' && equipmentTabBtn) {
        bootstrap.Tab.getOrCreateInstance(equipmentTabBtn).show();
    } else if (openTab === 'inspections' && inspectionsTabBtn) {
        bootstrap.Tab.getOrCreateInstance(inspectionsTabBtn).show();
    } else if (openTab === 'workorders' && workOrdersTabBtn) {
        bootstrap.Tab.getOrCreateInstance(workOrdersTabBtn).show();
    }

    // 2) If this came from inspection search, open that exact inspection group too
    if (openTab === 'inspections') {
        var groupId   = intent.group_id         || '';
        var inspTitle = intent.inspection_title || 'Inspection';
        var siteName  = <?= json_encode($site['name'] ?? '') ?>;

        if (groupId) {
            setTimeout(function () {
                if (typeof window.viewInspection === 'function') {
                    // Call with placeholder title first so the view opens immediately.
                    // We then fetch the real title + technician from the DB and patch
                    // the header so it always matches what's stored — regardless of
                    // what the search result's inspection_title field contained.
                    window.viewInspection(groupId, siteName, inspTitle || groupId, groupId, '—', 'In Progress');

                    // Fetch real group metadata and overwrite the header elements
                    var REPORT_DATA_URL = '<?= site_url('admin/inspections/reportData') ?>';
                    fetch(REPORT_DATA_URL + '?group_id=' + encodeURIComponent(groupId), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (!data.success || !data.latest) return;
                        var latest = data.latest;

                        // Real title from DB (title column, fallback to inspection_type)
                        var realTitle = (latest.title || latest.inspection_type || groupId).trim();
                        var titleEl = document.getElementById('insp-title');
                        if (titleEl) titleEl.textContent = realTitle;
                        window._savedInspTitle = realTitle;

                        // Real inspection # (always the group_id)
                        var idLabel = document.getElementById('insp-id-label');
                        if (idLabel) idLabel.textContent = groupId;

                        // Real technician name
                        var techName = latest.technician_name || '—';
                        var techEl = document.getElementById('insp-technician');
                        if (techEl) techEl.textContent = techName;

                        var realStatus =
                            latest.status ||
                            latest.group_status ||
                            latest.inspection_status ||
                            'In Progress';

                        var detailWrap = document.getElementById('view-inspection');
                        if (detailWrap) {
                            detailWrap.setAttribute('data-current-status', realStatus);
                        }

                        if (typeof applyInspectionStatusUI === 'function') {
                            applyInspectionStatusUI(realStatus);
                        }


                    })
                    .catch(function() { /* non-fatal: header shows groupId as fallback */ });
                }
            }, 500);
        }
    }
});
</script>
    <!-- Custom dynamic behaviour for inspection workflow and work orders -->
    <script>
        // Immediately invoked function to avoid global scope pollution
        (function() {
            // Constants for API endpoints and settings
            const SITE_ID = <?= (int) ($site['id'] ?? 0) ?>;
            // Expose key constants to window so site_inspection_workflow.php partial
            // and inline onclick handlers (onWOAssetChange, workOrderModal submit)
            // can access them — they live outside this IIFE's scope.
            window.SITE_ID               = SITE_ID;
            window.GET_EQUIPMENT_URL      = "<?= site_url('admin/site-inspection/get-equipment') ?>";
            window.WORK_ORDER_CREATE_URL  = "<?= site_url('admin/work-orders/create') ?>";
            window.WORK_ORDER_UPDATE_URL  = "<?= site_url('admin/work-orders/update') ?>";
            window.WORK_ORDER_DELETE_URL  = "<?= site_url('admin/work-orders/delete') ?>";
            // CSRF helper — reads fresh from cookie (CI4 regenerate=true rotates token)
            function getFreshCsrf() {
                var m = document.cookie.match(/csrf_cookie_name=([^;]+)/);
                return m ? decodeURIComponent(m[1]) : '';
            }
            $.ajaxSetup({
                beforeSend: function(xhr, s) {
                    if (s.type && s.type.toUpperCase() === 'POST') {
                        var c = getFreshCsrf();
                        if (c && typeof s.data === 'string') s.data += (s.data ? '&' : '') +
                            encodeURIComponent('csrf_test_name') + '=' + encodeURIComponent(c);
                    }
                }
            });
            const REPORT_DATA_URL = "<?= site_url('admin/inspections/reportData') ?>";
            const REPORT_PDF_URL = "<?= site_url('admin/inspections/reportPdf') ?>";
            const GET_EQUIPMENT_URL = "<?= site_url('admin/site-inspection/get-equipment') ?>";
            const EQUIPMENT_CREATE_URL = "<?= site_url('admin/site-inspection/add-device') ?>";
            const WORK_ORDER_CREATE_URL = "<?= site_url('admin/work-orders/create') ?>";
            const WORK_ORDER_UPDATE_URL = "<?= site_url('admin/work-orders/update') ?>";
            const WORK_ORDER_DELETE_URL = "<?= site_url('admin/work-orders/delete') ?>";
            const LOGO_BASE_URL = "<?= base_url('uploads/logos') ?>";

            // Track the currently selected inspection group for report operations
            let CURRENT_REPORT_GROUP_ID = null;

           

            /**
             * Fetch inspection report data for a group and render it in the
             * report modal and reports tab.
             *
             * @param {string} groupId
             */
            window.openInspectionReport = function openInspectionReport(groupId) {
                CURRENT_REPORT_GROUP_ID = groupId;
                window.CURRENT_REPORT_GROUP_ID = groupId;

                // Open the report modal — same as technician portal
                var reportContent = document.getElementById('reportContent');
                var modalEl = document.getElementById('inspectionReportModal');

                if (reportContent && modalEl) {
                    // Show spinner, open modal, load iframe
                    reportContent.innerHTML =
                        '<div class="text-center py-5">' +
                        '<i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>' +
                        '<p class="mt-3 text-muted">Loading report...</p></div>';
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();

                    fetch('<?= site_url("admin/inspections/reportPdf") ?>/' + encodeURIComponent(groupId), {
                        headers: { 'Accept': 'text/html' }
                    })
                    .then(function(r) { return r.text(); })
                    .then(function(html) {
                        reportContent.innerHTML = '';
                        var iframe = document.createElement('iframe');
                        iframe.style.cssText = 'width:100%;height:72vh;border:none;background:#fff;border-radius:0 0 14px 14px;';
                        reportContent.appendChild(iframe);
                        iframe.contentDocument.open();
                        iframe.contentDocument.write(html);
                        iframe.contentDocument.close();
                    })
                    .catch(function() {
                        reportContent.innerHTML =
                            '<div class="alert alert-danger m-3">Failed to load report.</div>';
                    });

                    // Wire download button
                    var dlBtn = document.getElementById('adminReportDownloadBtn');
                    if (dlBtn) {
                        dlBtn.onclick = function() {
                            window.open('<?= site_url("admin/inspections/reportPdf") ?>/' + encodeURIComponent(groupId), '_blank');
                        };
                    }
                } else {
                    // Fallback: if on dashboard view, switch to inspection + reports tab
                    const dashView = document.getElementById('view-dashboard');
                    const inspView = document.getElementById('view-inspection');
                    if (dashView && !dashView.classList.contains('d-none-view')) {
                        dashView.classList.add('d-none-view');
                        if (inspView) inspView.classList.remove('d-none-view');
                        var reportsTabBtn = document.getElementById('reports-tab');
                        if (reportsTabBtn) bootstrap.Tab.getOrCreateInstance(reportsTabBtn).show();
                    }
                }
            };

            /**
             * loadReportIntoContainer — fetches the clean reportPreview HTML
             * and renders it in an iframe inside the given container.
             * Same approach as the technician portal.
             */
            function loadReportIntoContainer(groupId, container) {
                if (!groupId || !container) return;
                container.innerHTML =
                    '<div class="text-center py-5">' +
                    '<i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>' +
                    '<p class="mt-3 text-muted">Loading report…</p>' +
                    '</div>';
                var previewUrl = '<?= site_url("admin/inspections/reportPdf") ?>/' + encodeURIComponent(groupId) + '?inline=1';
                fetch(previewUrl, {
                    headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(r) {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.text();
                })
                .then(function(html) {
                    container.innerHTML = '';
                    var iframe = document.createElement('iframe');
                    iframe.style.cssText = 'width:100%;height:72vh;border:none;background:#0E1630;border-radius:0 0 10px 10px;';
                    iframe.setAttribute('scrolling', 'yes');
                    var wrapper = document.createElement('div');
                    wrapper.style.cssText = 'background:#0E1630;border:1px solid rgba(255,255,255,.12);border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.35);';
                    wrapper.appendChild(iframe);
                    container.appendChild(wrapper);
                    iframe.contentDocument.open();
                    iframe.contentDocument.write(html);
                    iframe.contentDocument.close();
                })
                .catch(function(err) {
                    container.innerHTML =
                        '<div class="alert alert-danger m-3">' +
                        '<i class="fa-solid fa-triangle-exclamation me-2"></i>' +
                        'Failed to load report: ' + escapeHtml(String(err)) + '</div>';
                });
            }

            // Shim — legacy callers pass (latest, rows, groupId); we only need groupId
            function generateInspectionReportHTML(latest, rows, groupId) {
                if (groupId) {
                    var tabContainer = document.getElementById('reportsTabContent');
                    if (tabContainer) loadReportIntoContainer(groupId, tabContainer);
                }
                return '';
            }


            function buildReportHeaderForPrint() {
                var getText = function(id) {
                    var el = document.getElementById(id);
                    return el ? escapeHtml(el.textContent.trim()) : '';
                };
                var siteName = getText('insp-site-label') || 'Site';
                var inspTitle = getText('insp-title') || '';
                var inspId = getText('insp-id-label') || (CURRENT_REPORT_GROUP_ID ? escapeHtml(
                    CURRENT_REPORT_GROUP_ID) : '');
                var tech = getText('insp-technician') || '';
                // Build logo HTML using the latest report data
                var logoHtml = '';
                // Try to get logo from the rendered report content
                var logoImg = document.querySelector(
                    '#reportsTabContent img[src*="logos"], #reportContent img[src*="logos"]');
                if (logoImg) {
                    logoHtml = '<img src="' + escapeHtml(logoImg.src) +
                        '" style="height:60px;max-width:200px;" alt="Logo">';
                }
                return '<table style="width:100%;border-collapse:collapse;margin-bottom:16px;border-bottom:2px solid #e2e8f0;">' +
                    '<tr>' +
                    '<td style="vertical-align:top;padding:8px 0;">' +
                    '<strong>' + siteName + '</strong><br>' +
                    '<em style="color:#555;">' + inspTitle + '</em>' +
                    '</td>' +
                    '<td style="text-align:center;vertical-align:middle;padding:8px;">' + logoHtml + '</td>' +
                    '<td style="text-align:right;vertical-align:top;padding:8px 0;">' +
                    '<strong>Inspection #:</strong> ' + inspId + '<br>' +
                    '<em style="color:#555;">Technician: ' + tech + '</em>' +
                    '</td>' +
                    '</tr></table>';
            }

            window.previewReportPDF = function previewReportPDF() {
                var groupId = CURRENT_REPORT_GROUP_ID || window.CURRENT_REPORT_GROUP_ID || window.CURRENT_INSPECTION_GROUP_ID;
                if (!groupId) {
                    alert('Please click the report icon on an inspection row first.');
                    return;
                }
                openInspectionReport(groupId);
            };

            /**
             * Trigger download of the PDF for the current report group.
             */
            window.exportReportPDF = function exportReportPDF() {
                var groupId = CURRENT_REPORT_GROUP_ID || window.CURRENT_REPORT_GROUP_ID || window.CURRENT_INSPECTION_GROUP_ID;
                if (!groupId) {
                    alert('Please click the report icon on an inspection row first.');
                    return;
                }
                window.open('<?= site_url("admin/inspections/reportPdf") ?>/' + encodeURIComponent(groupId), '_blank');
            };

            // Helper: convert result to a badge
            function resultBadge(res) {
                const r = (res || '').trim();
                if (r === 'Pass') return '<span class="text-success"><i class="fa-solid fa-check"></i> Pass</span>';
                if (r === 'Fail')
                    return '<span class="text-danger fw-bold"><i class="fa-solid fa-xmark"></i> Fail</span>';
                if (r === 'Repair')
                    return '<span class="text-warning fw-bold"><i class="fa-solid fa-wrench"></i> Repair</span>';
                return '<span class="text-muted">-</span>';
            }
            // Helper: return Yes/No icon markup
            function yesNo(val) {
                const v = String(val || '').toLowerCase();
                return (v === '1' || v === 'yes' || v === 'true') ?
                    '<span class="text-success"><i class="fa-solid fa-check"></i> Yes</span>' :
                    'No';
            }
            // Helper: format datetime into date and time for overview table
            function formatInspectionDateHTML(dt) {
                const d = new Date((dt || '').replace(' ', 'T'));
                if (isNaN(d.getTime())) return escapeHtml(dt || '');
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                let hh = d.getHours();
                const ampm = hh >= 12 ? 'PM' : 'AM';
                hh = hh % 12;
                hh = hh ? hh : 12;
                const mi = String(d.getMinutes()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}<br>${String(hh).padStart(2, '0')}:${mi} ${ampm}`;
            }
            // Helper: escape HTML entities
            function escapeHtml(str) {
                return String(str || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // ---------------------------------------------------------------------
            // Work Order modal and table logic
            // ---------------------------------------------------------------------

            /**
             * Reset the work order modal fields to their default values.
             */
            function resetWorkOrderModal() {
                const modalTitle = document.getElementById('workOrderModalTitle');
                if (modalTitle) modalTitle.textContent = 'Add Work Order';
                document.getElementById('woTitle').value = '';
                document.getElementById('woAsset').value = '';
                document.getElementById('woMake').value = '';
                document.getElementById('woModel').value = '';
                document.getElementById('woSerial').value = '';
                document.getElementById('woEquipment').value = '';
                document.getElementById('woPriority').value = 'normal';
                document.getElementById('woStatus').value = 'open';
                document.getElementById('woTech').value = '';
                document.getElementById('woStartDate').value = '';
                document.getElementById('woEndDate').value = '';
                document.getElementById('woDescription').value = '';
                const errBox = document.getElementById('workOrderError');
                if (errBox) {
                    errBox.classList.add('d-none');
                    errBox.textContent = '';
                }
                // Hidden ids
                document.getElementById('woId').value = '';
                document.getElementById('woEquipmentId').value = '';
            }

            /**
             * Load equipment details given an asset tag and fill the modal fields.
             *
             * @param {string} assetTag
             */
            function loadEquipmentDetails(assetTag) {
                if (!assetTag) {
                    document.getElementById('woMake').value = '';
                    document.getElementById('woModel').value = '';
                    document.getElementById('woSerial').value = '';
                    document.getElementById('woEquipment').value = '';
                    document.getElementById('woEquipmentId').value = '';
                    return;
                }
                fetch(GET_EQUIPMENT_URL + '?asset_tag=' + encodeURIComponent(assetTag) + '&site_id=' +
                        encodeURIComponent(SITE_ID), {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                    .then(resp => resp.json())
                    .then(res => {
                        if (!res.found) {
                            document.getElementById('woMake').value = '';
                            document.getElementById('woModel').value = '';
                            document.getElementById('woSerial').value = '';
                            document.getElementById('woEquipment').value = '';
                            document.getElementById('woEquipmentId').value = '';
                            return;
                        }
                        document.getElementById('woMake').value = res.make || '';
                        document.getElementById('woModel').value = res.model || '';
                        document.getElementById('woSerial').value = res.serial_number || '';
                        document.getElementById('woEquipment').value = res.device_type || '';
                        document.getElementById('woEquipmentId').value = res.id;
                    })
                    .catch(() => {
                        // On failure, clear fields
                        document.getElementById('woMake').value = '';
                        document.getElementById('woModel').value = '';
                        document.getElementById('woSerial').value = '';
                        document.getElementById('woEquipment').value = '';
                        document.getElementById('woEquipmentId').value = '';
                    });
            }
            // Expose to window so workflow partial and onclick handlers can call it
            window.loadEquipmentDetails = loadEquipmentDetails;

            /**
             * Open the work order modal for the given asset.  Pre-fills the
             * asset field and loads equipment details.
             *
             * @param {string} assetTag
             */
            window.openWorkOrderModalFromInventory = function openWorkOrderModalFromInventory(assetTag) {
                resetWorkOrderModal();
                document.getElementById('woAsset').value = assetTag || '';
                loadEquipmentDetails(assetTag || '');
                // Use getOrCreateInstance to avoid Bootstrap errors when the modal
                // was previously shown and not fully disposed.
                bootstrap.Modal.getOrCreateInstance(document.getElementById('workOrderModal')).show();
            };

            /**
             * Open the work order modal without pre-filling.  Used for the Add
             * Work Order button in the tab.
             */
            window.openWorkOrderModal = function openWorkOrderModal() {
                resetWorkOrderModal();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('workOrderModal')).show();
            };

            /**
             * Called when the asset number field changes in the modal.  Looks up
             * details for the entered asset and fills related fields.
             */
            window.onWOAssetChange = function onWOAssetChange() {
                const assetTag = document.getElementById('woAsset').value;
                loadEquipmentDetails(assetTag);
            };

            /**
             * Append a new row to the Work Orders table.  Accepts an object
             * describing the work order fields.
             *
             * @param {object} row
             */
            function appendWorkOrderRow(row) {
                const tbody = document.getElementById('workOrdersTableBody');
                if (!tbody) return;
                const tr = document.createElement('tr');
                tr.setAttribute('data-row-id', row.id);
                tr.setAttribute('data-group-id', row.group_id || (window.CURRENT_INSPECTION_GROUP_ID || ''));
                tr.innerHTML = `
            <td>
                <button class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-icon text-danger btn-delete" title="Delete"><i class="fa-solid fa-trash"></i></button>
            </td>
            <td>${row.id}</td>
            <td>${escapeHtml(row.title || '')}</td>
            <td>${row.asset_tag ? ('<span class="badge bg-light text-dark border">' + escapeHtml(row.asset_tag) + '</span>' + (row.serial_number ? '<br><span class="small text-muted">S/N: ' + escapeHtml(row.serial_number) + '</span>' : '')) : ''}</td>
            <td>${escapeHtml(row.priority || '')}</td>
            <td>${escapeHtml(row.status || '')}</td>
            <td>${escapeHtml(row.assigned_to_name || 'N/A')}</td>
            <td>${row.start_date ? escapeHtml(row.start_date) : '<span class="text-muted">-</span>'}</td>
            <td>${row.end_date ? escapeHtml(row.end_date) : '<span class="text-muted">-</span>'}</td>
            <td>${escapeHtml(row.description || '')}</td>
        `;
                tbody.appendChild(tr);
            }

            /**
             * Update an existing row in the Work Orders table with new values.
             *
             * @param {object} row
             */
            function updateWorkOrderRow(row) {
                const tr = document.querySelector('#workOrdersTableBody tr[data-row-id="' + row.id + '"]');
                if (!tr) return;
                // Column order: 0 Actions, 1 ID, 2 Title, 3 Equipment, 4 Priority, 5 Status, 6 Tech, 7 Start, 8 End, 9 Description
                tr.children[1].textContent = row.id;
                tr.children[2].textContent = row.title || '';
                let equipHtml = '';
                if (row.asset_tag) {
                    equipHtml += '<span class="badge bg-light text-dark border">' + escapeHtml(row.asset_tag) +
                        '</span>';
                    if (row.serial_number) {
                        equipHtml += '<br><span class="small text-muted">S/N: ' + escapeHtml(row.serial_number) +
                            '</span>';
                    }
                }
                tr.children[3].innerHTML = equipHtml;
                tr.children[4].textContent = row.priority || '';
                tr.children[5].textContent = row.status || '';
                tr.children[6].textContent = row.assigned_to_name || 'N/A';
                tr.children[7].innerHTML = row.start_date ? escapeHtml(row.start_date) :
                    '<span class="text-muted">-</span>';
                tr.children[8].innerHTML = row.end_date ? escapeHtml(row.end_date) :
                    '<span class="text-muted">-</span>';
                tr.children[9].textContent = row.description || '';
            }

            /**
             * Event delegation for edit and delete buttons on the Work Orders table.
             */
            const woTableBody = document.getElementById('workOrdersTableBody');
            if (woTableBody) {
                woTableBody.addEventListener('click', function(e) {
                    const editBtn = e.target.closest('.btn-edit');
                    if (editBtn) {
                        e.preventDefault();
                        const tr = editBtn.closest('tr');
                        if (!tr) return;
                        const id = tr.getAttribute('data-row-id');
                        editWorkOrder(id, tr);
                        return;
                    }
                    const delBtn = e.target.closest('.btn-delete');
                    if (delBtn) {
                        e.preventDefault();
                        const tr = delBtn.closest('tr');
                        if (!tr) return;
                        const id = tr.getAttribute('data-row-id');
                        deleteWorkOrder(id, tr);
                    }
                });
            }

            /**
             * Edit a work order: populate the modal with values from the row and show it.
             *
             * @param {string} id
             * @param {HTMLElement} tr
             */
            function editWorkOrder(id, tr) {
                resetWorkOrderModal();
                const modalTitle = document.getElementById('workOrderModalTitle');
                if (modalTitle) modalTitle.textContent = 'Edit Work Order';
                document.getElementById('woId').value = id;
                document.getElementById('woTitle').value = tr.children[2].textContent.trim();
                // Extract asset tag and serial from cell 3
                const equipCell = tr.children[3];
                const badge = equipCell.querySelector('.badge');
                const sn = equipCell.querySelector('.small.text-muted');
                document.getElementById('woAsset').value = badge ? badge.textContent.trim() : '';
                document.getElementById('woSerial').value = sn ? sn.textContent.replace('S/N:', '').trim() : '';
                // Load equipment details to fill make/model/equipment_id
                loadEquipmentDetails(document.getElementById('woAsset').value);
                document.getElementById('woPriority').value = tr.children[4].textContent.trim() || 'Medium';
                document.getElementById('woStatus').value = tr.children[5].textContent.trim() || 'Open';
                const techVal = tr.children[6].textContent.trim();
                document.getElementById('woTech').value = techVal === 'N/A' ? '' : techVal;
                const startVal = tr.children[7].textContent.trim();
                document.getElementById('woStartDate').value = (startVal === '-' || startVal === '') ? '' : startVal;
                const endVal = tr.children[8].textContent.trim();
                document.getElementById('woEndDate').value = (endVal === '-' || endVal === '') ? '' : endVal;
                document.getElementById('woDescription').value = tr.children[9].textContent.trim();
                // Show modal
                bootstrap.Modal.getOrCreateInstance(document.getElementById('workOrderModal')).show();
            }

            /**
             * Delete a work order by id via AJAX and remove its row from the table.
             *
             * @param {string} id
             * @param {HTMLElement} tr
             */
            function deleteWorkOrder(id, tr) {
                if (!confirm('Are you sure you want to delete this work order?')) return;
                fetch(WORK_ORDER_DELETE_URL + '/' + encodeURIComponent(id), {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(resp => resp.json().catch(() => ({
                        success: resp.ok
                    })))
                    .then(res => {
                        if (res && res.success) {
                            tr.remove();
                        }
                    })
                    .catch(() => {
                        // On error, do nothing
                    });
            }

            // ── Add Device Form (from "asset not found" flow) ────────────────────────
            // Handles saving a new device directly into the inspection flow.
            const addDeviceModalElRef = document.getElementById('addDeviceModal');
            if (addDeviceModalElRef) {
                addDeviceModalElRef.addEventListener('shown.bs.modal', function() {
                    const deptEl = document.getElementById('addDept');
                    const roomEl = document.getElementById('addRoom');
                    if (!deptEl || !roomEl) return;
                    fetch('<?= site_url('admin/site-inspection/last-device') ?>?site_id=' + encodeURIComponent(siteId) + '&group_id=' + encodeURIComponent(window.CURRENT_INSPECTION_GROUP_ID || window.CURRENT_REPORT_GROUP_ID || ''), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    }).then(r => r.json()).then(data => {
                        if (data.department && !deptEl.value) deptEl.value = data.department;
                        if (data.location && !roomEl.value) roomEl.value = data.location;
                    }).catch(() => {});
                });
            }
            const addDeviceFormEl = document.getElementById('addDeviceForm');
            if (addDeviceFormEl) {
                addDeviceFormEl.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const errBox = document.getElementById('addDeviceError');
                    if (errBox) errBox.classList.add('d-none');

                    const assetTag  = (document.getElementById('addAsset')?.value || '').trim();
                    const serialVal = (document.getElementById('addSerial')?.value || '').trim();

                    const modelVal = (document.getElementById('addModel')?.value || '').trim();
                    const makeVal  = (document.getElementById('addManufacturer')?.value || '').trim();
                    const typeVal  = (document.getElementById('addType')?.value || '').trim();

                    // Require Asset #, Manufacturer, Model and Type before saving
                    if (!assetTag || !makeVal || !modelVal || !typeVal) {
                        const missing = [];
                        if (!assetTag)  missing.push('Asset #');
                        if (!makeVal)   missing.push('Manufacturer');
                        if (!modelVal)  missing.push('Model Number');
                        if (!typeVal)   missing.push('Type');
                        if (errBox) {
                            errBox.textContent = 'Required fields missing: ' + missing.join(', ') + '.';
                            errBox.classList.remove('d-none');
                        }
                        return;
                    }

                    // ── Client-side duplicate check: asset tag ────────────────
                    const existingAssetRow = document.querySelector(
                        '#notInspectedTableBody tr[data-asset="' + assetTag.replace(/"/g,'') + '"]'
                    );
                    if (existingAssetRow) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate Asset #',
                            text: 'Asset # "' + assetTag + '" already exists in this site\'s inventory.',
                            confirmButtonColor: '#7c3aed'
                        });
                        return;
                    }

                    const params = new URLSearchParams();
                    // Include CSRF token
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    if (csrfMeta) params.append('<?= csrf_token() ?>', csrfMeta.getAttribute('content'));
                    params.append('site_id', SITE_ID);
                    params.append('asset_tag', assetTag);
                    params.append('model', document.getElementById('addModel')?.value || '');
                    params.append('make', document.getElementById('addManufacturer')?.value || '');
                    params.append('serial_number', serialVal);
                    params.append('device_type', document.getElementById('addType')?.value || '');
                    params.append('department', document.getElementById('addDept')?.value || '');
                    params.append('location', document.getElementById('addRoom')?.value || '');
                    params.append('est', document.getElementById('addEST')?.value || 'No');
                    params.append('cal', document.getElementById('addCAL')?.value || 'No');
                    params.append('status', 'ready');

                    const saveBtn = addDeviceFormEl.querySelector('[type="submit"]');
                    if (saveBtn) {
                        saveBtn.disabled = true;
                        saveBtn.textContent = 'Saving…';
                    }

                    fetch(EQUIPMENT_CREATE_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: params.toString()
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (!res || !res.success) {
                                const msg = (res && res.message) || 'Failed to save device.';
                                // Show SweetAlert for duplicate errors, inline error for others
                                if (msg.includes('already exists') || msg.includes('Duplicate')) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Duplicate Found',
                                        text: msg,
                                        confirmButtonColor: '#7c3aed'
                                    });
                                } else {
                                    if (errBox) {
                                        errBox.textContent = msg;
                                        errBox.classList.remove('d-none');
                                    }
                                }
                                return;
                            }
                            // ── Close the modal and continue inspection directly ─────────
                           
                            const modalEl = document.getElementById('addDeviceModal');
                            const deviceData = {
                                asset_tag: res.asset_tag || assetTag,
                                make: res.make || (document.getElementById('addManufacturer')?.value || '').trim(),
                                model: res.model || (document.getElementById('addModel')?.value || '').trim(),
                                device_type: res.device_type || (document.getElementById('addType')?.value || '').trim(),
                                serial_number: res.serial_number || (document.getElementById('addSerial')?.value || '').trim(),
                                department: res.department || (document.getElementById('addDept')?.value || '').trim(),
                                location: res.location || (document.getElementById('addRoom')?.value || '').trim(),
                                equipment_id: res.equipment_id || null,
                                source: res.reused ? 'equipment' : 'inspection'
                            };

                            if (modalEl) {
                                modalEl.addEventListener('hidden.bs.modal', function onHidden() {
                                    modalEl.removeEventListener('hidden.bs.modal', onHidden);

                                    if (typeof window.startInspectionWithDeviceData === 'function') {
                                        window.startInspectionWithDeviceData(deviceData);
                                    }

                                    if (typeof showToast === 'function') {
                                        showToast(
                                            res.reused ? 'Device found and ready to inspect.' : 'Device ready to inspect.',
                                            'success'
                                        );
                                    }
                                }, { once: true });

                                bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                            } else {
                                if (typeof window.startInspectionWithDeviceData === 'function') {
                                    window.startInspectionWithDeviceData(deviceData);
                                }

                                if (typeof showToast === 'function') {
                                    showToast(
                                        res.reused ? 'Device found and ready to inspect.' : 'Device ready to inspect.',
                                        'success'
                                    );
                                }
                            }


                            
                        })
                        .catch(err => {
                            if (errBox) {
                                errBox.textContent = 'Error: ' + (err.message || 'Unknown error');
                                errBox.classList.remove('d-none');
                            }
                        })
                        .finally(() => {
                            if (saveBtn) {
                                saveBtn.disabled = false;
                                saveBtn.textContent = '+ Save Device';
                            }
                        });
                });
            }

            // Handle work order form submission (create or update)
            const workOrderForm = document.getElementById('inspectionWorkOrderForm');
            if (workOrderForm) {
                workOrderForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const woId = document.getElementById('woId').value;
                    const url = woId ? (WORK_ORDER_UPDATE_URL + '/' + encodeURIComponent(woId)) :
                        WORK_ORDER_CREATE_URL;
                    const params = new URLSearchParams();
                    // Include CSRF token (fetch() bypasses $.ajaxSetup)
                    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    var csrfCookie = (document.cookie.match(/csrf_cookie_name=([^;]+)/) || [])[1];
                    var csrfVal = (csrfMeta ? csrfMeta.getAttribute('content') : null) || (csrfCookie ? decodeURIComponent(csrfCookie) : '');
                    if (csrfVal) params.append('csrf_test_name', csrfVal);
                    params.append('site_id', SITE_ID);
                    params.append('title', document.getElementById('woTitle').value);
                    params.append('equipment_id', document.getElementById('woEquipmentId').value);
                    params.append('status', document.getElementById('woStatus').value);
                    params.append('priority', document.getElementById('woPriority').value);
                    params.append('assigned_to', document.getElementById('woTech').value);
                    params.append('start_date', document.getElementById('woStartDate').value);
                    params.append('end_date', document.getElementById('woEndDate').value);
                    params.append('description', document.getElementById('woDescription').value);
                    // Pass current inspection group_id so work orders are scoped to the inspection
                    if (window.CURRENT_INSPECTION_GROUP_ID) {
                        params.append('group_id', window.CURRENT_INSPECTION_GROUP_ID);
                    }
                    // Submit via fetch
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: params.toString()
                        })
                        .then(resp => resp.json())
                        .then(res => {
                            if (!res || !res.success) {
                                const errBox = document.getElementById('workOrderError');
                                if (errBox) {
                                    errBox.classList.remove('d-none');
                                    errBox.textContent = res && res.message ? res.message :
                                        'Failed to save work order';
                                }
                                return;
                            }
                            // Build row data from response and form fields
                            const row = {
                                id: res.work_order_id || woId,
                                title: document.getElementById('woTitle').value,
                                priority: document.getElementById('woPriority').value,
                                status: document.getElementById('woStatus').value,
                                assigned_to_name: (function () {
                                    var sel = document.getElementById('woTech');
                                    return sel && sel.selectedIndex >= 0 ? sel.options[sel.selectedIndex].text : 'N/A';
                                })(),
                                start_date: document.getElementById('woStartDate').value,
                                end_date: document.getElementById('woEndDate').value,
                                description: document.getElementById('woDescription').value,
                                asset_tag: res.asset_tag || document.getElementById('woAsset').value,
                                serial_number: res.serial_number || document.getElementById('woSerial')
                                    .value,
                            };
                            if (woId) {
                                updateWorkOrderRow(row);
                            } else {
                                appendWorkOrderRow(row);
                            }
                            // Hide modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'workOrderModal'));
                            if (modal) modal.hide();
                            // Switch to Work Orders tab
                            window.showWorkOrdersTab();
                        })
                        .catch(() => {
                            const errBox = document.getElementById('workOrderError');
                            if (errBox) {
                                errBox.classList.remove('d-none');
                                errBox.textContent = 'Error saving work order.';
                            }
                        });
                });
            }
        })();
    </script>


    <!-- end inspectionWizardModal -->


    <!-- ================================================
     EDIT INSPECTION MODAL (legacy single-form, for editing existing records)
     ================================================ -->
    <div class="modal fade" id="editInspectionModal" tabindex="-1" aria-labelledby="editInspectionModalLabel"
        aria-hidden="true">
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
                                <label for="workorder-equipment" class="form-label">Equipment <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="edit-workorder-equipment" name="equipment_id" required>
                                    <option value="">-- Select Equipment --</option>
                                    <?php foreach ($equipment as $eq): ?>
                                        <option value="<?= $eq['id'] ?>"><?= esc($eq['asset_tag']) ?> -
                                            <?= esc($eq['make']) ?> <?= esc($eq['model']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-inspection-scheduled-at" class="form-label">Scheduled Date <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="edit-inspection-scheduled-at"
                                    name="scheduled_at" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-inspection-status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
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
                                <input type="datetime-local" class="form-control" id="edit-inspection-completed-at"
                                    name="completed_at">
                            </div>
                            <div class="col-md-6">
                                <label for="edit-inspection-next-due-date" class="form-label">Next Due Date</label>
                                <input type="date" class="form-control" id="edit-inspection-next-due-date"
                                    name="next_due_date">
                            </div>
                            <div class="col-12">
                                <label for="edit-inspection-findings" class="form-label">Findings</label>
                                <textarea class="form-control" id="edit-inspection-findings" name="findings"
                                    rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label for="edit-inspection-notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="edit-inspection-notes" name="notes"
                                    rows="3"></textarea>
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
</div>
<!-- ================================================
     ADD WORK ORDER MODAL (unchanged)
     ================================================ -->
<div class="modal fade" id="addWorkOrderModal" tabindex="-1" aria-labelledby="addWorkOrderModalLabel"
    aria-hidden="true">
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
                            <label for="workorder-title" class="form-label">Title <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="workorder-title" name="title" required>
                        </div>
                        <div class="col-12">
                            <label for="workorder-sn" class="form-label">S/N</label>
                            <input type="text" class="form-control" id="workorder-sn" name="sn"
                                placeholder="Serial number." autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-equipment" class="form-label">Equipment <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="workorder-equipment" name="equipment_id" required>
                                <option value="">-- Select Equipment --</option>
                                <?php foreach ($equipment as $eq): ?>
                                    <option
                                        value="<?= (int) $eq['id'] ?>"
                                        data-master-equipment-id="<?= (int) ($eq['master_equipment_id'] ?? 0) ?>">
                                        <?= esc($eq['asset_tag']) ?> - <?= esc($eq['make']) ?> <?= esc($eq['model']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-status" class="form-label">Status <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="workorder-status" name="status" required>
                                <option value="">-- Select Status --</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-priority" class="form-label">Priority <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="workorder-priority" name="priority" required>
                                <option value="">-- Select Priority --</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="workorder-assigned-to" class="form-label">Technician</label>
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
                            <textarea class="form-control" id="workorder-description" name="description"
                                rows="4"></textarea>
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
    var WIZ_SITE_ID = '<?= $site['id'] ?>';

    $(document).ready(function() {


        // ─── DataTables Init ─────────────────────────────────
        function getTodayDate() {
            const today = new Date();
            let day = String(today.getDate()).padStart(2, '0');
            let month = String(today.getMonth() + 1).padStart(2, '0');
            let year = today.getFullYear();
            return day + month + year;
        }
        // ─── COMMON PDF DESIGN FUNCTION ─────────────────
        function pdfCustomize(doc) {

            // font size
            doc.defaultStyle.fontSize = 7;

            // header style
            doc.styles.tableHeader = {
                bold: true,
                fontSize: 8,
                color: 'black',
                fillColor: '#a4d169',
                alignment: 'center'
            };

            // margins
            doc.pageMargins = [10, 10, 10, 10];

            // borders
            var objLayout = {};
            objLayout.hLineWidth = function() {
                return 0.5;
            };
            objLayout.vLineWidth = function() {
                return 0.5;
            };
            objLayout.hLineColor = function() {
                return "#aaaaaa";
            };
            objLayout.vLineColor = function() {
                return "#aaaaaa";
            };

            doc.content[1].layout = objLayout;

            // auto column width
            var table = doc.content[1].table;
            var colCount = table.body[0].length;
            table.widths = [];

            for (var i = 0; i < colCount; i++) {
                table.widths.push('*');
            }

            // row colors
            doc.styles.tableBodyEven = {
                fillColor: '#f2f2f2'
            };
            doc.styles.tableBodyOdd = {
                fillColor: '#ffffff'
            };
        }


        /* ================= EQUIPMENT ================= */
        $('#equipment-datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    filename: 'Equipment',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'csv',
                    filename: 'Equipment',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'excel',
                    filename: 'Equipment',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    filename: 'Equipment_' + getTodayDate(),
                    title: 'Equipment',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: ':visible:not(:last-child)' // remove action column
                    },
                    customize: pdfCustomize
                }
            ],
            responsive: true,
            pageLength: 10,
            order: [
                [0, 'asc']
            ]
        });


        /* ================= INSPECTIONS ================= */
        // The inspections listing in the Site Details page has been
        // replaced by a custom inspection workflow.  Only initialise
        // DataTables for the old table if it still exists on the page.
        if ($('#inspections-datatable').length) {
            $('#inspections-datatable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        filename: 'Inspections',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'csv',
                        filename: 'Inspections',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'excel',
                        filename: 'Inspections',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        filename: 'Inspections_' + getTodayDate(),
                        title: 'Inspections',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        customize: pdfCustomize
                    }
                ],
                responsive: true,
                pageLength: 10,
                order: [
                    [1, 'desc']
                ]
            });
        }


        /* ================= WORK ORDERS ================= */
        $('#work-orders-datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    filename: 'WorkOrders',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'csv',
                    filename: 'WorkOrders',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'excel',
                    filename: 'WorkOrders',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    filename: 'WorkOrders_' + getTodayDate(),
                    title: 'Work Orders',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },
                    customize: pdfCustomize
                }
            ],
            responsive: true,
            pageLength: 10,
            order: [
                [5, 'desc']
            ]
        });

        // ─── Equipment Modal (unchanged) ─────────────────────

        // Handle Edit Equipment button click
        $(document).on('click', '.edit-equipment-btn', function() {
            var equipmentId = $(this).data('id');
            $('#equipmentModalLabel').text('Edit Equipment');
            $('#equipmentForm').attr('action', '<?= site_url('admin/equipment/update/') ?>' + equipmentId);

            $.ajax({
                url: '<?= site_url('admin/equipment/show/') ?>' + equipmentId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status !== 'success') {
                        alert('Error loading equipment data');
                        return;
                    }
                    var data = response.data;
                    // Populate all plain fields immediately
                    $('#equipment-id').val(data.id);
                    $('#equipment-asset-tag').val(data.asset_tag);
                    $('#equipment-serial-number').val(data.serial_number);
                    $('#equipment-department').val(data.department);
                    $('#equipment-location').val(data.location);
                    $('#equipment-status').val(data.status);
                    $('#equipment-pm-kit').val(data.pm_kit);
                    $('#equipment-fast-notes').val(data.fast_notes);
                    $('#equipment-customer-location').val(data.site_id);
                    $('#equipment-installation-date').val(data.installation_date);
                    $('#equipment-warranty-expires').val(data.warranty_expires);
                    // Set make/model/device_type directly — do NOT rely on the
                    // dropdown AJAX callback which can race and blank these out.
                    $('#equipment-make').val(data.make || '');
                    $('#equipment-model').val(data.model || '');
                    $('#equipment-device-type').val(data.device_type || '');
                    // Show modal; the show.bs.modal guard ensures dropdown
                    // suggestions load only in Add mode (no equipment-id set yet,
                    // but we set it above so the guard will skip the empty call).
                    $('#addEquipmentModal').modal('show');
                    // Populate suggestion lists in background (does NOT overwrite values)
                    loadEquipmentDropdownOptions(data.make || '', data.model || '', data.device_type || '');
                },
                error: function() {
                    alert('Failed to fetch equipment data');
                }
            });
        });

        /**
         * -------------------------------------------------------------------
         * Custom Inspection Workflow Functions
         *
         * The site-inspection-workflow component included in the Inspections tab
         * relies on a number of JavaScript helpers to toggle views, update
         * statuses, and handle exports.  The following functions provide
         * minimal implementations so the UI operates without errors.  They can
         * be expanded upon to integrate with your backend and business logic.

         * The dynamic inspection workflow requires the current site ID.  Capture
         * it here from the server-side so AJAX requests know which site they
         * are targeting.
         */
        const siteId = <?= (int)($site['id'] ?? 0) ?>;
        window.siteId = siteId; // needed by workflow partial inline handlers
        // Switch view from dashboard to inspection.  Hide other views when
        // starting an inspection and show the inspection detail view.
        window.startInspection = function startInspection() {
            // ── Reset all state so this is a completely NEW inspection ────────────
            // Clear group IDs — the backend will auto-create a new group_id when
            // the first Pass/Fail is recorded.
            window.CURRENT_INSPECTION_GROUP_ID = null;
            window.CURRENT_REPORT_GROUP_ID = null;
            window._savedInspTitle = null; // clear saved title on new inspection

            // Clear the inspection header labels
            var siteLabel = document.getElementById('insp-site-label');
            var titleEl = document.getElementById('insp-title');
            var idLabel = document.getElementById('insp-id-label');
            var techEl = document.getElementById('insp-technician');
            var siteName = '<?= esc($site['name'] ?? '—') ?>';
            if (siteLabel) siteLabel.textContent = 'Site: ' + siteName;
            if (titleEl) { titleEl.textContent = 'New Inspection'; }
            if (idLabel) idLabel.textContent = '—';
            if (techEl) techEl.textContent = '—';
            var dateDisp = document.getElementById('insp-date-display');
            if (dateDisp) dateDisp.textContent = '';

            // Reset the status badge to "In Progress"
            var statusBtn = document.getElementById('statusDropdown');
            if (statusBtn) {
                statusBtn.className = 'btn btn-light dropdown-toggle status-badge status-in-progress';
                statusBtn.innerHTML = '<i class="fa-solid fa-rotate"></i> In Progress';
            }

            // Clear the asset barcode input and pass/fail form
            var assetInput = document.getElementById('assetInput');
            if (assetInput) { assetInput.value = ''; }
            var inspectAsset = document.getElementById('inspectAsset');
            if (inspectAsset) inspectAsset.value = '';
            // Fix 1: Reset Action Performed to "-- Select --" and blank Notes on new inspection
            var inspectNotes = document.getElementById('inspectNotes');
            if (inspectNotes) inspectNotes.value = '';
            var actionSel = document.getElementById('inspectActionPerformed');
            if (actionSel) actionSel.selectedIndex = 0;

            // Switch Pass/Fail tab back to "Not Inspected" tab by default
            var notInspTabBtn = document.getElementById('not-inspected-tab');
            if (notInspTabBtn) bootstrap.Tab.getOrCreateInstance(notInspTabBtn).show();

            // Clear the reports tab so it doesn't show a previous inspection's report
            var reportsTabContent = document.getElementById('reportsTabContent');
            if (reportsTabContent) reportsTabContent.innerHTML =
                '<p class="text-muted text-center py-4">Inspect devices and the report will appear here.</p>';

            // Show the inspection view
            const dashView = document.getElementById('view-dashboard');
            const inspView = document.getElementById('view-inspection');
            const woView = document.getElementById('view-workorders');
            const reportsView = document.getElementById('view-reports');
            if (dashView) dashView.classList.add('d-none-view');
            if (woView) woView.classList.add('d-none-view');
            if (reportsView) reportsView.classList.add('d-none-view');
            if (inspView) inspView.classList.remove('d-none-view');

            // Focus the asset input
            setTimeout(function() {
                var input = document.getElementById('assetInput');
                if (input) {
                    input.focus();
                    input.select();
                }
            }, 100);
        }

        // Return to the dashboard view from any other section.
        window.showDashboard = function showDashboard() {
            const inspView = document.getElementById('view-inspection');
            const woView = document.getElementById('view-workorders');
            const reportsView = document.getElementById('view-reports');
            const dashView = document.getElementById('view-dashboard');
            if (inspView) inspView.classList.add('d-none-view');
            if (woView) woView.classList.add('d-none-view');
            if (reportsView) reportsView.classList.add('d-none-view');
            if (dashView) dashView.classList.remove('d-none-view');
        }

        // Update the status badge based on selection from the dropdown.
       function applyInspectionStatusUI(status) {
        if (typeof setInspectionStatusUI === 'function') {
            setInspectionStatusUI(status || 'In Progress');
            return;
        }

        const btn = document.getElementById('statusDropdown');
        if (!btn) return;

        const normalized = String(status || '').trim().toLowerCase();
        const isClosed =
            normalized === 'closed/complete' ||
            normalized === 'closed' ||
            normalized === 'complete';

        btn.className = 'btn btn-light dropdown-toggle status-badge';

        if (isClosed) {
            btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Closed/Complete';
            btn.classList.add('status-closed');
        } else {
            btn.innerHTML = '<i class="fa-solid fa-rotate"></i> In Progress';
            btn.classList.add('status-in-progress');
        }
        }

        window.viewInspection = function viewInspection(groupId, siteName, testName, inspIdLabel, techName, currentStatus) {
            const siteLabel = document.getElementById('insp-site-label');
            const title = document.getElementById('insp-title');
            const idLabel = document.getElementById('insp-id-label');
            const techLabel = document.getElementById('insp-technician');
            const dashView = document.getElementById('view-dashboard');
            const inspView = document.getElementById('view-inspection');

            if (siteLabel) siteLabel.textContent = 'Site: ' + (siteName || '—');
            if (title) title.textContent = testName || '—';
            if (idLabel) idLabel.textContent = inspIdLabel || '—';
            if (techLabel) techLabel.textContent = techName || '—';

            window._savedInspTitle = testName || '—';
            window.CURRENT_INSPECTION_GROUP_ID = groupId;
            window.CURRENT_REPORT_GROUP_ID = groupId;

            if (inspView) {
                inspView.setAttribute('data-current-status', currentStatus || 'In Progress');
            }

            if (dashView) dashView.classList.add('d-none-view');
            if (inspView) inspView.classList.remove('d-none-view');

            if (typeof backgroundRefreshTabs === 'function') {
                backgroundRefreshTabs(function() {
                    if (typeof filterInspectedByGroup === 'function') {
                        filterInspectedByGroup(groupId);
                    }

                    const savedStatus = inspView
                        ? inspView.getAttribute('data-current-status')
                        : currentStatus;

                    applyInspectionStatusUI(savedStatus || 'In Progress');
                });
            } else {
                if (typeof filterInspectedByGroup === 'function') {
                    filterInspectedByGroup(groupId);
                }
                applyInspectionStatusUI(currentStatus || 'In Progress');
            }

            requestAnimationFrame(function() {
                const savedStatus = inspView
                    ? inspView.getAttribute('data-current-status')
                    : currentStatus;

                applyInspectionStatusUI(savedStatus || 'In Progress');
            });
        };

        window.updateStatus = function updateStatus(newStatus) {
            const inspView = document.getElementById('view-inspection');
            if (inspView) {
                inspView.setAttribute('data-current-status', newStatus);
            }

            applyInspectionStatusUI(newStatus);

            const groupId = window.CURRENT_INSPECTION_GROUP_ID || window.CURRENT_REPORT_GROUP_ID;
            if (!groupId) return;

            const rows = document.querySelectorAll('#inspectionsTable tbody tr');
            rows.forEach(function(row) {
                const viewBtn = row.querySelector('.btn-view-insp');
                if (!viewBtn || viewBtn.dataset.group !== groupId) return;

                const statusTd = row.querySelector('td:nth-child(5)');
                if (!statusTd) return;

                viewBtn.dataset.status = newStatus;

                if (String(newStatus).trim().toLowerCase() === 'closed/complete') {
                    statusTd.innerHTML = '<span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Closed</span>';
                } else {
                    statusTd.innerHTML = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-rotate me-1"></i>In Progress</span>';
                }
            });

            const fd = new FormData();
            fd.append('group_id', groupId);
            fd.append('status', newStatus);
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            fetch('<?= site_url("admin/inspections/updateGroupStatus") ?>', {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(res) {
                if (typeof showToast === 'function') {
                    showToast(
                        res.success ? 'Status updated.' : 'Failed to save status.',
                        res.success ? 'success' : 'danger'
                    );
                }
            })
            .catch(function() {
                if (typeof showToast === 'function') {
                    showToast('Network error saving status.', 'danger');
                }
            });
        };

        window.startInspectionWithDeviceData = function startInspectionWithDeviceData(resp) {
            resp = resp || {};
            const assetVal = resp.asset_tag || '';
            _originalScannedAsset = assetVal;
            window._inspectionDeviceMeta = {
                equipment_id: resp.equipment_id || null,
                asset_tag: assetVal,
                make: resp.make || '',
                model: resp.model || '',
                device_type: resp.device_type || '',
                serial_number: resp.serial_number || '',
                department: resp.department || '',
                location: resp.location || '',
                est: resp.est || '',
                cal: resp.cal || ''
            };

            const tabButton = document.getElementById('inspect-device-tab');
            const emptyView = document.getElementById('inspectDeviceEmpty');
            const formWrapper = document.getElementById('inspectDeviceFormWrapper');
            if (tabButton) bootstrap.Tab.getOrCreateInstance(tabButton).show();
            if (emptyView) emptyView.classList.add('d-none');
            if (formWrapper) formWrapper.classList.remove('d-none');

            $('#inspectAsset').val(assetVal);
            $('#inspectModelDisplay').text(resp.model && resp.model.length ? resp.model : (resp.make || '—'));
            // Restore last-used dept/room when device has none saved
            $('#inspectDept').val(resp.department || window._lastDept || '');
            $('#inspectRoom').val(resp.location || window._lastRoom || '');
            $('#inspectSerial').val(resp.serial_number || '');
            $('#inspectPMFrequency').val('12 Month');

            var existingRow = document.querySelector('#inspectionTableBody tr[data-asset="' + assetVal.replace(/"/g, '') + '"]');
            var existingNotes = existingRow ? (existingRow.getAttribute('data-notes') || '') : (resp.notes || '');
            var existingAction = existingRow ? (existingRow.getAttribute('data-action') || '') : (resp.action_performed || '');
            var actionSel = document.getElementById('inspectActionPerformed');
            $('#inspectNotes').val(existingNotes || '');
            if (actionSel) {
                if (existingAction) {
                    for (var oi = 0; oi < actionSel.options.length; oi++) {
                        if (actionSel.options[oi].value === existingAction) {
                            actionSel.selectedIndex = oi;
                            break;
                        }
                    }
                } else {
                    actionSel.selectedIndex = 0;
                }
            }
        };

        // Handle asset submission.  For demonstration this simply enables
        // the Pass/Fail tab and populates example values.  Integrate your
        // actual asset lookup here.
        window.handleAssetGo = function handleAssetGo() {
            const assetInput = document.getElementById('assetInput');
            const asset = assetInput ? assetInput.value.trim() : '';
            if (!asset) {
                alert('Please enter an Asset number.');
                return;
            }
            // Query the backend for equipment details by asset tag and site ID.
            $.get('<?= site_url('admin/site-inspection/get-equipment') ?>', {
                    asset_tag: asset,
                    site_id: siteId,
                    group_id: window.CURRENT_INSPECTION_GROUP_ID || window.CURRENT_REPORT_GROUP_ID || ''
                }, function(resp) {
                   if (resp && resp.exists_other_site) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Asset already assigned',
                            text: resp.message || ('Asset # "' + asset + '" already belongs to another site.'),
                            confirmButtonColor: '#7c3aed'
                        });
                        return;
                    }

                    if (!resp || resp.found === false) {
                        var addAssetInput = document.getElementById('addAsset');
                        if (addAssetInput) addAssetInput.value = asset;

                        ['addModel', 'addSerial', 'addManufacturer', 'addType', 'addDescription', 'addDept',
                            'addRoom', 'addNotes'
                        ].forEach(function(id) {
                            var el = document.getElementById(id);
                            if (el) el.value = '';
                        });

                        var addDeviceModalEl = document.getElementById('addDeviceModal');
                        if (addDeviceModalEl) {
                            bootstrap.Modal.getOrCreateInstance(addDeviceModalEl).show();
                        } else {
                            alert('Device with Asset # ' + asset + ' was not found for this site. Please add it as new equipment.');
                        }
                        return;
                    }
                    if (typeof window.startInspectionWithDeviceData === 'function') {
                        window.startInspectionWithDeviceData(resp || {});
                    }
                }, 'json')
                .fail(function() {
                    alert('Error looking up equipment. Please try again.');
                });
        }

        // When selecting Inspect from the Not Inspected list, show the Pass/Fail tab
        window.inspectFromNotInspected = function inspectFromNotInspected(asset) {
            const input = document.getElementById('assetInput');
            if (input) {
                input.value = asset;
            }
            handleAssetGo();
        }

        // Add Enter key handler for assetInput barcode field
        $(document).ready(function() {
            const assetInput = document.getElementById('assetInput');
            if (assetInput) {
                assetInput.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter' || event.keyCode === 13) {
                        event.preventDefault();
                        handleAssetGo();
                    }
                });
            }
        });

        /**
         * Record an inspection result via AJAX.
         *
         * This helper gathers values from the Pass/Fail form and posts them
         * to the site-inspection/record endpoint.  Upon success the page
         * reloads to reflect updated lists.  On failure a message is shown.
         *
         * @param {string} result Pass | Fail | Repair
         */
        // Track the original scanned asset tag so changes to the ID field don't break lookup
        var _originalScannedAsset = '';

        function recordInspection(result, onSuccessCallback) {
            var now = new Date();
            var pad = function(n){ return n < 10 ? '0'+n : n; };
            var localNow = now.getFullYear() + '-' + pad(now.getMonth()+1) + '-' + pad(now.getDate())
                + ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
            var meta = window._inspectionDeviceMeta || {};
            var payload = {
                site_id: siteId,
                asset_tag: $('#inspectAsset').val(),
                original_asset_tag: _originalScannedAsset || $('#inspectAsset').val(),
                result: result,
                notes: $('#inspectNotes').val(),
                department: $('#inspectDept').val(),
                room: $('#inspectRoom').val(),
                serial_number: $('#inspectSerial').val(),
                action_performed: $('#inspectActionPerformed').val(),
                pm_frequency: $('#inspectPMFrequency').val(),
                completed_at: localNow,
                make: meta.make || '',
                model: meta.model || '',
                device_type: meta.device_type || ''
            };

            // ── KEY FIX: if we are already viewing an active inspection session,
            // reuse its group_id so new devices are appended to the SAME report
            // instead of creating a brand-new inspection group. ──────────────────
            if (typeof window.CURRENT_REPORT_GROUP_ID !== 'undefined' && window.CURRENT_REPORT_GROUP_ID) {
                payload.group_id = window.CURRENT_REPORT_GROUP_ID;
            }

            if (!payload.asset_tag) {
                alert('Asset tag is missing. Please enter a valid Asset #.');
                return;
            }

            // Capture the currently active sub-tab BEFORE the AJAX call
            var activeSubTabId = null;
            var activeSubTabBtn = document.querySelector('#myTabContent') ?
                null :
                null;
            // Find which inner tab is currently active inside view-inspection
            var activeNavLink = document.querySelector(
                '#view-inspection .nav-tabs .nav-link.active, #view-inspection [role="tablist"] .nav-link.active'
            );
            if (activeNavLink) activeSubTabId = activeNavLink.id;

            $.post('<?= site_url('admin/site-inspection/record') ?>', payload, function(resp) {
                if (resp && resp.success) {
                    // ── CRITICAL: capture the group_id returned by the server ──────
                    // For a new inspection CURRENT_INSPECTION_GROUP_ID was null before
                    // this call. The server created a fresh group_id and returned it.
                    // We must store it NOW before the background refresh so that
                    // filterInspectedByGroup() can find the right rows.
                    var newGroupId = resp.group_id || null;
                    if (newGroupId) {
                        window.CURRENT_INSPECTION_GROUP_ID = newGroupId;
                        window.CURRENT_REPORT_GROUP_ID = newGroupId;

                        // Update the inspection header with the real ID
                        var idLabel = document.getElementById('insp-id-label');
                        if (idLabel && (idLabel.textContent === '—' || !idLabel.textContent.trim())) {
                            idLabel.textContent = newGroupId;
                        }
                        // Fix 4: Use localStorage to persist title — restoreInspTitle handles it
                        if (typeof window.restoreInspTitle === 'function') {
                            window.restoreInspTitle(newGroupId);
                        } else {
                            var titleEl = document.getElementById('insp-title');
                            var currentTitle = titleEl ? titleEl.textContent.trim() : '';
                            if (titleEl && (currentTitle === 'New Inspection' || currentTitle === '—' || currentTitle === '')) {
                                titleEl.textContent = window._savedInspTitle || resp.inspection_type || 'Inspection';
                            }
                        }
                    }
                    // Fix 8: Update live timestamp when inspection result is recorded
                    (function() {
                        var dateDisplayEl = document.getElementById('insp-date-display');
                        if (!dateDisplayEl) return;
                        var nowDisp = new Date();
                        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                        var pd = function(n) { return n < 10 ? '0' + n : n; };
                        var h = nowDisp.getHours(), ampm = h >= 12 ? 'PM' : 'AM';
                        h = h % 12 || 12;
                        dateDisplayEl.textContent = months[nowDisp.getMonth()] + ' ' + nowDisp.getDate()
                            + ', ' + nowDisp.getFullYear() + ' ' + h + ':' + pd(nowDisp.getMinutes()) + ' ' + ampm;
                    })();

                    // ── Background refresh: reload page HTML silently, then restore state ──
                    $.get(window.location.href, function(html) {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');

                        // Refresh inspected items table body
                        var newInspBody = doc.getElementById('inspectionTableBody');
                        var curInspBody = document.getElementById('inspectionTableBody');
                        if (newInspBody && curInspBody) curInspBody.innerHTML = newInspBody
                            .innerHTML;

                        // Refresh not-inspected table body
                        var newNotInspBody = doc.getElementById('notInspectedTableBody');
                        var curNotInspBody = document.getElementById('notInspectedTableBody');
                        if (newNotInspBody && curNotInspBody) curNotInspBody.innerHTML =
                            newNotInspBody.innerHTML;

                        // Refresh device type counter
                        var newCounter = doc.getElementById('deviceTypeCountsBody');
                        var curCounter = document.getElementById('deviceTypeCountsBody');
                        if (newCounter && curCounter) curCounter.innerHTML = newCounter.innerHTML;

                        // Refresh tab badge counts (e.g. "Not Inspected 2", "Inspected Items 1")
                        ['not-inspected-count', 'inspected-count', 'not-inspected-tab',
                            'inspected-tab'
                        ].forEach(function(id) {
                            var newEl = doc.getElementById(id);
                            var curEl = document.getElementById(id);
                            if (newEl && curEl) curEl.innerHTML = newEl.innerHTML;
                        });

                        // ── Re-apply group filter so Device Type Counter rows, badges,
                        // and Work Orders table are all visible immediately ───────
                        var currentGroupId = window.CURRENT_INSPECTION_GROUP_ID || window
                            .CURRENT_REPORT_GROUP_ID || null;
                        if (currentGroupId && typeof filterInspectedByGroup === 'function') {
                            filterInspectedByGroup(currentGroupId);
                        }

                        // ── Silently refresh the Reports tab content (no modal) ──────────
                        // The modal only opens when the user clicks the report icon explicitly.
                        if (currentGroupId) {
                            var reportsTabEl = document.getElementById('reportsTabContent');
                            if (reportsTabEl) {
                                reportsTabEl.innerHTML = '';
                                var rIframe = document.createElement('iframe');
                                rIframe.style.cssText = 'width:100%;height:72vh;border:none;background:#0E1630;border-radius:0 0 10px 10px;';
                                var rWrapper = document.createElement('div');
                                rWrapper.style.cssText = 'background:#0E1630;border:1px solid rgba(255,255,255,.12);border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.35);';
                                rWrapper.appendChild(rIframe);
                                reportsTabEl.appendChild(rWrapper);
                                fetch('<?= site_url("admin/inspections/reportPdf") ?>/' + encodeURIComponent(currentGroupId) + '?inline=1', {
                                    headers: { 'Accept': 'text/html' }
                                })
                                .then(function(r) { return r.text(); })
                                .then(function(html) {
                                    rIframe.contentDocument.open();
                                    rIframe.contentDocument.write(html);
                                    rIframe.contentDocument.close();
                                })
                                .catch(function() {});
                            }
                        }

                        // ── Clear asset input fields for next inspection ──────────────────
                        // Clear the barcode input (main asset scanner field)
                        var assetInput = document.getElementById('assetInput');
                        if (assetInput) {
                            assetInput.value = '';
                        }

                        // Clear device-specific fields but KEEP dept and room
                        // so they carry over to the next device in the same area.
                        $('#inspectAsset').val('');
                        $('#inspectNotes').val('');
                        $('#inspectSerial').val('');
                        // dept (#inspectDept) and room (#inspectRoom) intentionally NOT cleared
                        // but store them so next scan can restore if device has no saved location
                        window._lastDept = $('#inspectDept').val();
                        window._lastRoom = $('#inspectRoom').val();

                        // ── Switch to "Inspected Items" sub-tab (unless a callback overrides) ──
                        if (typeof onSuccessCallback === 'function') {
                            // Let the caller decide which tab to show
                            onSuccessCallback();
                        } else {
                            var inspectedTabBtn = document.getElementById('inspected-tab');
                            if (inspectedTabBtn) bootstrap.Tab.getOrCreateInstance(inspectedTabBtn)
                                .show();
                        }

                        // Brief success toast
                        if (typeof showToast === 'function') {
                            showToast('Inspection recorded as <strong>' + result + '</strong>!',
                                'success');
                        } else if (typeof toast === 'function') {
                            toast('Inspection recorded as <strong>' + result + '</strong>!',
                                'success');
                        }

                        // ── Focus asset input for next scan ────────────────────────────────
                        setTimeout(function() {
                            if (assetInput) {
                                assetInput.focus();
                                assetInput.select();
                            }
                        }, 200);
                    }).fail(function() {
                        // Fallback: full reload staying on inspected tab
                        sessionStorage.setItem('siteDetailsActiveTab', 'inspections-tab');
                        sessionStorage.setItem('siteDetailsActiveSubTab', 'inspected-tab');
                        location.reload();
                    });
                } else {
                    var msg = resp && resp.message ? resp.message : 'Failed to record inspection.';
                    alert(msg);
                }
            }, 'json').fail(function() {
                alert('Error recording inspection. Please try again.');
            });
        }

        // Attach click handlers for inspection outcome buttons.  Use jQuery
        // delegated events to ensure buttons inside dynamic content work.
        $(document).ready(function() {
            // Remove existing handlers to prevent duplicates
            $('#btnPassInspection').off('click').on('click', function() {
                recordInspection('Pass');
            });
            $('#btnFailInspection').off('click').on('click', function() {
                recordInspection('Fail');
            });
            $('#btnRepairInspection').off('click').on('click', function() {
                recordInspection('Repair');
            });
            $('#btnFailWOInspection').off('click').on('click', function() {
                // Capture asset before recordInspection refreshes UI
                var assetId = $('#inspectAsset').val();
                if (!assetId) {
                    alert('Please scan or enter an Asset # first.');
                    return;
                }

                recordInspection('Fail', function() {
                    // 1) Switch to INSPECTION -> Work Orders tab (your updated inner tab id)
                    if (typeof window.showWorkOrdersTab === 'function') {
                        window.showWorkOrdersTab();
                    }

                    // 2) Open the inspection work order modal with auto-fill
                    if (typeof window.openWorkOrderModalFromInventory === 'function') {
                        window.openWorkOrderModalFromInventory(assetId);
                    } else {
                        // fallback: at least set asset and trigger change
                        var woAssetEl = document.getElementById('woAsset');
                        if (woAssetEl) {
                            woAssetEl.value = assetId;
                            if (typeof window.onWOAssetChange === 'function') window
                                .onWOAssetChange();
                        }
                    }
                });
            });

        });

        /* ══════════════════════════════════════════════════════════════
           TABLE EXPORT UTILITIES
           Reads visible rows from any table and exports to
           Copy / CSV / Excel (TSV) / PDF (print window).
           Skips the first column (Action buttons) automatically.
        ══════════════════════════════════════════════════════════════ */
        (function() {
            var SITE_NAME = '<?= esc($site['name'] ?? '') ?>';

            // Extract header + data rows from a table, skipping Action column
            function getTableData(tableId) {
                var table  = document.getElementById(tableId);
                if (!table) return { headers: [], rows: [] };

                var headers = [];
                var rows    = [];

                // Headers — skip col 0 (Action)
                var ths = table.querySelectorAll('thead th');
                ths.forEach(function(th, i) {
                    if (i === 0) return; // skip Action
                    headers.push(th.textContent.trim());
                });

                // Visible data rows — skip col 0 (buttons)
                var trs = table.querySelectorAll('tbody tr:not([style*="display: none"]):not([style*="display:none"])');
                trs.forEach(function(tr) {
                    // Skip "empty" placeholder rows
                    var tds = tr.querySelectorAll('td');
                    if (tds.length <= 1) return;
                    var row = [];
                    tds.forEach(function(td, i) {
                        if (i === 0) return; // skip Action
                        // Get plain text (strip badge HTML etc.)
                        row.push(td.textContent.trim().replace(/\s+/g, ' '));
                    });
                    if (row.length) rows.push(row);
                });

                return { headers: headers, rows: rows };
            }

            function tableLabel(tableId) {
                var map = {
                    notInspectedTable : 'Not_Inspected',
                    inspectedTable    : 'Inspected_Items',
                    archivedTable     : 'Archived_Items',
                    inventoryTable    : 'All_Inventory',
                    workOrdersTable   : 'Work_Orders',
                };
                return map[tableId] || tableId;
            }

            function safeFilename(str) {
                return str.replace(/[^a-z0-9_\-]/gi, '_');
            }

            function buildFilename(tableId, ext) {
                return safeFilename(SITE_NAME) + '_' + tableLabel(tableId) + '_' + new Date().toISOString().slice(0,10) + '.' + ext;
            }

            // ── Copy ─────────────────────────────────────────────────
            window.copyTable = function copyTable(tableId) {
                var d = getTableData(tableId);
                if (!d.rows.length) { alert('No data to copy.'); return; }
                var text = [d.headers.join('\t')]
                    .concat(d.rows.map(function(r) { return r.join('\t'); }))
                    .join('\n');
                navigator.clipboard.writeText(text).then(function() {
                    var btn = event && event.target;
                    if (btn) { var orig = btn.textContent; btn.textContent = '✓ Copied!'; setTimeout(function() { btn.textContent = orig; }, 1500); }
                }).catch(function() {
                    var ta = document.createElement('textarea');
                    ta.value = text;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                });
            };

            // ── CSV ──────────────────────────────────────────────────
            window.exportTableCSV = function exportTableCSV(tableId) {
                var d = getTableData(tableId);
                if (!d.rows.length) { alert('No data to export.'); return; }
                var esc = function(v) {
                    v = String(v);
                    if (v.indexOf(',') !== -1 || v.indexOf('"') !== -1 || v.indexOf('\n') !== -1) {
                        return '"' + v.replace(/"/g, '""') + '"';
                    }
                    return v;
                };
                var csv = [d.headers.map(esc).join(',')]
                    .concat(d.rows.map(function(r) { return r.map(esc).join(','); }))
                    .join('\n');
                var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
                var a    = document.createElement('a');
                a.href   = URL.createObjectURL(blob);
                a.download = buildFilename(tableId, 'csv');
                a.click();
            };

            // ── Excel (TSV → .xls which Excel opens natively) ────────
            window.exportTableExcel = function exportTableExcel(tableId) {
                var d = getTableData(tableId);
                if (!d.rows.length) { alert('No data to export.'); return; }
                var tsv = [d.headers.join('\t')]
                    .concat(d.rows.map(function(r) { return r.join('\t'); }))
                    .join('\n');
                var blob = new Blob([tsv], { type: 'application/vnd.ms-excel;charset=utf-8;' });
                var a    = document.createElement('a');
                a.href   = URL.createObjectURL(blob);
                a.download = buildFilename(tableId, 'xls');
                a.click();
            };

            // ── PDF (print window) ────────────────────────────────────
            window.exportTablePDF = function exportTablePDF(tableId) {
                var d = getTableData(tableId);
                if (!d.rows.length) { alert('No data to export.'); return; }
                var label = tableLabel(tableId).replace(/_/g,' ');
                var ths = d.headers.map(function(h) { return '<th>' + h + '</th>'; }).join('');
                var trs = d.rows.map(function(r) {
                    return '<tr>' + r.map(function(c) { return '<td>' + c + '</td>'; }).join('') + '</tr>';
                }).join('');

                var html = '<!DOCTYPE html><html><head><meta charset="UTF-8">'
                    + '<title>' + SITE_NAME + ' — ' + label + '</title>'
                    + '<style>'
                    + 'body{font-family:Arial,sans-serif;font-size:11px;margin:20px;}'
                    + 'h2{font-size:15px;margin-bottom:2px;}'
                    + 'h3{font-size:12px;color:#475569;margin:0 0 10px;font-weight:400;}'
                    + 'p{font-size:11px;color:#555;margin:0 0 12px;}'
                    + 'table{width:100%;border-collapse:collapse;}'
                    + 'th{background:#1e293b;color:#fff;padding:7px 10px;text-align:left;font-size:10px;text-transform:uppercase;}'
                    + 'td{padding:6px 10px;border-bottom:1px solid #e2e8f0;}'
                    + 'tr:nth-child(even) td{background:#f8fafc;}'
                    + '@media print{body{margin:0;}}'
                    + '</style></head><body>'
                    + '<h2>' + SITE_NAME + '</h2>'
                    + '<h3>' + label + '</h3>'
                    + '<p>Generated: ' + new Date().toLocaleString() + '</p>'
                    + '<table><thead><tr>' + ths + '</tr></thead><tbody>' + trs + '</tbody></table>'
                    + '<script>window.onload=function(){window.print();}<\/script>'
                    + '</body></html>';

                var w = window.open('', '_blank');
                w.document.write(html);
                w.document.close();
            };

        })();

        // Show the Work Orders view inside the inspection workflow
        // IMPORTANT: Work Orders is a tab INSIDE view-inspection — do NOT hide view-inspection.
        // Simply activate the correct Bootstrap tab.
        window.showWorkOrdersTab = function showWorkOrdersTab() {
            // Make sure the inspection view is visible (not the dashboard)
            const dashView = document.getElementById('view-dashboard');
            const inspView = document.getElementById('view-inspection');
            if (dashView) dashView.classList.add('d-none-view');
            if (inspView) inspView.classList.remove('d-none-view');
            // Activate the Work Orders tab inside the inspection view
            var woTabBtn = document.getElementById('insp-work-orders-tab');
            if (woTabBtn) {
                woTabBtn.classList.remove('disabled');
                bootstrap.Tab.getOrCreateInstance(woTabBtn).show();
            }
        }

        // Show the Inspection Reports tab inside the inspection workflow
        window.showReportsView = function showReportsView() {
            // Make sure the inspection view is visible
            const dashView = document.getElementById('view-dashboard');
            const inspView = document.getElementById('view-inspection');
            if (dashView) dashView.classList.add('d-none-view');
            if (inspView) inspView.classList.remove('d-none-view');
            // Activate the Inspection Reports tab
            var reportsTabBtn = document.getElementById('reports-tab');
            if (reportsTabBtn) {
                bootstrap.Tab.getOrCreateInstance(reportsTabBtn).show();
            }
        }



        // NOTE: The functions openWorkOrderModalFromInventory, openWorkOrderModal,
        // onWOAssetChange and renderWorkOrdersTable are implemented in the
        // custom dynamic script defined earlier in this file. The following
        // stub definitions have been removed to avoid overriding the dynamic
        // implementations.
        // Reset form when adding new equipment
        $('#addEquipmentBtn').on('click', function() {
            $('#equipmentModalLabel').text('Add Equipment');
            $('#equipmentForm')[0].reset();
            $('#equipment-id').val('');
            $('#equipmentForm').attr('action', '<?= site_url('admin/equipment/create') ?>');
            // Set default site_id
            $('#equipment-customer-location').val('<?= $site['id'] ?>');
        });

        $('#equipmentForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var actionUrl = $(this).attr('action');
            var currentId = $('#equipment-id').val() || '0';

            // Client-side serial number duplicate check
            var serialVal = ($('#equipment-serial-number').val() || '').trim();
            if (serialVal) {
                var dupSerial = null;
                <?php foreach ($equipment as $eq): ?>
                if (!dupSerial && '<?= addslashes($eq['serial_number'] ?? '') ?>' === serialVal && String(<?= (int)$eq['id'] ?>) !== currentId) {
                    dupSerial = 'Asset Tag: <?= addslashes($eq['asset_tag'] ?? '') ?>';
                }
                <?php endforeach; ?>
                if (dupSerial) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Serial Number',
                        text: 'Serial number "' + serialVal + '" already exists in this site (' + dupSerial + ').',
                        confirmButtonColor: '#7c3aed',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            }

            $('#equipmentSubmitBtn').prop('disabled', true).html(
                '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: formData,
                dataType: 'json', // ← tell jQuery to parse JSON
                success: function(response) {
                    if (response && response.success === false) {
                        $('#equipmentSubmitBtn').prop('disabled', false).html('Save changes');
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate Asset Tag',
                            text: response.message,
                            confirmButtonColor: '#7c3aed',
                            confirmButtonText: 'OK, I\'ll change it'
                        });
                        return;
                    }
                    // Success — show brief toast then reload
                    Swal.fire({
                        icon: 'success',
                        title: 'Equipment Saved!',
                        text: 'The equipment record has been saved successfully.',
                        confirmButtonColor: '#7c3aed',
                        timer: 1800,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(function() {
                        $('#addEquipmentModal').modal('hide');
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var msg = 'Error saving equipment. Please try again.';
                    try {
                        var parsed = JSON.parse(xhr.responseText);
                        if (parsed && parsed.message) msg = parsed.message;
                    } catch (e) {}
                    $('#equipmentSubmitBtn').prop('disabled', false).html('Save changes');
                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        text: msg,
                        confirmButtonColor: '#7c3aed'
                    });
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
        $(
            '#edit-inspection-next-due-date').val(formatDate($(this).data('next_due_date')));

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
        $('#addWorkOrderModal').on('hidden.bs.modal', function() {
            resetWorkOrderForm();
        });

        function resetWorkOrderForm() {
            $('#workOrderForm')[0].reset();
            $('#workorder-id').val('');
            $('#workOrderModalLabel').text('Add Work Order');
            $('#workOrderSubmitBtn').text('Save Work Order');
            $('#workOrderForm').attr('action', '<?= site_url('admin/work-orders/create') ?>');
        }
        $('#workorder-equipment').on('change', function() {
            var equipmentId = $(this).val();
            if (!equipmentId) {
                $('#workorder-sn').val('');
                return;
            }
            $.ajax({
                url: '<?= site_url('admin/equipment/show/') ?>' + equipmentId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#workorder-sn').val(response.data.serial_number || '');
                    }
                }
            });
        });


        function setAdminWorkOrderEquipment(siteEquipmentId, masterEquipmentId) {
            const $select = $('#workorder-equipment');
            let matched = false;

            // 1. Try direct match on site_equipment.id (option value)
            if (siteEquipmentId && $select.find('option[value="' + siteEquipmentId + '"]').length) {
                $select.val(siteEquipmentId);
                matched = true;
            }

            // 2. Fallback: match by master_equipment_id stored on the option
            if (!matched) {
                $select.find('option').each(function () {
                    const optionValue = String($(this).val() || '');
                    const masterId = String($(this).data('master-equipment-id') || '');
                    if (masterEquipmentId && masterId && masterId === String(masterEquipmentId)) {
                        $select.val(optionValue);
                        matched = true;
                        return false;
                    }
                });
            }

            if (!matched) {
                $select.val('');
            }
        }


       $(document).on('click', '.edit-workorder-btn', function() {
            var id = $(this).data('id');
            $('#workorder-id').val(id);
            $('#workorder-title').val($(this).data('title'));
            // Pass site_equipment_id for direct dropdown match, and master
            // equipment_id as fallback for linked equipment.
            setAdminWorkOrderEquipment(
                $(this).data('site_equipment_id'),
                $(this).data('equipment_id')
            );
            
            $('#workorder-equipment').val($(this).attr('data-site_equipment_id') || '');
            $('#workorder-sn').val($(this).attr('data-serial_number') || '');
            $('#workorder-description').val($(this).data('description'));
            $('#workorder-status').val($(this).data('status'));
            $('#workorder-priority').val($(this).data('priority'));
            // Trigger change so the select properly highlights the saved option.
            $('#workorder-assigned-to').val($(this).data('assigned_to')).trigger('change');
            $('#workorder-start-date').val($(this).data('start_date'));
            $('#workorder-end-date').val($(this).data('end_date'));

            $('#workOrderModalLabel').text('Edit Work Order');
            $('#workOrderSubmitBtn').text('Update Work Order');
            $('#workOrderForm').attr('action', '<?= site_url('admin/work-orders/update/') ?>' + id);
            $('#addWorkOrderModal').modal('show');
        });

        // ─── Delete handlers (unchanged) ─────────────────────
        $(document).on('click', '.delete-workorder-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Delete work order?',
                text: 'This work order will be soft deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it'
            }).then(function(result) {
                if (result.isConfirmed) {
                    window.location.href = '<?= site_url('admin/work-orders/delete/') ?>' + id;
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
            return 'INSP-' + new Date().toISOString().split('T')[0].replace(/-/g, '') + '-' + Math.random()
                .toString(36).substr(2, 9).toUpperCase();
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
                $info.append('<div class="queue-item-details">S/N: ' + (item.serial_number || 'N/A') +
                    ' | Status: ' + item.status + '</div>');

                var $removeBtn = $('<button class="queue-item-remove" data-index="' + index +
                    '">Remove</button>');

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
                url: WIZ_URL_SEARCH_SERIAL, // CHANGED URL
                method: 'GET',
                data: {
                    serial_number: serialNumber,
                    site_id: WIZ_SITE_ID
                }, // CHANGED parameter name
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
                    data: {
                        keyword: val
                    },
                    success: function(results) {
                        $('#wiz-model-dropdown').remove();

                        if (!results || results.length === 0) return;

                        var $wrap = $('#wiz-search-model').parent();
                        $wrap.css('position', 'relative');

                        var html =
                            '<div id="wiz-model-dropdown" style="position:absolute; top:100%; left:0; right:0; z-index:9999; background:#fff; border:1px solid #cbd5e1; border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,0.12); max-height:220px; overflow-y:auto; margin-top:2px;">';

                        $.each(results, function(i, item) {
                            var label = (item.make || '') + ' ' + (item.model ||
                                '');
                            label = $.trim(label) || item.device_type ||
                                'Unknown';

                            html +=
                                '<div class="wiz-model-option" data-make="' + (
                                    item.make || '') + '" data-model="' + (item
                                    .model || '') + '" data-serial_number="' + (
                                    item.serial_number || '') +
                                '" data-device_type="' + (item.device_type ||
                                    '') +
                                '" style="padding:0.55rem 0.75rem; cursor:pointer; border-bottom:1px solid #f1f5f9;" onmouseover="$(this).css(\'background\',\'#eef2ff\')" onmouseout="$(this).css(\'background\',\'#fff\')">';
                            html += '<strong>' + label + '</strong>';
                            if (item.serial_number) {
                                html +=
                                    ' &nbsp;<span style="color:#64748b; font-size:0.82rem;">S/N: ' +
                                    item.serial_number + '</span>';
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
            if (!$(e.target).is('#wiz-search-model') && !$(e.target).closest('#wiz-model-dropdown')
                .length) {
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
                make: wizardMatchedEquip ? (wizardMatchedEquip.make || '') : ($('#wiz-s2-manufacturer').val() ||
                    ''),
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
            alert(
                'Inspection added to queue. Click "Add to Queue & Next Device" to continue or "Complete Inspections" to finish.'
            );
        });

        $('#wizBtnFail').on('click', function() {
            addToQueue('Fail');
            alert(
                'Inspection added to queue. Click "Add to Queue & Next Device" to continue or "Complete Inspections" to finish.'
            );
        });

        $('#wizBtnRepair').on('click', function() {
            addToQueue('Repair');
            alert(
                'Inspection added to queue. Click "Add to Queue & Next Device" to continue or "Complete Inspections" to finish.'
            );
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

            $('#wizBtnComplete').prop('disabled', true).html(
                '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

            $.ajax({
                url: '<?= site_url('admin/inspections/create') ?>',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log('Success response:', response);
                    $('#inspectionWizardModal').modal('hide');
                    // Remember to stay on Inspections tab after reload
                    sessionStorage.setItem('siteDetailsActiveTab', 'inspections-tab');
                    location.reload();
                },
                error: function(xhr) {
                    console.error('Failed to save inspections', xhr);
                    console.error('Response Text:', xhr.responseText);
                    alert('Failed to save inspections. Check console for details.');
                    $('#wizBtnComplete').prop('disabled', false).html(
                        '<i class="fa fa-check-double me-1"></i>Complete Inspections');
                }
            });
        });


        $(document).on('click', '.view-inspection-btn', function() {
            var groupId = $(this).data('group_id'); // Get the group_id from the button

            // Show loading indicator
            // $('#viewInspectionModal').find('.modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');

            // AJAX request to get inspections by group_id
            $.ajax({
                url: '<?= site_url('admin/inspections/getByGroupId') ?>', // Endpoint to fetch inspection data
                method: 'GET',
                data: {
                    group_id: groupId
                },
                success: function(response) {

                    if (response.success) {
                        var inspections = response
                            .data; // Assume response contains the inspections data

                        // Generate HTML for the inspections list
                        var inspectionHtml = '';
                        inspections.forEach(function(inspec) {

                            inspectionHtml += '<tr>';
                            inspectionHtml +=
                                '<td> <button class="btn btn-sm btn-primary btn-edit-inspection" data-inspection-id="' +
                                inspec.inspections_id + '" data-group-id="' + inspec
                                .group_id +
                                '"><i class="fa fa-edit"></i> Edit</button></td>';
                            inspectionHtml += '<td>' + (inspec.inspection_status ===
                                    'Pass' ?
                                    '<span class="badge bg-success">Pass</span>' :
                                    '<span class="badge bg-danger">Fail</span>') +
                                '</td>';
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
                            inspectionHtml += '<td>' + inspec.battery_expiration_date +
                                '</td>';
                            inspectionHtml += '<td>' + inspec.notes + '</td>';
                            inspectionHtml += '</tr>';
                        });
                        // Inject the generated HTML into the modal body
                        $('#inspection-details-body').html(inspectionHtml);

                        // Show the modal
                        $('#viewInspectionModal').modal('show');
                    } else {
                        $('#viewInspectionModal').find('.modal-body').html(
                            '<div class="text-center text-danger">No inspections found for this group.</div>'
                        );
                    }
                },
                error: function() {
                    $('#viewInspectionModal').find('.modal-body').html(
                        '<div class="text-center text-danger">An error occurred while fetching the data.</div>'
                    );
                }
            });
        });


        // ------------------------------------------------------------
        // IMPORTANT: This page uses inline onclick handlers in the HTML
        // (ex: onclick="handleAssetGo()" / onclick="inspectFromNotInspected('...')").
        // Functions declared inside $(document).ready(...) are NOT global,
        // so browsers won't find them from inline handlers.
        // Expose the handlers on window so the buttons work.
        // ------------------------------------------------------------
        window.startInspection = startInspection;
        window.showDashboard = showDashboard;
        window.updateStatus =
            updateStatus;
        window.handleAssetGo = handleAssetGo;
        window.inspectFromNotInspected =
            inspectFromNotInspected;
        window.openInspectionReport = openInspectionReport;
        window.showWorkOrdersTab =
            showWorkOrdersTab;
        window.showReportsView = showReportsView;
        window.openWorkOrderModalFromInventory =
            openWorkOrderModalFromInventory;
        window.openWorkOrderModal = openWorkOrderModal;
        window.previewReportPDF =
            previewReportPDF;
        window.exportReportPDF = exportReportPDF;






    });
</script>



<!-- ═══════════════════════════════════════════════════════
     BULK IMPORT EQUIPMENT MODAL  (Admin → Sites → Details)
     Accepts CSV only for maximum server compatibility.
     ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="adminBulkImportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
        <div class="modal-content" style="background:#0E1630;border:1px solid rgba(255,255,255,.1);border-radius:16px;">
            <div class="modal-header"
                style="background:linear-gradient(135deg,rgba(124,58,237,.9),rgba(34,211,238,.8));border-radius:16px 16px 0 0;border:none;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fa-solid fa-file-csv me-2"></i>Import Equipment from CSV
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Info box -->
                <div class="mb-3 p-3"
                    style="background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.25);border-radius:10px;">
                    <p class="small fw-semibold mb-1" style="color:rgba(34,211,238,.9);">Required CSV columns (Row 1 =
                        headers):</p>
                    <code
                        style="font-size:11px;color:#E9EDFF;background:rgba(0,0,0,.3);padding:6px 10px;border-radius:6px;display:block;line-height:1.8;">
                        Make, Model, Device Type, Asset Tag, Serial Number, Department, Location Or Room
                    </code>
                    <p class="small mb-0 mt-2" style="color:rgba(233,237,255,.5);">
                        Headers are case-insensitive &amp; trim spaces. Asset Tag auto-generated if blank. "N/A" serial
                        treated as empty.
                    </p>
                </div>

                <!-- Alert box -->
                <div id="adminImportAlert" class="d-none alert mb-3" style="border-radius:10px;"></div>

                <!-- File picker -->
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="color:#E9EDFF;">
                        CSV File <span class="text-danger">*</span>
                    </label>
                    <div id="adminDropZone"
                        style="border:2px dashed rgba(124,58,237,.5);border-radius:12px;padding:28px 20px;text-align:center;cursor:pointer;transition:border-color .2s;"
                        onclick="document.getElementById('adminImportFile').click()">
                        <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2" style="color:rgba(124,58,237,.7);"></i>
                        <p class="mb-1 fw-semibold" style="color:#E9EDFF;">Click to choose file or drag &amp; drop</p>
                        <p class="small mb-0" id="adminImportFileName" style="color:rgba(233,237,255,.5);">CSV files
                            only (.csv)</p>
                    </div>
                    <input type="file" id="adminImportFile" accept=".csv" style="display:none;">
                </div>

                <!-- Progress bar (hidden until import starts) -->
                <div id="adminImportProgressWrap" class="d-none mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small" style="color:rgba(233,237,255,.7);"
                            id="adminImportProgressLabel">Uploading...</span>
                        <span class="small fw-bold" style="color:#E9EDFF;" id="adminImportProgressPct">0%</span>
                    </div>
                    <div class="progress" style="height:8px;background:rgba(255,255,255,.08);border-radius:8px;">
                        <div id="adminImportProgressBar" class="progress-bar" role="progressbar"
                            style="width:0%;background:linear-gradient(90deg,rgba(124,58,237,.9),rgba(34,211,238,.8));border-radius:8px;transition:width .3s;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,.08);">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-success" id="adminDownloadSampleCsv">
                    <i class="fa-solid fa-file-csv me-1"></i> Download Sample CSV
                </button>
                <button type="button" class="btn btn-primary" id="adminImportBtn" disabled
                    style="background:linear-gradient(90deg,rgba(34,211,238,.9),rgba(124,58,237,.8));border:none;">
                    <i class="fa-solid fa-upload me-1"></i> Import Equipment
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ── Download Sample CSV ─────────────────────────────────────────────────────
    document.getElementById('adminDownloadSampleCsv').addEventListener('click', function() {
        var headers = ['Make','Model','Device Type','Asset Tag','Serial Number','Department','Location Or Room'];
        var csv = headers.join(',') + '\r\n';
        var blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
        var url  = URL.createObjectURL(blob);
        var a    = document.createElement('a');
        a.href = url; a.download = 'equipment_import_sample.csv'; a.click();
        URL.revokeObjectURL(url);
    });

    // ── Admin Equipment CSV Import ──────────────────────────────────────────────
    (function() {
        var fileInput = document.getElementById('adminImportFile');
        var dropZone = document.getElementById('adminDropZone');
        var importBtn = document.getElementById('adminImportBtn');
        var alertEl = document.getElementById('adminImportAlert');
        var nameEl = document.getElementById('adminImportFileName');
        var progressWrap = document.getElementById('adminImportProgressWrap');
        var progressBar = document.getElementById('adminImportProgressBar');
        var progressPct = document.getElementById('adminImportProgressPct');
        var progressLbl = document.getElementById('adminImportProgressLabel');

        var selectedFile = null;

        function showAlert(type, html) {
            alertEl.className = 'alert alert-' + type + ' mb-3';
            alertEl.innerHTML = html;
            alertEl.classList.remove('d-none');
        }

        function hideAlert() {
            alertEl.classList.add('d-none');
        }

        function setProgress(pct, label) {
            progressWrap.classList.remove('d-none');
            progressBar.style.width = pct + '%';
            progressPct.textContent = pct + '%';
            if (label) progressLbl.textContent = label;
        }

        function onFileSelected(file) {
            if (!file) return;
            var ext = file.name.split('.').pop().toLowerCase();
            if (ext !== 'csv') {
                showAlert('danger',
                    '<i class="fa-solid fa-triangle-exclamation me-2"></i>Only <strong>.csv</strong> files are accepted. Please save your Excel file as CSV first.'
                );
                selectedFile = null;
                importBtn.disabled = true;
                return;
            }
            selectedFile = file;
            hideAlert();
            nameEl.textContent = file.name + '  (' + (file.size / 1024).toFixed(1) + ' KB)';
            nameEl.style.color = 'rgba(34,211,238,.9)';
            dropZone.style.borderColor = 'rgba(34,211,238,.7)';
            importBtn.disabled = false;
        }

        // File input change
        fileInput.addEventListener('change', function() {
            onFileSelected(this.files[0]);
        });

        // Drag and drop
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = 'rgba(124,58,237,.9)';
            this.style.background = 'rgba(124,58,237,.06)';
        });
        dropZone.addEventListener('dragleave', function() {
            this.style.borderColor = 'rgba(124,58,237,.5)';
            this.style.background = '';
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = 'rgba(124,58,237,.5)';
            this.style.background = '';
            onFileSelected(e.dataTransfer.files[0]);
        });

        // Reset modal when closed
        document.getElementById('adminBulkImportModal').addEventListener('hidden.bs.modal', function() {
            selectedFile = null;
            fileInput.value = '';
            importBtn.disabled = true;
            hideAlert();
            progressWrap.classList.add('d-none');
            progressBar.style.width = '0%';
            nameEl.textContent = 'CSV files only (.csv)';
            nameEl.style.color = '';
            dropZone.style.borderColor = 'rgba(124,58,237,.5)';
        });

        // Import button click — use XMLHttpRequest for real upload progress
        importBtn.addEventListener('click', function() {
            if (!selectedFile) return;

            importBtn.disabled = true;
            importBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Importing...';
            hideAlert();
            setProgress(5, 'Preparing upload...');

            var fd = new FormData();
            fd.append('excel_file', selectedFile);
            fd.append('site_id', '<?= (int)($site['id'] ?? 0) ?>');

            // Fresh CSRF from cookie (CI4 regenerate=true)
            var csrfMatch = document.cookie.match(/csrf_cookie_name=([^;]+)/);
            if (csrfMatch) fd.append('csrf_test_name', decodeURIComponent(csrfMatch[1]));

            var xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var pct = Math.round((e.loaded / e.total) * 70); // 0-70% for upload
                    setProgress(pct, 'Uploading file... ' + pct + '%');
                }
            });

            xhr.addEventListener('load', function() {
                setProgress(95, 'Processing rows...');
                importBtn.innerHTML = '<i class="fa-solid fa-upload me-1"></i> Import Equipment';
                importBtn.disabled = false;
                try {
                    var res = JSON.parse(xhr.responseText);
                    setProgress(100, 'Done!');
                    if (res.success) {
                        showAlert('success',
                            '<i class="fa-solid fa-check-circle me-2"></i>' +
                            '<strong>' + res.imported + ' equipment records imported</strong>' +
                            (res.skipped > 0 ? ' &nbsp;·&nbsp; <span class="text-warning">' + res
                                .skipped + ' skipped (duplicates)</span>' : '') +
                            '<br><small class="text-muted">Page will reload in 2 seconds to show new equipment.</small>'
                        );
                        setTimeout(function() {
                            location.reload();
                        }, 2200);
                    } else {
                        showAlert('danger', '<i class="fa-solid fa-triangle-exclamation me-2"></i>' + (
                            res.message || 'Import failed.'));
                        progressWrap.classList.add('d-none');
                    }
                } catch (e) {
                    showAlert('danger',
                        '<i class="fa-solid fa-triangle-exclamation me-2"></i>Server error (status ' +
                        xhr.status + '). ' + xhr.responseText.substring(0, 150));
                    progressWrap.classList.add('d-none');
                }
            });

            xhr.addEventListener('error', function() {
                importBtn.innerHTML = '<i class="fa-solid fa-upload me-1"></i> Import Equipment';
                importBtn.disabled = false;
                showAlert('danger',
                    '<i class="fa-solid fa-triangle-exclamation me-2"></i>Network error. Check your connection and try again.'
                );
                progressWrap.classList.add('d-none');
            });

            xhr.open('POST', '<?= site_url('admin/equipment/bulk-import') ?>');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(fd);
        });
    })();
</script>



<style>
  #woInvoiceModal #invoiceItemsTable thead th {
    background: #f8f9fa !important;
    color: #111827 !important;
    font-weight: 700 !important;
    vertical-align: middle;
    white-space: normal;
  }

  #woInvoiceModal #invoiceItemsTable tbody td,
  #woInvoiceModal #invoiceItemsTable tfoot td {
    background: #ffffff !important;
    color: #111827 !important;
    vertical-align: middle;
  }

  #woInvoiceModal #invoiceItemsTable .form-control,
  #woInvoiceModal #invoiceItemsTable .form-select {
    background: #ffffff !important;
    color: #111827 !important;
    border-color: #ced4da !important;
  }

  #woInvoiceModal #invoiceItemsTable .form-control::placeholder {
    color: #6b7280 !important;
  }

  #woInvoiceModal #invoiceItemsTable .inv-total-cost[readonly] {
    background: #f8f9fa !important;
    color: #111827 !important;
  }

  #woInvoiceModal #invoiceGrandTotal,
  #woInvoiceModal #woGrandTotal {
    color: #111827 !important;
  }
</style>

<div class="modal fade modal-xl" id="woInvoiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2"></i> Work Order Invoice</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="invoiceModalBody">
        <div class="text-center py-5" id="woInvoiceSpinner">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted">Loading invoice data…</p>
        </div>

        <!-- Invoice content (shown after load) -->
        <div id="woInvoiceContent" style="display:none;">

          <!-- Work Order header info (read-only) -->
          <div class="row g-3 mb-3" id="woInvoiceWoInfo"></div>

          <!-- Line Items table -->
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Line Items</h6>
            <button class="btn btn-sm btn-primary" id="woBtnAddItem">
              <i class="fas fa-plus me-1"></i> Add Line Item
            </button>
          </div>

          <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered invoice-editor-table" id="invoiceItemsTable">
              <thead class="table-light">
                <tr>
                  <th style="width:90px">Type</th>
                  <th style="width:90px">Part #</th>
                  <th style="width:130px">Labor/Part Code</th>
                  <th>Description</th>
                  <th style="width:80px">QTY/Hrs</th>
                  <th style="width:100px">Unit Cost ($)</th>
                  <th style="width:100px">Total Cost ($)</th>
                  <th style="width:60px"></th>
                </tr>
              </thead>
              <tbody id="woItemsTbody">
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="6" class="text-end fw-bold">Grand Total</td>
                  <td class="fw-bold" id="woGrandTotal">$0.00</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Notes -->
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Problem Notes</label>
              <textarea id="woProblemNotes" class="form-control" rows="3"
                placeholder="e.g. Weekly Repair/PM week of 3/30 - 4/1"></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold text-danger">Invoice Note</label>
              <textarea id="woInvoiceNote" class="form-control" rows="3"
                placeholder="Billing rates, terms, etc."></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Service Notes</label>
              <textarea id="woServiceNotes" class="form-control" rows="3"
                placeholder="Tech observations..."></textarea>
            </div>
          </div>

          <!-- Signatures -->
          <hr class="my-3">
          <div class="row g-4">
            <!-- Customer Signature -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Customer Acceptance Signature</label>
              <canvas id="woCustCanvas" class="d-block w-100 border rounded"
                style="height:130px;cursor:crosshair;background:#fafafa;border-style:dashed !important;"></canvas>
              <div class="d-flex gap-2 mt-1">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="woClearCust">
                  <i class="fas fa-eraser me-1"></i>Clear
                </button>
              </div>
              <div class="mt-2">
                <label class="form-label small fw-semibold mb-1">Customer Name</label>
                <input type="text" id="woCustName" class="form-control form-control-sm"
                  placeholder="e.g. CHAR MORALES">
              </div>
            </div>
            <!-- Technician Signature -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Technician Signature</label>
              <canvas id="woTechCanvas" class="d-block w-100 border rounded"
                style="height:130px;cursor:crosshair;background:#fafafa;border-style:dashed !important;"></canvas>
              <div class="d-flex gap-2 mt-1">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="woClearTech">
                  <i class="fas fa-eraser me-1"></i>Clear
                </button>
              </div>
              <div class="mt-2">
                <label class="form-label small fw-semibold mb-1">Technician Name</label>
                <input type="text" id="woTechName" class="form-control form-control-sm"
                  placeholder="Technician name">
              </div>
            </div>
          </div>

        </div><!-- /invoiceContent -->
      </div><!-- /modal-body -->

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-success" id="woBtnSave">
          <i class="fas fa-save me-1"></i> Save Invoice
        </button>
        <a class="btn btn-primary" id="woBtnDownload" href="#" target="_blank">
          <i class="fas fa-download me-1"></i> Download PDF
        </a>
      </div>

    </div>
  </div>
</div>

<script>
var _currentWoId = null;
var _laborCodes  = [];

$(function () {
    // ── DataTable ─────────────────────────────────────────────
    // ── Load labor codes once ─────────────────────────────────
    $.getJSON('<?= site_url('admin/work-orders/labor-codes-list') ?>', function (res) {
        if (res.success) _laborCodes = res.data || [];
    });

    // ── Delete WO ─────────────────────────────────────────────
    $(document).on('click', '.btn-delete-wo', function (e) {
        e.preventDefault();
        var woId = $(this).data('wo-id');
        Swal.fire({
            icon: 'warning', title: 'Delete work order?',
            text: 'This cannot be undone.',
            showCancelButton: true, confirmButtonText: 'Yes, delete'
        }).then(function (r) {
            if (!r.isConfirmed) return;
            $.post('<?= site_url('admin/work-orders/delete') ?>/' + woId, {
                '<?= csrf_token() ?>': $('input[name="<?= csrf_token() ?>"]').first().val()
            }, function (res) {
                if (res.success) {
                    Swal.fire('Deleted', res.message, 'success');
                    _woTable.ajax ? _woTable.ajax.reload(null, false) : location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }, 'json');
        });
    });

    // ── Open Invoice Modal ────────────────────────────────────
    $(document).on('click', '.site-wo-invoice-btn', function (e) {
        e.preventDefault();
        _currentWoId = $(this).data('wo-id');

        $('#woInvoiceContent').hide();
        $('#woInvoiceSpinner').show();
        $('#woInvoiceModal').modal('show');

        // Set download link
        $('#woBtnDownload').attr('href', '<?= site_url('admin/work-orders') ?>/' + _currentWoId + '/invoice/download');

        // Fetch invoice data
        $.getJSON('<?= site_url('admin/work-orders') ?>/' + _currentWoId + '/invoice/data', function (res) {
            $('#woInvoiceSpinner').hide();

            if (!res.success) {
                $('#woInvoiceContent').html('<div class="alert alert-danger">Failed to load invoice data.</div>').show();
                return;
            }

            var wo = res.wo || {};
            var inv = res.invoice || {};

            // Header info
            $('#woInvoiceWoInfo').html(
                '<div class="col-md-3"><strong>WO #</strong><br>#WO-' + String(wo.id).padStart(4,'0') + '</div>' +
                '<div class="col-md-3"><strong>Customer</strong><br>' + (wo.customer_name || '—') + '</div>' +
                '<div class="col-md-3"><strong>Site</strong><br>' + (wo.site_name || '—') + '</div>' +
                '<div class="col-md-3"><strong>Technician</strong><br>' + (wo.tech_name || '—') + '</div>'
            );

            // Notes
            $('#woProblemNotes').val(inv.problem_notes || '');
            $('#woInvoiceNote').val(inv.invoice_note   || '');
            $('#woServiceNotes').val(inv.service_notes || '');

            // Line items
            $('#woItemsTbody').empty();
            (res.items || []).forEach(function (item) { addInvoiceRow(item); });
            recalcTotal();

            // Restore saved signatures if any
            if (inv.signature_image) {
                restoreSig(window._woCustCtx, inv.signature_image);
            } else { clearSig(window._woCustCtx); }
            $('#woCustName').val(inv.signed_by || wo.customer_name || '');
            if (inv.tech_sig_image) {
                restoreSig(window._woTechCtx, inv.tech_sig_image);
            } else { clearSig(window._woTechCtx); }
            $('#woTechName').val(inv.tech_signed_by || wo.tech_name || '');
            $('#woInvoiceContent').show();
        }).fail(function () {
            $('#woInvoiceSpinner').hide();
            $('#woInvoiceContent').html('<div class="alert alert-danger">Error loading data.</div>').show();
        });
    });

    // ── Add blank row ─────────────────────────────────────────
    $('#woBtnAddItem').on('click', function () { addInvoiceRow({}); });

    // ── Build labor code select options ───────────────────────
    // Inventory parts for Part-type rows
    var _inventoryParts = [];
    $.getJSON('<?= site_url('admin/inventory/data') ?>', function(res) {
        _inventoryParts = res.data || [];
    });

    function laborCodeOptions(selected) {
        var opts = '<option value="">-- select labor code --</option>';
        _laborCodes.forEach(function(lc) {
            opts += '<option value="' + lc.id + '"'
                + ' data-code="' + lc.code + '"'
                + ' data-amount="' + lc.amount + '"'
                + (String(selected) === String(lc.id) ? ' selected' : '') + '>'
                + lc.code + (lc.description ? ' — ' + lc.description : '')
                + ' ($' + parseFloat(lc.amount).toFixed(2) + ')'
                + '</option>';
        });
        return opts;
    }

    function partOptions(selectedId) {
        var opts = '<option value="">-- select part --</option>';
        _inventoryParts.forEach(function(p) {
            opts += '<option value="' + p.id + '"'
                + ' data-part-num="' + (p.part_number || '') + '"'
                + ' data-desc="' + (p.part_description || '').replace(/"/g,'&quot;') + '"'
                + ' data-cost="' + (p.total_value || 0) + '"'
                + (String(selectedId) === String(p.id) ? ' selected' : '') + '>'
                + (p.part_number || '') + (p.part_description ? ' — ' + p.part_description : '')
                + '</option>';
        });
        return opts;
    }

    function buildMidCell(item) {
        if ((item.item_type || 'labor') === 'part') {
            return '<td><select class="form-select form-select-sm inv-part-select">'
                + partOptions(item.inventory_id || '') + '</select></td>';
        }
        return '<td><select class="form-select form-select-sm inv-labor-code">'
            + laborCodeOptions(item.labor_code_id || '') + '</select></td>';
    }

    function addInvoiceRow(item) {
        item = item || {};
        var typeOpts = ['labor','travel','part'].map(function(t) {
            return '<option value="' + t + '"' + ((item.item_type || 'labor') === t ? ' selected' : '') + '>'
                + t.charAt(0).toUpperCase() + t.slice(1) + '</option>';
        }).join('');

        var row = $('<tr>').html(
            '<td><select class="form-select form-select-sm inv-type">' + typeOpts + '</select></td>'
            + '<td><input class="form-control form-control-sm inv-part-num"'
                + ' value="' + (item.part_number || '') + '" placeholder="Part # / Code"></td>'
            + buildMidCell(item)
            + '<td><input class="form-control form-control-sm inv-desc"'
                + ' value="' + (item.description || '') + '" placeholder="Description"></td>'
            + '<td><input type="number" class="form-control form-control-sm inv-qty"'
                + ' value="' + (item.qty || 1) + '" min="0" step="0.5"></td>'
            + '<td><input type="number" class="form-control form-control-sm inv-unit-cost"'
                + ' value="' + (item.unit_cost || '') + '" min="0" step="0.01" placeholder="0.00"></td>'
            + '<td><input type="number" class="form-control form-control-sm inv-total-cost"'
                + ' value="' + (item.total_cost || '') + '" readonly style="background:#f8f9fa"></td>'
            + '<td class="text-center"><button class="btn btn-sm btn-outline-danger btn-remove-inv-row">'
                + '<i class="fas fa-times"></i></button></td>'
        );
        $('#woItemsTbody').append(row);
        recalcRow(row);
    }

    // Type changed: swap middle cell between labor-code and part-select
    $(document).on('change', '.inv-type', function() {
        var $row = $(this).closest('tr');
        var type = $(this).val();
        var $mid = $row.find('td').eq(2);
        if (type === 'part') {
            $mid.html('<select class="form-select form-select-sm inv-part-select">' + partOptions('') + '</select>');
        } else {
            $mid.html('<select class="form-select form-select-sm inv-labor-code">' + laborCodeOptions('') + '</select>');
        }
        $row.find('.inv-part-num, .inv-desc, .inv-unit-cost, .inv-total-cost').val('');
        recalcRow($row);
    });

    // Part selected from inventory: auto-fill part#, description, unit cost
    $(document).on('change', '.inv-part-select', function() {
        var $opt = $(this).find(':selected');
        var $row = $(this).closest('tr');
        $row.find('.inv-part-num').val($opt.data('part-num') || '');
        $row.find('.inv-desc').val($opt.data('desc') || '');
        $row.find('.inv-unit-cost').val(parseFloat($opt.data('cost') || 0).toFixed(2));
        recalcRow($row);
    });

    // Labor code selected: auto-fill code and rate
    $(document).on('change', '.inv-labor-code', function() {
        var $opt = $(this).find(':selected');
        var $row = $(this).closest('tr');
        if ($opt.data('amount') !== undefined && $opt.data('amount') !== '') {
            $row.find('.inv-unit-cost').val(parseFloat($opt.data('amount')).toFixed(2));
        }
        if ($opt.data('code')) $row.find('.inv-part-num').val($opt.data('code'));
        recalcRow($row);
    });

    // ── Recalc row total on qty/unit change ───────────────────
    $(document).on('input', '.inv-qty, .inv-unit-cost', function () {
        recalcRow($(this).closest('tr'));
    });

    function recalcRow($row) {
        var qty  = parseFloat($row.find('.inv-qty').val())       || 0;
        var unit = parseFloat($row.find('.inv-unit-cost').val()) || 0;
        $row.find('.inv-total-cost').val((qty * unit).toFixed(2));
        recalcTotal();
    }

    function recalcTotal() {
        var total = 0;
        $('#woItemsTbody .inv-total-cost').each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        $('#woGrandTotal').text('$' + total.toFixed(2));
    }

    // ── Remove row ────────────────────────────────────────────
    $(document).on('click', '.btn-remove-inv-row', function () {
        $(this).closest('tr').remove();
        recalcTotal();
    });

    // ── Save invoice ──────────────────────────────────────────
    $('#woBtnSave').on('click', function () {
        if (!_currentWoId) return;
        var $btn = $(this).prop('disabled', true).text('Saving…');

        // Collect items
        var items = [];
        $('#woItemsTbody tr').each(function () {
            var $r = $(this);
            var $lcSel = $r.find('.inv-labor-code');
            items.push({
                item_type:      $r.find('.inv-type').val(),
                part_number:    $r.find('.inv-part-num').val(),
                labor_code_id:  $lcSel.val() || null,
                part_labor_code: $lcSel.find(':selected').data('code') || '',
                description:    $r.find('.inv-desc').val(),
                qty:            parseFloat($r.find('.inv-qty').val())       || 1,
                unit_cost:      parseFloat($r.find('.inv-unit-cost').val()) || 0,
                total_cost:     parseFloat($r.find('.inv-total-cost').val())|| 0,
            });
        });

        $.ajax({
            url: '<?= site_url('admin/work-orders') ?>/' + _currentWoId + '/invoice/save',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                problem_notes:      $('#woProblemNotes').val(),
                invoice_note:       $('#woInvoiceNote').val(),
                service_notes:      $('#woServiceNotes').val(),
                customer_sig_name:  $('#woCustName').val(),
                customer_sig_image: getSigDataUrl(window._woCustCanvas),
                tech_sig_name:      $('#woTechName').val(),
                tech_sig_image:     getSigDataUrl(window._woTechCanvas),
                items: items,
                '<?= csrf_token() ?>': $('input[name="<?= csrf_token() ?>"]').first().val(),
            }),
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    Swal.fire('Saved', res.message, 'success');
                } else {
                    Swal.fire('Error', res.message || 'Save failed', 'error');
                }
            },
            error: function () { Swal.fire('Error', 'Save failed', 'error'); },
            complete: function () { $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Invoice'); }
        });
    });
});

// ── Status filter ─────────────────────────────────────────────
// =====================================================================
// SIGNATURE PAD HELPERS
// =====================================================================
window._woCustCanvas = null;
window._woTechCanvas = null;
window._woCustCtx    = null;
window._woTechCtx    = null;

function initSigPad(canvasId) {
    var canvas = document.getElementById(canvasId);
    if (!canvas) return null;
    var ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width  = canvas.offsetWidth  * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    var ctx = canvas.getContext('2d');
    ctx.scale(ratio, ratio);
    var drawing = false;
    function getPos(e) {
        var r = canvas.getBoundingClientRect();
        if (e.touches && e.touches.length) {
            return { x: e.touches[0].clientX - r.left, y: e.touches[0].clientY - r.top };
        }
        return { x: e.clientX - r.left, y: e.clientY - r.top };
    }
    canvas.addEventListener('mousedown',  function(e) { drawing=true; var p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); });
    canvas.addEventListener('mousemove',  function(e) { if(!drawing) return; var p=getPos(e); ctx.lineWidth=2; ctx.lineCap='round'; ctx.strokeStyle='#000'; ctx.lineTo(p.x,p.y); ctx.stroke(); });
    canvas.addEventListener('mouseup',    function()  { drawing=false; ctx.beginPath(); });
    canvas.addEventListener('mouseleave', function()  { drawing=false; });
    canvas.addEventListener('touchstart', function(e) { e.preventDefault(); drawing=true; var p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); }, {passive:false});
    canvas.addEventListener('touchmove',  function(e) { e.preventDefault(); if(!drawing) return; var p=getPos(e); ctx.lineWidth=2; ctx.lineCap='round'; ctx.strokeStyle='#000'; ctx.lineTo(p.x,p.y); ctx.stroke(); }, {passive:false});
    canvas.addEventListener('touchend',   function()  { drawing=false; ctx.beginPath(); });
    return ctx;
}

function clearSig(ctx) {
    if (!ctx) return;
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
}

function getSigDataUrl(canvas) {
    if (!canvas) return '';
    var blank = document.createElement('canvas');
    blank.width  = canvas.width;
    blank.height = canvas.height;
    if (canvas.toDataURL() === blank.toDataURL()) return '';
    return canvas.toDataURL('image/png');
}

function restoreSig(ctx, dataUrl) {
    if (!ctx || !dataUrl) return;
    var img = new Image();
    img.onload = function() {
        var r = window.devicePixelRatio || 1;
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        ctx.drawImage(img, 0, 0, ctx.canvas.width / r, ctx.canvas.height / r);
    };
    img.src = dataUrl;
}

// Re-init sig pads every time modal opens (canvas sizing requires visible DOM)
$('#woInvoiceModal').on('shown.bs.modal', function() {
    window._woCustCanvas = document.getElementById('woCustCanvas');
    window._woTechCanvas = document.getElementById('woTechCanvas');
    window._woCustCtx    = initSigPad('woCustCanvas');
    window._woTechCtx    = initSigPad('woTechCanvas');
});

$(document).on('click', '#woClearCust', function() { clearSig(window._woCustCtx); });
$(document).on('click', '#woClearTech', function() { clearSig(window._woTechCtx); });


</script>
<?= $this->endSection() ?>