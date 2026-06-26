<?php
$pageTitle = 'Email Inbox';

$inboxError = '';
try {
    require_once __DIR__ . '/includes/sidebar.php';

    $hasImap = $pdo->query("SELECT COUNT(*) FROM cms_settings WHERE setting_key LIKE 'imap_%'")->fetchColumn();
    if (!$hasImap) {
        $defaults = [
            ['imap_host', 'mail.jenniferlamivisuals.com'],
            ['imap_port', '993'],
            ['imap_username', 'info@jenniferlamivisuals.com'],
            ['imap_password', ''],
            ['imap_encryption', 'ssl'],
        ];
        $seed = $pdo->prepare("INSERT IGNORE INTO cms_settings (setting_key, setting_value) VALUES (?,?)");
        foreach ($defaults as $d) $seed->execute($d);
    }

    $configOk = false;
    $imap_host = $imap_port = $imap_username = $imap_password = $imap_encryption = '';

    $stmt = $pdo->query("SELECT setting_key, setting_value FROM cms_settings WHERE setting_key LIKE 'imap_%'");
    foreach ($stmt as $row) {
        switch ($row['setting_key']) {
            case 'imap_host': $imap_host = $row['setting_value']; break;
            case 'imap_port': $imap_port = $row['setting_value']; break;
            case 'imap_username': $imap_username = $row['setting_value']; break;
            case 'imap_password': $imap_password = $row['setting_value']; break;
            case 'imap_encryption': $imap_encryption = $row['setting_value']; break;
        }
    }
    if (!empty($imap_host) && !empty($imap_username) && !empty($imap_password)) {
        $configOk = true;
    }
    $imap_enc = $imap_encryption ?: 'ssl';

    $emails = [];
    $selectedEmail = null;
    $error = '';
    $imapAvailable = function_exists('imap_open');

    if ($configOk && $imapAvailable && isset($_GET['view'])) {
        $msgNum = (int)$_GET['view'];
        $mailbox = '{' . $imap_host . ':' . $imap_port . '/' . $imap_enc . '}INBOX';
        $inbox = @imap_open($mailbox, $imap_username, $imap_password);
        if ($inbox) {
            $overview = @imap_fetch_overview($inbox, $msgNum);
            $structure = @imap_fetchstructure($inbox, $msgNum);
            $body = '';
            if ($structure) {
                if (isset($structure->parts) && count($structure->parts) > 0) {
                    foreach ($structure->parts as $partNo => $part) {
                        if ($part->ifsubtype && strtolower($part->subtype) === 'html') {
                            $body = @imap_fetchbody($inbox, $msgNum, $partNo + 1);
                            break;
                        }
                    }
                    if (!$body && isset($structure->parts[0])) {
                        $body = @imap_fetchbody($inbox, $msgNum, 1);
                    }
                } else {
                    $body = @imap_fetchbody($inbox, $msgNum, 1);
                }
            }
            if ($body) {
                if (isset($structure->encoding) && $structure->encoding === 3) {
                    $body = base64_decode($body);
                } elseif (isset($structure->encoding) && $structure->encoding === 4) {
                    $body = quoted_printable_decode($body);
                }
            }
            $header = $overview[0] ?? null;
            if ($header) {
                $selectedEmail = [
                    'no' => $header->msgno,
                    'from' => $header->from,
                    'fromAddr' => $header->from,
                    'subject' => $header->subject ?? '(No Subject)',
                    'date' => $header->date,
                    'body' => $body,
                ];
                if (isset($header->from) && is_array($header->from)) {
                    $fromObj = $header->from[0];
                    $selectedEmail['from'] = $fromObj->personal ?? $fromObj->mailbox . '@' . $fromObj->host;
                    $selectedEmail['fromAddr'] = $fromObj->mailbox . '@' . $fromObj->host;
                } elseif (isset($header->from)) {
                    $selectedEmail['fromAddr'] = $header->from;
                    $selectedEmail['from'] = $header->from;
                }
            }
            @imap_close($inbox);
        } else {
            $error = 'Could not open email: ' . @imap_last_error();
        }
    } elseif ($configOk && $imapAvailable) {
        $mailbox = '{' . $imap_host . ':' . $imap_port . '/' . $imap_enc . '}INBOX';
        $inbox = @imap_open($mailbox, $imap_username, $imap_password);
        if ($inbox) {
            $emailsNum = 50;
            $check = @imap_check($inbox);
            $total = $check ? $check->Nmsgs : 0;
            $start = max(1, $total - $emailsNum + 1);
            $overviews = @imap_fetch_overview($inbox, "$start:$total");
            if ($overviews) {
                foreach (array_reverse($overviews) as $h) {
                    $fromName = $h->from;
                    if (isset($h->from) && is_array($h->from)) {
                        $fromObj = $h->from[0];
                        $fromName = $fromObj->personal ?? $fromObj->mailbox . '@' . $fromObj->host;
                    }
                    $emails[] = [
                        'no' => $h->msgno,
                        'from' => $fromName,
                        'subject' => $h->subject ?? '(No Subject)',
                        'date' => $h->date,
                        'seen' => ($h->seen ?? 0) ? true : false,
                    ];
                }
            }
            @imap_close($inbox);
        } else {
            $error = 'IMAP connection failed: ' . @imap_last_error();
        }
    }
} catch (Throwable $e) {
    $inboxError = 'An unexpected error occurred: ' . $e->getMessage();
}

function _trunc($s, $len) {
    return function_exists('mb_substr') ? mb_substr($s, 0, $len) : substr($s, 0, $len);
}
?>
<style>
.inbox-layout { display: flex; gap: 0; margin: -24px; min-height: calc(100vh - 61px); }
.inbox-list { width: 380px; flex-shrink: 0; border-right: 1px solid var(--border); background: var(--dark-2); }
.inbox-list-header { padding: 16px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.inbox-list-header h5 { margin: 0; font-size: 14px; }
.inbox-emails { overflow-y: auto; height: calc(100vh - 120px); }
.inbox-item { display: block; padding: 12px 16px; border-bottom: 1px solid var(--border); text-decoration: none; color: var(--text); transition: background .15s; }
.inbox-item:hover { background: var(--dark-4); }
.inbox-item.unread { border-left: 3px solid var(--orange); background: rgba(238,80,7,.04); }
.inbox-item .from { font-weight: 600; font-size: 13px; margin-bottom: 2px; }
.inbox-item .subject { font-size: 13px; color: #aaa; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.inbox-item .date { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.inbox-view { flex: 1; overflow-y: auto; height: calc(100vh - 61px); }
.inbox-view-content { padding: 24px; }
.inbox-email-header { margin-bottom: 20px; }
.inbox-email-header h3 { font-size: 18px; margin-bottom: 8px; }
.inbox-email-header .meta { font-size: 13px; color: var(--text-muted); }
.inbox-email-header .meta span { display: inline-block; margin-right: 20px; }
.inbox-email-body { margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border); }
.inbox-email-body iframe { width: 100%; border: none; background: #fff; border-radius: 8px; }
.inbox-empty { padding: 40px; text-align: center; color: var(--text-muted); }
.inbox-empty i { font-size: 48px; margin-bottom: 12px; display: block; }
.inbox-setup { max-width: 500px; margin: 60px auto; text-align: center; }
@media (max-width: 991.98px) {
    .inbox-layout { flex-direction: column; margin: -16px; min-height: auto; }
    .inbox-list { width: 100%; border-right: none; border-bottom: 1px solid var(--border); }
    .inbox-list-header { padding: 12px 16px; }
    .inbox-list-header h5 { font-size: 13px; }
    .inbox-emails { height: auto; max-height: 260px; }
    .inbox-view { height: auto; overflow: visible; }
    .inbox-view-content { padding: 16px; }
    .inbox-email-body iframe { height: 400px; }
    .inbox-empty { padding: 24px; }
    .inbox-empty i { font-size: 36px; }
}
</style>

<?php if ($inboxError): ?>
    <div class="alert alert-danger m-3"><i class="bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($inboxError) ?></div>
<?php endif; ?>

<div class="inbox-layout">
    <div class="inbox-list">
        <div class="inbox-list-header">
            <h5><i class="bi-envelope-fill"></i> Inbox</h5>
            <a href="inbox.php" class="btn-admin btn-admin-sm"><i class="bi-arrow-clockwise"></i> Refresh</a>
        </div>
        <?php if (!$configOk): ?>
            <div class="inbox-empty" style="padding: 20px;">
                <p>IMAP not configured.</p>
                <p class="mt-2"><a href="cms/settings.php" class="btn-admin btn-admin-sm">Configure in Settings</a></p>
            </div>
        <?php elseif (!$imapAvailable): ?>
            <div class="inbox-empty" style="padding: 20px;">
                <i class="bi-exclamation-triangle-fill"></i>
                <p>PHP IMAP extension not installed.<br>Contact your hosting provider.</p>
            </div>
        <?php elseif ($error): ?>
            <div class="inbox-empty" style="padding: 20px;">
                <i class="bi-exclamation-triangle-fill"></i>
                <p style="color:#ea868f;font-size:12px;"><?= htmlspecialchars($error) ?></p>
                <a href="inbox.php" class="btn-admin btn-admin-sm mt-2">Retry</a>
            </div>
        <?php elseif (empty($emails)): ?>
            <div class="inbox-empty">
                <i class="bi-inbox"></i>
                <p>No emails found</p>
            </div>
        <?php else: ?>
            <div class="inbox-emails">
                <?php foreach ($emails as $e): ?>
                    <a href="inbox.php?view=<?= $e['no'] ?>" class="inbox-item <?= !$e['seen'] ? 'unread' : '' ?>">
                        <div class="from"><?= htmlspecialchars(_trunc($e['from'], 40)) ?></div>
                        <div class="subject"><?= htmlspecialchars(_trunc($e['subject'], 60)) ?></div>
                        <div class="date"><?= htmlspecialchars($e['date']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="inbox-view">
        <?php if ($selectedEmail): ?>
            <div class="inbox-view-content">
                <a href="inbox.php" class="btn-secondary btn-admin-sm" style="margin-bottom:16px;display:inline-block;">&larr; Back</a>
                <div class="inbox-email-header">
                    <h3><?= htmlspecialchars($selectedEmail['subject']) ?></h3>
                    <div class="meta">
                        <span><strong>From:</strong> <?= htmlspecialchars($selectedEmail['from']) ?> &lt;<?= htmlspecialchars($selectedEmail['fromAddr']) ?>&gt;</span>
                        <span><strong>Date:</strong> <?= htmlspecialchars($selectedEmail['date']) ?></span>
                    </div>
                </div>
                <div class="inbox-email-body">
                    <?php if (!empty($selectedEmail['body'])): ?>
                        <iframe srcdoc="<?= htmlspecialchars($selectedEmail['body'], ENT_QUOTES) ?>" style="height:600px;background:#fff;" onload="this.style.height=(this.contentWindow.document.body.scrollHeight+40)+'px'"></iframe>
                    <?php else: ?>
                        <p class="text-muted">(No content / Plain text email)</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($configOk && $imapAvailable && empty($error)): ?>
            <div class="inbox-empty" style="padding-top:80px;">
                <i class="bi-envelope-open"></i>
                <p>Select an email to view</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/sidebar-footer.php'; ?>
