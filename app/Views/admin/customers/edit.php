<!-- Edit Customer Modal Form -->
<form id="edit-customer-form" method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- Customer Details -->
        <div class="col-md-6">
            <label class="form-label" for="customer-name">Customer Name</label>
            <input type="text" class="form-control" id="customer-name" name="name" value="<?= $customer['name'] ?>" required>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="customer-email">Customer Email</label>
            <input type="email" class="form-control" id="customer-email" name="email" value="<?= $customer['email'] ?>" required>
        </div>

        <!-- Customer Logo Upload (If Logo exists) -->
        <div class="col-md-6">
            <label class="form-label" for="customer-logo">Customer Logo</label>
            <input type="file" class="form-control" id="customer-logo" name="logo" accept="image/*">
            <?php if ($customer['logo_path']): ?>
                <img src="<?= base_url($customer['logo_path']) ?>" alt="Logo" class="img-thumbnail mt-2" width="100">
            <?php endif; ?>
        </div>

        <!-- Credentials Section (Dynamic) -->
        <div id="credentials-section">
            <?php foreach ($credentials as $key => $credential): ?>
                <div class="credential-block">
                    <div class="col-md-4">
                        <label class="form-label" for="portal-username">Admin username <?= $key+1 ?></label>
                        <input type="text" class="form-control" name="portal_username[]" value="<?= $credential['username'] ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="portal-email">Email</label>
                        <input type="email" class="form-control" name="portal_email[]" value="<?= $credential['email'] ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="portal-password">Admin password <?= $key+1 ?></label>
                        <input type="password" class="form-control" name="portal_password[]" value="<?= $credential['password'] ?>">
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeCredential(this)">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Add More Credentials Button -->
        <button type="button" class="btn btn-link btn-sm mt-2" id="addMoreCredentials">Add More Credentials</button>

        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary">Update Customer</button>
        </div>
    </div>
</form>

<!-- jQuery Script to Add More Credentials and Remove Credentials -->
<script>
    document.getElementById('addMoreCredentials').addEventListener('click', function() {
        const credentialsSection = document.querySelector('#credentials-section');
        const newFields = credentialsSection.firstElementChild.cloneNode(true); // Clone only the first credential block

        // Clear the input fields to make them empty for new entries
        newFields.querySelectorAll('input').forEach(input => {
            input.value = '';
        });

        // Append the cloned credentials section
        credentialsSection.appendChild(newFields);
    });

    function removeCredential(button) {
        button.parentElement.remove(); // Remove the credential block
    }
</script>
