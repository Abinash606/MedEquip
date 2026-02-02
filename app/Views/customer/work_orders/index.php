<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="mb-4">Work Orders</h1>
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