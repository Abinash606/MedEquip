<?= $this->extend('layouts/customer-header') ?>

<?= $this->section('content') ?>
<!-- Assets section -->
<section id="inventory" class="view-section active">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Assets</h4>
            <div class="text-muted small">Search, sort, and export your asset list.</div>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
            <i class="fa-solid fa-plus me-2"></i> Add Asset
        </button>
    </div>

    <div class="glass-card">

        <!-- Top bar: Search right-aligned -->
        <div class="d-flex justify-content-end align-items-center mb-3">
            <div class="input-group" style="max-width: 260px;">
                <span class="input-group-text bg-light border-0 text-muted">
                    <i class="fa-solid fa-magnifying-glass fa-sm"></i>
                </span>
                <input type="text" id="assetSearch" class="form-control bg-light border-0" placeholder="Search…"
                    autocomplete="off">
                <button class="btn bg-light border-0 text-muted" id="clearSearch" title="Clear" style="display:none;">
                    <i class="fa-solid fa-xmark fa-sm"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="assetsTable" class="table table-hover align-middle mb-0" style="width:100%">
                <thead class="bg-light">
                    <tr>
                        <th>Asset Tag</th>
                        <th>Serial Number</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Device Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipment)): ?>
                        <?php foreach ($equipment as $eq): ?>
                            <tr>
                                <td><?= esc($eq['asset_tag']) ?></td>
                                <td><?= esc($eq['serial_number']) ?></td>
                                <td><?= esc($eq['make']) ?></td>
                                <td><?= esc($eq['model']) ?></td>
                                <td><?= esc($eq['device_type']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No assets found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Bottom: "Showing X to Y of Z entries" left | Previous [1][2] Next right -->
        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
            <div class="text-muted small" id="assetCount"></div>
            <nav aria-label="Asset pagination">
                <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
            </nav>
        </div>

    </div>
</section>

<!-- Add Equipment Modal -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add New Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <form id="addEquipmentForm" method="post" action="<?= base_url('customer/assets/store') ?>" novalidate>
                    <?= csrf_field() ?>

                    <!-- hidden site id -->
                    <input type="hidden" name="site_id" value="<?= $sites[0]['id'] ?? '' ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Asset Tag</label>
                        <input type="text" name="asset_tag" id="asset_tag" class="form-control bg-light border-0"
                            placeholder="Leave blank for auto-generate.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Serial Number <span
                                class="text-danger">*</span></label>
                        <input type="text" name="serial_number" id="serial_number"
                            class="form-control bg-light border-0" placeholder="Enter serial number" required>
                        <div class="invalid-feedback">Serial number is required.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Make <span
                                class="text-danger">*</span></label>
                        <input type="text" name="make" id="make" class="form-control bg-light border-0"
                            placeholder="Enter manufacturer" required minlength="2">
                        <div class="invalid-feedback">Make is required (minimum 2 characters).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Model <span
                                class="text-danger">*</span></label>
                        <input type="text" name="model" id="model" class="form-control bg-light border-0"
                            placeholder="Enter model" required minlength="2">
                        <div class="invalid-feedback">Model is required (minimum 2 characters).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Device Type <span
                                class="text-danger">*</span></label>
                        <input type="text" name="device_type" id="device_type" class="form-control bg-light border-0"
                            placeholder="Enter device type" required>
                        <div class="invalid-feedback">Device type is required.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Department</label>
                        <input type="text" name="department" id="department" class="form-control bg-light border-0"
                            placeholder="Enter department">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Location</label>
                        <input type="text" name="location" id="location" class="form-control bg-light border-0"
                            placeholder="Room Number or Location">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Status <span
                                class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select bg-light border-0" required>
                            <option value="">Select status</option>
                            <option value="Operational">Operational</option>
                            <option value="Needs Attention">Needs Attention</option>
                            <option value="Out of Service">Out of Service</option>
                        </select>
                        <div class="invalid-feedback">Please select a status.</div>
                    </div>

                    <button type="submit" class="btn btn-wow w-100">Save Equipment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* ── Modal / form logic ── */
        const form = document.getElementById('addEquipmentForm');
        const modal = document.getElementById('addEquipmentModal');
        const bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            const s = id => document.getElementById(id).value.trim();
            if (!s('serial_number') || !s('make') || !s('model') || !s('device_type') || !document
                .getElementById('status').value) {
                form.classList.add('was-validated');
                return;
            }
            form.classList.remove('was-validated');
            form.submit();
        });

        <?php if (session()->getFlashdata('success')): ?>
            bsModal.hide();
            Swal.fire({
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                    icon: 'success',
                    confirmButtonText: 'OK'
                })
                .then(() => location.reload());
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            bsModal.show();
            Swal.fire({
                title: 'Error!',
                text: '<?= session()->getFlashdata('error') ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        modal.addEventListener('hidden.bs.modal', function() {
            form.reset();
            form.classList.remove('was-validated');
        });

        /* ── Search + Pagination ── */
        const table = document.getElementById('assetsTable');
        const allRows = Array.from(table.querySelectorAll('tbody tr'));
        const searchInput = document.getElementById('assetSearch');
        const clearBtn = document.getElementById('clearSearch');
        const pagination = document.getElementById('paginationControls');
        const assetCount = document.getElementById('assetCount');

        const PAGE_SIZE = 10;
        let currentPage = 1;
        let filtered = allRows.slice();

        function applySearch() {
            const q = searchInput.value.trim().toLowerCase();
            clearBtn.style.display = q ? '' : 'none';
            filtered = q ? allRows.filter(r => r.textContent.toLowerCase().includes(q)) : allRows.slice();
            currentPage = 1;
            render();
        }

        function render() {
            const total = filtered.length;
            const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * PAGE_SIZE;
            const end = start + PAGE_SIZE;

            // Show / hide rows
            allRows.forEach(r => r.style.display = 'none');
            filtered.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');

            // Empty state row
            const existingEmpty = table.querySelector('tr.empty-state');
            if (existingEmpty) existingEmpty.remove();
            if (total === 0) {
                const tr = document.createElement('tr');
                tr.className = 'empty-state';
                tr.innerHTML = `<td colspan="5" class="text-center text-muted py-4">No matching records found</td>`;
                table.querySelector('tbody').appendChild(tr);
            }

            // "Showing X to Y of Z entries" — exactly like DataTables
            const showFrom = total === 0 ? 0 : start + 1;
            const showTo = Math.min(end, total);
            assetCount.textContent = total === 0 ?
                'Showing 0 entries' :
                `Showing ${showFrom} to ${showTo} of ${total} entries`;

            // Build pagination: Previous [1] [2] [3] Next
            pagination.innerHTML = '';

            const mkLi = (label, page, disabled, active) => {
                const li = document.createElement('li');
                li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
                const a = document.createElement('a');
                a.className = 'page-link';
                a.href = '#';
                a.innerHTML = label;
                if (!disabled && !active) {
                    a.addEventListener('click', e => {
                        e.preventDefault();
                        currentPage = page;
                        render();
                    });
                }
                li.appendChild(a);
                return li;
            };

            // Previous button
            pagination.appendChild(mkLi('Previous', currentPage - 1, currentPage === 1, false));

            // Page number buttons (windowed)
            let pages = [];
            if (totalPages <= 7) {
                pages = Array.from({
                    length: totalPages
                }, (_, i) => i + 1);
            } else {
                pages = [1];
                if (currentPage > 3) pages.push('…');
                for (let p = Math.max(2, currentPage - 1); p <= Math.min(totalPages - 1, currentPage + 1); p++)
                    pages.push(p);
                if (currentPage < totalPages - 2) pages.push('…');
                pages.push(totalPages);
            }

            pages.forEach(p => {
                if (p === '…') {
                    const li = document.createElement('li');
                    li.className = 'page-item disabled';
                    li.innerHTML = '<span class="page-link">…</span>';
                    pagination.appendChild(li);
                } else {
                    pagination.appendChild(mkLi(p, p, false, p === currentPage));
                }
            });

            // Next button
            pagination.appendChild(mkLi('Next', currentPage + 1, currentPage === totalPages, false));
        }

        searchInput.addEventListener('input', applySearch);

        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            applySearch();
            searchInput.focus();
        });

        // Initial render
        render();
    });
</script>

<?= $this->endSection() ?>