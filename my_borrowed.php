<?php
session_start();
require 'auth/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "
    SELECT bb.id AS borrow_id, b.title, b.author, bb.borrow_date, bb.return_date 
    FROM borrowed_books bb
    JOIN books b ON bb.book_id = b.id
    WHERE bb.user_id = $user_id AND bb.return_date IS NULL
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Borrowed Books</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
  <div class="container">
    <h2>My Borrowed Books</h2>
    <table class="table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Author</th>
          <th>Borrow Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $row['title'] ?></td>
            <td><?= $row['author'] ?></td>
            <td><?= $row['borrow_date'] ?></td>
            <td>
              <form method="POST" action="return_book.php">
                <input type="hidden" name="borrow_id" value="<?= $row['borrow_id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Return</button>
              </form>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
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
