<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 1) {
    header("Location: login.php");
    exit;
}

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    if (!empty($name) && !empty($email) && !empty($_POST['password'])) {
        $stmt = $pdo->prepare("INSERT INTO user (name, email, password, isAdmin) VALUES (?, ?, ?, 1)");
        $stmt->execute([$name, $email, $password]);
        $message = "Admin added successfully.";
    } else {
        $message = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Admin</title></head>
<body>
<h1>Add Admin</h1>
<?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>
<form method="post">
    <input type="text" name="name" placeholder="Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Add Admin</button>
</form>
<a href="manageAdmins.php">Back to Manage Admins</a>
</body>
</html>