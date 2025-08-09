<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';
use PHPMailer\PHPMailer\PHPMailer;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $userId = $user['id'];

        // Generate 6 digit reset code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');
        $tokenHash = password_hash($code, PASSWORD_DEFAULT);

        // Insert or update reset token in email_resets table
        $stmt = $pdo->prepare("REPLACE INTO email_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $tokenHash, $expiresAt]);

        // Send email with reset code using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $config['mail']['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['mail']['smtp_user'];
            $mail->Password = $config['mail']['smtp_app_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['mail']['smtp_port'];

            $mail->setFrom($config['mail']['from_email'], $config['mail']['from_name']);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your Password Reset Code';
            $mail->Body = "Your password reset code is <b>$code</b>. It will expire in 15 minutes.";
            $mail->send();

            $_SESSION['reset_email'] = $email;
            header("Location: verify_reset_code.php");
            exit();
        } catch (Exception $e) {
            $message = "Failed to send email: " . $mail->ErrorInfo;
        }
    } else {
        $message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
            <div class="card-body">
                <h3 class="card-title text-center mb-4 text-primary">Reset Your Password</h3>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Continue</button>
                </form>
                <div class="mt-4 text-center">
                    <a href="login.php" class="text-decoration-none">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>