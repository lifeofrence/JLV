<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/sidebar.php';

$bookingsTotal = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$contactsTotal = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$newBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='new'")->fetchColumn();
$newContacts = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='new'")->fetchColumn();
$recentBookings = $pdo->query("SELECT id, name, package, status, created_at FROM bookings ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$recentMessages = $pdo->query("SELECT id, name, status, created_at FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
.action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 28px; }
.action-card { background: var(--dark-3); border: 1px solid var(--border); border-radius: 14px; padding: 20px; text-decoration: none; color: var(--text); display: flex; align-items: center; gap: 14px; transition: all .15s; }
.action-card:hover { background: var(--dark-4); border-color: var(--orange); color: var(--text); transform: translateY(-2px); }
.action-card .ac-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.action-card .ac-text { flex: 1; }
.action-card .ac-text .ac-title { font-weight: 600; font-size: 14px; }
.action-card .ac-text .ac-sub { font-size: 11px; color: var(--text-muted); }
.action-card .ac-arrow { color: var(--text-muted); font-size: 14px; }
.action-card:hover .ac-arrow { color: var(--orange); }

.recent-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.recent-card { background: var(--dark-3); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.recent-card .rc-header { padding: 12px 16px; font-size: 13px; font-weight: 600; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.recent-card .rc-header a { font-size: 11px; font-weight: 400; }
.recent-card .rc-body { padding: 4px 0; }
.recent-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 1px solid var(--dark-4); }
.recent-item:last-child { border-bottom: none; }
.recent-item .ri-left .ri-name { font-size: 13px; font-weight: 500; }
.recent-item .ri-left .ri-meta { font-size: 11px; color: var(--text-muted); }
.recent-item .ri-right { display: flex; align-items: center; gap: 8px; }
@media (max-width: 767.98px) {
    .recent-grid { grid-template-columns: 1fr; }
    .action-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 400px) {
    .action-grid { grid-template-columns: 1fr; }
}
</style>

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

<div class="action-grid">
    <a href="bookings.php" class="action-card">
        <div class="ac-icon orange"><i class="bi-calendar-check-fill"></i></div>
        <div class="ac-text">
            <div class="ac-title">View Bookings</div>
            <div class="ac-sub"><?= $newBookings ?> new, <?= $bookingsTotal ?> total</div>
        </div>
        <i class="bi-chevron-right ac-arrow"></i>
    </a>
    <a href="messages.php" class="action-card">
        <div class="ac-icon blue"><i class="bi-chat-dots-fill"></i></div>
        <div class="ac-text">
            <div class="ac-title">View Messages</div>
            <div class="ac-sub"><?= $newContacts ?> new, <?= $contactsTotal ?> total</div>
        </div>
        <i class="bi-chevron-right ac-arrow"></i>
    </a>
    <a href="inbox.php" class="action-card">
        <div class="ac-icon green"><i class="bi-envelope-fill"></i></div>
        <div class="ac-text">
            <div class="ac-title">Email Inbox</div>
            <div class="ac-sub">View emails from your mailbox</div>
        </div>
        <i class="bi-chevron-right ac-arrow"></i>
    </a>
    <a href="cms/settings.php" class="action-card">
        <div class="ac-icon" style="background:rgba(255,193,7,.15);color:#ffc107;"><i class="bi-gear-wide-connected"></i></div>
        <div class="ac-text">
            <div class="ac-title">Site Settings</div>
            <div class="ac-sub">SEO, IMAP, general config</div>
        </div>
        <i class="bi-chevron-right ac-arrow"></i>
    </a>
</div>

<div class="recent-grid">
    <div class="recent-card">
        <div class="rc-header">
            Recent Bookings
            <a href="bookings.php" class="text-secondary" style="text-decoration:none;">View all &rarr;</a>
        </div>
        <div class="rc-body">
            <?php if (empty($recentBookings)): ?>
                <div class="recent-item"><span class="text-muted">No bookings yet</span></div>
            <?php else: ?>
                <?php foreach ($recentBookings as $b): ?>
                    <div class="recent-item">
                        <div class="ri-left">
                            <div class="ri-name"><?= htmlspecialchars($b['name']) ?></div>
                            <div class="ri-meta"><?= htmlspecialchars($b['package']) ?> &middot; <?= date('d-M', strtotime($b['created_at'])) ?></div>
                        </div>
                        <div class="ri-right">
                            <span class="badge bg-<?= $b['status'] === 'new' ? 'warning text-dark' : ($b['status'] === 'replied' ? 'info' : 'success') ?>"><?= ucfirst($b['status']) ?></span>
                            <a href="reply.php?table=bookings&id=<?= $b['id'] ?>" class="btn-sm btn-admin" style="text-decoration:none;padding:2px 10px;font-size:11px;">Reply</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="recent-card">
        <div class="rc-header">
            Recent Messages
            <a href="messages.php" class="text-secondary" style="text-decoration:none;">View all &rarr;</a>
        </div>
        <div class="rc-body">
            <?php if (empty($recentMessages)): ?>
                <div class="recent-item"><span class="text-muted">No messages yet</span></div>
            <?php else: ?>
                <?php foreach ($recentMessages as $m): ?>
                    <div class="recent-item">
                        <div class="ri-left">
                            <div class="ri-name"><?= htmlspecialchars($m['name']) ?></div>
                            <div class="ri-meta"><?= date('d-M g:i A', strtotime($m['created_at'])) ?></div>
                        </div>
                        <div class="ri-right">
                            <span class="badge bg-<?= $m['status'] === 'new' ? 'warning text-dark' : ($m['status'] === 'replied' ? 'info' : 'success') ?>"><?= ucfirst($m['status']) ?></span>
                            <a href="reply.php?table=contacts&id=<?= $m['id'] ?>" class="btn-sm btn-admin" style="text-decoration:none;padding:2px 10px;font-size:11px;">Reply</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/sidebar-footer.php'; ?>
