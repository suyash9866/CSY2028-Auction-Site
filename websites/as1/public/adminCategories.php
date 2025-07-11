<?php
session_start();
require 'db.php';

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// get category from database
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
    <?php if (isset($_GET['error'])): ?>
    <p class="message" style="color: red;"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

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

<?php
require 'manageAdmins.php';
?>
<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Logout -->
    <form action="logout.php" method="post" style="text-align: center; margin: 20px;">
        <button type="submit" style="
            background-color: #d9534f; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 4px; 
            cursor: pointer;
            margin-left: 670px; 
            margin-right: 670px;">
            Logout
        </button>
    </form>
<?php endif; ?>
</body>
</html>
