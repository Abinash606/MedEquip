<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $BASE_URL = base_url(); ?>
<div class="d-flex align-items-center mb-3">
    <h1 class="me-auto">Equipment DB</h1>
    <button class="btn btn-primary" id="addBtn"><i class="fa-solid fa-plus"></i> Add Equipment</button>
</div>

<!-- PASS PHP DATA TO JS -->
<script>
const BASE_URL = "<?= $BASE_URL ?>";
const equipmentData = <?= json_encode($equipment) ?>;
</script>

<div class="glass-card">
    <table class="table table-striped" id="equipmentTable">
        <thead>
            <tr>
                <th>Brand</th>
                <th>Model</th>
                <th>Description</th>
                <th>Part #</th>
                <!-- <th>AKA</th> -->
                <th>Service Manual</th>
                <th>Owners Manual</th>
                <th>Photo</th>
                <th width="150">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<!-- MODAL -->
<div class="modal fade" id="equipmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="equipmentForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="equipment_id">

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Brand</label>
                            <input type="text" name="make" id="make" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Model</label>
                            <input type="text" name="model" id="model" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label>Description</label>
                        <input type="text" name="device_type" id="device_type" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Part #</label>
                            <input type="text" name="serial_number" id="serial_number" class="form-control">
                        </div>
                        <!-- <div class="col-md-6 mb-2">
                            <label>AKA</label>
                            <input type="text" name="asset_tag" id="asset_tag" class="form-control">
                        </div> -->
                    </div>

                    <hr>

                    <div class="mb-2">
                        <label>Service Manual (PDF, Max 5MB)</label>
                        <input type="file" name="service_manual" class="form-control" accept="application/pdf">
                    </div>

                    <div class="mb-2">
                        <label>Owners Manual (Max 5MB)</label>
                        <input type="file" name="pm_manual" class="form-control">
                    </div>



                    <div class="mb-2">
                        <label>Photo / File (Max 5MB)</label>
                        <input type="file" name="photo" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="saveBtn">
                        <span class="btn-text">Save</span>
                        <span class="spinner-border spinner-border-sm d-none" id="saveLoader"></span>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DEPENDENCIES -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let table;

function initEquipmentTable() {

    table = $('#equipmentTable').DataTable({
        data: equipmentData,
        destroy: true,

        columns: [{
                data: 'make',
                defaultContent: '-'
            },
            {
                data: 'model',
                defaultContent: '-'
            },
            {
                data: 'device_type',
                defaultContent: '-'
            },
            {
                data: 'serial_number',
                defaultContent: '-'
            },

            {
                data: 'service_manual_path',
                orderable: false,
                searchable: false,
                render: d => d ?
                    `<a href="${BASE_URL}/${d}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>` :
                    '—'
            },
            {
                data: 'pm_manual_path',
                orderable: false,
                searchable: false,
                render: d => d ?
                    `<a href="${BASE_URL}/${d}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>` :
                    '—'
            },
            {
                data: 'photo_path',
                orderable: false,
                searchable: false,
                render: d => d ?
                    `<a href="${BASE_URL}/${d}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>` :
                    '—'
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: id => `
                    <button class="btn btn-sm btn-info editBtn" data-id="${id}">Edit</button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="${id}">Delete</button>
                `
            }
        ],

        dom: 'Bfrtip',

        buttons: [{
                extend: 'copy',
                filename: 'Equipment',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'csv',
                filename: 'Equipment',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'excel',
                filename: 'Equipment',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'pdf',
                filename: function() {
                    const today = new Date();
                    let day = String(today.getDate()).padStart(2, '0');
                    let month = String(today.getMonth() + 1).padStart(2, '0');
                    let year = today.getFullYear();
                    return 'Equipment_' + day + month + year;
                },
                title: 'Equipment',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'print',
                title: 'Equipment',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            }
        ],

        pageLength: 10,
        responsive: true
    });
}


$(document).ready(() => initEquipmentTable());

/* ADD */
$('#addBtn').on('click', () => {
    equipmentForm.reset();
    equipment_id.value = '';
    new bootstrap.Modal('#equipmentModal').show();
});

/* EDIT */
$(document).on('click', '.editBtn', function() {
    const id = $(this).data('id');
    fetch(`${BASE_URL}/admin/equipment-db/${id}`)
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                const d = res.data;
                equipment_id.value = d.id;
                make.value = d.make ?? '';
                model.value = d.model ?? '';
                device_type.value = d.device_type ?? '';
                serial_number.value = d.serial_number ?? '';
                // asset_tag.value = d.asset_tag ?? '';
                new bootstrap.Modal('#equipmentModal').show();
            }
        });
});

/* SAVE */
// equipmentForm.onsubmit = e => {
//     e.preventDefault();
//     fetch(`${BASE_URL}/admin/equipment-db/save`, {
//             method: 'POST',
//             body: new FormData(equipmentForm)
//         })
//         .then(r => r.json())
//         .then(res => {
//             if (res.status === 'success') {
//                 location.reload();
//             } else {
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Upload Error',
//                     text: res.message ?? 'Save failed'
//                 });
//             }
//         });

// };
equipmentForm.onsubmit = e => {
    e.preventDefault();

    const saveBtn = document.getElementById('saveBtn');
    const loader = document.getElementById('saveLoader');
    const text = saveBtn.querySelector('.btn-text');

    // 🔒 Disable button & show loader
    saveBtn.disabled = true;
    loader.classList.remove('d-none');
    text.textContent = 'Saving...';

    fetch(`${BASE_URL}/admin/equipment-db/save`, {
            method: 'POST',
            body: new FormData(equipmentForm)
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Error',
                    text: res.message ?? 'Save failed'
                });
            }
        })
        .catch(() => {
            Swal.fire('Error', 'Something went wrong', 'error');
        })
        .finally(() => {
            // 🔓 Re-enable button if needed
            saveBtn.disabled = false;
            loader.classList.add('d-none');
            text.textContent = 'Save';
        });
};


/* DELETE */
$(document).on('click', '.deleteBtn', function() {
    const id = $(this).data('id');
    const row = $(this).closest('tr');

    Swal.fire({
        title: 'Delete?',
        text: 'This equipment will be deleted',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33'
    }).then(r => {
        if (!r.isConfirmed) return;

        fetch(`${BASE_URL}/admin/equipment-db/delete/${id}`, {
                method: 'POST'
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    table.row(row).remove().draw();
                    Swal.fire('Deleted', '', 'success');
                }
            });
    });
});

const MAX_SIZE = 5 * 1024 * 1024; // 5MB

function checkFileSize(input) {
    if (!input.files.length) return true;

    const file = input.files[0];
    if (file.size > MAX_SIZE) {
        Swal.fire({
            icon: 'error',
            title: 'File too large',
            text: 'Maximum allowed size is 5MB'
        });
        input.value = ''; // reset file input
        return false;
    }
    return true;
}

// Attach to all file inputs
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        checkFileSize(this);
    });
});
</script>

<?= $this->endSection() ?>