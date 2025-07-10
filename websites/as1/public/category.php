<?php
session_start();
require 'db.php';
ob_start();

// fetch category id
if (!isset($_GET['id']) && isset($_SESSION['last_category_id'])) {
    $_GET['id'] = $_SESSION['last_category_id'];
}

// category id showing from url
$categoryId = $_GET['id'] ?? null;

// exception handeling
if (!$categoryId) {
    header("Location: index.php");
    exit;
}


$_SESSION['last_category_id'] = $categoryId;

// detch category detail
$catStmt = $pdo->prepare("SELECT * FROM category WHERE id = ?");
$catStmt->execute([$categoryId]);
$category = $catStmt->fetch();

// exception handeling 
if (!$category) {
    header("Location: index.php");
    exit;
}

// search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// filtering
if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT auction.*, category.name AS categoryName 
        FROM auction 
        JOIN category ON auction.categoryId = category.id 
        WHERE categoryId = :categoryId 
        AND (auction.title LIKE :search OR auction.description LIKE :search)
        ORDER BY endDate ASC
    ");
    $stmt->execute([
        'categoryId' => $categoryId,
        'search' => '%' . $search . '%'
    ]);
} else {
    // when blank shows all
    $stmt = $pdo->prepare("
        SELECT auction.*, category.name AS categoryName 
        FROM auction 
        JOIN category ON auction.categoryId = category.id 
        WHERE categoryId = ?
        ORDER BY endDate ASC
    ");
    $stmt->execute([$categoryId]);
}

// Fetch auction data
$auctions = $stmt->fetchAll();

// Fetch category
$categories = $pdo->query("SELECT * FROM category")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($category['name']) ?> Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
<header>
    <h1>Carbuy</h1>
    <form action="#">
        <input type="text" name="search" placeholder="Search for a car" />
        <input type="submit" name="submit" value="Search" />
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
    <h1><?= htmlspecialchars($category['name']) ?> Auctions</h1>
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
<?php require 'addAuction.php'; ?>
<footer>&copy; Carbuy 2024</footer>
<?php ob_end_flush(); ?>
</body>
</html>
