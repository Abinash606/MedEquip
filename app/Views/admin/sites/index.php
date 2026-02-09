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


<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Site Directory</h3>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#siteModal">
        <i class="fa-solid fa-sitemap me-2"></i> Add Site
    </button>
</div>

<!-- Search Box -->
<div class="glass-card mb-4">
    <div class="input-group">
        <span class="input-group-text bg-white"><i class="fa-solid fa-search"></i></span>
        <input type="text" id="site-search" class="form-control border-start-0 ps-0" placeholder="Search for sites by name or address...">
    </div>
</div>

<!-- Filter by Customer -->
<div class="glass-card mb-4">
    <label for="customer-filter" class="form-label fw-bold">Filter by Customer</label>
    <select id="customer-filter" class="form-select" style="width: 25%;">
        <option value="">All Customers</option>
        <?php foreach ($customers as $cust): ?>
            <option value="<?= esc($cust['name']) ?>"><?= esc($cust['name']) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Sites Table -->
<div class="glass-card">
    <table class="table table-bordered table-striped table-hover" id="sitesTable">
        <thead class="table-light">
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
                            <!-- Edit button: populate modal with site data -->
                            <button type="button" class="btn btn-sm btn-outline-secondary edit-site-btn" title="Edit"
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
                            <a href="<?= site_url('admin/sites/delete/' . $site['id']) ?>" class="btn btn-sm btn-outline-danger" title="Delete"
                                onclick="return confirm('Are you sure you want to delete this site?')">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<!-- Site Modal -->
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php if (isset($_GET['customer_id']) && $_GET['customer_id']): ?>
    <script>
        const modal = new bootstrap.Modal(siteModal);
        modal.show();
        document.getElementById('site-customer-id').value = '<?= $_GET['customer_id'] ?>';
    </script>
<?php endif; ?>

<script>
    $(document).ready(function() {
        // Initialize DataTable with search and filter features
        var table = $('#sitesTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excelHtml5',
                    filename: 'sites',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csvHtml5',
                    filename: 'sites',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    filename: function() {
                        const today = new Date();

                        let day = String(today.getDate()).padStart(2, '0');
                        let month = String(today.getMonth() + 1).padStart(2, '0');
                        let year = today.getFullYear();

                        return 'Sites_' + day + month + year;
                    },
                    title: 'Sites',
                    exportOptions: {
                        columns: ':visible'
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

        // Handle customer filter
        $('#customer-filter').on('change', function() {
            var selectedCustomer = this.value;

            if (selectedCustomer === "") {
                table.column(1).search('').draw(); // Show all rows if "All Customers" is selected
            } else {
                table.column(1).search(selectedCustomer).draw(); // Filter by customer name
            }
        });
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
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Site saved successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            // Close modal and reset form
                            $('#siteModal').modal('hide');
                            $('#siteForm')[0].reset(); // Clear form data
                            $('#siteModalLabel').text('Add Site');
                            $('#submitBtn').text('Save');

                            // Force page reload after success
                            location.reload(); // This reloads the page and shows the latest data
                        });
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred. Please try again.',
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
            $('#siteForm').attr('action', '/admin/sites/add');
        });
    });
</script>

<?= $this->endSection() ?>