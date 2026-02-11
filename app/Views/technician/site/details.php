<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<style>
    .glass-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        padding: 1.5rem;
    }
</style>

<!-- Back Button -->
<button class="btn btn-secondary mb-3" onclick="window.location.href='<?= site_url('technician/sites') ?>'">
    Back to Sites
</button>

<!-- Site Header Card -->
<div class="glass-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-auto me-4">
            <?php
            $logoPath = $customer['logo_path'] ?? '';
            $logoFullPath = FCPATH . 'uploads/logos/' . $logoPath;

            if (!empty($logoPath) && file_exists($logoFullPath)) {
                echo '<img src="' . base_url('uploads/logos/' . $logoPath) . '" class="rounded-circle" width="80" alt="Logo">';
            } else {
                $customerName = $customer['name'] ?? 'Unknown';
                $nameParts = explode(' ', $customerName);
                if (count($nameParts) >= 2) {
                    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
                } else {
                    $initials = strtoupper(substr($customerName, 0, 2));
                }
                echo '<img src="https://ui-avatars.com/api/?name=' . urlencode($initials) . '" class="rounded-circle" width="80" alt="Logo">';
            }
            ?>
        </div>
        <div class="col-md">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Site Name:</strong> <span><?= esc($site['name'] ?? 'N/A') ?></span></p>
                    <p><strong>Site ID:</strong> <span><?= esc($site['id'] ?? 'N/A') ?></span></p>
                    <p><strong>Email:</strong> <span><?= esc($site['email'] ?? 'N/A') ?></span></p>
                    <p><strong>Phone:</strong> <span><?= esc($site['phone'] ?? 'N/A') ?></span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Customer Name:</strong> <span><?= esc($customer['name'] ?? 'N/A') ?></span></p>
                    <p><strong>Site Address:</strong> <span><?= esc($site['address'] ?? 'N/A') ?></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs" id="site-details-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#site-equipment"
            type="button" role="tab" aria-controls="site-equipment" aria-selected="true">Equipment</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="inspections-tab" data-bs-toggle="tab" data-bs-target="#site-inspections"
            type="button" role="tab" aria-controls="site-inspections" aria-selected="false">Inspections</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="work-orders-tab" data-bs-toggle="tab" data-bs-target="#site-work-orders"
            type="button" role="tab" aria-controls="site-work-orders" aria-selected="false">Work Orders</button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="site-details-tabs-content">

    <!-- Equipment Tab -->
    <div class="tab-pane fade show active" id="site-equipment" role="tabpanel" aria-labelledby="equipment-tab">
        <div class="glass-card">
            <table id="equipment-datatable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Asset Tag</th>
                        <th>Serial Number</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Device Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipment)): ?>
                        <?php foreach ($equipment as $eq): ?>
                            <tr>
                                <td><?= esc($eq['asset_tag'] ?? 'N/A') ?></td>
                                <td><?= esc($eq['serial_number'] ?? 'N/A') ?></td>
                                <td><?= esc($eq['make'] ?? 'N/A') ?></td>
                                <td><?= esc($eq['model'] ?? 'N/A') ?></td>
                                <td><?= esc($eq['device_type'] ?? 'N/A') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info">View</button>
                                    <!-- <button class="btn btn-sm btn-secondary">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button> -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inspections Tab -->
    <div class="tab-pane fade" id="site-inspections" role="tabpanel" aria-labelledby="inspections-tab">
        <div class="glass-card">
            <table id="inspections-datatable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Inspection ID</th>
                        <th>Date</th>
                        <th>Technician</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inspections)): ?>
                        <?php foreach ($inspections as $insp): ?>
                            <tr>
                                <td><?= esc($insp['group_id'] ?? 'N/A') ?></td>
                                <td><?= !empty($insp['scheduled_at']) ? date('Y-m-d', strtotime($insp['scheduled_at'])) : 'N/A' ?>
                                </td>
                                <td><?= esc($insp['technician_name'] ?? 'Unassigned') ?></td>
                                <td><?= esc($insp['status'] ?? 'Pending') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info">View</button>
                                    <!-- <button class="btn btn-sm btn-secondary">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button> -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Work Orders Tab -->
    <div class="tab-pane fade" id="site-work-orders" role="tabpanel" aria-labelledby="work-orders-tab">
        <div class="glass-card">
            <table id="work-orders-datatable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Work Order ID</th>
                        <th>Date</th>
                        <th>Technician</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($workOrders)): ?>
                        <?php foreach ($workOrders as $wo): ?>
                            <tr>
                                <td>WO-<?= esc($wo['id']) ?></td>
                                <td><?= !empty($wo['created_at']) ? date('Y-m-d', strtotime($wo['created_at'])) : 'N/A' ?></td>
                                <td><?= esc($wo['technician_name'] ?? 'Unassigned') ?></td>
                                <td><?= esc($wo['status'] ?? 'Open') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info">View</button>
                                    <!-- <button class="btn btn-sm btn-secondary">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button> -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    $(document).ready(function() {
        // Get current date in DDMMYYYY format
        function getCurrentDate() {
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            return day + month + year;
        }

        const currentDate = getCurrentDate();

        /* COMMON PDF STYLE FUNCTION */
        function pdfCustomize(doc) {

            doc.styles.title.fontSize = 13;
            doc.defaultStyle.fontSize = 8;

            doc.pageMargins = [15, 30, 15, 20];

            const table = doc.content[1].table;
            const body = table.body;
            const colCount = body[0].length;

            /* AUTO WIDTH */
            table.widths = Array(colCount).fill('*');

            /* HEADER STYLE */
            doc.styles.tableHeader = {
                bold: true,
                fontSize: 9,
                color: 'black',
                fillColor: '#a4d169',
                alignment: 'left'
            };

            /* ROW COLORS */
            doc.styles.tableBodyEven = {
                fillColor: '#f3f3f3'
            };
            doc.styles.tableBodyOdd = {
                fillColor: '#ffffff'
            };

            /* BORDERS */
            table.layout = {
                hLineWidth: function() {
                    return 0.8;
                },
                vLineWidth: function() {
                    return 0.8;
                },
                hLineColor: function() {
                    return '#cccccc';
                },
                vLineColor: function() {
                    return '#cccccc';
                },
                paddingLeft: function() {
                    return 4;
                },
                paddingRight: function() {
                    return 4;
                },
                paddingTop: function() {
                    return 3;
                },
                paddingBottom: function() {
                    return 3;
                }
            };

            /* WORD WRAP */
            body.forEach(function(row, rowIndex) {
                row.forEach(function(cell) {
                    if (rowIndex === 0) return;

                    if (typeof cell.text === 'string') {
                        cell.text = cell.text.replace(/(.{35})/g, '$1\n');
                    }

                    cell.noWrap = false;
                    cell.alignment = 'left';
                });
            });
        }

        /* ================= EQUIPMENT ================= */
        $('#equipment-datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    title: 'Equipment_' + currentDate
                },
                {
                    extend: 'excel',
                    filename: 'Equipment_' + currentDate
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Equipment',
                    filename: 'Equipment_' + currentDate,
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },
                    customize: pdfCustomize
                }
            ]
        });

        /* ================= INSPECTIONS ================= */
        $('#inspections-datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    title: 'Inspections_' + currentDate
                },
                {
                    extend: 'excel',
                    filename: 'Inspections_' + currentDate
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Inspections',
                    filename: 'Inspections_' + currentDate,
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },
                    customize: pdfCustomize
                }
            ]
        });

        /* ================= WORK ORDERS ================= */
        $('#work-orders-datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    title: 'WorkOrders_' + currentDate
                },
                {
                    extend: 'excel',
                    filename: 'WorkOrders_' + currentDate
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Work Orders',
                    filename: 'WorkOrders_' + currentDate,
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },
                    customize: pdfCustomize
                }
            ]
        });
    });
</script>

<?= $this->endSection() ?>