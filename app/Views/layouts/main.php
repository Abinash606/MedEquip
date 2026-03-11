<?php

/** @var string|null $title */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Dashboard') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <!-- Additional styles for calendar and map views -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">


    <style>
         :root{
      --bg0:#070A12;
      --bg1:#0B1020;
      --panel:#0E1630;
      --panel2:rgba(16, 24, 50, .55);
      --stroke:rgba(255,255,255,.08);
      --stroke2:rgba(255,255,255,.12);
      --text:#E9EDFF;
      --muted:rgba(233,237,255,.70);
      --muted2:rgba(233,237,255,.52);
      --shadow: 0 18px 50px rgba(0,0,0,.55);

      --accA:#7C3AED;
      --accB:#22D3EE;
      --accC:#F97316;
      --good:#22C55E;
      --warn:#F59E0B;
      --bad:#EF4444;
      --info:#60A5FA;

      --radius:18px;
      --radius2:14px;
    }

    html,body{height:100%;}
    body{
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color:var(--text);
      background:
        radial-gradient(1200px 800px at 20% 0%, rgba(124,58,237,.22), transparent 60%),
        radial-gradient(900px 650px at 85% 12%, rgba(34,211,238,.18), transparent 55%),
        radial-gradient(900px 700px at 65% 110%, rgba(249,115,22,.10), transparent 50%),
        linear-gradient(180deg, var(--bg0), var(--bg1));
      overflow-x:hidden;
    }
    /* .modal .modal-dialog .modal-content{
        background-color: #000!important;
        box-shadow: 0px 0px 6px rgba(233,237,255,.52);;
    } */
    h1,h2,h3,h4,.brand-text,.split-head,.compact-head,.table-title,.work-title{
      font-family: "Space Grotesk", Inter, system-ui, sans-serif;
      letter-spacing:.2px;
    }
        .app-shell{
      min-height:100vh;
      display:grid;
      grid-template-columns: 270px 1fr;
      gap:0;
    }
    .sidebar-collapsed {
    grid-template-columns: 92px 1fr;
}
    .sidebar-collapsed .nav-link span,
    .sidebar-collapsed .section-header{display:none;}

    .sidebar-collapsed .sidebar{
      padding-left: 10px;
      padding-right: 10px;
    }
    .sidebar-collapsed .nav-logo{
        height:20px!important;
    }

    .sidebar-collapsed .nav-link{justify-content:center;}

    @media (max-width: 992px){
      .app-shell{grid-template-columns: 1fr;}
      .sidebar{position:relative; height:auto;}
      .topbar{position:relative;}
      .search{min-width: 100%;}
    }
    .sidebar-collapsed .brand-text, .sidebar-collapsed .nav-link span, .sidebar-collapsed .section-header, .sidebar-collapsed  .sidebar-text {
    display: none;
}

    .sidebar-divider{
      height:1px;
      background: rgba(255,255,255,.08);
      margin:14px 10px;
    }

        /* Layout */
  .sidebar{
      position:sticky;
      top:0;
      height:100vh;
      padding:18px 14px;
      background:
        linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02)),
        rgba(10, 16, 38, .65);
      border-right: 1px solid var(--stroke);
      box-shadow: inset -1px 0 0 rgba(255,255,255,.04);
      backdrop-filter: blur(14px);
      overflow-y:auto;
      scrollbar-width:thin;
    }
    @media(max-width:992px){
        .sidebar{
            position: relative;
            height: auto;
        }
        .sidebar-nav, .sidebar-footer{
            display:none;
        }
         .sidebar-nav.show, .sidebar-footer.show{
            display:block;
        }
    }
    .sidebar-toggle{
      margin-left:auto;
      width:38px;
      height:38px;
      border-radius:12px;
      border:1px solid rgba(255,255,255,.10);
      background: rgba(255,255,255,.04);
      color:var(--text);
      display:grid;
      place-items:center;
      transition: transform .18s ease, background .18s ease, border-color .18s ease;
      font-size:16px;
    }
    .sidebar-mtoggle{
         margin-left:auto;
      width:38px;
      height:38px;
      border-radius:12px;
      border:1px solid rgba(255,255,255,.10);
      background: rgba(255,255,255,.04);
      color:var(--text);
      display:grid;
      place-items:center;
      transition: transform .18s ease, background .18s ease, border-color .18s ease;
      font-size:16px;
    }
    .sidebar-toggle:hover, .sidebar-mtoggle:hover{transform: translateY(-1px); background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.16);}
        .main-content {
            /* padding: 1.5rem; */
            transition: all 0.3s;
            max-width: calc(100vw - 275px); 
            width:100%;
        }
         .sidebar-collapsed  .main-content{
             max-width: calc(100vw - 90px);
         }
 .topbar{
      position:sticky;
      top:0;
      z-index:10;
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:18px 22px;
      background: linear-gradient(180deg, rgba(11,16,32,.80), rgba(11,16,32,.55));
      border-bottom: 1px solid rgba(255,255,255,.06);
      backdrop-filter: blur(14px);
    }
     .btn-new{
      border:0;
      border-radius:16px;
      padding: 11px 16px;
      font-weight:700;
      letter-spacing:.2px;
      background: linear-gradient(135deg, rgba(124,58,237,1), rgba(34,211,238,1));
      box-shadow: 0 18px 42px rgba(0,0,0,.45);
      position:relative;
      overflow:hidden;
    }
    .btn-new::after{
      content:"";
      position:absolute;
      inset:-40% -60%;
      background: radial-gradient(circle at 30% 40%, rgba(255,255,255,.30), transparent 55%);
      transform: translateX(-15%);
      opacity:.0;
      transition: opacity .18s ease;
    }
    .btn-new:hover{transform: translateY(-1px); filter:saturate(1.1);}
    .btn-new:hover::after{opacity:1;}
 .search{
      position:relative;
      min-width: 320px;
      max-width: 460px;
    }

    .search i{
      position:absolute;
      left:14px;
      top:50%;
      transform: translateY(-50%);
      color: rgba(233,237,255,.55);
      pointer-events:none;
    }

    .search .form-control{
      background: rgba(255,255,255,.04);
      border:1px solid rgba(255,255,255,.10);
      color:var(--text);
      padding-left:42px;
      border-radius: 16px;
      height:44px;
      transition: box-shadow .18s ease, border-color .18s ease, transform .18s ease;
    }
    .search .form-control:focus{
      background: rgba(255,255,255,.05);
      border-color: rgba(34,211,238,.35);
      box-shadow: 0 0 0 .25rem rgba(34,211,238,.12);
      color:var(--text);
    }
      .content{padding: 18px 22px 32px; width:100%;}
        /* Cards & Glassmorphism */
        .glass-card{
      border-radius: var(--radius);
      border:1px solid rgba(255,255,255,.10);
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
      backdrop-filter: blur(14px);
      box-shadow: var(--shadow);
      position:relative;
      /* overflow:hidden; */
      transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
    }
    .g-card{
  border-radius: var(--radius);
      border:1px solid rgba(255,255,255,.10);
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
      box-shadow: var(--shadow);
      position:relative;
      /* overflow:hidden; */
      transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
    }
    .glass-card:hover{
      transform: translateY(-2px);
      border-color: rgba(255,255,255,.16);
      box-shadow: 0 22px 65px rgba(0,0,0,.60);
    }
      .metric-card{padding:16px 16px 14px; min-height: 172px;}

    .metric-card::before{
      content:"";
      position:absolute;
      left:0;
      top:18px;
      bottom:18px;
      width:5px;
      border-radius:999px;
      background: linear-gradient(180deg, var(--accA), var(--accB));
      opacity:.85;
    }

    .metric-card::after{
      content:"";
      position:absolute;
      top:0;
      left:0;
      right:0;
      height:4px;
      background: linear-gradient(90deg, rgba(124,58,237,.95), rgba(34,211,238,.95), rgba(249,115,22,.85));
      opacity:.75;
    }
    .text-muted{
        color: rgba(233, 237, 255, .55)!important;
    }
    .glass-card .text-muted.small.uppercase{
        font-size: 11px;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: rgba(233, 237, 255, .55);
    line-height: 1.25;
    }
    .glass-card .opacity-25 i{
    font-size:16px!important;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, .10);
    background: rgba(255, 255, 255, .04);
    transition: transform .16s ease, background .16s ease, border-color .16s ease;
    padding:10px!important;
    }
        .glass-card .opacity-25 {
            opacity: 1!important;
        }
        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        /* Navigation */
        .nav-link {
          display:flex;
      align-items:center;
      gap:12px;
      padding:10px 12px;
      border-radius:14px;
      color:var(--muted);
      cursor:pointer;
      position:relative;
      transition: transform .16s ease, background .16s ease, color .16s ease;
      user-select:none;
        }
 .nav-link i{
    font-size:16px; opacity:.92;
 }
        .nav-link:hover {
           background: rgba(255,255,255,.05); color:var(--text); transform: translateX(2px);
        }

        .nav-link.active {
           color:var(--text);
      background: linear-gradient(90deg, rgba(124,58,237,.18), rgba(34,211,238,.10));
      border:1px solid rgba(255,255,255,.08);
        }
   .nav-link.active::before{
      content:"";
      position:absolute;
      left:0;
      top:10px;
      bottom:10px;
      width:4px;
      border-radius:99px;
      background: linear-gradient(180deg, var(--accA), var(--accB));
    }
        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .section-header {
            font-size: 12px;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: rgba(233, 237, 255, .55);
    padding: 0 12px;
    margin: 12px 0 6px;
        }
    .g-text span{
        color:#e9edffb3!important;
    }
        /* Tables & Lists */
      .service-table{
      --bs-table-bg: transparent;
      --bs-table-color: var(--text);
      --bs-table-border-color: rgba(255,255,255,.06);
      margin:0;
    }

    .service-table thead th{
      font-size:11px;
      letter-spacing:.18em;
      text-transform:uppercase;
      color: rgba(233,237,255,.55) !important;
      border-bottom: 1px solid rgba(255,255,255,.08)!important;
      padding-top:14px;
      padding-bottom:14px;
      white-space:nowrap;
    }

    .service-table tbody td{padding:14px 4px; border-bottom: 1px solid rgba(255,255,255,.06)!important;color:#fff;font-size:14px;}

    .service-table tbody tr{
      transition: background .14s ease, transform .14s ease;
    }

    .service-table tbody tr:hover{
      background: rgba(255,255,255,.04);
      color:#fff!important;
    }
    .t-pill{
        display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, .12);
    background: rgba(255, 255, 255, .04);
    color: rgba(233, 237, 255, .86);
    font-size: 12px;
    letter-spacing: .2px;
    white-space:nowrap;
}
      .avatar-circle{
      width:28px;
      height:28px;
      border-radius: 12px;
      display:grid;
      place-items:center;
      font-size:10px;
      font-weight:800;
      letter-spacing:.7px;
      color: rgba(255,255,255,.92);
      border:1px solid rgba(255,255,255,.18);
      box-shadow: 0 10px 18px rgba(0,0,0,.35);
      background: linear-gradient(135deg, rgba(124,58,237,.85), rgba(34,211,238,.85));
    }
    .service-table .text-muted.small{
        color:#fff!important;
        font-size:12px;
    }

    /* .avatar-circle.a1{background: linear-gradient(135deg, rgba(249,115,22,.90), rgba(245,158,11,.75));}
    .avatar-cilrcle.a2{background: linear-gradient(135deg, rgba(96,165,250,.90), rgba(34,211,238,.70));} */
        .workload-card h5{
font-size: 12px;
    letter-spacing: .18em;
    color: rgba(233, 237, 255, .55);
    text-transform:uppercase;
        }
        .work-sub {
    margin-top: 8px;
    color: var(--muted2);
    font-size: 13px;
}
.progress {
     background: rgba(255,255,255,.06)!important;
      border:1px solid rgba(255,255,255,.06);
}
.progress-bar.bg-success{
background: linear-gradient(90deg, rgba(34,197,94,.95), rgba(34,211,238,.65))!important;
border-radius:99px;
}
.progress-bar.bg-warning{
background: linear-gradient(90deg, rgba(245,158,11,.95), rgba(249,115,22,.80))!important;
border-radius:99px;
}
.progress-bar.bg-danger{
  background: linear-gradient(90deg, rgba(239,68,68,.95), rgba(249,115,22,.72))!important;
  border-radius:99px;
}

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25em 0.6em;
            border-radius: 4px;
            font-weight: 600;
        }

        /* Visuals for Gantt/Schedule */
        .gantt-row {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .gantt-label {
            width: 140px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .gantt-timeline {
            flex-grow: 1;
            height: 30px;
           background: rgba(255, 255, 255, .06) !important;
    border: 1px solid rgba(255, 255, 255, .06);
            border-radius: 6px;
            position: relative;
        }

        .gantt-bar {
            position: absolute;
            height: 100%;
            border-radius: 6px;
            font-size: 0.75rem;
            color: white;
            display: flex;
            align-items: center;
            padding-left: 8px;
            white-space: nowrap;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .gantt-bar.bg-primary{
            background: linear-gradient(90deg, rgba(34,211,238,.92), rgba(124,58,237,.70))!important;
        }
        .gantt-bar.bg-danger{
            background: linear-gradient(90deg, rgba(239,68,68,.95), rgba(249,115,22,.72))!important;
        }
        .gantt-bar.bg-warning{
            background: linear-gradient(90deg, rgba(245,158,11,.95), rgba(249,115,22,.80))!important;
        }
        .dataTable, .dataTables_scrollHeadInner{
            width:100%!important;
        }
        .dataTables_info{
            color:#fff!important;
        }
        .dt-buttons .dt-button, .export-btn{
            background: linear-gradient(135deg, rgba(124, 58, 237, 1), rgba(34, 211, 238, 1))!important;
            color:#fff!important;
            border-radius:10px!important;
            padding:7px 14px;
            font-size:14px;
        }
        .dt-button:hover, .export-btn:hover{
              background: linear-gradient(135deg, rgba(124, 58, 237, 1), rgba(34, 211, 238, 1))!important;
        }
        #customer-datatable_filter input{
            color:#fff!important;
        }
           .dataTable tr th{
            white-space:nowrap;
                  font-size:11px;
      letter-spacing:.18em;
      text-transform:uppercase;
      color: rgba(233,237,255,.55) !important;
      border-bottom: 1px solid rgba(255,255,255,.08)!important;
       }
          .dataTable tr td{
             border-bottom: 1px solid rgba(255,255,255,.08)!important;
          }
           table.dataTable.stripe tbody tr.odd, table.dataTable tbody tr.odd,table.dataTable tbody tr.even,
           table.dataTable tbody tr.odd > .sorting_1, table.dataTable tbody tr.even > .sorting_1, .table>:not(caption)>*>*{
            background-color:transparent!important;
            color:#fff!important;
           }
           .action-btns{
            display:flex;
            gap:10px;
            align-items:center;
           }
             .action-btns .btn{
                white-space:nowrap;
             }
             .btn-primary{
                background: linear-gradient(90deg, rgba(34,211,238,.92), rgba(124,58,237,.70))!important;
                border:none!important;
             }
             .btn-info{
                background: linear-gradient(135deg, rgba(124, 58, 237, 1), rgba(34, 211, 238, 1))!important;
             }
             .modal-body .form-label, .modal-title, .modal-body h5, .modal-body .form-check-label, .modal-body{
                color:#000!important;
             }
             .glass-card .input-group .input-group-text, .glass-card .input-group .form-control, .glass-card .form-select, .glass-card .form-control {
                    border: 1px solid rgba(255, 255, 255, .12);
                    background: rgba(255, 255, 255, .04);
                        color: rgba(233, 237, 255, .86)!important;
             }
             /* .g-card .input-group .input-group-text, .g-card .input-group .form-control, .g-card .form-select, .g-card .form-control{
                  border: 1px solid rgba(255, 255, 255, .12);
                    background: rgba(255, 255, 255, .04);
                        color: rgba(233, 237, 255, .86)!important;
             } */
                         .g-card .input-group .input-group-text, .g-card .input-group .form-control{
                              border: 1px solid rgba(255, 255, 255, .12);
                    background: rgba(255, 255, 255, .04);
                        color: rgba(233, 237, 255, .86)!important;
                         }
             .big-input::placeholder{
                color:#fff!important;
             }
             .glass-card .form-select option,   .g-card .form-select option{
                color:#000!important;
             }
             .glass-card .input-group .form-control::placeholder,.glass-card .form-control::placeholder, .glass-card .form-label{
                  color: rgba(233, 237, 255, .86)!important;
             }
               /* .g-card .input-group .form-control::placeholder,.g-card  .form-control::placeholder, .g-card  .form-label{
                  color: rgba(233, 237, 255, .86)!important;
             } */
             .list-group-item-action.active{
                background:linear-gradient(135deg, rgba(124, 58, 237, 1), rgba(34, 211, 238, 1)) !important;
             }
             .list-group-item-action{
                background: rgba(255, 255, 255, .04);
                        color: rgba(233, 237, 255, .86);
             }
             .dataTables_filter, .dataTables_length,  .dataTables_filter input, .dataTables_length select{
                color:#fff!important;
             }
             .badge{
                padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, .12);
    background: rgba(255, 255, 255, .04);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .2px;
    color: rgba(233, 237, 255, .90)!important;
    white-space: nowrap;
             }
             .badge.bg-warning{
                border-color: rgba(245, 158, 11, .26)!important;
                background: rgba(245, 158, 11, .12)!important;
             }
             .badge.bg-success{
                border-color: rgba(34, 197, 94, .22)!important;
    background: rgba(34, 197, 94, .10)!important;
}
             .badge.bg-danger{
                    background:rgba(239, 68, 68, .5)!important;
                      border-color: rgba(239, 68, 68, .26)!important;
             }
             .btn-group .btn-outline-secondary.active,  .btn-group .btn-outline-secondary:hover{
                 background:linear-gradient(135deg, rgba(124, 58, 237, 1), rgba(34, 211, 238, 1)) !important;
             }
             .btn-group .btn-outline-secondary{
                color:#fff!important;
             }
        /* Schedule view containers */
        .schedule-view {
            display: none;
        }
        #addDeviceModal .list-group *{
            color:#000!important;
            background-color:#fff!important;
        }
        .schedule-view.active {
            display: block;
        }

        .timeline-view {
            display: none;
        }

        .timeline-view.active {
            display: block;
        }

        /* Utilities */
        .view-section {
            display: none;
        }

        .view-section.active {
            display: block;
        }

        /* Settings panes hidden by default; shown when active */
        .settings-pane {
            display: none;
        }

        .settings-pane:not(.d-none) {
            display: block;
        }

        /* Collapsible sidebar styles */
        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed .sidebar-text {
            display: none;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar.collapsed .section-header {
            text-align: center;
        }

        .main-content.collapsed {
            margin-left: 80px;

        }

        #sidebar-toggle {
            position: absolute;
            bottom: 10px;
            right: -15px;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Style for valid input fields */
        input.valid {
            border-color: #28a745;
            /* Green border for valid fields */
        }

        /* Style for invalid input fields */
        input.invalid,
        .error {
            border-color: #dc3545;
            /* Red border for invalid fields */
        }

        /* Style for error message */
        .error {
            color: #dc3545;
            /* Red color for error messages */
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Style for valid input field when it has been validated */
        input.valid:focus {
            border-color: #28a745;
            /* Green border on focus for valid fields */
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            /* Soft green box shadow */
        }

        /* Style for invalid input field when it has been validated */
        input.invalid:focus {
            border-color: #dc3545;
            /* Red border on focus for invalid fields */
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            /* Soft red box shadow */
        }

        /* Optional: Styling for form inputs when they are required */
        input[required] {
            background-color: #f9f9f9;
            /* Light gray background for required fields */
        }

        /* Custom message styling */
        .error {
            font-size: 14px;
            color: #dc3545;
        }

        /* @media (max-width: 768px) {
            .sidebar {
                left: -260px;
                transition: left 0.3s;
            } */

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.collapsed {
                margin-left: 0;
            }

            #sidebar-toggle {
                display: none;
            }

            .hamburger-menu {
                display: block;
            }
        

        @media (min-width: 769px) {
            .hamburger-menu {
                display: none;
            }
        }
    </style>
</head>

<body>
<div class="app-shell" id="shell">
    <nav class="sidebar">
        <!-- Logo -->
        <div class="d-flex align-items-center gap-2  p-2 text-primary fw-bold fs-4">
            <img src="<?= base_url('logo/assetiq logo.png') ?>" alt="assetIQ Logo"
                style="max-height: 90px; width: auto; max-width: 100%; object-fit: contain;border-radius:6px" class="nav-logo">
                <button class="sidebar-toggle d-none d-lg-block" type="button" aria-label="Toggle sidebar" id="sidebarToggle">
          <i class="bi bi-layout-sidebar-inset"></i>
           <button class="sidebar-mtoggle d-lg-none ms-auto" type="button" aria-label="Toggle sidebar" id="sidebar-mtoggle">
          <i class="bi bi-layout-sidebar-inset"></i>
        </button>
        </div>

        <!-- Navigation -->
        <div class="sidebar-nav">
            <?php $role = session('role'); ?>

            <!-- Role: Super Admin -->
            <?php if ($role === 'super_admin'): ?>
                <div class="section-header">CORE OPERATIONS</div>
                <a class="nav-link<?= url_is('admin/dashboard') ? ' active' : '' ?>" href="<?= site_url('admin/dashboard') ?>">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link<?= url_is('admin/scheduling*') ? ' active' : '' ?>" href="<?= site_url('admin/scheduling') ?>">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Scheduling</span>
                </a>
                <div class="sidebar-divider"></div>
                <div class="section-header">ASSETS & CLIENTS</div>
                <a class="nav-link<?= url_is('admin/customers*') ? ' active' : '' ?>" href="<?= site_url('admin/customers') ?>">
                    <i class="fa-solid fa-users"></i>
                    <span>Customers</span>
                </a>
                <a class="nav-link<?= url_is('admin/sites*') ? ' active' : '' ?>" href="<?= site_url('admin/sites') ?>">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>Sites</span>
                </a>
                <a class="nav-link<?= url_is('admin/equipment*') ? ' active' : '' ?>" href="<?= site_url('admin/equipment') ?>">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Equipment DB</span>
                </a>
                <a class="nav-link<?= url_is('admin/inspection-reports*') ? ' active' : '' ?>" href="<?= site_url('admin/inspection-reports') ?>">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Inspection Reports</span>
                </a>
                <a class="nav-link<?= url_is('admin/inventory*') ? ' active' : '' ?>" href="<?= site_url('admin/inventory') ?>">
                    <i class="fa-solid fa-box-open"></i>
                    <span>Inventory</span>
                </a>
                <a class="nav-link<?= url_is('admin/technicians*') ? ' active' : '' ?>" href="<?= site_url('admin/technicians') ?>">
                    <i class="fa-solid fa-users-cog"></i>
                    <span>Technicians</span>
                </a>
                <div class="sidebar-divider"></div>
                <div class="section-header">ADMIN & ANALYTICS</div>
                <a class="nav-link<?= url_is('admin/financials*') ? ' active' : '' ?>" href="<?= site_url('admin/financials') ?>">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Financials</span>
                </a>
                <a class="nav-link<?= url_is('admin/data-ops*') ? ' active' : '' ?>" href="<?= site_url('admin/data-ops') ?>">
                    <i class="fa-solid fa-database"></i>
                    <span>Data Ops</span>
                </a>
                <a class="nav-link<?= url_is('admin/settings*') ? ' active' : '' ?>" href="<?= site_url('admin/settings') ?>">
                    <i class="fa-solid fa-gears"></i>
                    <span>Settings</span>
                </a>

                <!-- Role: Customer -->
            <?php elseif ($role === 'customer'): ?>
                <a class="nav-link<?= url_is('customer/dashboard') ? ' active' : '' ?>" href="<?= site_url('customer/dashboard') ?>">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link<?= url_is('customer/sites*') ? ' active' : '' ?>" href="<?= site_url('customer/sites') ?>">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>Sites</span>
                </a>

                <!-- Role: Technician -->
            <?php elseif ($role === 'technician'): ?>
                <a class="nav-link<?= url_is('technician/dashboard') ? ' active' : '' ?>" href="<?= site_url('technician/dashboard') ?>">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link<?= url_is('technician/work-orders*') ? ' active' : '' ?>" href="<?= site_url('technician/work-orders') ?>">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>Work Orders</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- User Profile Footer -->
        <div class="mt-auto  p-2 border-top sidebar-footer">
            <div class="d-flex align-items-center gap-2">

                <?php
                $username = session('username') ?? 'SysAdmin';
                $initials = strtoupper(substr(trim($username), 0, 2));
                ?>

                <!-- Avatar circle -->
                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-semibold"
                    style="width:35px;height:35px; font-size:12px; line-height:1;">
                    <?= esc($initials) ?>
                </div>

                <!-- Name + logout -->
                <div class="small lh-sm sidebar-text">
                    <div class="fw-bold"><?= esc($username) ?></div>
                    <a href="<?= site_url('logout') ?>" class="text-white text-decoration-none">Logout</a>
                </div>

            </div>
        </div>


    </nav>
    <?= $this->include('partials/scripts') ?>
    <div class="main-content">
        <?= $this->renderSection('content') ?>
    </div>

    <?= $this->include('partials/modals') ?>
            </div>
    <script>
    (function(){
      const shell = document.getElementById('shell');
      const btn = document.getElementById('sidebarToggle');
      if(btn && shell){
        btn.addEventListener('click', () => shell.classList.toggle('sidebar-collapsed'));
      }
            })();

            document.getElementById('sidebar-mtoggle').addEventListener('click', function() {
  document.querySelector('.sidebar-nav').classList.add('show');
  document.querySelector('.sidebar-footer').classList.add('show');
});
      </script>
      

</body>

</html>