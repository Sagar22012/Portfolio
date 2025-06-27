<?php
require __DIR__ . '/../vendor/autoload.php'; // PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$receiving_email_address = 'sagartirkey906@gmail.com';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars($_POST['name'] ?? '');
    $email   = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? 'New Contact Message');
    $message = htmlspecialchars($_POST['message'] ?? 'No message provided');

    if (empty($email) || empty($name) || empty($message)) {
        echo "Please fill in all fields.";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sagartirkey906@gmail.com';
        $mail->Password   = 'zuirvosnjzdfklkr'; // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom($email, $name);
        $mail->addAddress($receiving_email_address);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($message);
        $mail->AltBody = $message;

        if ($mail->send()) {
            echo "OK"; // ✔️ Required by validate.js for success
        } else {
            echo "Failed to send message.";
        }

    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
} else {
    echo "Invalid request method.";
}