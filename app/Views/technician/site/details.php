<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<style>
    .glass-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e9eef5;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        padding: 1.5rem;
        position: relative;
    }

    .back-btn-custom {
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 600;
    }

    .site-header-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: #475569;
        margin: 0;
    }

    .site-info-toggle-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
        box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
    }

    .site-info-toggle-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
        background: linear-gradient(135deg, #0284c7, #1d4ed8);
    }

    .site-info-toggle-btn:focus {
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .site-info-toggle-btn i {
        font-size: 18px;
        font-weight: 700;
        line-height: 1;
        color: #ffffff;
    }

    #siteInfoBody {
        margin-top: 12px;
    }

    .site-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #f1f5f9;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    .site-info-item {
        margin-bottom: 12px;
        font-size: 15px;
        color: #334155;
    }

    .site-info-item strong {
        display: inline-block;
        min-width: 120px;
        color: #0f172a;
        font-weight: 700;
    }

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
        border-bottom: 1px solid #ffffff;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.03);
    }

    .tab-pane .glass-card {
        border-top-left-radius: 0;
    }

    .dataTables_wrapper .dt-buttons .btn {
        margin-right: 8px;
        margin-bottom: 8px;
        border-radius: 8px !important;
        font-size: 14px;
        padding: 7px 14px;
    }

    table.dataTable thead th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 700;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    table.dataTable tbody td {
        vertical-align: middle;
    }

    .btn-info.btn-sm {
        border-radius: 8px;
        padding: 6px 14px;
        font-weight: 600;
        color: #fff;
    }

    @media (max-width: 767px) {
        .site-info-item strong {
            min-width: 100px;
        }

        .site-avatar {
            width: 75px;
            height: 75px;
            margin-bottom: 15px;
        }

        .site-header-title {
            font-size: 1rem;
        }
    }
</style>

<!-- Back Button -->
<button class="btn btn-secondary back-btn-custom mb-3"
    onclick="window.location.href='<?= site_url('technician/sites') ?>'">
    Back to Sites
</button>

<!-- Site Header Card -->
<div class="glass-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="site-header-title">
            <i class="bi bi-info-circle me-1"></i> Site Information
            <?php if (!empty($customer['name'])): ?>
                — <?= esc($customer['name']) ?>
            <?php endif; ?>
        </h6>

        <button class="site-info-toggle-btn" id="toggleSiteInfo" type="button" title="Toggle Site Info">
            <i class="bi bi-chevron-up" id="toggleIcon"></i>
        </button>
    </div>

    <div id="siteInfoBody">
        <div class="row align-items-center">
            <div class="col-md-auto me-4 text-center">
                <?php
                $logoPath = $customer['logo_path'] ?? '';
                $logoFullPath = FCPATH . 'uploads/logos/' . $logoPath;

                if (!empty($logoPath) && file_exists($logoFullPath)) {
                    echo '<img src="' . base_url('uploads/logos/' . $logoPath) . '" class="site-avatar" alt="Logo">';
                } else {
                    $customerName = $customer['name'] ?? 'Unknown';
                    $nameParts = explode(' ', $customerName);
                    if (count($nameParts) >= 2) {
                        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
                    } else {
                        $initials = strtoupper(substr($customerName, 0, 2));
                    }
                    echo '<img src="https://ui-avatars.com/api/?name=' . urlencode($initials) . '&background=e2e8f0&color=0f172a&size=128' . '" class="site-avatar" alt="Logo">';
                }
                ?>
            </div>

            <div class="col-md">
                <div class="row">
                    <div class="col-md-6">
                        <div class="site-info-item"><strong>Site Name:</strong> <?= esc($site['name'] ?? 'N/A') ?></div>
                        <div class="site-info-item"><strong>Site ID:</strong> <?= esc($site['id'] ?? 'N/A') ?></div>
                        <div class="site-info-item"><strong>Email:</strong> <?= esc($site['email'] ?? 'N/A') ?></div>
                        <div class="site-info-item"><strong>Phone:</strong> <?= esc($site['phone'] ?? 'N/A') ?></div>
                    </div>

                    <div class="col-md-6">
                        <div class="site-info-item"><strong>Customer Name:</strong>
                            <?= esc($customer['name'] ?? 'N/A') ?></div>
                        <div class="site-info-item"><strong>Site Address:</strong> <?= esc($site['address'] ?? 'N/A') ?>
                        </div>
                    </div>
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
        function getCurrentDate() {
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            return day + month + year;
        }

        $('#toggleSiteInfo').on('click', function() {
            const body = $('#siteInfoBody');
            const icon = $('#toggleIcon');

            body.slideToggle(250, function() {
                if (body.is(':visible')) {
                    icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
                } else {
                    icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
                }
            });
        });

        const currentDate = getCurrentDate();

        function pdfCustomize(doc) {
            doc.styles.title.fontSize = 13;
            doc.defaultStyle.fontSize = 8;
            doc.pageMargins = [15, 30, 15, 20];

            const table = doc.content[1].table;
            const body = table.body;
            const colCount = body[0].length;

            table.widths = Array(colCount).fill('*');

            doc.styles.tableHeader = {
                bold: true,
                fontSize: 9,
                color: 'black',
                fillColor: '#a4d169',
                alignment: 'left'
            };

            doc.styles.tableBodyEven = {
                fillColor: '#f3f3f3'
            };

            doc.styles.tableBodyOdd = {
                fillColor: '#ffffff'
            };

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