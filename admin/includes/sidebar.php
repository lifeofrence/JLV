<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
requireAuth();
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Admin';
$script = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> - JenniferLamiVisuals</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><text y='14' font-size='14'>🎥</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
:root {
    --orange: #ee5007; --orange-hover: #ff6a1a; --orange-glow: rgba(238,80,7,.25);
    --dark: #0a0a0a; --dark-2: #111; --dark-3: #1a1a1a; --dark-4: #222;
    --border: #2a2a2a; --text: #eee; --text-muted: #888; --sidebar-width: 250px;
}
* { box-sizing: border-box; }
body { margin: 0; padding: 0; font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; background: var(--dark); color: var(--text); }

/* Sidebar */
.admin-wrapper { display: flex; min-height: 100vh; }
.admin-sidebar {
    width: var(--sidebar-width); background: var(--dark-2); border-right: 1px solid var(--border);
    display: flex; flex-direction: column; position: fixed; top: 0; left: 0; height: 100vh;
    z-index: 1040; transition: transform .3s ease; overflow-y: auto;
}
.sidebar-brand { padding: 16px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.sidebar-logo { width: 38px; height: 38px; object-fit: contain; border-radius: 8px; border: 1px solid var(--border); flex-shrink: 0; }
.sidebar-brand-text .brand-name { font-weight: 700; font-size: 14px; line-height: 1.2; color: #fff; display: block; }
.sidebar-brand-text .brand-sub { font-size: 10px; color: var(--orange); text-transform: uppercase; letter-spacing: 1px; }
.sidebar-nav { flex: 1; padding: 8px 0; overflow-y: auto; }
.sidebar-label { padding: 16px 16px 4px; font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); font-weight: 600; }
.sidebar-link { display: flex; align-items: center; gap: 10px; padding: 9px 16px; color: #ccc; text-decoration: none; font-size: 13px; border-left: 3px solid transparent; transition: all .15s ease; }
.sidebar-link i { width: 18px; text-align: center; color: #666; font-size: 15px; transition: color .15s; flex-shrink: 0; }
.sidebar-link:hover { background: rgba(238,80,7,.06); color: #fff; }
.sidebar-link:hover i { color: var(--orange); }
.sidebar-link.active { background: rgba(238,80,7,.1); border-left-color: var(--orange); color: var(--orange); font-weight: 600; }
.sidebar-link.active i { color: var(--orange); }
.sidebar-footer-nav { padding: 8px 0; border-top: 1px solid var(--border); flex-shrink: 0; }
.sidebar-user { padding: 8px 16px 10px; display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 12px; border-bottom: 1px solid var(--border); margin-bottom: 6px; }
.sidebar-user i { font-size: 16px; flex-shrink: 0; }
.sidebar-logout { color: #dc3545 !important; }
.sidebar-logout i { color: #dc3545 !important; }
.sidebar-logout:hover { background: rgba(220,53,69,.1) !important; }

/* Main */
.admin-main { flex: 1; margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
.admin-topbar { display: flex; align-items: center; justify-content: space-between; padding: 10px 24px; background: var(--dark-2); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 1030; flex-shrink: 0; }
.topbar-left { display: flex; align-items: center; gap: 12px; }
.topbar-title { font-weight: 600; font-size: 16px; color: #fff; }
.topbar-user { font-size: 13px; color: var(--text-muted); }
.topbar-user i { margin-right: 6px; color: var(--orange); }
.sidebar-toggle { background: none; border: 1px solid var(--border); color: #fff; font-size: 18px; padding: 4px 8px; border-radius: 6px; cursor: pointer; display: none; }
.sidebar-toggle:hover { background: var(--dark-3); }
.admin-content { padding: 24px; flex: 1; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap: 14px; margin-bottom: 24px; }
.stat-card { background: var(--dark-3); border: 1px solid var(--border); border-radius: 14px; padding: 18px; display: flex; align-items: center; gap: 14px; }
.stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.stat-icon.orange { background: rgba(238,80,7,.15); color: var(--orange); }
.stat-icon.blue { background: rgba(13,202,240,.15); color: #0dcaf0; }
.stat-icon.green { background: rgba(25,135,84,.15); color: #198754; }
.stat-icon.yellow { background: rgba(255,193,7,.15); color: #ffc107; }
.stat-info .stat-number { font-size: 22px; font-weight: 700; color: #fff; line-height: 1.1; }
.stat-info .stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; }

/* Tabs */
.tab-buttons { display: flex; gap: 8px; margin-bottom: 20px; }
.tab-btn { padding: 7px 18px; border-radius: 100px; border: 1px solid var(--border); background: var(--dark-3); color: var(--text); font-size: 13px; cursor: pointer; text-decoration: none; transition: all .15s; }
.tab-btn:hover { border-color: var(--orange); color: var(--orange); }
.tab-btn.active { background: var(--orange); border-color: var(--orange); color: #fff; }

/* Table */
.table { width: 100%; border-collapse: collapse; }
.table th { color: var(--orange); font-size: 11px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid var(--border); padding: 12px 8px; text-align: left; white-space: nowrap; }
.table td { border-bottom: 1px solid var(--border); padding: 10px 8px; vertical-align: middle; }
.table tr:hover td { background: var(--dark-4); }
.table-responsive { overflow-x: auto; border-radius: 12px; border: 1px solid var(--border); background: var(--dark-3); }
.status-new td { border-left: 3px solid #ffc107; }
.status-replied td { border-left: 3px solid #0dcaf0; }
.status-completed td { border-left: 3px solid #198754; }

/* Buttons */
.btn-admin { background: var(--orange); color: #fff; border: none; border-radius: 100px; padding: 7px 20px; font-size: 13px; font-weight: 500; cursor: pointer; display: inline-block; text-decoration: none; transition: all .15s; }
.btn-admin:hover { background: var(--orange-hover); color: #fff; box-shadow: 0 0 16px var(--orange-glow); }
.btn-admin-sm { padding: 4px 14px; font-size: 12px; }
.btn-outline-info { border: 1px solid #0dcaf0; color: #0dcaf0; background: transparent; border-radius: 8px; padding: 4px 10px; font-size: 12px; cursor: pointer; text-decoration: none; }
.btn-outline-info:hover { background: #0dcaf0; color: #000; }
.btn-outline-success { border: 1px solid #198754; color: #198754; background: transparent; border-radius: 8px; padding: 4px 10px; font-size: 12px; cursor: pointer; text-decoration: none; }
.btn-outline-success:hover { background: #198754; color: #fff; }
.btn-outline-danger { border: 1px solid #dc3545; color: #dc3545; background: transparent; border-radius: 8px; padding: 4px 10px; font-size: 12px; cursor: pointer; text-decoration: none; }
.btn-outline-danger:hover { background: #dc3545; color: #fff; }
.btn-secondary { background: var(--dark-4); color: var(--text); border: 1px solid var(--border); border-radius: 100px; padding: 7px 20px; font-size: 13px; cursor: pointer; text-decoration: none; display: inline-block; }
.btn-secondary:hover { background: var(--dark-3); }

/* Cards */
.card { background: var(--dark-3); border: 1px solid var(--border); border-radius: 14px; margin-bottom: 20px; overflow: hidden; }
.card-header { padding: 12px 16px; font-weight: 600; font-size: 14px; border-bottom: 1px solid var(--border); background: var(--dark-4); }
.card-body { padding: 16px; }

/* Forms */
.form-control, .form-select { background: var(--dark-4); border: 1px solid var(--border); color: var(--text); border-radius: 10px; padding: 9px 13px; font-size: 14px; width: 100%; }
.form-control:focus, .form-select:focus { background: var(--dark-3); border-color: var(--orange); color: #fff; outline: none; box-shadow: 0 0 0 3px var(--orange-glow); }
.form-label { font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; display: block; }
.form-check-input { accent-color: var(--orange); }
.form-control[type="file"]::file-selector-button { background: var(--orange); color: #fff; border: none; padding: 5px 14px; border-radius: 8px; font-size: 13px; cursor: pointer; }
textarea.form-control { resize: vertical; }
select.form-select { cursor: pointer; }
.row { display: flex; flex-wrap: wrap; gap: 0; }
.col-md-1 { flex: 0 0 8.33%; max-width: 8.33%; }
.col-md-2 { flex: 0 0 16.66%; max-width: 16.66%; }
.col-md-3 { flex: 0 0 25%; max-width: 25%; }
.col-md-4 { flex: 0 0 33.33%; max-width: 33.33%; }
.col-md-5 { flex: 0 0 41.66%; max-width: 41.66%; }
.col-md-6 { flex: 0 0 50%; max-width: 50%; }
.col-md-7 { flex: 0 0 58.33%; max-width: 58.33%; }
.col-md-12 { flex: 0 0 100%; max-width: 100%; }
.g-2 { gap: 8px !important; }
.g-3 { gap: 16px !important; }
.g-4 { gap: 24px !important; }
.mt-2 { margin-top: 8px; }
.mt-3 { margin-top: 16px; }
.mt-4 { margin-top: 24px; }
.mb-2 { margin-bottom: 8px; }
.mb-3 { margin-bottom: 16px; }
.mb-4 { margin-bottom: 24px; }
.me-2 { margin-right: 8px; }
.ms-auto { margin-left: auto; }
.text-center { text-align: center; }
.text-light { color: #ccc; }
.text-secondary { color: var(--text-muted); }
.w-100 { width: 100%; }
.text-white { color: #fff; }
.border-bottom { border-bottom: 1px solid var(--border); }
.border-secondary { border-color: var(--border) !important; }
.pb-3 { padding-bottom: 16px; }
.py-4 { padding-top: 24px; padding-bottom: 24px; }
.d-flex { display: flex; }
.align-items-center { align-items: center; }
.justify-content-between { justify-content: space-between; }
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }

/* Badge */
.badge { display: inline-block; padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 500; letter-spacing: .3px; }
.badge.bg-warning { background: #ffc107; color: #000; }
.badge.bg-info { background: #0dcaf0; color: #000; }
.badge.bg-success { background: #198754; color: #fff; }

/* Alerts */
.alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; }
.alert-success { background: rgba(25,135,84,.15); color: #75b798; }
.alert-danger { background: rgba(220,53,69,.15); color: #ea868f; }
.alert-info { background: rgba(13,202,240,.1); color: #6edff6; }

/* Pagination */
.pagination { display: flex; justify-content: center; gap: 4px; list-style: none; padding: 0; margin-top: 20px; }
.page-item .page-link { display: block; padding: 6px 12px; background: var(--dark-4); border: 1px solid var(--border); color: var(--text); text-decoration: none; border-radius: 8px; font-size: 13px; }
.page-item.active .page-link { background: var(--orange); border-color: var(--orange); color: #fff; }
.page-item .page-link:hover { background: var(--dark-3); }

/* Reply Card */
.booking-detail-card { background: var(--dark-3); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.booking-detail-card .detail-header { background: var(--dark-2); padding: 16px; text-align: center; border-bottom: 1px solid var(--orange); }
.booking-detail-card .detail-header h5 { color: var(--orange); text-transform: uppercase; letter-spacing: 2px; font-size: 11px; margin: 0; }
.booking-detail-card .detail-body { padding: 16px; }
.booking-detail-card .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--dark-4); }
.booking-detail-card .detail-row .label { color: var(--text-muted); font-size: 12px; }
.booking-detail-card .detail-row .value { color: var(--orange); font-size: 12px; text-align: right; max-width: 55%; }
.booking-detail-card .detail-message { margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--dark-4); }
.booking-detail-card .detail-message strong { color: var(--text-muted); font-size: 11px; display: block; margin-bottom: 6px; }
.booking-detail-card .detail-message p { color: #ccc; font-size: 13px; line-height: 1.6; white-space: pre-wrap; }

/* Section thumb */
.section-thumb { max-height: 50px; width: auto; margin-top: 8px; border-radius: 6px; border: 1px solid var(--border); }

/* Password card */
.password-card { max-width: 500px; }

/* Responsive */
@media (max-width: 991.98px) {
    .admin-sidebar { transform: translateX(-100%); }
    .admin-sidebar.show { transform: translateX(0); box-shadow: 0 0 40px rgba(0,0,0,.5); }
    .admin-main { margin-left: 0; }
    .sidebar-toggle { display: flex; align-items: center; justify-content: center; min-width: 36px; min-height: 36px; }
    .admin-topbar { padding: 10px 16px; }
    .admin-content { padding: 16px; }
    .col-md-1,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-12 { flex: 0 0 100%; max-width: 100%; }
    .topbar-title { font-size: 14px; }
    .topbar-user { font-size: 12px; }
    .topbar-user i { display: none; }
    .table th, .table td { padding: 8px 6px; font-size: 12px; white-space: nowrap; }
    .table-responsive table { min-width: 800px; }
    .card-body { padding: 12px; }
    .card-header { padding: 10px 12px; font-size: 13px; }
    .booking-detail-card .detail-body { padding: 12px; }
    .form-control, .form-select { font-size: 16px; padding: 10px 12px; }
    .pagination { gap: 2px; }
    .page-item .page-link { padding: 8px 10px; font-size: 12px; }
}
@media (max-width: 575.98px) {
    .admin-content { padding: 12px; }
    .stats-grid { grid-template-columns: 1fr; gap: 10px; margin-bottom: 16px; }
    .stat-card { padding: 12px; }
    .stat-icon { width: 36px; height: 36px; font-size: 16px; }
    .stat-info .stat-number { font-size: 18px; }
    .stat-info .stat-label { font-size: 10px; }
    .tab-buttons { flex-direction: column; gap: 6px; }
    .tab-btn { text-align: center; padding: 10px 14px; font-size: 14px; }
    .btn-admin { padding: 10px 20px; font-size: 14px; }
    .btn-secondary { padding: 10px 20px; font-size: 14px; }
    .row { gap: 8px; }
    .alert { padding: 10px 12px; font-size: 13px; }
    .booking-detail-card .detail-header { padding: 12px; }
    .booking-detail-card .detail-header h5 { font-size: 10px; }
    .booking-detail-card .detail-row .label,
    .booking-detail-card .detail-row .value { font-size: 11px; }
    .booking-detail-card .detail-message p { font-size: 12px; }
    .badge { font-size: 10px; padding: 3px 8px; }
    .inbox-view-content { padding: 12px !important; }
    .inbox-email-header h3 { font-size: 15px; }
    .inbox-email-header .meta span { display: block; margin-right: 0; margin-bottom: 4px; }
    .inbox-email-body iframe { height: 400px !important; }
}
@media (max-width: 400px) {
    .admin-content { padding: 8px; }
    .stats-grid { grid-template-columns: 1fr; }
    .stat-card { padding: 10px; gap: 10px; }
    .sidebar-toggle { min-width: 40px; min-height: 40px; }
    .table th, .table td { padding: 6px 4px; font-size: 11px; }
}
    </style>
</head>
<body>
<div class="admin-wrapper">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <img src="<?= assetUrl('images/logo.png') ?>" alt="JLV Logo" class="sidebar-logo">
            <div class="sidebar-brand-text">
                <span class="brand-name">JenniferLami Visuals</span>
                <span class="brand-sub">Admin Panel</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-label">Management</div>
            <a href="<?= assetUrl('admin/index.php') ?>" class="sidebar-link <?= isActive('/admin/index.php') ?>">
                <i class="bi-speedometer2"></i> Dashboard
            </a>

            <div class="sidebar-label">Content</div>
            <a href="<?= assetUrl('admin/cms/sections.php') ?>" class="sidebar-link <?= isActive('/admin/cms/sections.php') ?>">
                <i class="bi-layers-fill"></i> Page Sections
            </a>
            <a href="<?= assetUrl('admin/cms/pricing.php') ?>" class="sidebar-link <?= isActive('/admin/cms/pricing.php') ?>">
                <i class="bi-currency-dollar"></i> Pricing Packages
            </a>
            <a href="<?= assetUrl('admin/cms/portfolio.php') ?>" class="sidebar-link <?= isActive('/admin/cms/portfolio.php') ?>">
                <i class="bi-play-btn-fill"></i> Portfolio Videos
            </a>
            <a href="<?= assetUrl('admin/cms/navigation.php') ?>" class="sidebar-link <?= isActive('/admin/cms/navigation.php') ?>">
                <i class="bi-list-ul"></i> Navigation Menu
            </a>
            <a href="<?= assetUrl('admin/cms/social.php') ?>" class="sidebar-link <?= isActive('/admin/cms/social.php') ?>">
                <i class="bi-share"></i> Social Links
            </a>
            <a href="<?= assetUrl('admin/cms/settings.php') ?>" class="sidebar-link <?= isActive('/admin/cms/settings.php') ?>">
                <i class="bi-gear-wide-connected"></i> Settings &amp; SEO
            </a>

            <div class="sidebar-label">Communication</div>
            <a href="<?= assetUrl('admin/inbox.php') ?>" class="sidebar-link <?= isActive('/admin/inbox.php') ?>">
                <i class="bi-envelope-fill"></i> Email Inbox
            </a>
        </nav>

        <div class="sidebar-footer-nav">
            <div class="sidebar-user">
                <i class="bi-person-circle"></i>
                <span><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
            </div>
            <a href="<?= assetUrl('admin/change-password.php') ?>" class="sidebar-link <?= isActive('/admin/change-password.php') ?>">
                <i class="bi-key"></i> Change Password
            </a>
            <a href="<?= assetUrl('admin/logout.php') ?>" class="sidebar-link sidebar-logout">
                <i class="bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="bi-list"></i>
                </button>
                <span class="topbar-title"><?= htmlspecialchars($pageTitle) ?></span>
            </div>
            <div class="topbar-right">
                <span class="topbar-user"><i class="bi-person-circle"></i> <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
            </div>
        </div>
        <div class="admin-content">
