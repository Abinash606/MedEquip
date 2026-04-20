<?php

/**
 * Admin Sites index view.
 *
 * Displays a list of sites for the current company and allows the super admin
 * to add new sites or edit/delete existing ones. Each site belongs to a
 * customer. The modal form includes fields for site details and selects
 * the appropriate customer. An optional query parameter `customer_id`
 * preselects the customer and automatically opens the Add Site modal.
 */
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
// Build a lookup array of customer names keyed by ID for display
$customerMap = [];
foreach ($customers as $cust) {
    $customerMap[$cust['id']] = $cust['name'];
}
?>


<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <h3 class="fw-bold mb-0">Site Directory</h3>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#siteModal">
        <i class="fa-solid fa-sitemap me-2"></i> Add Site
    </button>
</div>
<div class="content">
    <!-- Filter by Customer -->
    <div class="glass-card mb-4 p-3">
        <div class="row align-items-end">
            <div class="col-md-6 mb-2">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                    <input type="text" id="site-search" class="form-control border-start-0 ps-0" placeholder="Search for sites by name or address...">
                </div>
            </div>
            <div class="col-md-6 mb-2">
                <div>
                    <label for="customer-filter" class="form-label fw-bold">Filter by Customer</label>
                    <select id="customer-filter" class="form-select">
                        <option value="">All Customers</option>
                        <?php foreach ($customers as $cust): ?>
                            <option value="<?= esc($cust['id']) ?>"><?= esc($cust['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>



    <!-- Sites Table -->
    <div class="glass-card p-3">
        <div class="table-responsive">
            <table class="table " id="sitesTable">
                <thead class="">
                    <tr>
                        <th>Site Name</th>
                        <th>Customer Name</th>
                        <th>Site Address</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Site Contact Name</th>
                        <th>Site Phone Number</th>
                        <th>Site Email</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sites)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No sites found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sites as $site): ?>
                            <tr data-customer-id="<?= $site['customer_id'] ?>">
                                <td>
                                    <a href="<?= site_url('admin/sites/' . $site['id']) ?>" class="text-primary fw-bold">
                                        <?= esc($site['name']) ?>
                                    </a>
                                </td>
                                <td><?= esc($customerMap[$site['customer_id']] ?? '') ?></td>
                                <td><?= esc($site['address'] ?? '') ?></td>
                                <td><?= esc($site['city'] ?? '') ?></td>
                                <td><?= esc($site['state'] ?? '') ?></td>
                                <td><?= esc($site['contact_name'] ?? '') ?></td>
                                <td><?= esc($site['phone'] ?? '') ?></td>
                                <td><?= esc($site['email'] ?? '') ?></td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <!-- Edit button: populate modal with site data -->
                                        <button type="button" class="btn btn-sm btn-primary edit-site-btn" title="Edit"
                                            data-id="<?= $site['id'] ?>"
                                            data-name="<?= esc($site['name'], 'attr') ?>"
                                            data-customer_id="<?= esc($site['customer_id'], 'attr') ?>"
                                            data-address="<?= esc($site['address'] ?? '', 'attr') ?>"
                                            data-city="<?= esc($site['city'] ?? '', 'attr') ?>"
                                            data-state="<?= esc($site['state'] ?? '', 'attr') ?>"
                                            data-zip="<?= esc($site['zip'] ?? '', 'attr') ?>"
                                            data-contact_name="<?= esc($site['contact_name'] ?? '', 'attr') ?>"
                                            data-email="<?= esc($site['email'] ?? '', 'attr') ?>"
                                            data-phone="<?= esc($site['phone'] ?? '', 'attr') ?>"
                                            class="btn btn-sm btn-outline-secondary edit-site-btn">
                                            <i class="fa fa-pen"></i> Edit
                                        </button>

                                        <!-- Delete button -->
                                        <a href="<?= site_url('admin/sites/delete/' . $site['id']) ?>" class="btn btn-sm btn-danger" title="Delete"
                                            onclick="techDeleteSiteConfirm(event, this); return false;">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
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
</div>
<!-- Site Modal -->
<div class="modal fade" id="siteModal" tabindex="-1" aria-labelledby="siteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="siteForm" method="post" action="<?= site_url('admin/sites/add') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="siteModalLabel">Add Site</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="site-id" name="id">

                    <!-- Site Details Fields -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="site-name">Site Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="site-name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="site-customer-id">Customer<span class="text-danger">*</span></label>
                            <select class="form-select" id="site-customer-id" name="customer_id" required>
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $cust): ?>
                                    <option value="<?= $cust['id'] ?>"><?= esc($cust['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label" for="site-address">Address</label>
                            <input type="text" class="form-control" id="site-address" name="address">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="site-city">City</label>
                            <input type="text" class="form-control" id="site-city" name="city">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="site-state">State</label>
                            <input type="text" class="form-control" id="site-state" name="state">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="site-zip">Zip</label>
                            <input type="text" class="form-control" id="site-zip" name="zip">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="site-contact">Contact Name</label>
                            <input type="text" class="form-control" id="site-contact" name="contact_name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="site-email">Email</label>
                            <input type="email" class="form-control" id="site-email" name="email">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="site-phone">Phone</label>
                            <input type="text" class="form-control" id="site-phone" name="phone">
                        </div>
                    </div>
                </div>
                <div id="siteFormError" class="alert alert-danger mx-3 d-none" role="alert"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>




<script>
    $(document).ready(function() {
        // Initialize DataTable with search and filter features
        var table = $('#sitesTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: ':visible:not(:last-child)' // remove action column
                    }
                },
                {
                    extend: 'excelHtml5',
                    filename: 'Sites',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'csvHtml5',
                    filename: 'Sites',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Sites',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',

                    filename: function() {
                        const today = new Date();
                        let day = String(today.getDate()).padStart(2, '0');
                        let month = String(today.getMonth() + 1).padStart(2, '0');
                        let year = today.getFullYear();
                        return 'Sites_' + day + month + year;
                    },

                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },

                    customize: function(doc) {

                        /* FONT SIZE */
                        doc.defaultStyle.fontSize = 7;

                        /* HEADER STYLE */
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 8,
                            color: 'black',
                            fillColor: '#a4d169',
                            alignment: 'center'
                        };

                        /* PAGE MARGIN */
                        doc.pageMargins = [10, 10, 10, 10];

                        /* AUTO WIDTH */
                        var table = doc.content[1].table;
                        var colCount = table.body[0].length;
                        table.widths = new Array(colCount).fill('*');

                        /* ROW COLORS */
                        doc.styles.tableBodyEven = {
                            fillColor: '#f2f2f2'
                        };
                        doc.styles.tableBodyOdd = {
                            fillColor: '#ffffff'
                        };

                        /* ===== BORDER FIX ===== */
                        doc.content[1].layout = {
                            hLineWidth: function() {
                                return 0.5;
                            },
                            vLineWidth: function() {
                                return 0.5;
                            },
                            hLineColor: function() {
                                return '#aaaaaa';
                            },
                            vLineColor: function() {
                                return '#aaaaaa';
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
                    }
                }

            ],
            responsive: true,
            scrollX: true,
            language: {
                emptyTable: "No sites found matching your search criteria."
            }
        });

        // Handle search by name or address (global search)
        $('#site-search').on('keyup', function() {
            table.search(this.value).draw(); // Trigger DataTable search
        });

        // ---- Customer Filter ----
        function applyCustomerFilter(selectedId) {
            $.fn.dataTable.ext.search = [];
            if (selectedId && selectedId !== '') {
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var row = table.row(dataIndex).node();
                    return $(row).data('customer-id') == selectedId;
                });
            }
            table.draw();
        }

        $('#customer-filter').on('change', function() {
            applyCustomerFilter(this.value);
        });

        var _preselect    = '<?= (int)($active_customer_id ?? 0) ?>';
        var _autoOpenCust = '<?= (int)($auto_open_modal_customer ?? 0) ?>';
        var _defaultModalCust = _autoOpenCust || _preselect;

        // 1. Apply table filter
        if (_preselect && _preselect !== '0') {
            document.getElementById('customer-filter').value = _preselect;
            applyCustomerFilter(_preselect);
        }

        // 2. Pre-select customer in modal immediately
        if (_defaultModalCust && _defaultModalCust !== '0') {
            $('#site-customer-id').val(_defaultModalCust);
        }

        // 3. Auto-open modal when redirected after creating new customer
        if (_autoOpenCust && _autoOpenCust !== '0') {
            setTimeout(function() {
                var modalEl = document.getElementById('siteModal');
                if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }, 350);
        }
    });



    $(document).ready(function() {

        // jQuery Validation for Add/Edit Site
        $('#siteForm').validate({
            rules: {
                'name': {
                    required: true,
                    maxlength: 255
                },
                'customer_id': {
                    required: true
                },
                'address': {
                    maxlength: 255
                },
                'city': {
                    maxlength: 255
                },
                'state': {
                    maxlength: 255
                },
                'zip': {
                    maxlength: 20
                },
                'contact_name': {
                    maxlength: 255
                },
                'email': {
                    email: true,
                    maxlength: 255
                },
                'phone': {
                    maxlength: 50
                }
            },
            messages: {
                'name': {
                    required: "Site name is required.",
                    maxlength: "Site name cannot exceed 255 characters."
                },
                'customer_id': {
                    required: "Please select a customer."
                },
                'email': {
                    email: "Please enter a valid email address.",
                    maxlength: "Email cannot exceed 255 characters."
                },
                'phone': {
                    maxlength: "Phone number cannot exceed 50 characters."
                }
            },
            submitHandler: function(form) {
                var actionUrl = $(form).attr('action');
                var formData = new FormData(form);

                // AJAX request to submit the form
                $.ajax({
                    type: 'POST',
                    url: actionUrl,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (typeof response === 'string') {
                            try {
                                response = JSON.parse(response);
                            } catch (e) {}
                        }
                        if (response && response.success) {
                            Swal.fire({
                                    title: 'Success!',
                                    text: 'Site saved successfully!',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                })
                                .then(function() {
                                    $('#siteModal').modal('hide');
                                    $('#siteForm')[0].reset();
                                    $('#siteModalLabel').text('Add Site');
                                    $('#submitBtn').text('Save');
                                    if (response.redirect_url) {
                                        window.location.href = response.redirect_url;
                                    } else { location.reload(); }
                                });
                        } else {
                            var msg = (response && response.message) ? response.message : 'Failed to save site. Please try again.';
                            // Show inline error in the modal
                            var errBox = document.getElementById('siteFormError');
                            if (errBox) {
                                errBox.textContent = msg;
                                errBox.classList.remove('d-none');
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: msg,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        var msg = 'An error occurred. Please try again.';
                        try {
                            var r = JSON.parse(xhr.responseText);
                            if (r && r.message) msg = r.message;
                        } catch (e) {}
                        Swal.fire({
                            title: 'Error!',
                            text: msg,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });

        // Edit Site button functionality
        $(document).on('click', '.edit-site-btn', function() {
            var siteId = $(this).data('id');

            // Populate modal fields with existing site data
            $('#site-id').val(siteId);
            $('#site-name').val($(this).data('name'));
            $('#site-customer-id').val($(this).data('customer_id'));
            $('#site-address').val($(this).data('address'));
            $('#site-city').val($(this).data('city'));
            $('#site-state').val($(this).data('state'));
            $('#site-zip').val($(this).data('zip'));
            $('#site-contact').val($(this).data('contact_name'));
            $('#site-email').val($(this).data('email'));
            $('#site-phone').val($(this).data('phone'));

            // Change modal title and button text for editing
            $('#siteModalLabel').text('Edit Site');
            $('#submitBtn').text('Update Site');
            $('#siteForm').attr('action', '<?= site_url('admin/sites/update/') ?>' + siteId);

            // Open the modal
            var modal = new bootstrap.Modal(document.getElementById('siteModal'));
            modal.show();
        });

        // Reset modal when closed
        $('#siteModal').on('hidden.bs.modal', function() {
            $('#siteForm')[0].reset();
            $('#site-id').val('');
            $('#siteModalLabel').text('Add Site');
            $('#submitBtn').text('Save');
            $('#siteForm').attr('action', '<?= site_url("admin/sites/add") ?>');
            var eb = document.getElementById('siteFormError');
            if (eb) { eb.textContent = ''; eb.classList.add('d-none'); }
            // Restore pre-selected customer after reset
            var custToRestore = (typeof _defaultModalCust !== 'undefined' && _defaultModalCust && _defaultModalCust !== '0')
                ? _defaultModalCust : document.getElementById('customer-filter').value;
            if (custToRestore) $('#site-customer-id').val(custToRestore);
        });
    });

    // Always pre-select the right customer when Add Site modal opens
    document.getElementById('siteModal').addEventListener('show.bs.modal', function() {
        if ($('#site-id').val()) return; // skip when editing
        var custToSet = (typeof _defaultModalCust !== 'undefined' && _defaultModalCust && _defaultModalCust !== '0')
            ? _defaultModalCust
            : document.getElementById('customer-filter').value;
        if (custToSet) $('#site-customer-id').val(custToSet);
    });

    function techDeleteSiteConfirm(e, btn) {
        e.preventDefault();
        var href = btn.getAttribute('href');
        Swal.fire({
            title: 'Delete Site?',
            text: 'This will permanently remove the site.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete'
        }).then(function(r) {
            if (r.isConfirmed) window.location.href = href;
        });
    }
</script>

<?= $this->endSection() ?>