<?php
// Database connection 
$host = 'db'; // Docker service 
$db = 'assignment1'; // Database
$user = 'root'; // user
$pass = 'root'; // password
$charset = 'utf8mb4'; // tf8 support

// PDO DNS
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// error style 
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    // PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>
