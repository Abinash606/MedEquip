<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<h3 class="fw-bold mb-4">System Settings</h3>
        <div class="glass-card">
            <div class="row">
                <div class="col-md-3 border-end">
                    <div class="list-group list-group-flush" id="settings-nav">
                        <a href="#" class="list-group-item list-group-item-action active" data-target="generalSettings">General</a>
    <a href="#" class="list-group-item list-group-item-action" data-target="adminSettings">Admins</a>
                        <a href="#" class="list-group-item list-group-item-action" data-target="notificationSettings">Notifications</a>
                    </div>
                </div>
                <div class="col-md-9 p-4">
                    <!-- General settings pane -->
                    <div id="generalSettings" class="settings-pane">
                        <h5 class="fw-bold mb-3">General Configuration</h5>
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" class="form-control" value="MedEquip Services Inc.">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Time Zone</label>
                                <select class="form-select">
                                    <option>Eastern Standard Time (EST)</option>
                                    <option>Central Standard Time (CST)</option>
                                    <option>Pacific Standard Time (PST)</option>
                                </select>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="maintenanceMode" checked>
                                <label class="form-check-label" for="maintenanceMode">Enable Maintenance Mode</label>
                            </div>
                            <button type="button" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                    <!-- Admin settings pane -->
                    <div id="adminSettings" class="settings-pane d-none">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Admins</h5>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#adminModal"><i class="fa-solid fa-user-plus me-2"></i> Add Admin</button>
        </div>
        <div class="glass-card">
            <table id="admins-datatable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
                    </div>
                    <!-- Notifications pane -->
                    <div id="notificationSettings" class="settings-pane d-none">
                        <h5 class="fw-bold mb-3">Notification Preferences</h5>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                            <label class="form-check-label" for="emailNotifications">Email Notifications</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="smsNotifications">
                            <label class="form-check-label" for="smsNotifications">SMS Notifications</label>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="pushNotifications">
                            <label class="form-check-label" for="pushNotifications">Push Notifications</label>
                        </div>
                        <button type="button" class="btn btn-primary">Save Preferences</button>
                    </div>
                </div>
            </div>
        </div>
	
	
<!-- Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="admin-username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="admin-username">
                    </div>
                    <div class="mb-3">
                        <label for="admin-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="admin-email">
                    </div>
                    <div class="mb-3">
                        <label for="admin-role" class="form-label">Role</label>
                        <select class="form-select" id="admin-role">
                            <option>Super Admin</option>
                            <option>Admin</option>
                            <option>Viewer</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
 // Settings navigation pane switching
    document.addEventListener('DOMContentLoaded', function() {
      var settingsLinks = document.querySelectorAll('#settings-nav a');
      settingsLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
          event.preventDefault();
          // remove active state from all nav links
          settingsLinks.forEach(function(l) { l.classList.remove('active'); });
          // hide all panes
          document.querySelectorAll('.settings-pane').forEach(function(pane) { pane.classList.add('d-none'); });
          // activate clicked link
          this.classList.add('active');
          // show targeted pane
          var targetId = this.getAttribute('data-target');
          if (targetId) {
            var targetPane = document.getElementById(targetId);
            if (targetPane) {
              targetPane.classList.remove('d-none');
            }
          }
        });
      });
    });
</script>	
<?= $this->endSection() ?>

