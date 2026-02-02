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

        <!-- Credentials Section (Dynamic) -->
        <div id="credentials-section">
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
        const newFields = credentialsSection.firstElementChild.cloneNode(true); // Clone the first credential block

        // Clear the input fields to make them empty for new entries
        newFields.querySelectorAll('input').forEach(input => {
            input.value = '';
        });

        // Append the cloned credentials section
        credentialsSection.appendChild(newFields);
    });
</script>
