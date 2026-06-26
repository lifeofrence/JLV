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
    --orange: #ee5007; --orange-hover: #ff6a1a; --orange-glow: rgba(238,80,7,0.25);
    --dark: #0a0a0a; --dark-2: #111; --dark-3: #1a1a1a; --dark-4: #222;
    --border: #2a2a2a; --text: #eee; --text-muted: #888; --sidebar-width: 250px;
}
* { box-sizing: border-box; }
body {
    margin: 0; padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    background: var(--dark); color: var(--text);
}
.admin-wrapper { display: flex; min-height: 100vh; }
.admin-sidebar {
    width: var(--sidebar-width); background: var(--dark-2);
    border-right: 1px solid var(--border); display: flex; flex-direction: column;
    position: fixed; top: 0; left: 0; height: 100vh; z-index: 1040;
    transition: transform .3s ease; overflow-y: auto;
}
.sidebar-brand {
    padding: 16px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px; flex-shrink: 0;
}
.sidebar-logo { width: 38px; height: 38px; object-fit: contain; border-radius: 8px; border: 1px solid var(--border); flex-shrink: 0; }
.sidebar-brand-text .brand-name { font-weight: 700; font-size: 14px; line-height: 1.2; color: #fff; display: block; }
.sidebar-brand-text .brand-sub { font-size: 10px; color: var(--orange); text-transform: uppercase; letter-spacing: 1px; }
.sidebar-nav { flex: 1; padding: 8px 0; overflow-y: auto; }
.sidebar-label { padding: 16px 16px 4px; font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); font-weight: 600; }
.sidebar-link {
    display: flex; align-items: center; gap: 10px; padding: 9px 16px;
    color: #ccc; text-decoration: none; font-size: 13px;
    border-left: 3px solid transparent; transition: all .15s ease;
}
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
.admin-main { flex: 1; margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
.admin-topbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 24px; background: var(--dark-2);
    border-bottom: 1px solid var(--border); position: sticky; top: 0;
    z-index: 1030; flex-shrink: 0;
}
.topbar-left { display: flex; align-items: center; gap: 12px; }
.topbar-title { font-weight: 600; font-size: 16px; color: #fff; }
.topbar-user { font-size: 13px; color: var(--text-muted); }
.topbar-user i { margin-right: 6px; color: var(--orange); }
.sidebar-toggle { background: none; border: 1px solid var(--border); color: #fff; font-size: 18px; padding: 4px 8px; border-radius: 6px; cursor: pointer; display: none; }
.sidebar-toggle:hover { background: var(--dark-3); }
.admin-content { padding: 24px; flex: 1; }
@media (max-width: 991.98px) {
    .admin-sidebar { transform: translateX(-100%); }
    .admin-sidebar.show { transform: translateX(0); box-shadow: 0 0 40px rgba(0,0,0,.5); }
    .admin-main { margin-left: 0; }
    .sidebar-toggle { display: flex; align-items: center; justify-content: center; }
    .admin-topbar { padding: 10px 16px; }
    .admin-content { padding: 16px; }
}
    </style>
    <link href="<?= assetUrl('admin/style.css') ?>" rel="stylesheet">
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
