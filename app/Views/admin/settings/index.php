<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class=" mb-4 topbar">
  <h3 class="fw-bold mb-4">System Settings</h3>
</div>
<div class="content">
  <div class="glass-card p-3">
    <div class="row">
      <div class="col-md-3 border-end">
        <div class="list-group list-group-flush" id="settings-nav">
          <a href="#" class="list-group-item list-group-item-action active"
            data-target="generalSettings">General</a>
          <a href="#" class="list-group-item list-group-item-action" data-target="adminSettings">Admins</a>
          <a href="#" class="list-group-item list-group-item-action"
            data-target="notificationSettings">Notifications</a>
          <a href="#" class="list-group-item list-group-item-action" data-target="iqNotesSettings">IQ
            Notes</a>
          <a href="#" class="list-group-item list-group-item-action" data-target="equipmentSettings">Equipment
            Setting</a>
        </div>
      </div>

      <div class="col-md-9 p-4">

        <!-- General settings pane -->
        <div id="generalSettings" class="settings-pane">
          <h5 class="fw-bold mb-3">General Configuration</h5>

          <form id="generalForm">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label class="form-label">Company Name</label>
              <input type="text" name="company_name" class="form-control"
                value="<?= esc($settings['company_name'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Time Zone</label>
              <select name="time_zone" class="form-select">
                <?php
                $tz = $settings['time_zone'] ?? 'Asia/Kolkata';
                $timezones = [
                  'Asia/Kolkata',
                  'America/Chicago',
                  'America/New_York',
                  'America/Los_Angeles',
                  'UTC'
                ];
                ?>
                <?php foreach ($timezones as $z): ?>
                  <option value="<?= esc($z) ?>" <?= $tz === $z ? 'selected' : '' ?>>
                    <?= esc($z) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" id="maintenanceMode" name="maintenance_mode"
                value="1" <?= !empty($settings['maintenance_mode']) ? 'checked' : '' ?>>
              <label class="form-check-label" for="maintenanceMode">Enable Maintenance Mode</label>
            </div>

            <button type="submit" class="btn btn-primary" id="btnSaveGeneral">Save Changes</button>
          </form>
        </div>

        <!-- Admin settings pane -->
        <div id="adminSettings" class="settings-pane d-none">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Admins</h5>
            <button class="btn btn-primary shadow-sm" type="button" id="btnAddAdmin" data-bs-toggle="modal"
              data-bs-target="#adminModal">
              <i class="fa-solid fa-user-plus me-2"></i> Add Admin
            </button>
          </div>

          <div class="glass-card p-3">
            <div class="table-responsive">
              <table id="admins-datatable" class="display" style="width:100%">
                <thead>
                  <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

        <!-- Notifications settings pane -->
        <div id="notificationSettings" class="settings-pane d-none">
          <h5 class="fw-bold mb-3">Notification Preferences</h5>

          <form id="notifyForm">
            <?= csrf_field() ?>

            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" id="emailNotifications"
                name="email_notifications" value="1"
                <?= !empty($settings['email_notifications']) ? 'checked' : '' ?>>
              <label class="form-check-label" for="emailNotifications">Email Notifications</label>
            </div>

            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" id="smsNotifications"
                name="sms_notifications" value="1"
                <?= !empty($settings['sms_notifications']) ? 'checked' : '' ?>>
              <label class="form-check-label" for="smsNotifications">SMS Notifications</label>
            </div>

            <div class="form-check form-switch mb-4">
              <input class="form-check-input" type="checkbox" id="pushNotifications"
                name="push_notifications" value="1"
                <?= !empty($settings['push_notifications']) ? 'checked' : '' ?>>
              <label class="form-check-label" for="pushNotifications">Push Notifications</label>
            </div>

            <button type="submit" class="btn btn-primary" id="btnSaveNotify">Save Preferences</button>
          </form>
        </div>

        <!-- IQ Notes pane -->
        <div id="iqNotesSettings" class="settings-pane d-none">

          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">IQ Notes</h5>
            <button class="btn btn-primary btn-sm" id="btnAddIqNote">
              <i class="fa fa-plus"></i> Add Note
            </button>
          </div>

          <div class="glass-card mb-3 p-3">
            <div class="table-responsive">
              <table class="table table-sm w-100" id="iqNotesTable">
                <thead>
                  <tr>
                    <th>Note</th>
                    <th width="120">Action</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>

        </div>

        <!-- Equipment Settings -->
        <div id="equipmentSettings" class="settings-pane d-none">
          <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div>
                <h5 class="fw-bold mb-0">Equipment Inventory</h5>
                <small class="text-muted">Dynamically populated from Equipment DB. Toggle EST &amp; CAL
                  flags per device row.</small>
              </div>
              <!-- <button class="btn btn-primary btn-sm" id="btnAddEquipment">
              <i class="fas fa-plus"></i> Add Equipment
            </button> -->
            </div>
            <div class="glass-card mb-3 p-3 mt-3">
              <div class="table-responsive">
                <table id="equipment-datatable" class="table  table-hover w-100">
                  <thead>
                    <tr>
                      <th>Description</th>
                      <th class="text-center" style="width:80px;">
                        EST
                        <div class="form-check d-flex justify-content-center m-0 mt-1">
                          <input class="form-check-input" type="checkbox" id="selectAllEst"
                            title="Select All EST"
                            style="cursor:pointer;width:1.2em;height:1.2em;">
                        </div>
                      </th>
                      <th class="text-center" style="width:80px;">
                        CAL
                        <div class="form-check d-flex justify-content-center m-0 mt-1">
                          <input class="form-check-input" type="checkbox" id="selectAllCal"
                            title="Select All CAL"
                            style="cursor:pointer;width:1.2em;height:1.2em;">
                        </div>
                      </th>
                      <th class="text-center" style="width:100px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="equipmentTable"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
  </div>
</div>

<!-- Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="adminForm">
        <?= csrf_field() ?>

        <div class="modal-header">
          <h5 class="modal-title" id="adminModalTitle">Add Admin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="admin-id">

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="full_name" id="admin-username">
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="admin-email">
          </div>

          <div class="mb-3">
            <label class="form-label">Role</label>
            <select class="form-select" name="role_id" id="admin-role">
              <option value="1">Super Admin</option>
              <option value="2">Admin</option>
              <option value="3">Viewer</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" id="admin-status">
              <option value="active">active</option>
              <option value="inactive">inactive</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">
              Password <small class="text-muted">(min 6 characters)</small>
              <br><small class="text-muted">(Leave blank while Edit to keep same password)</small>
            </label>
            <input type="password" class="form-control" name="password" id="admin-password">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="btnSaveAdmin">Save changes</button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- IQ Notes Modal -->
<div class="modal fade" id="iqNoteModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="iqNoteForm">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="iq-note-id">

        <div class="modal-header">
          <h5 class="modal-title">IQ Note</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Note</label>
            <textarea name="note" id="iq-note-text" class="form-control" rows="4" required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Save</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Equipment Setting Edit Modal (EST/CAL only — description comes from Equipment DB) -->
<div class="modal fade" id="equipmentModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="equipmentForm">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="equipment-id">

        <div class="modal-header">
          <h5 class="modal-title">Edit Equipment Flags</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <p class="mb-3">
            <strong id="equipment-label-make"></strong>
            &mdash; <span id="equipment-label-model"></span>
            &mdash; <em class="text-muted" id="equipment-label-type"></em>
          </p>

          <div class="form-check form-switch mb-3">
            <input type="checkbox" class="form-check-input" name="est" id="equipment-est" role="switch">
            <label class="form-check-label fw-semibold" for="equipment-est">EST (Electrical Safety
              Test)</label>
          </div>

          <div class="form-check form-switch">
            <input type="checkbox" class="form-check-input" name="cal" id="equipment-cal" role="switch">
            <label class="form-check-label fw-semibold" for="equipment-cal">CAL (Calibration)</label>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>



<script>
  $(function() {

    const hash = window.location.hash.replace('#', '');

    if (hash) {
      activateTab(hash);
    } else {
      activateTab('generalSettings'); // default
    }


    // ---------------------------------------
    // SweetAlert helper
    // ---------------------------------------
    function swalSuccess(msg) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: msg || 'Done'
      });
    }

    function swalError(msg) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: msg || 'Something went wrong'
      });
    }


    $('#settings-nav a').on('click', function(e) {
      e.preventDefault();

      const targetId = $(this).data('target');

      history.pushState(null, '', '#' + targetId);

      activateTab(targetId);
    });

    function activateTab(targetId) {
      $('#settings-nav a').removeClass('active');
      $('.settings-pane').addClass('d-none');

      $(`#settings-nav a[data-target="${targetId}"]`).addClass('active');
      $('#' + targetId).removeClass('d-none');

      // ✅ Ensure IQ Notes always loads
      // if (targetId === 'iqNotesSettings') {
      //   loadIqNotes();
      // }
    }

    // ---------------------------------------
    // General Form Validation + Submit
    // ---------------------------------------
    $("#generalForm").validate({
      rules: {
        company_name: {
          required: true,
          minlength: 2
        },
        time_zone: {
          required: true
        }
      },
      messages: {
        company_name: {
          required: "Company name is required"
        },
        time_zone: {
          required: "Please select a time zone"
        }
      },
      errorClass: "text-danger small",
      submitHandler: function(form) {

        $('#btnSaveGeneral').prop('disabled', true).text('Saving...');

        $.ajax({
          url: "<?= site_url('admin/settings/update') ?>",
          type: "POST",
          data: $(form).serialize(),
          dataType: "json",
          success: function(res) {
            swalSuccess(res.message || 'General settings updated');
          },
          error: function(xhr) {
            let msg = 'Failed to update settings';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr
              .responseJSON.message;
            swalError(msg);
          },
          complete: function() {
            $('#btnSaveGeneral').prop('disabled', false).text('Save Changes');
          }
        });

        return false;
      }
    });

    // ---------------------------------------
    // Notifications Form Validation + Submit
    // (No required fields, but we keep structure)
    // ---------------------------------------
    $("#notifyForm").validate({
      submitHandler: function(form) {

        $('#btnSaveNotify').prop('disabled', true).text('Saving...');

        // Merge general + notify (so controller gets all columns)
        const merged = $("#generalForm").serialize() + '&' + $(form).serialize();

        $.ajax({
          url: "<?= site_url('admin/settings/update') ?>",
          type: "POST",
          data: merged,
          dataType: "json",
          success: function(res) {
            swalSuccess(res.message || 'Notification settings updated');
          },
          error: function(xhr) {
            let msg = 'Failed to update notifications';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr
              .responseJSON.message;
            swalError(msg);
          },
          complete: function() {
            $('#btnSaveNotify').prop('disabled', false).text(
              'Save Preferences');
          }
        });

        return false;
      }
    });

    // ---------------------------------------
    // Admins DataTable
    // ---------------------------------------
    const adminTable = $('#admins-datatable').DataTable({
      ajax: "<?= site_url('admin/settings/admins/list') ?>",
      columns: [{
          data: 'username'
        },
        {
          data: 'email'
        },
        {
          data: 'role'
        },
        {
          data: 'status'
        },
        {
          data: null,
          orderable: false,
          render: function(row) {
            return `
            <div class="action-btns">
            <button type="button" class="btn btn-sm btn-primary btn-edit-admin" data-id="${row.id}">Edit</button>
            <button type="button" class="btn btn-sm btn-danger btn-del-admin" data-id="${row.id}">Delete</button>
            </div>
          `;
          }
        }
      ]
    });

    // ---------------------------------------
    // Admin Modal: Add
    // ---------------------------------------
    $('#btnAddAdmin').on('click', function() {
      $('#adminModalTitle').text('Add Admin');
      $('#adminForm')[0].reset();
      $('#admin-id').val('');
      $("#adminForm").validate().resetForm();
      $("#adminForm .is-invalid, #adminForm .is-valid").removeClass("is-invalid is-valid");
    });

    // ---------------------------------------
    // Admin Modal: Edit
    // ---------------------------------------
    $(document).on('click', '.btn-edit-admin', function() {
      const id = $(this).data('id');

      $.get("<?= site_url('admin/settings/admins') ?>/" + id, function(res) {
        if (res.status === 'success') {
          $('#adminModalTitle').text('Edit Admin');
          $('#admin-id').val(res.data.id);
          $('#admin-username').val(res.data.full_name);
          $('#admin-email').val(res.data.email);
          $('#admin-role').val(res.data.role_id);
          $('#admin-status').val(res.data.status);
          $('#admin-password').val('');
          $("#adminForm").validate().resetForm();
          $("#adminForm .is-invalid, #adminForm .is-valid").removeClass(
            "is-invalid is-valid");
          $('#adminModal').modal('show');
        } else {
          swalError('Admin not found');
        }
      }, 'json').fail(function() {
        swalError('Failed to load admin details');
      });
    });

    // ---------------------------------------
    // Admin Form Validation + Save (Add/Edit)
    // - password required only on Add
    // ---------------------------------------
    $("#adminForm").validate({
      rules: {
        full_name: {
          required: true,
          minlength: 2
        },
        email: {
          required: true,
          email: true
        },
        role_id: {
          required: true
        },
        status: {
          required: true
        },
        password: {
          minlength: 6,
          required: function() {
            return ($("#admin-id").val() === ""); // required only when adding
          }
        }
      },
      messages: {
        full_name: {
          required: "Username is required"
        },
        email: {
          required: "Email is required",
          email: "Enter valid email"
        },
        password: {
          required: "Password is required for new admin",
          minlength: "Minimum 6 characters"
        }
      },
      errorClass: "text-danger small",
      highlight: function(element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
      },
      unhighlight: function(element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
      },

      submitHandler: function(form) {

        $('#btnSaveAdmin').prop('disabled', true).text('Saving...');

        $.ajax({
          url: "<?= site_url('admin/settings/admins/save') ?>",
          type: "POST",
          data: $(form).serialize(),
          dataType: "json",
          success: function(res) {
            $('#adminModal').modal('hide');
            swalSuccess(res.message || 'Admin saved');
            adminTable.ajax.reload(null, false);
          },
          error: function(xhr) {
            let msg = 'Failed to save admin';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr
              .responseJSON.message;
            swalError(msg);
          },
          complete: function() {
            $('#btnSaveAdmin').prop('disabled', false).text('Save changes');
          }
        });

        return false;
      }
    });

    // ---------------------------------------
    // Delete Admin with SweetAlert confirm
    // ---------------------------------------
    $(document).on('click', '.btn-del-admin', function() {
      const id = $(this).data('id');

      Swal.fire({
        icon: 'warning',
        title: 'Delete admin?',
        text: 'This cannot be undone.',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
      }).then((r) => {
        if (!r.isConfirmed) return;

        $.ajax({
          url: "<?= site_url('admin/settings/admins/delete') ?>/" + id,
          type: "DELETE",
          dataType: "json",
          success: function(res) {
            swalSuccess(res.message || 'Admin deleted');
            adminTable.ajax.reload(null, false);
          },
          error: function(xhr) {
            let msg = 'Delete failed';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr
              .responseJSON.message;
            swalError(msg);
          }
        });
      });
    });



    // ---------------------------------------
    // IQ NOTES (MUST BE INSIDE document ready)
    // ---------------------------------------

    // function loadIqNotes() {
    //   $.ajax({
    //     url: "<?= site_url('admin/settings/iq-notes') ?>",
    //     type: "GET",
    //     dataType: "json",
    //     success: function(res) {
    //       let rows = '';
    //       if (res.data && res.data.length) {
    //         res.data.forEach(r => {
    //           rows += `
    //         <tr>
    //           <td>${r.note}</td>
    //           <td>
    //             <button class="btn btn-sm btn-outline-secondary edit-note" data-id="${r.id}">Edit</button>
    //             <button class="btn btn-sm btn-outline-danger del-note" data-id="${r.id}">Delete</button>
    //           </td>
    //         </tr>
    //       `;
    //         });
    //       }
    //       $('#iqNotesTable tbody').html(rows);
    //     }
    //   });
    // }

    // ---------------------------------------
    // IQ NOTES DATATABLE
    // ---------------------------------------

    const iqTable = $('#iqNotesTable').DataTable({
      ajax: {
        url: "<?= site_url('admin/settings/iq-notes') ?>",
        dataSrc: 'data'
      },
      pageLength: 10,
      columns: [{
          data: 'note'
        },
        {
          data: null,
          orderable: false,
          render: function(row) {
            return `
            <div class="action-btns">
          <button class="btn btn-sm btn-primary edit-note" data-id="${row.id}">Edit</button>
          <button class="btn btn-sm btn-danger del-note" data-id="${row.id}">Delete</button>
          </div>
        `;
          }
        }
      ]
    });


    // OPEN MODAL
    $('#btnAddIqNote').on('click', function() {
      $('#iqNoteForm')[0].reset();
      $('#iq-note-id').val('');
      $('#iqNoteModal').modal('show');
    });

    // SAVE (ADD / EDIT)
    $('#iqNoteForm').on('submit', function(e) {
      e.preventDefault();

      $.ajax({
        url: "<?= site_url('admin/settings/iq-notes/save') ?>",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function(res) {
          $('#iqNoteModal').modal('hide'); // ✅ FIX 1
          swalSuccess(res.message || 'Saved');
          iqTable.ajax.reload(null, false);

        },
        error: function() {
          swalError('Save failed');
        }
      });
    });

    // EDIT
    $(document).on('click', '.edit-note', function() {
      const id = $(this).data('id');

      $.get("<?= site_url('admin/settings/iq-notes') ?>/" + id, function(res) {
        $('#iq-note-id').val(res.data.id);
        $('#iq-note-text').val(res.data.note);
        $('#iqNoteModal').modal('show');
      }, 'json');
    });

    // DELETE
    $(document).on('click', '.del-note', function() {
      const id = $(this).data('id');

      Swal.fire({
        icon: 'warning',
        title: 'Delete note?',
        showCancelButton: true
      }).then(r => {
        if (!r.isConfirmed) return;

        $.ajax({
          url: "<?= site_url('admin/settings/iq-notes/delete') ?>",
          type: "POST",
          data: {
            id: id,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
          },
          dataType: "json",
          success: function(res) {
            swalSuccess(res.message);
            iqTable.ajax.reload(null, false);

          },
          error: function() {
            swalError('Delete failed');
          }
        });

      });
    });

    // TAB CLICK → LOAD DATA
    // $('a[data-target="iqNotesSettings"]').on('click', function() {
    //   loadIqNotes();
    // });


    // ---------------------------------------
    // Equipment DataTable
    // ---------------------------------------

    // ── Equipment Setting DataTable ────────────────────────────────────────
    // Columns: Make | Model | Device Type | EST (toggle) | CAL (toggle) | Actions
    const EQUIP_TOGGLE_URL = "<?= site_url('admin/settings/equipment/toggle') ?>";
    const EQUIP_SAVE_URL = "<?= site_url('admin/settings/equipment/save') ?>";
    const EQUIP_DEL_URL = "<?= site_url('admin/settings/equipment/delete') ?>";

    const equipmentTable = $('#equipment-datatable').DataTable({
      ajax: {
        url: "<?= site_url('admin/settings/equipment') ?>",
        dataSrc: 'data'
      },
      columns: [{
          data: null,
          render: function(row) {
            const make = row.make || '';
            const model = row.model || '';
            const dtype = row.device_type || '';
            const parts = [make, model, dtype].filter(p => p !== '');
            return parts.join(' — ') || '<span class="text-muted">—</span>';
          }
        },
        {
          data: 'est',
          className: 'text-center',
          orderable: false,
          render: function(data, type, row) {
            return `<div class="form-check d-flex justify-content-center m-0">
              <input class="form-check-input equip-cb" type="checkbox"
                data-id="${row.id}" data-field="est"
                style="cursor:pointer;width:1.2em;height:1.2em;"
                ${data == 1 ? 'checked' : ''}>
            </div>`;
          }
        },
        {
          data: 'cal',
          className: 'text-center',
          orderable: false,
          render: function(data, type, row) {
            return `<div class="form-check d-flex justify-content-center m-0">
              <input class="form-check-input equip-cb" type="checkbox"
                data-id="${row.id}" data-field="cal"
                style="cursor:pointer;width:1.2em;height:1.2em;"
                ${data == 1 ? 'checked' : ''}>
            </div>`;
          }
        },
        {
          data: null,
          orderable: false,
          className: 'text-center',
          render: function(row) {
            const make = (row.make || '').replace(/"/g, '&quot;');
            const model = (row.model || '').replace(/"/g, '&quot;');
            const dtype = (row.device_type || '').replace(/"/g, '&quot;');
            return `
              <button class="btn btn-sm btn-outline-primary edit-equipment me-1"
                title="Edit EST/CAL"
                data-id="${row.id}" data-make="${make}"
                data-model="${model}" data-device_type="${dtype}"
                data-est="${row.est}" data-cal="${row.cal}">
            <i class="fas fa-edit"></i>
          </button>
              <button class="btn btn-sm btn-outline-warning del-equipment"
                title="Reset EST &amp; CAL to No"
            data-id="${row.id}">
                <i class="fas fa-redo-alt"></i>
              </button>`;
          }
        }
      ]
    });

    // Inline checkbox toggle
    $(document).on('change', '.equip-cb', function() {
      const $cb = $(this);
      const id = $cb.data('id');
      const field = $cb.data('field');
      const value = this.checked ? 1 : 0;
      const label = field.toUpperCase();
      $.post(EQUIP_TOGGLE_URL, {
          id,
          field,
          value,
          '<?= csrf_token() ?>': $('input[name="<?= csrf_token() ?>"]').first().val()
        },
        function(res) {
          if (res.status === 'success') {
            swalSuccess(label + (value ? ' enabled' : ' disabled') + ' successfully');
          } else {
            $cb.prop('checked', !$cb.prop('checked'));
            swalError(res.message || 'Toggle failed');
          }
        },
        'json'
      ).fail(function() {
        $cb.prop('checked', !$cb.prop('checked'));
        swalError('Connection error');
      });
    });

    // Open edit modal
    $(document).on('click', '.edit-equipment', function() {
      const $btn = $(this);
      $('#equipment-id').val($btn.data('id'));
      $('#equipment-label-make').text($btn.data('make') || '—');
      $('#equipment-label-model').text($btn.data('model') || '—');
      $('#equipment-label-type').text($btn.data('device_type') || '—');
      $('#equipment-est').prop('checked', $btn.data('est') == 1);
      $('#equipment-cal').prop('checked', $btn.data('cal') == 1);
      $('#equipmentModal').modal('show');
    });

    // Save modal
    $('#equipmentForm').on('submit', function(e) {
      e.preventDefault();
      const id = $('#equipment-id').val();
      const est = $('#equipment-est').is(':checked') ? 1 : 0;
      const cal = $('#equipment-cal').is(':checked') ? 1 : 0;
      $.post(EQUIP_SAVE_URL, {
          id,
          est,
          cal,
          '<?= csrf_token() ?>': $('input[name="<?= csrf_token() ?>"]').first().val()
        },
        function(res) {
          $('#equipmentModal').modal('hide');
          swalSuccess(res.message || 'Saved');
          equipmentTable.ajax.reload(null, false);
        }, 'json'
      ).fail(function() {
        swalError('Save failed');
      });
    });
    // ---------------------------------------
    // Select All EST / CAL with confirmation
    // ---------------------------------------
    function handleSelectAll(field, $masterCb) {
      const checked = $masterCb.is(':checked');
      const label = field.toUpperCase();
      const action = checked ? 'enable' : 'disable';

      Swal.fire({
        icon: 'warning',
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} all ${label}?`,
        text: `This will ${action} ${label} for ALL equipment records in the current dataset.`,
        showCancelButton: true,
        confirmButtonText: `Yes, ${action} all`,
        cancelButtonText: 'Cancel'
      }).then(function(r) {
        if (!r.isConfirmed) {
          $masterCb.prop('checked', !checked); // revert
          return;
        }

        // Collect all visible row IDs from DataTable
        const ids = equipmentTable.rows({
          search: 'applied'
        }).data().toArray().map(r => r.id);
        const value = checked ? 1 : 0;
        let done = 0,
          failed = 0;

        if (!ids.length) return;

        ids.forEach(function(id) {
          $.post(EQUIP_TOGGLE_URL, {
              id,
              field,
              value,
              '<?= csrf_token() ?>': $('input[name="<?= csrf_token() ?>"]')
                .first().val()
            },
            function(res) {
              if (res.status !== 'success') failed++;
              done++;
              if (done === ids.length) {
                equipmentTable.ajax.reload(null, false);
                if (failed === 0) {
                  swalSuccess(`${label} ${action}d for all ${done} records`);
                } else {
                  swalError(`${failed} record(s) failed to update`);
                }
              }
            }, 'json'
          ).fail(function() {
            failed++;
            done++;
            if (done === ids.length) {
              equipmentTable.ajax.reload(null, false);
              swalError(`${failed} record(s) failed to update`);
            }
          });
        });
      });
    }

    $('#selectAllEst').on('change', function() {
      handleSelectAll('est', $(this));
    });
    $('#selectAllCal').on('change', function() {
      handleSelectAll('cal', $(this));
    });

    // Keep master checkboxes in sync after table redraws
    equipmentTable.on('draw', function() {
      ['est', 'cal'].forEach(function(field) {
        const $master = $('#selectAll' + field.charAt(0).toUpperCase() + field.slice(1));
        const $rows = $('#equipment-datatable .equip-cb[data-field="' + field + '"]');
        const allChecked = $rows.length > 0 && $rows.filter(':checked').length === $rows
          .length;
        $master.prop('checked', allChecked);
      });
    });
    // Reset (soft-delete) — sets EST=0, CAL=0, does NOT delete the record
    $(document).on('click', '.del-equipment', function() {
      const id = $(this).data('id');
      Swal.fire({
        icon: 'warning',
        title: 'Reset flags?',
        text: 'EST and CAL will be set to No. The record will not be deleted.',
        showCancelButton: true,
        confirmButtonText: 'Yes, Reset',
        confirmButtonColor: '#e67e22',
        cancelButtonColor: '#6c757d'
      }).then(function(r) {
        if (!r.isConfirmed) return;
        $.ajax({
          url: EQUIP_DEL_URL + '/' + id,
          type: 'DELETE',
          success: function(res) {
            swalSuccess(res.message || 'Flags reset');
            equipmentTable.ajax.reload(null, false);
          },
          error: function() {
            swalError('Reset failed');
          }
        });
      });
    });

  });
</script>

<?= $this->endSection() ?>