<?php
$conn = mysqli_connect("localhost", "root", "password", "database name");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// db.php
$config = require __DIR__ . '/config.php';
try {
  $pdo = new PDO(
    $config['db']['dsn'],
    $config['db']['user'],
    $config['db']['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );
} catch (PDOException $e) {
  die('DB connection failed: ' . $e->getMessage());
}
?>