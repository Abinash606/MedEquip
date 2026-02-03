<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<a href="<?= site_url('technician/work-orders') ?>" class="btn btn-secondary mb-3">&larr; Back to Work Orders</a>
<h1 class="mb-4">Work Order #<?= esc($workOrder['id']) ?></h1>
<p><strong>Equipment:</strong> <?= esc($workOrder['equipment_id']) ?></p>
<p><strong>Title:</strong> <?= esc($workOrder['title']) ?></p>
<p><strong>Status:</strong> <?= esc($workOrder['status']) ?></p>
<p><strong>Priority:</strong> <?= esc($workOrder['priority']) ?></p>
<p><strong>Details:</strong></p>
<p><?= esc($workOrder['description']) ?></p>
<?= $this->endSection() ?>