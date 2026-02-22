<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form inputs
    $name = trim($_POST['contact-name'] ?? '');
    $email = filter_var($_POST['contact-email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['contact-phone'] ?? '');
    $message = trim($_POST['contact-message'] ?? '');

    // Check if inputs are not empty
    if (empty($name) || empty($email) || empty($message)) {
        header("Location: error.html?message=Please fill in all required fields.");
        exit;
    }

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: error.html?message=Invalid email format.");
        exit;
    }

    // SMTP Configuration
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'mail.jenniferlamivisuals.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@jenniferlamivisuals.com';
        $mail->Password = 'C$^YqL!+oclO0tJE';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Set the recipient email address
        $to1 = "info@jenniferlamivisuals.com"; // Replace with your email

        // Set the email subject
        $subject = "New Contact Message from $name";

        // Admin Email HTML Content
        $adminEmailContent = "
    <div style='background-color: #000; color: #fff; padding: 40px; font-family: Arial, sans-serif; text-align: center;'>
        <div style='max-width: 500px; margin: 0 auto; border: 1px solid #ee5007; padding: 40px; border-radius: 20px;'>
            <h2 style='text-transform: uppercase; letter-spacing: 3px; font-size: 16px; margin-bottom: 5px; color: #ee5007;'>Contact Message</h2>
            <p style='text-transform: uppercase; color: #aaa; font-size: 12px; letter-spacing: 2px; margin-bottom: 30px;'>Inquiry from jenniferlamivisuals Website</p>
            
            <div style='text-align: left; background: #111; padding: 20px; border-radius: 10px;'>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Phone:</strong> $phone</p>
                <div style='margin-top: 20px; padding-top: 10px; border-top: 1px solid #222;'>
                    <strong>Message:</strong><br>
                    <span style='color: #eee; line-height: 1.6;'>$message</span>
                </div>
            </div>
            
            <p style='margin-top: 30px; font-size: 12px; color: #444;'>JenniferLami Visuals &copy; 2026</p>
        </div>
    </div>";

        // Email headers
        $mail->setFrom('info@jenniferlamivisuals.com', 'jenniferlamivisuals Website'); // Using verified sender
        $mail->addReplyTo($email, $name);
        $mail->addAddress($to1);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $adminEmailContent;

        // Send email
        $mail->send();

        // Redirect back with success message
        header("Location: success.html?message=Email sent successfully.");
        exit;
    } catch (Exception $e) {
        // Redirect back with error message
        header("Location: error.html?message=Error sending email: " . $mail->ErrorInfo);
        exit;
    }
} else {
    // If form not submitted, redirect back to form
    header("Location: error.html?message=Method not allowed.");
    exit;
}
?>