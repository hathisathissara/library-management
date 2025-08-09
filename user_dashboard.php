<?php
require 'auth/auth.php';
require 'auth/db.php';

if (!isUser()) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info from database
$user_query = "SELECT name, email FROM users WHERE id = $user_id LIMIT 1";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get view from GET parameter: default is 'available'
$view = isset($_GET['view']) ? $_GET['view'] : 'available';

// Get search query if provided
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Fetch available books with search
$books_result = null;
if ($view === 'available') {
    $books_query = "SELECT * FROM books WHERE available = 1 AND (title LIKE '%$search_query%' OR author LIKE '%$search_query%')";
    $books_result = mysqli_query($conn, $books_query);
}

// Fetch borrowed books
$borrowed_result = null;
if ($view === 'borrowed') {
    $borrowed_query = "
        SELECT b.title, b.author, bb.book_id, bb.borrow_date, bb.return_date 
        FROM borrowed_books bb
        JOIN books b ON bb.book_id = b.id
        WHERE bb.user_id = $user_id
        AND (b.title LIKE '%$search_query%' OR b.author LIKE '%$search_query%')
    ";
    $borrowed_result = mysqli_query($conn, $borrowed_query);
}

// Handle user information update
if (isset($_POST['update_info'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password_input = $_POST['password'];

    if (!empty($password_input)) {
        $password = password_hash($password_input, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET name='$name', email='$email', password='$password' WHERE id=$user_id";
    } else {
        $update_query = "UPDATE users SET name='$name', email='$email' WHERE id=$user_id";
    }

    if (mysqli_query($conn, $update_query)) {
        // Update session variables
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        header("Location: user_dashboard.php");
        exit();
    } else {
        echo "Error updating information: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="user_dashboard.php">📚 Hello, <?= htmlspecialchars($user['name']) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?= $view === 'available' ? 'active' : '' ?>" href="?view=available"><i class="fas fa-book-open"></i> Available Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $view === 'borrowed' ? 'active' : '' ?>" href="?view=borrowed"><i class="fas fa-book"></i> Borrowed Books</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-outline-light ms-3" data-bs-toggle="modal" data-bs-target="#updateModal"><i class="fas fa-user-edit"></i> Update Info</button>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Update Info Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">🔧 Update Your Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">👤 Name</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">📧 Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">🔒 New Password (optional)</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_info" class="btn btn-primary">💾 Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-4">
        <form class="d-flex mb-4" method="GET">
            <input type="hidden" name="view" value="<?= $view ?>">
            <div class="input-group shadow-sm">
                <input class="form-control border-primary" type="search" name="search" placeholder="🔍 Search for books or authors..." value="<?= htmlspecialchars($search_query) ?>" style="border-radius: 20px 0 0 20px;">
                <button class="btn btn-primary" type="submit" style="border-radius: 0 20px 20px 0;">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
        <?php if ($view === 'available'): ?>
            <h2>📚 Available Books</h2>
            <div class="row">
                <?php while ($row = mysqli_fetch_assoc($books_result)) { ?>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 shadow-sm">
                            <?php if (!empty($row['cover_photo']) && filter_var($row['cover_photo'], FILTER_VALIDATE_URL)): ?>
                                <img src="<?= htmlspecialchars($row['cover_photo']) ?>" class="card-img-top" alt="Cover" style="height: 250px; object-fit: cover;">
                            <?php elseif (!empty($row['cover_photo']) && file_exists($row['cover_photo'])): ?>
                                <img src="<?= htmlspecialchars($row['cover_photo']) ?>" class="card-img-top" alt="Cover" style="height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <img src="default_cover.jpg" class="card-img-top" alt="No cover" style="height: 250px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                                <p class="card-text">✍️ Author: <?= htmlspecialchars($row['author']) ?></p>
                                <form method="POST" action="borrow.php">
                                    <input type="hidden" name="book_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-primary">📥 Borrow</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php elseif ($view === 'borrowed'): ?>
            <h2>📦 Your Borrowed Books</h2>
            <?php if (mysqli_num_rows($borrowed_result) > 0): ?>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>📖 Title</th>
                            <th>✍️ Author</th>
                            <th>📅 Borrow Date</th>
                            <th>📅 Return Date</th>
                            <th>🔄 Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($borrowed = mysqli_fetch_assoc($borrowed_result)) { ?>
                            <tr>
                                <td><?= htmlspecialchars($borrowed['title']) ?></td>
                                <td><?= htmlspecialchars($borrowed['author']) ?></td>
                                <td><?= $borrowed['borrow_date'] ?></td>
                                <td><?= $borrowed['return_date'] ? $borrowed['return_date'] : '<span class="text-danger">Not returned</span>' ?></td>
                                <td>
                                    <?php if (!$borrowed['return_date']) { ?>
                                        <form method="POST" action="return_book.php">
                                            <input type="hidden" name="book_id" value="<?= $borrowed['book_id'] ?>">
                                            <button type="submit" class="btn btn-warning btn-sm">↩️ Return</button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>📭 You haven't borrowed any books yet.</p>
            <?php endif; ?>
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