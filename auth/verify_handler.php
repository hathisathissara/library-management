<?php
// verify_handler.php
require __DIR__ . '/db.php';

$userId = $_POST['user_id'] ?? null;
$code = trim($_POST['code'] ?? '');

function showMessage($message) {
    ?>
    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>Verification Result</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
      <div class="container mt-5">
        <div class="alert alert-info text-center" role="alert">
          <?php echo htmlspecialchars($message); ?>
        </div>
        <div class="text-center">
          <a href="login.php" class="btn btn-primary">Go to Login</a>
        </div>
      </div>
    </body>
    </html>
    <?php
    exit;
}

if (!$userId || !$code) {
    showMessage('Missing data.');
}

// get latest token for user
$stmt = $pdo->prepare("SELECT id, token_hash, expires_at, used FROM email_verifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$userId]);
$ver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ver) {
    showMessage('No verification token found. Please request a new one.');
}

if ($ver['used']) {
    showMessage('This code has already been used.');
}

$expires = new DateTime($ver['expires_at']);
$now = new DateTime();
if ($now > $expires) {
    showMessage('Code expired. Please request a new verification email.');
}

// verify code
if (!password_verify($code, $ver['token_hash'])) {
    showMessage('Invalid code.');
}

// mark user verified
$pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?")->execute([$userId]);
// mark token used
$pdo->prepare("UPDATE email_verifications SET used = 1 WHERE id = ?")->execute([$ver['id']]);

showMessage('Email verified successfully! You may now login.');
