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
            <div style='background-color: #000; color: #fff; padding: 40px; font-family: Arial, sans-serif; text-align: center;'>
                <div style='max-width: 500px; margin: 0 auto; border: 1px solid #ee5007; padding: 40px; border-radius: 20px;'>
                    <img src='https://jenniferlamivisuals.com/images/logo.png' alt='JLV Logo' style='width: 80px; margin-bottom: 20px;'>
                    <h2 style='text-transform: uppercase; letter-spacing: 3px; font-size: 16px; margin-bottom: 5px; color: #ee5007;'>JenniferLami Visuals</h2>
                    <h1 style='font-size: 28px; margin: 10px 0; font-family: Times, serif; color: #fff;'>Hi " . htmlspecialchars($entry['name']) . ",</h1>
                    <div style='text-align: left; background: #111; padding: 20px; border-radius: 10px; margin: 20px 0; line-height: 1.8; color: #eee;'>
                        " . nl2br(htmlspecialchars($replyBody)) . "
                    </div>
                    <hr style='border-color: #333; margin: 30px 0;'>
                    <p style='font-size: 12px; color: #555;'>JenniferLami Visuals<br>info@jenniferlamivisuals.com</p>
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
    <style>
        .booking-detail-card {
            background: #111;
            border: 1px solid #333;
            border-radius: 16px;
            overflow: hidden;
        }
        .booking-detail-card .detail-header {
            background: #000;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #ee5007;
        }
        .booking-detail-card .detail-header img {
            width: 60px;
            margin-bottom: 8px;
        }
        .booking-detail-card .detail-header h5 {
            color: #ee5007;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 12px;
            margin: 0;
        }
        .booking-detail-card .detail-body {
            padding: 20px;
        }
        .booking-detail-card .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #222;
        }
        .booking-detail-card .detail-row:last-child {
            border-bottom: none;
        }
        .booking-detail-card .detail-row .label {
            color: #888;
            font-size: 13px;
        }
        .booking-detail-card .detail-row .value {
            color: #ee5007;
            font-size: 13px;
            text-align: right;
            max-width: 55%;
        }
        .booking-detail-card .detail-message {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #222;
        }
        .booking-detail-card .detail-message strong {
            color: #888;
            font-size: 12px;
            display: block;
            margin-bottom: 8px;
        }
        .booking-detail-card .detail-message p {
            color: #ccc;
            font-size: 13px;
            line-height: 1.6;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <span class="navbar-brand"><i class="bi-chat-dots-fill"></i> Reply</span>
            <a href="index.php?tab=<?= $table ?>" class="btn btn-outline-light btn-sm">&larr; Back to Dashboard</a>
        </div>
    </nav>

    <div class="container py-4">
        <?php if ($sent): ?>
            <div class="alert alert-success">Reply sent successfully! <a href="index.php?tab=<?= $table ?>">Back to dashboard</a></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left: Original Booking Details -->
            <div class="col-md-5">
                <div class="booking-detail-card">
                    <div class="detail-header">
                        <img src="../images/logo.png" alt="JLV">
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

            <!-- Right: Reply Form -->
            <div class="col-md-7">
                <div class="card bg-dark border-secondary">
                    <div class="card-header border-secondary">
                        <strong><i class="bi-send-fill"></i> Send Reply to <?= htmlspecialchars($entry['email']) ?></strong>
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
                                <textarea name="reply_body" rows="12" class="form-control" placeholder="Type your reply here..." required></textarea>
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
