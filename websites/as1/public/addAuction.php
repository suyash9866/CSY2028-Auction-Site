<?php
require 'db.php';

$message = '';

// Get all categories for dropdown
$categories = $pdo->query("SELECT * FROM category")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categoryId = $_POST['category'];
    $endDate = $_POST['endDate'];

    // Validate required fields
    if (empty($title) || empty($description) || empty($categoryId) || empty($endDate)) {
        $message = "All fields are required.";
    }
    // Check if image was uploaded
    elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $message = "Image upload is required to add an auction.";
    } else {
        $imagePath = null;

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'images/auctions/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid() . '.' . $ext;
            $imagePath = $uploadDir . $imageName;
            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        }

        // Insert auction into database
        $stmt = $pdo->prepare("INSERT INTO auction (title, description, categoryId, endDate, userId, imagePath) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $categoryId, $endDate, $_SESSION['user_id'], $imagePath]);

        header("Location: index.php");
        exit;
    }
}

$categoriesNav = $pdo->query("SELECT * FROM category")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Auction</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>

<main>
    <h1>Add Auction</h1>
    <?php if ($message): ?>
        <p style="color: red;"><?= $message ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Category:</label><br>
        <select name="category" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>End Date:</label><br>
        <input type="datetime-local" name="endDate" required><br><br>

        <label>Upload Image (optional):</label><br>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit">Add Auction</button>
    </form>
</main>
</body>
</html>
