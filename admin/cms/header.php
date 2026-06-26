<?php
require_once dirname(__DIR__) . '/config.php';
requireAuth();

$pageTitle = $pageTitle ?? 'CMS';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $pageTitle ?> - CMS</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><i class="bi-gear-fill"></i> CMS</a>
        <div class="d-flex">
            <a href="../index.php" class="btn btn-outline-light btn-sm me-2"><i class="bi-speedometer2"></i> Dashboard</a>
            <span class="text-light me-3 my-auto"><i class="bi-person-circle"></i> <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm my-auto">Logout</a>
        </div>
    </div>
</nav>
