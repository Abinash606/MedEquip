<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Customer Directory</h3>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#customerModal"><i class="fa-solid fa-building me-2"></i> Add Customer</button>
</div>

<div class="glass-card mb-4">
    <div class="input-group">
        <span class="input-group-text bg-white"><i class="fa-solid fa-search"></i></span>
        <input type="text" class="form-control border-start-0 ps-0" id="customer-search" placeholder="Search for customers by name or address...">
        <button class="btn btn-outline-primary" id="searchBtn">Search</button>
    </div>
</div>

<div class="glass-card">
    <table id="customer-datatable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Address</th>
                <th>Billing City</th>
                <th>State</th>
                <th>Zip</th>
                <th>Contact Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Fax</th>
                <th>Website</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
            <tr>
			
                <td>
					<?php if (!empty($customer['first_site_id'])): ?>
						<a href="<?= site_url('admin/sites/' . $customer['first_site_id']) ?>" class="text-primary fw-bold">
							<?= esc($customer['name']) ?>
						</a>
					<?php else: ?>
						<?= esc($customer['name']) ?>
					<?php endif; ?>
				</td>

                <td><?= $customer['billing_address'] ?></td>
                <td><?= $customer['billing_city'] ?></td>
                <td><?= $customer['billing_state'] ?></td>
                <td><?= $customer['billing_zip'] ?></td>
                <td><?= $customer['contact_name'] ?></td>
                <td><?= $customer['email'] ?></td>
                <td><?= $customer['phone'] ?></td>
                <td><?= $customer['fax'] ?></td>
                <td><?= $customer['website'] ?></td>
                <td>
                    <button data-id="<?= $customer['id'] ?>" class="btn btn-sm btn-outline-secondary btn-edit-customer">Edit</button>
                    <button data-id="<?= $customer['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-customer">Delete</button>
                    <a href="<?= site_url('admin/sites/add?customer_id=' . $customer['id']) ?>" class="btn btn-sm btn-outline-info btn-add-site">Add Sites</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="customerForm" action="" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="customer-id" value="">

                    <!-- Customer Details -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="customer-name">Customer Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer-name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="customer-contact">Contact Name</label>
                            <input type="text" class="form-control" id="customer-contact" name="contact_name">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="customer-billing-address">Customer Address</label>
                            <input type="text" class="form-control" id="customer-billing-address" name="billing_address">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="customer-city">Billing City</label>
                            <input type="text" class="form-control" id="customer-city" name="billing_city">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="customer-state">State</label>
                            <!-- <input type="text" class="form-control" id="customer-state" name="billing_state"> -->
                            <select class="form-select" id="customer-state" name="billing_state">
    <option value="">Select State</option>
    <?php if (!empty($states)): ?>
        <?php foreach ($states as $st): ?>
            <option value="<?= esc($st['code']) ?>">
                <?= esc($st['name']) ?> (<?= esc($st['code']) ?>)
            </option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>



                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="customer-zip">Zip</label>
                            <input type="text" class="form-control" id="customer-zip" name="billing_zip">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="customer-fax">Fax</label>
                            <input type="text" class="form-control" id="customer-fax" name="fax">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="customer-email">Email</label>
                            <input type="email" class="form-control" id="customer-email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="customer-phone">Phone Number</label>
                            <input type="text" class="form-control" id="customer-phone" name="phone">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="customer-website">Website</label>
                            <input type="url" class="form-control" id="customer-website" name="website">
                        </div>
                    </div>

                    <!-- Customer Logo Field -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="customer-logo">Customer Logo</label>
                            <input type="file" class="form-control" id="customer-logo" name="logo" accept="image/*">
                            <div id="logo-preview" class="mt-2"></div>
                        </div>
                    </div>

                    <!-- Credentials Section -->
                    <hr>
                    <h5 class="mb-3">Credentials</h5>
                     <div id="credentials-container">
                        <div class="row g-3 credential-set">
                            <div class="col-md-4">
                                <label for="admin-username-1" class="form-label">Admin username 1</label>
                                <input type="text" class="form-control" id="admin-username-1" name="portal_username[]">
                            </div>
							<div class="col-md-4">
                                <label for="admin-email-1" class="form-label">Admin email 1</label>
                                <input type="text" class="form-control" id="admin-email-1" name="portal_email[]">
                            </div>
                            <div class="col-md-4">
                                <label for="admin-password-1" class="form-label">Admin password 1</label>
                                <input type="password" class="form-control" id="admin-password-1" name="portal_password[]">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link ps-0" id="add-credential-btn">Add More</button>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitBtn" onclick="validateForm()">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>



<script>

$(document).ready(function() {
	var table = $('#customer-datatable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        responsive: true, // Makes the table responsive
        scrollX: true, // Enables horizontal scroll
		language: {
            emptyTable: "No customers found matching your search criteria." // Custom message when no data is found
        }
    });
	 // On page load, populate the table with all customers
    // Load all customers on page load
    function loadTable(customers) { 
    // Clear existing rows in the table
    table.clear();

    // Add customers to the table
    customers.forEach(function(customer) {
        var editUrl = "<?= site_url('admin/sites/add?customer_id='); ?>" + customer.id; // Correct way to get the URL
        var row = [
            customer.name,
            customer.billing_address,
            customer.billing_city,
            customer.billing_state,
            customer.billing_zip,
            customer.contact_name,
            customer.email,
            customer.phone,
            customer.fax,
            customer.website,
            `<button data-id="${customer.id}" class="btn btn-sm btn-outline-secondary btn-edit-customer" >Edit</button>
             <button data-id="${customer.id}" class="btn btn-sm btn-outline-danger btn-delete-customer">Delete</button>
             <a href="${editUrl}" class="btn btn-sm btn-outline-info btn-add-site">Add Sites</a>`
        ];

        table.row.add(row).draw();
    });
}


        // Search button functionality
    $('#searchBtn').click(function() {
        var searchTerm = $('#customer-search').val().trim(); // Get the search term

        // Make AJAX request to search customers
        $.ajax({
            url: '<?php echo base_url(); ?>admin/customers/search',  // Controller method to handle search
            method: 'GET',
            data: { search: searchTerm },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    loadTable(response.customers);  // Update the table with search results
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred while searching for customers.', 'error');
            }
        });
    });
	let credentialCount = 1;
        $('#add-credential-btn').on('click', function() {
            credentialCount++;
            const newCredentialSet = `
                <div class="row g-3 credential-set mt-3">
                    <div class="col-md-4">
                        <label for="admin-username-${credentialCount}" class="form-label">Admin username ${credentialCount}</label>
                        <input type="text" class="form-control" id="admin-username-${credentialCount}" name="portal_username[]">
                    </div>
					<div class="col-md-4">
                                <label for="admin-email-${credentialCount}" class="form-label">Admin email ${credentialCount}</label>
                                <input type="text" class="form-control" id="admin-email-${credentialCount}" name="portal_email[]">
                    </div>
                    <div class="col-md-4">
                        <label for="admin-password-${credentialCount}" class="form-label">Admin password ${credentialCount}</label>
                        <input type="password" class="form-control" id="admin-password-${credentialCount}" name="portal_password[]">
                    </div>
                </div>`;
            $('#credentials-container').append(newCredentialSet);
        });
    
});

$(document).ready(function () {
	$.validator.addMethod("extension", function(value, element, param) {
        // Get file extension from input value
        var fileExtension = value.split('.').pop().toLowerCase();
        // Check if the extension is in the allowed list
        return this.optional(element) || param.split('|').indexOf(fileExtension) !== -1;
    }, "Please upload a valid file format (jpg, jpeg, png, gif).");
	    // Custom filesize validation function
    $.validator.addMethod("filesize", function(value, element, param) {
        var file = element.files[0];
        if (file) {
            var fileSize = file.size / 1024; // File size in KB
            return fileSize <= param; // Check if file size is less than or equal to max size
        }
        return true; // Allow empty file input (optional)
    }, "File size must be less than {0}KB.");
    // jQuery Validation
    $('#customerForm').validate({
        rules: {
            'name': {
                required: true,
                maxlength: 255
            },
            'contact_name': {
                maxlength: 255
            },
            'email': {
                required: true,
                email: true,
                maxlength: 255,
                remote: {
                    url: "<?php echo base_url(); ?>admin/customers/check-email", // URL to check email uniqueness
                    type: "post",
                    data: {
                        email: function () {
                            return $("#customer-email").val();
                        },
                    id: function() {
                        return $("#customer-id").val();  // Send the customer ID along with the email
                    }
                    },
                    dataType: "json",
                    dataFilter: function (data) {
                        var json = JSON.parse(data);
                        return json.unique === "true"; // Based on your backend logic (respond with {unique: "true/false"})
                    }
                }
            },
            'phone': {
                maxlength: 50
            },
            'billing_address': {
                maxlength: 255
            },
            'billing_city': {
                maxlength: 255
            },
            'billing_state': {
                maxlength: 255
            },
            'billing_zip': {
                maxlength: 20
            },
            'fax': {
                maxlength: 50
            },
            'website': {
                maxlength: 255
            },
            'logo': {
                extension: "jpg|jpeg|png|gif",
                filesize: 2048 // 2MB max size
            }
        },
        messages: {
            'name': {
                required: "Customer name is required.",
                maxlength: "Customer name cannot exceed 255 characters."
            },
            'email': {
                required: "Email is required.",
                email: "Please enter a valid email address.",
                maxlength: "Email cannot exceed 255 characters.",
                remote: "This email is already taken." // Error message when email is not unique
            },
            'phone': {
                maxlength: "Phone number cannot exceed 50 characters."
            },
            'billing_address': {
                maxlength: "Billing address cannot exceed 255 characters."
            },
            'billing_city': {
                maxlength: "Billing city cannot exceed 255 characters."
            },
            'billing_state': {
                maxlength: "Billing state cannot exceed 255 characters."
            },
            'billing_zip': {
                maxlength: "Billing zip cannot exceed 20 characters."
            },
            'fax': {
                maxlength: "Fax cannot exceed 50 characters."
            },
            'website': {
                maxlength: "Website cannot exceed 255 characters."
            },
            'logo': {
                extension: "Only image files (jpg, jpeg, png, gif) are allowed.",
                filesize: "File size should not exceed 2MB."
            }
        },
        submitHandler: function (form, event) {
            event.preventDefault(); // Prevent default form submission until validation is complete
            var formData = new FormData(form);
            var actionUrl = ($('#customer-id').val()) ? '<?php echo base_url(); ?>admin/customers/update/' + $('#customer-id').val() : '<?php echo base_url(); ?>admin/customers/add';
        
            $.ajax({
                type: 'POST',
                 url: actionUrl,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Customer saved successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    $('#customerModal').modal('hide');
                    location.reload();
                },
                error: function () {
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
});

function validateForm() {
    if ($("#customerForm").valid()) {
        $('#customerForm').submit();
    } else {
        console.log('Form is invalid');
    }
}

$(document).ready(function() {
    var credentialCount = 1;

    // Reset modal when opened for adding new customer
    $('#customerModal').on('show.bs.modal', function (e) {
        // Check if we are opening the "Add Customer" modal
        if ($(e.relatedTarget).hasClass('btn-add-customer')) {
            // Reset form fields for Add action
            $('#customerForm')[0].reset();
            $('#customer-id').val(''); // Reset the hidden ID field
            $('#logo-preview').html(''); // Reset the logo preview

            // Clear any existing credentials
            $('#credentials-container').empty();
            credentialCount = 1; // Reset the credentials count
        }
    });

    // Edit button functionality
    $(".btn-edit-customer").click(function() {
        var customerId = $(this).data('id'); // Get customer ID from the button

        // Make AJAX call to fetch customer details
        $.ajax({
            url: '<?php echo base_url(); ?>admin/customers/edit/' + customerId,  // Controller action to fetch data
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var customer = response.data; // Customer data
                    var credentials = response.credentials;
                    var imageUrl = response.image_url; // Image URL

                    // Populate the modal fields with the data
                    $("#customer-id").val(customer.id);
                    $("#customer-name").val(customer.name);
                    $("#customer-contact").val(customer.contact_name);
                    $("#customer-billing-address").val(customer.billing_address);
                    $("#customer-city").val(customer.billing_city);
                    $("#customer-state").val(customer.billing_state);
                    $("#customer-zip").val(customer.billing_zip);
                    $("#customer-fax").val(customer.fax);
                    $("#customer-email").val(customer.email);
                    $("#customer-phone").val(customer.phone);
                    $("#customer-website").val(customer.website);

                    // Show the customer logo image if it exists
                    if (imageUrl) {
                        $("#logo-preview").html('<img src="' + imageUrl + '" alt="Logo" class="img-thumbnail" style="max-width: 100px;">');
                    } else {
                        $("#logo-preview").html('<p>No logo uploaded</p>');
                    }

                    // Clear existing credentials before adding new ones
                    $("#credentials-container").empty();

                    // Populate the credentials section dynamically
                    credentials.forEach(function(credential, index) {
                        var credentialHTML = `
                            <div class="row g-3 credential-set">
                                <div class="col-md-4">
                                    <label for="admin-username-${index + 1}" class="form-label">Admin username ${index + 1}</label>
                                    <input type="text" class="form-control" id="admin-username-${index + 1}" name="portal_username[]" value="${credential.username}">
                                </div>
                                <div class="col-md-4">
                                    <label for="admin-email-${index + 1}" class="form-label">Admin email ${index + 1}</label>
                                    <input type="text" class="form-control" id="admin-email-${index + 1}" name="portal_email[]" value="${credential.email}">
                                </div>
                                <div class="col-md-4">
                                    <label for="admin-password-${index + 1}" class="form-label">Admin password ${index + 1}</label>
                                    <input type="password" class="form-control" id="admin-password-${index + 1}" name="portal_password[]" value="${credential.password}">
                                </div>
                            </div>`;
                        $("#credentials-container").append(credentialHTML);
                    });

                    // Open the modal for editing
                    $('#customerModal').modal('show');
                } else {
                    Swal.fire('Error!', 'Customer data not found!', 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'An error occurred while fetching customer data.', 'error');
            }
        });
    });

    // Delete button functionality with SweetAlert confirmation
    $(".btn-delete-customer").click(function(e) {
        e.preventDefault();
        var customerId = $(this).data('id');

        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to delete URL
                window.location.href = '<?php echo base_url(); ?>admin/customers/delete/' + customerId;
            }
        });
    });
});

</script>

<?= $this->endSection() ?>
