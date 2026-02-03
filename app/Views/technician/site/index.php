<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>
<!-- Sites view -->
    <section id="sites" class="view-section active">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Site Directory</h3>
        </div>

        <div class="glass-card mb-4">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-solid fa-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Search for sites by name or address...">
                <button class="btn btn-outline-primary">Search</button>
            </div>
        </div>

        <div class="glass-card mb-4">
            <label for="customer-filter" class="form-label fw-bold">Filter by Customer</label>
            <select id="customer-filter" class="form-select" style="width: 25%;">
                <option value="">All Customers</option>
                <option value="Tiger Nixon">Tiger Nixon</option>
                <option value="Garrett Winters">Garrett Winters</option>
            </select>
        </div>
        <div class="glass-card">
            <table id="sites-datatable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Site Name</th>
                        <th>Customer Name</th>
                        <th>Site Address</th>
                        <th>Site Contact Name</th>
                        <th>Site Phone Number</th>
                        <th>Site Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
<?= $this->endSection() ?>