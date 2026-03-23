<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<section id="sites" class="view-section active">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Site Directory</h3>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addSiteModal">
            <i class="fa-solid fa-plus me-2"></i> Add Site
        </button>
    </div>

    <!-- Search -->
    <div class="glass-card mb-4">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="fa-solid fa-search"></i>
            </span>
            <input id="site-search" type="text" class="form-control border-start-0 ps-0"
                placeholder="Search by site, address or customer name...">
        </div>
    </div>

    <!-- Customer Filter -->
    <div class="glass-card mb-4">
        <label class="form-label fw-bold">Filter by Customer</label>
        <select id="customer-filter" class="form-select" style="width:25%">
            <option value="">All Customers</option>
            <?php
            $uniqueCustomers = array_unique(array_column($sites, 'customer_name'));
            foreach ($uniqueCustomers as $customer):
            ?>
                <option value="<?= esc($customer) ?>">
                    <?= esc($customer) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Sites Table -->
    <div class="glass-card">
        <table id="sites-datatable" class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Site Name</th>
                    <th>Customer Name</th>
                    <th>Site Address</th>
                    <th>Site Contact Name</th>
                    <th>Site Phone Number</th>
                    <th>Site Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sites)) : ?>
                    <?php foreach ($sites as $site) : ?>
                        <tr>
                            <td><?= esc($site['site_name']) ?></td>
                            <td><?= esc($site['customer_name']) ?></td>
                            <td><?= esc($site['site_address']) ?></td>
                            <td><?= esc($site['site_contact_name']) ?></td>
                            <td><?= esc($site['site_phone']) ?></td>
                            <td><?= esc($site['site_email']) ?></td>
                            <td>
                                <a href="<?= base_url('technician/sites/view/' . $site['id']) ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No sites found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>


<!-- ═══════════════════════════════════════════════════════
     ADD SITE MODAL
════════════════════════════════════════════════════════ -->
<div class="modal fade" id="addSiteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addSiteForm" method="POST" action="<?= site_url('technician/sites/add') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-building me-2"></i>Add Site
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3 mb-3">
                        <!-- Site Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Site Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="add-site-name" name="name"
                                placeholder="Enter site name" required>
                        </div>

                        <!-- Customer Dropdown -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Customer <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="add-site-customer" name="customer_id" required>
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= esc($customer['id']) ?>">
                                        <?= esc($customer['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <input type="text" class="form-control" id="add-site-address" name="address"
                                placeholder="Street address">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" class="form-control" id="add-site-city" name="city" placeholder="City">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">State</label>
                            <input type="text" class="form-control" id="add-site-state" name="state"
                                placeholder="State">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Zip</label>
                            <input type="text" class="form-control" id="add-site-zip" name="zip" placeholder="Zip code">
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contact Name</label>
                            <input type="text" class="form-control" id="add-site-contact" name="contact_name"
                                placeholder="Contact person">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="add-site-email" name="email"
                                placeholder="email@example.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" class="form-control" id="add-site-phone" name="phone"
                                placeholder="Phone number">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveSiteBtn">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {

        // ── DataTable safe init ──────────────────────────────────────
        var table;
        if ($.fn.dataTable.isDataTable('#sites-datatable')) {
            table = $('#sites-datatable').DataTable();
        } else {
            table = $('#sites-datatable').DataTable({
                dom: 'Bfrtip',
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                buttons: [{
                        extend: 'copy',
                        text: 'Copy',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        filename: function() {
                            const d = new Date();
                            return 'Technician_Sites_' +
                                String(d.getDate()).padStart(2, '0') +
                                String(d.getMonth() + 1).padStart(2, '0') +
                                d.getFullYear();
                        },
                        title: 'Technician Sites',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Technician Sites',
                        filename: function() {
                            const d = new Date();
                            return 'Technician_Sites_' +
                                String(d.getDate()).padStart(2, '0') +
                                String(d.getMonth() + 1).padStart(2, '0') +
                                d.getFullYear();
                        },
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        customize: function(doc) {
                            doc.styles.title.fontSize = 13;
                            doc.styles.tableHeader.fontSize = 9;
                            doc.defaultStyle.fontSize = 8;
                            doc.pageMargins = [15, 30, 15, 20];
                            const tbl = doc.content[1].table;
                            const colCount = tbl.body[0].length;
                            tbl.widths = Array(colCount).fill('*');
                            doc.styles.tableHeader = {
                                bold: true,
                                fontSize: 9,
                                color: 'black',
                                fillColor: '#a4d169',
                                alignment: 'left'
                            };
                            tbl.layout = {
                                hLineWidth: () => 0.8,
                                vLineWidth: () => 0.8,
                                hLineColor: () => '#cccccc',
                                vLineColor: () => '#cccccc',
                                paddingLeft: () => 4,
                                paddingRight: () => 4,
                                paddingTop: () => 3,
                                paddingBottom: () => 3
                            };
                        }
                    }
                ]
            });
        }

        // ── DataTable search & filter ────────────────────────────────
        $('#site-search').on('keyup', function() {
            table.search(this.value).draw();
        });

        $('#customer-filter').on('change', function() {
            const val = this.value;
            if (val === '') {
                table.column(1).search('').draw();
            } else {
                table.column(1).search('^' + val + '$', true, false).draw();
            }
        });

        // ── Reset Add Site modal on close ────────────────────────────
        $('#addSiteModal').on('hidden.bs.modal', function() {
            $('#addSiteForm')[0].reset();
        });

        // ── Save Site AJAX ───────────────────────────────────────────
        $('#saveSiteBtn').on('click', function() {

            // Basic validation
            if (!$('#add-site-name').val().trim()) {
                Swal.fire('Validation', 'Site name is required.', 'warning');
                return;
            }
            if (!$('#add-site-customer').val()) {
                Swal.fire('Validation', 'Please select a customer.', 'warning');
                return;
            }

            var formData = new FormData($('#addSiteForm')[0]);

            $.ajax({
                type: 'POST',
                url: '<?= site_url('technician/sites/add') ?>',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success!', 'Site added successfully!', 'success').then(
                            () => {
                                $('#addSiteModal').modal('hide');
                                location.reload();
                            });
                    } else {
                        Swal.fire('Error!', response.message || 'Could not save site.',
                            'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                }
            });
        });

    });
</script>

<?= $this->endSection() ?>