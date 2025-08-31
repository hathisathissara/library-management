<?php
$conn = mysqli_connect("localhost", "root", "", "ezyro_38868148_library_db");
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