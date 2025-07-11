<?php
session_start();
require 'db.php';

// loged in admin to access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// messaging
$message = '';

// adding new admin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and capture form inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); //hashing password

    // Checking if any field in empty
    if (!empty($name) && !empty($email) && !empty($_POST['password'])) {
        try {
            // inserting new admin
            $stmt = $pdo->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$name, $email, $password]);

            $message = "Admin added successfully.";
        } catch (PDOException $e) {
            // exception handeling
            if ($e->getCode() == 23000) { 
                $message = "An account with this email already exists.";
            } else {
                $message = "Error: " . $e->getMessage();
            }
        }
    } else {
        $message = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="admin.css"/>
    <title>Add Admin</title>
</head>
<body>
<div class="container">
    <h2>Add Admin</h2>
  
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Add Admin</button>
    </form>

    <p><a href="adminCategories.php">Back to Manage Admins</a></p>
</div>
</body>
</html>
