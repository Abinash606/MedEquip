<!-- Add Customer Modal Form -->
<form id="customer-form" method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- Customer Details -->
        <div class="col-md-6">
            <label class="form-label" for="customer-name">Customer Name</label>
            <input type="text" class="form-control" id="customer-name" name="name" required>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="customer-email">Customer Email</label>
            <input type="email" class="form-control" id="customer-email" name="email" required>
        </div>

        <!-- Customer Logo Upload -->
        <div class="col-md-6">
            <label class="form-label" for="customer-logo">Customer Logo</label>
            <input type="file" class="form-control" id="customer-logo" name="logo" accept="image/*">
        </div>

        <!-- Internal Labor Rate Notes (admin/internal only — never shown on invoice/packing slip) -->
        <div class="col-12 mt-3">
            <label class="form-label fw-semibold" for="internal-labor-rate-notes">
                Internal Labor Rate Notes
                <span class="badge bg-warning text-dark ms-1" style="font-size:0.7rem;">Internal Only</span>
            </label>
            <textarea class="form-control" id="internal-labor-rate-notes"
                name="internal_labor_rate_notes" rows="3"
                placeholder="e.g. PM rate: $100/hr, Repair rate: $110/hr, After-hours: $175.50/hr — visible internally and on Site screen only."></textarea>
            <div class="form-text text-muted">
                <i class="fas fa-lock me-1"></i>
                This note is <strong>internal only</strong> — it will appear on the Site screen for reference
                but will <strong>never</strong> be printed on invoices or packing slips.
            </div>
        </div>

        <!-- Credentials Section (Dynamic) -->
        <div id="credentials-section" class="mt-3">
            <div class="col-md-4">
                <label class="form-label" for="portal-username">Admin username 1</label>
                <input type="text" class="form-control" id="portal-username" name="portal_username[]">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="portal-email">Email</label>
                <input type="email" class="form-control" id="portal-email" name="portal_email[]">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="portal-password">Admin password 1</label>
                <input type="password" class="form-control" id="portal-password" name="portal_password[]">
            </div>
        </div>

        <!-- Add More Credentials Button -->
        <button type="button" class="btn btn-link btn-sm mt-2" id="addMoreCredentials">Add More Credentials</button>

        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary">Save Customer</button>
        </div>
    </div>
</form>

<!-- jQuery Script to Add More Credentials -->
<script>
    document.getElementById('addMoreCredentials').addEventListener('click', function() {
        const credentialsSection = document.querySelector('#credentials-section');
        const newFields = credentialsSection.firstElementChild.cloneNode(true);

        // Clear the input fields to make them empty for new entries
        newFields.querySelectorAll('input').forEach(input => {
            input.value = '';
        });

        // Append the cloned credentials section
        credentialsSection.appendChild(newFields);
    });
</script>
