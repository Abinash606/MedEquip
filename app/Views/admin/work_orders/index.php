<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Work Orders</h1>
    <a href="#" class="btn btn-primary">Add Work Order</a>
</div>
<table class="table table-striped data-table">
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