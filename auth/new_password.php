<?php
session_start();
require 'db.php';

if (!isset($_SESSION['verified_reset_email'])) {
    header("Location: reset_password.php");
    exit();
}

$email = $_SESSION['verified_reset_email'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        if ($stmt->execute([$hashed, $email])) {
            unset($_SESSION['verified_reset_email']);

            // Delete reset token after successful password reset (optional)
            $stmt = $pdo->prepare("DELETE FROM email_resets WHERE user_id = (SELECT id FROM users WHERE email=?)");
            $stmt->execute([$email]);

            header("Location: login.php?reset=success");
            exit();
        } else {
            $message = "Error updating password. Try again.";
        }
    }
}
?>
<!-- Your existing new_password.php HTML form here -->


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 15px;
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }

        .btn-primary {
            background-color: #2575fc;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1a5bb8;
        }

        .text-primary {
            color: #2575fc !important;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card p-4 shadow-lg">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Reset Your Password</h3>
                <p class="text-muted">Create a strong password to secure your account.</p>
            </div>
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your new password" required>
                    <small class="text-muted">Must be at least 8 characters long.</small>
                </div>
                <div class="mb-3">
                    <label for="confirm" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm" name="confirm" placeholder="Re-enter your new password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
            <div class="text-center mt-3">
                <p class="text-muted">Remembered your password? <a href="login.php" class="text-primary fw-bold">Back to Login</a></p>
            </div>
        </div>
    </div>
</body>

</html>