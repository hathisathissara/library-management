<?php
session_start();
require 'auth/db.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: auth/login.php");
  exit();
}

// Handle search
$search_query = '';
if (isset($_GET['search'])) {
  $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}

// Fetch books with or without search filter
$sql = "SELECT * FROM books";
if (!empty($search_query)) {
  $sql .= " WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%'";
}
$books = mysqli_query($conn, $sql);

// Add book
if (isset($_POST['add_book'])) {
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $author = mysqli_real_escape_string($conn, $_POST['author']);
  $cover_path = '';

  if (!empty($_POST['cover_url'])) {
    $url = trim($_POST['cover_url']);
    if (filter_var($url, FILTER_VALIDATE_URL)) {
      $cover_path = $url;
    } else {
      $_SESSION['error'] = "Oops! The image URL seems invalid.";
      header("Location: admin_panel.php");
      exit();
    }
  } elseif (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['cover_photo']['tmp_name'];
    $fileName = basename($_FILES['cover_photo']['name']);
    $fileSize = $_FILES['cover_photo']['size'];
    $fileType = mime_content_type($fileTmp);

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($fileType, $allowedTypes) || $fileSize > 5 * 1024 * 1024) {
      $_SESSION['error'] = "Invalid file! Only JPG/PNG/GIF under 5MB are allowed.";
      header("Location: admin_panel.php");
      exit();
    }

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $newFileName = time() . '_' . $fileName;
    $uploadPath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
      $cover_path = $uploadPath;
    } else {
      $_SESSION['error'] = "Oh no! Failed to upload the cover photo.";
      header("Location: admin_panel.php");
      exit();
    }
  } else {
    $_SESSION['error'] = "Please upload an image or provide a valid URL.";
    header("Location: admin_panel.php");
    exit();
  }

  mysqli_query($conn, "INSERT INTO books (title, author, available, cover_photo) 
             VALUES ('$title', '$author', 1, '$cover_path')");
  $_SESSION['success'] = "Hooray! The book has been added successfully!";
  header("Location: admin_panel.php");
  exit();
}

// Delete book
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);

  $result = mysqli_query($conn, "SELECT cover_photo FROM books WHERE id = $id");
  $book = mysqli_fetch_assoc($result);
  if ($book && file_exists($book['cover_photo'])) {
    unlink($book['cover_photo']);
  }

  mysqli_query($conn, "DELETE FROM books WHERE id = $id");
  $_SESSION['success'] = "The book has been deleted successfully!";
  header("Location: admin_panel.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .card:hover {
      transform: scale(1.05);
      transition: transform 0.3s ease;
    }
    .btn-danger {
      position: relative;
    }
    .btn-danger:hover::after {
      content: "Delete";
      position: absolute;
      top: -25px;
      left: 50%;
      transform: translateX(-50%);
      background: #dc3545;
      color: #fff;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 12px;
    }
    .navbar-brand {
      font-family: 'Courier New', Courier, monospace;
      font-weight: bold;
    }
    .card-title {
      font-family: 'Georgia', serif;
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

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin_panel.php">📚 Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a href="borrowed_books.php" class="nav-link">
            <i class="fas fa-book"></i> Borrowed Books
          </a>
        </li>
        <li class="nav-item">
          <a href="manage_users.php" class="nav-link">
            <i class="fas fa-users"></i> Manage Users
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
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">📖 Manage Books</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBookModal">➕ Add New Book</button>
  </div>

  <!-- Search Bar -->
  <form class="d-flex mb-4" method="GET" action="">
    <div class="input-group shadow-sm">
      <input class="form-control border-primary" type="search" placeholder="🔍 Find your next favorite book..." aria-label="Search" name="search" value="<?= htmlspecialchars($search_query) ?>" style="border-radius: 20px 0 0 20px;">
      <button class="btn btn-primary text-white" type="submit" style="border-radius: 0 20px 20px 0;">
        <i class="fas fa-search"></i> Search
      </button>
    </div>
  </form>

  <!-- Add Book Modal -->
  <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="mb-3">
              <label for="title" class="form-label">Book Title</label>
              <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="author" class="form-label">Author</label>
              <input type="text" name="author" id="author" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="cover_photo" class="form-label">Upload Cover Photo</label>
              <input type="file" name="cover_photo" id="cover_photo" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
              <label for="cover_url" class="form-label">Or Cover Image URL</label>
              <input type="url" name="cover_url" id="cover_url" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="add_book" class="btn btn-primary">Add Book</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Book Cards -->
  <div class="row">
    <?php while ($row = mysqli_fetch_assoc($books)) { ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
          <?php if (!empty($row['cover_photo'])): ?>
            <img src="<?= htmlspecialchars($row['cover_photo']) ?>" class="card-img-top" alt="Book Cover" style="height: 200px; object-fit: cover;">
          <?php else: ?>
            <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
              No Image
            </div>
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
            <p class="card-text">Author: <?= htmlspecialchars($row['author']) ?></p>
            <p class="card-text">Available: <?= $row['available'] ? 'Yes' : 'No' ?></p>
          </div>
          <div class="card-footer d-flex justify-content-between">
            <a href="edit_book.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>">Delete</button>
          </div>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $row['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteModalLabel<?= $row['id'] ?>">Confirm Delete</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to delete the book "<strong><?= htmlspecialchars($row['title']) ?></strong>"?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger">Delete</a>
            </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
