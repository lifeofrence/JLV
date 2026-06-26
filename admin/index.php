<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/sidebar.php';

$activeTab = $_GET['tab'] ?? 'bookings';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$bookingsTotal = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$contactsTotal = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$newBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='new'")->fetchColumn();
$newContacts = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='new'")->fetchColumn();

if ($activeTab === 'bookings') {
    $total = $bookingsTotal;
    $stmt = $pdo->prepare("SELECT * FROM bookings ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $table = 'bookings';
} else {
    $total = $contactsTotal;
    $stmt = $pdo->prepare("SELECT * FROM contacts ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $table = 'contacts';
}

$totalPages = ceil($total / $perPage);
?>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="bi-calendar-check-fill"></i></div>
        <div class="stat-info">
            <div class="stat-number"><?= $bookingsTotal ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="bi-clock-fill"></i></div>
        <div class="stat-info">
            <div class="stat-number"><?= $newBookings ?></div>
            <div class="stat-label">New Bookings</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi-chat-dots-fill"></i></div>
        <div class="stat-info">
            <div class="stat-number"><?= $contactsTotal ?></div>
            <div class="stat-label">Total Messages</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi-envelope-open-fill"></i></div>
        <div class="stat-info">
            <div class="stat-number"><?= $newContacts ?></div>
            <div class="stat-label">New Messages</div>
        </div>
    </div>
</div>

<div class="tab-buttons">
    <a href="?tab=bookings" class="tab-btn <?= $activeTab === 'bookings' ? 'active' : '' ?>">
        <i class="bi-calendar-check"></i> Bookings
    </a>
    <a href="?tab=contacts" class="tab-btn <?= $activeTab === 'contacts' ? 'active' : '' ?>">
        <i class="bi-chat-dots"></i> Contact Messages
    </a>
</div>

<?php if (empty($rows)): ?>
    <div class="alert alert-info"><i class="bi-info-circle"></i> No entries found.</div>
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
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-light"><?= htmlspecialchars($row['email']) ?></a></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <?php if ($table === 'bookings'): ?>
                            <td><?= htmlspecialchars($row['package']) ?></td>
                            <td><?= $row['event_date'] ? date('d-M-Y', strtotime($row['event_date'])) : '-' ?></td>
                        <?php endif; ?>
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <?= htmlspecialchars($row['message'] ?: '-') ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $row['status'] === 'new' ? 'warning text-dark' : ($row['status'] === 'replied' ? 'info' : 'success') ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="reply.php?table=<?= $table ?>&id=<?= $row['id'] ?>" class="btn btn-sm btn-admin">
                                    <i class="bi-reply-fill"></i> Reply
                                </a>
                                <?php if ($row['status'] === 'new'): ?>
                                    <a href="mark.php?table=<?= $table ?>&id=<?= $row['id'] ?>&status=replied&page=<?= $page ?>&tab=<?= $activeTab ?>" class="btn btn-sm btn-outline-info">
                                        <i class="bi-check-lg"></i>
                                    </a>
                                <?php elseif ($row['status'] === 'replied'): ?>
                                    <a href="mark.php?table=<?= $table ?>&id=<?= $row['id'] ?>&status=completed&page=<?= $page ?>&tab=<?= $activeTab ?>" class="btn btn-sm btn-outline-success">
                                        <i class="bi-check-all"></i>
                                    </a>
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

<?php require_once __DIR__ . '/includes/sidebar-footer.php'; ?>
