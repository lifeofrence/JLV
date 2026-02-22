<?php
session_start();
ob_start();
error_reporting(E_ALL);

// Include PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = $_POST['booking-name'] ?? '';
        $email = $_POST['booking-email'] ?? '';
        $phone = $_POST['booking-phone'] ?? '';
        $date = $_POST['booking-date'] ?? '';
        $package = $_POST['ServicePackage'] ?? '';
        $message = $_POST['booking-message'] ?? '';

        if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($package)) {
            throw new Exception("Please fill in all required fields.");
        }

        // Email to Client
        $mailClient = new PHPMailer(true);
        try {
            $mailClient->isSMTP();
            $mailClient->Host = 'mail.jenniferlamivisuals.com';
            $mailClient->SMTPAuth = true;
            $mailClient->Username = 'info@jenniferlamivisuals.com';
            $mailClient->Password = 'C$^YqL!+oclO0tJE';
            $mailClient->SMTPSecure = 'ssl';
            $mailClient->Port = 465;

            $mailClient->setFrom('info@jenniferlamivisuals.com', 'JenniferLami Visuals');
            $mailClient->addAddress($email, $name);
            $mailClient->Subject = 'Booking Request Received';
            $mailClient->isHTML(true);

            $clientEmailContent = "
    <div style='background-color: #000; color: #fff; padding: 40px; font-family: Arial, sans-serif; text-align: center;'>
        <div style='max-width: 500px; margin: 0 auto; border: 1px solid #333; padding: 40px; border-radius: 20px;'>
            <img src='https://jenniferlamivisuals.com/images/logo.png' alt='JLV Logo' style='width: 80px; margin-bottom: 20px;'>
            <h2 style='text-transform: uppercase; letter-spacing: 3px; font-size: 16px; margin-bottom: 5px; color: #ee5007;'>Booking Received</h2>
            <h1 style='font-size: 36px; margin: 10px 0; font-family: Times, serif;'>Thank You</h1>
            <p style='color: #aaa; font-size: 14px; line-height: 1.6; margin-bottom: 30px;'>Dear $name, we have received your request and will get back to you shortly to confirm your session.</p>
            
            <div style='text-align: left; background: #111; padding: 20px; border-radius: 10px; margin-bottom: 30px;'>
                <div style='border-bottom: 1px solid #222; padding: 10px 0;'><strong>Package:</strong> <span style='color: #ee5007; float: right;'>$package</span></div>
                <div style='border-bottom: 1px solid #222; padding: 10px 0;'><strong>Date:</strong> <span style='color: #ee5007; float: right;'>$date</span></div>
                <div style='padding: 10px 0;'><strong>Phone:</strong> <span style='color: #ee5007; float: right;'>$phone</span></div>
            </div>
            
            <p style='margin-top: 30px; font-size: 12px; color: #555;'>JenniferLami Visuals &copy; 2026<br>Call: 08060425569</p>
        </div>
    </div>";

            $mailClient->Body = $clientEmailContent;
            $mailClient->send();
        } catch (Exception $e) {
            error_log('Error sending client email: ' . $mailClient->ErrorInfo);
        }

        // Email to Admin
        $mailAdmin = new PHPMailer(true);
        try {
            $mailAdmin->isSMTP();
            $mailAdmin->Host = 'mail.jenniferlamivisuals.com';
            $mailAdmin->SMTPAuth = true;
            $mailAdmin->Username = 'info@jenniferlamivisuals.com';
            $mailAdmin->Password = 'C$^YqL!+oclO0tJE';
            $mailAdmin->SMTPSecure = 'ssl';
            $mailAdmin->Port = 465;

            $mailAdmin->setFrom('info@jenniferlamivisuals.com', 'JLV Website');
            $mailAdmin->addAddress('info@jenniferlamivisuals.com', 'JenniferLami Admin');
            $mailAdmin->addReplyTo($email, $name);
            $mailAdmin->Subject = 'New Booking Alert: ' . $package;
            $mailAdmin->isHTML(true);

            $adminEmailContent = "
    <div style='background-color: #000; color: #fff; padding: 40px; font-family: Arial, sans-serif; text-align: center;'>
        <div style='max-width: 500px; margin: 0 auto; border: 1px solid #ee5007; padding: 40px; border-radius: 20px;'>
            <img src='https://jenniferlamivisuals.com/images/logo.png' alt='JLV Logo' style='width: 80px; margin-bottom: 20px;'>
            <h2 style='text-transform: uppercase; letter-spacing: 3px; font-size: 16px; margin-bottom: 5px; color: #ee5007;'>New Booking</h2>
            <h1 style='font-size: 36px; margin: 10px 0; font-family: Times, serif;'>$name</h1>
            <p style='color: #aaa; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px;'>New Request via jenniferlamivisuals Website</p>
            
            <div style='text-align: left; background: #111; padding: 20px; border-radius: 10px; margin-bottom: 30px;'>
                <div style='border-bottom: 1px solid #222; padding: 10px 0;'><strong>Name:</strong> <span style='color: #ee5007; float: right;'>$name</span></div>
                <div style='border-bottom: 1px solid #222; padding: 10px 0;'><strong>Email:</strong> <span style='color: #ee5007; float: right;'>$email</span></div>
                <div style='border-bottom: 1px solid #222; padding: 10px 0;'><strong>Phone:</strong> <span style='color: #ee5007; float: right;'>$phone</span></div>
                <div style='border-bottom: 1px solid #222; padding: 10px 0;'><strong>Package:</strong> <span style='color: #ee5007; float: right;'>$package</span></div>
                <div style='border-bottom: 1px solid #222; padding: 10px 0;'><strong>Date:</strong> <span style='color: #ee5007; float: right;'>$date</span></div>
                <div style='padding: 10px 0; margin-top: 10px;'>
                    <strong>Message:</strong><br>
                    <span style='color: #aaa; font-size: 14px; display: block; margin-top: 5px;'>$message</span>
                </div>
            </div>
            
            <p style='margin-top: 30px; font-size: 12px; color: #555;'>JenniferLami Visuals &copy; 2026</p>
        </div>
    </div>";

            $mailAdmin->Body = $adminEmailContent;
            $mailAdmin->send();

            header("Location: success.html?message=Booking request sent successfully.");
            exit;
        } catch (Exception $e) {
            throw new Exception("Error sending admin email: " . $mailAdmin->ErrorInfo);
        }

    } catch (Exception $e) {
        $errorMsg = urlencode($e->getMessage());
        header("Location: error.html?message=" . $errorMsg);
        exit;
    }
}
?>