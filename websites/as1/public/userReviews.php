<?php
session_start();
require 'db.php';
ob_start();

// get user id
$reviewerId = $_GET['reviewerId'] ?? null;
if (!$reviewerId) {
    die("Reviewer not specified.");
}

// Get user info
$stmt = $pdo->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$reviewerId]);
$reviewer = $stmt->fetch();
if (!$reviewer) {
    die("Reviewer not found.");
}

// show review of a user
$reviewStmt = $pdo->prepare("
    SELECT review.*, auction.title AS auctionTitle
    FROM review
    JOIN auction ON review.auctionId = auction.id
    WHERE review.reviewerId = ?
    ORDER BY review.created_at DESC
");
$reviewStmt->execute([$reviewerId]);
$reviews = $reviewStmt->fetchAll();

// get category 
$categories = $pdo->query("SELECT * FROM category")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reviews by <?= htmlspecialchars($reviewer['name']) ?> - Carbuy</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
<header>
    <h1>Carbuy</h1>
    <form action="index.php" method="get">
        <input type="text" name="search" placeholder="Search for a car" />
        <input type="submit" value="Search" />
    </form>
</header>

<nav>
    <ul>
        <?php foreach ($categories as $cat): ?>
            <li><a class="categoryLink" href="category.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</nav>
<img src="banners/1.jpg" alt="Banner" />

<main>
    <h1>Reviews by <?= htmlspecialchars($reviewer['name']) ?></h1>

    <?php if (count($reviews) === 0): ?>
        <p>This user has not posted any reviews yet.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($reviews as $review): ?>
                <li>
                    On 
                    <a href="auction.php?id=<?= $review['auctionId'] ?>">
                        <?= htmlspecialchars($review['auctionTitle']) ?>
                    </a>:
                    <?= htmlspecialchars($review['reviewText']) ?>
                    <em><?= $review['created_at'] ?></em>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>

<footer>&copy; Carbuy 2024</footer>
<?php ob_end_flush(); ?>
</body>
</html>
