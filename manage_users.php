<?php
session_start();
require 'auth/db.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// Pagination & Search
$search = '';
$limit = 5; // Users per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$where = "";
if (isset($_GET['search']) && $_GET['search'] !== '') {
  $search = mysqli_real_escape_string($conn, $_GET['search']);
  $where = "WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
}

// Count total users
$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users $where");
$total_users = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_users / $limit);

// Fetch users for current page
$sql = "SELECT * FROM users $where LIMIT $limit OFFSET $offset";
$users = mysqli_query($conn, $sql);

// Update user role
if (isset($_POST['update_role'])) {
  $user_id = intval($_POST['user_id']);
  $role = $_POST['role'];

  if ($role !== 'admin' && $role !== 'user') {
    echo "Invalid role.";
    exit();
  }

  mysqli_query($conn, "UPDATE users SET role = '$role' WHERE id = $user_id");
  header("Location: manage_users.php");
  exit();
}

// Delete user
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  mysqli_query($conn, "DELETE FROM users WHERE id = $id");
  header("Location: manage_users.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin_panel.php">
      <i class="fas fa-book"></i> Library Admin
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a href="borrowed_books.php" class="nav-link">
            <i class="fas fa-book-reader"></i> Borrowed Books
          </a>
        </li>
        <li class="nav-item">
          <a href="admin_panel.php" class="nav-link">
            <i class="fas fa-cogs"></i> Manage Books
          </a>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="nav-link">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container py-5">
  <h2 class="text-primary mb-4">Manage Users</h2>

  <!-- Search Form -->
  <form class="d-flex mb-3" method="GET">
    <input class="form-control me-2" type="search" name="search" placeholder="Search by name or email" aria-label="Search" value="<?= htmlspecialchars($search) ?>">
    <button class="btn btn-outline-primary me-2" type="submit">
      <i class="fas fa-search"></i>
    </button>
    <?php if (!empty($search)) : ?>
      <a href="manage_users.php" class="btn btn-outline-secondary">Reset</a>
    <?php endif; ?>
  </form>

  <!-- User Table -->
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($user = mysqli_fetch_assoc($users)) { ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= htmlspecialchars($user['name']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td>
            <!-- Update User Role -->
            <form method="POST" class="d-inline">
              <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
              <select name="role" class="form-select form-select-sm" required>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
              </select>
              <button type="submit" name="update_role" class="btn btn-warning btn-sm mt-1">Update</button>
            </form>
          </td>
          <td>
            <!-- Delete User -->
            <a href="?delete=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <?php if ($total_pages > 1) : ?>
    <nav>
      <ul class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
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
