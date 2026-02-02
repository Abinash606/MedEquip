<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="mb-4">My Work Orders</h1>
<table class="table table-striped data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Equipment</th>
            <th>Title</th>
            <th>Status</th>
            <th>Priority</th>
            <th></th>
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
                <td><a href="<?= site_url('technician/work-orders/' . $wo['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>