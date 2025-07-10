<?php
session_start();
require 'db.php';

// Check admin role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch all categories
$stmt = $pdo->query("SELECT * FROM category ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Categories</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <h2>Manage Categories</h2>
        <a href="addCategory.php">➕ Add New Category</a>
        <table>
            <tr>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= htmlspecialchars($category['name']) ?></td>
                    <td>
                        <a href="editCategory.php?id=<?= $category['id'] ?>">✏️ Edit</a> |
                        <a href="deleteCategory.php?id=<?= $category['id'] ?>" onclick="return confirm('Are you sure you want to delete this category?')">🗑️ Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
