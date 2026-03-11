<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <h3 class="fw-bold mb-0">Inventory Management</h3>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#inventoryModal">
        <i class="fa-solid fa-plus me-2"></i> Add Item
    </button>
</div>
    <div class="content">
<div class="glass-card p-3">
    <div class="table-responsive">
    <table id="inventory-datatable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Part #</th>
                <th>Image</th>
                <th>Part Description</th>
                <th>Bin</th>
                <th>QTY</th>
                <th>Total Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    </div>
</div>
</div>
<!-- Inventory Modal -->
<div class="modal fade" id="inventoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invModalLabel">Add Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="invForm" novalidate enctype="multipart/form-data">
                    <input type="hidden" id="invId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Part # <span class="text-danger">*</span></label>
                            <input type="text" id="invPartNumber" name="part_number" class="form-control" required
                                oninput="validateField(this)">
                            <div class="invalid-feedback">Part number is required.</div>
                        </div>

                        <!-- Part Description -->
                        <div class="col-md-6">
                            <label class="form-label">Part Description <span class="text-danger">*</span></label>
                            <input type="text" id="invDescription" name="part_description" class="form-control" required
                                oninput="validateField(this)">
                            <div class="invalid-feedback">Part description is required.</div>
                        </div>

                        <!-- Bin -->
                        <div class="col-md-4">
                            <label class="form-label">Bin <span class="text-danger">*</span></label>
                            <input type="text" id="invBin" name="bin" class="form-control" required
                                oninput="validateField(this)">
                            <div class="invalid-feedback">Bin location is required.</div>
                        </div>
                        <!-- Row / Aisle -->
                        <div class="col-md-4">
                            <label class="form-label">Row/Aisle <span class="text-danger">*</span></label>
                            <input type="text" id="invRowAisle" name="row_aisle" class="form-control" required
                                oninput="validateField(this)">
                            <div class="invalid-feedback">Row/Aisle is required.</div>
                        </div>

                        <!-- Shelf -->
                        <div class="col-md-4">
                            <label class="form-label">Shelf <span class="text-danger">*</span></label>
                            <input type="text" id="invShelf" name="shelf" class="form-control" required
                                oninput="validateField(this)">
                            <div class="invalid-feedback">Shelf is required.</div>
                        </div>

                        <!-- QTY -->
                        <div class="col-md-4">
                            <label class="form-label">QTY <span class="text-danger">*</span></label>
                            <input type="number" id="invQty" name="qty" class="form-control" min="0" required
                                oninput="validateField(this)">
                            <div class="invalid-feedback" id="invQtyFeedback">Quantity is required and must be 0 or
                                more.</div>
                        </div>

                        <!-- Total Value -->
                        <div class="col-md-4">
                            <label class="form-label">Total Value <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="invCost" name="total_value" class="form-control"
                                min="0" required oninput="validateField(this)">
                            <div class="invalid-feedback" id="invCostFeedback">Total value is required and must be 0 or
                                more.</div>
                        </div>

                        <!-- Image -->
                        <div class="col-12">
                            <label class="form-label">Image</label>
                            <input type="file" id="invImage" name="image" class="form-control" accept="image/*"
                                onchange="validateImageField(this)">
                            <div class="invalid-feedback" id="invImageFeedback">Please select a valid image file (JPG,
                                PNG, GIF, WEBP).</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveInvBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    let inventoryTable;
    let inventoryModal;
    let inventoryData = [];

    // ─── Allowed MIME types for the image field ────────────────────────────────
    const ALLOWED_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml'
    ];

    // ─── Real-time validation for text / number fields ─────────────────────────
    function validateField(input) {
        // Remove any previous state so Bootstrap shows the right one
        input.classList.remove('is-valid', 'is-invalid');

        if (!input.checkValidity()) {
            input.classList.add('is-invalid');

            // Custom messages for min violations on number fields
            if (input.type === 'number' && input.validity.rangeUnderflow) {
                const feedbackEl = input.nextElementSibling;
                if (feedbackEl && feedbackEl.classList.contains('invalid-feedback')) {
                    feedbackEl.textContent = `Value must be ${input.min} or more.`;
                }
            }
        } else {
            input.classList.add('is-valid');
        }
    }

    // ─── Validation for the file input (not covered by checkValidity) ──────────
    function validateImageField(input) {
        input.classList.remove('is-valid', 'is-invalid');
        const feedback = document.getElementById('invImageFeedback');

        // Nothing selected is fine — image is optional
        if (input.files.length === 0) {
            input.classList.remove('is-valid'); // stay neutral
            return true;
        }

        const file = input.files[0];

        // 1. MIME-type check
        if (!ALLOWED_IMAGE_MIMES.includes(file.type)) {
            input.classList.add('is-invalid');
            feedback.textContent = 'Invalid file type. Please select an image (JPG, PNG, GIF, WEBP).';
            return false;
        }

        // 2. Optional size check — 5 MB max
        const MAX_SIZE_BYTES = 5 * 1024 * 1024;
        if (file.size > MAX_SIZE_BYTES) {
            input.classList.add('is-invalid');
            feedback.textContent = 'Image is too large. Maximum allowed size is 5 MB.';
            return false;
        }

        input.classList.add('is-valid');
        return true;
    }

    // ─── Full-form validation — returns true only when every field is valid ─────
    function validateForm() {
        const form = document.getElementById('invForm');
        const inputs = form.querySelectorAll('input[required]');
        let isFormValid = true;

        inputs.forEach(function(input) {
            validateField(input); // mark each field
            if (!input.checkValidity()) {
                isFormValid = false;
            }
        });

        // Also run the image check (it's optional but may have a bad file selected)
        const imageInput = document.getElementById('invImage');
        if (!validateImageField(imageInput)) {
            isFormValid = false;
        }

        // Scroll the first invalid field into view so the user sees it
        if (!isFormValid) {
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstInvalid.focus();
            }
        }

        return isFormValid;
    }

    $(document).ready(function() {
        inventoryModal = new bootstrap.Modal(document.getElementById('inventoryModal'));

        fetchInventoryData();

        // ── Save button: validate FIRST, then proceed ──────────────────────────
        $('#saveInvBtn').on('click', function() {
            if (validateForm()) {
                saveInventory();
            }
        });

        // ── Reset modal + validation states on close ────────────────────────────
        $('#inventoryModal').on('hidden.bs.modal', function() {
            $('#invForm')[0].reset();
            $('#invId').val('');
            $('#invModalLabel').text('Add Inventory Item');

            // Strip every validation class so the modal opens clean next time
            $('#invForm input').removeClass('is-valid is-invalid');
        });
    });

    // ─── Fetch inventory data ───────────────────────────────────────────────────
    function fetchInventoryData() {
        const dataUrl = '<?= base_url('admin/inventory/data') ?>';

        $.ajax({
            url: dataUrl,
            type: 'GET',
            success: function(response) {
                inventoryData = response.data || [];
                initializeDataTable();
            },
            error: function(xhr, error, thrown) {
                console.error('Data fetch error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Load Data',
                    html: `Status: ${xhr.status}<br>Error: ${error}<br>Message: ${thrown}`,
                    footer: 'Check browser console for details'
                });
                inventoryData = [];
                initializeDataTable();
            }
        });
    }

    // ─── Initialize DataTable ───────────────────────────────────────────────────
    function initializeDataTable() {
        try {
            inventoryTable = $('#inventory-datatable').DataTable({
                data: inventoryData,
                columns: [{
                        data: 'part_number',
                        defaultContent: '-'
                    },
                    {
                        data: 'image',
                        render: function(data, type) {
                            if (type === 'display') {
                                return data ?
                                    `<img src="${data}" alt="Part Image" style="width:50px;height:50px;object-fit:cover;" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'50\' height=\'50\'%3E%3Crect fill=\'%23ddd\' width=\'50\' height=\'50\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23999\'%3ENo Image%3C/text%3E%3C/svg%3E\';">` :
                                    '<span class="text-muted">No Image</span>';
                            }
                            return data || '';
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'part_description',
                        defaultContent: '-'
                    },
                    {
                        data: 'bin',
                        defaultContent: '-'
                    },
                    {
                        data: 'qty',
                        defaultContent: '0',
                        render: function(data) {
                            return parseInt(data) || 0;
                        }
                    },
                    {
                        data: 'total_value',
                        defaultContent: '0.00',
                        render: function(data) {
                            return '$' + parseFloat(data || 0).toFixed(2);
                        }
                    },
                    {
                        data: 'id',
                        render: function(data) {
                            return `
                            <button class="btn btn-sm btn-primary btn-edit-site" data-bs-toggle="modal" onclick="editInventory(${data})">Edit</button>
                            <button class="btn btn-sm btn-danger btn-delete-site" onclick="deleteInventory(${data})">Delete</button>
                        `;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy',
                    {
                        extend: 'csv',
                        filename: 'Inventory'
                    },
                    {
                        extend: 'excel',
                        filename: 'Inventory'
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Inventory',

                        orientation: 'landscape',
                        pageSize: 'LEGAL',

                        filename: function() {
                            const today = new Date();
                            let day = String(today.getDate()).padStart(2, '0');
                            let month = String(today.getMonth() + 1).padStart(2, '0');
                            let year = today.getFullYear();
                            return 'Inventory_' + day + month + year;
                        },

                        exportOptions: {
                            columns: ':visible:not(:last-child)' // remove actions column if exists
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

                            /* PAGE MARGINS */
                            doc.pageMargins = [10, 10, 10, 10];

                            /* AUTO COLUMN WIDTH */
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

                            /* ===== BORDERS ===== */
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
                    },
                    'print'
                ],
                searching: true,
                search: {
                    smart: true,
                    regex: false,
                    caseInsensitive: true
                },
                language: {
                    emptyTable: "No inventory items found. Click 'Add Item' to create one.",
                    zeroRecords: "No matching records found",
                    search: "Search:",
                    searchPlaceholder: "Search inventory...",
                    info: "Showing _START_ to _END_ of _TOTAL_ items",
                    infoEmpty: "Showing 0 to 0 of 0 items",
                    infoFiltered: "(filtered from _MAX_ total items)"
                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                pageLength: 10
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

    // ─── Reload data into the existing table ────────────────────────────────────
    function reloadInventoryData() {
        const dataUrl = '<?= base_url('admin/inventory/data') ?>';

        $.ajax({
            url: dataUrl,
            type: 'GET',
            success: function(response) {
                inventoryData = response.data || [];
                inventoryTable.clear();
                inventoryTable.rows.add(inventoryData);
                inventoryTable.draw(false);
            },
            error: function(xhr) {
                console.error('Reload error:', xhr);
                Swal.fire('Error', 'Failed to reload data', 'error');
            }
        });
    }

    // ─── Edit inventory ─────────────────────────────────────────────────────────
    function editInventory(id) {
        const url = `<?= base_url('admin/inventory') ?>/${id}`;

        $.get(url, function(response) {
            if (response.success) {
                const data = response.data;
                $('#invId').val(data.id);
                $('#invPartNumber').val(data.part_number);
                $('#invDescription').val(data.part_description);
                $('#invBin').val(data.bin);
                $('#invRowAisle').val(data.row_aisle);
                $('#invShelf').val(data.shelf);
                $('#invQty').val(data.qty);
                $('#invCost').val(data.total_value);
                $('#invModalLabel').text('Edit Inventory Item');

                // Mark pre-filled fields as valid so they don't look untouched
                $('#invForm input[required]').each(function() {
                    if ($(this).val() !== '') {
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                });

                inventoryModal.show();
            } else {
                Swal.fire('Error', response.message || 'Failed to load item', 'error');
            }
        }).fail(function(xhr) {
            console.error('Edit failed:', xhr);
            Swal.fire('Error', 'Failed to load inventory item', 'error');
        });
    }

    // ─── Save / Update inventory ────────────────────────────────────────────────
    function saveInventory() {
        const formData = new FormData($('#invForm')[0]);
        const id = $('#invId').val();
        const url = id ?
            `<?= base_url('admin/inventory/update') ?>/${id}` :
            '<?= base_url('admin/inventory/store') ?>';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    inventoryModal.hide();
                    reloadInventoryData();
                    Swal.fire('Success', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message || 'Failed to save', 'error');
                }
            },
            error: function(xhr) {
                console.error('Save error:', xhr);
                const resp = xhr.responseJSON;
                if (resp && resp.errors) {
                    Swal.fire('Validation Error', Object.values(resp.errors).join('<br>'), 'error');
                } else {
                    Swal.fire('Error', resp?.message || 'An error occurred', 'error');
                }
            }
        });
    }

    // ─── Delete inventory ───────────────────────────────────────────────────────
    function deleteInventory(id) {
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
                const url = `<?= base_url('admin/inventory/delete') ?>/${id}`;

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            reloadInventoryData();
                            Swal.fire('Deleted!', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message || 'Failed to delete', 'error');
                        }
                    },
                    error: function(xhr) {
                        const resp = xhr.responseJSON;
                        Swal.fire('Error', resp?.message || 'Failed to delete inventory item', 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>