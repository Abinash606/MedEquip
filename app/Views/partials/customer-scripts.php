<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include jQuery Validation plugin -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>

<!-- Load the equipment data as a JavaScript file. This defines window.equipmentData globally -->
<!-- FullCalendar and Leaflet scripts for scheduling calendar and map -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales-all.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

<script>
  $(document).ready(function() {

    const table = $('#customer-datatable').DataTable({
      pageLength: 5,
      lengthChange: false,
      searching: true,
      ordering: true,
      info: true,
      paging: true,

      scrollX: true,
      autoWidth: false,

      dom: "<'row mb-3'<'col-md-6'B><'col-md-6 text-end'f>>" + // buttons LEFT, search RIGHT
        "<'row'<'col-12'tr>>" +
        "<'row mt-3'<'col-md-6'i><'col-md-6 text-end'p>>",

      buttons: [{
          extend: 'copyHtml5',
          text: 'Copy',
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          extend: 'excelHtml5',
          text: 'Excel',
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          extend: 'pdfHtml5',
          text: 'PDF',
          orientation: 'landscape',
          pageSize: 'A4',
          title: 'MedEquip Customer Portal | Service Management',

          exportOptions: {
            columns: ':visible'
          },

          customize: function(doc) {

            /* ========= FONT CONTROL ========= */
            doc.styles.title.fontSize = 13;
            doc.styles.tableHeader.fontSize = 8;
            doc.defaultStyle.fontSize = 7;

            doc.defaultStyle.alignment = 'left';

            /* ========= PAGE ========= */
            doc.pageMargins = [15, 35, 15, 25];

            const table = doc.content[1].table;
            const body = table.body;
            const columnCount = body[0].length;

            /* ========= COLUMN WIDTHS ========= */
            // Narrow + wide mix (IMPORTANT)
            table.widths = [
              '10%', // Customer Name
              '18%', // Address (WIDE)
              '8%', // Billing City
              '5%', // State
              '6%', // Zip
              '8%', // Contact Name
              '20%', // Email (WIDEST)
              '8%', // Phone
              '5%', // Fax
              '12%' // Website
            ];

            /* ========= FORCE WORD WRAP ========= */
            body.forEach(function(row, rowIndex) {
              row.forEach(function(cell, colIndex) {

                // Header row skip styling
                if (rowIndex === 0) {
                  cell.alignment = 'left';
                  return;
                }

                if (typeof cell.text === 'string') {
                  cell.text = cell.text
                    .replace(/(.{30})/g, '$1\n'); // ⬅️ break every 30 chars
                }

                cell.alignment = 'left';
                cell.noWrap = false;
              });
            });



          }
        }

      ]
    });

    /* ===============================
       CUSTOM TOP SEARCH WORKING
    =============================== */

    // Typing search (live)
    $('#customerSearch').on('keyup', function () {
        table.search(this.value).draw();
    });

    // Button click search
    $('#customerSearchBtn').on('click', function () {
        table.search($('#customerSearch').val()).draw();
    });

  });
</script>