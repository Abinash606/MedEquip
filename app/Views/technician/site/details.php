<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<style>
    /* ======================== DARK THEME OVERRIDES ======================== */
    /* Modal dark styling */
    .modal-content {
        background: #0E1630 !important;
        border: 1px solid rgba(255, 255, 255, .12) !important;
        color: #E9EDFF !important;
        border-radius: 16px !important;
    }

    .modal-header {
        background: linear-gradient(135deg, rgba(124, 58, 237, .9), rgba(34, 211, 238, .8)) !important;
        border-bottom: none !important;
        border-radius: 16px 16px 0 0 !important;
    }

    .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, .08) !important;
        background: rgba(14, 22, 48, .5) !important;
    }

    .modal-body {
        background: transparent !important;
    }

    .modal-title,
    .modal-body label,
    .modal-body h5,
    .modal-body .form-label,
    .modal-body p,
    .modal-body span {
        color: #E9EDFF !important;
    }

    .modal-body .form-control,
    .modal-body .form-select,
    .modal-body .input-group-text {
        background: rgba(255, 255, 255, .06) !important;
        border: 1px solid rgba(255, 255, 255, .14) !important;
        color: #E9EDFF !important;
        border-radius: 10px !important;
    }

    .modal-body .form-control::placeholder {
        color: rgba(233, 237, 255, .4) !important;
    }

    .modal-body .form-select option {
        color: #000 !important;
        background: #fff !important;
    }

    .modal-body .form-control[readonly] {
        background: rgba(255, 255, 255, .03) !important;
        color: rgba(233, 237, 255, .6) !important;
    }

    /* DataTables dark */
    table.dataTable thead th,
    table.dataTable thead td {
        background: rgba(14, 22, 48, .8) !important;
        color: rgba(233, 237, 255, .6) !important;
        border-bottom: 1px solid rgba(255, 255, 255, .08) !important;
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    table.dataTable tbody tr {
        background: transparent !important;
    }

    table.dataTable tbody tr:hover {
        background: rgba(255, 255, 255, .04) !important;
    }

    table.dataTable tbody td {
        border-bottom: 1px solid rgba(255, 255, 255, .06) !important;
        color: #E9EDFF !important;
    }

    table.dataTable.stripe tbody tr.odd,
    table.dataTable.stripe tbody tr.even {
        background: transparent !important;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        background: rgba(255, 255, 255, .06) !important;
        border: 1px solid rgba(255, 255, 255, .12) !important;
        color: #E9EDFF !important;
        border-radius: 8px !important;
        padding: 4px 8px;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        color: rgba(233, 237, 255, .7) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #E9EDFF !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, rgba(124, 58, 237, .8), rgba(34, 211, 238, .6)) !important;
        border-color: transparent !important;
        color: #fff !important;
    }

    .dt-buttons .dt-button,
    .export-btn {
        background: linear-gradient(135deg, rgba(124, 58, 237, 1), rgba(34, 211, 238, 1)) !important;
        color: #fff !important;
        border: none !important;
        border-radius: 10px !important;
        padding: 6px 14px !important;
    }

    /* Nav tabs dark */
    .nav-tabs {
        border-bottom: 1px solid rgba(255, 255, 255, .10) !important;
    }

    .nav-tabs .nav-link {
        color: rgba(233, 237, 255, .6) !important;
        border: none !important;
    }

    .nav-tabs .nav-link.active {
        background: rgba(124, 58, 237, .2) !important;
        color: #E9EDFF !important;
        border-bottom: 2px solid rgba(124, 58, 237, .9) !important;
        border-radius: 8px 8px 0 0 !important;
    }

    .nav-tabs .nav-link:hover {
        color: #E9EDFF !important;
        background: rgba(255, 255, 255, .05) !important;
    }

    /* Badges */
    .badge {
        border-radius: 999px !important;
        font-weight: 600 !important;
        font-size: 11px !important;
    }

    .badge.bg-success {
        background: rgba(34, 197, 94, .18) !important;
        color: #4ade80 !important;
        border: 1px solid rgba(34, 197, 94, .3) !important;
    }

    .badge.bg-danger {
        background: rgba(239, 68, 68, .18) !important;
        color: #f87171 !important;
        border: 1px solid rgba(239, 68, 68, .3) !important;
    }

    .badge.bg-warning {
        background: rgba(245, 158, 11, .18) !important;
        color: #fbbf24 !important;
        border: 1px solid rgba(245, 158, 11, .3) !important;
    }

    .badge.bg-info {
        background: rgba(96, 165, 250, .18) !important;
        color: #93c5fd !important;
        border: 1px solid rgba(96, 165, 250, .3) !important;
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(90deg, rgba(34, 211, 238, .9), rgba(124, 58, 237, .8)) !important;
        border: none !important;
    }

    .btn-danger {
        background: rgba(239, 68, 68, .85) !important;
        border: none !important;
    }

    .btn-outline-secondary {
        color: rgba(233, 237, 255, .7) !important;
        border-color: rgba(255, 255, 255, .2) !important;
    }

    .btn-outline-primary {
        color: rgba(34, 211, 238, .9) !important;
        border-color: rgba(34, 211, 238, .4) !important;
    }

    .btn-outline-success {
        color: rgba(34, 197, 94, .9) !important;
        border-color: rgba(34, 197, 94, .4) !important;
    }

    .btn-outline-danger {
        color: rgba(239, 68, 68, .9) !important;
        border-color: rgba(239, 68, 68, .4) !important;
    }

    .btn-light {
        background: rgba(255, 255, 255, .08) !important;
        border-color: rgba(255, 255, 255, .12) !important;
        color: #E9EDFF !important;
    }

    /* Form controls outside modals */
    .glass-card .form-control,
    .glass-card .form-select,
    .glass-card .input-group-text {
        background: rgba(255, 255, 255, .06) !important;
        border: 1px solid rgba(255, 255, 255, .12) !important;
        color: #E9EDFF !important;
    }

    .glass-card .form-label {
        color: rgba(233, 237, 255, .8) !important;
    }

    .glass-card .form-select option {
        color: #000 !important;
    }

    /* Text */
    .text-muted {
        color: rgba(233, 237, 255, .5) !important;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .fw-bold {
        color: #E9EDFF !important;
    }

    p,
    span,
    td,
    th,
    label {
        color: #E9EDFF;
    }

    /* Back button */
    .btn-secondary {
        background: rgba(255, 255, 255, .08) !important;
        border: 1px solid rgba(255, 255, 255, .15) !important;
        color: #E9EDFF !important;
    }

    /* Site info card */
    #siteInfoCard .text-muted,
    #siteInfoCard p,
    #siteInfoCard span {
        color: rgba(233, 237, 255, .7) !important;
    }

    #siteInfoCard strong {
        color: #E9EDFF !important;
    }

    /* Tab content area */
    .tab-content {
        background: transparent !important;
    }

    .card,
    .card-body {
        background: transparent !important;
        border-color: rgba(255, 255, 255, .08) !important;
    }

    /* ======================== END DARK THEME ======================== */
    /* ── base card & layout ── */
    .glass-card {
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, .10);
        background: linear-gradient(180deg, rgba(255, 255, 255, .06), rgba(255, 255, 255, .03));
        backdrop-filter: blur(14px);
        box-shadow: 0 18px 50px rgba(0, 0, 0, .55);
        padding: 1.5rem;
        position: relative;
        color: #E9EDFF;
    }

    /* Required for the workflow partial */
    .d-none-view {
        display: none !important;
    }

    .site-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #f1f5f9;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    .site-avatar-initials {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: 700;
        color: #fff;
    }

    .site-info-item {
        margin-bottom: 12px;
        font-size: 15px;
        color: #334155;
    }

    .site-info-item strong {
        display: inline-block;
        min-width: 130px;
        color: #0f172a;
        font-weight: 700;
    }

    /* ── tabs ── */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 0;
    }

    .nav-tabs .nav-link {
        border: none;
        border-radius: 12px 12px 0 0;
        color: #475569;
        font-weight: 600;
        padding: 12px 24px;
        margin-right: 8px;
    }

    .nav-tabs .nav-link.active {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #e9eef5;
        border-bottom: 1px solid #fff;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.03);
    }

    /* ── status badges ── */
    .status-badge {
        padding: .25rem .75rem;
        border-radius: 12px;
        font-size: .875rem;
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

    /* ── modals ── */
    .modal-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    /* ── wizard ── */
    .wizard-step {
        display: none;
    }

    .wizard-step.active {
        display: block;
    }

    .wizard-step h5 {
        font-weight: 700;
        margin-bottom: .25rem;
        color: #1e293b;
    }

    .wizard-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .step3-readonly-row .form-control {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #475569;
    }

    .asset-not-found-alert {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        border-radius: 6px;
        padding: .5rem .75rem;
        font-size: .875rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }

    /* ── inspection outcome buttons ── */
    .inspection-outcome-btns {
        display: flex;
        justify-content: center;
        gap: .75rem;
        margin-top: 1rem;
    }

    .btn-pass {
        background: #16a34a;
        color: #fff;
        border: none;
        padding: .5rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: .875rem;
        cursor: pointer;
    }

    .btn-pass:hover {
        background: #15803d;
    }

    .btn-fail {
        background: #dc2626;
        color: #fff;
        border: none;
        padding: .5rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: .875rem;
        cursor: pointer;
    }

    .btn-fail:hover {
        background: #b91c1c;
    }

    .btn-repair {
        background: #0ea5e9;
        color: #fff;
        border: none;
        padding: .5rem 1.25rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: .875rem;
        cursor: pointer;
    }

    .btn-repair:hover {
        background: #0284c7;
    }

    /* ── queue ── */
    .queue-item {
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: .75rem;
        margin-bottom: .5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .queue-item-info {
        flex: 1;
    }

    .queue-item-model {
        font-weight: 600;
        color: #1e293b;
        font-size: .9rem;
    }

    .queue-item-details {
        font-size: .82rem;
        color: #64748b;
        margin-top: .25rem;
    }

    .queue-item-remove {
        background: #ef4444;
        color: #fff;
        border: none;
        padding: .25rem .5rem;
        border-radius: 4px;
        font-size: .75rem;
        cursor: pointer;
    }

    .queue-item-remove:hover {
        background: #dc2626;
    }

    /* ── view inspection modal ── */
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

    /* ── workflow inner UI ── */
    #site-inspection-workflow {
        --primary-color: #2563eb;
        --secondary-color: #64748b;
        --success-color: #10b981;
    }

    #site-inspection-workflow .nav-link {
        color: var(--secondary-color);
        font-weight: 500;
        border: none;
        padding: 12px 14px;
        font-size: 14px;
        border-bottom: 2px solid transparent;
        transition: all .2s;
    }

    #site-inspection-workflow .nav-link:hover {
        color: var(--primary-color);
    }

    #site-inspection-workflow .nav-link.active {
        color: #fff !important;
        border-bottom: 2px solid var(--primary-color) !important;
        background: linear-gradient(135deg, rgba(124, 58, 237, 1), rgba(34, 211, 238, 1)) !important;
    }

    #site-inspection-workflow .table-custom th {
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        font-size: .75rem;
        letter-spacing: .05em;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 8px;
    }

    #site-inspection-workflow .table-custom td {
        vertical-align: middle;
        font-size: .9rem;
        padding: 12px 8px;
        border-bottom: 1px solid #f1f5f9;
    }

    #site-inspection-workflow .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all .2s;
    }

    #site-inspection-workflow .export-btn {
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: .85rem;
        color: var(--secondary-color);
        font-weight: 500;
    }

    #site-inspection-workflow .export-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .action-btns {
        display: flex;
        gap: 4px;
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

    .fade-in {
        animation: fadeIn .4s ease-in-out;
    }

    /* ===== FIX: Make ONLY text white in Site Info ===== */
    #siteInfoCard,
    #siteInfoCard p,
    #siteInfoCard span,
    #siteInfoCard label,
    #siteInfoCard div,
    #siteInfoCard strong,
    #siteInfoCard h6 {
        color: #ffffff !important;
    }

    /* Optional: slightly softer for muted text */
    #siteInfoCard .text-muted {
        color: rgba(255, 255, 255, 0.7) !important;
    }
</style>

<!-- ── Back button ── -->
 <div class="topbar">
<button class="btn btn-secondary mb-3" onclick="window.location.href='<?= site_url('technician/sites') ?>'">
    <i class="fa fa-arrow-left me-2"></i> Back to Sites
</button>
</div>
<div class="content">
<!-- ── Site info card ── -->
<div class="glass-card mb-4" id="siteInfoCard">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 style="font-size:1rem;font-weight:600;color:#475569;margin:0;">
            <i class="fa fa-circle-info me-1"></i> Site Information
            <?php if (!empty($customer['name'])): ?> — <?= esc($customer['name']) ?><?php endif; ?>
        </h6>
        <button class="btn btn-sm btn-primary rounded-circle" id="toggleSiteInfo" type="button" title="Toggle">
            <i class="fa fa-chevron-up" id="toggleIcon"></i>
        </button>
    </div>

    <div id="siteInfoBody">
        <div class="row align-items-center">
            <div class="col-md-auto me-4 text-center">
                <?php
                $logoPath     = $customer['logo_path'] ?? '';
                $logoFullPath = FCPATH . 'uploads/logos/' . $logoPath;
                if (!empty($logoPath) && file_exists($logoFullPath)): ?>
                    <img src="<?= base_url('uploads/logos/' . $logoPath) ?>" class="site-avatar" alt="Logo">
                <?php else:
                    $nameParts = explode(' ', $customer['name'] ?? 'Unknown');
                    $initials  = count($nameParts) >= 2
                        ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                        : strtoupper(substr($customer['name'] ?? 'UN', 0, 2));
                ?>
                    <div class="site-avatar-initials"><?= esc($initials) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md">
                <div class="row">
                    <div class="col-md-6">
                        <div class="site-info-item"><strong>Site Name:</strong> <?= esc($site['name'] ?? 'N/A') ?></div>
                        <div class="site-info-item"><strong>Site ID:</strong> <?= esc($site['id'] ?? 'N/A') ?></div>
                        <div class="site-info-item"><strong>Contact:</strong> <?= esc($site['contact_name'] ?? 'N/A') ?>
                        </div>
                        <div class="site-info-item"><strong>Email:</strong> <?= esc($site['email'] ?? 'N/A') ?></div>
                        <div class="site-info-item"><strong>Phone:</strong> <?= esc($site['phone'] ?? 'N/A') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="site-info-item"><strong>Customer:</strong> <?= esc($customer['name'] ?? 'N/A') ?>
                        </div>
                        <div class="site-info-item"><strong>Address:</strong> <?= esc($site['address'] ?? 'N/A') ?>,
                            <?= esc($site['city'] ?? '') ?></div>
                        <div class="site-info-item"><strong>State:</strong> <?= esc($site['state'] ?? 'N/A') ?></div>
                        <div class="site-info-item"><strong>Zip:</strong> <?= esc($site['zip'] ?? 'N/A') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Main tabs ── -->
<ul class="nav nav-tabs" id="site-details-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#tab-equipment"
            type="button">
            <i class="fa fa-desktop me-2"></i>Equipment
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="inspections-tab" data-bs-toggle="tab" data-bs-target="#tab-inspections"
            type="button">
            <i class="fa fa-clipboard-check me-2"></i>Inspections
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="work-orders-tab" data-bs-toggle="tab" data-bs-target="#tab-workorders"
            type="button">
            <i class="fa fa-wrench me-2"></i>Work Orders
        </button>
    </li>
</ul>

<div class="tab-content" id="site-details-tabs-content">

    <!-- ══ EQUIPMENT TAB (unchanged) ══ -->
    <div class="tab-pane fade show active" id="tab-equipment" role="tabpanel">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Equipment List</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                    <i class="fa fa-plus me-2"></i> Add Equipment
                </button>
            </div>
            <div class="table-responsive">
            <table id="equipment-datatable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Asset Tag</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Serial Number</th>
                        <th>Device Type</th>
                        <th>Location / Room</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
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
                                $st  = strtolower($eq['status'] ?? 'pending');
                                $cls = str_contains($st, 'ready') ? 'status-ready' : (str_contains($st, 'need') ? 'status-need-attention' : 'status-pending');
                                ?>
                                <span class="status-badge <?= $cls ?>"><?= esc($eq['status'] ?? 'Pending') ?></span>
                            </td>
                            <td class="text-center">
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-primary edit-equipment-btn" data-id="<?= $eq['id'] ?>"
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
                                <a href="<?= site_url('technician/equipment/delete/' . $eq['id']) ?>"
                                    class="btn btn-sm btn-danger" onclick="return confirm('Delete this equipment?')">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                                </div>
                               
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- ══ INSPECTIONS TAB — full workflow partial ══ -->
    <div class="tab-pane fade" id="tab-inspections" role="tabpanel">
        <?= $this->include('technician/site/site_inspection_workflow') ?>
    </div>

    <!-- ══ WORK ORDERS TAB (unchanged) ══ -->
    <div class="tab-pane fade" id="tab-workorders" role="tabpanel">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Work Orders</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkOrderModal">
                    <i class="fa fa-plus me-2"></i> Add Work Order
                </button>
            </div>
            <div class="table-responsive">
            <table id="work-orders-datatable" class="table table-striped table-hover">
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
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($workOrders as $wo): ?>
                        <tr>
                            <td>WO-<?= esc($wo['id']) ?></td>
                            <td><?= esc($wo['title']) ?></td>
                            <td><?= esc($wo['asset_tag'] ?? 'N/A') ?></td>
                            <td>
                                <?php
                                $ws = strtolower($wo['status'] ?? '');
                                $wc = $ws === 'completed' ? 'status-completed' : ($ws === 'in progress' || $ws === 'in-progress' ? 'status-in-progress' : 'status-open');
                                ?>
                                <span class="status-badge <?= $wc ?>"><?= esc($wo['status']) ?></span>
                            </td>
                            <td><?= esc($wo['priority']) ?></td>
                            <td><?= esc($wo['assigned_to_name'] ?? 'Unassigned') ?></td>
                            <td><?= $wo['start_date'] ? date('M d, Y', strtotime($wo['start_date'])) : '—' ?></td>
                            <td><?= $wo['end_date']   ? date('M d, Y', strtotime($wo['end_date']))   : '—' ?></td>
                            <td class="text-center">
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-primary edit-workorder-btn" data-id="<?= $wo['id'] ?>"
                                    data-equipment_id="<?= $wo['equipment_id'] ?? '' ?>"
                                    data-serial_number="<?= esc($wo['serial_number'] ?? '', 'attr') ?>"
                                    data-title="<?= esc($wo['title'], 'attr') ?>"
                                    data-description="<?= esc($wo['description'] ?? '', 'attr') ?>"
                                    data-status="<?= esc($wo['status'], 'attr') ?>"
                                    data-priority="<?= esc($wo['priority'], 'attr') ?>"
                                    data-assigned_to="<?= $wo['assigned_to'] ?? '' ?>"
                                    data-start_date="<?= $wo['start_date'] ?? '' ?>"
                                    data-end_date="<?= $wo['end_date'] ?? '' ?>">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                                <a href="<?= site_url('technician/work-orders/delete/' . $wo['id']) ?>"
                                    class="btn btn-sm btn-danger" onclick="return confirm('Delete this work order?')">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                                </div>
                               
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
</div><!-- /tab-content -->


<!-- ════════════════════════════════════════════════════
     ADD / EDIT EQUIPMENT MODAL (unchanged)
════════════════════════════════════════════════════ -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="equipmentForm" method="post" action="<?= site_url('technician/equipment/create') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="site_id" value="<?= $site['id'] ?>">
                <input type="hidden" id="equipment-id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentModalLabel">Add / Edit Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Asset Tag <span
                                    class="text-danger">*</span></label><input type="text" class="form-control"
                                id="equipment-asset-tag" name="asset_tag" required></div>
                        <div class="col-md-6"><label class="form-label">Serial Number <span
                                    class="text-danger">*</span></label><input type="text" class="form-control"
                                id="equipment-serial-number" name="serial_number" required></div>
                        <div class="col-md-6"><label class="form-label">Make <span
                                    class="text-danger">*</span></label><input type="text" class="form-control"
                                id="equipment-make" name="make" placeholder="e.g. Philips, GE" required></div>
                        <div class="col-md-6"><label class="form-label">Model <span
                                    class="text-danger">*</span></label><input type="text" class="form-control"
                                id="equipment-model" name="model" required></div>
                        <div class="col-md-6"><label class="form-label">Device Type <span
                                    class="text-danger">*</span></label><input type="text" class="form-control"
                                id="equipment-device-type" name="device_type" placeholder="e.g. MRI, CT" required></div>
                        <div class="col-md-6"><label class="form-label">Department</label><input type="text"
                                class="form-control" id="equipment-department" name="department"></div>
                        <div class="col-md-6"><label class="form-label">Room / Location</label><input type="text"
                                class="form-control" id="equipment-location" name="location"></div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="equipment-status" name="status">
                                <option value="Ready">Ready</option>
                                <option value="Need Attention">Need Attention</option>
                                <option value="Repair">Repair</option>
                                <option value="Out of Service">Out of Service</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PM Kit</label>
                            <select class="form-select" id="equipment-pm-kit" name="pm_kit">
                                <option value="">Select PM Kit</option>
                                <option value="Kit A">Kit A</option>
                                <option value="Kit B">Kit B</option>
                                <option value="Kit C">Kit C</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Installation Date</label><input type="date"
                                class="form-control" id="equipment-installation-date" name="installation_date"></div>
                        <div class="col-md-6"><label class="form-label">Warranty Expires</label><input type="date"
                                class="form-control" id="equipment-warranty-expires" name="warranty_expires"></div>
                        <div class="col-md-12"><label class="form-label">Fast Notes</label><textarea
                                class="form-control" id="equipment-fast-notes" name="fast_notes" rows="2"></textarea>
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


<!-- ════════════════════════════════════════════════════
     3-STEP INSPECTION WIZARD MODAL
     (opened by the workflow partial's "Add Inspection" button)
════════════════════════════════════════════════════ -->
<div class="modal fade" id="inspectionWizardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-clipboard-check me-2"></i>New Inspection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">

                <input type="hidden" id="wiz-equipment-id">
                <input type="hidden" id="wiz-site-id" value="<?= $site['id'] ?>">
                <input type="hidden" id="wiz-group-id">

                <!-- Queue display -->
                <div id="inspectionQueueContainer" style="display:none;">
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-weight:600;"><i class="fa fa-list me-2"></i><span id="queueCount">0</span>
                                device(s) in queue</span>
                        </div>
                        <div id="inspectionQueue" style="max-height:180px;overflow-y:auto;"></div>
                    </div>
                </div>

                <!-- STEP 1 -->
                <div class="wizard-step active" id="wizardStep1">
                    <h5>Step 1: Enter Serial Number</h5>
                    <p class="text-muted" style="font-size:.875rem;"><strong>Site:</strong> <?= esc($site['name']) ?>
                    </p>
                    <label class="form-label">Serial Number</label>
                    <input type="text" class="form-control" id="wiz-serial-number"
                        placeholder="Enter or scan serial number" autocomplete="off">
                    <p class="text-muted mt-2" style="font-size:.82rem;">If found, details will be auto-filled.</p>
                    <div class="wizard-footer">
                        <div></div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="wizStep1Next">Next</button>
                        </div>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div class="wizard-step" id="wizardStep2">
                    <h5>Step 2: Asset Verification (Not Found)</h5>
                    <div class="asset-not-found-alert"><i class="fa fa-triangle-exclamation me-1"></i>Serial number not
                        found. Search for the device model.</div>
                    <label class="form-label">Search Model</label>
                    <div style="position:relative;">
                        <input type="text" class="form-control mb-3" id="wiz-search-model"
                            placeholder="Search for model..." autocomplete="off">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Manufacturer</label><input type="text"
                                class="form-control" id="wiz-s2-manufacturer"></div>
                        <div class="col-md-6"><label class="form-label">Model</label><input type="text"
                                class="form-control" id="wiz-s2-model"></div>
                        <div class="col-md-6"><label class="form-label">Description</label><input type="text"
                                class="form-control" id="wiz-s2-description"></div>
                        <div class="col-md-6"><label class="form-label">Serial #</label><input type="text"
                                class="form-control" id="wiz-s2-serial" readonly></div>
                    </div>
                    <div class="wizard-footer">
                        <button type="button" class="btn btn-outline-secondary" id="wizStep2Back">Back</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="wizStep2Next">Next</button>
                        </div>
                    </div>
                </div>

                <!-- STEP 3 -->
                <div class="wizard-step" id="wizardStep3">
                    <h5>Step 3: Inspection Details</h5>
                    <div class="row g-3 step3-readonly-row">
                        <div class="col-md-4"><label class="form-label">Model</label><input class="form-control"
                                id="wiz-s3-model" readonly></div>
                        <div class="col-md-4"><label class="form-label">Description</label><input class="form-control"
                                id="wiz-s3-description" readonly></div>
                        <div class="col-md-4"><label class="form-label">Serial #</label><input class="form-control"
                                id="wiz-s3-serial" readonly></div>
                    </div>
                    <div class="row g-3 mt-1 step3-readonly-row">
                        <div class="col-md-4"><label class="form-label">Asset ID</label><input class="form-control"
                                id="wiz-s3-assetid" readonly></div>
                        <div class="col-md-4"><label class="form-label">Department</label><input class="form-control"
                                id="wiz-s3-department" readonly></div>
                        <div class="col-md-4"><label class="form-label">Location / Room</label><input
                                class="form-control" id="wiz-s3-location" readonly></div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">PM Service Frequency</label>
                            <select class="form-select" id="wiz-s3-pmfreq">
                                <option value="">Select frequency</option>
                                <option>Monthly</option>
                                <option>Quarterly</option>
                                <option>Semi-Annual</option>
                                <option>Annual</option>
                                <option>As Needed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Inspection Type</label>
                            <select class="form-select" id="wiz-s3-insptype">
                                <option value="">Select type</option>
                                <option>Preventive Maintenance</option>
                                <option>Corrective Maintenance</option>
                                <option>Safety Inspection</option>
                                <option>Compliance Check</option>
                                <option>Initial Setup</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Technician</label>
                            <select class="form-select" id="wiz-s3-technician">
                                <option value="">-- Select Technician --</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>"><?= esc($tech['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Inspection Date</label><input type="date"
                                class="form-control" id="wiz-s3-inspdate" value="<?= date('Y-m-d') ?>"></div>
                        <div class="col-12"><label class="form-label">Service Notes</label><textarea
                                class="form-control" id="wiz-s3-notes" rows="3"></textarea></div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="wiz-s3-status">
                                <option value="Pass">Pass</option>
                                <option value="Fail">Fail</option>
                                <option value="Repair">Repair</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Device Complete</label>
                            <select class="form-select" id="wiz-s3-devicecomplete">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <p class="text-center text-muted mt-3 mb-1" style="font-size:.85rem;">Select outcome then add to
                        queue or complete.</p>
                    <div class="inspection-outcome-btns">
                        <button type="button" class="btn-pass" id="wizBtnPass">Pass Inspection</button>
                        <button type="button" class="btn-fail" id="wizBtnFail">Fail Inspection</button>
                        <button type="button" class="btn-repair" id="wizBtnRepair">Repair Inspection</button>
                    </div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="wizBtnNextDevice">Add to
                            Queue &amp; Next Device</button>
                    </div>
                    <div class="wizard-footer">
                        <button type="button" class="btn btn-outline-secondary" id="wizStep3Back">Back</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="wizBtnComplete" style="display:none;">
                                <i class="fa fa-check-double me-1"></i>Complete Inspections
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- ════════════════════════════════════════════════════
     VIEW INSPECTION MODAL
════════════════════════════════════════════════════ -->
<div class="modal fade" id="viewInspectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Inspection Group Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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
                                <th>Date</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="inspection-details-body"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- ════════════════════════════════════════════════════
     EDIT INSPECTION WIZARD MODAL
════════════════════════════════════════════════════ -->
<div class="modal fade" id="editInspectionWizardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Inspection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-wiz-inspection-id">
                <input type="hidden" id="edit-wiz-site-id" value="<?= $site['id'] ?>">
                <input type="hidden" id="edit-wiz-group-id">
                <input type="hidden" id="edit-wiz-equipment-id">

                <!-- Step 1 -->
                <div class="wizard-step active" id="edit-wiz-step1">
                    <h5>Step 1: Search Equipment</h5>
                    <div class="row g-3 mt-2">
                        <div class="col-12"><label class="form-label">Serial Number</label><input type="text"
                                class="form-control" id="edit-wiz-s1-serial" placeholder="Enter serial number"
                                autocomplete="off"></div>
                        <div class="col-auto"><button type="button" class="btn btn-primary"
                                id="edit-wiz-s1-search-btn"><i class="fa fa-search me-1"></i>Search</button></div>
                    </div>
                    <div id="edit-wiz-asset-not-found-alert" class="asset-not-found-alert mt-2" style="display:none;"><i
                            class="fa fa-triangle-exclamation me-1"></i>Asset not found — enter details manually in Step
                        2.</div>
                    <div class="wizard-footer">
                        <div></div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="edit-wiz-step1-next">Next</button>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="wizard-step" id="edit-wiz-step2">
                    <h5>Step 2: Equipment Details</h5>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><label class="form-label">Manufacturer</label><input type="text"
                                class="form-control" id="edit-wiz-s2-manufacturer"></div>
                        <div class="col-md-6"><label class="form-label">Model</label><input type="text"
                                class="form-control" id="edit-wiz-s2-model"></div>
                        <div class="col-md-6"><label class="form-label">Description</label><input type="text"
                                class="form-control" id="edit-wiz-s2-description"></div>
                        <div class="col-md-6"><label class="form-label">Serial #</label><input type="text"
                                class="form-control" id="edit-wiz-s2-serial"></div>
                        <div class="col-md-4"><label class="form-label">Asset ID</label><input type="text"
                                class="form-control" id="edit-wiz-s2-assetid"></div>
                        <div class="col-md-4"><label class="form-label">Department</label><input type="text"
                                class="form-control" id="edit-wiz-s2-department"></div>
                        <div class="col-md-4"><label class="form-label">Location / Room</label><input type="text"
                                class="form-control" id="edit-wiz-s2-location"></div>
                    </div>
                    <div class="wizard-footer">
                        <button type="button" class="btn btn-outline-secondary" id="edit-wiz-step2-back">Back</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="edit-wiz-step2-next">Next</button>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="wizard-step" id="edit-wiz-step3">
                    <h5>Step 3: Inspection Details</h5>
                    <div class="row g-3 mt-1 step3-readonly-row">
                        <div class="col-md-4"><label class="form-label">Model</label><input class="form-control"
                                id="edit-wiz-s3-model-ro" readonly></div>
                        <div class="col-md-4"><label class="form-label">Description</label><input class="form-control"
                                id="edit-wiz-s3-desc-ro" readonly></div>
                        <div class="col-md-4"><label class="form-label">Serial #</label><input class="form-control"
                                id="edit-wiz-s3-serial-ro" readonly></div>
                    </div>
                    <div class="row g-3 mt-1 step3-readonly-row">
                        <div class="col-md-4"><label class="form-label">Asset ID</label><input class="form-control"
                                id="edit-wiz-s3-assetid-ro" readonly></div>
                        <div class="col-md-4"><label class="form-label">Department</label><input class="form-control"
                                id="edit-wiz-s3-dept-ro" readonly></div>
                        <div class="col-md-4"><label class="form-label">Location / Room</label><input
                                class="form-control" id="edit-wiz-s3-location-ro" readonly></div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">PM Service Frequency</label>
                            <select class="form-select" id="edit-wiz-s3-pmfreq">
                                <option value="">Select frequency</option>
                                <option>Monthly</option>
                                <option>Quarterly</option>
                                <option>Semi-Annual</option>
                                <option>Annual</option>
                                <option>As Needed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Inspection Type</label>
                            <select class="form-select" id="edit-wiz-s3-insptype">
                                <option value="">Select type</option>
                                <option>Preventive Maintenance</option>
                                <option>Corrective Maintenance</option>
                                <option>Safety Inspection</option>
                                <option>Compliance Check</option>
                                <option>Initial Setup</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Technician</label>
                            <select class="form-select" id="edit-wiz-s3-technician">
                                <option value="">-- Select Technician --</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>"><?= esc($tech['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Inspection Date</label><input type="date"
                                class="form-control" id="edit-wiz-s3-inspdate"></div>
                        <div class="col-12"><label class="form-label">Service Notes</label><textarea
                                class="form-control" id="edit-wiz-s3-notes" rows="3"></textarea></div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="edit-wiz-s3-status">
                                <option value="Pass">Pass</option>
                                <option value="Fail">Fail</option>
                                <option value="Repair">Repair</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Device Complete</label>
                            <select class="form-select" id="edit-wiz-s3-devicecomplete">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="inspection-outcome-btns">
                        <button type="button" class="btn-pass" id="edit-wizBtnPass">Pass</button>
                        <button type="button" class="btn-fail" id="edit-wizBtnFail">Fail</button>
                        <button type="button" class="btn-repair" id="edit-wizBtnRepair">Repair</button>
                    </div>
                    <div class="wizard-footer">
                        <button type="button" class="btn btn-outline-secondary" id="edit-wiz-step3-back">Back</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" id="edit-wizBtnUpdate">
                                <i class="fa fa-save me-1"></i>Update Inspection
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ════════════════════════════════════════════════════
     ADD / EDIT WORK ORDER MODAL (unchanged)
════════════════════════════════════════════════════ -->
<div class="modal fade" id="addWorkOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="workOrderForm" method="post" action="<?= site_url('technician/work-orders/create') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="site_id" value="<?= $site['id'] ?>">
                <input type="hidden" id="workorder-id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="workOrderModalLabel">Add Work Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label">Title <span
                                    class="text-danger">*</span></label><input type="text" class="form-control"
                                id="workorder-title" name="title" required></div>
                        <div class="col-md-6">
                            <label class="form-label">Equipment</label>
                            <select class="form-select" id="workorder-equipment" name="equipment_id">
                                <option value="">-- Select Equipment --</option>
                                <?php foreach ($equipment as $eq): ?>
                                    <option value="<?= $eq['id'] ?>"><?= esc($eq['asset_tag']) ?> — <?= esc($eq['make']) ?>
                                        <?= esc($eq['model']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
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
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="workorder-priority" name="priority" required>
                                <option value="">-- Select Priority --</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assigned Technician</label>
                            <select class="form-select" id="workorder-assigned-to" name="assigned_to">
                                <option value="">-- Select --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>"><?= esc($user['full_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Start Date</label><input type="date"
                                class="form-control" id="workorder-start-date" name="start_date"></div>
                        <div class="col-md-6"><label class="form-label">End Date</label><input type="date"
                                class="form-control" id="workorder-end-date" name="end_date"></div>
                        <div class="col-12"><label class="form-label">Description</label><textarea class="form-control"
                                id="workorder-description" name="description" rows="4"></textarea></div>
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


<!-- ════════════════════════════════════════════════════
     JAVASCRIPT — all URLs use technician prefix
════════════════════════════════════════════════════ -->
<script>
    $(document).ready(function() {

        const SITE_ID = <?= (int)$site['id'] ?>;
        const BASE_TEC = '<?= site_url('technician') ?>';
        // All inspection AJAX calls go to the new technician controller
        const URL_SEARCH_SERIAL = BASE_TEC + '/inspections/searchBySerial';
        const URL_SEARCH_MODEL = BASE_TEC + '/inspections/searchByModel';
        const URL_GET_BY_GROUP = BASE_TEC + '/inspections/getByGroupId';
        const URL_GET_INSP_BY_ID = BASE_TEC + '/inspections/getInspectionById';
        const URL_UPDATE_INSP = BASE_TEC + '/inspections/updateInspection';
        const URL_CREATE_INSP = BASE_TEC + '/inspections/create';

        // ── Toggle site info ─────────────────────────────────────────────
        $('#toggleSiteInfo').on('click', function() {
            $('#siteInfoBody').slideToggle(250, function() {
                const visible = $('#siteInfoBody').is(':visible');
                $('#toggleIcon')
                    .toggleClass('fa-chevron-up', visible)
                    .toggleClass('fa-chevron-down', !visible);
            });
        });

        // ── Restore saved tab ────────────────────────────────────────────
        const savedTab = sessionStorage.getItem('siteDetailsActiveTab');
        if (savedTab) {
            sessionStorage.removeItem('siteDetailsActiveTab');
            const tabEl = document.getElementById(savedTab);
            if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();
        }

        // ── DataTables ───────────────────────────────────────────────────
        function getTodayDate() {
            const d = new Date();
            return String(d.getDate()).padStart(2, '0') + String(d.getMonth() + 1).padStart(2, '0') + d
                .getFullYear();
        }

        function pdfCustomize(doc) {
            doc.defaultStyle.fontSize = 7;
            doc.styles.tableHeader = {
                bold: true,
                fontSize: 8,
                color: 'black',
                fillColor: '#a4d169',
                alignment: 'center'
            };
            doc.pageMargins = [10, 10, 10, 10];
            const table = doc.content[1].table;
            table.widths = Array(table.body[0].length).fill('*');
            doc.styles.tableBodyEven = {
                fillColor: '#f2f2f2'
            };
            doc.styles.tableBodyOdd = {
                fillColor: '#ffffff'
            };
            const layout = {};
            layout.hLineWidth = () => 0.5;
            layout.vLineWidth = () => 0.5;
            layout.hLineColor = () => '#aaaaaa';
            layout.vLineColor = () => '#aaaaaa';
            doc.content[1].layout = layout;
        }
        const dtBtns = (fn) => [{
                extend: 'copy',
                filename: fn
            }, {
                extend: 'csv',
                filename: fn
            }, {
                extend: 'excel',
                filename: fn
            },
            {
                extend: 'pdfHtml5',
                filename: fn + '_' + getTodayDate(),
                title: fn,
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                customize: pdfCustomize
            }
        ];

        $('#equipment-datatable').DataTable({
            dom: 'Bfrtip',
            responsive: true,
            pageLength: 10,
            order: [
                [0, 'asc']
            ],
            buttons: dtBtns('Equipment')
        });
        $('#work-orders-datatable').DataTable({
            dom: 'Bfrtip',
            responsive: true,
            pageLength: 10,
            order: [
                [6, 'desc']
            ],
            buttons: dtBtns('WorkOrders')
        });

        // ── Equipment — Edit ─────────────────────────────────────────────
        $(document).on('click', '.edit-equipment-btn', function() {
            const id = $(this).data('id');
            $('#equipmentModalLabel').text('Edit Equipment');
            $('#equipmentForm').attr('action', BASE_TEC + '/equipment/update/' + id);
            $.ajax({
                url: BASE_TEC + '/equipment/show/' + id,
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        const d = res.data;
                        $('#equipment-id').val(d.id);
                        $('#equipment-asset-tag').val(d.asset_tag);
                        $('#equipment-serial-number').val(d.serial_number);
                        $('#equipment-make').val(d.make);
                        $('#equipment-model').val(d.model);
                        $('#equipment-device-type').val(d.device_type);
                        $('#equipment-department').val(d.department);
                        $('#equipment-location').val(d.location);
                        $('#equipment-status').val(d.status);
                        $('#equipment-pm-kit').val(d.pm_kit);
                        $('#equipment-fast-notes').val(d.fast_notes);
                        $('#equipment-installation-date').val(d.installation_date);
                        $('#equipment-warranty-expires').val(d.warranty_expires);
                        $('#addEquipmentModal').modal('show');
                    }
                },
                error: function() {
                    alert('Failed to load equipment data.');
                }
            });
        });

        $('#addEquipmentModal').on('hidden.bs.modal', function() {
            $('#equipmentForm')[0].reset();
            $('#equipment-id').val('');
            $('#equipmentModalLabel').text('Add / Edit Equipment');
            $('#equipmentForm').attr('action', BASE_TEC + '/equipment/create');
            $('#equipmentSubmitBtn').prop('disabled', false).html('Save Equipment');
        });

        $('#equipmentForm').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $('#equipmentSubmitBtn');

            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Saving…');

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    $btn.prop('disabled', false).html('Save Equipment');

                    if (response && response.success === false) {
                        // ── Duplicate or validation error ──────────────────
                        Swal.fire({
                            icon: 'warning',
                            title: 'Asset Tag Already Exists',
                            html: '<p style="color:#64748b;font-size:.95rem;margin:0 0 8px;">' +
                                response.message +
                                '</p>' +
                                '<p style="color:#94a3b8;font-size:.82rem;margin:0;">' +
                                'Each device must have a unique Asset Tag within your company.' +
                                '</p>',
                            confirmButtonColor: '#7c3aed',
                            confirmButtonText: "Got it — I'll update the tag",
                            customClass: {
                                popup: 'swal2-popup',
                                title: 'swal2-title',
                                confirmButton: 'swal2-confirm'
                            }
                        });
                        return;
                    }

                    // ── Success ────────────────────────────────────────────
                    Swal.fire({
                        icon: 'success',
                        title: 'Equipment Saved!',
                        html: '<p style="color:#64748b;font-size:.95rem;margin:0;">The equipment record has been saved successfully.</p>',
                        confirmButtonColor: '#7c3aed',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(function() {
                        $('#addEquipmentModal').modal('hide');
                        location.reload();
                    });
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('Save Equipment');

                    // Try to parse a JSON error message from the server
                    var msg = 'An unexpected error occurred. Please try again.';
                    try {
                        var parsed = JSON.parse(xhr.responseText);
                        if (parsed && parsed.message) msg = parsed.message;
                    } catch (err) {}

                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        html: '<p style="color:#64748b;font-size:.95rem;margin:0 0 8px;">' +
                            msg +
                            '</p>' +
                            '<p style="color:#94a3b8;font-size:.82rem;margin:0;">' +
                            'If the problem persists, please contact your administrator.' +
                            '</p>',
                        confirmButtonColor: '#7c3aed',
                        confirmButtonText: 'Close'
                    });
                }
            });
        });

        // ════════════════════════════════════════════════════
        // INSPECTION WIZARD — 3-step new inspection
        // ════════════════════════════════════════════════════
        let wizardAssetFound = false;
        let wizardMatchedEquip = null;
        let inspectionQueue = [];
        let wizGroupId = '';

        function generateGroupId() {
            return 'INSP-' + new Date().toISOString().split('T')[0].replace(/-/g, '') + '-' + Math.random()
                .toString(36).substr(2, 9).toUpperCase();
        }

        function showStep(n) {
            $('#wizardStep1,#wizardStep2,#wizardStep3').removeClass('active');
            $('#wizardStep' + n).addClass('active');
            $('#wizBtnComplete').toggle(n === 3 && inspectionQueue.length > 0);
        }

        function resetWizard() {
            wizardAssetFound = false;
            wizardMatchedEquip = null;
            $('#wiz-serial-number,#wiz-search-model,#wiz-s2-manufacturer,#wiz-s2-model,#wiz-s2-description,#wiz-s2-serial')
                .val('');
            $('#wiz-s3-model,#wiz-s3-description,#wiz-s3-serial,#wiz-s3-assetid,#wiz-s3-department,#wiz-s3-location')
                .val('');
            $('#wiz-equipment-id,#wiz-s3-pmfreq,#wiz-s3-insptype,#wiz-s3-technician,#wiz-s3-notes').val('');
            $('#wiz-s3-inspdate').val('<?= date('Y-m-d') ?>');
            $('#wiz-s3-status').val('Pass');
            $('#wiz-s3-devicecomplete').val('Yes');
            showStep(1);
        }

        function updateQueueDisplay() {
            const $q = $('#inspectionQueue').empty();
            if (!inspectionQueue.length) {
                $('#inspectionQueueContainer').hide();
                $('#wizBtnComplete').hide();
                return;
            }
            $('#inspectionQueueContainer').show();
            $('#queueCount').text(inspectionQueue.length);
            inspectionQueue.forEach(function(item, idx) {
                $q.append(
                    '<div class="queue-item"><div class="queue-item-info"><div class="queue-item-model">' +
                    (item.make || '') + ' ' + (item.model || '') +
                    '</div><div class="queue-item-details">S/N: ' + (item.serial_number || 'N/A') +
                    ' | ' + item.status + '</div></div><button class="queue-item-remove" data-index="' +
                    idx + '">Remove</button></div>');
            });
            if (inspectionQueue.length > 0) $('#wizBtnComplete').show();
        }

        $(document).on('click', '.queue-item-remove', function() {
            inspectionQueue.splice($(this).data('index'), 1);
            updateQueueDisplay();
        });

        // Open wizard
        // The workflow partial calls startInspection() which is defined inside the partial's script.
        // The wizard modal is kept here and opened by the "New Inspection" button in details.php
        // via a separate openInspectionWizardBtn if needed, but the partial's startInspection()
        // switches to the detail view; the wizard modal is for batch entry.
        // The partial calls startInspection() for the Pass/Fail workflow.
        // For batch wizard (New Inspection button in dashboard list), wire it here:
        $(document).on('click', '#openInspectionWizardBtn', function() {
            inspectionQueue = [];
            wizGroupId = generateGroupId();
            $('#wiz-group-id').val(wizGroupId);
            resetWizard();
            updateQueueDisplay();
            $('#inspectionWizardModal').modal('show');
        });

        $('#inspectionWizardModal').on('hidden.bs.modal', function() {
            inspectionQueue = [];
            resetWizard();
            updateQueueDisplay();
        });

        // Step 1 → Next
        $('#wizStep1Next').on('click', function() {
            const serial = $.trim($('#wiz-serial-number').val());
            if (!serial) {
                alert('Please enter a serial number.');
                return;
            }
            $(this).prop('disabled', true).text('Searching…');
            $.ajax({
                url: URL_SEARCH_SERIAL,
                method: 'GET',
                data: {
                    serial_number: serial,
                    site_id: SITE_ID
                },
                success: function(res) {
                    $('#wizStep1Next').prop('disabled', false).text('Next');
                    if (res.found) {
                        wizardAssetFound = true;
                        wizardMatchedEquip = res;
                        $('#wiz-equipment-id').val(res.id);
                        $('#wiz-s3-model').val(res.model);
                        $('#wiz-s3-description').val(res.device_type);
                        $('#wiz-s3-serial').val(res.serial_number);
                        $('#wiz-s3-assetid').val(res.asset_tag);
                        $('#wiz-s3-department').val(res.department);
                        $('#wiz-s3-location').val(res.location);
                        showStep(3);
                    } else {
                        wizardAssetFound = false;
                        wizardMatchedEquip = null;
                        $('#wiz-s2-serial').val(serial);
                        showStep(2);
                    }
                },
                error: function() {
                    $('#wizStep1Next').prop('disabled', false).text('Next');
                    alert('Search failed.');
                }
            });
        });

        $('#wizStep2Back').on('click', function() {
            showStep(1);
        });

        // Model search autocomplete
        let modelTimer = null;
        $('#wiz-search-model').on('keyup', function() {
            const val = $.trim($(this).val());
            $('#wiz-model-dropdown').remove();
            if (val.length < 2) return;
            clearTimeout(modelTimer);
            modelTimer = setTimeout(function() {
                $.get(URL_SEARCH_MODEL, {
                    keyword: val
                }, function(results) {
                    $('#wiz-model-dropdown').remove();
                    if (!results || !results.length) return;
                    const $wrap = $('#wiz-search-model').parent();
                    $wrap.css('position', 'relative');
                    let html =
                        '<div id="wiz-model-dropdown" style="position:absolute;top:100%;left:0;right:0;z-index:9999;background:#fff;border:1px solid #cbd5e1;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;margin-top:2px;">';
                    results.forEach(function(item) {
                        const label = $.trim((item.make || '') + ' ' + (item
                            .model || '')) || item.device_type || 'Unknown';
                        html += '<div class="wiz-model-option" data-make="' + (item
                                .make || '') + '" data-model="' + (item.model ||
                                '') + '" data-serial_number="' + (item
                                .serial_number ||
                                '') + '" data-device_type="' + (item.device_type ||
                                '') +
                            '" style="padding:.55rem .75rem;cursor:pointer;border-bottom:1px solid #f1f5f9;" onmouseover="$(this).css(\'background\',\'#eef2ff\')" onmouseout="$(this).css(\'background\',\'#fff\')">';
                        html += '<strong>' + label + '</strong>';
                        if (item.serial_number) html +=
                            ' <span style="color:#64748b;font-size:.82rem;">S/N: ' +
                            item.serial_number + '</span>';
                        html += '</div>';
                    });
                    html += '</div>';
                    $('#wiz-search-model').after(html);
                });
            }, 350);
        });
        $(document).on('click', '.wiz-model-option', function() {
            $('#wiz-s2-manufacturer').val($(this).data('make'));
            $('#wiz-s2-model').val($(this).data('model'));
            $('#wiz-s2-description').val($(this).data('device_type'));
            $('#wiz-search-model').val($.trim(($(this).data('make') || '') + ' ' + ($(this).data('model') ||
                '')));
            $('#wiz-model-dropdown').remove();
        });
        $(document).on('click', function(e) {
            if (!$(e.target).is('#wiz-search-model') && !$(e.target).closest('#wiz-model-dropdown').length)
                $('#wiz-model-dropdown').remove();
        });

        $('#wizStep2Next').on('click', function() {
            $('#wiz-s3-model').val($('#wiz-s2-model').val());
            $('#wiz-s3-description').val($('#wiz-s2-description').val());
            $('#wiz-s3-serial').val($('#wiz-s2-serial').val());
            $('#wiz-s3-assetid,#wiz-s3-department,#wiz-s3-location').val('');
            $('#wiz-equipment-id').val('');
            showStep(3);
        });
        $('#wizStep3Back').on('click', function() {
            showStep(wizardAssetFound ? 1 : 2);
        });

        function addToQueue(status) {
            inspectionQueue.push({
                site_id: SITE_ID,
                equipment_id: $('#wiz-equipment-id').val(),
                scheduled_at: $('#wiz-s3-inspdate').val(),
                status,
                technician_id: $('#wiz-s3-technician').val(),
                notes: $('#wiz-s3-notes').val(),
                pm_frequency: $('#wiz-s3-pmfreq').val(),
                inspection_type: $('#wiz-s3-insptype').val(),
                device_complete: $('#wiz-s3-devicecomplete').val(),
                make: wizardMatchedEquip ? (wizardMatchedEquip.make || '') : $('#wiz-s2-manufacturer')
                    .val(),
                model: $('#wiz-s3-model').val(),
                serial_number: $('#wiz-s3-serial').val(),
                device_type: $('#wiz-s3-description').val(),
                asset_tag: $('#wiz-s3-assetid').val(),
                department: $('#wiz-s3-department').val(),
                location: $('#wiz-s3-location').val(),
                manufacturer: $('#wiz-s2-manufacturer').val(),
                model_name: $('#wiz-s2-model').val(),
                description: $('#wiz-s2-description').val(),
                asset_not_found: wizardAssetFound ? '0' : '1'
            });
            updateQueueDisplay();
        }

        $('#wizBtnPass').on('click', function() {
            addToQueue('Pass');
            alert('Added to queue as Pass.');
        });
        $('#wizBtnFail').on('click', function() {
            addToQueue('Fail');
            alert('Added to queue as Fail.');
        });
        $('#wizBtnRepair').on('click', function() {
            addToQueue('Repair');
            alert('Added to queue as Repair.');
        });
        $('#wizBtnNextDevice').on('click', function() {
            resetWizard();
            updateQueueDisplay();
        });

        $('#wizBtnComplete').on('click', function() {
            if (!inspectionQueue.length) {
                alert('No inspections in queue.');
                return;
            }
            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Saving…');
            const fd = new FormData();
            const csrf = $('input[name="csrf_token"]').first();
            if (csrf.length) fd.append(csrf.attr('name'), csrf.val());
            fd.append('group_id', wizGroupId);
            fd.append('inspection_items', JSON.stringify(inspectionQueue));
            $.ajax({
                url: URL_CREATE_INSP,
                method: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                success: function() {
                    $('#inspectionWizardModal').modal('hide');
                    sessionStorage.setItem('siteDetailsActiveTab', 'inspections-tab');
                    location.reload();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Failed to save inspections.');
                    $('#wizBtnComplete').prop('disabled', false).html(
                        '<i class="fa fa-check-double me-1"></i>Complete Inspections');
                }
            });
        });

        // ── View Inspection (from inspections list table) ─────────────────
        $(document).on('click', '.view-inspection-btn', function() {
            const groupId = $(this).data('group_id');
            $.ajax({
                url: URL_GET_BY_GROUP,
                method: 'GET',
                data: {
                    group_id: groupId
                },
                dataType: 'json',
                success: function(res) {
                    if (!res.success) {
                        alert('Error: ' + res.message);
                        return;
                    }
                    const tbody = $('#inspection-details-body').empty();
                    res.data.forEach(function(insp) {
                        const badge = insp.inspection_status === 'Pass' ?
                            '<span class="badge bg-success">Pass</span>' :
                            (insp.inspection_status === 'Fail' ?
                                '<span class="badge bg-danger">Fail</span>' :
                                '<span class="badge bg-warning text-dark">' + insp
                                .inspection_status + '</span>');
                        tbody.append(
                            '<tr><td><button class="btn btn-sm btn-primary btn-edit-inspection" data-inspection-id="' +
                            insp.inspections_id + '" data-group-id="' + insp
                            .group_id +
                            '"><i class="fa fa-edit"></i> Edit</button></td>' +
                            '<td>' + badge + '</td>' +
                            '<td>' + (insp.customer_site || 'N/A') + '</td>' +
                            '<td>' + (insp.model || 'N/A') + '</td>' +
                            '<td>' + (insp.device_type || 'N/A') + '</td>' +
                            '<td>' + (insp.serial_number || 'N/A') + '</td>' +
                            '<td>' + (insp.inspection_type || 'N/A') + '</td>' +
                            '<td>' + (insp.asset_tag || 'N/A') + '</td>' +
                            '<td>' + (insp.department || 'N/A') + '</td>' +
                            '<td>' + (insp.location || insp.room || 'N/A') +
                            '</td>' +
                            '<td>' + (insp.technician_name || 'N/A') + '</td>' +
                            '<td>' + (insp.updated_at ? new Date(insp.updated_at)
                                .toLocaleDateString() : 'N/A') + '</td>' +
                            '<td>' + (insp.notes || '') + '</td></tr>'
                        );
                    });
                    $('#viewInspectionModal').modal('show');
                },
                error: function() {
                    alert('Failed to fetch inspection details.');
                }
            });
        });

        // ── Edit Inspection Wizard ────────────────────────────────────────
        function editGoToStep(n) {
            $('#edit-wiz-step1,#edit-wiz-step2,#edit-wiz-step3').removeClass('active');
            $('#edit-wiz-step' + n).addClass('active');
        }

        $(document).on('click', '.btn-edit-inspection', function() {
            const inspId = $(this).data('inspection-id');
            $.ajax({
                url: URL_GET_INSP_BY_ID + '/' + inspId,
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (!res.success) {
                        alert('Failed to load inspection.');
                        return;
                    }
                    const d = res.data;
                    $('#edit-wiz-inspection-id').val(d.id);
                    $('#edit-wiz-group-id').val(d.group_id);
                    $('#edit-wiz-equipment-id').val(d.equipment_id);
                    $('#edit-wiz-s1-serial').val(d.serial_number || '');
                    $('#edit-wiz-s2-manufacturer').val(d.make || '');
                    $('#edit-wiz-s2-model').val(d.model || '');
                    $('#edit-wiz-s2-description').val(d.device_type || '');
                    $('#edit-wiz-s2-serial').val(d.serial_number || '');
                    $('#edit-wiz-s2-assetid').val(d.asset_tag || '');
                    $('#edit-wiz-s2-department').val(d.department || '');
                    $('#edit-wiz-s2-location').val(d.location || '');
                    $('#edit-wiz-s3-model-ro').val(d.model || '');
                    $('#edit-wiz-s3-desc-ro').val(d.device_type || '');
                    $('#edit-wiz-s3-serial-ro').val(d.serial_number || '');
                    $('#edit-wiz-s3-assetid-ro').val(d.asset_tag || '');
                    $('#edit-wiz-s3-dept-ro').val(d.department || '');
                    $('#edit-wiz-s3-location-ro').val(d.location || '');
                    $('#edit-wiz-s3-pmfreq').val(d.pm_frequency || '');
                    $('#edit-wiz-s3-insptype').val(d.inspection_type || '');
                    $('#edit-wiz-s3-technician').val(d.technician_id || '');
                    $('#edit-wiz-s3-inspdate').val(d.scheduled_at ? d.scheduled_at.split(' ')[
                        0] : '');
                    $('#edit-wiz-s3-notes').val(d.notes || '');
                    $('#edit-wiz-s3-status').val(d.status || 'Pass');
                    $('#edit-wiz-s3-devicecomplete').val(d.device_complete || 'Yes');
                    $('#viewInspectionModal').modal('hide');
                    $('#editInspectionWizardModal').modal('show');
                    editGoToStep(1);
                },
                error: function() {
                    alert('Failed to load inspection data.');
                }
            });
        });

        function searchEditSerial() {
            const serial = $.trim($('#edit-wiz-s1-serial').val());
            if (!serial) return;
            $.ajax({
                url: URL_SEARCH_SERIAL,
                method: 'GET',
                data: {
                    serial_number: serial,
                    site_id: SITE_ID
                },
                dataType: 'json',
                success: function(res) {
                    if (res.found) {
                        $('#edit-wiz-equipment-id').val(res.id);
                        $('#edit-wiz-asset-not-found-alert').hide();
                        $('#edit-wiz-s2-manufacturer').val(res.make || '');
                        $('#edit-wiz-s2-model').val(res.model || '');
                        $('#edit-wiz-s2-description').val(res.device_type || '');
                        $('#edit-wiz-s2-serial').val(res.serial_number || '');
                        $('#edit-wiz-s2-assetid').val(res.asset_tag || '');
                        $('#edit-wiz-s2-department').val(res.department || '');
                        $('#edit-wiz-s2-location').val(res.location || '');
                    } else {
                        $('#edit-wiz-equipment-id').val('');
                        $('#edit-wiz-asset-not-found-alert').show();
                        $('#edit-wiz-s2-serial').val(serial);
                    }
                }
            });
        }
        $('#edit-wiz-s1-search-btn').on('click', searchEditSerial);
        $('#edit-wiz-s1-serial').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                searchEditSerial();
            }
        });
        $('#edit-wiz-step1-next').on('click', function() {
            editGoToStep(2);
        });
        $('#edit-wiz-step2-back').on('click', function() {
            editGoToStep(1);
        });
        $('#edit-wiz-step2-next').on('click', function() {
            $('#edit-wiz-s3-model-ro').val($('#edit-wiz-s2-model').val());
            $('#edit-wiz-s3-desc-ro').val($('#edit-wiz-s2-description').val());
            $('#edit-wiz-s3-serial-ro').val($('#edit-wiz-s2-serial').val());
            $('#edit-wiz-s3-assetid-ro').val($('#edit-wiz-s2-assetid').val());
            $('#edit-wiz-s3-dept-ro').val($('#edit-wiz-s2-department').val());
            $('#edit-wiz-s3-location-ro').val($('#edit-wiz-s2-location').val());
            editGoToStep(3);
        });
        $('#edit-wiz-step3-back').on('click', function() {
            editGoToStep(2);
        });
        $('#edit-wizBtnPass').on('click', function() {
            $('#edit-wiz-s3-status').val('Pass');
        });
        $('#edit-wizBtnFail').on('click', function() {
            $('#edit-wiz-s3-status').val('Fail');
        });
        $('#edit-wizBtnRepair').on('click', function() {
            $('#edit-wiz-s3-status').val('Repair');
        });

        $('#edit-wizBtnUpdate').on('click', function() {
            const inspId = $('#edit-wiz-inspection-id').val(),
                groupId = $('#edit-wiz-group-id').val();
            $.ajax({
                url: URL_UPDATE_INSP,
                method: 'POST',
                dataType: 'json',
                data: {
                    inspection_id: inspId,
                    equipment_id: $('#edit-wiz-equipment-id').val(),
                    site_id: SITE_ID,
                    manufacturer: $('#edit-wiz-s2-manufacturer').val(),
                    model_name: $('#edit-wiz-s2-model').val(),
                    description: $('#edit-wiz-s2-description').val(),
                    serial_number: $('#edit-wiz-s2-serial').val(),
                    asset_tag: $('#edit-wiz-s2-assetid').val(),
                    department: $('#edit-wiz-s2-department').val(),
                    location: $('#edit-wiz-s2-location').val(),
                    pm_frequency: $('#edit-wiz-s3-pmfreq').val(),
                    inspection_type: $('#edit-wiz-s3-insptype').val(),
                    technician_id: $('#edit-wiz-s3-technician').val(),
                    scheduled_at: $('#edit-wiz-s3-inspdate').val(),
                    notes: $('#edit-wiz-s3-notes').val(),
                    status: $('#edit-wiz-s3-status').val(),
                    device_complete: $('#edit-wiz-s3-devicecomplete').val()
                },
                success: function(res) {
                    if (res.success) {
                        alert('Inspection updated successfully!');
                        $('#editInspectionWizardModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + res.message);
                    }
                },
                error: function() {
                    alert('Failed to update inspection.');
                }
            });
        });
        $('#editInspectionWizardModal').on('hidden.bs.modal', function() {
            editGoToStep(1);
            $('#edit-wiz-asset-not-found-alert').hide();
        });

        // ── Work Orders — Edit (unchanged) ────────────────────────────────
        $(document).on('click', '.edit-workorder-btn', function() {
            const id = $(this).data('id');
            $('#workorder-id').val(id);
            $('#workorder-title').val($(this).data('title'));
            $('#workorder-equipment').val($(this).data('equipment_id'));
            $('#workorder-status').val($(this).data('status'));
            $('#workorder-priority').val($(this).data('priority'));
            $('#workorder-assigned-to').val($(this).data('assigned_to'));
            $('#workorder-start-date').val($(this).data('start_date'));
            $('#workorder-end-date').val($(this).data('end_date'));
            $('#workorder-description').val($(this).data('description'));
            $('#workOrderModalLabel').text('Edit Work Order');
            $('#workOrderSubmitBtn').text('Update Work Order');
            $('#workOrderForm').attr('action', BASE_TEC + '/work-orders/update/' + id);
            $('#addWorkOrderModal').modal('show');
        });
        $('#addWorkOrderModal').on('hidden.bs.modal', function() {
            $('#workOrderForm')[0].reset();
            $('#workorder-id').val('');
            $('#workOrderModalLabel').text('Add Work Order');
            $('#workOrderSubmitBtn').text('Save Work Order');
            $('#workOrderForm').attr('action', BASE_TEC + '/work-orders/create');
        });

    });
</script>

<?= $this->endSection() ?>