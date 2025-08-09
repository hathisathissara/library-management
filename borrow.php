<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: auth/login.php');
  exit();
}
require 'auth/db.php';

$book_id = $_POST['book_id'];
$user_id = $_SESSION['user_id'];

// Check if already borrowed
$check = mysqli_query($conn, "
    SELECT * FROM borrowed_books 
    WHERE book_id = $book_id AND return_date IS NULL
");

if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "
        INSERT INTO borrowed_books (user_id, book_id) 
        VALUES ($user_id, $book_id)
    ");
    
    mysqli_query($conn, "
        UPDATE books SET available = FALSE WHERE id = $book_id
    ");
}

header("Location: user_dashboard.php");
?>
