<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Inventory Management</h3>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#inventoryModal">
        <i class="fa-solid fa-plus me-2"></i> Add Item
    </button>
</div>

<div class="glass-card">
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

<!-- Inventory Modal -->
<div class="modal fade" id="inventoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invModalLabel">Add Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="invForm" enctype="multipart/form-data">
                    <input type="hidden" id="invId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Part #</label>
                            <input type="text" id="invPartNumber" name="part_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Part Description</label>
                            <input type="text" id="invDescription" name="part_description" class="form-control"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bin</label>
                            <input type="text" id="invBin" name="bin" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">QTY</label>
                            <input type="number" id="invQty" name="qty" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Value</label>
                            <input type="number" step="0.01" id="invCost" name="total_value" class="form-control"
                                required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Image</label>
                            <input type="file" id="invImage" name="image" class="form-control" accept="image/*">
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
let inventoryData = []; // Store data locally

$(document).ready(function() {
    inventoryModal = new bootstrap.Modal(document.getElementById('inventoryModal'));

    // Fetch data immediately when page loads
    fetchInventoryData();

    // Save button
    $('#saveInvBtn').on('click', function() {
        saveInventory();
    });

    // Reset modal on close
    $('#inventoryModal').on('hidden.bs.modal', function() {
        $('#invForm')[0].reset();
        $('#invId').val('');
        $('#invModalLabel').text('Add Inventory Item');
    });
});

// Fetch inventory data
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
            // Initialize empty table even on error
            inventoryData = [];
            initializeDataTable();
        }
    });
}

// Initialize DataTable with fetched data
function initializeDataTable() {
    try {
        inventoryTable = $('#inventory-datatable').DataTable({
            data: inventoryData, // Use local data instead of ajax
            columns: [{
                    data: 'part_number',
                    defaultContent: '-'
                },
                {
                    data: 'image',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data ?
                                `<img src="${data}" alt="Part Image" style="width:50px;height:50px;object-fit:cover;" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'50\' height=\'50\'%3E%3Crect fill=\'%23ddd\' width=\'50\' height=\'50\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23999\'%3ENo Image%3C/text%3E%3C/svg%3E';">` :
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
                        return typeof data === 'number' ?
                            '$' + parseFloat(data).toFixed(2) :
                            '$' + parseFloat(data || 0).toFixed(2);
                    }
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        return `
                            <button class='btn btn-sm btn-outline-secondary btn-edit-site' data-bs-toggle='modal' onclick="editInventory(${data})">Edit</button>
                            <button class='btn btn-sm btn-outline-danger btn-delete-site' onclick="deleteInventory(${data})">Delete</button>
                        `;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            responsive: true,
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
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
            pageLength: 10,
            drawCallback: function(settings) {
                var api = this.api();
                var filteredCount = api.rows({
                    search: 'applied'
                }).count();
            },
            initComplete: function(settings, json) {}
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

// Reload data function
function reloadInventoryData() {
    const dataUrl = '<?= base_url('admin/inventory/data') ?>';

    $.ajax({
        url: dataUrl,
        type: 'GET',
        success: function(response) {
            inventoryData = response.data || [];
            inventoryTable.clear();
            inventoryTable.rows.add(inventoryData);
            inventoryTable.draw(false); // false maintains current page
        },
        error: function(xhr) {
            console.error('Reload error:', xhr);
            Swal.fire('Error', 'Failed to reload data', 'error');
        }
    });
}

// Edit inventory
function editInventory(id) {
    const url = `<?= base_url('admin/inventory') ?>/${id}`;

    $.get(url, function(response) {
        if (response.success) {
            const data = response.data;
            $('#invId').val(data.id);
            $('#invPartNumber').val(data.part_number);
            $('#invDescription').val(data.part_description);
            $('#invBin').val(data.bin);
            $('#invQty').val(data.qty);
            $('#invCost').val(data.total_value);
            $('#invModalLabel').text('Edit Inventory Item');
            inventoryModal.show();
        } else {
            Swal.fire('Error', response.message || 'Failed to load item', 'error');
        }
    }).fail(function(xhr) {
        console.error('Edit failed:', xhr);
        Swal.fire('Error', 'Failed to load inventory item', 'error');
    });
}

// Save / Update inventory
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
                reloadInventoryData(); // Use custom reload function
                Swal.fire('Success', response.message, 'success');
            } else {
                Swal.fire('Error', response.message || 'Failed to save', 'error');
            }
        },
        error: function(xhr) {
            console.error('Save error:', xhr);
            const resp = xhr.responseJSON;
            if (resp && resp.errors) {
                let errorMsg = Object.values(resp.errors).join('<br>');
                Swal.fire('Validation Error', errorMsg, 'error');
            } else {
                Swal.fire('Error', resp?.message || 'An error occurred', 'error');
            }
        }
    });
}

// Delete inventory
function deleteInventory(id) {
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