<?php
require 'auth/auth.php';  // Include your auth.php for user session checks
require 'auth/db.php';    // Include your db.php for database connection

// Ensure the user is logged in
if (!isUser()) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']);
    $user_id = $_SESSION['user_id'];

    // Get the current date to mark the return date
    $return_date = date('Y-m-d');

    // Update the borrowed_books table to set the return_date
    $update_borrowed_query = "UPDATE borrowed_books SET return_date = '$return_date' WHERE book_id = $book_id AND user_id = $user_id AND return_date IS NULL";
    mysqli_query($conn, $update_borrowed_query);

    // Update the books table to mark the book as available
    $update_book_query = "UPDATE books SET available = 1 WHERE id = $book_id";
    mysqli_query($conn, $update_book_query);

    // Redirect back to the user dashboard
    header("Location: user_dashboard.php?view=borrowed");
    exit();
}
?>
