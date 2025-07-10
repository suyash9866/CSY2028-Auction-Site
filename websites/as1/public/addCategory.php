<?php
session_start();
require 'db.php';

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);

    // Insert category 
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO category (name) VALUES (?)");
        $stmt->execute([$name]);
        header("Location: adminCategories.php");
        exit;
    } else {
        $message = "Category name cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <h2>Add Category</h2>

        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="name" placeholder="Category Name" required>
            <button type="submit">Add Category</button>
        </form>

        <p><a href="adminCategories.php">🔙 Back to Categories</a></p>
    </div>
</body>
</html>
