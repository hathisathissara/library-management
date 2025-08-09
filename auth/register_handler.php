<?php
// register_handler.php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
    die('Fill all fields.');
}

// check existing email
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die('Email already registered.');
}

// create user
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
$stmt->execute([$name, $email, $passwordHash]);
$userId = $pdo->lastInsertId();

// generate 6-digit code
$code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expiresAt = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');
$tokenHash = password_hash($code, PASSWORD_DEFAULT);

// store token (hashed)
$stmt = $pdo->prepare("INSERT INTO email_verifications (user_id, token_hash, expires_at) VALUES (?,?,?)");
$stmt->execute([$userId, $tokenHash, $expiresAt]);

// send email via Gmail SMTP (PHPMailer)
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $config['mail']['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['mail']['smtp_user'];
    $mail->Password = $config['mail']['smtp_app_password']; // app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $config['mail']['smtp_port'];

    $mail->setFrom($config['mail']['from_email'], $config['mail']['from_name']);
    $mail->addAddress($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Your verification code';
    $mail->Body = "Hello {$name},<br><br>Your verification code is: <b>{$code}</b><br>It will expire in 15 minutes.";
    $mail->AltBody = "Your verification code is: {$code} (expires in 15 minutes)";

    $mail->send();
    // redirect to verify page with user id (or show message)
    header("Location: verify.php?user_id={$userId}");
    exit;
} catch (Exception $e) {
    // In production, log error instead of echoing
    echo "Mail could not be sent. Error: " . $mail->ErrorInfo;
}
