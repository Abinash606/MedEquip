<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>
<style>
    /* ===== DARK THEME: Modals & DataTables ===== */
    .modal-content {
        background: #0E1630 !important;
        border: 1px solid rgba(255, 255, 255, .12) !important;
        color: #E9EDFF !important;
        border-radius: 16px !important;
    }

    .modal-header {
        background: linear-gradient(135deg, rgba(124, 58, 237, .9), rgba(34, 211, 238, .8)) !important;
        border-bottom: none !important;
        border-radius: 16px 16px 0 0 !important;
    }

    .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, .08) !important;
        background: rgba(7, 10, 18, .4) !important;
        border-radius: 0 0 16px 16px !important;
    }

    .modal-body {
        background: rgba(14, 22, 48, .6) !important;
    }

    .modal-title,
    .modal-body .form-label,
    .modal-body label,
    .modal-body h5,
    .modal-body p {
        color: #E9EDFF !important;
    }

    .modal-body .form-control,
    .modal-body .form-select {
        background: rgba(255, 255, 255, .06) !important;
        border: 1px solid rgba(255, 255, 255, .14) !important;
        color: #E9EDFF !important;
        border-radius: 10px !important;
    }

    .modal-body .form-control::placeholder {
        color: rgba(233, 237, 255, .35) !important;
    }

    .modal-body .form-select option {
        color: #000 !important;
        background: #fff !important;
    }

    /* ===== DataTable Header ===== */
    table.dataTable thead th {
        background: rgba(7, 10, 18, .6) !important;
        color: rgba(233, 237, 255, .55) !important;
        border-bottom: 1px solid rgba(255, 255, 255, .08) !important;
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        position: relative;
        padding-right: 24px !important;
        white-space: nowrap;
    }

    /* ===== Nuclear Fix: Remove ALL default sort arrows ===== */
    table.dataTable thead th.sorting,
    table.dataTable thead th.sorting_asc,
    table.dataTable thead th.sorting_desc {
        background-image: none !important;
    }

    table.dataTable thead th.sorting::before,
    table.dataTable thead th.sorting::after,
    table.dataTable thead th.sorting_asc::before,
    table.dataTable thead th.sorting_asc::after,
    table.dataTable thead th.sorting_desc::before,
    table.dataTable thead th.sorting_desc::after,
    table.dataTable thead th span.dt-column-order,
    table.dataTable thead th span.dt-column-order::before,
    table.dataTable thead th span.dt-column-order::after {
        display: none !important;
        content: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    /* ===== Add single clean sort icon ===== */
    table.dataTable thead th.sorting::after {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 0.4 !important;
        content: "⇅" !important;
        position: absolute !important;
        right: 6px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        font-size: 10px !important;
    }

    table.dataTable thead th.sorting_asc::after {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 0.8 !important;
        content: "↑" !important;
        position: absolute !important;
        right: 6px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        font-size: 10px !important;
    }

    table.dataTable thead th.sorting_desc::after {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 0.8 !important;
        content: "↓" !important;
        position: absolute !important;
        right: 6px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        font-size: 10px !important;
    }

    /* ===== DataTable Body ===== */
    table.dataTable tbody tr {
        background: transparent !important;
    }

    table.dataTable tbody tr:hover {
        background: rgba(255, 255, 255, .04) !important;
    }

    table.dataTable tbody td {
        border-bottom: 1px solid rgba(255, 255, 255, .05) !important;
        color: #E9EDFF !important;
    }

    table.dataTable.stripe tbody tr.odd,
    table.dataTable.stripe tbody tr.even {
        background: transparent !important;
    }

    /* ===== Fix Text Visibility ===== */
    #sites-datatable tbody td,
    #sites-datatable tbody tr td {
        color: #E9EDFF !important;
    }

    /* ===== Actions Column — prevent wrapping ===== */
    table.dataTable tbody td:last-child {
        white-space: nowrap !important;
        min-width: 120px;
    }

    /* ===== DataTable Controls ===== */
    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        background: rgba(255, 255, 255, .06) !important;
        border: 1px solid rgba(255, 255, 255, .12) !important;
        color: #E9EDFF !important;
        border-radius: 8px !important;
        padding: 4px 8px;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        color: rgba(233, 237, 255, .6) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #E9EDFF !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, rgba(124, 58, 237, .8), rgba(34, 211, 238, .6)) !important;
        border-color: transparent !important;
        color: #fff !important;
    }

    /* ===== Export Buttons ===== */
    .dt-buttons .btn,
    .dt-button {
        color: #E9EDFF !important;
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 8px !important;
        margin-right: 4px;
    }

    .dt-buttons .btn:hover,
    .dt-button:hover {
        background: rgba(255, 255, 255, 0.15) !important;
    }

    /* ===== Modal Buttons ===== */
    .modal .btn-primary {
        background: linear-gradient(90deg, rgba(34, 211, 238, .9), rgba(124, 58, 237, .8)) !important;
        border: none !important;
        color: #fff !important;
    }

    .modal .btn-outline-secondary {
        color: rgba(233, 237, 255, .7) !important;
        border-color: rgba(255, 255, 255, .2) !important;
    }

    /* ===== Fix Add Site button position ===== */
    #sites .d-flex.justify-content-between {
        padding-top: 10px;
        align-items: center;
    }

    /* ===== END DARK THEME ===== */
</style>

<section id="sites" class="view-section active">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 topbar">
        <h3 class="fw-bold mb-0">Site Directory</h3>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addSiteModal">
            <i class="fa-solid fa-plus me-2"></i> Add Site
        </button>
    </div>
    <div class="content">
    <!-- Search -->
    <div class="glass-card mb-4">
        <div class="input-group">
            <span class="input-group-text">
                <i class="fa-solid fa-search"></i>
            </span>
            <input id="site-search" type="text" class="form-control border-start-0"
                placeholder="Search by site, address or customer name...">
        </div>
    </div>

    <!-- Customer Filter -->
    <div class="glass-card mb-4">
        <label class="form-label fw-bold">Filter by Customer</label>
        <select id="customer-filter" class="form-select">
            <option value="">All Customers</option>
            <?php foreach ($customers as $cust): ?>
                <option value="<?= esc($cust['id']) ?>"><?= esc($cust['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Sites Table -->
    <div class="glass-card">
        <div class="table-responsive">
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
                        <tr data-customer-id="<?= esc($site['customer_id']) ?>">
                            <td><?= esc($site['site_name']) ?></td>
                            <td><?= esc($site['customer_name']) ?></td>
                            <td><?= esc($site['site_address']) ?></td>
                            <td><?= esc($site['site_contact_name']) ?></td>
                            <td><?= esc($site['site_phone']) ?></td>
                            <td><?= esc($site['site_email']) ?></td>
                            <td>
                                <a href="<?= base_url('technician/sites/view/' . $site['id']) ?>"
                                    class="btn btn-sm btn-primary">
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
    </div>
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
    // $(document).ready(function() {

    //     // DataTable init
    //     var table;
    //     if ($.fn.dataTable.isDataTable('#sites-datatable')) {
    //         table = $('#sites-datatable').DataTable();
    //         $('#sites-datatable thead th').each(function() {
    //             $(this).find('span.dt-column-order').remove();
    //         });
    //     } else {
    //         table = $('#sites-datatable').DataTable({
    //             dom: 'Brtip', pageLength: 10, order: [[0, 'asc']], stateSave: false,
    //             buttons: [
    //                 { extend: 'copy', text: 'Copy', exportOptions: { columns: ':visible:not(:last-child)' } },
    //                 { extend: 'excelHtml5', text: 'Excel',
    //                   filename: function() { var d=new Date(); return 'Technician_Sites_'+String(d.getDate()).padStart(2,'0')+String(d.getMonth()+1).padStart(2,'0')+d.getFullYear(); },
    //                   title: 'Technician Sites', exportOptions: { columns: ':visible:not(:last-child)' } },
    //                 { extend: 'pdfHtml5', text: 'PDF', orientation: 'landscape', pageSize: 'A4',
    //                   title: 'Technician Sites',
    //                   filename: function() { var d=new Date(); return 'Technician_Sites_'+String(d.getDate()).padStart(2,'0')+String(d.getMonth()+1).padStart(2,'0')+d.getFullYear(); },
    //                   exportOptions: { columns: ':visible:not(:last-child)' },
    //                   customize: function(doc) {
    //                       doc.styles.title.fontSize=13; doc.styles.tableHeader.fontSize=9;
    //                       doc.defaultStyle.fontSize=8; doc.pageMargins=[15,30,15,20];
    //                       var tbl=doc.content[1].table; tbl.widths=Array(tbl.body[0].length).fill('*');
    //                       doc.styles.tableHeader={bold:true,fontSize:9,color:'black',fillColor:'#a4d169',alignment:'left'};
    //                       tbl.layout={hLineWidth:function(){return 0.8;},vLineWidth:function(){return 0.8;},
    //                           hLineColor:function(){return '#cccccc';},vLineColor:function(){return '#cccccc';},
    //                           paddingLeft:function(){return 4;},paddingRight:function(){return 4;},
    //                           paddingTop:function(){return 3;},paddingBottom:function(){return 3;}};
    //                   }
    //                 }
    //             ]
    //         });
    //     }

    //     // Search
    //     $('#site-search').on('keyup', function() { table.search(this.value).draw(); });

    //     // Customer filter
    //     function applyTechCustomerFilter(selectedId) {
    //         $.fn.dataTable.ext.search = [];
    //         if (selectedId) {
    //             $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    //                 var row = table.row(dataIndex).node();
    //                 return $(row).data('customer-id') == selectedId;
    //             });
    //         }
    //         table.draw();
    //     }

    //     $('#customer-filter').on('change', function() {
    //         applyTechCustomerFilter(this.value);
    //         if (this.value) { $('#add-site-customer').val(this.value); _defaultModalCust = this.value; }
    //     });

    //     // Pre-selection from session or flashdata
    //     var _preselect        = '<?= (int)($active_customer_id ?? 0) ?>';
    //     var _autoOpenCust     = '<?= (int)($auto_open_modal_customer ?? 0) ?>';
    //     var _defaultModalCust = _autoOpenCust || _preselect;

    //     if (_preselect && _preselect !== '0') {
    //         document.getElementById('customer-filter').value = _preselect;
    //         applyTechCustomerFilter(_preselect);
    //     }
    //     if (_defaultModalCust && _defaultModalCust !== '0') {
    //         $('#add-site-customer').val(_defaultModalCust);
    //     }
    //     if (_autoOpenCust && _autoOpenCust !== '0') {
    //         setTimeout(function() {
    //             var m = document.getElementById('addSiteModal');
    //             if (m) bootstrap.Modal.getOrCreateInstance(m).show();
    //         }, 350);
    //     }

    //     // Always sync customer when modal opens
    //     document.getElementById('addSiteModal').addEventListener('show.bs.modal', function() {
    //         var c = (_defaultModalCust && _defaultModalCust !== '0') ? _defaultModalCust : document.getElementById('customer-filter').value;
    //         if (c) $('#add-site-customer').val(c);
    //     });

    //     // After modal close, restore preselect
    //     $('#addSiteModal').on('hidden.bs.modal', function() {
    //         $('#addSiteForm')[0].reset();
    //         var c = (_defaultModalCust && _defaultModalCust !== '0') ? _defaultModalCust : document.getElementById('customer-filter').value;
    //         if (c) $('#add-site-customer').val(c);
    //     });

    //     // Save Site via AJAX
    //     $('#saveSiteBtn').on('click', function() {
    //         if (!$('#add-site-name').val().trim()) { Swal.fire('Validation', 'Site name is required.', 'warning'); return; }
    //         if (!$('#add-site-customer').val()) { Swal.fire('Validation', 'Please select a customer.', 'warning'); return; }

    //         var formData = new FormData($('#addSiteForm')[0]);
    //         $.ajax({
    //             type: 'POST',
    //             url: '<?= site_url("technician/sites/add") ?>',
    //             data: formData, processData: false, contentType: false,
    //             success: function(response) {
    //                 if (response.success) {
    //                     Swal.fire('Success!', 'Site added successfully!', 'success').then(function() {
    //                         $('#addSiteModal').modal('hide');
    //                         if (response.redirect_url) { window.location.href = response.redirect_url; }
    //                         else { location.reload(); }
    //                     });
    //                 } else {
    //                     Swal.fire('Error!', response.message || 'Could not save site.', 'error');
    //                 }
    //             },
    //             error: function() { Swal.fire('Error!', 'An error occurred. Please try again.', 'error'); }
    //         });
    //     });

    // }); // end $(document).ready
</script>

<script>
    $(document).ready(function () {

        var currentCustomerId = '';

        // scoped custom filter only for sites table
        var techCustomerFilter = function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'sites-datatable') {
                return true;
            }

            if (!currentCustomerId) {
                return true;
            }

            var rowNode = table.row(dataIndex).node();
            if (!rowNode) {
                return true;
            }

            return String($(rowNode).attr('data-customer-id')) === String(currentCustomerId);
        };

        // add custom filter only once
        $.fn.dataTable.ext.search.push(techCustomerFilter);

        // DataTable init
        var table;
        if ($.fn.dataTable.isDataTable('#sites-datatable')) {
            table = $('#sites-datatable').DataTable();
            $('#sites-datatable thead th').each(function () {
                $(this).find('span.dt-column-order').remove();
            });
        } else {
            table = $('#sites-datatable').DataTable({
                dom: 'Brtip', // removed default DataTable search box
                pageLength: 10,
                order: [[0, 'asc']],
                stateSave: false,
                buttons: [
                    {
                        extend: 'copy',
                        text: 'Copy',
                        exportOptions: { columns: ':visible:not(:last-child)' }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        filename: function () {
                            var d = new Date();
                            return 'Technician_Sites_' +
                                String(d.getDate()).padStart(2, '0') +
                                String(d.getMonth() + 1).padStart(2, '0') +
                                d.getFullYear();
                        },
                        title: 'Technician Sites',
                        exportOptions: { columns: ':visible:not(:last-child)' }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Technician Sites',
                        filename: function () {
                            var d = new Date();
                            return 'Technician_Sites_' +
                                String(d.getDate()).padStart(2, '0') +
                                String(d.getMonth() + 1).padStart(2, '0') +
                                d.getFullYear();
                        },
                        exportOptions: { columns: ':visible:not(:last-child)' },
                        customize: function (doc) {
                            doc.styles.title.fontSize = 13;
                            doc.styles.tableHeader.fontSize = 9;
                            doc.defaultStyle.fontSize = 8;
                            doc.pageMargins = [15, 30, 15, 20];

                            var tbl = doc.content[1].table;
                            tbl.widths = Array(tbl.body[0].length).fill('*');

                            doc.styles.tableHeader = {
                                bold: true,
                                fontSize: 9,
                                color: 'black',
                                fillColor: '#a4d169',
                                alignment: 'left'
                            };

                            tbl.layout = {
                                hLineWidth: function () { return 0.8; },
                                vLineWidth: function () { return 0.8; },
                                hLineColor: function () { return '#cccccc'; },
                                vLineColor: function () { return '#cccccc'; },
                                paddingLeft: function () { return 4; },
                                paddingRight: function () { return 4; },
                                paddingTop: function () { return 3; },
                                paddingBottom: function () { return 3; }
                            };
                        }
                    }
                ]
            });
        }

        function applyTechCustomerFilter(selectedId) {
            currentCustomerId = $.trim(selectedId);
            table.draw();
        }

        // custom top search only
        $('#site-search').on('keyup', function () {
            table.search(this.value).draw();
        });

        // customer filter
        $('#customer-filter').on('change', function () {
            applyTechCustomerFilter(this.value);

            if (this.value) {
                $('#add-site-customer').val(this.value);
                _defaultModalCust = this.value;
            }
        });

        // Pre-selection from session or flashdata
        var _preselect        = '<?= (int)($active_customer_id ?? 0) ?>';
        var _autoOpenCust     = '<?= (int)($auto_open_modal_customer ?? 0) ?>';
        var _defaultModalCust = _autoOpenCust || _preselect;

        if (_preselect && _preselect !== '0') {
            $('#customer-filter').val(_preselect);
            applyTechCustomerFilter(_preselect);
        }

        if (_defaultModalCust && _defaultModalCust !== '0') {
            $('#add-site-customer').val(_defaultModalCust);
        }

        if (_autoOpenCust && _autoOpenCust !== '0') {
            setTimeout(function () {
                var m = document.getElementById('addSiteModal');
                if (m) bootstrap.Modal.getOrCreateInstance(m).show();
            }, 350);
        }

        // Always sync customer when modal opens
        document.getElementById('addSiteModal').addEventListener('show.bs.modal', function () {
            var c = (_defaultModalCust && _defaultModalCust !== '0')
                ? _defaultModalCust
                : document.getElementById('customer-filter').value;

            if (c) {
                $('#add-site-customer').val(c);
            }
        });

        // After modal close, restore preselect
        $('#addSiteModal').on('hidden.bs.modal', function () {
            $('#addSiteForm')[0].reset();

            var c = (_defaultModalCust && _defaultModalCust !== '0')
                ? _defaultModalCust
                : document.getElementById('customer-filter').value;

            if (c) {
                $('#add-site-customer').val(c);
            }
        });

        // Save Site via AJAX
        $('#saveSiteBtn').on('click', function () {
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
                url: '<?= site_url("technician/sites/add") ?>',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Success!', 'Site added successfully!', 'success').then(function () {
                            $('#addSiteModal').modal('hide');

                            if (response.redirect_url) {
                                window.location.href = response.redirect_url;
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Could not save site.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                }
            });
        });

    });
</script>

<?= $this->endSection() ?>