<?php
$pageTitle = 'Reply';
require_once __DIR__ . '/includes/sidebar.php';

require dirname(__DIR__) . '/PHPMailer/src/PHPMailer.php';
require dirname(__DIR__) . '/PHPMailer/src/SMTP.php';
require dirname(__DIR__) . '/PHPMailer/src/Exception.php';
require dirname(__DIR__) . '/config_smtp.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$table = $_GET['table'] ?? 'bookings';
$id = (int)($_GET['id'] ?? 0);
$allowedTables = ['bookings', 'contacts'];

if (!in_array($table, $allowedTables) || !$id) {
    die("Invalid request.");
}

$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$id]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entry) {
    die("Entry not found.");
}

$sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $replySubject = trim($_POST['reply_subject'] ?? '');
    $replyBody = trim($_POST['reply_body'] ?? '');

    if (empty($replySubject) || empty($replyBody)) {
        $error = 'Please fill in both subject and message.';
    } else {
        try {
            $mail = new PHPMailer(true);
            setupSMTP($mail);
            $mail->setFrom('info@jenniferlamivisuals.com', 'JenniferLami Visuals');
            $mail->addAddress($entry['email'], $entry['name']);
            $mail->addReplyTo('info@jenniferlamivisuals.com', 'JenniferLami Visuals');
            $mail->Subject = $replySubject;
            $mail->isHTML(true);

            $htmlBody = "
            <div style='background-color: #000; color: #fff; padding: 40px; font-family: Arial, sans-serif; text-align: center;'>
                <div style='max-width: 500px; margin: 0 auto; border: 1px solid #ee5007; padding: 40px; border-radius: 20px;'>
                    <img src='https://jenniferlamivisuals.com/images/logo.png' alt='JLV Logo' style='width: 80px; margin-bottom: 20px;'>
                    <h2 style='text-transform: uppercase; letter-spacing: 3px; font-size: 16px; margin-bottom: 5px; color: #ee5007;'>JenniferLami Visuals</h2>
                    <h1 style='font-size: 36px; margin: 10px 0; font-family: Times, serif;'>" . htmlspecialchars($entry['name']) . "</h1>
                    <p style='color: #aaa; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px;'>Reply from jenniferlamivisuals</p>
                    <div style='text-align: left; background: #111; padding: 20px; border-radius: 10px; margin-bottom: 30px; line-height: 1.8; color: #eee;'>
                        " . nl2br(htmlspecialchars($replyBody)) . "
                    </div>
                    <hr style='border-color: #333; margin: 30px 0;'>
                    <p style='font-size: 12px; color: #555;'>JenniferLami Visuals &copy; 2026<br>info@jenniferlamivisuals.com</p>
                </div>
            </div>";

            $mail->Body = $htmlBody;
            $mail->send();

            $upd = $pdo->prepare("UPDATE $table SET status = 'replied' WHERE id = ?");
            $upd->execute([$id]);

            $sent = true;
        } catch (Exception $e) {
            $error = 'Failed to send: ' . $mail->ErrorInfo;
        }
    }
}
?>

<?php if ($sent): ?>
    <div class="alert alert-success"><i class="bi-check-circle-fill"></i> Reply sent successfully! <a href="index.php?tab=<?= $table ?>">Back to dashboard</a></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><i class="bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-3" style="gap:0;">
    <div class="col-12 col-md-6">
        <div class="booking-detail-card">
            <div class="detail-header">
                <img src="<?= assetUrl('images/logo.png') ?>" alt="JLV">
                <h5><?= $table === 'bookings' ? 'Booking Details' : 'Contact Message' ?></h5>
            </div>
            <div class="detail-body">
                <div class="detail-row">
                    <span class="label">Name</span>
                    <span class="value"><?= htmlspecialchars($entry['name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Email</span>
                    <span class="value"><?= htmlspecialchars($entry['email']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Phone</span>
                    <span class="value"><?= htmlspecialchars($entry['phone'] ?: '-') ?></span>
                </div>
                <?php if ($table === 'bookings'): ?>
                    <div class="detail-row">
                        <span class="label">Package</span>
                        <span class="value"><?= htmlspecialchars($entry['package']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Event Date</span>
                        <span class="value"><?= $entry['event_date'] ? date('d-M-Y', strtotime($entry['event_date'])) : '-' ?></span>
                    </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span class="label">Submitted</span>
                    <span class="value"><?= date('d-M-Y g:i A', strtotime($entry['created_at'])) ?></span>
                </div>
                <div class="detail-message">
                    <strong>MESSAGE</strong>
                    <p><?= htmlspecialchars($entry['message'] ?: 'No message') ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi-send-fill"></i> Send Reply to <?= htmlspecialchars($entry['email']) ?>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="reply_subject" class="form-control"
                            value="Re: <?= $table === 'bookings' ? 'Your Booking Inquiry' : 'Your Message' ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="reply_body" rows="8" class="form-control" placeholder="Type your reply here..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-admin w-100"><i class="bi-send-fill"></i> Send Reply</button>
                    <a href="index.php?tab=<?= $table ?>" class="btn btn-secondary w-100 mt-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/sidebar-footer.php'; ?>
