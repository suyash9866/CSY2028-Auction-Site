<?php
session_start();
require 'db.php';
ob_start();

// Fetch 10 auctions ending soonest
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT auction.*, category.name AS categoryName 
        FROM auction 
        JOIN category ON auction.categoryId = category.id 
        WHERE auction.title LIKE :search OR auction.description LIKE :search
        ORDER BY endDate ASC
    ");
    $stmt->execute(['search' => '%' . $search . '%']);
} else {
    $stmt = $pdo->query("
        SELECT auction.*, category.name AS categoryName 
        FROM auction 
        JOIN category ON auction.categoryId = category.id 
        ORDER BY endDate ASC 
        LIMIT 10
    ");
}
$auctions = $stmt->fetchAll();


// Fetch categories for navigation
$catStmt = $pdo->query("SELECT * FROM category");
$categories = $catStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
<?php
	require 'header.php';
?>

<nav>
    <ul>
        <?php foreach ($categories as $cat): ?>
            <li><a class="categoryLink" href="category.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</nav>
<img src="banners/1.jpg" alt="Banner" />

<main>
    <h1>Latest Car Listings</h1>
    <ul class="carList">
        <?php foreach ($auctions as $auction): ?>
            <li>
                    <?php if (!empty($auction['imagePath']) && file_exists($auction['imagePath'])): ?>
				<img src="<?= htmlspecialchars($auction['imagePath']) ?>" alt="<?= htmlspecialchars($auction['title']) ?>" style="max-width:300px; height:auto;">
				<?php else: ?>
				<img src="car.png" alt="<?= htmlspecialchars($auction['title']) ?>">
				<?php endif; ?>
                <article>
                    <h2 style="margin-left: 150px;"><?= htmlspecialchars($auction['title']) ?></h2>
                    <h3 style="margin-left: 150px;"><?= htmlspecialchars($auction['categoryName']) ?></h3>
                    <p style="margin-left: 150px;"><?= nl2br(htmlspecialchars(substr($auction['description'], 0, 200))) ?>...</p>
                    <p class="price">
                        <?php
                        $bidStmt = $pdo->prepare("SELECT MAX(amount) AS maxBid FROM bid WHERE auctionId = ?");
                        $bidStmt->execute([$auction['id']]);
                        $bid = $bidStmt->fetch();
                        echo $bid && $bid['maxBid'] ? "Current bid: £" . number_format($bid['maxBid'], 2) : "No bids yet";
                        ?>
                    </p>
                    <a href="auction.php?id=<?= $auction['id'] ?>" class="more auctionLink">More &gt;&gt;</a>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>
</main>

<?php
	require 'addAuction.php';
?>

<?php if (isset($_SESSION['user_id'])): ?>
    <form action="logout.php" method="post" style="text-align: center; margin: 20px;">
        <button type="submit" style="
            background-color: #d9534f; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 4px; 
            cursor: pointer;
        ">
            Logout
        </button>
    </form>
<?php endif; ?>

<?php
	require 'footer.php';
?>

<?php
ob_end_flush();
?>

</body>
</html>
