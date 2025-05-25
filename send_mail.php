<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if PHPMailer exists
if (!file_exists(__DIR__ . '/PHPMailer-master/src/PHPMailer.php')) {
    echo '<div style="margin:2em auto;max-width:600px;padding:2em;border:1px solid #ccc;background:#fffbe6;color:#b94a48;font-size:1.2em;">';
    echo '<strong>PHPMailer library not found!</strong><br>Please download PHPMailer from <a href="https://github.com/PHPMailer/PHPMailer" target="_blank">github.com/PHPMailer/PHPMailer</a> and place the <code>src</code> folder in <code>guideEasy/PHPMailer-master/</code>.';
    echo '<br><br>Expected path: <code>guideEasy/PHPMailer-master/src/PHPMailer.php</code>';
    echo '<br><br><a href="admin_dashboard.php">Back to Admin Dashboard</a>';
    echo '</div>';
    exit();
}

require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['to'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change to your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'np03cs4a220007@heraldcollege.edu.np';
        $mail->Password = 'mdnn lgbd wkts pyiu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('np03cs4a220007@heraldcollege.edu.np', 'Guide Easy Admin');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br(htmlspecialchars($message));
        $mail->AltBody = $message;

        $mail->send();
        $result = '<div class="alert alert-success mt-5">Email sent successfully to ' . htmlspecialchars($to) . '.</div>';
    } catch (Exception $e) {
        $result = '<div class="alert alert-danger mt-5">Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '</div>';
    }
} else {
    $result = '<div class="alert alert-danger mt-5">Invalid request.</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Mail - Guide Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .result-card {
            max-width: 500px;
            margin: 40px auto;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.15);
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            text-align: center;
        }
        .result-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .btn-primary {
            font-size: 1.1rem;
            padding: 0.6rem 2.2rem;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="result-card">
        <?php
        if (strpos($result, 'alert-success') !== false) {
            echo '<div class="result-icon text-success"><i class="fas fa-check-circle"></i></div>';
        } else {
            echo '<div class="result-icon text-danger"><i class="fas fa-times-circle"></i></div>';
        }
        echo $result;
        ?>
        <a href="admin_dashboard.php" class="btn btn-primary mt-3">Back to Admin Dashboard</a>
    </div>
</body>
</html> 