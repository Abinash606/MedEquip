<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<a href="<?= site_url('customer/sites') ?>" class="btn btn-secondary mb-3">&larr; Back to Sites</a>
<h1 class="mb-3">Site Details</h1>
<div class="mb-4">
    <h4><?= esc($site['name']) ?></h4>
    <p><strong>Address:</strong> <?= esc($site['address'] ?? '') ?>, <?= esc($site['city'] ?? '') ?>, <?= esc($site['state'] ?? '') ?> <?= esc($site['zip'] ?? '') ?></p>
    <p><strong>Contact:</strong> <?= esc($site['contact_name'] ?? '') ?> | <?= esc($site['phone'] ?? '') ?> | <?= esc($site['email'] ?? '') ?></p>
</div>
<h2>Equipment</h2>
<table class="table table-striped data-table mb-5">
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

<h2>Inspections</h2>
<table class="table table-striped data-table mb-5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Equipment</th>
            <th>Scheduled Date</th>
            <th>Status</th>
            <th>Technician</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($inspections as $inspection): ?>
            <tr>
                <td><?= esc($inspection['id']) ?></td>
                <td><?= esc($inspection['equipment_id']) ?></td>
                <td><?= esc($inspection['scheduled_at']) ?></td>
                <td><?= esc($inspection['status']) ?></td>
                <td><?= esc($inspection['technician_id'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Work Orders</h2>
<table class="table table-striped data-table mb-5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Equipment</th>
            <th>Title</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Assigned To</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($workOrders as $wo): ?>
            <tr>
                <td><?= esc($wo['id']) ?></td>
                <td><?= esc($wo['equipment_id']) ?></td>
                <td><?= esc($wo['title']) ?></td>
                <td><?= esc($wo['status']) ?></td>
                <td><?= esc($wo['priority']) ?></td>
                <td><?= esc($wo['assigned_to'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>