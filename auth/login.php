<?php
session_start();
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email    = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  $query = "SELECT * FROM users WHERE email = '$email'";
  $result = mysqli_query($conn, $query);
  $user = mysqli_fetch_assoc($result);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name']; // Store username
    header("Location: " . ($user['role'] === 'admin' ? '../admin_panel.php' : '../user_dashboard.php'));
    exit();
  } else {
    $errors[] = "Invalid email or password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Library System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .login-wrapper {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding-top: 60px;
      padding-bottom: 60px;
    }

    .card {
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
      background-color: #6a11cb;
      border: none;
    }

    .btn-primary:hover {
      background-color: #2575fc;
    }

    .input-group-text {
      background-color: #6a11cb;
      color: #fff;
      border: none;
      cursor: pointer;
    }

    footer {
      background-color: #212529;
      color: #ffffff;
      text-align: center;
      padding: 20px 0;
    }

    footer a {
      color: #ffffff;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="../index.php">📚 Library System</a>
    </div>
  </nav>

  <!-- Login Card -->
  <div class="login-wrapper">
    <div class="card p-4" style="width: 100%; max-width: 400px;">
      <div class="card-body">
        <h3 class="card-title text-center mb-4">Welcome Back 👋</h3>

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
              <p class="mb-0"><?= $error ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" required class="form-control" placeholder="e.g., john.doe@example.com" />
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
              <input type="password" id="password" name="password" required class="form-control" placeholder="Your secure password" />
              <button type="button" class="input-group-text" id="togglePassword">
                <i class="fas fa-eye-slash" id="toggleIcon"></i>
              </button>
            </div>
          </div>

          <script>
            document.getElementById('togglePassword').addEventListener('click', function () {
              const passwordField = document.getElementById('password');
              const toggleIcon = document.getElementById('toggleIcon');
              const isHidden = passwordField.type === 'password';
              passwordField.type = isHidden ? 'text' : 'password';
              toggleIcon.classList.toggle('fa-eye-slash', !isHidden);
              toggleIcon.classList.toggle('fa-eye', isHidden);
            });
          </script>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
          </div>

          <div class="text-center mt-3">
            <p class="mb-1">Forgot your password? <a href="reset_password.php">Reset it</a></p>
            <p>New here? <a href="register.php">Create an account</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="container">
      <p class="mb-1">📚 <strong>Library System</strong> - Empowering Knowledge, One Book at a Time</p>
      <p class="mb-1">&copy; <?= date('Y') ?> Library System. All rights reserved.</p>
      <p class="small">
        <a href="privacy_policy.php">Privacy Policy</a> |
        <a href="terms_of_service.php">Terms of Service</a>
      </p>
    </div>
  </footer>

</body>
</html>
