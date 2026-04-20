<?php

/**
 * Partial view for the site inspection workflow.  This markup implements the
 * replacement interface for the Inspections tab within the Site Details
 * section.  It includes a dashboard for scheduled inspections, a detailed
 * inspection workflow with pass/fail recording, and additional tabs for
 * inspected, archived, all inventory, work orders and reports.  The
 * surrounding page should ensure that the supporting CSS and JavaScript
 * functions (see details.php) are loaded.
 */
?>

<style>
    /* ── Dark theme: override Bootstrap table-striped white backgrounds ── */
    #notInspectedTable,
    #inspectedTable,
    #archivedTable,
    #inventoryTable,
    #workOrdersTable {
        --bs-table-bg: transparent !important;
        --bs-table-striped-bg: rgba(255, 255, 255, .04) !important;
        --bs-table-hover-bg: rgba(255, 255, 255, .07) !important;
        --bs-table-border-color: rgba(255, 255, 255, .07) !important;
        --bs-table-color: #e9edff !important;
        --bs-table-striped-color: #e9edff !important;
        --bs-table-hover-color: #fff !important;
        color: #e9edff !important;
    }

    #notInspectedTable thead th,
    #inspectedTable thead th,
    #archivedTable thead th,
    #inventoryTable thead th,
    #workOrdersTable thead th {
        background: transparent !important;
        color: rgba(233, 237, 255, .55) !important;
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        border-bottom: 1px solid rgba(255, 255, 255, .08) !important;
        white-space: nowrap;
    }

    #notInspectedTable tbody td,
    #inspectedTable tbody td,
    #archivedTable tbody td,
    #inventoryTable tbody td,
    #workOrdersTable tbody td {
        background: transparent !important;
        color: #e9edff !important;
        border-color: rgba(255, 255, 255, .06) !important;
    }

    /* Sort indicator arrows on column headers */
    thead th.sort-asc::after {
        content: ' ▲';
        font-size: 10px;
        opacity: .7;
    }

    thead th.sort-desc::after {
        content: ' ▼';
        font-size: 10px;
        opacity: .7;
    }

    thead th[title="Click to sort"]:hover {
        color: #fff !important;
    }

    /* Asset # badge: dark border on dark bg */
    #inspectedTable .badge.text-dark {
        color: #e9edff !important;
        border-color: rgba(255, 255, 255, .25) !important;
    }

    /* Status dropdown button states */
    .status-badge {
        border: none;
        font-weight: 600;
        font-size: 13px;
    }

    .status-in-progress {
        background: rgba(234, 179, 8, .15);
        color: #fbbf24;
        border: 1px solid rgba(234, 179, 8, .3);
    }

    .status-closed {
        background: rgba(34, 197, 94, .15);
        color: #4ade80;
        border: 1px solid rgba(34, 197, 94, .3);
    }
</style>

<!-- Site Inspection Workflow -->
<div id="site-inspection-workflow">
    <div class="container-fluid px-4 py-4 g-card">
        <!-- Dashboard view (list of inspections) -->
        <div class="fade-in" id="view-dashboard">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Inspections List</h3>
                    <p class="text-muted small mb-0">
                        Manage and track equipment safety checks.
                    </p>
                </div>
                <button class="btn btn-primary" onclick="startInspection()">
                    <i class="fa-solid fa-plus me-2"></i>Add Inspection
                </button>
            </div>
            <div class="card-custom p-4">
                <!-- FIX 6: Open / All / Closed filter tabs -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                 <div class="export-bar mb-0">
                        <button class="export-btn btn">Copy</button>
                        <button class="export-btn btn">CSV</button>
                        <button class="export-btn btn">Excel</button>
                        <button class="export-btn btn">PDF</button>
                    </div>
                        
                <div class="btn-group btn-group-sm" id="inspFilterBtns" role="group">
                        <button type="button" class="btn btn-primary active" id="inspFilterOpen"
                            onclick="filterInspectionsByStatus('open')">
                            <i class="fa-solid fa-rotate me-1"></i> Open
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="inspFilterAll"
                            onclick="filterInspectionsByStatus('all')">
                            All
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="inspFilterClosed"
                            onclick="filterInspectionsByStatus('closed')">
                            <i class="fa-solid fa-circle-check me-1"></i> Closed
                        </button>
                    </div>
                    <div class="input-group" style="max-width: 300px">
                        <span class="input-group-text border-end-0"><i
                                class="fa-solid fa-search text-muted"></i></span>
                        <input class="form-control border-start-0 ps-0" id="inspSearchInput"
                            placeholder="Search inspections..." type="text"
                            oninput="applyInspectionSearch(this.value)">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover" id="inspectionsTable">
                        <thead>
                            <tr>
                                <th>Inspection ID</th>
                                <th>Scheduled Date</th>
                                <th>Technician</th>
                                <th>Next Due Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($inspectionList ?? [])): ?>
                                <?php foreach ($inspectionList as $insp): ?>
                                    <tr data-insp-status="<?= ($insp['status'] ?? '') === 'Closed/Complete' ? 'closed' : 'open' ?>">
                                        <td>
                                            <?php
                                            // Use the actual group_id stored in the database — this is what
                                            // appears in the report header (e.g. INSP-20260309151712).
                                            $inspId = $insp['group_id'];
                                            ?>
                                            <span class="fw-medium"><?= esc($inspId) ?></span>
                                        </td>
                                        <td><?= esc(date('M d, Y', strtotime($insp['scheduled_at']))) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php
                                                $techName = $insp['technician_name'] ?? 'N/A';
                                                $initials = strtoupper(substr($techName, 0, 2));
                                                ?>
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 24px; height: 24px; font-size: 10px">
                                                    <?= esc($initials) ?>
                                                </div>
                                                <?= esc($techName) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($insp['next_due_date'])): ?>
                                                <?= esc(date('M d, Y', strtotime($insp['next_due_date']))) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $st = $insp['status'] ?? 'In Progress';
                                            if ($st === 'Closed/Complete'):
                                            ?>
                                                <span class="badge bg-success">
                                                    <i class="fa-solid fa-circle-check me-1"></i>Closed
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fa-solid fa-rotate me-1"></i>In Progress
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-secondary me-1"
                                                onclick="openInspectionReport('<?= esc($insp['group_id']) ?>')"
                                                title="View Inspection Report">
                                                <i class="fa-solid fa-file-export"></i>
                                            </button>

                                            <button class="btn btn-sm btn-primary btn-view-insp"
                                                data-group="<?= esc($insp['group_id']) ?>"
                                                data-site="<?= esc($site['name']) ?>"
                                                data-type="<?= esc($insp['inspection_type'] ?? 'Equipment Inspection') ?>"
                                                data-title="<?= esc($insp['title'] ?? '') ?>"
                                                data-inspid="<?= esc($inspId) ?>"
                                                data-tech="<?= esc($insp['technician_name'] ?? 'N/A') ?>"
                                                data-status="<?= esc($insp['status'] ?? 'In Progress') ?>">
                                                <i class="fa-solid fa-eye me-1"></i> View
                                            </button>
                                            <!--
                                        Use a dedicated delete-inspection-btn with a data-id attribute to
                                        trigger the SweetAlert confirmation and AJAX route defined in
                                        admin/sites/details.php. The previous implementation merely
                                        displayed a confirmation dialog without performing any action, so
                                        the inspection was never deleted. By adding the class and data-id,
                                        the global jQuery handler can capture the click event and
                                        redirect to the appropriate deletion route (e.g. /admin/inspections/delete/{groupId}).
                                    -->
                                            <button class="btn btn-sm btn-danger delete-inspection-btn"
                                                data-id="<?= esc($insp['group_id']) ?>" title="Delete Inspection">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No inspections found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small">
                    <span>Showing 1 to 1 of 1 entries</span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Previous</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!-- Modal for recording inspection results (pass/fail and notes) -->
        <div aria-hidden="true" class="modal fade" id="inspectionResultModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="inspectionResultForm">
                        <div class="modal-header">
                            <h5 class="modal-title">Record Inspection Result</h5>
                            <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Device info will be populated dynamically -->
                            <p class="small text-muted" id="inspectionDeviceInfo"></p>
                            <div class="mb-3">
                                <label class="form-label">Pass/Fail</label>
                                <select class="form-select" id="inspectionResultSelect" required="">
                                    <option selected="" value="Pass">Pass</option>
                                    <option value="Fail">Fail</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Service Notes</label>
                                <textarea class="form-control" id="inspectionResultNotes"
                                    placeholder="Enter inspection notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                type="button">Cancel</button>
                            <button class="btn btn-primary" type="submit">Save Inspection</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Inspection detail view -->
        <div class="fade-in d-none-view" id="view-inspection" data-current-status="In Progress">
            <!-- Hidden JSON data: Equipment EST/CAL settings for counting logic -->
            <script type="application/json" id="equipmentSettingsData">
                <?php
                // Build equipment settings map: model => {est, cal}
                $equipmentSettingsMap = [];
                if (!empty($site) && !empty($site['id'])) {
                    $equipmentModel = new \App\Models\EquipmentModel();
                    $allEquipment = $equipmentModel->where('site_id', $site['id'])->findAll();
                    foreach ($allEquipment as $eq) {
                        $model = $eq['model'] ?? '';
                        if (!empty($model)) {
                            $equipmentSettingsMap[$model] = [
                                'est' => $eq['est'] ?? '0',
                                'cal' => $eq['cal'] ?? '0'
                            ];
                        }
                    }
                }
                echo json_encode($equipmentSettingsMap);
                ?>
            </script>
            <div class="mb-4">
                <a class="text-decoration-none text-muted small mb-2 d-inline-block" href="#" onclick="showDashboard()">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Inspections
                    List
                </a>
                <div class="d-flex justify-content-between align-items-start mt-2">
                    <div>
                        <!-- Dynamic header: populated by viewInspection() JS function -->
                        <div class="badge  text-light border mb-2" id="insp-site-label">Site:
                            <?= esc($site['name'] ?? '—') ?></div>
                        <h2 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <span id="insp-title" contenteditable="true"
                                style="outline:none;min-width:120px;border-bottom:2px dashed transparent;cursor:text;transition:border-color .2s;"
                                title="Click to edit inspection title"
                                onmouseenter="this.style.borderBottomColor='#6c757d'"
                                onmouseleave="this.style.borderBottomColor='transparent'"
                                onfocus="this.style.borderBottomColor='#0d6efd'"
                                onblur="saveInspTitle(this)">—</span>
                            <i class="fa-solid fa-pencil text-muted" style="font-size:14px;opacity:.5;cursor:text;" onclick="document.getElementById('insp-title').focus()"></i>
                            <span id="insp-title-saved" style="font-size:11px;color:#22c55e;opacity:0;transition:opacity .4s;font-weight:400;">✓ saved</span>
                        </h2>
                        <p class="text-muted">
                            <span id="insp-id-label">—</span> •
                            <span class="text-primary" id="insp-technician">—</span> •
                            <span id="insp-date-display" class="small"></span>
                        </p>
                    </div>
                    <div class="text-end">
                        <label class="d-block small text-muted mb-1">Current Status</label>
                        <div class="dropdown">
                            <button aria-expanded="false"
                                class="btn btn-light dropdown-toggle status-badge status-in-progress"
                                data-bs-toggle="dropdown" id="statusDropdown" type="button">
                                <i class="fa-solid fa-rotate"></i> In Progress
                            </button>
                            <ul aria-labelledby="statusDropdown" class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="updateStatus('In Progress')">In
                                        Progress</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                        onclick="updateStatus('Closed/Complete')">Closed/Complete</a>
                                </li>
                            </ul>
                        </div>
                        <div class="small text-muted mt-1 fst-italic">
                            Mark as Closed when done
                        </div>
                    </div>
                </div>
            </div>
            <!-- Asset entry section -->
            <div class="asset-input-wrapper glass-card p-3 mb-3">
                <h5 class="fw-bold mb-3">
                    Start With Asset Number <span class="text-danger">Not</span> Serial
                    Number
                </h5>
                <div class="d-flex justify-content-center">
                    <div class="input-group" style="max-width: 600px">
                        <input aria-label="Asset Barcode" class="form-control big-input" id="assetInput"
                            placeholder="Scan or Enter Barcode..." type="text">
                        <button class="btn btn-primary big-btn" onclick="handleAssetGo()" type="button">
                            <i class="fa-solid fa-arrow-right"></i> Go
                        </button>
                    </div>
                </div>
                <p class="text-muted small mt-2">
                    This will open the Pass/Fail form. Devices only move to Inspected Items after the inspection result
                    is recorded.
                </p>
            </div>
            <!-- Tabs and data grid -->
            <div class="card-custom">
                <div class="card-header  border-0 pt-3 pb-0">
                    <ul class="nav nav-tabs" id="inspectionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-target="#not-inspected" data-bs-toggle="tab"
                                id="not-inspected-tab" role="tab" type="button" aria-selected="true">
                                Not Inspected
                                <span class="badge rounded-pill ms-1" id="not-inspected-count">
                                    <?= esc(count($notInspected ?? [])) ?>
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link disabled" data-bs-target="#inspect-device" data-bs-toggle="tab"
                                disabled="" id="inspect-device-tab" role="tab" type="button" aria-selected="false"
                                tabindex="-1">
                                Pass/Fail
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#inspected" data-bs-toggle="tab" id="inspected-tab"
                                role="tab" type="button" aria-selected="false" tabindex="-1">
                                Inspected Items
                                <span class="badge bg-success rounded-pill ms-1" id="inspected-count">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#archived" data-bs-toggle="tab" id="archived-tab"
                                role="tab" type="button" aria-selected="false" tabindex="-1">
                                Archived Items
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#inventory" data-bs-toggle="tab" id="inventory-tab"
                                role="tab" type="button" aria-selected="false" tabindex="-1">
                                All Inventory
                            </button>
                        </li>
                        <!-- Added tab for Work Orders inside the inspection workflow -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#insp-work-orders" data-bs-toggle="tab"
                                id="insp-work-orders-tab" role="tab" type="button" aria-selected="false" tabindex="-1">
                                Work Orders
                            </button>
                        </li>
                        <!-- Added tab for inspection reports inside the inspection workflow -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#inspection-reports" data-bs-toggle="tab"
                                id="reports-tab" role="tab" type="button" aria-selected="false" tabindex="-1">
                                Inspection Reports
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="myTabContent">
                        <!-- Not inspected content -->
                        <div class="tab-pane fade p-4 active show" id="not-inspected" role="tabpanel"
                            aria-labelledby="not-inspected-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <div class="fw-semibold">Site Inventory Pending Inspection</div>
                                    <div class="text-muted small" id="not-inspected-message">
                                        <?= esc(count($notInspected ?? [])) ?> item(s) remaining in site inventory
                                        pending inspection.
                                    </div>
                                </div>
                                <div class="export-bar mb-0">
                                    <button class="export-btn btn"
                                        onclick="copyTable('notInspectedTable')">Copy</button>
                                    <button class="export-btn btn"
                                        onclick="exportTableCSV('notInspectedTable')">CSV</button>
                                    <button class="export-btn btn"
                                        onclick="exportTableExcel('notInspectedTable')">Excel</button>
                                    <button class="export-btn btn"
                                        onclick="exportTablePDF('notInspectedTable')">PDF</button>
                                </div>
                                <div class="input-group" style="max-width: 320px">
                                    <span class="input-group-text  border-end-0"><i
                                            class="fa-solid fa-search text-muted"></i></span>
                                    <input class="form-control border-start-0 ps-0" id="notInspectedSearch"
                                        placeholder="Search pending items..." type="text">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-striped mb-0" id="notInspectedTable">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Asset #</th>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Type</th>
                                            <th>Dept / Room</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="notInspectedTableBody">
                                        <?php if (!empty($notInspected ?? [])): ?>
                                            <?php foreach ($notInspected as $eq): ?>
                                                <tr data-asset="<?= esc($eq['asset_tag']) ?>">
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" type="button"
                                                            onclick="inspectFromNotInspected('<?= esc($eq['asset_tag']) ?>')">
                                                            <i class="fa-solid fa-arrow-right me-1"></i> Inspect
                                                        </button>
                                                    </td>
                                                    <td><?= esc($eq['asset_tag']) ?></td>
                                                    <td><?= esc($eq['make'] ?? $eq['equipment_make'] ?? '') ?></td>
                                                    <td><?= esc($eq['model'] ?? $eq['equipment_model'] ?? '') ?></td>
                                                    <td><?= esc($eq['device_type'] ?? '') ?></td>
                                                    <td><?= esc($eq['department'] ?? '') ?><?php if (!empty($eq['location'])): ?>
                                                        / <?= esc($eq['location']) ?><?php endif; ?></td>
                                                    <td><span class="badge bg-secondary">Not Inspected</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No equipment pending
                                                    inspection.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Pass/Fail inspection form content -->
                        <div class="tab-pane fade p-4" id="inspect-device" role="tabpanel"
                            aria-labelledby="inspect-device-tab">
                            <div class="text-white" id="inspectDeviceEmpty">
                                Select a device to inspect (scan an Asset # above or click <strong>Inspect</strong> from
                                the Not Inspected tab).
                            </div>
                            <div class="d-none p-3" id="inspectDeviceFormWrapper">
                                <div class="card border-0 shadow-sm mb-4 glass-card">
                                    <div class="card-body">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-3 text-white  fw-semibold">Model:</div>
                                            <div class="col-md-9 text-white" id="inspectModelDisplay">—</div>
                                            <div class="col-md-3 text-white  fw-semibold">Department:</div>
                                            <div class="col-md-9 text-white"><input class="form-control"
                                                    id="inspectDept" placeholder="Department" type="text"></div>
                                            <div class="col-md-3 text-white  fw-semibold">Room:</div>
                                            <div class="col-md-9 text-white"><input class="form-control"
                                                    id="inspectRoom" placeholder="Room" type="text"></div>
                                            <div class="col-md-3 text-white  fw-semibold">Serial #:</div>
                                            <div class="col-md-9 text-white"><input class="form-control"
                                                    id="inspectSerial" placeholder="Serial #" type="text"></div>
                                            <div class="col-md-3 text-white  fw-semibold">Asset ID:</div>
                                            <div class="col-md-9 text-white"><input class="form-control"
                                                    id="inspectAsset" placeholder="Asset ID" type="text" title="You may edit the Asset ID if needed">
                                            </div>
                                            <div class="col-md-3 text-white fw-semibold">Manufacturer PM Frequency
                                                (Days):</div>
                                            <div class="col-md-9">
                                                <select class="form-select" id="inspectPMFrequency"
                                                    style="max-width: 240px;">
                                                    <option selected="" value="12 Month">12 Month</option>
                                                    <option value="6 Month">6 Month</option>
                                                    <option value="3 Month">3 Month</option>
                                                    <option value="24 Month">24 Month</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3 text-white fw-semibold">Action Performed::</div>

                                            <div class="col-md-9">
                                                <select class="form-select" id="inspectActionPerformed"
                                                    style="max-width: 240px;">
                                                    <option value="Annual Performance Inspection">Annual Performance
                                                        Inspection</option>
                                                </select>
                                            </div>



                                        </div>
                                    </div>
                                </div>
                                <div class="card border-0 glass-card shadow-sm">
                                    <div class="card-body">
                                        <div class="row g-3">

                                            <div class="col-12">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="text-white fw-semibold"
                                                        style="min-width: 70px; padding-top: 6px;">Notes:</div>
                                                    <textarea class="form-control text-white" id="inspectNotes"
                                                        placeholder="Enter service notes..." rows="5"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                                                <button class="btn btn-success px-4" id="btnPassInspection"
                                                    type="button">Pass Inspection</button>
                                                <button class="btn btn-danger px-4" id="btnFailInspection"
                                                    type="button">Fail Inspection</button>
                                                <button class="btn btn-warning px-4 text-white" id="btnFailWOInspection"
                                                    type="button">Fail Inspection &amp; Open Work Order</button>
                                                <button class="btn btn-primary px-4" id="btnRepairInspection"
                                                    type="button">Repair Inspection</button>
                                                <!-- <button class="btn btn-primary ms-auto" id="btnCancelInspection"
                                                    type="button">Cancel</button> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Inspected items content -->
                        <div class="tab-pane fade" id="inspected" role="tabpanel" aria-labelledby="inspected-tab">
                            <!-- Header for inspected items: title, export buttons, and search bar -->
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <div class="fw-semibold">Inspected Items</div>
                                    <div class="text-muted small">These items have been inspected.</div>
                                </div>
                                <div class="export-bar mb-0">
                                    <button class="export-btn btn" onclick="copyTable('inspectedTable')">Copy</button>
                                    <button class="export-btn btn"
                                        onclick="exportTableCSV('inspectedTable')">CSV</button>
                                    <button class="export-btn btn"
                                        onclick="exportTableExcel('inspectedTable')">Excel</button>
                                    <button class="export-btn btn"
                                        onclick="exportTablePDF('inspectedTable')">PDF</button>
                                </div>
                                <div class="d-flex gap-2 align-items-center flex-wrap">
                                    <!-- Result filter -->
                                    <select id="inspectedResultFilter" class="form-select form-select-sm"
                                        style="max-width:140px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:#e9edff;"
                                        onchange="filterInspectedResult(this.value)">
                                        <option value="">All Results</option>
                                        <option value="Pass">✓ Pass</option>
                                        <option value="Fail">✗ Fail</option>
                                        <option value="Repair">Repair</option>
                                    </select>
                                    <div class="input-group" style="max-width: 260px">
                                        <span class="input-group-text border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                                        <input class="form-control border-start-0 ps-0" id="inspectedSearch"
                                            placeholder="Search brand, model, asset..." type="text">
                                    </div>
                                </div>
                            </div>
                            <!-- Device type counter: Total, EST, CAL per device type with totals row -->
                            <div class="mb-3" id="deviceTypeCounter">
                                <h6 class="fw-semibold">Device Type Counter</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm  mb-0">
                                        <thead>
                                            <tr>
                                                <th>Device Type</th>
                                                <th>Total</th>
                                                <th>EST</th>
                                                <th>CAL</th>
                                            </tr>
                                        </thead>
                                        <tbody id="deviceTypeCountsBody">
                                            <?php
                                            // Build device type counts grouped by group_id.
                                            $deviceCountsByGroup = [];
                                            if (!empty($inspectedItems ?? [])) {
                                                foreach ($inspectedItems as $item) {
                                                    // echo '<pre>'; print_r($item); echo '</pre>';
                                                    $gid  = $item['group_id'] ?? '';
                                                    $type = $item['device_type'] ?? 'Unknown';
                                                    if (!isset($deviceCountsByGroup[$gid][$type])) {
                                                        $deviceCountsByGroup[$gid][$type] = ['total' => 0, 'est' => 0, 'cal' => 0];
                                                    }
                                                    $deviceCountsByGroup[$gid][$type]['total']++;

                                                    // Check EST: Convert to int first, then check if truthy
                                                    // Values can be: 1 (int), '1' (string), 'Yes', or 0/'No'
                                                    $estVal = $item['est'] ?? 0;
                                                    $estInt = (int) $estVal;  // Convert to integer
                                                    if ($estInt === 1 || strtolower((string)$estVal) === 'yes') {
                                                        $deviceCountsByGroup[$gid][$type]['est']++;
                                                    }

                                                    // Check CAL: Convert to int first, then check if truthy
                                                    // Values can be: 1 (int), '1' (string), 'Yes', or 0/'No'
                                                    $calVal = $item['cal'] ?? 0;
                                                    $calInt = (int) $calVal;  // Convert to integer
                                                    if ($calInt === 1 || strtolower((string)$calVal) === 'yes') {
                                                        $deviceCountsByGroup[$gid][$type]['cal']++;
                                                    }
                                                }
                                            }
                                            $allDeviceCounts = [];
                                            foreach ($deviceCountsByGroup as $gid => $types) {
                                                foreach ($types as $type => $counts) {
                                                    $allDeviceCounts[] = ['group_id' => $gid, 'type' => $type, 'counts' => $counts];
                                                }
                                            }
                                            ?>
                                            <?php if (!empty($allDeviceCounts)): ?>
                                                <?php foreach ($allDeviceCounts as $row): ?>
                                                    <tr data-group-id="<?= esc($row['group_id']) ?>" style="display:none">
                                                        <td><?= esc($row['type']) ?></td>
                                                        <td><?= esc($row['counts']['total']) ?></td>
                                                        <td><?= esc($row['counts']['est']) ?></td>
                                                        <td><?= esc($row['counts']['cal']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tr id="deviceCountTotalRow" data-total-row="1" style="display:none"
                                                    class="fw-bold table-secondary">
                                                    <td class="fw-bold">Total</td>
                                                    <td class="fw-bold" id="deviceCountTotal">0</td>
                                                    <td class="fw-bold" id="deviceCountTotalEST">0</td>
                                                    <td class="fw-bold" id="deviceCountTotalCAL">0</td>
                                                </tr>
                                            <?php else: ?>
                                                <tr id="deviceCountEmptyRow">
                                                    <td colspan="4" class="text-center text-muted">No inspected items yet.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-striped mb-0" id="inspectedTable">
                                    <thead>
                                        <tr>
                                            <th>Actions</th>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Type</th>
                                            <th>S/N</th>
                                            <th>Asset #</th>
                                            <th>Dept / Room</th>
                                            <th>Tech</th>
                                            <th>Result</th>
                                            <th style="width: 28%">Notes</th>
                                            <th>Insp Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="inspectionTableBody">
                                        <?php if (!empty($inspectedItems ?? [])): ?>
                                            <tr id="inspectedEmptyRow" style="display:none">
                                                <td colspan="10" class="text-center text-muted">No inspected items for this
                                                    inspection yet.</td>
                                            </tr>
                                            <?php foreach ($inspectedItems as $item): ?>
                                                <tr class="fade-in" data-row-id="<?= esc($item['id']) ?>"
                                                    data-asset="<?= esc($item['asset_tag']) ?>"
                                                    data-notes="<?= esc($item['notes'] ?? '') ?>"
                                                    data-action="<?= esc($item['inspection_type'] ?? $item['action_performed'] ?? '') ?>"
                                                    data-group-id="<?= esc($item['group_id'] ?? '') ?>"
                                                    data-device-type="<?= esc($item['device_type'] ?? '') ?>"
                                                    data-est="<?= esc($item['est'] ?? 'No') ?>"
                                                    data-cal="<?= esc($item['cal'] ?? 'No') ?>">
                                                    <td>
                                                        <div class="action-btns">
                                                            <button class="btn-icon btn-edit-inspected btn-primary" title="Edit"
                                                                data-id="<?= esc($item['id']) ?>"
                                                                data-model="<?= esc($item['model'] ?? $item['make'] ?? '') ?>"
                                                                data-type="<?= esc($item['device_type'] ?? '') ?>"
                                                                data-serial="<?= esc($item['serial_number'] ?? '') ?>"
                                                                data-asset="<?= esc($item['asset_tag'] ?? '') ?>"
                                                                data-dept="<?= esc($item['department'] ?? '') ?>"
                                                                data-room="<?= esc($item['location'] ?? '') ?>"
                                                                data-tech="<?= esc($item['technician'] ?? '') ?>"
                                                                data-notes="<?= esc($item['notes'] ?? '') ?>">
                                                                <i class="fa-solid fa-pen text-white"></i>
                                                            </button>
                                                            <button class="btn-icon text-danger btn-delete-inspected btn-danger"
                                                                title="Delete" data-id="<?= esc($item['id']) ?>"
                                                                data-asset="<?= esc($item['asset_tag'] ?? '') ?>">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td><?= esc($item['make'] ?? '') ?></td>
                                                    <td><strong><?= esc($item['model'] ?? $item['make']) ?></strong></td>
                                                    <td><?= esc($item['device_type'] ?? '') ?></td>
                                                    <td><?= esc($item['serial_number'] ?? 'N/A') ?></td>
                                                    <td><span
                                                            class="badge  text-dark border"><?= esc($item['asset_tag']) ?></span>
                                                    </td>
                                                    <td><?= esc($item['department'] ?? '') ?><br><span
                                                            class="text-muted small"><?= esc($item['location'] ?? '') ?></span>
                                                    </td>
                                                    <td><?= esc($item['technician'] ?? 'N/A') ?></td>
                                                    <td><?php
                                                        $res = $item['result'] ?? '';
                                                        if ($res === 'Pass'): ?>
                                                            <span class="text-success fw-semibold"><i
                                                                    class="fa-solid fa-check me-1"></i>Pass</span>
                                                        <?php elseif ($res === 'Fail'): ?>
                                                            <span class="text-danger fw-semibold"><i
                                                                    class="fa-solid fa-xmark me-1"></i>Fail</span>
                                                        <?php elseif (!empty($res)): ?>
                                                            <span class="text-warning fw-semibold"><i
                                                                    class="fa-solid fa-triangle-exclamation me-1"></i><?= esc($res) ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="small text-muted"><?= esc($item['notes'] ?? '') ?></td>
                                                    <td>
                                                        <?php if (!empty($item['inspection_date'])):
                                                            $d = new DateTime($item['inspection_date']);
                                                            echo esc($d->format('Y-m-d')) . ' <br>' . esc($d->format('h:i A'));
                                                        else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">No inspected items found.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Archived items -->
                        <div class="tab-pane fade p-4" id="archived" role="tabpanel" aria-labelledby="archived-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <div class="fw-semibold">Archived Items</div>
                                    <div class="text-muted small">Archived equipment is kept for history and reporting.
                                    </div>
                                </div>
                                <div class="export-bar mb-0">
                                    <button class="export-btn" onclick="copyTable('archivedTable')">Copy</button>
                                    <button class="export-btn" onclick="exportTableCSV('archivedTable')">CSV</button>
                                    <button class="export-btn"
                                        onclick="exportTableExcel('archivedTable')">Excel</button>
                                    <button class="export-btn" onclick="exportTablePDF('archivedTable')">PDF</button>
                                </div>
                                <div class="input-group" style="max-width: 320px">
                                    <span class="input-group-text  border-end-0"><i
                                            class="fa-solid fa-search text-muted"></i></span>
                                    <input class="form-control border-start-0 ps-0" id="archivedSearch"
                                        placeholder="Search archived..." type="text">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-striped mb-0" id="archivedTable">
                                    <thead>
                                        <tr>
                                            <th>Asset #</th>
                                            <th>Model</th>
                                            <th>Type</th>
                                            <th>Dept / Room</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="archivedTableBody">
                                        <?php if (!empty($archivedItems ?? [])): ?>
                                            <?php foreach ($archivedItems as $item): ?>
                                                <tr data-asset="<?= esc($item['asset_tag']) ?>">
                                                    <td><?= esc($item['asset_tag']) ?></td>
                                                    <td><?= esc($item['model'] ?? $item['make']) ?></td>
                                                    <td><?= esc($item['device_type'] ?? '') ?></td>
                                                    <td><?= esc($item['department'] ?? '') ?><?php if (!empty($item['location'])): ?>
                                                        / <?= esc($item['location']) ?><?php endif; ?></td>
                                                    <td><span class="badge bg-dark">Archived</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center ">No archived items found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- All inventory list -->
                        <div class="tab-pane fade p-4" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
                            <!-- Header for all inventory: title, export buttons, and search bar -->
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <div class="fw-semibold">All Inventory</div>
                                    <div class="text-muted small">Full list of equipment in the site inventory.</div>
                                </div>
                                <div class="export-bar mb-0">
                                    <button class="export-btn btn" onclick="copyTable('inventoryTable')">Copy</button>
                                    <button class="export-btn btn"
                                        onclick="exportTableCSV('inventoryTable')">CSV</button>
                                    <button class="export-btn btn"
                                        onclick="exportTableExcel('inventoryTable')">Excel</button>
                                    <button class="export-btn btn"
                                        onclick="exportTablePDF('inventoryTable')">PDF</button>
                                </div>
                                <div class="input-group" style="max-width: 320px">
                                    <span class="input-group-text  border-end-0"><i
                                            class="fa-solid fa-search text-muted"></i></span>
                                    <input class="form-control border-start-0 ps-0" id="allInventorySearch"
                                        placeholder="Search all inventory..." type="text">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-striped" id="inventoryTable">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Asset #</th>
                                            <th>Model</th>
                                            <th>Type</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="allInventoryTableBody">
                                        <?php if (!empty($equipment ?? [])): ?>
                                            <?php foreach ($equipment as $eq): ?>
                                                <tr data-asset="<?= esc($eq['asset_tag']) ?>">
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            title="Open Work Order"
                                                            onclick="openWorkOrderModalFromInventory('<?= esc($eq['asset_tag']) ?>')">
                                                            <i class="fa-solid fa-briefcase"></i>
                                                        </button>
                                                    </td>
                                                    <td><?= esc($eq['asset_tag']) ?></td>
                                                    <td><?= esc($eq['model'] ?? $eq['make']) ?></td>
                                                    <td><?= esc($eq['device_type'] ?? '') ?></td>
                                                    <td><?= esc($eq['department'] ?? '') ?><?php if (!empty($eq['location'])): ?>
                                                        / <?= esc($eq['location']) ?><?php endif; ?></td>
                                                    <td>
                                                        <?php
                                                        // Determine the inspection status (Inspected, Not Inspected, Archived)
                                                        $status = $eq['inspection_status'] ?? 'Unknown';
                                                        $badgeClass = 'bg-secondary';
                                                        if ($status === 'Inspected') {
                                                            $badgeClass = 'bg-success';
                                                        } elseif ($status === 'Archived') {
                                                            $badgeClass = 'bg-dark';
                                                        }
                                                        ?>
                                                        <span class="badge <?= esc($badgeClass) ?>">
                                                            <?= esc($status) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No inventory found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <!-- Work Orders tab pane -->
                        <div class="tab-pane fade p-3" id="insp-work-orders" role="tabpanel"
                            aria-labelledby="insp-work-orders-tab">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h5 class="fw-bold mb-1">Work Orders</h5>
                                    <p class="text-muted small mb-0">Manage and track work orders for this site.</p>
                                </div>
                                <button class="btn btn-primary" onclick="openWorkOrderModal()">
                                    <i class="fa-solid fa-plus me-2"></i>Add Work Order
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                                <div class="export-bar mb-0">
                                    <button class="export-btn btn" onclick="copyTable('workOrdersTable')">Copy</button>
                                    <button class="export-btn btn"
                                        onclick="exportTableCSV('workOrdersTable')">CSV</button>
                                    <button class="export-btn btn"
                                        onclick="exportTableExcel('workOrdersTable')">Excel</button>
                                    <button class="export-btn btn"
                                        onclick="exportTablePDF('workOrdersTable')">PDF</button>
                                </div>
                                <div class="input-group" style="max-width: 300px">
                                    <span class="input-group-text  border-end-0"><i
                                            class="fa-solid fa-search text-muted"></i></span>
                                    <input class="form-control border-start-0 ps-0" id="workOrdersSearch"
                                        oninput="renderWorkOrdersTable()" placeholder="Search work orders..."
                                        type="text">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-hover" id="workOrdersTable">
                                    <thead>
                                        <tr>
                                            <th>Actions</th>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Equipment</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Technician</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody id="workOrdersTableBody">
                                        <?php if (!empty($workOrders ?? [])): ?>
                                            <?php foreach ($workOrders as $wo): ?>
                                                <tr data-row-id="<?= esc($wo['id']) ?>"
                                                    data-group-id="<?= esc($wo['group_id'] ?? '') ?>">
                                                    <td>
                                                        <div class="action-btns">
                                                            <button class="btn-icon btn-edit btn-primary" title="Edit"
                                                                data-tech-id="<?= esc($wo['assigned_to'] ?? '') ?>"
                                                                data-tech-name="<?= esc($wo['assigned_to_name'] ?? '') ?>">
                                                                <i class="fa-solid fa-pen text-white"></i>
                                                            </button>
                                                            <button class="btn-icon text-danger btn-delete" title="Delete">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td><?= esc($wo['id']) ?></td>
                                                    <td><?= esc($wo['title'] ?? '') ?></td>
                                                    <td>
                                                        <?php if (!empty($wo['asset_tag'])): ?>
                                                            <span
                                                                class="badge  text-dark border"><?= esc($wo['asset_tag']) ?></span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($wo['serial_number'])): ?>
                                                            <br><span class="small text-muted">S/N:
                                                                <?= esc($wo['serial_number']) ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= esc($wo['priority'] ?? '') ?></td>
                                                    <td><?= esc($wo['status'] ?? '') ?></td>
                                                    <td><?= esc(
                                                            (!empty($wo['assigned_to_name']) ? $wo['assigned_to_name'] : null)
                                                                ?? (!empty($wo['technician'])    ? $wo['technician']       : null)
                                                                ?? (!empty($wo['technician_name']) ? $wo['technician_name'] : null)
                                                                ?? 'N/A'
                                                        ) ?></td>
                                                    <td><?= !empty($wo['start_date']) ? esc(date('Y-m-d', strtotime($wo['start_date']))) : '<span class="text-muted">-</span>' ?>
                                                    </td>
                                                    <td><?= !empty($wo['end_date']) ? esc(date('Y-m-d', strtotime($wo['end_date']))) : '<span class="text-muted">-</span>' ?>
                                                    </td>
                                                    <td><?= esc($wo['description'] ?? '') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10" class="text-center text-muted py-3">No work orders found.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Inspection Reports tab pane -->
                        <div class="tab-pane fade p-3" id="inspection-reports" role="tabpanel"
                            aria-labelledby="reports-tab">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <div>
                                    <h5 class="fw-bold mb-1">Inspection Reports</h5>
                                    <p class="text-muted small mb-0">Preview and export inspection summaries.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary" onclick="adminTabPreviewReport()">
                                        <i class="fa-solid fa-file-pdf me-2"></i>Preview
                                    </button>
                                    <button class="btn btn-primary" onclick="adminTabDownloadReport()">
                                        <i class="fa-solid fa-download me-2"></i>Download PDF
                                    </button>
                                </div>
                            </div>
                            <div id="reportsTabContent" class="mb-4">
                                <p class="text-muted fst-italic">Click the <i class="fa-solid fa-file-export"></i>
                                    report icon on an inspection row to load the report here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer" style="z-index: 2000;"></div>
        <!-- Work Orders and Reports are now rendered as tab panes inside view-inspection -->
        <!-- Work Order modal -->
        <div aria-hidden="true" class="modal fade" id="workOrderModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="inspectionWorkOrderForm">
                        <input id="woIndex" type="hidden" value="-1">
                        <!-- Hidden fields to track work order id and equipment id for AJAX operations -->
                        <input id="woId" type="hidden" value="">
                        <input id="woEquipmentId" type="hidden" value="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="workOrderModalTitle">Add Work Order</h5>
                            <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="workOrderError"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Title</label>
                                    <input class="form-control" id="woTitle" required="" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Asset #</label>
                                    <input class="form-control" id="woAsset" onchange="onWOAssetChange()"
                                        placeholder="Enter asset to auto-fill" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="woMake">Make</label>
                                    <input class="form-control" id="woMake" placeholder="Auto-filled"
                                        readonly="readonly" type="text"></input>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="woModel">Model</label>
                                    <input class="form-control" id="woModel" placeholder="Auto-filled"
                                        readonly="readonly" type="text"></input>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Serial #</label>
                                    <input class="form-control" id="woSerial" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Equipment</label>
                                    <input class="form-control" id="woEquipment" readonly="" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Priority</label>
                                    <select class="form-select" id="woPriority">
                                        <option value="low">Low</option>
                                        <option selected value="normal">Normal</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="woStatus">
                                        <option value="open">Open</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Technician</label>
                                    <select class="form-select" id="woTech">
                                        <option value="">-- Select Technician --</option>
                                        <?php if (!empty($technicians ?? [])): ?>
                                            <?php foreach ($technicians as $tech): ?>
                                                <option value="<?= (int) $tech['id'] ?>">
                                                    <?= esc($tech['full_name'] ?? 'Technician #' . $tech['id']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input class="form-control" id="woStartDate" type="date">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input class="form-control" id="woEndDate" type="date">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" id="woDescription" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                type="button">Cancel</button>
                            <button class="btn btn-primary" id="saveWorkOrderBtn" type="submit">Save Work Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Inspection Report Modal-->
        <div class="modal fade" id="inspectionReportModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content" style="background:#0E1630;border:1px solid rgba(255,255,255,.12);border-radius:16px;">
                    <div class="modal-header" style="background:linear-gradient(135deg,rgba(124,58,237,.9),rgba(34,211,238,.8));border-bottom:none;border-radius:16px 16px 0 0;">
                        <h5 class="modal-title fw-bold text-white">
                            <i class="fa-solid fa-file-pdf me-2"></i>Inspection Report Preview
                        </h5>
                        <button aria-label="Close" class="btn-close btn-close-white" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <div class="modal-body p-0" id="reportContent"></div>
                    <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,.08);background:rgba(7,10,18,.4);border-radius:0 0 16px 16px;">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-primary" id="adminReportDownloadBtn" type="button">
                            <i class="fa-solid fa-download me-1"></i> Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Device Modal-->
        <div aria-hidden="true" aria-labelledby="addDeviceModalLabel" class="modal fade" id="addDeviceModal"
            tabindex="-1">
            <div class="modal-dialog modal-lg ">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="addDeviceModalLabel">Add New Device</h5>
                            <div class="text-muted small">Select a device from the equipment database, then save it to
                                site inventory and this inspection.</div>
                        </div>
                        <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <form id="addDeviceForm">
                        <div class="modal-body">
                            <div class="alert alert-warning d-none" id="addDeviceError"></div>
                            <div class="row g-3">
                                <!-- Primary identifiers: asset and model -->
                                <div class="col-md-4">
                                    <label class="form-label">Asset #</label>
                                    <input class="form-control" id="addAsset" required="" type="text">
                                </div>
                                <div class="col-md-4 position-relative">
                                    <label class="form-label">Model Number</label>
                                    <!-- Remove datalist on the model input and provide a custom suggestions dropdown -->
                                    <input autocomplete="off" class="form-control" id="addModel"
                                        placeholder="Start typing..." type="text">
                                    <!-- Custom suggestion list for models with manufacturer -->
                                    <div class="list-group position-absolute w-100 d-none" id="modelSuggestions"
                                        style="z-index: 2000; max-height: 250px; overflow-y: auto; top: calc(100% + 2px); left: 0;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Serial #</label>
                                    <input class="form-control" id="addSerial" placeholder="Optional" type="text">
                                </div>
                                <!-- Secondary details -->
                                <div class="col-md-6">
                                    <label class="form-label">Manufacturer</label>
                                    <input autocomplete="off" class="form-control" id="addManufacturer"
                                        list="manufacturerOptions" placeholder="Start typing...">
                                    <datalist id="manufacturerOptions"></datalist>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Type</label>
                                    <input class="form-control" id="addType" placeholder="Optional" type="text">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <input class="form-control" id="addDescription"
                                        placeholder="Auto-filled when model is selected" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <input class="form-control" id="addDept" placeholder="e.g. ICU" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Room</label>
                                    <input class="form-control" id="addRoom" placeholder="e.g. Room 2" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tech</label>
                                    <input class="form-control" id="addTech" type="text" value="Admin">
                                </div>
                                <!-- EST and CAL: auto-filled from equipment settings based on selected model -->
                                <div class="col-md-4">
                                    <label class="form-label">EST</label>
                                    <select class="form-select" id="addEST">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected="">No</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">CAL</label>
                                    <select class="form-select" id="addCAL">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected="">No</option>
                                    </select>
                                </div>
                                <!-- <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control d-none" id="addNotes" placeholder="Optional" rows="3"></textarea>
                                </div> -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                type="button">Cancel</button>
                            <button class="btn btn-primary" type="submit">
                                <i class="fa-solid fa-plus me-2"></i>Save Device
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit Device Modal -->
        <div aria-hidden="true" aria-labelledby="editModalLabel" class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Device</h5>
                        <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <form id="editInspectedForm">
                        <input type="hidden" id="editInspId">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label" for="editModel">Model</label>
                                    <input class="form-control" id="editModel" required="" type="text">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="editType">Type</label>
                                    <input class="form-control" id="editType" required="" type="text">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="editSerial">S/N</label>
                                    <input class="form-control" id="editSerial" type="text">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="editAsset">Asset #</label>
                                    <input class="form-control" id="editAsset" required="" type="text">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="editDept">Department</label>
                                    <input class="form-control" id="editDept" type="text">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="editRoom">Room</label>
                                    <input class="form-control" id="editRoom" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="editTech">Tech</label>
                                    <input class="form-control" id="editTech" type="text">
                                </div>
                                <!-- EST and CAL removed - managed via Equipment Settings -->
                                <div class="col-12">
                                    <label class="form-label" for="editNotes">Notes</label>
                                    <textarea class="form-control" id="editNotes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">
                                Cancel
                            </button>
                            <button class="btn btn-primary" type="submit">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- ================================================================
             Edit & Delete handlers for Inspected Items
             ================================================================ -->

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // ── 1. NOT INSPECTED ─────────────────────────────────────────────────────
                var notInspectedSearch = document.getElementById('notInspectedSearch');
                if (notInspectedSearch) {
                    notInspectedSearch.addEventListener('input', function() {
                        var query = this.value.toLowerCase().trim();
                        var rows = document.querySelectorAll('#notInspectedTableBody tr[data-asset]');
                        var visible = 0;

                        rows.forEach(function(row) {
                            // Respect rows already hidden by filterNotInspectedByGroup()
                            // (those have data-group-hidden="1")
                            var groupHidden = row.getAttribute('data-group-hidden') === '1';
                            if (groupHidden) {
                                row.style.display = 'none';
                                return;
                            }

                            if (!query) {
                                row.style.display = '';
                                visible++;
                                return;
                            }

                            // Search only visible columns — td index matches table header order:
                            // 0=Action(btn) 1=Asset# 2=Model 3=Type 4=Dept/Room 5=Status
                            var asset = (row.querySelector('td:nth-child(2)') || {}).textContent || '';
                            var model = (row.querySelector('td:nth-child(3)') || {}).textContent || '';
                            var type = (row.querySelector('td:nth-child(4)') || {}).textContent || '';
                            var dept = (row.querySelector('td:nth-child(5)') || {}).textContent || '';

                            var match = [asset, model, type, dept].join(' ').toLowerCase().includes(query);
                            row.style.display = match ? '' : 'none';
                            if (match) visible++;
                        });

                        // Update badge and message to reflect search results
                        var badge = document.getElementById('not-inspected-count');
                        if (badge) badge.textContent = visible;
                        var msg = document.getElementById('not-inspected-message');
                        if (msg) msg.textContent = visible + ' item(s) remaining in site inventory pending inspection.';

                        // Empty row
                        var emptyRow = document.querySelector('#notInspectedTableBody tr:not([data-asset])');
                        if (emptyRow) emptyRow.style.display = (visible === 0) ? '' : 'none';
                    });
                }

                // ── 2. INSPECTED ITEMS ───────────────────────────────────────────────────
                var inspectedSearch = document.getElementById('inspectedSearch');
                if (inspectedSearch) {
                    inspectedSearch.addEventListener('input', function() {
                        var query = this.value.toLowerCase().trim();
                        var currentGroup = window.CURRENT_INSPECTION_GROUP_ID || '';
                        var rows = document.querySelectorAll('#inspectionTableBody tr[data-row-id]');
                        var visible = 0;

                        rows.forEach(function(row) {
                            // Only operate on rows that belong to the current inspection group
                            var rowGroup = row.getAttribute('data-group-id') || '';
                            if (rowGroup !== currentGroup) {
                                row.style.display = 'none';
                                return;
                            }

                            if (!query) {
                                row.style.display = '';
                                visible++;
                                return;
                            }

                            // Col order: 0=Actions 1=Brand 2=Model 3=Type 4=S/N
                            //            5=Asset# 6=Dept/Room 7=Tech 8=Result 9=Notes 10=InspDate
                            var brand = (row.querySelector('td:nth-child(2)') || {}).textContent || '';
                            var model = (row.querySelector('td:nth-child(3)') || {}).textContent || '';
                            var type = (row.querySelector('td:nth-child(4)') || {}).textContent || '';
                            var serial = (row.querySelector('td:nth-child(5)') || {}).textContent || '';
                            var asset = (row.querySelector('td:nth-child(6)') || {}).textContent || '';
                            var dept = (row.querySelector('td:nth-child(7)') || {}).textContent || '';
                            var tech = (row.querySelector('td:nth-child(8)') || {}).textContent || '';
                            var result = (row.querySelector('td:nth-child(9)') || {}).textContent || '';
                            var notes = (row.querySelector('td:nth-child(10)') || {}).textContent || '';

                            var match = [brand, model, type, serial, asset, dept, tech, result, notes]
                                .join(' ').toLowerCase().includes(query);

                            row.style.display = match ? '' : 'none';
                            if (match) visible++;
                        });

                        // Update inspected badge count
                        var badge = document.getElementById('inspected-count');
                        if (badge) badge.textContent = visible;

                        // Empty placeholder row
                        var emptyRow = document.getElementById('inspectedEmptyRow');
                        if (emptyRow) emptyRow.style.display = (visible === 0) ? '' : 'none';
                    });
                }

                // ── 3. ARCHIVED ITEMS ────────────────────────────────────────────────────
                var archivedSearch = document.getElementById('archivedSearch');
                if (archivedSearch) {
                    archivedSearch.addEventListener('input', function() {
                        var query = this.value.toLowerCase().trim();
                        var rows = document.querySelectorAll('#archivedTableBody tr[data-asset]');
                        var visible = 0;

                        rows.forEach(function(row) {
                            if (!query) {
                                row.style.display = '';
                                visible++;
                                return;
                            }

                            // Col order: 0=Asset# 1=Model 2=Type 3=Dept/Room 4=Status
                            var asset = (row.querySelector('td:nth-child(1)') || {}).textContent || '';
                            var model = (row.querySelector('td:nth-child(2)') || {}).textContent || '';
                            var type = (row.querySelector('td:nth-child(3)') || {}).textContent || '';
                            var dept = (row.querySelector('td:nth-child(4)') || {}).textContent || '';

                            var match = [asset, model, type, dept].join(' ').toLowerCase().includes(query);
                            row.style.display = match ? '' : 'none';
                            if (match) visible++;
                        });

                        var emptyRow = document.querySelector('#archivedTableBody tr:not([data-asset])');
                        if (emptyRow) emptyRow.style.display = (visible === 0) ? '' : 'none';
                    });
                }

                // ── 4. ALL INVENTORY ─────────────────────────────────────────────────────
                var allInventorySearch = document.getElementById('allInventorySearch');
                if (allInventorySearch) {
                    allInventorySearch.addEventListener('input', function() {
                        var query = this.value.toLowerCase().trim();
                        var rows = document.querySelectorAll('#allInventoryTableBody tr[data-asset]');
                        var visible = 0;

                        rows.forEach(function(row) {
                            if (!query) {
                                row.style.display = '';
                                visible++;
                                return;
                            }

                            // Col order: 0=Action(btn) 1=Asset# 2=Model 3=Type 4=Department 5=Status
                            var asset = (row.querySelector('td:nth-child(2)') || {}).textContent || '';
                            var model = (row.querySelector('td:nth-child(3)') || {}).textContent || '';
                            var type = (row.querySelector('td:nth-child(4)') || {}).textContent || '';
                            var dept = (row.querySelector('td:nth-child(5)') || {}).textContent || '';
                            var status = (row.querySelector('td:nth-child(6)') || {}).textContent || '';

                            var match = [asset, model, type, dept, status].join(' ').toLowerCase().includes(query);
                            row.style.display = match ? '' : 'none';
                            if (match) visible++;
                        });

                        var emptyRow = document.querySelector('#allInventoryTableBody tr:not([data-asset])');
                        if (emptyRow) emptyRow.style.display = (visible === 0) ? '' : 'none';
                    });
                }

            });
            /**
             * Switch from the inspections list dashboard to the detail workflow view
             * for a specific inspection group. Populates the dynamic header elements
             * with real data from the PHP-rendered table row.
             *
             * @param {string} groupId       The unique inspection group identifier
             * @param {string} siteName      The site name to display in the badge
             * @param {string} inspType      The inspection type / title
             * @param {string} inspDisplayId The formatted display ID (INSP-YYYYMMDD-XXXXX)
             * @param {string} techName      Name of the technician assigned
             */
            // ── Event delegation for View buttons (handles special chars safely) ──
           function setInspectionStatusUI(status) {
                var btn = document.getElementById('statusDropdown');
                if (!btn) return;

                var normalized = String(status || '').trim().toLowerCase();
                var isClosed =
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

            document.addEventListener('click', function(e) {
                var btn = e.target.closest('.btn-view-insp');
                if (!btn) return;

                var groupId = btn.dataset.group || '';
                var siteName = btn.dataset.site || '';
                var inspType = btn.dataset.title || btn.dataset.type || 'Equipment Inspection';
                var inspId = btn.dataset.inspid || groupId;
                var techName = btn.dataset.tech || '—';

                // safest source: read visible row status first, then fallback to data-status
                var currentStatus = 'In Progress';
                var row = btn.closest('tr');
                if (row) {
                    var statusTd = row.querySelector('td:nth-child(5)');
                    var statusText = statusTd ? statusTd.textContent.trim().toLowerCase() : '';
                    if (statusText.indexOf('closed') !== -1 || statusText.indexOf('complete') !== -1) {
                        currentStatus = 'Closed/Complete';
                    }
                }

                if (btn.dataset.status) {
                    var ds = String(btn.dataset.status).trim().toLowerCase();
                    if (ds === 'closed/complete' || ds === 'closed' || ds === 'complete') {
                        currentStatus = 'Closed/Complete';
                    }
                }

                var detailWrap = document.getElementById('view-inspection');
                if (detailWrap) {
                    detailWrap.setAttribute('data-current-status', currentStatus);
                }

                viewInspection(groupId, siteName, inspType, inspId, techName, currentStatus);
            });

            function viewInspection(groupId, siteName, inspType, inspDisplayId, techName, currentStatus) {
                var siteLabel = document.getElementById('insp-site-label');
                var titleEl = document.getElementById('insp-title');
                var idLabel = document.getElementById('insp-id-label');
                var techEl = document.getElementById('insp-technician');
                var inspView = document.getElementById('view-inspection');
                var dashView = document.getElementById('view-dashboard');

                if (siteLabel) siteLabel.textContent = 'Site: ' + (siteName || '—');
                if (titleEl) titleEl.textContent = inspType || 'Equipment Inspection';
                if (idLabel) idLabel.textContent = 'Inspection #' + (inspDisplayId || groupId);
                if (techEl) techEl.textContent = techName || '—';

                window._savedInspTitle = inspType || 'Equipment Inspection';
                window.CURRENT_INSPECTION_GROUP_ID = groupId;

                if (inspView) {
                    inspView.setAttribute('data-current-status', currentStatus || 'In Progress');
                }

                filterInspectedByGroup(groupId);

                ['notInspectedSearch', 'inspectedSearch', 'archivedSearch', 'allInventorySearch', 'workOrdersSearch']
                    .forEach(function(id) {
                        var el = document.getElementById(id);
                        if (el) el.value = '';
                    });

                showDashboard();

                if (dashView) dashView.classList.add('d-none-view');
                if (inspView) inspView.classList.remove('d-none-view');

                function repaintStatus() {
                    var savedStatus = inspView ? inspView.getAttribute('data-current-status') : currentStatus;
                    setInspectionStatusUI(savedStatus || 'In Progress');
                }

                // paint more than once in case another script resets the button
                repaintStatus();
                requestAnimationFrame(repaintStatus);
                setTimeout(repaintStatus, 80);
                setTimeout(repaintStatus, 200);

                var workflow = document.getElementById('site-inspection-workflow');
                if (workflow) {
                    workflow.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
            /**
             * Show only Inspected Items rows and Device Type Counter rows that belong
             * to the given inspection group_id. All other rows are hidden so the tab
             * shows exactly what was done in this inspection session.
             *
             * @param {string} groupId  e.g. "INSP-20260223-8086AD66"
             */
            function filterInspectedByGroup(groupId) {
                // ── Inspected Items table rows ───────────────────────────────────────
                var inspRows = document.querySelectorAll('#inspectionTableBody tr[data-group-id]');
                var hasExactMatch = false;

                // First pass: check if ANY row has an exact match for this groupId
                inspRows.forEach(function(row) {
                    if (row.getAttribute('data-group-id') === groupId) {
                        hasExactMatch = true;
                    }
                });

                var visibleCount = 0;
                inspRows.forEach(function(row) {
                    var rowGroup = row.getAttribute('data-group-id');
                    // Show row if: exact group match OR (no exact matches exist AND row has empty group_id)
                    var show = (rowGroup === groupId) || (!hasExactMatch && rowGroup === '');
                    row.style.display = show ? '' : 'none';
                    if (show) visibleCount++;
                });

                // Show empty placeholder if nothing is visible
                var emptyRow = document.querySelector('#inspectionTableBody tr:not([data-group-id])');
                if (emptyRow) emptyRow.style.display = (visibleCount === 0) ? '' : 'none';
                var emptyRowById = document.getElementById('inspectedEmptyRow');
                if (emptyRowById) emptyRowById.style.display = (visibleCount === 0) ? '' : 'none';

                // ── Device Type Counter rows ─────────────────────────────────────────
                var counterRows = document.querySelectorAll('#deviceTypeCountsBody tr[data-group-id]');
                var hasExactCounterMatch = false;
                counterRows.forEach(function(row) {
                    if (row.getAttribute('data-group-id') === groupId) {
                        hasExactCounterMatch = true;
                    }
                });

                // Build a map of equipment EST/CAL values from the equipment settings
                // This data should be embedded in the page or fetched from server
                var equipmentMap = {};

                // Try to get equipment data from page (if available)
                var equipmentDataElement = document.getElementById('equipmentSettingsData');
                if (equipmentDataElement) {
                    try {
                        equipmentMap = JSON.parse(equipmentDataElement.textContent);
                    } catch (e) {
                        console.warn('Could not parse equipment settings data:', e);
                    }
                }

                // Calculate EST/CAL counts from inspected items
                // Read EST/CAL directly from row data attributes (data-est and data-cal)
                var deviceTypeCounts = {};
                var visibleInspRows = document.querySelectorAll('#inspectionTableBody tr[data-group-id]');
                visibleInspRows.forEach(function(row) {
                    var deviceType = row.getAttribute('data-device-type') || 'Unknown';
                    var estAttr = row.getAttribute('data-est') || '0'; // Read from row attribute
                    var calAttr = row.getAttribute('data-cal') || '0'; // Read from row attribute

                    if (!deviceTypeCounts[deviceType]) {
                        deviceTypeCounts[deviceType] = {
                            total: 0,
                            est: 0,
                            cal: 0
                        };
                    }

                    deviceTypeCounts[deviceType].total++;

                    // Check EST value from row attribute
                    // Can be: 1, '1', 'Yes', 'yes', or 0, '0', 'No'
                    var estVal = estAttr.toString().toLowerCase();
                    if (estVal === '1' || estVal === 'yes' || estVal === 'true') {
                        deviceTypeCounts[deviceType].est++;
                    }

                    // Check CAL value from row attribute
                    // Can be: 1, '1', 'Yes', 'yes', or 0, '0', 'No'
                    var calVal = calAttr.toString().toLowerCase();
                    if (calVal === '1' || calVal === 'yes' || calVal === 'true') {
                        deviceTypeCounts[deviceType].cal++;
                    }
                });

                // Update counter table rows with calculated values
                var counterVisible = 0;
                var counterTotal = 0;
                var counterTotalEST = 0;
                var counterTotalCAL = 0;
                counterRows.forEach(function(row) {
                    var rowGroup = row.getAttribute('data-group-id');
                    var show = (rowGroup === groupId) || (!hasExactCounterMatch && rowGroup === '');
                    row.style.display = show ? '' : 'none';
                    if (show) {
                        counterVisible++;
                        var cells = row.querySelectorAll('td');
                        var deviceTypeCell = cells[0];
                        var deviceType = deviceTypeCell ? deviceTypeCell.textContent.trim() : '';

                        // Get calculated counts from deviceTypeCounts
                        var counts = deviceTypeCounts[deviceType] || {
                            total: 0,
                            est: 0,
                            cal: 0
                        };
                        counterTotal += counts.total;
                        counterTotalEST += counts.est;
                        counterTotalCAL += counts.cal;

                        // Update cell display with calculated values
                        if (cells[1]) cells[1].textContent = counts.total;
                        if (cells[2]) cells[2].textContent = counts.est;
                        if (cells[3]) cells[3].textContent = counts.cal;
                    }
                });
                var counterEmpty = document.getElementById('deviceCountEmptyRow');
                if (counterEmpty) counterEmpty.style.display = (counterVisible === 0) ? '' : 'none';

                // Show/update the Total row
                var totalRow = document.getElementById('deviceCountTotalRow');
                var totalCell = document.getElementById('deviceCountTotal');
                var totalCellEST = document.getElementById('deviceCountTotalEST');
                var totalCellCAL = document.getElementById('deviceCountTotalCAL');
                if (totalRow) totalRow.style.display = (counterVisible > 0) ? '' : 'none';
                if (totalCell) totalCell.textContent = counterTotal;
                if (totalCellEST) totalCellEST.textContent = counterTotalEST;
                if (totalCellCAL) totalCellCAL.textContent = counterTotalCAL;

                // ── Update the "Inspected Items" tab badge count ─────────────────────
                var badge = document.getElementById('inspected-count');
                if (badge) badge.textContent = visibleCount > 0 ? visibleCount : '0';

                // ── Work Orders: filter strictly by group_id ──────────────────────────
                var woRows = document.querySelectorAll('#workOrdersTableBody tr[data-group-id]');
                var woVisible = 0;
                woRows.forEach(function(row) {
                    var rg = row.getAttribute('data-group-id');
                    var show = (rg === groupId);
                    row.style.display = show ? '' : 'none';
                    if (show) woVisible++;
                });
                var woEmpty = document.querySelector('#workOrdersTableBody tr:not([data-group-id])');
                if (woEmpty) woEmpty.style.display = (woVisible === 0) ? '' : 'none';

                // ── Not Inspected: hide rows whose asset_tag appears in inspected list ─
                filterNotInspectedByGroup(groupId);
            }

            // Filter not-inspected rows by removing already-inspected assets for this group
            function filterNotInspectedByGroup(groupId) {
                // Build a set of asset_tags that ARE inspected in this group
                var inspectedAssets = {};
                document.querySelectorAll('#inspectionTableBody tr[data-group-id]').forEach(function(r) {
                    if (r.getAttribute('data-group-id') === groupId) {
                        var a = r.getAttribute('data-asset');
                        if (a) inspectedAssets[a.toLowerCase()] = true;
                    }
                });
                var rows = document.querySelectorAll('#notInspectedTableBody tr[data-asset]');
                var rem = 0;
                rows.forEach(function(r) {
                    var asset = (r.getAttribute('data-asset') || '').toLowerCase();
                    var done = inspectedAssets[asset];
                    r.setAttribute('data-group-hidden', done ? '1' : '0');
                    if (!done) rem++;
                });
                var badge = document.getElementById('not-inspected-count');
                if (badge) badge.textContent = rem;
                var msg = document.getElementById('not-inspected-message');
                if (msg) msg.textContent = rem + ' item(s) remaining in site inventory pending inspection.';
            }
            window.filterNotInspectedByGroup = filterNotInspectedByGroup;

            // Configuration constants for report endpoints
            const REPORT_DATA_URL = "<?= site_url('admin/inspections/reportData') ?>";
            const REPORT_PDF_URL = "<?= site_url('admin/inspections/reportPdf') ?>";
            const LOGO_BASE_URL = "<?= base_url('uploads/logos') ?>";
            const SEARCH_MODEL_URL = "<?= site_url('admin/inspections/searchByModel') ?>";

            // ── Add Device Modal: Model Number Autocomplete ──────────────────────────────
            (function() {
                var _modelTimer = null;

                function showModelSuggestions(results) {
                    var box = document.getElementById('modelSuggestions');
                    if (!box) return;
                    if (!results || results.length === 0) {
                        box.classList.add('d-none');
                        box.innerHTML = '';
                        return;
                    }
                    box.innerHTML = results.map(function(r) {
                        var label = [r.make, r.model].filter(Boolean).join(' — ');
                        var sub = r.device_type || '';
                        return '<button type="button" class="list-group-item list-group-item-action py-2 px-3 model-suggestion-item"' +
                            ' data-make="' + escapeHtml(r.make || '') + '"' +
                            ' data-model="' + escapeHtml(r.model || '') + '"' +
                            ' data-device_type="' + escapeHtml(r.device_type || '') + '"' +
                            ' data-department="' + escapeHtml(r.department || '') + '"' +
                            ' data-location="' + escapeHtml(r.location || '') + '"' +
                            ' data-est="' + escapeHtml(r.est || 'No') + '"' +
                            ' data-cal="' + escapeHtml(r.cal || 'No') + '"' +
                            '>' +
                            '<span class="fw-semibold">' + escapeHtml(label) + '</span>' +
                            (sub ? '<br><small class="text-muted">' + escapeHtml(sub) + '</small>' : '') +
                            '</button>';
                    }).join('');
                    box.classList.remove('d-none');
                }

                function hideModelSuggestions() {
                    var box = document.getElementById('modelSuggestions');
                    if (box) {
                        box.classList.add('d-none');
                        box.innerHTML = '';
                    }
                }

                function fillFromModel(btn) {
                    document.getElementById('addModel').value = btn.getAttribute('data-model') || '';
                    document.getElementById('addManufacturer').value = btn.getAttribute('data-make') || '';
                    document.getElementById('addType').value = btn.getAttribute('data-device_type') || '';
                    document.getElementById('addDescription').value = btn.getAttribute('data-device_type') || '';
                    // Lock the model field to read-only once a model has been selected
                    var addModelEl = document.getElementById('addModel');
                    if (addModelEl) {
                        addModelEl.setAttribute('readonly', 'readonly');
                        addModelEl.classList.add('bg-light');
                        addModelEl.title = 'Model locked. Clear field to change.';
                    }
                    // Serial # intentionally NOT auto-populated — technician must fill manually
                    // Only fill department/room if empty (don't overwrite user entries)
                    var deptEl = document.getElementById('addDept');
                    var roomEl = document.getElementById('addRoom');
                    if (deptEl && !deptEl.value) deptEl.value = btn.getAttribute('data-department') || '';
                    if (roomEl && !roomEl.value) roomEl.value = btn.getAttribute('data-location') || '';

                    // Set EST and CAL dropdowns with proper value conversion
                    var estVal = btn.getAttribute('data-est') || 'No';
                    var calVal = btn.getAttribute('data-cal') || 'No';
                    estVal = convertToYesNo(estVal);
                    calVal = convertToYesNo(calVal);
                    var estSel = document.getElementById('addEST');
                    var calSel = document.getElementById('addCAL');
                    if (estSel) estSel.value = estVal;
                    if (calSel) calSel.value = calVal;

                    hideModelSuggestions();
                }

                document.addEventListener('DOMContentLoaded', function() {
                    var inp = document.getElementById('addModel');
                    var box = document.getElementById('modelSuggestions');
                    if (!inp || !box) return;

                    // Typing → debounce search
                    inp.addEventListener('input', function() {
                        var q = inp.value.trim();
                        clearTimeout(_modelTimer);
                        if (q.length < 2) {
                            hideModelSuggestions();
                            return;
                        }
                        _modelTimer = setTimeout(function() {
                            fetch(SEARCH_MODEL_URL + '?keyword=' + encodeURIComponent(q), {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(function(r) {
                                    return r.json();
                                })
                                .then(function(data) {
                                    showModelSuggestions(data || []);
                                })
                                .catch(function() {
                                    hideModelSuggestions();
                                });
                        }, 220);
                    });

                    // Click a suggestion
                    box.addEventListener('mousedown', function(e) {
                        var btn = e.target.closest('.model-suggestion-item');
                        if (btn) {
                            e.preventDefault();
                            fillFromModel(btn);
                        }
                    });

                    // Hide on blur
                    inp.addEventListener('blur', function() {
                        setTimeout(hideModelSuggestions, 160);
                    });

                    // Keyboard navigation
                    inp.addEventListener('keydown', function(e) {
                        var items = box.querySelectorAll('.model-suggestion-item');
                        var active = box.querySelector('.model-suggestion-item.active');
                        if (e.key === 'ArrowDown') {
                            e.preventDefault();
                            if (!active) {
                                if (items[0]) items[0].classList.add('active');
                            } else {
                                items.forEach(function(el) {
                                    el.classList.remove('active');
                                });
                                var next = active.nextElementSibling;
                                if (next) next.classList.add('active');
                            }
                        } else if (e.key === 'ArrowUp') {
                            e.preventDefault();
                            if (active) {
                                var prev = active.previousElementSibling;
                                items.forEach(function(el) {
                                    el.classList.remove('active');
                                });
                                if (prev) prev.classList.add('active');
                            }
                        } else if (e.key === 'Enter') {
                            if (active) {
                                e.preventDefault();
                                fillFromModel(active);
                            }
                        } else if (e.key === 'Escape') {
                            hideModelSuggestions();
                        }
                    });
                });

                function escapeHtml(s) {
                    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                        '&quot;');
                }
            })();
            // Tracks the currently loaded report group; used when downloading PDFs
            let CURRENT_REPORT_GROUP_ID = null;

            /**
             * Open the Inspection Report modal, fetch data via AJAX, render it,
             * and wire up the Preview + Download PDF buttons in the modal footer.
             */
            function openInspectionReport(groupId) {
                CURRENT_REPORT_GROUP_ID = groupId;
                window.CURRENT_REPORT_GROUP_ID = groupId;

                var reportContent = document.getElementById('reportContent');
                var modalEl = document.getElementById('inspectionReportModal');
                if (!reportContent || !modalEl) return;

                // Show spinner then open modal — same flow as technician portal
                reportContent.innerHTML =
                    '<div class="text-center py-5">' +
                    '<i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>' +
                    '<p class="mt-3 text-muted">Loading report...</p>' +
                    '</div>';
                bootstrap.Modal.getOrCreateInstance(modalEl).show();

                // Fetch reportPdf (same endpoint technician uses) and render in iframe
                fetch('<?= site_url("admin/inspections/reportPdf") ?>/' + encodeURIComponent(groupId), {
                        headers: {
                            'Accept': 'text/html'
                        }
                    })
                    .then(function(r) {
                        return r.text();
                    })
                    .then(function(html) {
                        var iframe = document.createElement('iframe');
                        iframe.style.cssText = 'width:100%;height:72vh;border:none;background:#fff;';
                        reportContent.innerHTML = '';
                        reportContent.appendChild(iframe);
                        iframe.contentDocument.open();
                        iframe.contentDocument.write(html);
                        iframe.contentDocument.close();
                    })
                    .catch(function() {
                        reportContent.innerHTML =
                            '<div class="alert alert-danger m-3">' +
                            '<i class="fa-solid fa-triangle-exclamation me-2"></i>Failed to load report.</div>';
                    });

                // Wire download button
                var dlBtn = document.getElementById('adminReportDownloadBtn');
                if (dlBtn) {
                    dlBtn.onclick = function() {
                        window.open('<?= site_url("admin/inspections/reportPdf") ?>/' + encodeURIComponent(groupId), '_blank');
                    };
                }
            }

            function renderWorkOrdersTable() {
                var query = (document.getElementById('workOrdersSearch').value || '').toLowerCase().trim();
                var rows = document.querySelectorAll('#workOrdersTableBody tr[data-row-id]');
                var emptyRow = document.querySelector('#workOrdersTableBody tr:not([data-row-id])');
                var visible = 0;

                rows.forEach(function(row) {
                    // Only search within rows that belong to the current inspection group
                    var groupId = row.getAttribute('data-group-id') || '';
                    if (window.CURRENT_INSPECTION_GROUP_ID && groupId !== window.CURRENT_INSPECTION_GROUP_ID) {
                        return; // already hidden by filterInspectedByGroup — skip
                    }
                    var text = row.textContent.toLowerCase();
                    var show = !query || text.includes(query);
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                if (emptyRow) emptyRow.style.display = (visible === 0) ? '' : 'none';
            }
            /**
             * Generate a site/inspection header for PDF output.
             */
            function generateReportHeaderHTML() {
                var getText = function(id) {
                    var el = document.getElementById(id);
                    return el ? el.textContent.trim() : '';
                };
                var siteName = getText('insp-site-label') || 'Site Name';
                var inspTitle = getText('insp-title') || 'Inspection';
                var inspId = getText('insp-id-label') || '';
                var tech = getText('insp-technician') || '';
                return '<div style="margin-bottom:16px;border-bottom:2px solid #e2e8f0;padding-bottom:12px;">' +
                    '<div style="display:flex;justify-content:space-between;align-items:center;">' +
                    '<div><div style="font-size:18px;font-weight:700;">' + escapeHtml(siteName) + '</div>' +
                    '<div style="font-size:13px;color:#334155;">' + escapeHtml(inspTitle) + '</div></div>' +
                    '<div style="text-align:right;">' +
                    '<div style="font-size:13px;font-weight:600;">Inspection #: ' + escapeHtml(inspId) + '</div>' +
                    '<div style="font-size:12px;color:#64748b;">Technician: ' + escapeHtml(tech) + '</div>' +
                    '</div></div></div>';
            }

            // NOTE: previewReportPDF() and exportReportPDF() are defined in details.php

            // Tab Preview: open current group in the styled modal (iframe)
            window.adminTabPreviewReport = function() {
                var groupId = window.CURRENT_REPORT_GROUP_ID;
                if (!groupId) {
                    alert('Click the report icon on an inspection row first.');
                    return;
                }
                openInspectionReport(groupId);
            };

            window.adminTabDownloadReport = function() {
                var groupId = window.CURRENT_REPORT_GROUP_ID;
                if (!groupId) {
                    alert('Click the report icon on an inspection row first.');
                    return;
                }
                window.open(REPORT_PDF_URL + '/' + encodeURIComponent(groupId), '_blank');
            };

            window.previewReportPDF = window.adminTabPreviewReport;
            window.exportReportPDF = window.adminTabDownloadReport;

            /* ══════════════════════════════════════════════════════════════
               INSPECTION TITLE — database persistence
               Saves the user-edited title to all inspections in the group.
               ══════════════════════════════════════════════════════════════ */
            window.saveInspTitle = function(el) {
                el.style.borderBottomColor = 'transparent';
                var title = (el.textContent || '').trim();
                if (!title || title === '—') return;
                window._savedInspTitle = title;
                var gid = window.CURRENT_INSPECTION_GROUP_ID || window.CURRENT_REPORT_GROUP_ID;
                if (!gid) return;
                // Save to database via AJAX
                var fd = new FormData();
                fd.append('group_id', gid);
                fd.append('title', title);
                fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                fetch('<?= site_url("admin/inspections/updateGroupTitle") ?>', {
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
                        var badge = document.getElementById('insp-title-saved');
                        if (badge) {
                            badge.style.opacity = '1';
                            badge.textContent = res.success ? '✓ saved' : '✗ error';
                            badge.style.color = res.success ? '#22c55e' : '#ef4444';
                            setTimeout(function() {
                                badge.style.opacity = '0';
                            }, 2000);
                        }
                    })
                    .catch(function() {
                        var badge = document.getElementById('insp-title-saved');
                        if (badge) {
                            badge.textContent = '✗ error';
                            badge.style.color = '#ef4444';
                            badge.style.opacity = '1';
                            setTimeout(function() {
                                badge.style.opacity = '0';
                            }, 2000);
                        }
                    });
            };

            window.restoreInspTitle = function(groupId) {
                if (!window._savedInspTitle) return;
                var titleEl = document.getElementById('insp-title');
                if (titleEl && window._savedInspTitle) titleEl.textContent = window._savedInspTitle;
            };
            // and support customer logos. Do NOT redefine them here as that would
            // override the implementations that include logo support.

            /**
             * Build the inner HTML for the inspection report (Latest Device + Overview table).
             */
            /**
             * Renders the inspection report inside a given container element
             * using an iframe that loads the clean reportPreview HTML — same
             * design as the technician portal.
             *
             * @param {string} groupId
             * @param {HTMLElement} container  — the div to render into
             */
            function loadReportIntoContainer(groupId, container) {
                if (!groupId || !container) return;
                container.innerHTML =
                    '<div class="text-center py-5">' +
                    '<i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>' +
                    '<p class="mt-3 text-muted">Loading report…</p>' +
                    '</div>';

                var previewUrl = '<?= site_url("admin/inspections/reportPreview") ?>/' + encodeURIComponent(groupId);
                fetch(previewUrl, {
                        headers: {
                            'Accept': 'text/html',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
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
                            '<div class="alert alert-danger m-3"><i class="fa-solid fa-triangle-exclamation me-2"></i>' +
                            'Failed to load report: ' + escapeHtml(String(err)) + '</div>';
                    });
            }

            // Keep generateInspectionReportHTML as a no-op shim so any legacy
            // callers don't throw — they now go through loadReportIntoContainer.
            function generateInspectionReportHTML(latest, rows, groupId) {
                // Trigger the clean iframe-based render if we have a groupId
                if (groupId) {
                    var tabContainer = document.getElementById('reportsTabContent');
                    if (tabContainer) loadReportIntoContainer(groupId, tabContainer);
                }
                return ''; // tab already populated above
            }



            // ── Load IQ Notes dynamically into Action Performed dropdown ────────────────
            (function loadIqNotes() {
                var IQ_NOTES_URL = "<?= site_url('admin/settings/iq-notes') ?>";
                var sel = document.getElementById('inspectActionPerformed');
                if (!sel) return;
                fetch(IQ_NOTES_URL, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(data) {
                        var notes = data.data || [];
                        if (!notes.length) return; // keep defaults if no IQ notes configured
                        // Clear existing options and repopulate from IQ notes (show title, value = note)
                        sel.innerHTML = '<option value="">-- Select Action Performed --</option>';
                        notes.forEach(function(n) {
                            var opt = document.createElement('option');
                            opt.value = n.note || n.title;
                            opt.setAttribute('data-id', n.id || '');
                            opt.setAttribute('data-title', n.title || '');
                            opt.setAttribute('data-note', n.note || '');
                            opt.textContent = n.title || n.note;
                            sel.appendChild(opt);
                        });
                        if (sel.options.length > 1) {
                            sel.selectedIndex = 0; // Fix 1: default to -- Select --
                        }
                        // When an IQ Note is selected, auto-populate the Notes textarea
                        sel.removeEventListener('change', window._iqNoteChangeHandler);
                        window._iqNoteChangeHandler = function() {
                            var selected = sel.options[sel.selectedIndex];
                            if (!selected || selected.value === '') return;
                            var noteText = selected.getAttribute('data-note') || '';
                            var notesArea = document.getElementById('inspectNotes');
                            // Always fill notes when user actively changes the dropdown
                            if (notesArea && noteText) notesArea.value = noteText;
                        };
                        sel.addEventListener('change', window._iqNoteChangeHandler);
                        // Do NOT fire immediately — notes stay blank until user selects action

                        // FIX 5: Do NOT save user-edited notes back to the master IQ table.
                        // Only update the in-memory data-note attribute so the text stays
                        // in the dropdown for the current session, but the master IQ notes
                        // record is never modified by what the inspector types here.
                        var notesArea = document.getElementById('inspectNotes');
                        if (notesArea) {
                            // Remove any previously attached blur handler
                            if (window._adminNotesBlurHandler) {
                                notesArea.removeEventListener('blur', window._adminNotesBlurHandler);
                                window._adminNotesBlurHandler = null;
                            }
                            // When a dropdown is re-selected, always reload the original
                            // note text from data-note (not any user-edited value)
                            // -- handled above by _iqNoteChangeHandler reading data-note
                        }
                    })
                    .catch(function() {
                        /* keep static defaults on error */
                    });
            })();

            // ── Auto-fill EST/CAL in Add Device modal when model is selected ─────────────
            // When a model is picked from autocomplete suggestions, look up its equipment
            // settings and set the EST/CAL dropdowns accordingly.
            document.addEventListener('DOMContentLoaded', function() {
                var box = document.getElementById('modelSuggestions');
                if (box) {
                    box.addEventListener('mousedown', function(e) {
                        var btn = e.target.closest('.model-suggestion-item');
                        if (!btn) return;
                        // After fillFromModel runs (via existing handler), also set EST/CAL
                        setTimeout(function() {
                            var estVal = btn.getAttribute('data-est') || 'No';
                            var calVal = btn.getAttribute('data-cal') || 'No';

                            // Convert 1/0 to Yes/No
                            estVal = convertToYesNo(estVal);
                            calVal = convertToYesNo(calVal);

                            var estSel = document.getElementById('addEST');
                            var calSel = document.getElementById('addCAL');
                            if (estSel) estSel.value = estVal;
                            if (calSel) calSel.value = calVal;
                        }, 50);
                    });
                }
            });

            // Helper function to convert 1/0 or Yes/No to proper Yes/No value
            function convertToYesNo(value) {
                if (!value) return 'No';
                var str = String(value).toLowerCase().trim();
                // Handle numeric values
                if (str === '1' || str === 'yes' || str === 'true') return 'Yes';
                if (str === '0' || str === 'no' || str === 'false') return 'No';
                return 'No'; // default to No
            }

            function thStyle() {
                return 'padding:10px 12px;text-align:left;font-size:11px;font-weight:600;' +
                    'color:#64748b;text-transform:uppercase;letter-spacing:0.05em;' +
                    'border-bottom:2px solid #e2e8f0;white-space:nowrap;';
            }

            function tdStyle() {
                return 'padding:12px 12px;border-bottom:1px solid #f1f5f9;vertical-align:middle;font-size:13px;';
            }

            function esc(str) {
                return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                    '&quot;');
            }


            /** Return a result badge based on the inspection outcome */
            function resultBadge(res) {
                const r = String(res || '').trim();
                if (r === 'Pass')
                    return '<span class="text-success fw-semibold"><i class="fa-solid fa-check"></i> Pass</span>';
                if (r === 'Fail')
                    return '<span class="text-danger fw-semibold"><i class="fa-solid fa-xmark"></i> Fail</span>';
                if (r === 'Repair')
                    return '<span class="text-warning fw-semibold"><i class="fa-solid fa-wrench"></i> Repair</span>';
                return '<span class="text-muted">-</span>';
            }

            /** Convert true/false or yes/no values into a formatted Yes/No string */
            function yesNo(v) {
                const val = String(v || '').toLowerCase();
                return (val === '1' || val === 'yes' || val === 'true') ?
                    '<span class="text-success"><i class="fa-solid fa-check"></i> Yes</span>' :
                    'No';
            }

            /** Format inspection date values into YYYY-MM-DD and HH:MM AM/PM strings */
            function formatInspectionDateHTML(dt) {
                const d = new Date(dt.replace(' ', 'T'));
                if (isNaN(d.getTime())) return escapeHtml(dt);
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                let hh = d.getHours();
                const ampm = hh >= 12 ? 'PM' : 'AM';
                hh = hh % 12;
                hh = hh ? hh : 12; // convert 0 to 12
                const mi = String(d.getMinutes()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}<br>${String(hh).padStart(2, '0')}:${mi} ${ampm}`;
            }

            /** Escape HTML special characters to prevent XSS when inserting dynamic text */
            function escapeHtml(str) {
                return String(str || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        </script>

        <script>
            /**
             * Silently fetch the current page in the background and update
             * the key table bodies, badge counts, and device-type counter
             * without a visible page reload. Accepts an optional callback
             * fired after the DOM has been patched.
             */
            window.backgroundRefreshTabs = function backgroundRefreshTabs(callback) {
                $.get(window.location.href, function(html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');

                    // IDs to swap wholesale
                    var ids = [
                        'inspectionTableBody', // Inspected Items rows
                        'notInspectedTableBody', // Not Inspected rows
                        'archivedTableBody', // Archived rows
                        'deviceTypeCountsBody', // Device type counter
                        'workOrdersTableBody', // Work Orders rows
                        'not-inspected-count', // Tab badge
                        'inspected-count', // Tab badge
                    ];
                    ids.forEach(function(id) {
                        var fresh = doc.getElementById(id);
                        var live = document.getElementById(id);
                        if (fresh && live) live.innerHTML = fresh.innerHTML;
                    });

                    // Fix 4: Restore user-edited title after DOM refresh
                    if (window._savedInspTitle) {
                        var titleEl = document.getElementById('insp-title');
                        if (titleEl) titleEl.textContent = window._savedInspTitle;
                    }
                    if (typeof window.restoreInspTitle === 'function') {
                        window.restoreInspTitle(window.CURRENT_INSPECTION_GROUP_ID || window.CURRENT_REPORT_GROUP_ID);
                    }

                    // Re-apply group filter so only current inspection rows stay visible
                    if (window.CURRENT_INSPECTION_GROUP_ID) {
                        // Reset filter selects on group change
                        var rf = document.getElementById('inspectedResultFilter');
                        if (rf) rf.value = '';
                        var sf = document.getElementById('inspectedSearch');
                        if (sf) sf.value = '';
                        filterInspectedByGroup(window.CURRENT_INSPECTION_GROUP_ID);
                        filterNotInspectedByGroup(window.CURRENT_INSPECTION_GROUP_ID);
                    }

                    if (typeof callback === 'function') callback();
                }); // silent fail — DOM stays as-is if fetch fails
            };

            (function() {
                'use strict';

                var CSRF_NAME = '<?= csrf_token() ?>';
                var csrfHash = '<?= csrf_hash() ?>';
                var URL_UPDATE = '<?= site_url('admin/inspections/updateInspection') ?>';
                var URL_DELETE = '<?= site_url('admin/inspections/deleteById/') ?>';

                // Bootstrap modal instance (lazy-created)
                var _bsModal = null;

                function getModal() {
                    if (!_bsModal) {
                        _bsModal = new bootstrap.Modal(document.getElementById('editModal'));
                    }
                    return _bsModal;
                }

                // ── Toast helper – reuse page-level showToast if available ──────
                function toast(msg, type) {
                    if (typeof showToast === 'function') {
                        showToast(msg, type);
                        return;
                    }
                    var c = document.getElementById('toastContainer') || document.body;
                    var div = document.createElement('div');
                    div.className = 'alert alert-' + (type === 'success' ? 'success' : type === 'warning' ? 'warning' :
                            'danger') +
                        ' shadow fade show d-flex align-items-center gap-2';
                    div.style.cssText = 'position:fixed;bottom:1rem;right:1rem;z-index:9999;min-width:280px';
                    div.textContent = msg;
                    document.body.appendChild(div);
                    setTimeout(function() {
                        div.remove();
                    }, 3500);
                }

                // ── Populate and open the modal ─────────────────────────────────
                function openEditModal(btn) {
                    var d = btn.dataset;
                    document.getElementById('editInspId').value = d.id || '';
                    document.getElementById('editModel').value = d.model || '';
                    document.getElementById('editType').value = d.type || '';
                    document.getElementById('editSerial').value = d.serial || '';
                    document.getElementById('editAsset').value = d.asset || '';
                    document.getElementById('editDept').value = d.dept || '';
                    document.getElementById('editRoom').value = d.room || '';
                    document.getElementById('editTech').value = d.tech || '';
                    // EST/CAL removed from edit form - managed via Equipment Settings
                    document.getElementById('editNotes').value = d.notes || '';
                    getModal().show();
                }

                // ── Delegated click handler on the inspected table body ─────────
                var tbody = document.getElementById('inspectionTableBody');
                if (tbody) {
                    tbody.addEventListener('click', function(e) {
                        // Edit
                        var editBtn = e.target.closest('.btn-edit-inspected');
                        if (editBtn) {
                            openEditModal(editBtn);
                            return;
                        }

                        // Delete
                        var delBtn = e.target.closest('.btn-delete-inspected');
                        if (!delBtn) return;

                        var id = delBtn.dataset.id;
                        var asset = delBtn.dataset.asset || id;

                        Swal.fire({
                            title: 'Remove Inspection Record?',
                            text: 'Inspection record for asset #' + asset + ' will be permanently removed.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, remove'
                        }).then(function(result) {
                            if (!result.isConfirmed) return;

                            var body = CSRF_NAME + '=' + encodeURIComponent(csrfHash);
                            fetch(URL_DELETE + encodeURIComponent(id), {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: body,
                                    redirect: 'follow'
                                })
                                .then(function(r) {
                                    if (r.ok || r.status === 302 || r.redirected) {
                                        var row = delBtn.closest('tr');
                                        if (row) row.remove();
                                        backgroundRefreshTabs(function() {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Removed',
                                                text: 'Inspection record deleted.',
                                                timer: 1500,
                                                showConfirmButton: false
                                            });
                                        });
                                    } else {
                                        toast('Delete failed (HTTP ' + r.status + ').', 'danger');
                                    }
                                })
                                .catch(function(err) {
                                    toast('Delete failed – ' + err.message, 'danger');
                                });
                        }); // end Swal.fire
                    });
                }
                document.addEventListener('click', function(e) {
                    var btn = e.target.closest('#workOrdersTableBody .btn-edit');
                    if (!btn) return;

                    // ... your existing code that populates other fields ...

                    // ── ADD ONLY THIS PART for technician ──────────────────────────
                    var techSelect = document.getElementById('woTech');
                    if (techSelect) {
                        // dataset converts data-tech-id → btn.dataset.techId
                        techSelect.value = btn.dataset.techId || '';

                        // Fallback: if ID not matched, try by name
                        if (!techSelect.value && btn.dataset.techName) {
                            Array.from(techSelect.options).forEach(function(opt) {
                                if (opt.textContent.trim() === btn.dataset.techName.trim()) {
                                    techSelect.value = opt.value;
                                }
                            });
                        }
                    }
                    // ── END technician fix ─────────────────────────────────────────
                });
                // ── Save Changes ────────────────────────────────────────────────
                var form = document.getElementById('editInspectedForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        var id = document.getElementById('editInspId').value;
                        if (!id) return;

                        var params = [
                            CSRF_NAME + '=' + encodeURIComponent(csrfHash),
                            'inspection_id' + '=' + encodeURIComponent(id),
                            'notes' + '=' + encodeURIComponent(document.getElementById('editNotes').value
                                .trim()),
                            'department' + '=' + encodeURIComponent(document.getElementById('editDept')
                                .value.trim()),
                            'location' + '=' + encodeURIComponent(document.getElementById('editRoom').value
                                .trim()),
                            'serial_number' + '=' + encodeURIComponent(document.getElementById('editSerial')
                                .value.trim()),
                            // est/cal removed from edit form - managed via Equipment Settings
                        ].join('&');

                        var saveBtn = form.querySelector('[type=submit]');
                        saveBtn.disabled = true;
                        saveBtn.textContent = 'Saving…';

                        fetch(URL_UPDATE, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: params
                            })
                            .then(function(r) {
                                var ct = (r.headers.get('content-type') || '').toLowerCase();
                                if (ct.includes('application/json')) return r.json();
                                return r.text().then(function(t) {
                                    throw new Error('Server returned non-JSON:\n' + t.substring(0,
                                        300));
                                });
                            })
                            .then(function(data) {
                                if (data.csrf_hash) csrfHash = data.csrf_hash;

                                if (data.success) {
                                    // Background-refresh badges, counter, not-inspected list
                                    backgroundRefreshTabs();
                                    // Refresh data-attributes on the edit button
                                    var editBtn = tbody ? tbody.querySelector(
                                        '.btn-edit-inspected[data-id="' + id + '"]') : null;
                                    if (editBtn) {
                                        editBtn.dataset.serial = document.getElementById('editSerial').value
                                            .trim();
                                        editBtn.dataset.dept = document.getElementById('editDept').value
                                            .trim();
                                        editBtn.dataset.room = document.getElementById('editRoom').value
                                            .trim();
                                        // est/cal dataset updates removed
                                        editBtn.dataset.notes = document.getElementById('editNotes').value
                                            .trim();

                                        // Live-update visible cells
                                        // Col order: 0=Actions 1=Model 2=Type 3=S/N 4=Asset
                                        //            5=Dept/Room 6=Tech 7=EST 8=CAL 9=Result 10=Notes 11=Date
                                        var row = editBtn.closest('tr');
                                        var cells = row ? row.querySelectorAll('td') : [];

                                        if (cells[3]) cells[3].textContent = document.getElementById(
                                            'editSerial').value.trim();
                                        if (cells[5]) cells[5].innerHTML =
                                            esc(document.getElementById('editDept').value.trim()) +
                                            '<br><span class="text-muted small">' +
                                            esc(document.getElementById('editRoom').value.trim()) +
                                            '</span>';
                                        // EST/CAL cell values come from DB on next refresh (not from edit form)
                                        if (cells[10]) cells[10].textContent = document.getElementById(
                                            'editNotes').value.trim();
                                    }

                                    toast('Device updated successfully.', 'success');
                                    getModal().hide();
                                } else {
                                    toast(data.message || 'Update failed.', 'danger');
                                }
                            })
                            .catch(function(err) {
                                console.error(err);
                                toast('Save failed – ' + err.message, 'danger');
                            })
                            .finally(function() {
                                saveBtn.disabled = false;
                                saveBtn.textContent = 'Save Changes';
                            });
                    });
                }

                // ── Helpers ─────────────────────────────────────────────────────
                function esc(str) {
                    return String(str || '')
                        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                }

                function yesNo(val) {
                    return val === 'Yes' ?
                        '<span class="text-success"><i class="fa-solid fa-check"></i> Yes</span>' :
                        'No';
                }

            })();

            // Auto-load the report when "Inspection Reports" tab is clicked inside view-inspection.
            // This makes the tab completely independent — no need to go back to the list first.
            document.addEventListener('DOMContentLoaded', function() {
                const woForm = document.getElementById('workOrderForm');
                if (!woForm) return;

                woForm.addEventListener('submit', function() {
                    // keep MAIN Work Orders tab selected after refresh
                    sessionStorage.setItem('siteDetailsActiveTab', 'work-orders-tab');

                    // if this submit is coming from INSPECTIONS workflow, use these instead:
                    // sessionStorage.setItem('siteDetailsActiveTab', 'inspections-tab');
                    // sessionStorage.setItem('siteDetailsActiveSubTab', 'insp-work-orders-tab');
                });

                var reportsTabBtn = document.getElementById('reports-tab');
                if (!reportsTabBtn) return;

                reportsTabBtn.addEventListener('shown.bs.tab', function() {
                    var container = document.getElementById('reportsTabContent');

                    // Skip if already loaded (iframe present)
                    if (container && container.querySelector('iframe')) return;

                    // Use current inspection group or previously opened report group
                    var groupId = window.CURRENT_INSPECTION_GROUP_ID || CURRENT_REPORT_GROUP_ID;
                    if (!groupId) {
                        if (container) container.innerHTML =
                            '<div class="alert alert-info m-3">No inspection loaded yet. Open an inspection from the list first.</div>';
                        return;
                    }

                    CURRENT_REPORT_GROUP_ID = groupId;
                    if (container) container.innerHTML =
                        '<div class="text-center py-5">' +
                        '<i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>' +
                        '<p class="mt-3 text-muted">Loading report...</p></div>';

                    fetch('<?= site_url("admin/inspections/reportPdf") ?>/' + encodeURIComponent(groupId) + '?inline=1', {
                            headers: {
                                'Accept': 'text/html'
                            }
                        })
                        .then(function(r) {
                            return r.text();
                        })
                        .then(function(html) {
                            if (!container) return;
                            container.innerHTML = '';
                            var iframe = document.createElement('iframe');
                            iframe.style.cssText = 'width:100%;height:72vh;border:none;background:#0E1630;border-radius:0 0 10px 10px;';
                            var wrapper = document.createElement('div');
                            wrapper.style.cssText = 'background:#0E1630;border:1px solid rgba(255,255,255,.12);border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.35);';
                            wrapper.appendChild(iframe);
                            container.innerHTML = '';
                            container.appendChild(wrapper);
                            iframe.contentDocument.open();
                            iframe.contentDocument.write(html);
                            iframe.contentDocument.close();
                        })
                        .catch(function() {
                            if (container) container.innerHTML =
                                '<div class="alert alert-danger m-3">Failed to load report.</div>';
                        });
                });
            });
        </script>
    </div>
</div>
<script>
    // Auto-fill Department & Room in Add Device modal from last saved device (server-side, persists across logout)
(function() {
        var LAST_DEVICE_URL = '<?= site_url("admin/site-inspection/last-device") ?>';
        var SITE_ID_VAL = '<?= $site["id"] ?? 0 ?>';
        var modalEl = document.getElementById('addDeviceModal');
        if (!modalEl) return;

        function hideModelSuggestions() {
            var suggestions = document.getElementById('modelSuggestions');
            if (suggestions) {
                suggestions.classList.add('d-none');
                suggestions.innerHTML = '';
            }
        }

        function unlockModelField() {
            var addModelEl = document.getElementById('addModel');
            if (addModelEl) {
                addModelEl.removeAttribute('readonly');
                addModelEl.classList.remove('bg-light');
                addModelEl.title = '';
            }
            hideModelSuggestions();
        }

        function resetAddDeviceModal() {
            var form = document.getElementById('addDeviceForm');
            if (form) {
                form.reset();
            }

            var err = document.getElementById('addDeviceError');
            if (err) {
                err.classList.add('d-none');
                err.textContent = '';
            }

            unlockModelField();

            // keep expected defaults after reset
            var addTech = document.getElementById('addTech');
            if (addTech) addTech.value = 'Admin';

            var addEST = document.getElementById('addEST');
            if (addEST) addEST.value = 'No';

            var addCAL = document.getElementById('addCAL');
            if (addCAL) addCAL.value = 'No';

            var modalBody = modalEl.querySelector('.modal-body');
            if (modalBody) {
                modalBody.scrollTop = 0;
            }
        }

        modalEl.addEventListener('show.bs.modal', function() {
            // extra safety: always unlock when opening
            unlockModelField();

            var deptEl = document.getElementById('addDept');
            var roomEl = document.getElementById('addRoom');
            if (!deptEl || !roomEl) return;

            fetch(LAST_DEVICE_URL + '?site_id=' + encodeURIComponent(SITE_ID_VAL), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(r) {
                    return r.json();
                })
                .then(function(data) {
                    if (data.department && !deptEl.value) deptEl.value = data.department;
                    if (data.location && !roomEl.value) roomEl.value = data.location;
                })
                .catch(function() {});
        });

        modalEl.addEventListener('hidden.bs.modal', function() {
            // when user clicks Cancel, presses ESC, or closes modal
            // completely reset and unlock field for next open
            resetAddDeviceModal();
        });
    })();



    // ── Column sorting for inspection sub-tabs ───────────────────────────────────
    // Adds clickable sort to <th> headers in notInspectedTable and inspectedTable
    // without replacing the existing manual search / export integration.
    (function() {
        var _sortState = {}; // { tableId: { col: N, asc: true/false } }

        function cellText(cell) {
            return (cell ? cell.innerText || cell.textContent || '' : '').trim().toLowerCase();
        }

        function sortTable(tableId, colIdx) {
            var tbl = document.getElementById(tableId);
            if (!tbl) return;
            var tbody = tbl.querySelector('tbody');
            if (!tbody) return;

            var state = _sortState[tableId] || {
                col: -1,
                asc: true
            };
            var asc = (state.col === colIdx) ? !state.asc : true;
            _sortState[tableId] = {
                col: colIdx,
                asc: asc
            };

            // Sort visible rows only (hidden rows from group-filter stay hidden)
            var rows = Array.from(tbody.querySelectorAll('tr[data-asset], tr[data-row-id]'));
            rows.sort(function(a, b) {
                var ta = cellText(a.cells[colIdx]);
                var tb = cellText(b.cells[colIdx]);
                // Numeric sort if both look numeric
                var na = parseFloat(ta),
                    nb = parseFloat(tb);
                if (!isNaN(na) && !isNaN(nb)) return asc ? na - nb : nb - na;
                return asc ? ta.localeCompare(tb) : tb.localeCompare(ta);
            });
            rows.forEach(function(r) {
                tbody.appendChild(r);
            });

            // Update sort indicator on all headers
            var ths = tbl.querySelectorAll('thead th');
            ths.forEach(function(th, i) {
                th.classList.remove('sort-asc', 'sort-desc');
                if (i === colIdx) th.classList.add(asc ? 'sort-asc' : 'sort-desc');
            });
        }

        function makeSortable(tableId, skipCols) {
            skipCols = skipCols || [0]; // default: skip first (Action) column
            var tbl = document.getElementById(tableId);
            if (!tbl) return;
            var ths = tbl.querySelectorAll('thead th');
            ths.forEach(function(th, i) {
                if (skipCols.indexOf(i) !== -1) return;
                th.style.cursor = 'pointer';
                th.style.userSelect = 'none';
                th.setAttribute('title', 'Click to sort');
                th.addEventListener('click', function() {
                    sortTable(tableId, i);
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            makeSortable('notInspectedTable', [0]);
            makeSortable('inspectedTable', [0]);
            makeSortable('archivedTable', []);
            makeSortable('inventoryTable', []);
            makeSortable('workOrdersTable', [0]);
        });

        /**
         * Save inspection status to database via AJAX and update the UI button.
         */
        window.updateStatus = function(newStatus) {
            setInspectionStatusUI(newStatus);
            var inspView = document.getElementById('view-inspection');
            if (inspView) {
                inspView.setAttribute('data-current-status', newStatus);
            }

            var groupId = window.CURRENT_INSPECTION_GROUP_ID;
            if (groupId) {
                var rows = document.querySelectorAll('#inspectionsTable tbody tr');
                rows.forEach(function(row) {
                    var viewBtn = row.querySelector('.btn-view-insp');
                    if (!viewBtn || viewBtn.dataset.group !== groupId) return;

                    var statusTd = row.querySelector('td:nth-child(5)');
                    if (!statusTd) return;

                    viewBtn.dataset.status = newStatus;

                    if (String(newStatus).trim().toLowerCase() === 'closed/complete') {
                        statusTd.innerHTML = '<span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Closed</span>';
                    } else {
                        statusTd.innerHTML = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-rotate me-1"></i>In Progress</span>';
                    }
                });

                var fd = new FormData();
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
                            showToast(res.success ? 'Status updated.' : 'Failed to save status.', res.success ? 'success' : 'danger');
                        }
                    })
                    .catch(function() {
                        if (typeof showToast === 'function') {
                            showToast('Network error saving status.', 'danger');
                        }
                    });
            }
        };

    })();


    // ── Result filter for Inspected Items table ───────────────────────────────
    window.filterInspectedResult = function(result) {
        var currentGroup = window.CURRENT_INSPECTION_GROUP_ID || '';
        var rows = document.querySelectorAll('#inspectionTableBody tr[data-row-id]');
        rows.forEach(function(row) {
            var rowGroup = row.getAttribute('data-group-id') || '';
            if (rowGroup !== currentGroup) {
                row.style.display = 'none';
                return;
            }
            if (!result) {
                row.style.display = '';
                return;
            }
            // Result is in col 9 (1-based) after Brand column added
            var resultCell = row.querySelector('td:nth-child(9)');
            var cellText = resultCell ? resultCell.textContent.trim() : '';
            row.style.display = cellText.toLowerCase().includes(result.toLowerCase()) ? '' : 'none';
        });
    };

    // ================================================================
    // FIX 6: Inspection list filter (Open / All / Closed)
    // ================================================================
    var _inspCurrentFilter = 'open'; // default: show open only

    window.filterInspectionsByStatus = function filterInspectionsByStatus(mode) {
        _inspCurrentFilter = mode;

        // Update button active classes
        var btnOpen   = document.getElementById('inspFilterOpen');
        var btnAll    = document.getElementById('inspFilterAll');
        var btnClosed = document.getElementById('inspFilterClosed');
        [btnOpen, btnAll, btnClosed].forEach(function(b) {
            if (!b) return;
            b.classList.remove('btn-primary', 'btn-outline-secondary', 'active');
            b.classList.add('btn-outline-secondary');
        });
        var activeBtn = mode === 'open' ? btnOpen : (mode === 'closed' ? btnClosed : btnAll);
        if (activeBtn) {
            activeBtn.classList.remove('btn-outline-secondary');
            activeBtn.classList.add('btn-primary', 'active');
        }

        var rows = document.querySelectorAll('#inspectionsTable tbody tr[data-insp-status]');
        var visible = 0;
        rows.forEach(function(row) {
            var st = row.getAttribute('data-insp-status') || 'open';
            var show = (mode === 'all')
                    || (mode === 'open'   && st === 'open')
                    || (mode === 'closed' && st === 'closed');
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Show empty state row if nothing matches
        var emptyRow = document.querySelector('#inspectionsTable tbody tr:not([data-insp-status])');
        if (emptyRow) emptyRow.style.display = (visible === 0) ? '' : 'none';

        // Reset search when filter tab changes
        var searchEl = document.getElementById('inspSearchInput');
        if (searchEl) searchEl.value = '';
    };

    window.applyInspectionSearch = function applyInspectionSearch(q) {
        q = (q || '').toLowerCase().trim();
        var rows = document.querySelectorAll('#inspectionsTable tbody tr[data-insp-status]');
        var visible = 0;
        rows.forEach(function(row) {
            // Respect current filter tab first
            var st = row.getAttribute('data-insp-status') || 'open';
            var passFilter = (_inspCurrentFilter === 'all')
                          || (_inspCurrentFilter === 'open'   && st === 'open')
                          || (_inspCurrentFilter === 'closed' && st === 'closed');
            if (!passFilter) { row.style.display = 'none'; return; }

            // Then apply search query against all cell text
            if (!q) { row.style.display = ''; visible++; return; }
            var text = row.textContent || row.innerText || '';
            var show = text.toLowerCase().includes(q);
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        var emptyRow = document.querySelector('#inspectionsTable tbody tr:not([data-insp-status])');
        if (emptyRow) emptyRow.style.display = (visible === 0) ? '' : 'none';
    };

    // Auto-apply 'open' filter on page load so only open inspections show
    document.addEventListener('DOMContentLoaded', function() {
        filterInspectionsByStatus('open');
    });
</script>