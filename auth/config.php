<?php
// config.php
return [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=ezyro_38868148_library_db;charset=utf8mb4',
        'user' => 'root',
        'pass' => '',
    ],
    'mail' => [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_user' => '', // change me
        'smtp_app_password' => 'r', // change me (Google App Password)
        'from_email' => 'yourgmail@gmail.com',
        'from_name' => 'Library System'
    ],
];
