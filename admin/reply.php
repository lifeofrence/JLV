<?php
require_once 'config.php';
requireAuth();

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';
require '../config_smtp.php';

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
            <div style='background-color: #000; color: #fff; padding: 40px; font-family: Arial, sans-serif;'>
                <div style='max-width: 600px; margin: 0 auto; border: 1px solid #ee5007; padding: 30px; border-radius: 20px;'>
                    <h2 style='color: #ee5007; text-transform: uppercase; letter-spacing: 2px; font-size: 14px; margin-bottom: 20px;'>JenniferLami Visuals</h2>
                    <p>Hi " . htmlspecialchars($entry['name']) . ",</p>
                    <div style='line-height: 1.8;'>" . nl2br(htmlspecialchars($replyBody)) . "</div>
                    <hr style='border-color: #333; margin: 30px 0;'>
                    <p style='font-size: 12px; color: #666;'>JenniferLami Visuals<br>info@jenniferlamivisuals.com</p>
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reply - Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <span class="navbar-brand"><i class="bi-chat-dots-fill"></i> Reply</span>
            <a href="index.php?tab=<?= $table ?>" class="btn btn-outline-light btn-sm">&larr; Back</a>
        </div>
    </nav>

    <div class="container py-4">
        <?php if ($sent): ?>
            <div class="alert alert-success">Reply sent successfully! <a href="index.php?tab=<?= $table ?>">Back to dashboard</a></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-5">
                <div class="card bg-dark border-secondary mb-4">
                    <div class="card-header border-secondary"><strong>Original <?= $table === 'bookings' ? 'Booking' : 'Message' ?></strong></div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($entry['name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($entry['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($entry['phone'] ?: '-') ?></p>
                        <?php if ($table === 'bookings'): ?>
                            <p><strong>Package:</strong> <?= htmlspecialchars($entry['package']) ?></p>
                            <p><strong>Event Date:</strong> <?= $entry['event_date'] ? date('d-M-Y', strtotime($entry['event_date'])) : '-' ?></p>
                        <?php endif; ?>
                        <hr class="border-secondary">
                        <p><strong>Message:</strong></p>
                        <p style="white-space: pre-wrap;"><?= htmlspecialchars($entry['message'] ?: 'No message') ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card bg-dark border-secondary">
                    <div class="card-header border-secondary"><strong>Send Reply to <?= htmlspecialchars($entry['email']) ?></strong></div>
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
                                <textarea name="reply_body" rows="10" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-admin"><i class="bi-send-fill"></i> Send Reply</button>
                            <a href="index.php?tab=<?= $table ?>" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
