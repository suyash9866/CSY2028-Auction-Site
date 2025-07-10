<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $adminId = $_GET['delete'];
    if ($adminId != $_SESSION['user_id']) { // Prevent self-deletion
        $stmt = $pdo->prepare("DELETE FROM user WHERE id = ? AND isAdmin = 1");
        $stmt->execute([$adminId]);
    }
}

$admins = $pdo->query("SELECT * FROM user WHERE isAdmin = 1")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Manage Admins</title></head>
<body>
<h1>Manage Admins</h1>
<a href="addAdmin.php">Add New Admin</a>
<table border="1">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
<?php foreach ($admins as $admin): ?>
<tr>
    <td><?= $admin['id'] ?></td>
    <td><?= htmlspecialchars($admin['name']) ?></td>
    <td><?= htmlspecialchars($admin['email']) ?></td>
    <td>
        <a href="editAdmin.php?id=<?= $admin['id'] ?>">Edit</a> |
        <a href="manageAdmins.php?delete=<?= $admin['id'] ?>" onclick="return confirm('Delete this admin?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>