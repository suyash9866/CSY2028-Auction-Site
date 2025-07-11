<?php
session_start();
require 'db.php';

// Check admin role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Delete category 
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM category WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: adminCategories.php');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            
            $error = "Cannot delete, auctions are held in this category.";
            header('Location: adminCategories.php?error=' . urlencode($error));
            exit;
        } else {
            die("Database error: " . $e->getMessage());
        }
    }
} else {
    header('Location: adminCategories.php');
    exit;
}
?>
