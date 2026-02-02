<?php
/**
 * Admin Sites index view.
 *
 * Displays a list of sites for the current company and allows the super admin
 * to add new sites or edit/delete existing ones. Each site belongs to a
 * customer. The modal form includes fields for site details and selects
 * the appropriate customer. An optional query parameter `customer_id`
 * preselects the customer and automatically opens the Add Site modal.
 */
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
    // Build a lookup array of customer names keyed by ID for display
    $customerMap = [];
    foreach ($customers as $cust) {
        $customerMap[$cust['id']] = $cust['name'];
    }
?>



<div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Site Directory</h3>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#siteModal"><i class="fa-solid fa-sitemap me-2"></i> Add Site</button>
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
            <table class="table table-bordered table-striped table-hover" id="sitesTable">
                <thead class="table-light">
                    <tr>
                        <th>Site Name</th>
                        <th>Customer Name</th>
                        <th>Site Address</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Site Contact Name</th>
                        <th>Site Phone Number</th>
                        <th>Site Email</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sites)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No sites found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sites as $site): ?>
                            <tr data-customer-id="<?= $site['customer_id'] ?>">
                                <td>
                                    <a href="<?= site_url('admin/sites/' . $site['id']) ?>" class="text-primary fw-bold">
                                        <?= esc($site['name']) ?>
                                    </a>
                                </td>
                                <td><?= esc($customerMap[$site['customer_id']] ?? '') ?></td>
                                <td><?= esc($site['address'] ?? '') ?></td>
                                <td><?= esc($site['city'] ?? '') ?></td>
                                <td><?= esc($site['state'] ?? '') ?></td>
                                <td><?= esc($site['contact_name'] ?? '') ?></td>
                                <td><?= esc($site['phone'] ?? '') ?></td>
                                <td><?= esc($site['email'] ?? '') ?></td>
                                <td class="text-center">
                                    <!-- Edit button: populate modal with site data -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-site-btn" title="Edit"
                                        data-id="<?= $site['id'] ?>"
                                        data-name="<?= esc($site['name'], 'attr') ?>"
                                        data-customer_id="<?= esc($site['customer_id'], 'attr') ?>"
                                        data-address="<?= esc($site['address'] ?? '', 'attr') ?>"
                                        data-city="<?= esc($site['city'] ?? '', 'attr') ?>"
                                        data-state="<?= esc($site['state'] ?? '', 'attr') ?>"
                                        data-zip="<?= esc($site['zip'] ?? '', 'attr') ?>"
                                        data-contact_name="<?= esc($site['contact_name'] ?? '', 'attr') ?>"
                                        data-email="<?= esc($site['email'] ?? '', 'attr') ?>"
                                        data-phone="<?= esc($site['phone'] ?? '', 'attr') ?>">
                                        <i class="fa fa-pen"></i> Edit
                                    </button>
                                    <!-- Delete button -->
                                    <a href="<?= site_url('admin/sites/delete/' . $site['id']) ?>" class="btn btn-sm btn-outline-danger" title="Delete"
                                        onclick="return confirm('Are you sure you want to delete this site?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>


<!-- Site Modal -->
<div class="modal fade" id="siteModal" tabindex="-1" aria-labelledby="siteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="siteForm" method="post" action="<?= site_url('admin/sites/add') ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="siteModalLabel">Add Site</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="site-id" name="id">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="site-name">Site Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="site-name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="site-customer-id">Customer<span class="text-danger">*</span></label>
                            <select class="form-select" id="site-customer-id" name="customer_id" required>
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $cust): ?>
                                    <option value="<?= $cust['id'] ?>"><?= esc($cust['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label" for="site-address">Address</label>
                            <input type="text" class="form-control" id="site-address" name="address">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="site-city">City</label>
                            <input type="text" class="form-control" id="site-city" name="city">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="site-state">State</label>
                            <input type="text" class="form-control" id="site-state" name="state">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="site-zip">Zip</label>
                            <input type="text" class="form-control" id="site-zip" name="zip">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="site-contact">Contact Name</label>
                            <input type="text" class="form-control" id="site-contact" name="contact_name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="site-email">Email</label>
                            <input type="email" class="form-control" id="site-email" name="email">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="site-phone">Phone</label>
                            <input type="text" class="form-control" id="site-phone" name="phone">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const siteModal = document.getElementById('siteModal');
        const siteForm = document.getElementById('siteForm');
        const addSiteBtn = document.getElementById('addSiteBtn');
        
        // Reset modal function
        function resetModal() {
            siteForm.reset();
            document.getElementById('site-id').value = '';
            document.getElementById('siteModalLabel').textContent = 'Add Site';
            document.getElementById('submitBtn').textContent = 'Save';
            siteForm.setAttribute('action', '<?= site_url('admin/sites/add') ?>');
        }
        
        // Reset modal when closed
        siteModal.addEventListener('hidden.bs.modal', function () {
            resetModal();
        });
        
        // Add Site button handler
        addSiteBtn.addEventListener('click', function () {
            resetModal();
        });
        
        // Edit Site button handler using event delegation
        document.addEventListener('click', function(e) {
            if (e.target && (e.target.classList.contains('edit-site-btn') || e.target.closest('.edit-site-btn'))) {
                const btn = e.target.classList.contains('edit-site-btn') ? e.target : e.target.closest('.edit-site-btn');
                const id = btn.getAttribute('data-id');
                
                document.getElementById('siteModalLabel').textContent = 'Edit Site';
                document.getElementById('submitBtn').textContent = 'Update Site';
                siteForm.setAttribute('action', '<?= site_url('admin/sites/update/') ?>' + id);
                
                document.getElementById('site-id').value = id;
                document.getElementById('site-name').value = btn.getAttribute('data-name') || '';
                document.getElementById('site-customer-id').value = btn.getAttribute('data-customer_id') || '';
                document.getElementById('site-address').value = btn.getAttribute('data-address') || '';
                document.getElementById('site-city').value = btn.getAttribute('data-city') || '';
                document.getElementById('site-state').value = btn.getAttribute('data-state') || '';
                document.getElementById('site-zip').value = btn.getAttribute('data-zip') || '';
                document.getElementById('site-contact').value = btn.getAttribute('data-contact_name') || '';
                document.getElementById('site-email').value = btn.getAttribute('data-email') || '';
                document.getElementById('site-phone').value = btn.getAttribute('data-phone') || '';
                
                // Show the modal
                const modal = new bootstrap.Modal(siteModal);
                modal.show();
            }
        });
        
        // Auto-open Add Site modal if customer_id query parameter is present
        <?php if (isset($_GET['customer_id']) && $_GET['customer_id']): ?>
            const modal = new bootstrap.Modal(siteModal);
            modal.show();
            document.getElementById('site-customer-id').value = '<?= $_GET['customer_id'] ?>';
        <?php endif; ?>
        
        // Search functionality
        document.getElementById('mainSearch').addEventListener('input', function(e) {
            filterTable(e.target.value);
        });
        
        // Customer filter
        document.getElementById('customerFilter').addEventListener('change', function(e) {
            const customerId = e.target.value;
            const table = document.getElementById('sitesTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const rowCustomerId = row.getAttribute('data-customer-id');
                
                if (customerId === '' || rowCustomerId === customerId) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        function filterTable(searchTerm) {
            const table = document.getElementById('sitesTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            searchTerm = searchTerm.toLowerCase();
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        }
        
        // Export function placeholder
        window.exportTable = function(format) {
            alert('Export to ' + format + ' functionality would be implemented here');
        };
    });
</script>

<?= $this->endSection() ?>