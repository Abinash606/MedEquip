<?php

/**
 * Technician — site inspection workflow partial
 * app/Views/technician/site/site_inspection_workflow.php
 *
 * Merged: Add Device modal + model-search autocomplete from admin implementation.
 *
 * WORK ORDER BUGS FIXED IN THIS VERSION:
 *
 *  BUG 1 — CSRF token missing from work order form submit
 *  BUG 2 — Race condition: openWorkOrderModalFromInventory() called before backgroundRefreshTabs()
 *  BUG 3 — workOrderCreate() response not handled when server returns non-JSON
 *  BUG 4 — filterNotInspectedByGroup() was never defined
 */
?>

<div id="site-inspection-workflow">
    <div class="container-fluid px-4 py-4 g-card">

        <!-- ── Dashboard view ── -->
        <div class="fade-in" id="view-dashboard">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Inspections List</h3>
                    <p class="text-muted small mb-0">Manage and track equipment safety checks.</p>
                </div>
                <button class="btn btn-primary" onclick="startInspection()">
                    <i class="fa-solid fa-plus me-2"></i>Add Inspection
                </button>
            </div>
            <div class="card p-4 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover" id="inspectionsTable">
                        <thead>
                            <tr>
                                <th>Inspection ID</th>
                                <th>Scheduled Date</th>
                                <th>Technician</th>
                                <th>Next Due Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($inspectionList ?? [])): ?>
                                <?php foreach ($inspectionList as $insp):
                                    $inspId = 'INSP-' . date('Ymd', strtotime($insp['scheduled_at'])) . '-' . strtoupper(substr(md5($insp['group_id']), 0, 8));
                                ?>
                                    <tr>
                                        <td><span class="fw-medium"><?= esc($inspId) ?></span></td>
                                        <td><?= esc(date('M d, Y', strtotime($insp['scheduled_at']))) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php
                                                $techName = $insp['technician_name'] ?? 'N/A';
                                                $initials = strtoupper(substr($techName, 0, 2));
                                                ?>
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width:26px;height:26px;font-size:10px;flex-shrink:0;">
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
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-secondary me-1"
                                                onclick="openInspectionReport('<?= esc($insp['group_id']) ?>')"
                                                title="View Report">
                                                <i class="fa-solid fa-file-export"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="viewInspection(
                                                '<?= esc($insp['group_id']) ?>',
                                                '<?= esc($site['name']) ?>',
                                                '<?= esc($insp['inspection_type'] ?? 'Equipment Inspection') ?>',
                                                '<?= esc($inspId) ?>',
                                                '<?= esc($insp['technician_name'] ?? 'N/A') ?>'
                                            )">
                                                <i class="fa-solid fa-eye me-1"></i> View
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-inspection-btn"
                                                data-id="<?= esc($insp['group_id']) ?>" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No inspections found. Click "Add
                                        Inspection" to start.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── Inspection detail view ── -->
        <div class="fade-in d-none-view" id="view-inspection">

            <script type="application/json" id="equipmentSettingsData">
                <?php
                $equipmentSettingsMap = [];
                if (!empty($site['id'])) {
                    $eqModel2      = new \App\Models\EquipmentModel();
                    $allEquipment2 = $eqModel2->where('site_id', $site['id'])->findAll();
                    foreach ($allEquipment2 as $eq) {
                        $model = $eq['model'] ?? '';
                        if ($model !== '') {
                            $equipmentSettingsMap[$model] = [
                                'est' => $eq['est'] ?? '0',
                                'cal' => $eq['cal'] ?? '0',
                            ];
                        }
                    }
                }
                echo json_encode($equipmentSettingsMap);
                ?>
            </script>

            <div class="mb-4">
                <a class="text-decoration-none text-muted small mb-2 d-inline-block" href="#" onclick="showDashboard()">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Inspections List
                </a>
                <div class="d-flex justify-content-between align-items-start mt-2">
                    <div>
                        <div class="badge text-light border mb-2" id="insp-site-label">Site:
                            <?= esc($site['name'] ?? '—') ?></div>
                        <h2 class="fw-bold" id="insp-title">—</h2>
                        <p class="text-muted">
                            <span id="insp-id-label">—</span> •
                            <span class="text-primary" id="insp-technician">—</span>
                        </p>
                    </div>
                    <div class="text-end">
                        <label class="d-block small text-muted mb-1">Current Status</label>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle status-badge status-in-progress"
                                data-bs-toggle="dropdown" id="statusDropdown" type="button">
                                <i class="fa-solid fa-rotate"></i> In Progress
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="updateStatus('In Progress')">In
                                        Progress</a></li>
                                <li><a class="dropdown-item" href="#"
                                        onclick="updateStatus('Closed/Complete')">Closed/Complete</a></li>
                            </ul>
                        </div>
                        <div class="small text-muted mt-1 fst-italic">Mark as Closed when done</div>
                    </div>
                </div>
            </div>

            <!-- Asset entry -->
            <div class="asset-input-wrapper glass-card p-3 mb-3">
                <h5 class="fw-bold mb-3">Start With Asset Number <span class="text-danger">Not</span> Serial Number</h5>
                <div id="assetLookupAlert" class="alert mb-3" style="display:none;" role="alert"></div>
                <div class="d-flex justify-content-center">
                    <div class="input-group" style="max-width:600px">
                        <input class="form-control" id="assetInput" placeholder="Scan or Enter Asset Number..."
                            type="text" autocomplete="off">
                        <button class="btn btn-primary px-4" onclick="handleAssetGo()" type="button">
                            <i class="fa-solid fa-arrow-right"></i> Go
                        </button>
                    </div>
                </div>
                <p class="text-muted small mt-2 text-center">
                    Devices only move to Inspected Items after the inspection result is recorded.
                </p>
            </div>

            <!-- Inner tabs -->
            <div class="card shadow-sm">
                <div class="card-header border-0 pt-3 pb-0">
                    <ul class="nav nav-tabs" id="inspectionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-target="#not-inspected" data-bs-toggle="tab"
                                id="not-inspected-tab" role="tab" type="button">
                                Not Inspected
                                <span class="badge rounded-pill ms-1"
                                    id="not-inspected-count"><?= count($notInspected ?? []) ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#inspect-device" data-bs-toggle="tab"
                                id="inspect-device-tab" role="tab" type="button">Pass/Fail</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#inspected" data-bs-toggle="tab" id="inspected-tab"
                                role="tab" type="button">
                                Inspected Items
                                <span class="badge bg-success rounded-pill ms-1"
                                    id="inspected-count"><?= count($inspectedItems ?? []) ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#archived" data-bs-toggle="tab" id="archived-tab"
                                role="tab" type="button">Archived Items</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#inventory" data-bs-toggle="tab" id="inventory-tab"
                                role="tab" type="button">All Inventory</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#insp-work-orders" data-bs-toggle="tab"
                                id="insp-work-orders-tab" role="tab" type="button">Work Orders</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-target="#inspection-reports" data-bs-toggle="tab"
                                id="reports-tab" role="tab" type="button">Inspection Reports</button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content">

                        <!-- ══ NOT INSPECTED ══ -->
                        <div class="tab-pane fade p-4 active show" id="not-inspected" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <div class="fw-semibold">Site Inventory Pending Inspection</div>
                                    <div class="text-muted small" id="not-inspected-message">
                                        <?= count($notInspected ?? []) ?> item(s) remaining in site inventory pending
                                        inspection.
                                    </div>
                                </div>
                                <!-- ── Add Device button (NEW) ── -->
                                <button class="btn btn-primary btn-sm" type="button" onclick="openAddDeviceModal()">
                                    <i class="fa-solid fa-circle-plus me-1"></i> Add Device
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped mb-0" id="notInspectedTable">
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
                                    <tbody id="notInspectedTableBody">
                                        <?php if (!empty($notInspected ?? [])): ?>
                                            <?php foreach ($notInspected as $eq): ?>
                                                <tr data-asset="<?= esc($eq['asset_tag']) ?>"
                                                    data-eq-id="<?= esc($eq['id']) ?>">
                                                    <td>

                                                        <button class="btn btn-sm btn-primary d-flex gap-2 align-items-center"
                                                            type="button"
                                                            onclick="inspectFromNotInspected('<?= esc($eq['asset_tag']) ?>')">
                                                            <i class="fa-solid fa-arrow-right me-1"></i> Inspect
                                                        </button>


                                                    </td>
                                                    <td><?= esc($eq['asset_tag']) ?></td>
                                                    <td><?= esc($eq['model'] ?? $eq['make'] ?? 'N/A') ?></td>
                                                    <td><?= esc($eq['device_type'] ?? 'N/A') ?></td>
                                                    <td><?= esc($eq['department'] ?? '') ?><?php if (!empty($eq['location'])): ?>
                                                        / <?= esc($eq['location']) ?><?php endif; ?></td>
                                                    <td><span class="badge bg-secondary">Not Inspected</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr id="notInspectedEmpty">
                                                <td colspan="6" class="text-center text-muted py-3">No equipment pending
                                                    inspection.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ══ PASS / FAIL ══ -->
                        <div class="tab-pane fade p-4" id="inspect-device" role="tabpanel">
                            <div id="inspectDeviceEmpty" class="text-muted py-4 text-center">
                                <i class="fa-solid fa-barcode fa-2x mb-3 d-block text-secondary"></i>
                                Enter an Asset Number above and click <strong>Go</strong> to start inspecting.
                            </div>
                            <div class="d-none" id="inspectDeviceFormWrapper">
                                <div class="card border-0 shadow-sm mb-4 glass-card">
                                    <div class="card-body">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-3 fw-semibold">Customer:</div>
                                            <div class="col-md-9 fw-semibold" id="inspectCustomerName">—</div>
                                            <div class="col-md-3 fw-semibold">Model:</div>
                                            <div class="col-md-9" id="inspectModelDisplay">—</div>
                                            <div class="col-md-3 fw-semibold">Department:</div>
                                            <div class="col-md-9"><input class="form-control" id="inspectDept"
                                                    placeholder="Department" type="text"></div>
                                            <div class="col-md-3 fw-semibold">Room:</div>
                                            <div class="col-md-9"><input class="form-control" id="inspectRoom"
                                                    placeholder="Room" type="text"></div>
                                            <div class="col-md-3 fw-semibold">Serial #:</div>
                                            <div class="col-md-9"><input class="form-control" id="inspectSerial"
                                                    placeholder="Serial #" type="text"></div>
                                            <div class="col-md-3 fw-semibold">Asset ID:</div>
                                            <div class="col-md-9"><input class="form-control" id="inspectAsset"
                                                    placeholder="Asset ID" readonly type="text"></div>
                                            <div class="col-md-3 fw-semibold">PM Frequency:</div>
                                            <div class="col-md-9">
                                                <select class="form-select" id="inspectPMFrequency"
                                                    style="max-width:240px;">
                                                    <option value="12 Month">12 Month</option>
                                                    <option value="6 Month">6 Month</option>
                                                    <option value="3 Month">3 Month</option>
                                                    <option value="24 Month">24 Month</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 fw-semibold">Action Performed:</div>
                                            <div class="col-md-9">
                                                <select class="form-select" id="inspectActionPerformed"
                                                    style="max-width:240px;">
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
                                                    <div class="fw-semibold" style="min-width:70px;padding-top:6px;">
                                                        Notes:</div>
                                                    <textarea class="form-control" id="inspectNotes"
                                                        placeholder="Enter service notes..." rows="5"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                                                <button class="btn btn-success px-4" id="btnPassInspection"
                                                    type="button">Pass Inspection</button>
                                                <button class="btn btn-danger px-4" id="btnFailInspection"
                                                    type="button">Fail Inspection</button>
                                                <button class="btn btn-warning px-4 text-white" id="btnFailWOInspection"
                                                    type="button">Fail &amp; Open Work Order</button>
                                                <button class="btn btn-primary px-4" id="btnRepairInspection"
                                                    type="button">Repair Inspection</button>
                                                <button class="btn btn-secondary ms-auto" id="btnCancelInspection"
                                                    type="button">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ══ INSPECTED ITEMS ══ -->
                        <div class="tab-pane fade" id="inspected" role="tabpanel">
                            <div class="p-3">
                                <div class="mb-3" id="deviceTypeCounter">
                                    <h6 class="fw-semibold">Device Type Counter</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
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
                                                $deviceCountsByGroup = [];
                                                if (!empty($inspectedItems ?? [])) {
                                                    foreach ($inspectedItems as $item) {
                                                        $gid  = $item['group_id'] ?? '';
                                                        $type = $item['device_type'] ?? 'Unknown';
                                                        if (!isset($deviceCountsByGroup[$gid][$type])) {
                                                            $deviceCountsByGroup[$gid][$type] = ['total' => 0, 'est' => 0, 'cal' => 0];
                                                        }
                                                        $deviceCountsByGroup[$gid][$type]['total']++;
                                                        $estVal = $item['est'] ?? 0;
                                                        if ((int)$estVal === 1 || strtolower((string)$estVal) === 'yes') $deviceCountsByGroup[$gid][$type]['est']++;
                                                        $calVal = $item['cal'] ?? 0;
                                                        if ((int)$calVal === 1 || strtolower((string)$calVal) === 'yes') $deviceCountsByGroup[$gid][$type]['cal']++;
                                                    }
                                                }
                                                ?>
                                                <?php if (!empty($deviceCountsByGroup)): ?>
                                                    <?php foreach ($deviceCountsByGroup as $gid => $types): ?>
                                                        <?php foreach ($types as $type => $counts): ?>
                                                            <tr data-group-id="<?= esc($gid) ?>" style="display:none">
                                                                <td><?= esc($type) ?></td>
                                                                <td><?= esc($counts['total']) ?></td>
                                                                <td><?= esc($counts['est']) ?></td>
                                                                <td><?= esc($counts['cal']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>
                                                    <tr id="deviceCountTotalRow" style="display:none"
                                                        class="fw-bold table-secondary">
                                                        <td>Total</td>
                                                        <td id="deviceCountTotal">0</td>
                                                        <td id="deviceCountTotalEST">0</td>
                                                        <td id="deviceCountTotalCAL">0</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <tr id="deviceCountEmptyRow">
                                                        <td colspan="4" class="text-center text-muted">No inspected items
                                                            yet.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped mb-0" id="inspectedTable">
                                        <thead>
                                            <tr>
                                                <th>Actions</th>
                                                <th>Model</th>
                                                <th>Type</th>
                                                <th>S/N</th>
                                                <th>Asset #</th>
                                                <th>Dept / Room</th>
                                                <th>Tech</th>
                                                <th>Result</th>
                                                <th style="width:22%">Notes</th>
                                                <th>Insp Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="inspectionTableBody">
                                            <?php if (!empty($inspectedItems ?? [])): ?>
                                                <tr id="inspectedEmptyRow" style="display:none">
                                                    <td colspan="10" class="text-center text-muted">No inspected items for
                                                        this inspection yet.</td>
                                                </tr>
                                                <?php foreach ($inspectedItems as $item): ?>
                                                    <tr class="fade-in" data-row-id="<?= esc($item['id']) ?>"
                                                        data-asset="<?= esc($item['asset_tag']) ?>"
                                                        data-eq-id="<?= esc($item['equipment_id'] ?? '') ?>"
                                                        data-group-id="<?= esc($item['group_id'] ?? '') ?>"
                                                        data-device-type="<?= esc($item['device_type'] ?? '') ?>"
                                                        data-est="<?= esc($item['est'] ?? 'No') ?>"
                                                        data-cal="<?= esc($item['cal'] ?? 'No') ?>">
                                                        <td>
                                                            <div class="d-flex gap-1">
                                                                <button class="btn btn-sm btn-primary btn-edit-inspected"
                                                                    title="Edit" data-id="<?= esc($item['id']) ?>"
                                                                    data-model="<?= esc($item['model'] ?? $item['make'] ?? '') ?>"
                                                                    data-type="<?= esc($item['device_type'] ?? '') ?>"
                                                                    data-serial="<?= esc($item['serial_number'] ?? '') ?>"
                                                                    data-asset="<?= esc($item['asset_tag'] ?? '') ?>"
                                                                    data-dept="<?= esc($item['department'] ?? '') ?>"
                                                                    data-room="<?= esc($item['location'] ?? '') ?>"
                                                                    data-tech="<?= esc($item['technician'] ?? '') ?>"
                                                                    data-notes="<?= esc($item['notes'] ?? '') ?>">
                                                                    <i class="fa-solid fa-pen"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger btn-delete-inspected"
                                                                    title="Delete" data-id="<?= esc($item['id']) ?>"
                                                                    data-asset="<?= esc($item['asset_tag'] ?? '') ?>">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <td><strong><?= esc($item['model'] ?? $item['make'] ?? 'N/A') ?></strong>
                                                        </td>
                                                        <td><?= esc($item['device_type'] ?? '') ?></td>
                                                        <td><?= esc($item['serial_number'] ?? 'N/A') ?></td>
                                                        <td><span
                                                                class="badge text-dark border"><?= esc($item['asset_tag']) ?></span>
                                                        </td>
                                                        <td><?= esc($item['department'] ?? '') ?><br><span
                                                                class="text-muted small"><?= esc($item['location'] ?? '') ?></span>
                                                        </td>
                                                        <td><?= esc($item['technician'] ?? 'N/A') ?></td>
                                                        <td>
                                                            <?php $res = $item['result'] ?? ''; ?>
                                                            <?php if ($res === 'Pass'): ?><span
                                                                    class="text-success fw-semibold"><i
                                                                        class="fa-solid fa-check me-1"></i>Pass</span>
                                                            <?php elseif ($res === 'Fail'): ?><span
                                                                    class="text-danger fw-semibold"><i
                                                                        class="fa-solid fa-xmark me-1"></i>Fail</span>
                                                            <?php elseif (!empty($res)): ?><span
                                                                    class="text-warning fw-semibold"><i
                                                                        class="fa-solid fa-triangle-exclamation me-1"></i><?= esc($res) ?></span>
                                                            <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                                                        </td>
                                                        <td class="small text-muted"><?= esc($item['notes'] ?? '') ?></td>
                                                        <td>
                                                            <?php if (!empty($item['inspection_date'])):
                                                                $d = new DateTime($item['inspection_date']);
                                                                echo esc($d->format('Y-m-d')) . '<br><span class="text-muted small">' . esc($d->format('h:i A')) . '</span>';
                                                            else: ?><span class="text-muted">-</span><?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted py-3">No inspected items
                                                        found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ══ ARCHIVED ══ -->
                        <div class="tab-pane fade p-4" id="archived" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
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
                                                <tr>
                                                    <td><?= esc($item['asset_tag']) ?></td>
                                                    <td><?= esc($item['model'] ?? $item['make'] ?? 'N/A') ?></td>
                                                    <td><?= esc($item['device_type'] ?? '') ?></td>
                                                    <td><?= esc($item['department'] ?? '') ?><?php if (!empty($item['location'])): ?>
                                                        / <?= esc($item['location']) ?><?php endif; ?></td>
                                                    <td><span class="badge bg-dark">Archived</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No archived items found.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ══ ALL INVENTORY ══ -->
                        <div class="tab-pane fade p-4" id="inventory" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <div class="fw-semibold">All Inventory</div>
                                    <div class="text-muted small">Full list of equipment in the site inventory.</div>
                                </div>
                                <!-- Add Device shortcut in Inventory tab too -->
                                <button class="btn btn-primary btn-sm" type="button" onclick="openAddDeviceModal()">
                                    <i class="fa-solid fa-circle-plus me-1"></i> Add Device
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="inventoryTable">
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
                                                <tr>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            onclick="openWorkOrderModalFromInventory('<?= esc($eq['asset_tag']) ?>')">
                                                            <i class="fa-solid fa-briefcase"></i>
                                                        </button>
                                                    </td>
                                                    <td><?= esc($eq['asset_tag']) ?></td>
                                                    <td><?= esc($eq['model'] ?? $eq['make'] ?? 'N/A') ?></td>
                                                    <td><?= esc($eq['device_type'] ?? '') ?></td>
                                                    <td><?= esc($eq['department'] ?? '') ?><?php if (!empty($eq['location'])): ?>
                                                        / <?= esc($eq['location']) ?><?php endif; ?></td>
                                                    <td>
                                                        <?php
                                                        $status = $eq['status'] ?? 'Unknown';
                                                        $bc = strtolower($status) === 'ready' ? 'bg-success' : (str_contains(strtolower($status), 'need') ? 'bg-danger' : (str_contains(strtolower($status), 'repair') ? 'bg-warning text-dark' : 'bg-secondary'));
                                                        ?>
                                                        <span class="badge <?= $bc ?>"><?= esc($status) ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">No inventory found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ══ WORK ORDERS ══ -->
                        <div class="tab-pane fade p-3" id="insp-work-orders" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h5 class="fw-bold mb-1">Work Orders</h5>
                                    <p class="text-muted small mb-0">Manage work orders for this site.</p>
                                </div>
                                <button class="btn btn-primary" onclick="openWorkOrderModal()">
                                    <i class="fa-solid fa-plus me-2"></i>Add Work Order
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
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
                                                        <div class="d-flex gap-1">
                                                            <button class="btn btn-sm btn-primary btn-edit-wo"
                                                                data-id="<?= esc($wo['id']) ?>"><i
                                                                    class="fa-solid fa-pen"></i></button>
                                                            <button class="btn btn-sm btn-danger btn-delete-wo"
                                                                data-id="<?= esc($wo['id']) ?>"><i
                                                                    class="fa-solid fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                    <td><?= esc($wo['id']) ?></td>
                                                    <td><?= esc($wo['title'] ?? '') ?></td>
                                                    <td>
                                                        <?php if (!empty($wo['asset_tag'])): ?><span
                                                                class="badge text-dark border"><?= esc($wo['asset_tag']) ?></span><?php endif; ?>
                                                        <?php if (!empty($wo['serial_number'])): ?><br><span
                                                                class="small text-muted">S/N:
                                                                <?= esc($wo['serial_number']) ?></span><?php endif; ?>
                                                    </td>
                                                    <td><?= esc($wo['priority'] ?? '') ?></td>
                                                    <td><?= esc($wo['status'] ?? '') ?></td>
                                                    <td><?= esc($wo['assigned_to_name'] ?? 'N/A') ?></td>
                                                    <td><?= !empty($wo['start_date']) ? esc(date('Y-m-d', strtotime($wo['start_date']))) : '<span class="text-muted">-</span>' ?>
                                                    </td>
                                                    <td><?= !empty($wo['end_date'])   ? esc(date('Y-m-d', strtotime($wo['end_date'])))   : '<span class="text-muted">-</span>' ?>
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

                        <!-- ══ INSPECTION REPORTS ══ -->
                        <div class="tab-pane fade p-3" id="inspection-reports" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <div>
                                    <h5 class="fw-bold mb-1">Inspection Reports</h5>
                                    <p class="text-muted small mb-0">Preview and export inspection summaries.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary" onclick="previewReportPDF()"><i
                                            class="fa-solid fa-file-lines me-2"></i>Preview</button>
                                    <button class="btn btn-primary" onclick="exportReportPDF()"><i
                                            class="fa-solid fa-download me-2"></i>Download PDF</button>
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

        <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer" style="z-index:2000;"></div>

        <!-- ══════════════════════════════════════════════════════════════
             ADD DEVICE MODAL (merged from admin implementation)
             ══════════════════════════════════════════════════════════════ -->
        <div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content" style="border:0;border-radius:12px;overflow:hidden;">

                    <div class="modal-header border-0 pb-0"
                        style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;">
                        <div>
                            <h5 class="modal-title fw-bold mb-1" id="addDeviceModalLabel">
                                <i class="fa-solid fa-circle-plus me-2"></i>Add New Device
                            </h5>
                            <p class="small mb-0" style="opacity:.85;">
                                Select a device from the equipment database, then save it to site inventory.
                            </p>
                        </div>
                        <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Inline error alert -->
                    <div class="px-4 pt-3 d-none" id="addDeviceError">
                        <div class="alert alert-danger alert-dismissible mb-0" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            <span id="addDeviceErrorText">An error occurred.</span>
                            <button type="button" class="btn-close"
                                onclick="document.getElementById('addDeviceError').classList.add('d-none')"></button>
                        </div>
                    </div>

                    <form id="addDeviceForm" autocomplete="off">
                        <div class="modal-body pt-3">

                            <!-- ── Device Identification ───────────────── -->
                            <p class="text-muted small fw-semibold text-uppercase mb-3"
                                style="letter-spacing:.06em;border-bottom:1px solid #e9ecef;padding-bottom:6px;">
                                <i class="fa-solid fa-barcode me-1"></i> Device Identification
                            </p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="addAsset">
                                        Asset # <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control" id="addAsset" type="text" placeholder="e.g. A-10042"
                                        required>
                                </div>

                                <!-- Model with live search -->
                                <div class="col-md-4 position-relative">
                                    <label class="form-label fw-semibold" for="addModel">Model Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0 bg-white">
                                            <i class="fa-solid fa-magnifying-glass text-muted"
                                                id="addModelSearchIcon"></i>
                                        </span>
                                        <input class="form-control border-start-0 ps-0" id="addModel" type="text"
                                            placeholder="Type to search…" autocomplete="off">
                                    </div>
                                    <!-- Suggestion dropdown -->
                                    <div class="list-group shadow-lg position-absolute w-100 d-none"
                                        id="addModelSuggestions" style="z-index:3000;max-height:260px;overflow-y:auto;
                                                top:calc(100% + 2px);left:0;border-radius:8px;">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="addSerial">Serial #</label>
                                    <input class="form-control" id="addSerial" type="text" placeholder="Optional">
                                </div>
                            </div>

                            <!-- ── Manufacturer & Type ─────────────────── -->
                            <p class="text-muted small fw-semibold text-uppercase mb-3"
                                style="letter-spacing:.06em;border-bottom:1px solid #e9ecef;padding-bottom:6px;">
                                <i class="fa-solid fa-industry me-1"></i> Manufacturer &amp; Type
                            </p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="addManufacturer">Manufacturer</label>
                                    <input class="form-control" id="addManufacturer" type="text"
                                        list="addManufacturerOptions" placeholder="Start typing or auto-filled…">
                                    <datalist id="addManufacturerOptions"></datalist>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="addType">Device Type</label>
                                    <input class="form-control" id="addType" type="text"
                                        placeholder="e.g. Ventilator, Monitor">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="addDescription">Description</label>
                                    <input class="form-control" id="addDescription" type="text"
                                        placeholder="Auto-filled when model is selected">
                                </div>
                            </div>

                            <!-- ── Location ───────────────────────────── -->
                            <p class="text-muted small fw-semibold text-uppercase mb-3"
                                style="letter-spacing:.06em;border-bottom:1px solid #e9ecef;padding-bottom:6px;">
                                <i class="fa-solid fa-location-dot me-1"></i> Location
                            </p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="addDept">Department</label>
                                    <input class="form-control" id="addDept" type="text" placeholder="e.g. ICU, ER">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="addRoom">Room</label>
                                    <input class="form-control" id="addRoom" type="text" placeholder="e.g. Room 4B">
                                </div>
                            </div>

                            <!-- ── Technician & Flags ─────────────────── -->
                            <p class="text-muted small fw-semibold text-uppercase mb-3"
                                style="letter-spacing:.06em;border-bottom:1px solid #e9ecef;padding-bottom:6px;">
                                <i class="fa-solid fa-user-gear me-1"></i> Technician &amp; Calibration Flags
                            </p>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="addTech">Technician</label>
                                    <input class="form-control" id="addTech" type="text"
                                        value="<?= esc(session('full_name') ?? 'Technician') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="addEST">EST</label>
                                    <select class="form-select" id="addEST">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="addCAL">CAL</label>
                                    <select class="form-select" id="addCAL">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                    </select>
                                </div>
                            </div>

                        </div><!-- /modal-body -->

                        <div class="modal-footer border-0" style="background:#f8fafc;">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fa-solid fa-xmark me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary px-4" id="saveDeviceBtn">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Save Device
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /ADD DEVICE MODAL -->

        <!-- ══ WORK ORDER MODAL ══ -->
        <div class="modal fade" id="workOrderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;">
                        <h5 class="modal-title" id="workOrderModalTitle">Add Work Order</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" id="workOrderError"></div>
                        <input id="woId" type="hidden" value="">
                        <input id="woEquipmentId" type="hidden" value="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input class="form-control" id="woTitle" required type="text"
                                    placeholder="e.g. Failed device follow-up">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Asset #</label>
                                <input class="form-control" id="woAsset" onchange="onWOAssetChange()"
                                    placeholder="Auto-filled from inspection" type="text">
                            </div>
                            <div class="col-md-6"><label class="form-label">Make</label><input class="form-control"
                                    id="woMake" placeholder="Auto-filled" readonly type="text"></div>
                            <div class="col-md-6"><label class="form-label">Model</label><input class="form-control"
                                    id="woModel" placeholder="Auto-filled" readonly type="text"></div>
                            <div class="col-md-6"><label class="form-label">Serial #</label><input class="form-control"
                                    id="woSerial" type="text"></div>
                            <div class="col-md-6"><label class="form-label">Equipment Type</label><input
                                    class="form-control" id="woEquipment" readonly type="text"></div>
                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <select class="form-select" id="woPriority">
                                    <option value="Low">Low</option>
                                    <option selected value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="woStatus">
                                    <option value="Open">Open</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Technician</label>
                                <select class="form-select" id="woTech">
                                    <option value="">-- Select Technician --</option>
                                    <?php if (!empty($technicians ?? [])): ?>
                                        <?php foreach ($technicians as $tech): ?>
                                            <option value="<?= esc($tech['id']) ?>">
                                                <?= esc($tech['full_name'] ?? 'Technician #' . $tech['id']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label">Start Date</label><input
                                    class="form-control" id="woStartDate" type="date"></div>
                            <div class="col-md-6"><label class="form-label">End Date</label><input class="form-control"
                                    id="woEndDate" type="date"></div>
                            <div class="col-12"><label class="form-label">Description</label><textarea
                                    class="form-control" id="woDescription" rows="3"
                                    placeholder="Describe the work needed..."></textarea></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-primary" id="saveWorkOrderBtn" type="button" onclick="submitWorkOrder()">
                            Save Work Order
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ INSPECTION REPORT MODAL ══ -->
        <div class="modal fade" id="inspectionReportModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;">
                        <h5 class="modal-title">Inspection Report</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <div class="modal-body">
                        <div id="reportContent"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-outline-primary" onclick="previewReportPDF()" type="button"><i
                                class="fa-solid fa-file-lines me-2"></i>Preview</button>
                        <button class="btn btn-primary" onclick="exportReportPDF()" type="button"><i
                                class="fa-solid fa-download me-2"></i>Download PDF</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ EDIT DEVICE MODAL ══ -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;">
                        <h5 class="modal-title">Edit Device</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <form id="editInspectedForm">
                        <input type="hidden" id="editInspId">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label">Model</label><input class="form-control"
                                        id="editModel" required type="text"></div>
                                <div class="col-md-4"><label class="form-label">Type</label><input class="form-control"
                                        id="editType" required type="text"></div>
                                <div class="col-md-4"><label class="form-label">S/N</label><input class="form-control"
                                        id="editSerial" type="text"></div>
                                <div class="col-md-4"><label class="form-label">Asset #</label><input
                                        class="form-control" id="editAsset" required type="text"></div>
                                <div class="col-md-4"><label class="form-label">Department</label><input
                                        class="form-control" id="editDept" type="text"></div>
                                <div class="col-md-4"><label class="form-label">Room</label><input class="form-control"
                                        id="editRoom" type="text"></div>
                                <div class="col-md-6"><label class="form-label">Tech</label><input class="form-control"
                                        id="editTech" type="text"></div>
                                <div class="col-12"><label class="form-label">Notes</label><textarea
                                        class="form-control" id="editNotes" rows="3"></textarea></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                            <button class="btn btn-primary" type="submit">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>


<script>
    (function() {
        'use strict';

        var BASE = '<?= site_url('technician') ?>';
        var SITE_ID = <?= (int)($site['id'] ?? 0) ?>;
        var LOGGED_IN_TECH_ID = <?= (int)($loggedInTechId ?? 0) ?>;

        var URL_GET_EQ = BASE + '/site-inspection/get-equipment';
        var URL_RECORD = BASE + '/site-inspection/record';
        var URL_UPDATE_INSP = BASE + '/inspections/updateInspection';
        var URL_DELETE_INSP = BASE + '/inspections/deleteById/';
        var REPORT_DATA_URL = BASE + '/inspections/reportData';
        var SEARCH_MODEL_URL = BASE + '/inspections/searchByModel';
        var ADD_DEVICE_URL = BASE + '/site-inspection/add-device';

        var WO_CREATE_URL = BASE + '/work-orders/create';
        var WO_UPDATE_URL = BASE + '/work-orders/update/';

        var CSRF_NAME = '<?= csrf_token() ?>';
        var csrfHash = '<?= csrf_hash() ?>';

        window.CURRENT_INSPECTION_GROUP_ID = null;
        window.CURRENT_REPORT_GROUP_ID = null;

        var _pendingWOAsset = '';

        /* ── Helpers ─────────────────────────────────────────────────────── */
        function escH(s) {
            return String(s || '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function toYesNo(v) {
            var s = String(v || '').toLowerCase().trim();
            return (s === '1' || s === 'yes' || s === 'true') ? 'Yes' : 'No';
        }

        function toast(msg, type) {
            var c = document.getElementById('toastContainer') || document.body;
            var div = document.createElement('div');
            div.className = 'alert alert-' +
                (type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'danger') +
                ' shadow fade show';
            div.style.cssText = 'min-width:280px';
            div.innerHTML = escH(msg);
            c.appendChild(div);
            setTimeout(function() {
                div.remove();
            }, 3500);
        }

        function showAssetAlert(msg, type) {
            var el = document.getElementById('assetLookupAlert');
            if (!el) return;
            el.className = 'alert alert-' + (type || 'warning') + ' mb-3';
            el.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i>' + escH(msg);
            el.style.display = '';
            setTimeout(function() {
                el.style.display = 'none';
            }, 6000);
        }

        function resultBadge(r) {
            r = String(r || '').trim();
            if (r === 'Pass')
                return '<span class="text-success fw-semibold"><i class="fa-solid fa-check"></i> Pass</span>';
            if (r === 'Fail')
                return '<span class="text-danger fw-semibold"><i class="fa-solid fa-xmark"></i> Fail</span>';
            if (r === 'Repair')
                return '<span class="text-warning fw-semibold"><i class="fa-solid fa-wrench"></i> Repair</span>';
            return '<span class="text-muted">-</span>';
        }

        function formatDate(dt) {
            var d = new Date((dt || '').replace(' ', 'T'));
            if (isNaN(d.getTime())) return escH(dt);
            var hh = d.getHours(),
                ampm = hh >= 12 ? 'PM' : 'AM';
            hh = hh % 12 || 12;
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate())
                .padStart(2, '0') +
                '<br>' + String(hh).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0') + ' ' + ampm;
        }

        /* ══════════════════════════════════════════════════════════════════
           ADD DEVICE — Model Search Autocomplete
           ══════════════════════════════════════════════════════════════════ */
        var _modelTimer = null;
        var _modelInp = null;
        var _modelBox = null;
        var _modelIcon = null;

        function renderModelSuggestions(results) {
            if (!_modelBox) return;
            if (!results || results.length === 0) {
                hideModelSuggestions();
                return;
            }
            _modelBox.innerHTML = results.map(function(r) {
                var label = [r.make, r.model].filter(Boolean).join(' — ');
                var sub = r.device_type || '';
                return '<button type="button"' +
                    ' class="list-group-item list-group-item-action py-2 px-3 model-suggestion-item"' +
                    ' data-make="' + escH(r.make || '') + '"' +
                    ' data-model="' + escH(r.model || '') + '"' +
                    ' data-device_type="' + escH(r.device_type || '') + '"' +
                    ' data-department="' + escH(r.department || '') + '"' +
                    ' data-location="' + escH(r.location || '') + '"' +
                    ' data-est="' + escH(r.est || 'No') + '"' +
                    ' data-cal="' + escH(r.cal || 'No') + '"' +
                    '>' +
                    '<span class="fw-semibold d-block">' + escH(label) + '</span>' +
                    (sub ? '<small class="text-muted">' + escH(sub) + '</small>' : '') +
                    '</button>';
            }).join('');
            _modelBox.classList.remove('d-none');
        }

        function hideModelSuggestions() {
            if (_modelBox) {
                _modelBox.classList.add('d-none');
                _modelBox.innerHTML = '';
            }
        }

        function fillFromModelSuggestion(btn) {
            if (!btn) return;
            if (_modelInp) _modelInp.value = btn.getAttribute('data-model') || '';

            var mfr = document.getElementById('addManufacturer');
            var typ = document.getElementById('addType');
            var dsc = document.getElementById('addDescription');
            var ser = document.getElementById('addSerial');
            var dpt = document.getElementById('addDept');
            var rm = document.getElementById('addRoom');
            var est = document.getElementById('addEST');
            var cal = document.getElementById('addCAL');

            if (mfr) mfr.value = btn.getAttribute('data-make') || '';
            if (typ) typ.value = btn.getAttribute('data-device_type') || '';
            if (dsc) dsc.value = btn.getAttribute('data-device_type') || '';
            // Serial # intentionally NOT auto-populated — technician must enter manually
            if (dpt && !dpt.value.trim()) dpt.value = btn.getAttribute('data-department') || '';
            if (rm && !rm.value.trim()) rm.value = btn.getAttribute('data-location') || '';
            if (est) est.value = toYesNo(btn.getAttribute('data-est'));
            if (cal) cal.value = toYesNo(btn.getAttribute('data-cal'));

            hideModelSuggestions();
            if (_modelInp) _modelInp.focus();
        }

        function initModelSearch() {
            _modelInp = document.getElementById('addModel');
            _modelBox = document.getElementById('addModelSuggestions');
            _modelIcon = document.getElementById('addModelSearchIcon');
            if (!_modelInp || !_modelBox) return;

            _modelInp.addEventListener('input', function() {
                var q = _modelInp.value.trim();
                clearTimeout(_modelTimer);
                hideModelSuggestions();
                if (q.length < 2) {
                    if (_modelIcon) _modelIcon.className = 'fa-solid fa-magnifying-glass text-muted';
                    return;
                }
                if (_modelIcon) _modelIcon.className = 'fa-solid fa-spinner fa-spin text-primary';
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
                            if (_modelIcon) _modelIcon.className =
                                'fa-solid fa-magnifying-glass text-muted';
                            renderModelSuggestions(data || []);
                        })
                        .catch(function() {
                            if (_modelIcon) _modelIcon.className =
                                'fa-solid fa-magnifying-glass text-muted';
                            hideModelSuggestions();
                        });
                }, 220);
            });

            _modelBox.addEventListener('mousedown', function(e) {
                var btn = e.target.closest('.model-suggestion-item');
                if (btn) {
                    e.preventDefault();
                    fillFromModelSuggestion(btn);
                }
            });

            _modelInp.addEventListener('blur', function() {
                setTimeout(hideModelSuggestions, 180);
            });

            _modelInp.addEventListener('keydown', function(e) {
                var items = _modelBox.querySelectorAll('.model-suggestion-item');
                var active = _modelBox.querySelector('.model-suggestion-item.active');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (!active) {
                        if (items[0]) items[0].classList.add('active');
                    } else {
                        items.forEach(function(el) {
                            el.classList.remove('active');
                        });
                        if (active.nextElementSibling) active.nextElementSibling.classList.add('active');
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (active) {
                        items.forEach(function(el) {
                            el.classList.remove('active');
                        });
                        if (active.previousElementSibling) active.previousElementSibling.classList.add(
                            'active');
                    }
                } else if (e.key === 'Enter') {
                    var activeItem = _modelBox.querySelector('.model-suggestion-item.active');
                    if (activeItem) {
                        e.preventDefault();
                        fillFromModelSuggestion(activeItem);
                    }
                } else if (e.key === 'Escape') {
                    hideModelSuggestions();
                }
            });
        }

        /* ══════════════════════════════════════════════════════════════════
           ADD DEVICE — Modal open / reset / submit
           ══════════════════════════════════════════════════════════════════ */

        window.openAddDeviceModal = function(prefillAsset) {
            _resetAddDeviceForm();
            if (prefillAsset) {
                var assetEl = document.getElementById('addAsset');
                if (assetEl) assetEl.value = prefillAsset;
            }

            // Auto-fill dept/room from most recently saved device for this site (server-side, persists across logout)
            (function() {
                var deptEl = document.getElementById('addDept');
                var roomEl = document.getElementById('addRoom');
                if (!deptEl || !roomEl) return;
                fetch('<?= site_url("technician/site-inspection/last-device") ?>?site_id=' + encodeURIComponent(
                        SITE_ID), {
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
            })();

            bootstrap.Modal.getOrCreateInstance(document.getElementById('addDeviceModal')).show();
        };

        function _resetAddDeviceForm() {
            ['addAsset', 'addModel', 'addSerial', 'addManufacturer', 'addType', 'addDescription', 'addDept', 'addRoom']
            .forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.value = '';
            });
            var est = document.getElementById('addEST');
            if (est) est.value = 'No';
            var cal = document.getElementById('addCAL');
            if (cal) cal.value = 'No';
            var tech = document.getElementById('addTech');
            if (tech) tech.value = '<?= esc(session('full_name') ?? 'Technician') ?>';
            var errBox = document.getElementById('addDeviceError');
            if (errBox) errBox.classList.add('d-none');
            hideModelSuggestions();
        }

        /* Reset on close */
        var addDeviceModalEl = document.getElementById('addDeviceModal');
        if (addDeviceModalEl) {
            addDeviceModalEl.addEventListener('hidden.bs.modal', _resetAddDeviceForm);
        }

        /* Form submit */
        var addDeviceForm = document.getElementById('addDeviceForm');
        if (addDeviceForm) {
            addDeviceForm.addEventListener('submit', function(e) {
                e.preventDefault();
                _submitAddDevice();
            });
        }

        function _showAddDeviceError(msg) {
            var box = document.getElementById('addDeviceError');
            var txt = document.getElementById('addDeviceErrorText');
            if (!box || !txt) return;
            txt.textContent = msg;
            box.classList.remove('d-none');
        }

        function _submitAddDevice() {
            var errBox = document.getElementById('addDeviceError');
            if (errBox) errBox.classList.add('d-none');

            var assetTag  = (document.getElementById('addAsset').value || '').trim();
            var serialVal = (document.getElementById('addSerial').value || '').trim();

            if (!assetTag) {
                _showAddDeviceError('Asset # is required.');
                document.getElementById('addAsset').focus();
                return;
            }
            if (!SITE_ID) {
                _showAddDeviceError('Site ID is missing. Please reload the page.');
                return;
            }

            // Client-side duplicate check: asset tag
            var existingAssetRow = document.querySelector('#notInspectedTableBody tr[data-asset="' + assetTag.replace(/"/g,'') + '"]')
                || document.querySelector('#allInventoryTableBody tr[data-asset="' + assetTag.replace(/"/g,'') + '"]');
            if (existingAssetRow) {
                Swal.fire({ icon: 'error', title: 'Duplicate Asset #', text: 'Asset # "' + assetTag + '" already exists in this site\'s inventory.' });
                document.getElementById('addAsset').focus();
                return;
            }

            var saveBtn = document.getElementById('saveDeviceBtn');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Saving…';
            }

            var params = new URLSearchParams();
            params.append(CSRF_NAME, csrfHash);
            params.append('site_id', SITE_ID);
            params.append('asset_tag', assetTag);
            params.append('model', (document.getElementById('addModel').value || '').trim());
            params.append('serial_number', (document.getElementById('addSerial').value || '').trim());
            params.append('make', (document.getElementById('addManufacturer').value || '').trim());
            params.append('device_type', (document.getElementById('addType').value || '').trim());
            params.append('description', (document.getElementById('addDescription').value || '').trim());
            params.append('department', (document.getElementById('addDept').value || '').trim());
            params.append('location', (document.getElementById('addRoom').value || '').trim());
            params.append('technician', (document.getElementById('addTech').value || '').trim());
            params.append('est', document.getElementById('addEST').value);
            params.append('cal', document.getElementById('addCAL').value);
            if (window.CURRENT_INSPECTION_GROUP_ID) params.append('group_id', window.CURRENT_INSPECTION_GROUP_ID);

            fetch(ADD_DEVICE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: params.toString()
                })
                .then(function(r) {
                    var ct = (r.headers.get('content-type') || '').toLowerCase();
                    if (ct.includes('application/json')) return r.json();
                    return r.text().then(function(t) {
                        throw new Error('HTTP ' + r.status +
                            ' — CSRF may have expired. Refresh the page.\n' + t.substring(0, 200));
                    });
                })
                .then(function(res) {
                    if (res && res.csrf_hash) csrfHash = res.csrf_hash;
                    if (!res || !res.success) {
                        var errMsg = (res && res.message) || 'Failed to save device. Please try again.';
                        // Show SweetAlert for duplicate errors
                        if (errMsg.indexOf('already exists') !== -1) {
                            Swal.fire({ icon: 'error', title: 'Duplicate Found', text: errMsg });
                        } else {
                            _showAddDeviceError(errMsg);
                        }
                        return;
                    }
                    // Save last used dept/room for this site to localStorage
                    var lastDept = (document.getElementById('addDept').value || '').trim();
                    var lastRoom = (document.getElementById('addRoom').value || '').trim();
                    if (lastDept || lastRoom) {
                        // dept/room is auto-loaded from server on next modal open

                    }
                    bootstrap.Modal.getInstance(document.getElementById('addDeviceModal')).hide();
                    toast('Device "' + assetTag + '" added to site inventory.', 'success');

                    backgroundRefreshTabs(function() {
                        if (res.start_inspection) {
                            var ai = document.getElementById('assetInput');
                            if (ai) {
                                ai.value = assetTag;
                                handleAssetGo();
                            }
                        }
                    });
                })
                .catch(function(err) {
                    _showAddDeviceError(err.message || 'An unexpected error occurred.');
                })
                .finally(function() {
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Save Device';
                    }
                });
        }

        /* ══════════════════════════════════════════════════════════════════
           BACKGROUND REFRESH
           ══════════════════════════════════════════════════════════════════ */
        window.backgroundRefreshTabs = function(cb) {
            $.get(window.location.href, function(html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                ['inspectionTableBody', 'notInspectedTableBody', 'archivedTableBody',
                    'deviceTypeCountsBody', 'workOrdersTableBody', 'allInventoryTableBody',
                    'not-inspected-count', 'inspected-count'
                ].forEach(function(id) {
                    var f = doc.getElementById(id),
                        l = document.getElementById(id);
                    if (f && l) l.innerHTML = f.innerHTML;
                });
                var msgF = doc.getElementById('not-inspected-message'),
                    msgL = document.getElementById('not-inspected-message');
                if (msgF && msgL) msgL.textContent = msgF.textContent;
                var newMeta = doc.querySelector('meta[name="csrf-token"]');
                if (newMeta) csrfHash = newMeta.getAttribute('content');
                if (window.CURRENT_INSPECTION_GROUP_ID) {
                    filterInspectedByGroup(window.CURRENT_INSPECTION_GROUP_ID);
                    filterNotInspectedByGroup(window.CURRENT_INSPECTION_GROUP_ID);
                }
                document.querySelectorAll('#workOrdersTableBody tr').forEach(function(r) {
                    r.style.display = '';
                });
                if (typeof cb === 'function') cb();
            });
        };

        /* ══════════════════════════════════════════════════════════════════
           FILTER HELPERS
           ══════════════════════════════════════════════════════════════════ */
        window.filterInspectedByGroup = function(groupId) {
            var inspRows = document.querySelectorAll('#inspectionTableBody tr[data-group-id]');
            var visible = 0;
            inspRows.forEach(function(r) {
                var show = r.getAttribute('data-group-id') === groupId;
                r.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            var emptyR = document.getElementById('inspectedEmptyRow');
            if (emptyR) emptyR.style.display = visible === 0 ? '' : 'none';

            var counterRows = document.querySelectorAll('#deviceTypeCountsBody tr[data-group-id]');
            var dtCounts = {};
            inspRows.forEach(function(row) {
                if (row.getAttribute('data-group-id') !== groupId) return;
                var dt = row.getAttribute('data-device-type') || 'Unknown';
                var est = (row.getAttribute('data-est') || '0').toLowerCase();
                var cal = (row.getAttribute('data-cal') || '0').toLowerCase();
                if (!dtCounts[dt]) dtCounts[dt] = {
                    total: 0,
                    est: 0,
                    cal: 0
                };
                dtCounts[dt].total++;
                if (est === '1' || est === 'yes' || est === 'true') dtCounts[dt].est++;
                if (cal === '1' || cal === 'yes' || cal === 'true') dtCounts[dt].cal++;
            });
            var cT = 0,
                cE = 0,
                cC = 0,
                cV = 0;
            counterRows.forEach(function(row) {
                var show = row.getAttribute('data-group-id') === groupId;
                row.style.display = show ? '' : 'none';
                if (show) {
                    cV++;
                    var dt = (row.querySelectorAll('td')[0] || {}).textContent || '';
                    var counts = dtCounts[dt.trim()] || {
                        total: 0,
                        est: 0,
                        cal: 0
                    };
                    cT += counts.total;
                    cE += counts.est;
                    cC += counts.cal;
                    var cells = row.querySelectorAll('td');
                    if (cells[1]) cells[1].textContent = counts.total;
                    if (cells[2]) cells[2].textContent = counts.est;
                    if (cells[3]) cells[3].textContent = counts.cal;
                }
            });
            var tr = document.getElementById('deviceCountTotalRow');
            if (tr) tr.style.display = cV > 0 ? '' : 'none';
            ['deviceCountTotal', 'deviceCountTotalEST', 'deviceCountTotalCAL'].forEach(function(id, i) {
                var el = document.getElementById(id);
                if (el) el.textContent = [cT, cE, cC][i];
            });
            var ec = document.getElementById('deviceCountEmptyRow');
            if (ec) ec.style.display = cV === 0 ? '' : 'none';
            var badge = document.getElementById('inspected-count');
            if (badge) badge.textContent = visible > 0 ? visible : '0';
            document.querySelectorAll('#workOrdersTableBody tr').forEach(function(r) {
                r.style.display = '';
            });
        };

        window.filterNotInspectedByGroup = function(groupId) {
            var inspectedEqIds = {};
            document.querySelectorAll('#inspectionTableBody tr[data-group-id="' + groupId + '"]').forEach(function(
                r) {
                var id = r.getAttribute('data-eq-id');
                if (id) inspectedEqIds[id] = true;
            });
            var rows = document.querySelectorAll('#notInspectedTableBody tr[data-eq-id]');
            var rem = 0;
            rows.forEach(function(r) {
                var done = inspectedEqIds[r.getAttribute('data-eq-id')];
                r.style.display = done ? 'none' : '';
                if (!done) rem++;
            });
            var badge = document.getElementById('not-inspected-count');
            if (badge) badge.textContent = rem;
            var msg = document.getElementById('not-inspected-message');
            if (msg) msg.textContent = rem + ' item(s) remaining in site inventory pending inspection.';
            var emp = document.getElementById('notInspectedEmpty');
            if (emp) emp.style.display = (rem === 0 && rows.length > 0) ? '' : 'none';
        };

        /* ══════════════════════════════════════════════════════════════════
           VIEW SWITCHING
           ══════════════════════════════════════════════════════════════════ */
        window.viewInspection = function(groupId, siteName, inspType, inspDisplayId, techName) {
            document.getElementById('insp-site-label').textContent = 'Site: ' + (siteName || '—');
            document.getElementById('insp-title').textContent = inspType || 'Equipment Inspection';
            document.getElementById('insp-id-label').textContent = 'Inspection #' + (inspDisplayId || groupId);
            document.getElementById('insp-technician').textContent = techName || '—';
            window.CURRENT_INSPECTION_GROUP_ID = groupId;
            window.CURRENT_REPORT_GROUP_ID = groupId;
            filterInspectedByGroup(groupId);
            filterNotInspectedByGroup(groupId);
            document.getElementById('view-dashboard').classList.add('d-none-view');
            document.getElementById('view-inspection').classList.remove('d-none-view');
            var wf = document.getElementById('site-inspection-workflow');
            if (wf) wf.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        };

        window.showDashboard = function() {
            document.getElementById('view-inspection').classList.add('d-none-view');
            document.getElementById('view-dashboard').classList.remove('d-none-view');
            window.CURRENT_INSPECTION_GROUP_ID = null;
            window.CURRENT_REPORT_GROUP_ID = null;
        };

        window.updateStatus = function(status) {
            var btn = document.getElementById('statusDropdown');
            if (!btn) return;
            if (status === 'Closed/Complete') {
                btn.className = 'btn btn-light dropdown-toggle status-badge status-completed';
                btn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Closed/Complete';
            } else {
                btn.className = 'btn btn-light dropdown-toggle status-badge status-in-progress';
                btn.innerHTML = '<i class="fa-solid fa-rotate"></i> In Progress';
            }
        };

        window.showWorkOrdersTab = function() {
            document.getElementById('view-dashboard').classList.add('d-none-view');
            document.getElementById('view-inspection').classList.remove('d-none-view');
            document.querySelectorAll('#workOrdersTableBody tr').forEach(function(r) {
                r.style.display = '';
            });
            var btn = document.getElementById('insp-work-orders-tab');
            if (btn) bootstrap.Tab.getOrCreateInstance(btn).show();
        };

        /* ══════════════════════════════════════════════════════════════════
           START INSPECTION
           ══════════════════════════════════════════════════════════════════ */
        window.startInspection = function() {
            window.CURRENT_INSPECTION_GROUP_ID = null;
            window.CURRENT_REPORT_GROUP_ID = null;
            _pendingWOAsset = '';

            document.getElementById('insp-site-label').textContent = 'Site: <?= esc($site['name'] ?? '—') ?>';
            document.getElementById('insp-title').textContent = 'New Inspection';
            document.getElementById('insp-id-label').textContent = '—';
            // Auto-assign the logged-in technician immediately
            document.getElementById('insp-technician').textContent = '<?= esc(session('full_name') ?? '—') ?>';

            var statusBtn = document.getElementById('statusDropdown');
            if (statusBtn) {
                statusBtn.className = 'btn btn-light dropdown-toggle status-badge status-in-progress';
                statusBtn.innerHTML = '<i class="fa-solid fa-rotate"></i> In Progress';
            }
            var ai = document.getElementById('assetInput');
            if (ai) ai.value = '';
            var alertEl = document.getElementById('assetLookupAlert');
            if (alertEl) alertEl.style.display = 'none';
            $('#inspectAsset,#inspectNotes,#inspectDept,#inspectRoom,#inspectSerial').val('');

            var emptyV = document.getElementById('inspectDeviceEmpty');
            var formW = document.getElementById('inspectDeviceFormWrapper');
            if (emptyV) emptyV.classList.remove('d-none');
            if (formW) formW.classList.add('d-none');

            document.querySelectorAll('#notInspectedTableBody tr[data-eq-id]').forEach(function(r) {
                r.style.display = '';
            });
            var total = document.querySelectorAll('#notInspectedTableBody tr[data-eq-id]').length;
            var badge = document.getElementById('not-inspected-count');
            if (badge) badge.textContent = total;

            var nit = document.getElementById('not-inspected-tab');
            if (nit) bootstrap.Tab.getOrCreateInstance(nit).show();

            var rtc = document.getElementById('reportsTabContent');
            if (rtc) rtc.innerHTML =
                '<p class="text-muted text-center py-4">Inspect devices and the report will appear here.</p>';

            document.getElementById('view-dashboard').classList.add('d-none-view');
            document.getElementById('view-inspection').classList.remove('d-none-view');
            setTimeout(function() {
                if (ai) {
                    ai.focus();
                    ai.select();
                }
            }, 100);
        };

        /* ══════════════════════════════════════════════════════════════════
           ASSET LOOKUP
           ══════════════════════════════════════════════════════════════════ */
        window.handleAssetGo = function() {
            var asset = $.trim($('#assetInput').val());
            if (!asset) {
                showAssetAlert('Please enter an Asset number.', 'warning');
                return;
            }
            if (!SITE_ID) {
                showAssetAlert('Site ID is missing. Please reload the page.', 'danger');
                return;
            }

            var goBtn = document.querySelector('#view-inspection .asset-input-wrapper button');
            if (goBtn) {
                goBtn.disabled = true;
                goBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Searching…';
            }

            $.ajax({
                url: URL_GET_EQ,
                method: 'GET',
                dataType: 'json',
                data: {
                    asset_tag: asset,
                    site_id: SITE_ID
                },
                success: function(resp) {
                    if (goBtn) {
                        goBtn.disabled = false;
                        goBtn.innerHTML = '<i class="fa-solid fa-arrow-right"></i> Go';
                    }
                    var alertEl = document.getElementById('assetLookupAlert');
                    if (alertEl) alertEl.style.display = 'none';

                    if (!resp || resp.found === false) {
                        /* Asset not found — automatically open Add Device modal pre-filled */
                        openAddDeviceModal(asset);
                        return;
                    }

                    _pendingWOAsset = resp.asset_tag || asset;
                    var tabBtn = document.getElementById('inspect-device-tab');
                    var emptyV = document.getElementById('inspectDeviceEmpty');
                    var formW = document.getElementById('inspectDeviceFormWrapper');
                    if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
                    if (emptyV) emptyV.classList.add('d-none');
                    if (formW) formW.classList.remove('d-none');

                    $('#inspectAsset').val(resp.asset_tag || '');
                    $('#inspectModelDisplay').text((resp.model && resp.model.length) ? resp.model : (
                        resp.make || '—'));
                    $('#inspectCustomerName').text('<?= esc($customer['name'] ?? '') ?>');
                    $('#inspectDept').val(resp.department || '');
                    $('#inspectRoom').val(resp.location || '');
                    $('#inspectSerial').val(resp.serial_number || '');
                    $('#inspectPMFrequency').val('12 Month');
                    var apSel = document.getElementById('inspectActionPerformed');
                    if (apSel && apSel.options.length > 1) {
                        apSel.selectedIndex = 1;
                    }
                    $('#inspectNotes').val('');
                    setTimeout(function() {
                        if (typeof window._techIqNoteHandler === 'function') {
                            window._techIqNoteHandler();
                        }
                    }, 50);
                },
                error: function(xhr) {
                    if (goBtn) {
                        goBtn.disabled = false;
                        goBtn.innerHTML = '<i class="fa-solid fa-arrow-right"></i> Go';
                    }
                    showAssetAlert('Server error (' + xhr.status + '). Please try again.', 'danger');
                }
            });
        };

        window.inspectFromNotInspected = function(asset) {
            var ai = document.getElementById('assetInput');
            if (ai) ai.value = asset;
            handleAssetGo();
        };

        var assetInputEl = document.getElementById('assetInput');
        if (assetInputEl) {
            assetInputEl.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    handleAssetGo();
                }
            });
        }

        /* ══════════════════════════════════════════════════════════════════
           RECORD INSPECTION
           ══════════════════════════════════════════════════════════════════ */
        function recordInspection(result, onSuccessCallback) {
            var assetTag = $('#inspectAsset').val();
            var payload = {
                site_id: SITE_ID,
                asset_tag: assetTag,
                result: result,
                notes: $('#inspectNotes').val(),
                department: $('#inspectDept').val(),
                room: $('#inspectRoom').val(),
                serial_number: $('#inspectSerial').val(),
                action_performed: $('#inspectActionPerformed').val(),
                pm_frequency: $('#inspectPMFrequency').val()
            };
            if (window.CURRENT_REPORT_GROUP_ID) payload.group_id = window.CURRENT_REPORT_GROUP_ID;
            if (!assetTag) {
                showAssetAlert('Asset tag is missing. Please scan an asset first.', 'warning');
                return;
            }

            $('#btnPassInspection,#btnFailInspection,#btnRepairInspection,#btnFailWOInspection').prop('disabled', true);

            $.post(URL_RECORD, payload, function(resp) {
                $('#btnPassInspection,#btnFailInspection,#btnRepairInspection,#btnFailWOInspection').prop(
                    'disabled', false);

                if (resp && resp.success) {
                    var ng = resp.group_id || null;
                    if (ng) {
                        window.CURRENT_INSPECTION_GROUP_ID = ng;
                        window.CURRENT_REPORT_GROUP_ID = ng;
                        var idLabel = document.getElementById('insp-id-label');
                        if (idLabel && (idLabel.textContent === '—' || !idLabel.textContent.trim())) idLabel
                            .textContent = ng;
                    }

                    $('#assetInput').val('');
                    $('#inspectAsset,#inspectNotes,#inspectDept,#inspectRoom,#inspectSerial').val('');
                    var alertEl = document.getElementById('assetLookupAlert');
                    if (alertEl) alertEl.style.display = 'none';
                    var emptyV = document.getElementById('inspectDeviceEmpty');
                    var formW = document.getElementById('inspectDeviceFormWrapper');
                    if (emptyV) emptyV.classList.remove('d-none');
                    if (formW) formW.classList.add('d-none');

                    backgroundRefreshTabs(function() {
                        var cgid = window.CURRENT_INSPECTION_GROUP_ID || null;
                        if (cgid) {
                            filterInspectedByGroup(cgid);
                            filterNotInspectedByGroup(cgid);
                        }
                        if (cgid) {
                            $.ajax({
                                url: REPORT_DATA_URL + '?group_id=' + encodeURIComponent(cgid),
                                type: 'GET',
                                dataType: 'json',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                success: function(res) {
                                    if (res && res.success) {
                                        var html = generateInspectionReportHTML(res.latest,
                                            res.rows, res.group_id || cgid);
                                        var tc = document.getElementById(
                                            'reportsTabContent');
                                        if (tc) tc.innerHTML = html;
                                        var rc = document.getElementById('reportContent');
                                        if (rc) rc.innerHTML = html;
                                    }
                                }
                            });
                        }
                        toast('Inspection recorded: ' + result, 'success');
                        if (typeof onSuccessCallback === 'function') {
                            onSuccessCallback();
                        } else {
                            var itb = document.getElementById('inspected-tab');
                            if (itb) bootstrap.Tab.getOrCreateInstance(itb).show();
                        }
                        setTimeout(function() {
                            var ai = document.getElementById('assetInput');
                            if (ai) {
                                ai.focus();
                                ai.select();
                            }
                        }, 300);
                    });
                } else {
                    showAssetAlert((resp && resp.message) ? resp.message : 'Failed to record inspection.',
                        'danger');
                }
            }, 'json').fail(function(xhr) {
                $('#btnPassInspection,#btnFailInspection,#btnRepairInspection,#btnFailWOInspection').prop(
                    'disabled', false);
                var errMsg = 'Error recording inspection (HTTP ' + xhr.status + ').';
                try {
                    var json = JSON.parse(xhr.responseText);
                    if (json && json.message) errMsg = json.message;
                } catch (e) {}
                showAssetAlert(errMsg, 'danger');
            });
        }

        /* ══════════════════════════════════════════════════════════════════
           BUTTON WIRING + EDIT / DELETE HANDLERS
           ══════════════════════════════════════════════════════════════════ */
        $(document).ready(function() {

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
                var assetId = $('#inspectAsset').val() || _pendingWOAsset;
                if (!assetId) {
                    showAssetAlert('Please scan or enter an Asset # first.', 'warning');
                    return;
                }
                recordInspection('Fail', function() {
                    showWorkOrdersTab();
                    setTimeout(function() {
                        openWorkOrderModalFromInventory(assetId);
                    }, 350);
                });
            });

            $('#btnCancelInspection').off('click').on('click', function() {
                var emptyV = document.getElementById('inspectDeviceEmpty');
                var formW = document.getElementById('inspectDeviceFormWrapper');
                if (emptyV) emptyV.classList.remove('d-none');
                if (formW) formW.classList.add('d-none');
                var nit = document.getElementById('not-inspected-tab');
                if (nit) bootstrap.Tab.getOrCreateInstance(nit).show();
            });

            $(document).on('click', '.delete-inspection-btn', function() {
                var groupId = $(this).data('id');
                if (!confirm('Delete all inspections in group ' + groupId + '? This cannot be undone.'))
                    return;
                $.post(BASE + '/inspections/deleteGroup', {
                    group_id: groupId
                }, function(res) {
                    if (res && res.success) {
                        toast('Inspection group deleted.', 'warning');
                        location.reload();
                    } else toast('Delete failed.', 'danger');
                }, 'json').fail(function() {
                    toast('Delete request failed.', 'danger');
                });
            });

            $(document).on('click', '.btn-edit-inspected', function() {
                var d = $(this).data();
                $('#editInspId').val(d.id || '');
                $('#editModel').val(d.model || '');
                $('#editType').val(d.type || '');
                $('#editSerial').val(d.serial || '');
                $('#editAsset').val(d.asset || '');
                $('#editDept').val(d.dept || '');
                $('#editRoom').val(d.room || '');
                $('#editTech').val(d.tech || '');
                $('#editNotes').val(d.notes || '');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
            });

            $(document).on('click', '.btn-delete-inspected', function() {
                var id = $(this).data('id');
                var asset = $(this).data('asset') || id;
                if (!confirm('Remove inspection record for asset #' + asset +
                        '?\nThis cannot be undone.')) return;
                fetch(URL_DELETE_INSP + encodeURIComponent(id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: CSRF_NAME + '=' + encodeURIComponent(csrfHash)
                }).then(function(r) {
                    if (r.ok || r.status === 302 || r.redirected) {
                        var row = document.querySelector(
                            '#inspectionTableBody tr[data-row-id="' + id + '"]');
                        if (row) row.remove();
                        backgroundRefreshTabs(function() {
                            toast('Inspection record deleted.', 'warning');
                        });
                    } else {
                        toast('Delete failed (HTTP ' + r.status + ').', 'danger');
                    }
                }).catch(function(err) {
                    toast('Delete failed – ' + err.message, 'danger');
                });
            });

            $('#editInspectedForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#editInspId').val();
                if (!id) return;
                var params = [
                    CSRF_NAME + '=' + encodeURIComponent(csrfHash),
                    'inspection_id' + '=' + encodeURIComponent(id),
                    'notes' + '=' + encodeURIComponent($('#editNotes').val().trim()),
                    'department' + '=' + encodeURIComponent($('#editDept').val().trim()),
                    'location' + '=' + encodeURIComponent($('#editRoom').val().trim()),
                    'serial_number' + '=' + encodeURIComponent($('#editSerial').val().trim()),
                ].join('&');
                var saveBtn = this.querySelector('[type=submit]');
                saveBtn.disabled = true;
                saveBtn.textContent = 'Saving…';
                fetch(URL_UPDATE_INSP, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: params
                    }).then(function(r) {
                        var ct = (r.headers.get('content-type') || '').toLowerCase();
                        if (ct.includes('application/json')) return r.json();
                        return r.text().then(function(t) {
                            throw new Error('Non-JSON: ' + t.substring(0, 200));
                        });
                    }).then(function(data) {
                        if (data.csrf_hash) csrfHash = data.csrf_hash;
                        if (data.success) {
                            backgroundRefreshTabs();
                            toast('Device updated successfully.', 'success');
                            bootstrap.Modal.getInstance(document.getElementById('editModal'))
                                .hide();
                        } else toast(data.message || 'Update failed.', 'danger');
                    }).catch(function(err) {
                        toast('Save failed – ' + err.message, 'danger');
                    })
                    .finally(function() {
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Save Changes';
                    });
            });

            $(document).on('click', '.btn-edit-wo', function() {
                var id = $(this).data('id');
                _resetWO();
                document.getElementById('woId').value = id;
                document.getElementById('workOrderModalTitle').textContent = 'Edit Work Order';
                document.getElementById('saveWorkOrderBtn').textContent = 'Update Work Order';
                $.get(BASE + '/work-orders/show/' + id, function(res) {
                    if (res && res.data) {
                        var d = res.data;
                        $('#woTitle').val(d.title || '');
                        $('#woAsset').val(d.asset_tag || '');
                        $('#woMake').val(d.make || '');
                        $('#woModel').val(d.model || '');
                        $('#woSerial').val(d.serial_number || '');
                        $('#woEquipment').val(d.device_type || '');
                        $('#woPriority').val(d.priority || 'Medium');
                        $('#woStatus').val(d.status || 'Open');
                        $('#woTech').val(d.assigned_to || '');
                        $('#woStartDate').val(d.start_date || '');
                        $('#woEndDate').val(d.end_date || '');
                        $('#woDescription').val(d.description || '');
                        $('#woEquipmentId').val(d.equipment_id || '');
                    }
                }, 'json');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('workOrderModal')).show();
            });

            $(document).on('click', '.btn-delete-wo', function() {
                var id = $(this).data('id');
                if (!confirm('Delete work order #' + id + '?')) return;
                window.location.href = BASE + '/work-orders/delete/' + id;
            });

            /* Work Orders tab — always show all rows */
            var woTabBtn = document.getElementById('insp-work-orders-tab');
            if (woTabBtn) {
                woTabBtn.addEventListener('shown.bs.tab', function() {
                    document.querySelectorAll('#workOrdersTableBody tr').forEach(function(r) {
                        r.style.display = '';
                    });
                });
            }

            /* Reports tab auto-load */
            var reportsTabBtn = document.getElementById('reports-tab');
            if (reportsTabBtn) {
                reportsTabBtn.addEventListener('shown.bs.tab', function() {
                    var container = document.getElementById('reportsTabContent');
                    if (container && container.querySelector('p.fst-italic') === null && container
                        .innerHTML.trim() !== '') return;
                    var groupId = window.CURRENT_INSPECTION_GROUP_ID || window.CURRENT_REPORT_GROUP_ID;
                    if (!groupId) {
                        if (container) container.innerHTML =
                            '<div class="alert alert-info">No inspection loaded yet.</div>';
                        return;
                    }
                    window.CURRENT_REPORT_GROUP_ID = groupId;
                    if (container) container.innerHTML =
                        '<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3 text-muted">Loading…</p></div>';
                    $.ajax({
                        url: REPORT_DATA_URL + '?group_id=' + encodeURIComponent(groupId),
                        type: 'GET',
                        dataType: 'json',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(res) {
                            if (!res || !res.success) {
                                if (container) container.innerHTML =
                                    '<div class="alert alert-warning">No data.</div>';
                                return;
                            }
                            var html = generateInspectionReportHTML(res.latest, res.rows,
                                res.group_id || groupId);
                            if (container) container.innerHTML = html;
                            var rc = document.getElementById('reportContent');
                            if (rc) rc.innerHTML = html;
                        },
                        error: function(xhr) {
                            if (container) container.innerHTML =
                                '<div class="alert alert-danger">Error HTTP ' + xhr.status +
                                '</div>';
                        }
                    });
                });
            }

            /* IQ Notes loader */
            (function() {
                var IQ_URL = '<?= site_url('technician/iq-notes') ?>';
                var sel = document.getElementById('inspectActionPerformed');
                if (!sel) return;
                fetch(IQ_URL, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(r) {
                        return r.json();
                    })
                    .then(function(data) {
                        var notes = (data.data || []);
                        if (!notes.length) return;
                        sel.innerHTML = '<option value="">-- Select Action Performed --</option>';
                        notes.forEach(function(n) {
                            var o = document.createElement('option');
                            o.value = n.note || n.title;
                            o.setAttribute('data-title', n.title || '');
                            o.setAttribute('data-note', n.note || '');
                            o.textContent = n.title || n.note;
                            sel.appendChild(o);
                        });
                        // ADD THIS RIGHT AFTER THE forEach:
                        if (sel.options.length > 1) {
                            sel.selectedIndex = 1;
                        }
                        // Small defer to ensure DOM is settled before reading selected option
                        setTimeout(function() {
                            if (typeof window._techIqNoteHandler === 'function') {
                                window._techIqNoteHandler();
                            }
                        }, 50);
                        // Auto-populate notes textarea when IQ note selected
                        sel.removeEventListener('change', window._techIqNoteHandler);
                        window._techIqNoteHandler = function() {
                            var selected = sel.options[sel.selectedIndex];
                            if (!selected) return;
                            var noteText = selected.getAttribute('data-note') || '';
                            var notesArea = document.getElementById('inspectNotes');
                            if (notesArea && noteText) notesArea.value = noteText;
                        };
                        sel.addEventListener('change', window._techIqNoteHandler); // ← correct name
                        window._techIqNoteHandler(); // ← correct name, fires immediately
                    })
                    .catch(function() {
                        /* keep static defaults on error */
                    });
            })();

            /* Init model search after DOM ready */
            initModelSearch();

        }); /* end $(document).ready */

        /* ══════════════════════════════════════════════════════════════════
           INSPECTION REPORT GENERATION
           ══════════════════════════════════════════════════════════════════ */
        window.openInspectionReport = function(groupId) {
            window.CURRENT_REPORT_GROUP_ID = groupId;
            var rc = document.getElementById('reportContent');
            var modalEl = document.getElementById('inspectionReportModal');
            if (!rc || !modalEl) return;
            rc.innerHTML =
                '<div class="text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3 text-muted">Loading…</p></div>';
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            $.ajax({
                url: REPORT_DATA_URL + '?group_id=' + encodeURIComponent(groupId),
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res) {
                    if (!res || !res.success) {
                        rc.innerHTML = '<div class="alert alert-warning">No data found.</div>';
                        return;
                    }
                    var html = generateInspectionReportHTML(res.latest, res.rows, res.group_id ||
                        groupId);
                    rc.innerHTML = html;
                    var tc = document.getElementById('reportsTabContent');
                    if (tc) tc.innerHTML = html;
                },
                error: function(xhr) {
                    rc.innerHTML = '<div class="alert alert-danger">Error: HTTP ' + xhr.status +
                        '</div>';
                }
            });
        };

        function generateInspectionReportHTML(latest, rows, groupId) {
            var lh = '<p class="text-muted fst-italic">No device data available.</p>';
            if (latest) {
                lh = '<div class="table-responsive mb-4"><table class="table table-striped"><thead><tr>' +
                    '<th>Model</th><th>Type</th><th>S/N</th><th>Action</th><th>Asset #</th><th>Dept</th><th>Room</th><th>Tech</th><th>Notes</th>' +
                    '</tr></thead><tbody><tr>' +
                    '<td>' + escH(latest.model || '—') + '</td><td>' + escH(latest.device_type || '—') + '</td>' +
                    '<td>' + escH(latest.serial_number || 'N/A') + '</td><td>' + escH(latest.action_performed || '—') +
                    '</td>' +
                    '<td>' + escH(latest.asset_tag || '—') + '</td><td>' + escH(latest.dept || '—') + '</td>' +
                    '<td>' + escH(latest.room || '—') + '</td><td>' + escH(latest.technician_name || 'N/A') + '</td>' +
                    '<td>' + escH(latest.notes || '') + '</td>' +
                    '</tr></tbody></table></div>';
            }
            var ra = (rows || []).slice().sort(function(a, b) {
                var o = {
                    'Fail': 0,
                    'Repair': 1,
                    'Pass': 2
                };
                return (o[a.result || a.status] ?? 3) - (o[b.result || b.status] ?? 3);
            });
            var rh = ra.length === 0 ?
                '<tr><td colspan="12" class="text-center text-muted py-3">No inspections found.</td></tr>' :
                ra.map(function(r) {
                    return '<tr><td>' + resultBadge(r.result || r.status) + '</td>' +
                        '<td>' + escH(r.customer_name || r.site_name || '—') + '</td>' +
                        '<td>' + escH(r.model || '—') + '</td><td>' + escH(r.device_type || '—') + '</td>' +
                        '<td>' + escH(r.serial_number || 'N/A') + '</td><td>' + escH(r.action_performed || '—') +
                        '</td>' +
                        '<td>' + escH(r.asset_tag || '—') + '</td><td>' + escH(r.dept || '—') + '</td>' +
                        '<td>' + escH(r.room || '—') + '</td><td>' + escH(r.technician_name || 'N/A') + '</td>' +
                        '<td>' + (r.inspection_date ? formatDate(r.inspection_date) :
                            '<span class="text-muted">—</span>') + '</td>' +
                        '<td>' + escH(r.notes || '') + '</td></tr>';
                }).join('');
            return '<section class="mb-4"><h5 class="fw-semibold">Latest Added Device</h5>' + lh + '</section>' +
                '<section><h5 class="fw-semibold">Inspection Report Overview</h5>' +
                '<div class="table-responsive"><table class="table table-striped"><thead><tr>' +
                '<th>Result</th><th>Customer</th><th>Model</th><th>Type</th><th>S/N</th>' +
                '<th>Action</th><th>Asset #</th><th>Dept</th><th>Room</th><th>Tech</th><th>Date</th><th>Notes</th>' +
                '</tr></thead><tbody>' + rh + '</tbody></table></div></section>';
        }

        window.previewReportPDF = function() {
            var src = document.getElementById('reportsTabContent') || document.getElementById('reportContent');
            if (!src || !src.innerHTML.trim()) {
                alert('Please load a report first.');
                return;
            }
            var win = window.open('', '_blank', 'width=900,height=650,scrollbars=yes');
            if (!win) {
                alert('Please allow pop-ups.');
                return;
            }
            win.document.write(
                '<html><head><title>Inspection Report</title><style>body{font-family:Arial,sans-serif;margin:20px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;font-size:12px}th{background:#f8f9fa}</style></head><body>'
            );
            win.document.write(src.innerHTML);
            win.document.write('</body></html>');
            win.document.close();
            win.focus();
        };

        window.exportReportPDF = function() {
            var src = document.getElementById('reportsTabContent') || document.getElementById('reportContent');
            if (!src || !src.innerHTML.trim()) {
                alert('Please load a report first.');
                return;
            }
            var win = window.open('', '_blank', 'width=900,height=650');
            if (!win) {
                alert('Please allow pop-ups.');
                return;
            }
            win.document.write(
                '<html><head><title>Inspection Report</title><style>body{font-family:Arial,sans-serif;margin:20px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;font-size:12px}th{background:#f8f9fa}@media print{button{display:none}}</style></head><body>'
            );
            win.document.write(src.innerHTML);
            win.document.write('</body></html>');
            win.document.close();
            setTimeout(function() {
                win.focus();
                win.print();
            }, 300);
        };

        /* ══════════════════════════════════════════════════════════════════
           WORK ORDERS
           ══════════════════════════════════════════════════════════════════ */
        window.openWorkOrderModal = function() {
            _resetWO();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('workOrderModal')).show();
        };

        window.openWorkOrderModalFromInventory = function(assetTag) {
            _resetWO();
            var defaultTitle = assetTag ? 'Follow-up: Failed Inspection – ' + assetTag : '';
            document.getElementById('woTitle').value = defaultTitle;
            document.getElementById('woAsset').value = assetTag || '';
            document.getElementById('woDescription').value = assetTag ? 'Device ' + assetTag +
                ' failed inspection. Review and repair required.' : '';
            _loadEqForWO(assetTag || '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('workOrderModal')).show();
        };

        function _resetWO() {
            ['woTitle', 'woAsset', 'woMake', 'woModel', 'woSerial', 'woEquipment', 'woStartDate', 'woEndDate',
                'woDescription'
            ]
            .forEach(function(id) {
                var e = document.getElementById(id);
                if (e) e.value = '';
            });
            $('#woPriority').val('Medium');
            $('#woStatus').val('Open');
            // Pre-select the logged-in technician
            if (LOGGED_IN_TECH_ID) {
                $('#woTech').val(LOGGED_IN_TECH_ID);
            } else {
                $('#woTech').val('');
            }
            var e = document.getElementById('workOrderError');
            if (e) {
                e.classList.add('d-none');
                e.textContent = '';
            }
            $('#woId').val('');
            $('#woEquipmentId').val('');
            document.getElementById('workOrderModalTitle').textContent = 'Add Work Order';
            document.getElementById('saveWorkOrderBtn').textContent = 'Save Work Order';
        }

        function _loadEqForWO(assetTag) {
            if (!assetTag) return;
            $.get(URL_GET_EQ, {
                asset_tag: assetTag,
                site_id: SITE_ID
            }, function(res) {
                if (!res || !res.found) return;
                $('#woMake').val(res.make || '');
                $('#woModel').val(res.model || '');
                $('#woSerial').val(res.serial_number || '');
                $('#woEquipment').val(res.device_type || '');
                $('#woEquipmentId').val(res.id || '');
            }, 'json');
        }

        window.onWOAssetChange = function() {
            _loadEqForWO(($('#woAsset').val() || '').trim());
        };

        window.submitWorkOrder = function() {
            var title = $.trim($('#woTitle').val());
            if (!title) {
                var eb = document.getElementById('workOrderError');
                if (eb) {
                    eb.classList.remove('d-none');
                    eb.textContent = 'Title is required.';
                }
                document.getElementById('woTitle').focus();
                return;
            }
            var woId = document.getElementById('woId').value;
            var url = woId ? (WO_UPDATE_URL + encodeURIComponent(woId)) : WO_CREATE_URL;
            var saveBtn = document.getElementById('saveWorkOrderBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Saving…';

            var params = new URLSearchParams();
            params.append(CSRF_NAME, csrfHash);
            params.append('site_id', SITE_ID);
            params.append('title', title);
            params.append('equipment_id', document.getElementById('woEquipmentId').value);
            params.append('status', document.getElementById('woStatus').value);
            params.append('priority', document.getElementById('woPriority').value);
            params.append('assigned_to', document.getElementById('woTech').value);
            params.append('start_date', document.getElementById('woStartDate').value);
            params.append('end_date', document.getElementById('woEndDate').value);
            params.append('description', document.getElementById('woDescription').value);
            if (window.CURRENT_INSPECTION_GROUP_ID) params.append('group_id', window.CURRENT_INSPECTION_GROUP_ID);

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: params.toString()
                })
                .then(function(r) {
                    var ct = (r.headers.get('content-type') || '').toLowerCase();
                    if (ct.includes('application/json')) return r.json();
                    return r.text().then(function(t) {
                        throw new Error('Server returned HTTP ' + r.status +
                            '. CSRF may have expired — try refreshing the page.');
                    });
                })
                .then(function(res) {
                    if (res && res.csrf_hash) csrfHash = res.csrf_hash;
                    if (!res || !res.success) {
                        var eb = document.getElementById('workOrderError');
                        if (eb) {
                            eb.classList.remove('d-none');
                            eb.textContent = (res && res.message) || 'Failed to save work order.';
                        }
                        return;
                    }
                    bootstrap.Modal.getInstance(document.getElementById('workOrderModal')).hide();
                    toast('Work order created successfully.', 'success');
                    backgroundRefreshTabs();
                })
                .catch(function(err) {
                    var eb = document.getElementById('workOrderError');
                    if (eb) {
                        eb.classList.remove('d-none');
                        eb.textContent = err.message || 'Error saving work order.';
                    }
                })
                .finally(function() {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = 'Save Work Order';
                });
        };

    })();
</script>