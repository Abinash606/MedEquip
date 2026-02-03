<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>
<!-- Service History view -->
    <section id="serviceHistory" class="view-section active">
        <div class="row g-4">
            <div class="col-12">
                <div class="glass-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Open Work Orders</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#workOrderModal"><i class="fa-solid fa-plus me-2"></i> Add Work Order</button>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold">Repair Autoclave</div>
                                <div class="text-muted small">#WO-1022 • Created: 10/20/2025</div>
                            </div>
                            <span class="badge bg-warning text-dark">In Progress</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-bold">Replace Battery Pack</div>
                                <div class="text-muted small">#WO-1023 • Created: 10/18/2025</div>
                            </div>
                            <span class="badge bg-secondary text-dark">Scheduled</span>
                        </li>
                    </ul>
                </div>
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">Recent Service History</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-bold">Maintenance Completed</div>
                                <div class="text-muted small">MRI Scanner – Radiology</div>
                            </div>
                            <span class="small text-muted">Oct&nbsp;10,&nbsp;2025</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-bold">Service Request Logged</div>
                                <div class="text-muted small">Broken Display – Bed&nbsp;4 Monitor</div>
                            </div>
                            <span class="small text-muted">Oct&nbsp;01,&nbsp;2025</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
	
	<!-- Work Order Modal -->
<div class="modal fade" id="workOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Work Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Work Order #</label><input type="text" class="form-control" readonly></div>
                        <div class="col-md-8"><label class="form-label">Customer</label><input type="text" class="form-control" readonly></div>
                        <div class="col-12"><label class="form-label">Site</label><input type="text" class="form-control" readonly></div>
                        <div class="col-md-4"><label class="form-label">Date</label><input type="date" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Asset #</label><input type="text" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Model</label><input type="text" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">S/N</label><input type="text" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Status</label><select class="form-select"><option>Open</option><option>In Progress</option><option>Completed</option><option>On Hold</option></select></div>
                        <div class="col-12"><label class="form-label">Description</label><textarea class="form-control"></textarea></div>
                        <div class="col-12"><label class="form-label">Technician</label><input type="text" class="form-control"></div>
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