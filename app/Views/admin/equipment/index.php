<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $BASE_URL = rtrim(base_url(), '/'); ?>
<div class="d-flex align-items-center mb-3 topbar">
    <h3 class="me-auto">Equipment DB</h3>
    <button class="btn btn-primary" id="addBtn"><i class="fa-solid fa-plus"></i> Add Equipment</button>
</div>

<!-- PASS PHP DATA TO JS -->
<script>
    const BASE_URL = "<?= $BASE_URL ?>";
    const equipmentData = <?= json_encode($equipment) ?>;
</script>
<div class="content">
    <div class="glass-card p-3">
        <div class="table-responsive">
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
    </div>
</div>
<!-- MODAL -->
<div class="modal fade" id="equipmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="equipmentForm" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="equipment_id">

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Brand</label>
                            <select name="make" id="make" class="form-control select2-field" required
                                style="width:100%"></select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Model</label>
                            <select name="model" id="model" class="form-control select2-field" required
                                style="width:100%"></select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label>Description</label>
                        <select name="device_type" id="device_type" class="form-control select2-field" required
                            style="width:100%"></select>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    /* Select2 dropdown - white background, dark text */
    .select2-dropdown {
        background: #ffffff !important;
        border: 1px solid #ced4da !important;
        border-radius: 6px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .15) !important;
    }

    .select2-results__option {
        color: #212529 !important;
        padding: 8px 12px !important;
        font-size: 14px !important;
    }

    .select2-container--default .select2-results__option--highlighted {
        background-color: #0d6efd !important;
        color: #fff !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #e9ecef !important;
        color: #212529 !important;
    }

    .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        padding: 6px 8px !important;
        color: #212529 !important;
        background: #fff !important;
    }

    /* Selection box inside modal */
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid #adb5bd !important;
        border-radius: 10px !important;
        background: #fff !important;
    }

    .select2-container--default.select2-container--open .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, .15) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #212529 !important;
        line-height: 28px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 5px !important;
    }
</style>
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
                        `<a href="${BASE_URL}/${d}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>` : '—'
                },
                {
                    data: 'pm_manual_path',
                    orderable: false,
                    searchable: false,
                    render: d => d ?
                        `<a href="${BASE_URL}/${d}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>` : '—'
                },
                {
                    data: 'photo_path',
                    orderable: false,
                    searchable: false,
                    render: d => d ?
                        `<a href="${BASE_URL}/${d}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>` : '—'
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
                    extend: 'pdfHtml5',
                    title: 'Equipment',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',

                    filename: function() {
                        const d = new Date();
                        return 'Equipment_' +
                            String(d.getDate()).padStart(2, '0') +
                            String(d.getMonth() + 1).padStart(2, '0') +
                            d.getFullYear();
                    },

                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },

                    customize: function(doc) {

                        /* FONT SIZE */
                        doc.defaultStyle.fontSize = 8;

                        /* HEADER STYLE */
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 9,
                            color: 'black',
                            fillColor: '#a4d169',
                            alignment: 'center'
                        };

                        /* AUTO WIDTH */
                        var table = doc.content[1].table;
                        var colCount = table.body[0].length;
                        table.widths = new Array(colCount).fill('*');

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

                        /* MARGIN */
                        doc.pageMargins = [10, 10, 10, 10];

                        /* ROW COLORS */
                        doc.styles.tableBodyEven = {
                            fillColor: '#f2f2f2'
                        };
                        doc.styles.tableBodyOdd = {
                            fillColor: '#ffffff'
                        };
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
                    serial_number.value = d.serial_number ?? '';
                    loadBrands(d.make);
                    loadModels(d.make, d.model);
                    loadDescs(d.make, d.model, d.device_type);
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
        const loader  = document.getElementById('saveLoader');
        const text    = saveBtn.querySelector('.btn-text');
        const currentId = document.getElementById('equipment_id').value;

        // Duplicate serial number check (client-side against loaded data)
        const serialVal = (document.getElementById('serial_number').value || '').trim();
        if (serialVal) {
            const dup = equipmentData.find(r =>
                String(r.serial_number || '').trim() === serialVal &&
                String(r.id) !== String(currentId)
            );
            if (dup) {
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate Serial Number',
                    text: 'Serial number "' + serialVal + '" already exists in the equipment database (Brand: ' + (dup.make||'') + ', Model: ' + (dup.model||'') + ').'
                });
                return;
            }
        }

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
    // ---------------------------------------
    // Select2 Searchable Dropdowns
    // ---------------------------------------
    function makeOptions(values, selected) {
        return values.map(v =>
            new Option(v, v, v === selected, v === selected)
        );
    }

    function initSelect2(selector, placeholder) {
        $(selector).select2({
            placeholder: placeholder,
            allowClear: true,
            tags: true, // allows adding new entries
            dropdownParent: $('#equipmentModal'),
            createTag: function(params) {
                const term = $.trim(params.term);
                if (!term) return null;
                return {
                    id: 'new_' + term,
                    text: '+ Add "' + term + '"',
                    newVal: term
                };
            }
        }).on('select2:select', function(e) {
            // If user picked a "new" tag, replace value with clean text
            if (e.params.data.newVal) {
                const clean = e.params.data.newVal;
                // Replace the temp option with a real one
                const $opt = new Option(clean, clean, true, true);
                $(this).empty().append($opt).trigger('change');
            }
        });
    }

    function loadBrands(selected) {
        const brands = [...new Set(equipmentData.map(r => r.make).filter(Boolean))].sort();
        $('#make').empty().append('<option></option>');
        makeOptions(brands, selected).forEach(o => $('#make').append(o));
        $('#make').trigger('change');
    }

    function loadModels(brand, selected) {
        const rows = brand ? equipmentData.filter(r => r.make === brand) : equipmentData;
        const models = [...new Set(rows.map(r => r.model).filter(Boolean))].sort();
        $('#model').empty().append('<option></option>');
        makeOptions(models, selected).forEach(o => $('#model').append(o));
        $('#model').trigger('change');
    }

    function loadDescs(brand, model, selected) {
        let rows = equipmentData;
        if (brand) rows = rows.filter(r => r.make === brand);
        if (model) rows = rows.filter(r => r.model === model);
        const descs = [...new Set(rows.map(r => r.device_type).filter(Boolean))].sort();
        $('#device_type').empty().append('<option></option>');
        makeOptions(descs, selected).forEach(o => $('#device_type').append(o));
        $('#device_type').trigger('change');
    }

    // Init Select2 on all three
    initSelect2('#make', 'Type or select brand...');
    initSelect2('#model', 'Type or select model...');
    initSelect2('#device_type', 'Type or select description...');

    // Brand change → reload model & desc
    $('#make').on('change', function() {
        const brand = $(this).val();
        loadModels(brand, null);
        loadDescs(brand, null, null);
    });

    // Model change → auto-fill desc but keep ALL descriptions available (read-only master DB)
    $('#model').on('change', function() {
        const brand = $('#make').val();
        const model = $(this).val();
        // Always load ALL descriptions so new brand/model still sees existing descriptions
        loadDescs(null, null, null);
        const match = equipmentData.find(r => r.make === brand && r.model === model);
        if (match && match.device_type) {
            // Pre-select matching description but don't restrict the list
            $('#device_type').val(match.device_type).trigger('change');
        }
        // Serial # intentionally NOT auto-populated — must remain blank for manual entry
        $('#serial_number').val('');
    });

    // Load initial data
    loadBrands(null);
    loadModels(null, null);
    loadDescs(null, null, null);

    // Reset on Add
    $('#addBtn').on('click', function() {
        loadBrands(null);
        loadModels(null, null);
        loadDescs(null, null, null);
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