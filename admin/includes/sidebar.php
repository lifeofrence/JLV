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
    <link rel="icon" type="image/png" href="<?= assetUrl('images/logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
