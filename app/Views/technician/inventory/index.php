<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 topbar">
    <div>
        <h3 class="fw-bold mb-0">Inventory</h3>
        <p class="text-muted small mb-0">View-only parts and supplies inventory.</p>
    </div>
</div>

<div class="content">
<div class="glass-card p-3">
    <div class="table-responsive">
        <table id="inventoryTable" class="table table-hover service-table align-middle" style="width:100%">
            <thead>
                <tr>
                    <th>Part #</th>
                    <th>Description</th>
                    <th>Bin</th>
                    <th>Aisle / Row</th>
                    <th>Shelf</th>
                    <th>Qty</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory as $item): ?>
                <tr>
                    <td><span class="t-pill"><?= esc($item['part_number'] ?? '—') ?></span></td>
                    <td class="fw-medium"><?= esc($item['part_description'] ?? '—') ?></td>
                    <td><?= esc($item['bin'] ?? '—') ?></td>
                    <td><?= esc($item['row_aisle'] ?? '—') ?></td>
                    <td><?= esc($item['shelf'] ?? '—') ?></td>
                    <td>
                        <?php
                            $qty = (int)($item['qty'] ?? 0);
                            $qClass = $qty === 0 ? 'bg-danger' : ($qty <= 5 ? 'bg-warning' : 'bg-success');
                        ?>
                        <span class="badge <?= $qClass ?>"><?= esc($qty) ?></span>
                    </td>
                    <td class="text-muted small">
                        <?= !empty($item['total_value']) ? '$' . number_format((float)$item['total_value'], 2) : '—' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($inventory)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No inventory records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
$(function() {
    $('#inventoryTable').DataTable({ pageLength: 25, order: [[1, 'asc']] });
});
</script>
<?= $this->endSection() ?>
