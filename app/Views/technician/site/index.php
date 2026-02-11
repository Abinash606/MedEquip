<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<section id="sites" class="view-section active">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Site Directory</h3>
    </div>

    <!-- Search -->
    <div class="glass-card mb-4">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="fa-solid fa-search"></i>
            </span>
            <input id="site-search" type="text"
                class="form-control border-start-0 ps-0"
                placeholder="Search by site, address or customer name...">
        </div>
    </div>

    <!-- Customer Filter -->
    <div class="glass-card mb-4">
        <label class="form-label fw-bold">Filter by Customer</label>
        <select id="customer-filter" class="form-select" style="width:25%">
            <option value="">All Customers</option>
            <?php
            $uniqueCustomers = array_unique(array_column($sites, 'customer_name'));
            foreach ($uniqueCustomers as $customer):
            ?>
                <option value="<?= esc($customer) ?>">
                    <?= esc($customer) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Sites Table -->
    <div class="glass-card">
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
                        <tr>
                            <td><?= esc($site['site_name']) ?></td>
                            <td><?= esc($site['customer_name']) ?></td>
                            <td><?= esc($site['site_address']) ?></td>
                            <td><?= esc($site['site_contact_name']) ?></td>
                            <td><?= esc($site['site_phone']) ?></td>
                            <td><?= esc($site['site_email']) ?></td>
                            <td>
                                <a href="<?= base_url('technician/sites/view/' . $site['id']) ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No sites found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>

<!-- 🔥 SEARCH + FILTER SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('site-search');
        const customerFilter = document.getElementById('customer-filter');
        const rows = document.querySelectorAll('#sites-datatable tbody tr');

        function filterSites() {
            const searchValue = searchInput.value.toLowerCase();
            const customerValue = customerFilter.value.toLowerCase();

            rows.forEach(row => {
                const siteName = row.cells[0].innerText.toLowerCase();
                const customerName = row.cells[1].innerText.toLowerCase();
                const siteAddress = row.cells[2].innerText.toLowerCase();

                const matchSearch =
                    siteName.includes(searchValue) ||
                    customerName.includes(searchValue) ||
                    siteAddress.includes(searchValue);

                const matchCustomer =
                    customerValue === '' || customerName === customerValue;

                row.style.display = (matchSearch && matchCustomer) ?
                    '' :
                    'none';
            });
        }

        searchInput.addEventListener('keyup', filterSites);
        customerFilter.addEventListener('change', filterSites);
    });
</script>
<script>
    $(document).ready(function() {

        const table = $('#sites-datatable').DataTable({
            dom: 'Bfrtip',
            pageLength: 10,
            order: [
                [0, 'asc']
            ],

            buttons: [{
                    extend: 'copy',
                    text: 'Copy',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    filename: function() {
                        const today = new Date();
                        let d = String(today.getDate()).padStart(2, '0');
                        let m = String(today.getMonth() + 1).padStart(2, '0');
                        let y = today.getFullYear();
                        return 'Technician_Sites_' + d + m + y;
                    },
                    title: 'Technician Sites',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Technician Sites',

                    filename: function() {
                        const today = new Date();
                        let d = String(today.getDate()).padStart(2, '0');
                        let m = String(today.getMonth() + 1).padStart(2, '0');
                        let y = today.getFullYear();
                        return 'Technician_Sites_' + d + m + y;
                    },

                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },

                    customize: function(doc) {

                        /* FONT */
                        doc.styles.title.fontSize = 13;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.defaultStyle.fontSize = 8;

                        /* MARGIN */
                        doc.pageMargins = [15, 30, 15, 20];

                        const table = doc.content[1].table;
                        const body = table.body;
                        const colCount = body[0].length;

                        /* AUTO WIDTH */
                        table.widths = Array(colCount).fill('*');

                        /* HEADER COLOR */
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 9,
                            color: 'black',
                            fillColor: '#a4d169',
                            alignment: 'left'
                        };

                        /* ROW COLORS */
                        doc.styles.tableBodyEven = {
                            fillColor: '#f3f3f3'
                        };
                        doc.styles.tableBodyOdd = {
                            fillColor: '#ffffff'
                        };

                        /* BORDERS */
                        table.layout = {
                            hLineWidth: function() {
                                return 0.8;
                            },
                            vLineWidth: function() {
                                return 0.8;
                            },
                            hLineColor: function() {
                                return '#cccccc';
                            },
                            vLineColor: function() {
                                return '#cccccc';
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

                        /* WORD WRAP */
                        body.forEach(function(row, rowIndex) {
                            row.forEach(function(cell) {

                                if (rowIndex === 0) return;

                                if (typeof cell.text === 'string') {
                                    cell.text = cell.text.replace(/(.{35})/g,
                                        '$1\n');
                                }

                                cell.noWrap = false;
                                cell.alignment = 'left';
                            });
                        });

                    }
                }
            ]
        });

        // 🔍 Custom search input
        $('#site-search').on('keyup', function() {
            table.search(this.value).draw();
        });

        // 🧑‍💼 Customer filter
        $('#customer-filter').on('change', function() {
            const val = this.value;
            if (val === '') {
                table.column(1).search('').draw(); // Customer Name column
            } else {
                table.column(1).search('^' + val + '$', true, false).draw();
            }
        });

    });
</script>

<?= $this->endSection() ?>