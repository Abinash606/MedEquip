<?php
$uriSegments = service('uri')->getSegments();
$skipSegments = ['admin', 'technician', 'customer'];
$pageTitle = trim((string) ($title ?? ''));

if ($pageTitle === '') {
    $usableSegments = array_values(array_filter($uriSegments, static function ($segment) use ($skipSegments) {
        $segment = trim((string) $segment);
        return $segment !== '' && !in_array(strtolower($segment), $skipSegments, true) && !ctype_digit($segment);
    }));

    $derived = end($usableSegments) ?: 'Dashboard';
    $pageTitle = ucwords(str_replace(['-', '_'], ' ', (string) $derived));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <style>
        :root {
            --bg0: #070A12; --bg1: #0B1020; --panel: #0E1630;
            --panel2: rgba(16,24,50,.55);
            --stroke: rgba(255,255,255,.08); --stroke2: rgba(255,255,255,.12);
            --text: #E9EDFF; --muted: rgba(233,237,255,.70); --muted2: rgba(233,237,255,.52);
            --shadow: 0 18px 50px rgba(0,0,0,.55);
            --accA: #7C3AED; --accB: #22D3EE; --accC: #F97316;
            --good: #22C55E; --warn: #F59E0B; --bad: #EF4444;
            --radius: 18px; --radius2: 14px;
            --sidebar-width: 270px;
        }
        html,body { margin:0; padding:0; }
        body {
            font-family: Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
            color: var(--text);
            background:
                radial-gradient(1200px 800px at 20% 0%, rgba(124,58,237,.22), transparent 60%),
                radial-gradient(900px 650px at 85% 12%, rgba(34,211,238,.18), transparent 55%),
                radial-gradient(900px 700px at 65% 110%, rgba(249,115,22,.10), transparent 50%),
                linear-gradient(180deg, var(--bg0), var(--bg1));
            overflow-x: hidden;
        }
        h1,h2,h3,h4 { font-family:'Space Grotesk',Inter,system-ui,sans-serif; }

        .sidebar {
            width: var(--sidebar-width); height:100vh; position:fixed; top:0; left:0;
            background: linear-gradient(180deg, rgba(14,22,48,.97), rgba(7,10,18,.97));
            border-right: 1px solid var(--stroke); z-index:1000;
            display:flex; flex-direction:column; backdrop-filter:blur(20px);
            transition: all 0.3s ease;
        }
        .sidebar-brand { padding:1.5rem 1.25rem 1rem; border-bottom:1px solid var(--stroke); }
        .sidebar-brand img { max-height:70px; width:auto; object-fit:contain; }
        .sidebar-nav { flex:1; padding:1rem 0.75rem; overflow-y:auto; }
        .section-header { font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--muted2);padding:0 12px;margin:12px 0 6px; }

        .nav-link {
            display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:14px;
            color:var(--muted);cursor:pointer;position:relative;
            transition:transform .16s ease,background .16s ease,color .16s ease;
            text-decoration:none;user-select:none;margin-bottom:2px;
            font-size:0.9rem;font-weight:500;
        }
        .nav-link i { font-size:15px;width:20px;text-align:center; }
        .nav-link:hover { background:rgba(255,255,255,.05);color:var(--text);transform:translateX(2px); }
        .nav-link.active {
            color:var(--text);
            background:linear-gradient(90deg,rgba(124,58,237,.18),rgba(34,211,238,.10));
            border:1px solid rgba(255,255,255,.08);
        }
        .nav-link.active::before {
            content:"";position:absolute;left:0;top:10px;bottom:10px;
            width:4px;border-radius:99px;
            background:linear-gradient(180deg,var(--accA),var(--accB));
        }
        .sidebar-footer { padding:1rem 1.25rem;border-top:1px solid var(--stroke); }
        .avatar-circle {
            width:34px;height:34px;border-radius:10px;
            background:linear-gradient(135deg,rgba(124,58,237,.85),rgba(34,211,238,.85));
            display:flex;align-items:center;justify-content:center;
            font-size:12px;font-weight:800;color:#fff;flex-shrink:0;
        }

        .main-content { margin-left:var(--sidebar-width); min-height:100vh; }
        .topbar {
            position:sticky;top:0;z-index:10;
            display:flex;justify-content:space-between;align-items:center;
            padding:16px 24px;
            background:linear-gradient(180deg,rgba(11,16,32,.85),rgba(11,16,32,.60));
            border-bottom:1px solid rgba(255,255,255,.06);backdrop-filter:blur(14px);
        }
        .content { padding:20px 24px 40px; }

        .glass-card {
            border-radius:var(--radius);border:1px solid rgba(255,255,255,.10);
            background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.03));
            backdrop-filter:blur(14px);box-shadow:var(--shadow);position:relative;
            transition:transform .16s ease,box-shadow .16s ease,border-color .16s ease;
            padding:1.25rem;
        }
        .glass-card:hover { transform:translateY(-2px);border-color:rgba(255,255,255,.16); }

        /* Tables */
        .custom-table thead th { font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:rgba(233,237,255,.55)!important;border-bottom:1px solid rgba(255,255,255,.08)!important;font-weight:500; }
        .custom-table tbody td { vertical-align:middle;padding:1rem 0.5rem;border-bottom:1px solid rgba(255,255,255,.06)!important;color:var(--text); }
        .custom-table tbody tr:hover { background:rgba(255,255,255,.04)!important; }
        table.dataTable.stripe tbody tr.odd,table.dataTable tbody tr.odd,table.dataTable tbody tr.even,
        .table>:not(caption)>*>* { background-color:transparent!important;color:#fff!important; }
        .dataTable tr th { font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:rgba(233,237,255,.55)!important;border-bottom:1px solid rgba(255,255,255,.08)!important; }
        .dataTable tr td { border-bottom:1px solid rgba(255,255,255,.08)!important; }
        .dataTables_info,.dataTables_filter,.dataTables_length { color:#fff!important; }
        .dataTables_filter input,.dataTables_length select { color:#fff!important;background:rgba(255,255,255,.04)!important;border:1px solid rgba(255,255,255,.12)!important;border-radius:8px!important;padding:4px 8px; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { color:#fff!important; }
        .dt-buttons .dt-button { background:linear-gradient(135deg,rgba(124,58,237,1),rgba(34,211,238,1))!important;color:#fff!important;border-radius:10px!important;padding:7px 14px; }

        /* Buttons */
        .btn-primary { background:linear-gradient(90deg,rgba(34,211,238,.92),rgba(124,58,237,.70))!important;border:none!important; }
        .btn-danger { background:rgba(239,68,68,.85)!important;border:none!important; flex: 0 0 auto; }
        .btn-outline-secondary { color:rgba(233,237,255,.80)!important;border-color:rgba(255,255,255,.20)!important; }

        /* Badges */
        .badge { padding:6px 10px;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.04);font-size:12px;font-weight:700;color:rgba(233,237,255,.90)!important; }
        .badge.bg-success { border-color:rgba(34,197,94,.22)!important;background:rgba(34,197,94,.10)!important; }
        .badge.bg-danger  { background:rgba(239,68,68,.5)!important;border-color:rgba(239,68,68,.26)!important; }
        .badge.bg-warning { border-color:rgba(245,158,11,.26)!important;background:rgba(245,158,11,.12)!important; }

        /* Status badges */
        .status-badge { padding:.35em .8em;border-radius:50rem;font-size:.75em;font-weight:600; }
        .status-operational { background:rgba(34,197,94,.15);color:#4ade80; }
        .status-service { background:rgba(239,68,68,.15);color:#f87171; }
        .status-maintenance { background:rgba(245,158,11,.15);color:#fbbf24; }

        /* Form controls */
        .glass-card .form-control,.glass-card .form-select,.glass-card .input-group-text {
            border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.04);color:rgba(233,237,255,.86)!important;
        }
        .glass-card .form-control::placeholder,.glass-card .form-label { color:rgba(233,237,255,.70)!important; }
        .glass-card .form-select option { color:#000!important; }

        .text-muted { color:rgba(233,237,255,.55)!important; }
        h1,h2,h3,h4,h5,h6,.fw-bold { color:var(--text); }

        /* Doc icon */
        .doc-icon { width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.2rem; }

        .view-section { display:none;animation:fadeIn .3s ease-in-out; }
        .view-section.active { display:block; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        .action-btns { display:flex;gap:8px;align-items:center; }

        .mobile-overlay { display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:999; }
        .mobile-overlay.active { display:block; }

        @media (max-width:991px) {
            .sidebar { left:-300px; }
            .sidebar.active { left:0; }
            .main-content { margin-left:0; }
            .content { padding:1rem; }
        }
        #siteFilterDash option{
            color:#000!important;
        }
                 .glass-card input::placeholder, .topbar .search input::placeholder{
            color:#fff;
         }
         input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
  filter: invert(1); /* makes icon white */
  cursor: pointer;
}
    </style>
</head>
<body>
<div class="mobile-overlay" id="mobileOverlay"></div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="<?= base_url('logo/assetiq logo.png') ?>" alt="AssetIQ Logo">
    </div>

    <div class="sidebar-nav">
        <?php $role = session('role'); ?>
        <p class="section-header">Customer Portal</p>

        <?php if ($role === 'customer'): ?>
        <a class="nav-link<?= url_is('customer/dashboard') ? ' active' : '' ?>" href="<?= site_url('customer/dashboard') ?>">
            <i class="fa-solid fa-chart-pie"></i><span>Overview</span>
        </a>
        <a class="nav-link<?= url_is('customer/assets*') ? ' active' : '' ?>" href="<?= site_url('customer/assets') ?>">
            <i class="fa-solid fa-cubes"></i><span>Assets</span>
        </a>
        <a class="nav-link<?= url_is('customer/inspections*') ? ' active' : '' ?>" href="<?= site_url('customer/inspections') ?>">
            <i class="fa-solid fa-calendar-days"></i><span>Inspections</span>
        </a>
        <a class="nav-link<?= url_is('customer/documents*') ? ' active' : '' ?>" href="<?= site_url('customer/documents') ?>">
            <i class="fa-solid fa-folder-open"></i><span>Documents</span>
        </a>
        <?php endif; ?>
    </div>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <?php $username = session('username') ?? 'Customer'; $initials = strtoupper(substr(trim($username),0,2)); ?>
            <div class="avatar-circle"><?= esc($initials) ?></div>
            <div class="small lh-sm">
                <div class="fw-semibold"><?= esc($username) ?></div>
                <a href="<?= site_url('logout') ?>" class="text-decoration-none" style="color:rgba(233,237,255,.55);font-size:12px;">Logout</a>
            </div>
        </div>
    </div>
</nav>

<?= $this->include('partials/customer-scripts') ?>

<div class="main-content" id="mainContent">
    <?= $this->renderSection('content') ?>
</div>

<?= $this->include('partials/customer-modals') ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('mobileOverlay');
    if (toggleBtn) { toggleBtn.addEventListener('click', function() { sidebar&&sidebar.classList.toggle('active'); overlay&&overlay.classList.toggle('active'); }); }
    if (overlay)   { overlay.addEventListener('click', function()   { sidebar&&sidebar.classList.remove('active'); overlay.classList.remove('active'); }); }
});
</script>
</body>
</html>
