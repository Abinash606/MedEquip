<?= $this->extend('layouts/technician-header') ?>
<?= $this->section('content') ?>
<!-- Customers view -->
    <section id="customers" class="view-section active">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Customer Directory</h3>
        </div>

        <div class="glass-card mb-4">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-solid fa-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Search for customers by name or address...">
                <button class="btn btn-outline-primary">Search</button>
            </div>
        </div>

        <div class="glass-card">
            <table id="customer-datatable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Address</th>
                        <th>Billing City</th>
                        <th>State</th>
                        <th>Zip</th>
                        <th>Contact Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Fax</th>
                        <th>Website</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>
<?= $this->endSection() ?>