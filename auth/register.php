<?php
session_start();
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name     = mysqli_real_escape_string($conn, $_POST['name']);
  $email    = mysqli_real_escape_string($conn, $_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
  if (mysqli_num_rows($check) > 0) {
    $errors[] = "This email is already registered. Try logging in!";
  } else {
    $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'user')";
    if (mysqli_query($conn, $query)) {
      $_SESSION['user_id'] = mysqli_insert_id($conn);
      $_SESSION['role'] = 'user';
      header("Location: user_dashboard.php");
      exit();
    } else {
      $errors[] = "Oops! Something went wrong. Please try again.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register | Library System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
    body {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
    }

    .btn-primary {
      background-color: #6a11cb;
      border: none;
    }

    .btn-primary:hover {
      background-color: #2575fc;
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 1.5rem;
    }

    .form-control {
      border-radius: 10px;
    }

    .input-group-text {
      background-color: #6a11cb;
      color: #fff;
      border: none;
      cursor: pointer;
    }

    .input-group-text:hover {
      background-color: #2575fc;
    }

    footer {
      margin-top: auto;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark w-100">
    <div class="container-fluid">
      <a class="navbar-brand" href="../index.php">📚 Library System</a>
    </div>
  </nav>

  <!-- Register Card -->
  <div class="container d-flex justify-content-center align-items-center flex-grow-1">
    <div class="card shadow-lg p-4" style="width: 30rem;">
      <div class="card-body">
        <h2 class="card-title text-center mb-4">Create Your Account</h2>

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
              <p><?= $error ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="register_handler.php">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" required class="form-control" placeholder="John Doe">
          </div>
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" required class="form-control" placeholder="example@domain.com">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input type="password" name="password" id="password" required class="form-control" placeholder="Create a strong password">
              <button type="button" class="btn btn-outline-primary input-group-text" id="togglePassword">
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
            <button type="submit" class="btn btn-primary btn-lg">Sign Up</button>
          </div>

          <div class="text-center mt-3">
            <p>Already have an account? <a href="login.php" class="text-decoration-none"><strong>Login</strong></a></p>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white py-4">
    <div class="container text-center">
      <p class="mb-1">📚 <strong>Library System</strong> - Empowering Knowledge, One Book at a Time</p>
      <p class="small mb-0">&copy; <?= date('Y') ?> Library System. All rights reserved.</p>
      <p class="small">
        <a href="privacy_policy.php" class="text-white text-decoration-none">Privacy Policy</a> |
        <a href="terms_of_service.php" class="text-white text-decoration-none">Terms of Service</a>
      </p>
    </div>
  </footer>
</body>

</html>
