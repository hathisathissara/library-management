<?php
session_start();
require 'auth/db.php';

// Handle search query
$search_query = '';
if (isset($_GET['search'])) {
  $search_query = mysqli_real_escape_string($conn, $_GET['search']);
  $books = mysqli_query($conn, "SELECT * FROM books WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%'");
} else {
  $books = mysqli_query($conn, "SELECT * FROM books");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Library System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .navbar-brand {
      font-family: 'Georgia', serif;
      font-size: 1.5rem;
    }
    .card {
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s;
    }
    .card:hover {
      transform: scale(1.05);
    }
    .card-title {
      font-weight: bold;
    }
    .btn-primary {
      background-color: #007bff;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <!-- 🔝 Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php"><i class="bi bi-book"></i> Library System</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="admin_panel.php"><i class="bi bi-gear"></i> Admin Panel</a>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a class="nav-link" href="my_borrowed.php"><i class="bi bi-bookmark"></i> My Borrowed Books</a>
              </li>
            <?php endif; ?>
          <?php endif; ?>
        </ul>
        <form class="d-flex" method="GET" action="">
          <input class="form-control me-2" type="search" name="search" placeholder="Search books..." value="<?= htmlspecialchars($search_query) ?>" aria-label="Search">
          <button class="btn btn-outline-light" type="submit"><i class="bi bi-search"></i></button>
        </form>
        <ul class="navbar-nav">
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="auth/login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="auth/register.php"><i class="bi bi-person-plus"></i> Register</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- 📚 Main Content -->
  <div class="container py-4">
    <h2 class="mb-4 text-center">Explore Our Library Collection</h2>
    <div class="row">
      <?php while($row = mysqli_fetch_assoc($books)) { ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <?php if (!empty($row['cover_photo']) && filter_var($row['cover_photo'], FILTER_VALIDATE_URL)): ?>
              <img src="<?= htmlspecialchars($row['cover_photo']) ?>" class="card-img-top" alt="Cover" style="height: 250px; object-fit: cover;">
            <?php elseif (!empty($row['cover_photo']) && file_exists($row['cover_photo'])): ?>
              <img src="<?= htmlspecialchars($row['cover_photo']) ?>" class="card-img-top" alt="Cover" style="height: 250px; object-fit: cover;">
            <?php else: ?>
              <img src="default_cover.jpg" class="card-img-top" alt="No cover" style="height: 250px; object-fit: cover;">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
              <p class="card-text"><i class="bi bi-person"></i> Author: <?= htmlspecialchars($row['author']) ?></p>
              <?php if ($row['available']): ?>
                <form method="POST" action="borrow.php">
                  <input type="hidden" name="book_id" value="<?= $row['id'] ?>">
                  <button type="submit" class="btn btn-primary"><i class="bi bi-bookmark-plus"></i> Borrow</button>
                </form>
              <?php else: ?>
                <button class="btn btn-secondary" disabled><i class="bi bi-x-circle"></i> Not Available</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
  <footer class="bg-dark text-white py-4 mt-5">
  <div class="container text-center">
    <p class="mb-1">📚 <strong>Library System</strong> - Empowering Knowledge, One Book at a Time</p>
    <p class="small mb-0">&copy; <?= date('Y') ?> Library System. All rights reserved.</p>
    <p class="small">
      <a href="privacy_policy.php" class="text-white text-decoration-none">Privacy Policy</a> | 
      <a href="terms_of_service.php" class="text-white text-decoration-none">Terms of Service</a>
    </p>
  </div>
</footer>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
