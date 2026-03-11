<?php
/**
 * Site Details View
 * 
 * Displays complete site information including:
 * - Site details and customer information
 * - Equipment tab with all equipment at this site
 * - Inspections tab with inspection history
 * - Work Orders tab with work order history
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<style>
    .site-header {
        background: #fff;
        border-radius: 8px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .site-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #e0e7ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 600;
        color: #4f46e5;
    }
    
    .site-info {
        display: flex;
        gap: 2rem;
    }
    
    .site-info-left, .site-info-right {
        flex: 1;
    }
    
    .site-info-item {
        margin-bottom: 1rem;
    }
    
    .site-info-label {
        font-weight: 600;
        color: #374151;
        display: inline-block;
        min-width: 180px;
    }
    
    .site-info-value {
        color: #6b7280;
    }
    
    .nav-tabs {
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 2rem;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6b7280;
        padding: 1rem 2rem;
        font-weight: 500;
        border-bottom: 3px solid transparent;
    }
    
    .nav-tabs .nav-link:hover {
        border-bottom: 3px solid #d1d5db;
    }
    
    .nav-tabs .nav-link.active {
        color: #2563eb;
        border-bottom: 3px solid #2563eb;
        background: transparent;
    }
    
    .tab-content {
        background: #fff;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .table-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .export-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    
    
    /* .search-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
     */
    /* .search-box input {
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        width: 300px;
    }
     */
    .btn-action {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 4px;
        margin: 0 0.25rem;
    }
    
    .btn-view {
        background: #06b6d4;
        color: white;
        border: none;
    }
    
    .btn-view:hover {
        background: #0891b2;
    }
    
    .btn-edit {
        background: #64748b;
        color: white;
        border: none;
    }
    
    .btn-edit:hover {
        background: #475569;
    }
    
    .btn-delete {
        background: #ef4444;
        color: white;
        border: none;
    }
    
    .btn-delete:hover {
        background: #dc2626;
    }
    
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .status-ready {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-need-attention {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-scheduled {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .status-completed {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-open {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-in-progress {
        background: #dbeafe;
        color: #1e40af;
    }
</style>

<!-- Back Button -->
<div class="mb-3 topbar">
    <a href="<?= site_url('admin/sites') ?>" class="btn btn-primary">
        <i class="fa fa-arrow-left me-2"></i> Back to Sites
    </a>
</div>

<!-- Site Header with Information -->
<div class="site-header">
    <div class="d-flex align-items-start gap-4">
        <!-- Site Avatar -->
        <div class="site-avatar">
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
        
        <!-- Site Information -->
        <div class="flex-grow-1">
            <div class="site-info">
                <div class="site-info-left">
                    <div class="site-info-item">
                        <span class="site-info-label">Site Name:</span>
                        <span class="site-info-value"><?= esc($site['name']) ?></span>
                    </div>
                    <div class="site-info-item">
                        <span class="site-info-label">Site ID:</span>
                        <span class="site-info-value"><?= esc($site['id']) ?></span>
                    </div>
                    <div class="site-info-item">
                        <span class="site-info-label">Site Contact Name:</span>
                        <span class="site-info-value"><?= esc($site['contact_name']) ?></span>
                    </div>
                    <div class="site-info-item">
                        <span class="site-info-label">Site Email:</span>
                        <span class="site-info-value"><?= esc($site['email']) ?></span>
                    </div>
                    <div class="site-info-item">
                        <span class="site-info-label">Site Phone Number:</span>
                        <span class="site-info-value"><?= esc($site['phone']) ?></span>
                    </div>
                </div>
                
                <div class="site-info-right">
                    <div class="site-info-item">
                        <span class="site-info-label">Customer Name:</span>
                        <span class="site-info-value"><?= esc($customer['name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="site-info-item">
                        <span class="site-info-label">Site Address:</span>
                        <span class="site-info-value"><?= esc($site['address']) ?>, <?= esc($site['city']) ?></span>
                    </div>
                    <div class="site-info-item">
                        <span class="site-info-label">State:</span>
                        <span class="site-info-value"><?= esc($site['state']) ?></span>
                    </div>
                    <div class="site-info-item">
                        <span class="site-info-label">Zip code:</span>
                        <span class="site-info-value"><?= esc($site['zip']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs" id="siteTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#equipment" type="button" role="tab">
            Equipment
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="inspections-tab" data-bs-toggle="tab" data-bs-target="#inspections" type="button" role="tab">
            Inspections
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="work-orders-tab" data-bs-toggle="tab" data-bs-target="#work-orders" type="button" role="tab">
            Work Orders
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="siteTabsContent">
    
    <!-- Equipment Tab -->
    <div class="tab-pane fade show active" id="equipment" role="tabpanel">
        <div class="table-actions">
            <div class="export-buttons">
                <button class="export-btn" onclick="exportTable('copy', 'equipmentTable')"><i class="fa fa-copy me-1"></i> Copy</button>
                <button class="export-btn" onclick="exportTable('excel', 'equipmentTable')"><i class="fa fa-file-excel me-1"></i> Excel</button>
                <button class="export-btn" onclick="exportTable('csv', 'equipmentTable')"><i class="fa fa-file-csv me-1"></i> CSV</button>
                <button class="export-btn" onclick="exportTable('pdf', 'equipmentTable')"><i class="fa fa-file-pdf me-1"></i> PDF</button>
                <!-- CORRECTED: This button now properly passes site_id -->
                <button class="btn btn-primary" onclick="window.location.href='<?= site_url('admin/equipment/add?site_id=' . $site['id']) ?>'">
                    <i class="fa fa-plus me-1"></i> Add Equipment
                </button>
            </div>
            <div class="search-box">
                <label>Search:</label>
                <input type="text" id="equipmentSearch" placeholder="Search equipment...">
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped" id="equipmentTable">
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
                    <?php if (empty($equipment)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No equipment found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($equipment as $eq): ?>
                            <tr>
                                <td><?= esc($eq['asset_tag']) ?></td>
                                <td><?= esc($eq['make']) ?></td>
                                <td><?= esc($eq['model']) ?></td>
                                <td><?= esc($eq['serial_number']) ?></td>
                                <td><?= esc($eq['device_type']) ?></td>
                                <td><?= esc($eq['location']) ?></td>
                                <td><?= esc($eq['department']) ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'status-badge ';
                                    $status = strtolower($eq['status']);
                                    if ($status === 'ready') {
                                        $statusClass .= 'status-ready';
                                    } elseif (str_contains($status, 'need') || str_contains($status, 'attention')) {
                                        $statusClass .= 'status-need-attention';
                                    } else {
                                        $statusClass .= 'status-pending';
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= esc($eq['status']) ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('admin/equipment/view/' . $eq['id']) ?>" class="btn btn-sm btn-action btn-view">
                                        View
                                    </a>
                                    <a href="<?= site_url('admin/equipment/edit/' . $eq['id']) ?>" class="btn btn-sm btn-action btn-edit">
                                        Edit
                                    </a>
                                    <a href="<?= site_url('admin/equipment/delete/' . $eq['id']) ?>" 
                                       class="btn btn-sm btn-action btn-delete"
                                       onclick="return confirm('Are you sure you want to delete this equipment?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>Showing 1 to <?= count($equipment) ?> of <?= count($equipment) ?> entries</div>
            <div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Inspections Tab -->
    <div class="tab-pane fade" id="inspections" role="tabpanel">
        <div class="table-actions">
            <div class="export-buttons">
                <button class="export-btn" onclick="exportTable('copy', 'inspectionTable')"><i class="fa fa-copy me-1"></i> Copy</button>
                <button class="export-btn" onclick="exportTable('excel', 'inspectionTable')"><i class="fa fa-file-excel me-1"></i> Excel</button>
                <button class="export-btn" onclick="exportTable('csv', 'inspectionTable')"><i class="fa fa-file-csv me-1"></i> CSV</button>
                <button class="export-btn" onclick="exportTable('pdf', 'inspectionTable')"><i class="fa fa-file-pdf me-1"></i> PDF</button>
                <!-- CORRECTED: This button now properly passes site_id -->
                <button class="btn btn-primary" onclick="window.location.href='<?= site_url('admin/inspections/add?site_id=' . $site['id']) ?>'">
                    <i class="fa fa-plus me-1"></i> Add Inspection
                </button>
            </div>
            <div class="search-box">
                <label>Search:</label>
                <input type="text" id="inspectionSearch" placeholder="Search inspections...">
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped" id="inspectionTable">
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
                    <?php if (empty($inspections)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No inspections found</td>
                        </tr>
                    <?php else: ?>
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
                                <td class="text-center">
                                    <a href="<?= site_url('admin/inspections/view/' . $insp['id']) ?>" class="btn btn-sm btn-action btn-view">
                                        View
                                    </a>
                                    <a href="<?= site_url('admin/inspections/edit/' . $insp['id']) ?>" class="btn btn-sm btn-action btn-edit">
                                        Edit
                                    </a>
                                    <a href="<?= site_url('admin/inspections/delete/' . $insp['id']) ?>" 
                                       class="btn btn-sm btn-action btn-delete"
                                       onclick="return confirm('Are you sure you want to delete this inspection?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>Showing 1 to <?= count($inspections) ?> of <?= count($inspections) ?> entries</div>
            <div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Work Orders Tab -->
    <div class="tab-pane fade" id="work-orders" role="tabpanel">
        <div class="table-actions">
            <div class="export-buttons">
                <button class="export-btn" onclick="exportTable('copy', 'workOrderTable')"><i class="fa fa-copy me-1"></i> Copy</button>
                <button class="export-btn" onclick="exportTable('excel', 'workOrderTable')"><i class="fa fa-file-excel me-1"></i> Excel</button>
                <button class="export-btn" onclick="exportTable('csv', 'workOrderTable')"><i class="fa fa-file-csv me-1"></i> CSV</button>
                <button class="export-btn" onclick="exportTable('pdf', 'workOrderTable')"><i class="fa fa-file-pdf me-1"></i> PDF</button>
                <!-- CORRECTED: This button now properly passes site_id -->
                <button class="btn btn-primary" onclick="window.location.href='<?= site_url('admin/work-orders/add?site_id=' . $site['id']) ?>'">
                    <i class="fa fa-plus me-1"></i> Add Work Order
                </button>
            </div>
            <div class="search-box">
                <label>Search:</label>
                <input type="text" id="workOrderSearch" placeholder="Search work orders...">
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped" id="workOrderTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Equipment</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($workOrders)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No work orders found</td>
                        </tr>
                    <?php else: ?>
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
                                <td class="text-center">
                                    <a href="<?= site_url('admin/work-orders/view/' . $wo['id']) ?>" class="btn btn-sm btn-action btn-view">
                                        View
                                    </a>
                                    <a href="<?= site_url('admin/work-orders/edit/' . $wo['id']) ?>" class="btn btn-sm btn-action btn-edit">
                                        Edit
                                    </a>
                                    <a href="<?= site_url('admin/work-orders/delete/' . $wo['id']) ?>" 
                                       class="btn btn-sm btn-action btn-delete"
                                       onclick="return confirm('Are you sure you want to delete this work order?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>Showing 1 to <?= count($workOrders) ?> of <?= count($workOrders) ?> entries</div>
            <div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
    // Search functionality for each tab
    document.getElementById('equipmentSearch')?.addEventListener('input', function(e) {
        filterTable('equipmentTable', e.target.value);
    });
    
    document.getElementById('inspectionSearch')?.addEventListener('input', function(e) {
        filterTable('inspectionTable', e.target.value);
    });
    
    document.getElementById('workOrderSearch')?.addEventListener('input', function(e) {
        filterTable('workOrderTable', e.target.value);
    });
    
    function filterTable(tableId, searchTerm) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        searchTerm = searchTerm.toLowerCase();
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        }
    }
    
    // Export function - Enhanced version with table parameter
    function exportTable(format, tableId) {
        // Get the table
        const table = document.getElementById(tableId);
        const tableName = tableId.replace('Table', '');
        
        if (format === 'copy') {
            // Copy table to clipboard
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            window.getSelection().removeAllRanges();
            alert('Table copied to clipboard!');
        } else if (format === 'csv') {
            // Export to CSV
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (let row of rows) {
                let cols = row.querySelectorAll('td, th');
                let csvRow = [];
                for (let col of cols) {
                    // Skip actions column
                    if (col.textContent.trim() !== 'Actions') {
                        csvRow.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
                    }
                }
                csv.push(csvRow.join(','));
            }
            
            // Download CSV
            const csvString = csv.join('\n');
            const blob = new Blob([csvString], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = tableName + '_export.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        } else {
            alert('Export to ' + format + ' for ' + tableName + ' - Full implementation would be added here');
        }
    }
</script>

<?= $this->endSection() ?>