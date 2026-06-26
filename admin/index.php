<?php
require_once 'config.php';
requireAuth();

$activeTab = $_GET['tab'] ?? 'bookings';

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

if ($activeTab === 'bookings') {
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM bookings");
    $total = $totalStmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM bookings ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $table = 'bookings';
} else {
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM contacts");
    $total = $totalStmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM contacts ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $table = 'contacts';
}

$totalPages = ceil($total / $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - JenniferLamiVisuals</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <span class="navbar-brand"><img src="../images/logo.png" alt="JLV" style="height: 30px;"> Admin Panel</span>
            <div class="d-flex">
                <span class="text-light me-3 my-auto"><i class="bi-person-circle"></i> <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm my-auto">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><?= $activeTab === 'bookings' ? 'Bookings' : 'Contact Messages' ?></h4>
            <div>
                <a href="?tab=bookings" class="btn <?= $activeTab === 'bookings' ? 'btn-admin' : 'btn-outline-secondary' ?> btn-sm">Bookings</a>
                <a href="?tab=contacts" class="btn <?= $activeTab === 'contacts' ? 'btn-admin' : 'btn-outline-secondary' ?> btn-sm">Contact Messages</a>
            </div>
        </div>

        <?php if (empty($rows)): ?>
            <div class="alert alert-info">No entries found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <?php if ($table === 'bookings'): ?>
                                <th>Package</th>
                                <th>Event Date</th>
                            <?php endif; ?>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr class="status-<?= $row['status'] ?>">
                                <td><?= $row['id'] ?></td>
                                <td><?= date('d-M-Y', strtotime($row['created_at'])) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-light"><?= htmlspecialchars($row['email']) ?></a></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <?php if ($table === 'bookings'): ?>
                                    <td><?= htmlspecialchars($row['package']) ?></td>
                                    <td><?= $row['event_date'] ? date('d-M-Y', strtotime($row['event_date'])) : '-' ?></td>
                                <?php endif; ?>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($row['message'] ?: '-') ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $row['status'] === 'new' ? 'warning text-dark' : ($row['status'] === 'replied' ? 'info' : 'success') ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="reply.php?table=<?= $table ?>&id=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Reply"><i class="bi-reply-fill"></i>Reply</a>
                                        <?php if ($row['status'] === 'new'): ?>
                                            <a href="mark.php?table=<?= $table ?>&id=<?= $row['id'] ?>&status=replied&page=<?= $page ?>&tab=<?= $activeTab ?>" class="btn btn-sm btn-info" title="Mark as Replied"><i class="bi-check-lg"></i>Mark as Replied</a>
                                        <?php elseif ($row['status'] === 'replied'): ?>
                                            <a href="mark.php?table=<?= $table ?>&id=<?= $row['id'] ?>&status=completed&page=<?= $page ?>&tab=<?= $activeTab ?>" class="btn btn-sm btn-success" title="Mark as Completed"><i class="bi-check-all"></i>Mark as Completed</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination pagination-sm justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?tab=<?= $activeTab ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
