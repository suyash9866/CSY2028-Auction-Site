<?php
session_start();
require 'db.php';

// Check admin role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM category WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: adminCategories.php');
exit;
?>
