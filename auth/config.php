<?php
// config.php
return [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=database name;charset=utf8mb4',
        'user' => 'root',
        'pass' => '',
    ],
    'mail' => [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_user' => 'your-email',       // change me
        'smtp_app_password' => 'password', // change me (Google App Password)
        'from_email' => 'yourgmail@gmail.com',
        'from_name' => 'Library System'
    ],
];
