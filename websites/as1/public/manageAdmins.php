<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}


if (isset($_GET['delete'])) {
    $adminId = $_GET['delete'];
    if ($adminId != $_SESSION['user_id']) { // Prevent self-deletion
        $stmt = $pdo->prepare("DELETE FROM user WHERE id = ? AND role = 'admin'");
        $stmt->execute([$adminId]);
    }
}

$admins = $pdo->query("SELECT * FROM user WHERE role = 'admin'")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="admin.css"/>
    <title>Manage Admins</title>
</head>
<body>
<div class="container">
    <h2>Manage Admins</h2>

    <p><a href="addAdmin.php">Add New Admin</a></p>

    <table>
        <tr><th>Name</th><th>Email</th><th>Actions</th></tr>
        <?php foreach ($admins as $admin): ?>
        <tr>
            <td><?= htmlspecialchars($admin['name']) ?></td>
            <td><?= htmlspecialchars($admin['email']) ?></td>
            <td>
                <a href="editAdmin.php?id=<?= $admin['id'] ?>">Edit</a>
                <?php if ($admin['id'] != $_SESSION['user_id']): ?> <!-- Prevent self-deletion -->
                | <a href="manageAdmins.php?delete=<?= $admin['id'] ?>" onclick="return confirm('Delete this admin?')">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
