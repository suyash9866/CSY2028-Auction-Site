<?php
session_start();
require 'db.php';

// check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// edit 
$adminId = $_GET['id'] ?? null;
if (!$adminId || !is_numeric($adminId)) {
    header("Location: adminCategories.php");
    exit;
}

// admin data
$stmt = $pdo->prepare("SELECT * FROM user WHERE id = ? AND role = 'admin'");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();
if (!$admin) {
    header("Location: adminCategories.php");
    exit;
}

$message = '';
// update part 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($email)) {
        try {
            if (!empty($_POST['password'])) {
                // for password if inserted
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE user SET name = ?, email = ?, password = ? WHERE id = ? AND role = 'admin'");
                $stmt->execute([$name, $email, $password, $adminId]);
            } else {
                // if password not input
                $stmt = $pdo->prepare("UPDATE user SET name = ?, email = ? WHERE id = ? AND role = 'admin'");
                $stmt->execute([$name, $email, $adminId]);
            }
            $message = "Admin updated successfully.";
            // display updated value righg away 
            $stmt = $pdo->prepare("SELECT * FROM user WHERE id = ? AND role = 'admin'");
            $stmt->execute([$adminId]);
            $admin = $stmt->fetch();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                $message = "Error: Email already exists.";
            } else {
                $message = "Error updating admin: " . $e->getMessage();
            }
        }
    } else {
        $message = "Name and Email are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="admin.css"/>
    <title>Edit Admin</title>
</head>
<body>
<div class="container">
    <h1>Edit Admin</h1>
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($admin['name']) ?>" required><br>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($admin['email']) ?>" required><br>
        <input type="password" name="password" placeholder="New Password (leave blank to keep)"><br>
        <button type="submit">Update Admin</button>
    </form>
    <a href="adminCategories.php">Back to Manage Admins</a>
</div>
</body>
</html>
