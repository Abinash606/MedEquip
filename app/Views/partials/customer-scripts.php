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

      dom: "<'row mb-3'<'col-md-6'B><'col-md-6 text-end'f>>" +
        "<'row'<'col-12'tr>>" +
        "<'row mt-3'<'col-md-6'i><'col-md-6 text-end'p>>",

      buttons: [{
          extend: 'copyHtml5',
          text: 'Copy',
          exportOptions: {
            columns: ':visible:not(:last-child)'
          }
        },
        {
          extend: 'excelHtml5',
          text: 'Excel',
          exportOptions: {
            columns: ':visible:not(:last-child)'
          }
        },
        {
          extend: 'pdfHtml5',
          text: 'PDF',
          orientation: 'landscape',
          pageSize: 'A4',
          title: 'MedEquip Customer Portal | Service Management',

          filename: function() {
            const today = new Date();
            let day = String(today.getDate()).padStart(2, '0');
            let month = String(today.getMonth() + 1).padStart(2, '0');
            let year = today.getFullYear();
            return 'Customers_' + day + month + year;
          },

          exportOptions: {
            columns: ':visible:not(:last-child)'
          },

          customize: function(doc) {

            /* ========= FONT ========= */
            doc.styles.title.fontSize = 13;
            doc.styles.tableHeader.fontSize = 9;
            doc.defaultStyle.fontSize = 8;

            /* ========= PAGE MARGIN ========= */
            doc.pageMargins = [15, 35, 15, 25];

            const table = doc.content[1].table;
            const body = table.body;
            const columnCount = body[0].length;

            /* ========= COLUMN WIDTHS ========= */
            table.widths = [
              '10%',
              '18%',
              '8%',
              '5%',
              '6%',
              '8%',
              '20%',
              '8%',
              '5%',
              '12%'
            ];

            /* ========= HEADER STYLE ========= */
            doc.styles.tableHeader = {
              bold: true,
              fontSize: 9,
              color: 'black',
              fillColor: '#a4d169',
              alignment: 'left'
            };

            /* ========= ROW COLORS ========= */
            doc.styles.tableBodyEven = {
              fillColor: '#f3f3f3'
            };
            doc.styles.tableBodyOdd = {
              fillColor: '#ffffff'
            };

            /* ========= BORDERS ========= */
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

            /* ========= WORD WRAP ========= */
            body.forEach(function(row, rowIndex) {
              row.forEach(function(cell) {

                if (rowIndex === 0) {
                  cell.alignment = 'left';
                  return;
                }

                if (typeof cell.text === 'string') {
                  cell.text = cell.text.replace(/(.{30})/g,
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

    /* ===============================
       CUSTOM TOP SEARCH WORKING
    =============================== */

    // Typing search (live)
    $('#customerSearch').on('keyup', function() {
      table.search(this.value).draw();
    });

    // Button click search
    $('#customerSearchBtn').on('click', function() {
      table.search($('#customerSearch').val()).draw();
    });

  });
</script>