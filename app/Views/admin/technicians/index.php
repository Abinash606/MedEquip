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
        <tbody></tbody>
    </table>
</div>

<script>
    let technicianTable;
    let technicianModal;
    let techniciansData = [];

    // ─── Phone pattern: digits, spaces, dashes, dots, parentheses ──────────
    const PHONE_REGEX = /^[\d\s\-\.\(\)\+]+$/;

    // ─── Cache for us_states rows (fetched once on page load) ──────────────
    let statesData = [];

    // ─── Fetch states from the database ─────────────────────────────────────
    function fetchStates() {
        $.ajax({
            url: '<?= base_url('admin/states') ?>',
            type: 'GET',
            success: function(response) {
                statesData = response.data || [];
                renderStates();
            },
            error: function(xhr) {
                console.error('States fetch error:', xhr);
                Swal.fire('Error', 'Failed to load states list', 'error');
            }
        });
    }

    // ─── Build checkboxes from statesData and inject into the grid ─────────
    function renderStates() {
        const row = document.getElementById('states-checkbox-row');
        row.innerHTML = ''; // clear any previous render

        statesData.forEach(function(state) {
            const code = state.code; // e.g. "CA"
            const name = state.name; // e.g. "California"
            const idAttr = 'state-' + code.toLowerCase();

            row.insertAdjacentHTML('beforeend',
                '<div class="col">' +
                '<div class="form-check">' +
                '<input class="form-check-input state-checkbox" type="checkbox" value="' + code + '" id="' +
                idAttr + '" onchange="validateStates()">' +
                '<label class="form-check-label small" for="' + idAttr + '">' + name + '</label>' +
                '</div>' +
                '</div>'
            );
        });
    }

    // ─── Real-time validation for text / email / password fields ───────────
    function validateTechField(input) {
        input.classList.remove('is-valid', 'is-invalid');
        const feedback = input.closest('.col-md-6, .col-12')
            ?.querySelector('.invalid-feedback');

        if (!input.checkValidity()) {
            input.classList.add('is-invalid');
            // Write a contextual message into the feedback div
            if (feedback) {
                if (input.validity.valueMissing) {
                    feedback.textContent = input.labels?.[0]?.textContent?.replace('*', '').trim() +
                        ' is required.';
                } else if (input.validity.typeMismatch) {
                    feedback.textContent = 'Please enter a valid email address.';
                }
            }
        } else {
            input.classList.add('is-valid');
            if (feedback) feedback.textContent = '';
        }
    }

    // ─── Real-time validation for the optional phone field ────────────────
    function validateTechPhone(input) {
        input.classList.remove('is-valid', 'is-invalid');
        const feedback = document.getElementById('error-phone');

        let value = input.value.trim();

        // Empty is allowed (optional field)
        if (value === '') {
            feedback.textContent = '';
            return true;
        }

        // Allow only digits, spaces, dashes, dots, parentheses, +
        if (!PHONE_REGEX.test(value)) {
            input.classList.add('is-invalid');
            feedback.textContent = 'Please enter a valid phone number.';
            return false;
        }

        // Count only digits
        let digitsOnly = value.replace(/\D/g, '');

        if (digitsOnly.length < 10) {
            input.classList.add('is-invalid');
            feedback.textContent = 'Phone number must contain at least 10 digits.';
            return false;
        }

        // ✅ Valid
        input.classList.add('is-valid');
        feedback.textContent = '';
        return true;
    }


    // ─── Validate that at least one state checkbox is checked ─────────────
    function validateStates() {
        const checked = document.querySelectorAll('.state-checkbox:checked');
        const feedback = document.getElementById('error-states');
        const grid = document.getElementById('states-grid-wrapper');

        if (checked.length === 0) {
            feedback.style.visibility = 'visible';
            grid.style.borderColor = '#dc3545'; // red
            return false;
        }

        feedback.style.visibility = 'hidden';
        grid.style.borderColor = '#198754'; // green
        return true;
    }

    // ─── Full-form check — runs every field, returns true only if all pass ─
    function validateTechForm() {
        let valid = true;

        // 1. Every required input
        document.querySelectorAll('#technicianForm input[required]').forEach(function(input) {
            validateTechField(input);
            if (!input.checkValidity()) valid = false;
        });

        // 2. Phone (optional but must match pattern if filled)
        if (!validateTechPhone(document.getElementById('technician-phone'))) {
            valid = false;
        }

        // 3. At least one state must be selected
        if (!validateStates()) {
            valid = false;
        }

        // Scroll first bad field into view
        if (!valid) {
            const firstBadInput = document.querySelector('#technicianForm .is-invalid');
            // If no invalid input found, the error is likely the states grid
            const target = firstBadInput || document.getElementById('states-grid-wrapper');
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstBadInput?.focus?.();
            }
        }

        return valid;
    }

    // ─────────────────────────────────────────────────────────────────────────
    $(document).ready(function() {
        technicianModal = new bootstrap.Modal(document.getElementById('technicianModal'));
        fetchTechniciansData();
        fetchStates(); // ← populate the states grid from DB

        $('#search-username').on('keyup', function() {
            if (technicianTable) {
                technicianTable.column(2).search(this.value).draw();
            }
        });
    });

    // ─── Fetch technicians data ─────────────────────────────────────────────
    function fetchTechniciansData() {
        $.ajax({
            url: '<?= base_url('admin/technicians/data') ?>',
            type: 'GET',
            success: function(response) {
                techniciansData = response.data || [];
                initializeDataTable();
            },
            error: function(xhr, error) {
                console.error('Data fetch error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Load Data',
                    html: `Status: ${xhr.status}<br>Error: ${error}`,
                    footer: 'Check browser console for details'
                });
                techniciansData = [];
                initializeDataTable();
            }
        });
    }

    // ─── Initialize DataTable ───────────────────────────────────────────────
    function initializeDataTable() {
        try {
            technicianTable = $('#technicians-datatable').DataTable({
                data: techniciansData,
                columns: [{
                        data: null,
                        render: function(data, type, row) {
                            return row.full_name.split(' ')[0] || '';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return row.full_name.split(' ').slice(1).join(' ') || '';
                        }
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: null,
                        render: function() {
                            return '••••••••';
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
                                        onclick="editTechnician(${data})">Edit</button>
                                <button class="btn btn-sm btn-outline-danger btn-delete-technician"
                                        onclick="deleteTechnician(${data})">Delete</button>
                                <button class="btn btn-sm btn-outline-info ms-1 btn-login-technician"
                                        onclick="loginAsTechnician('${row.username}')">Login as Technician</button>
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
                        extend: 'csvHtml5',
                        filename: 'Technicians',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        filename: 'Technicians',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        filename: function() {
                            const today = new Date();

                            let day = String(today.getDate()).padStart(2, '0');
                            let month = String(today.getMonth() + 1).padStart(2, '0');
                            let year = today.getFullYear();

                            return 'Technicians_' + day + month + year;
                        },
                        title: 'Technicians',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = ['15%', '15%', '15%', '15%', '20%', '20%'];
                        }
                    }

                ]
            });
        } catch (error) {
            console.error('DataTable initialization error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Initialization Error',
                text: error.message
            });
        }
    }

    // ─── Reload data into the existing table ────────────────────────────────
    function reloadTechniciansData() {
        $.ajax({
            url: '<?= base_url('admin/technicians/data') ?>',
            type: 'GET',
            success: function(response) {
                techniciansData = response.data || [];
                technicianTable.clear();
                technicianTable.rows.add(techniciansData);
                technicianTable.draw(false);
            },
            error: function(xhr) {
                console.error('Reload error:', xhr);
                Swal.fire('Error', 'Failed to reload data', 'error');
            }
        });
    }

    // ─── Edit technician ────────────────────────────────────────────────────
    function editTechnician(id) {
        $.ajax({
            url: `<?= base_url('admin/technicians') ?>/${id}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const nameParts = data.full_name.split(' ');

                    document.getElementById('technician-id').value = data.id;
                    document.getElementById('technician-firstname').value = nameParts[0] || '';
                    document.getElementById('technician-lastname').value = nameParts.slice(1).join(' ') || '';
                    document.getElementById('technician-username').value = data.username;
                    document.getElementById('technician-phone').value = data.phone || '';
                    document.getElementById('technician-email').value = data.email || '';
                    document.getElementById('technician-password').value = '';

                    // UI: switch to edit mode
                    document.getElementById('modalTitle').textContent = 'Edit Technician';
                    document.getElementById('password-required').style.display = 'none';
                    document.getElementById('password-hint').style.display = 'block';
                    document.getElementById('technician-password').required = false;

                    // Mark every pre-filled required input green (username, email)
                    document.querySelectorAll('#technicianForm input[required]').forEach(function(input) {
                        input.classList.remove('is-invalid');
                        if (input.value.trim() !== '') {
                            input.classList.add('is-valid');
                        }
                    });

                    // Phone — mark green only if non-empty
                    const phoneInput = document.getElementById('technician-phone');
                    if (phoneInput.value.trim() !== '') {
                        phoneInput.classList.remove('is-invalid');
                        phoneInput.classList.add('is-valid');
                    }

                    // States: uncheck all first, then tick the ones returned by the server
                    document.querySelectorAll('.state-checkbox').forEach(function(cb) {
                        cb.checked = false;
                    });
                    if (data.states && data.states.length > 0) {
                        data.states.forEach(function(state) {
                            const cb = document.getElementById('state-' + state.toLowerCase());
                            if (cb) cb.checked = true;
                        });
                    }

                    // Run states validation so the grid border turns green if states exist
                    validateStates();

                    technicianModal.show();
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to load technician data', 'error');
            }
        });
    }

    // ─── Save / Update technician ───────────────────────────────────────────
    function saveTechnician() {
        // ── STOP HERE if anything is invalid ──────────────────────────────
        if (!validateTechForm()) return;

        const technicianId = document.getElementById('technician-id').value;
        const isEdit = technicianId !== '';

        const selectedStates = [];
        document.querySelectorAll('.state-checkbox:checked').forEach(function(cb) {
            selectedStates.push(cb.value);
        });

        const formData = {
            firstname: document.getElementById('technician-firstname').value.trim(),
            lastname: document.getElementById('technician-lastname').value.trim(),
            username: document.getElementById('technician-username').value.trim(),
            password: document.getElementById('technician-password').value,
            phone: document.getElementById('technician-phone').value.trim(),
            email: document.getElementById('technician-email').value.trim(),
            states: selectedStates
        };

        // Show spinner
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
                    reloadTechniciansData();
                    Swal.fire('Success', response.message, 'success');
                }
            },
            error: function(xhr) {
                document.getElementById('saveSpinner').classList.add('d-none');
                const response = xhr.responseJSON;

                if (response && response.errors) {
                    // Server-side errors → paint them onto the matching fields
                    Object.keys(response.errors).forEach(function(field) {
                        const errorEl = document.getElementById('error-' + field);
                        const inputEl = document.getElementById('technician-' + field);

                        if (field === 'states') {
                            // States error is handled separately (no input element)
                            if (errorEl) {
                                errorEl.textContent = response.errors[field];
                                errorEl.style.visibility = 'visible';
                                document.getElementById('states-grid-wrapper').style.borderColor =
                                    '#dc3545';
                            }
                        } else if (errorEl && inputEl) {
                            errorEl.textContent = response.errors[field];
                            inputEl.classList.remove('is-valid');
                            inputEl.classList.add('is-invalid');
                        }
                    });
                } else {
                    Swal.fire('Error', response?.message || 'An error occurred', 'error');
                }
            }
        });
    }

    // ─── Delete technician ──────────────────────────────────────────────────
    function deleteTechnician(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('admin/technicians/delete') ?>/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            reloadTechniciansData();
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

    // ─── Login as technician (placeholder) ──────────────────────────────────
    function loginAsTechnician(username) {
        // Open technician portal in new window
    }

    // ─── Reset modal to "Add" state ─────────────────────────────────────────
    function resetTechnicianModal() {
        document.getElementById('technician-id').value = '';
        document.getElementById('technician-firstname').value = '';
        document.getElementById('technician-lastname').value = '';
        document.getElementById('technician-username').value = '';
        document.getElementById('technician-password').value = '';
        document.getElementById('technician-phone').value = '';
        document.getElementById('technician-email').value = '';

        document.querySelectorAll('.state-checkbox').forEach(function(cb) {
            cb.checked = false;
        });

        document.getElementById('modalTitle').textContent = 'Add Technician';
        document.getElementById('password-required').style.display = 'block';
        document.getElementById('password-hint').style.display = 'none';
        document.getElementById('technician-password').required = true;

        clearValidationErrors();
    }

    // ─── Strip every validation class and message ──────────────────────────
    function clearValidationErrors() {
        // Inputs: remove both green and red
        document.querySelectorAll('#technicianForm input').forEach(function(el) {
            el.classList.remove('is-valid', 'is-invalid');
        });
        // Feedback text
        document.querySelectorAll('#technicianForm .invalid-feedback').forEach(function(el) {
            el.textContent = '';
        });
        document.getElementById('error-states').style.visibility = 'hidden';
        document.getElementById('states-grid-wrapper').style.borderColor = '#dee2e6';
    }
</script>

<?= $this->endSection() ?>