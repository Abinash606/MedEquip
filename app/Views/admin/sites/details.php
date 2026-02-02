<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<button class="btn btn-secondary mb-3" id="back-to-sites">Back to Sites</button>
        
        <div class="glass-card mb-4">
            <div class="row align-items-center">
                <div class="col-md-auto me-4">
                    <img id="site-details-logo" src="https://ui-avatars.com/api/?name=S" class="rounded-circle" width="80" alt="Logo">
                </div>
                <div class="col-md">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Site Name:</strong> <span id="site-details-name"></span></p>
                            <p><strong>Site ID:</strong> <span id="site-details-id"></span></p>
                            <p><strong>Site Contact Name:</strong> <span id="site-details-contact-name"></span></p>
                            <p><strong>Site Email:</strong> <span id="site-details-email"></span></p>
                            <p><strong>Site Phone Number:</strong> <span id="site-details-phone"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Customer Name:</strong> <span id="site-details-customer-name"></span></p>
                            <p><strong>Site Address:</strong> <span id="site-details-address"></span></p>
                            <p><strong>State:</strong> <span id="site-details-state"></span></p>
                            <p><strong>Zip code:</strong> <span id="site-details-zip"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs" id="site-details-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#equipment" type="button" role="tab" aria-controls="equipment" aria-selected="true">Equipment</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inspections-tab" data-bs-toggle="tab" data-bs-target="#inspections" type="button" role="tab" aria-controls="inspections" aria-selected="false">Inspections</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="work-orders-tab" data-bs-toggle="tab" data-bs-target="#work-orders" type="button" role="tab" aria-controls="work-orders" aria-selected="false">Work Orders</button>
            </li>
        </ul>
        <div class="tab-content" id="site-details-tabs-content">
            <div class="tab-pane fade show active" id="equipment" role="tabpanel" aria-labelledby="equipment-tab">
                <div class="glass-card">
                    <table id="equipment-datatable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Asset Tag</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Serial Number</th>
                                <th>Device Type</th>
                                <th>Location or Room</th>
                                <th>Department</th>
                                <th>Device Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="inspections" role="tabpanel" aria-labelledby="inspections-tab">
                <div class="glass-card">
                    <table id="inspections-datatable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Inspection ID</th>
                                <th>Date</th>
                                <th>Technician</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="work-orders" role="tabpanel" aria-labelledby="work-orders-tab">
                <div class="glass-card">
                    <table id="work-orders-datatable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Work Order ID</th>
                                <th>Date</th>
                                <th>Technician</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
<?= $this->endSection() ?>
