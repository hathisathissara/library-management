<?php
session_start();
require 'auth/db.php';

// Only admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: auth/login.php");
  exit();
}

// Get search query if provided
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Prepare the query with placeholders
$query = "
SELECT bb.id, u.email, b.title, b.author, bb.borrow_date, bb.return_date
FROM borrowed_books bb
JOIN users u ON bb.user_id = u.id
JOIN books b ON bb.book_id = b.id
WHERE u.email LIKE ? OR b.title LIKE ? OR b.author LIKE ?
ORDER BY bb.borrow_date DESC
";

$stmt = $conn->prepare($query);
$search_param = "%$search_query%";
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
  <title>Borrowed Books</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .navbar-brand {
      font-family: 'Courier New', Courier, monospace;
      font-weight: bold;
    }

    .table-hover tbody tr:hover {
      background-color: #f1f1f1;
    }

    .search-bar {
      border-radius: 30px;
      overflow: hidden;
    }

    .search-bar input {
      border: none;
      outline: none;
      box-shadow: none;
    }

    .search-bar button {
      border: none;
      outline: none;
    }

    .not-returned {
      font-weight: bold;
      color: #dc3545;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container-fluid">
      <a class="navbar-brand" href="admin_panel.php">📚 Library Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a href="borrowed_books.php" class="nav-link active"><i class="fas fa-book"></i> Borrowed Books</a>
          </li>
          <li class="nav-item">
            <a href="manage_users.php" class="nav-link"><i class="fas fa-users"></i> Manage Users</a>
          </li>
          <li class="nav-item">
            <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container py-5">
    <h2 class="text-center mb-4">📖 Borrowed Books</h2>

    <!-- Search Bar -->
    <form class="d-flex justify-content-center mb-4" method="GET" action="">
      <div class="input-group search-bar shadow">
        <input class="form-control" type="search" placeholder="🔍 Search by email, title, or author..." name="search" value="<?= htmlspecialchars($search_query) ?>">
        <button class="btn btn-primary" type="submit">
          <i class="fas fa-search"></i> Search
        </button>
      </div>
    </form>

    <!-- Books Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover shadow-sm">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>User Email</th>
            <th>Book Title</th>
            <th>Author</th>
            <th>Borrow Date</th>
            <th>Return Date</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1;
          while ($row = $result->fetch_assoc()) { ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= htmlspecialchars($row['author']) ?></td>
              <td><?= $row['borrow_date'] ?></td>
              <td><?= $row['return_date'] ? $row['return_date'] : '<span class="not-returned">Not returned</span>' ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
