<?php
require 'db.php';
// check auction id
$auctionId = isset($_GET['id']) ? $_GET['id'] : null;
if (!$auctionId) {
    die("Auction ID missing.");
}

// Fetch auction
$stmt = $pdo->prepare("SELECT * FROM auction WHERE id = ?");
$stmt->execute([$auctionId]);
$auction = $stmt->fetch();

if (!$auction || $auction['userId'] != $_SESSION['user_id']) {
    die("Unauthorized access.");
}

$message = '';

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categoryId = $_POST['category'];
    $endDate = $_POST['endDate'];

    if (empty($title) || empty($description) || empty($categoryId) || empty($endDate)) {
        $message = "All fields are required.";
    } else {
        $imagePath = $auction['imagePath']; // Keep existing image if no new upload

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

        $stmt = $pdo->prepare("UPDATE auction SET title = ?, description = ?, categoryId = ?, endDate = ?, imagePath = ? WHERE id = ?");
        $stmt->execute([$title, $description, $categoryId, $endDate, $imagePath, $auctionId]);

        header("Location: auction.php?id=" . $auctionId);
        exit;
    }
}

// Handle delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    
    $stmt = $pdo->prepare("DELETE FROM bid WHERE auctionId = ?");
    $stmt->execute([$auctionId]);

    
    $stmt = $pdo->prepare("DELETE FROM auction WHERE id = ?");
    $stmt->execute([$auctionId]);

    header("Location: index.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM category")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Auction</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>

<main>
    <h1>Edit Auction</h1>
    <?php if ($message): ?>
        <p style="color: red;"><?= $message ?></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($auction['title']) ?>" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required><?= htmlspecialchars($auction['description']) ?></textarea><br><br>

        <label>Category:</label><br>
        <select name="category" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $auction['categoryId'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>End Date:</label><br>
        <input type="datetime-local" name="endDate" value="<?= date('Y-m-d\TH:i', strtotime($auction['endDate'])) ?>" required><br><br>

        <label>Upload New Image (optional):</label><br>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit" name="update">Update Auction</button>
        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this auction?')">Delete Auction</button>
    </form>
</main>
</body>
</html>