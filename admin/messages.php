<?php
$pageTitle = 'Messages';
require_once __DIR__ . '/includes/sidebar.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$total = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$stmt = $pdo->prepare("SELECT * FROM contacts ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = ceil($total / $perPage);
?>
<style>
.entry-list { display: flex; flex-direction: column; gap: 12px; }
.entry-card { background: var(--dark-3); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.entry-card.status-new { border-left: 3px solid #ffc107; }
.entry-card.status-replied { border-left: 3px solid #0dcaf0; }
.entry-card.status-completed { border-left: 3px solid #198754; }
.entry-top { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: var(--dark-4); border-bottom: 1px solid var(--border); }
.entry-top .entry-id { font-size: 11px; color: var(--text-muted); }
.entry-top .entry-date { font-size: 11px; color: var(--text-muted); text-align: center; flex: 1; }
.entry-body { padding: 14px; }
.entry-row { display: flex; gap: 8px; margin-bottom: 6px; font-size: 13px; }
.entry-row:last-child { margin-bottom: 0; }
.entry-row .erl { color: var(--text-muted); min-width: 70px; flex-shrink: 0; }
.entry-row .erv { color: var(--text); word-break: break-word; }
.entry-row .erv a { color: var(--orange); text-decoration: none; }
.entry-message { margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--border); }
.entry-message strong { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); display: block; margin-bottom: 4px; }
.entry-message p { font-size: 13px; color: #ccc; line-height: 1.5; margin: 0; }
.entry-actions { display: flex; gap: 6px; padding: 10px 14px; border-top: 1px solid var(--border); flex-wrap: wrap; }
@media (max-width: 575.98px) {
    .entry-row { flex-direction: column; gap: 2px; }
    .entry-row .erl { min-width: auto; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="bi-chat-dots-fill"></i> Contact Messages</h4>
    <span class="text-secondary"><?= $total ?> total</span>
</div>

<?php if (empty($rows)): ?>
    <div class="alert alert-info"><i class="bi-info-circle"></i> No messages yet.</div>
<?php else: ?>
    <div class="entry-list">
        <?php foreach ($rows as $row): ?>
            <div class="entry-card status-<?= $row['status'] ?>">
                <div class="entry-top">
                    <span class="entry-id">#<?= $row['id'] ?></span>
                    <span class="entry-date"><?= date('d-M-Y g:i A', strtotime($row['created_at'])) ?></span>
                    <span class="badge bg-<?= $row['status'] === 'new' ? 'warning text-dark' : ($row['status'] === 'replied' ? 'info' : 'success') ?>"><?= ucfirst($row['status']) ?></span>
                </div>
                <div class="entry-body">
                    <div class="entry-row">
                        <span class="erl">Name</span>
                        <span class="erv"><strong><?= htmlspecialchars($row['name']) ?></strong></span>
                    </div>
                    <div class="entry-row">
                        <span class="erl">Email</span>
                        <span class="erv"><a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a></span>
                    </div>
                    <div class="entry-row">
                        <span class="erl">Phone</span>
                        <span class="erv"><?= htmlspecialchars($row['phone']) ?></span>
                    </div>
                    <?php if ($row['message']): ?>
                        <div class="entry-message">
                            <strong>Message</strong>
                            <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="entry-actions">
                    <a href="reply.php?table=contacts&id=<?= $row['id'] ?>" class="btn btn-admin btn-admin-sm"><i class="bi-reply-fill"></i> Reply</a>
                    <?php if ($row['status'] === 'new'): ?>
                        <a href="mark.php?table=contacts&id=<?= $row['id'] ?>&status=replied&page=<?= $page ?>" class="btn btn-sm btn-outline-info">Mark Replied</a>
                    <?php elseif ($row['status'] === 'replied'): ?>
                        <a href="mark.php?table=contacts&id=<?= $row['id'] ?>&status=completed&page=<?= $page ?>" class="btn btn-sm btn-outline-success">Mark Completed</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination pagination-sm justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/sidebar-footer.php'; ?>
