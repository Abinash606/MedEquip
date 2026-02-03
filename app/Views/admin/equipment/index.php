<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Equipment</h1>
    <a href="#" class="btn btn-primary">Add Equipment</a>
</div>
<table class="table table-striped data-table">
    <thead>
        <tr>
            <th>Asset Tag</th>
            <th>Make</th>
            <th>Model</th>
            <th>Serial</th>
            <th>Type</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($equipment as $item): ?>
            <tr>
                <td><?= esc($item['asset_tag']) ?></td>
                <td><?= esc($item['make'] ?? '') ?></td>
                <td><?= esc($item['model'] ?? '') ?></td>
                <td><?= esc($item['serial_number'] ?? '') ?></td>
                <td><?= esc($item['device_type'] ?? '') ?></td>
                <td><?= esc($item['status'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Add/Edit Equipment Modal -->
<div class="modal fade" id="equipmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="equipmentForm">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="eq-id">

        <div class="modal-header">
          <h5 class="modal-title fw-bold">Add/Edit Equipment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Asset Tag</label>
              <input type="text" name="asset_tag" id="eq-asset_tag" class="form-control" placeholder="Auto-generated if empty">
            </div>
            <div class="col-md-6">
              <label class="form-label">Serial Number</label>
              <input type="text" name="serial_number" id="eq-serial_number" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Make</label>
              <input type="text" name="make" id="eq-make" class="form-control" placeholder="e.g., Philips, GE">
            </div>
            <div class="col-md-6">
              <label class="form-label">Model</label>
              <input type="text" name="model" id="eq-model" class="form-control" placeholder="Enter model">
            </div>

            <div class="col-md-6">
              <label class="form-label">Device Type</label>
              <input type="text" name="device_type" id="eq-device_type" class="form-control" placeholder="e.g., MRI, CT, Ultrasound">
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <input type="text" name="department" id="eq-department" class="form-control" placeholder="e.g., Radiology">
            </div>

            <div class="col-md-6">
              <label class="form-label">Room/Location</label>
              <input type="text" name="location" id="eq-location" class="form-control" placeholder="e.g., Room 101">
            </div>

            <div class="col-md-6">
              <label class="form-label">Device Status</label>
              <select name="status" id="eq-status" class="form-select" required>
                <option value="Ready">Ready</option>
                <option value="In Service">In Service</option>
                <option value="Needs Repair">Needs Repair</option>
                <option value="Out of Service">Out of Service</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">PM Kit</label>
              <select name="pm_kit" id="eq-pm_kit" class="form-select">
                <option value="">Select PM Kit</option>
                <option value="Basic Kit">Basic Kit</option>
                <option value="Full Kit">Full Kit</option>
                <option value="Calibration Only">Calibration Only</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Fast Notes</label>
              <input type="text" name="fast_notes" id="eq-fast_notes" class="form-control" placeholder="Short note for fast entry">
            </div>

            <div class="col-12">
              <label class="form-label">Customer Location</label>
              <select name="site_id" id="eq-site_id" class="form-select" required>
                <option value="">Select Location</option>
                <?php foreach ($sites as $s): ?>
                  <option value="<?= (int)$s['id'] ?>"><?= esc($s['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Installation Date</label>
              <input type="date" name="installation_date" id="eq-installation_date" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Warranty Expires</label>
              <input type="date" name="warranty_expires" id="eq-warranty_expires" class="form-control">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?= $this->endSection() ?>