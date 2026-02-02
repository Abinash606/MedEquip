<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Inventory Management</h3>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#inventoryModal"><i class="fa-solid fa-plus me-2"></i> Add Item</button>
        </div>
        <div class="glass-card">
            <table id="inventory-datatable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Part #</th>
                        <th>Image</th>
                        <th>Part Description</th>
                        <th>Bin</th>
                        <th>QTY</th>
                        <th>Total Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
<!-- Inventory Modal -->
<div class="modal fade" id="inventoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Part #</label><input type="text" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Part Description</label><input type="text" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Bin</label><input type="text" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">QTY</label><input type="number" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Total Value</label><input type="text" class="form-control"></div>
                        <div class="col-12"><label class="form-label">Image</label><input type="file" class="form-control"></div>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#inventory-datatable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });

    $('.edit-inv').on('click', function() {
        const data = $(this).data();
        $('#invId').val(data.id);
        $('#invPartNumber').val(data.part);
        $('#invDescription').val(data.desc);
        $('#invBin').val(data.bin);
        $('#invQty').val(data.qty);
        $('#invCost').val(data.cost);
        
        $('#invForm').attr('action', '<?= base_url('inventory/update') ?>/' + data.id);
        $('#invModalLabel').text('Edit Inventory Item');
    });

    $('#inventoryModal').on('hidden.bs.modal', function () {
        $('#invForm').attr('action', '<?= base_url('inventory/store') ?>');
        $('#invForm')[0].reset();
        $('#invId').val('');
        $('#invModalLabel').text('Inventory Item Details');
    });
});
</script>
<?= $this->endSection() ?>
