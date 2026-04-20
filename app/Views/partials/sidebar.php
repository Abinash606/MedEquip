<nav class="sidebar">
    <div class="d-flex align-items-center gap-2 p-4 text-primary fw-bold fs-4">
        <img src="<?= base_url('logo/assetiq logo.png') ?>" alt="assetIQ Logo"
            style="max-height: 90px; width: auto; max-width: 100%; object-fit: contain;">
    </div>

    <div class="d-flex flex-column">
        <div class="section-header sidebar-text">Core Operations</div>
        <a href="<?= base_url('dashboard') ?>" class="nav-link <?= url_is('dashboard*') ? 'active' : '' ?>"><i class="fa-solid fa-gauge-high"></i> <span class="sidebar-text">Dashboard</span></a>
        <a href="<?= base_url('scheduling') ?>" class="nav-link <?= url_is('scheduling*') ? 'active' : '' ?>"><i class="fa-solid fa-calendar-check"></i> <span class="sidebar-text">Scheduling</span></a>

        <?php
        $__db = \Config\Database::connect();
        $__cid = (int) session('company_id');
        $__siteCount  = (int) $__db->query('SELECT COUNT(*) AS n FROM sites WHERE company_id=? AND deleted_at IS NULL', [$__cid])->getRow()->n;
        $__eqCount    = (int) $__db->query('SELECT COUNT(*) AS n FROM equipment WHERE company_id=? AND deleted_at IS NULL', [$__cid])->getRow()->n;
        $__woCount    = (int) $__db->query("SELECT COUNT(*) AS n FROM work_orders WHERE company_id=? AND deleted_at IS NULL AND status NOT IN ('completed','cancelled')", [$__cid])->getRow()->n;
        ?>
        <div class="section-header sidebar-text">Assets & Clients</div>
        <a href="<?= base_url('customers') ?>" class="nav-link <?= url_is('customers*') ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> <span class="sidebar-text">Customers</span></a>
        <a href="<?= base_url('sites') ?>" class="nav-link <?= url_is('sites*') ? 'active' : '' ?>"><i class="fa-solid fa-sitemap"></i> <span class="sidebar-text">Sites <span class="badge bg-secondary ms-1"><?= $__siteCount ?></span></span></a>
        <a href="<?= base_url('equipment') ?>" class="nav-link <?= url_is('equipment*') ? 'active' : '' ?>"><i class="fa-solid fa-boxes-stacked"></i> <span class="sidebar-text">Equipment DB <span class="badge bg-secondary ms-1"><?= $__eqCount ?></span></span></a>
        <a href="<?= base_url('work-orders') ?>" class="nav-link <?= url_is('work-orders*') ? 'active' : '' ?>"><i class="fa-solid fa-wrench"></i> <span class="sidebar-text">Work Orders <span class="badge bg-secondary ms-1"><?= $__woCount ?></span></span></a>
        <a href="<?= base_url('inspections') ?>" class="nav-link <?= url_is('inspections*') ? 'active' : '' ?>"><i class="fa-solid fa-clipboard-list"></i> <span class="sidebar-text">Inspection Reports</span></a>

        <a href="<?= base_url('inventory') ?>" class="nav-link <?= url_is('inventory*') ? 'active' : '' ?>"><i class="fa-solid fa-box-open"></i> <span class="sidebar-text">Inventory</span></a>
        <a href="<?= base_url('technicians') ?>" class="nav-link <?= url_is('technicians*') ? 'active' : '' ?>"><i class="fa-solid fa-users-cog"></i> <span class="sidebar-text">Technicians</span></a>

        <div class="section-header sidebar-text">Admin & Analytics</div>
        <a href="<?= base_url('financials') ?>" class="nav-link <?= url_is('financials*') ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i> <span class="sidebar-text">Operational Summary</span></a>
        <a href="<?= base_url('compliance') ?>" class="nav-link <?= url_is('compliance*') ? 'active' : '' ?>"><i class="fa-solid fa-shield-check"></i> <span class="sidebar-text">Compliance</span></a>
        <a href="<?= base_url('dataops') ?>" class="nav-link <?= url_is('dataops*') ? 'active' : '' ?>"><i class="fa-solid fa-database"></i> <span class="sidebar-text">Data Ops</span></a>
        <a href="<?= base_url('settings') ?>" class="nav-link <?= url_is('settings*') ? 'active' : '' ?>"><i class="fa-solid fa-gears"></i> <span class="sidebar-text">Settings</span></a>
    </div>

    <div class="mt-auto p-4 border-top">
        <div class="d-flex align-items-center gap-2">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('full_name')) ?>" class="rounded-circle" width="35">
            <div class="small lh-sm sidebar-text">
                <div class="fw-bold"><?= esc(session()->get('full_name')) ?></div>
                <div class="text-muted"><a href="<?= base_url('logout') ?>" class="text-decoration-none text-muted">Logout</a></div>
            </div>
        </div>
    </div>
</nav>