<?php
session_start();
require 'db.php';

// check admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// fetch category id
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: adminCategories.php');
    exit;
}

// Fetch category details 
$stmt = $pdo->prepare("SELECT * FROM category WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

// exception handeling 
if (!$category) {
    header('Location: adminCategories.php');
    exit;
}

$message = '';

// form submission 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        // Update
        $stmt = $pdo->prepare("UPDATE category SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
        header('Location: adminCategories.php');
        exit;
    } else {
        $message = "Category name cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <h2>Edit Category</h2>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
            <button type="submit">Update Category</button>
        </form>
        <p><a href="adminCategories.php">🔙 Back to Categories</a></p>
    </div>
</body>
</html>
