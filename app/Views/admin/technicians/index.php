<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="glass-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Technicians</h3>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#technicianModal"
            onclick="resetTechnicianModal()">
            <i class="fa-solid fa-user-plus me-2"></i> Add Technician
        </button>

    </div>

    <div class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                    <input type="text" id="search-username" class="form-control" placeholder="Search by username...">
                </div>
            </div>
        </div>
    </div>

    <table id="technicians-datatable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Username</th>
                <th>Password</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script>
    let technicianTable;
    let technicianModal;
    let techniciansData = []; // Store data locally

    $(document).ready(function() {
        technicianModal = new bootstrap.Modal(document.getElementById('technicianModal'));

        // Fetch data immediately when page loads
        fetchTechniciansData();

        // Search functionality
        $('#search-username').on('keyup', function() {
            if (technicianTable) {
                technicianTable.column(2).search(this.value).draw();
            }
        });
    });

    // Fetch technicians data
    function fetchTechniciansData() {
        $.ajax({
            url: '<?= base_url('admin/technicians/data') ?>',
            type: 'GET',
            success: function(response) {
                techniciansData = response.data || [];
                initializeDataTable();
            },
            error: function(xhr, error, thrown) {
                console.error('Data fetch error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Load Data',
                    html: `Status: ${xhr.status}<br>Error: ${error}`,
                    footer: 'Check browser console for details'
                });
                // Initialize empty table even on error
                techniciansData = [];
                initializeDataTable();
            }
        });
    }

    // Initialize DataTable with fetched data
    function initializeDataTable() {
        try {
            technicianTable = $('#technicians-datatable').DataTable({
                data: techniciansData, // Use local data instead of ajax
                columns: [{
                        data: null,
                        render: function(data, type, row) {
                            const nameParts = row.full_name.split(' ');
                            return nameParts[0] || '';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            const nameParts = row.full_name.split(' ');
                            return nameParts.slice(1).join(' ') || '';
                        }
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '********';
                        },
                        orderable: false
                    },
                    {
                        data: 'phone',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'email',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return `
                            <button class="btn btn-sm btn-outline-secondary btn-edit-technician" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#technicianModal"
                                    onclick="editTechnician(${data})">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete-technician" 
                                    onclick="deleteTechnician(${data})">
                                Delete
                            </button>
                            <button class="btn btn-sm btn-outline-info ms-1 btn-login-technician" 
                                    onclick="loginAsTechnician('${row.username}')">
                                Login as Technician
                            </button>
                        `;
                        },
                        orderable: false
                    }
                ],
                responsive: true,
                order: [
                    [0, 'asc']
                ],
                dom: '<"row mb-3"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = ['15%', '15%', '15%', '15%', '20%', '20%'];
                        }
                    },
                ]
            });

            console.log('DataTable initialized with', techniciansData.length, 'technicians');

        } catch (error) {
            console.error('DataTable initialization error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Initialization Error',
                text: error.message
            });
        }
    }

    // Reload data function
    function reloadTechniciansData() {
        $.ajax({
            url: '<?= base_url('admin/technicians/data') ?>',
            type: 'GET',
            success: function(response) {
                techniciansData = response.data || [];
                technicianTable.clear();
                technicianTable.rows.add(techniciansData);
                technicianTable.draw(false); // false maintains current page
            },
            error: function(xhr) {
                console.error('Reload error:', xhr);
                Swal.fire('Error', 'Failed to reload data', 'error');
            }
        });
    }

    function editTechnician(id) {
        $.ajax({
            url: `<?= base_url('admin/technicians') ?>/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;

                    const nameParts = data.full_name.split(' ');
                    const firstname = nameParts[0];
                    const lastname = nameParts.slice(1).join(' ');

                    document.getElementById('technician-id').value = data.id;
                    document.getElementById('technician-firstname').value = firstname;
                    document.getElementById('technician-lastname').value = lastname;
                    document.getElementById('technician-username').value = data.username;
                    document.getElementById('technician-phone').value = data.phone || '';
                    document.getElementById('technician-email').value = data.email || '';
                    document.getElementById('technician-password').value = '';

                    document.getElementById('modalTitle').textContent = 'Edit Technician';
                    document.getElementById('password-required').style.display = 'none';
                    document.getElementById('password-hint').style.display = 'block';
                    document.getElementById('technician-password').required = false;

                    // Clear validation errors
                    clearValidationErrors();

                    // Set states - multiple checkboxes can be checked
                    document.querySelectorAll('.state-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    if (data.states && data.states.length > 0) {
                        data.states.forEach(state => {
                            const checkbox = document.getElementById(`state-${state.toLowerCase()}`);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });
                    }

                    technicianModal.show();
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to load technician data', 'error');
            }
        });
    }

    function saveTechnician() {
        const technicianId = document.getElementById('technician-id').value;
        const isEdit = technicianId !== '';

        clearValidationErrors();

        const selectedStates = [];
        document.querySelectorAll('.state-checkbox:checked').forEach(checkbox => {
            selectedStates.push(checkbox.value);
        });

        if (selectedStates.length === 0) {
            document.getElementById('error-states').textContent = 'Please select at least one state';
            document.querySelector('.states-grid').classList.add('is-invalid');
            return;
        }

        const formData = {
            firstname: document.getElementById('technician-firstname').value.trim(),
            lastname: document.getElementById('technician-lastname').value.trim(),
            username: document.getElementById('technician-username').value.trim(),
            password: document.getElementById('technician-password').value,
            phone: document.getElementById('technician-phone').value.trim(),
            email: document.getElementById('technician-email').value.trim(),
            states: selectedStates
        };

        // Show loading spinner
        document.getElementById('saveSpinner').classList.remove('d-none');

        const url = isEdit ?
            `<?= base_url('admin/technicians/update') ?>/${technicianId}` :
            '<?= base_url('admin/technicians/store') ?>';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                document.getElementById('saveSpinner').classList.add('d-none');

                if (response.success) {
                    technicianModal.hide();
                    reloadTechniciansData(); // Use custom reload function
                    Swal.fire('Success', response.message, 'success');
                }
            },
            error: function(xhr) {
                document.getElementById('saveSpinner').classList.add('d-none');

                const response = xhr.responseJSON;

                if (response && response.errors) {
                    // Display validation errors
                    Object.keys(response.errors).forEach(field => {
                        const errorElement = document.getElementById(`error-${field}`);
                        const inputElement = document.getElementById(`technician-${field}`);

                        if (errorElement && inputElement) {
                            errorElement.textContent = response.errors[field];
                            inputElement.classList.add('is-invalid');
                        }
                    });
                } else {
                    Swal.fire('Error', response?.message || 'An error occurred', 'error');
                }
            }
        });
    }

    function deleteTechnician(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('admin/technicians/delete') ?>/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            reloadTechniciansData(); // Use custom reload function
                            Swal.fire('Deleted!', response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire('Error', response?.message || 'Failed to delete technician', 'error');
                    }
                });
            }
        });
    }

    function loginAsTechnician(username) {
        // Open technician portal in new window
    }

    function resetTechnicianModal() {
        // Clear hidden ID
        document.getElementById('technician-id').value = '';

        // Clear text inputs
        document.getElementById('technician-firstname').value = '';
        document.getElementById('technician-lastname').value = '';
        document.getElementById('technician-username').value = '';
        document.getElementById('technician-password').value = '';
        document.getElementById('technician-phone').value = '';
        document.getElementById('technician-email').value = '';

        // Reset states
        document.querySelectorAll('.state-checkbox').forEach(cb => cb.checked = false);

        // Reset modal title & password rules
        document.getElementById('modalTitle').textContent = 'Add Technician';
        document.getElementById('password-required').style.display = 'block';
        document.getElementById('password-hint').style.display = 'none';
        document.getElementById('technician-password').required = true;

        // Clear validation errors
        clearValidationErrors();
    }

    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(element => {
            element.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(element => {
            element.textContent = '';
        });
    }
</script>

<?= $this->endSection() ?>