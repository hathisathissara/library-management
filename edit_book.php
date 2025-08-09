<?php
session_start();
require 'auth/db.php';

// Admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

// Get book info
if (!isset($_GET['id'])) {
    header("Location: admin_panel.php");
    exit();
}
$id = intval($_GET['id']);
$book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE id = $id"));

if (!$book) {
    die("Book not found.");
}

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $available = isset($_POST['available']) ? 1 : 0;
    $cover_photo = $book['cover_photo']; // Default to old photo

    // Handle offline image upload
    if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['cover_photo']['tmp_name'];
        $fileName = basename($_FILES['cover_photo']['name']);
        $fileSize = $_FILES['cover_photo']['size'];
        $fileType = mime_content_type($fileTmp);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($fileType, $allowedTypes) && $fileSize <= 5 * 1024 * 1024) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $newFileName = time() . '_' . $fileName;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                // Delete old photo if exists
                if ($book['cover_photo'] && file_exists($book['cover_photo'])) {
                    unlink($book['cover_photo']);
                }
                $cover_photo = $uploadPath;
            }
        }
    }

    // Handle online image URL
    if (isset($_POST['cover_photo_url']) && !empty($_POST['cover_photo_url'])) {
        $url = filter_var($_POST['cover_photo_url'], FILTER_VALIDATE_URL);
        if ($url) {
            $cover_photo = $url;
        } else {
            echo "Invalid URL.";
            exit();
        }
    }

    // Update record in database
    mysqli_query($conn, "UPDATE books SET 
        title='$title', 
        author='$author', 
        available=$available, 
        cover_photo='$cover_photo' 
        WHERE id=$id");

    header("Location: admin_panel.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Book</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
  <h2>Edit Book</h2>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($book['title']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Author</label>
      <input type="text" name="author" class="form-control" required value="<?= htmlspecialchars($book['author']) ?>">
    </div>
    <div class="form-check mb-3">
      <input type="checkbox" name="available" class="form-check-input" id="availableCheck" <?= $book['available'] ? 'checked' : '' ?>>
      <label class="form-check-label" for="availableCheck">Available</label>
    </div>
    <div class="mb-3">
      <label class="form-label">Current Cover</label><br>
      <?php if ($book['cover_photo'] && file_exists($book['cover_photo'])): ?>
        <img src="<?= $book['cover_photo'] ?>" width="100" height="140" style="object-fit: cover;">
      <?php else: ?>
        <span>No image</span>
      <?php endif; ?>
    </div>
    
    <!-- Option to add online image URL -->
    <div class="mb-3">
      <label class="form-label">Change Cover Photo (URL - optional)</label>
      <input type="text" name="cover_photo_url" class="form-control" placeholder="Enter image URL" value="<?= htmlspecialchars($book['cover_photo']) ?>">
      <small class="form-text text-muted">Enter a URL for an online image if you prefer.</small>
    </div>
    
    <!-- Option to upload offline image -->
    <div class="mb-3">
      <label class="form-label">Change Cover Photo (File - optional)</label>
      <input type="file" name="cover_photo" class="form-control" accept="image/*">
      <small class="form-text text-muted">Upload an image from your local system.</small>
    </div>
    
    <button type="submit" class="btn btn-success">Update Book</button>
    <a href="admin_panel.php" class="btn btn-secondary">Cancel</a>
  </form>
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
</body>
</html>
