<?php
session_start();
require 'db.php';
ob_start();


// Move search redirect ABOVE auctionId check to avoid 'Auction not specified' when searching
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $searchQuery = urlencode(trim($_GET['search']));
    header("Location: index.php?search={$searchQuery}");
    exit;
}

$auctionId = $_GET['id'] ?? null;
if (!$auctionId || !is_numeric($auctionId)) {
    header('Location: index.php');
    exit;
}

if (!$auctionId) {
    die("Auction not specified.");
}

// Fetch auction
$stmt = $pdo->prepare("
    SELECT auction.*, category.name AS categoryName, user.name AS sellerName, user.id AS sellerId 
    FROM auction 
    JOIN category ON auction.categoryId = category.id 
    JOIN user ON auction.userId = user.id 
    WHERE auction.id = ?
");
$stmt->execute([$auctionId]);
$auction = $stmt->fetch();

if (!$auction) {
    die("Auction not found.");
}

// Fetch highest bid
$bidStmt = $pdo->prepare("SELECT MAX(amount) AS maxBid FROM bid WHERE auctionId = ?");
$bidStmt->execute([$auctionId]);
$maxBid = $bidStmt->fetch()['maxBid'] ?? null;

// Fetch reviews
$reviewStmt = $pdo->prepare("SELECT review.*, reviewer.name AS reviewerName FROM review JOIN user AS reviewer ON review.reviewerId = reviewer.id WHERE review.auctionId = ? ORDER BY created_at DESC");
$reviewStmt->execute([$auctionId]);
$reviews = $reviewStmt->fetchAll();

// Handle bid submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitBid']) && isset($_SESSION['user_id'])) {
    $bidAmount = $_POST['bid'];
    if (is_numeric($bidAmount) && $bidAmount > 0) {
        $insertBid = $pdo->prepare("INSERT INTO bid (auctionId, userId, amount) VALUES (?, ?, ?)");
        $insertBid->execute([$auctionId, $_SESSION['user_id'], $bidAmount]);
        header("Location: auction.php?id=" . $auctionId);
        exit;
    }
}

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitReview']) && isset($_SESSION['user_id'])) {
    $reviewText = trim($_POST['reviewText']);
    if (!empty($reviewText)) {
        $insertReview = $pdo->prepare("INSERT INTO review (reviewerId, revieweeId, reviewText, auctionId) VALUES (?, ?, ?, ?)");
        $insertReview->execute([$_SESSION['user_id'], $auction['sellerId'], $reviewText, $auctionId]);
        header("Location: auction.php?id=" . $auctionId);
        exit;
    }
}

// Fetch categories for nav
$categories = $pdo->query("SELECT * FROM category")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($auction['title']) ?> - Carbuy</title>
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
    <h1><?= htmlspecialchars($auction['title']) ?></h1>
    <article class="car">
        <?php if (!empty($auction['imagePath']) && file_exists($auction['imagePath'])): ?>
        <img src="<?= htmlspecialchars($auction['imagePath']) ?>" alt="<?= htmlspecialchars($auction['title']) ?>" style="max-width:300px; height:auto;">
        <?php else: ?>
            <img src="car.png" alt="<?= htmlspecialchars($auction['title']) ?>">
        <?php endif; ?>
        <section class="details">
            <h2><?= htmlspecialchars($auction['title']) ?></h2>
            <h3><?= htmlspecialchars($auction['categoryName']) ?></h3>
            <p>Auction created by <a href="#"><?= htmlspecialchars($auction['sellerName']) ?></a></p>
            <p class="price"><?= $maxBid ? "Current bid: £" . number_format($maxBid, 2) : "No bids yet" ?></p>
            <time>Time left: 
                <?php
                $now = new DateTime();
                $end = new DateTime($auction['endDate']);
                $interval = $now->diff($end);
                echo $end > $now ? $interval->format('%d days %h hours %i minutes') : 'Auction ended';
                ?>
            </time>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" class="bid">
                    <input type="text" name="bid" placeholder="Enter bid amount" />
                    <input type="submit" name="submitBid" value="Place bid" />
                </form>
            <?php else: ?>
                <p><a href="login.php">Login</a> to place a bid.</p>
            <?php endif; ?>
        </section>
        <section class="description">
            <p><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
        </section>

        <section class="reviews">
            <h2>Reviews of <?= htmlspecialchars($auction['sellerName']) ?></h2>
            <ul>
                <?php foreach ($reviews as $review): ?>
                    <li>
                        <strong>
                            <a href="userReviews.php?reviewerId=<?= $review['reviewerId'] ?>">
                                <?= htmlspecialchars($review['reviewerName']) ?>
                            </a> said
                        </strong> 
                        <?= htmlspecialchars($review['reviewText']) ?> 
                        <em><?= $review['created_at'] ?></em>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post">
                    <label>Add your review</label>
                    <textarea name="reviewText"></textarea>
                    <input type="submit" name="submitReview" value="Add Review" />
                </form>
            <?php else: ?>
                <p><a href="login.php">Login</a> to add a review.</p>
            <?php endif; ?>
        </section>
    </article>

</main>
<?php
	require 'editAuction.php';
?>
<footer>&copy; Carbuy 2024</footer>
<?php
ob_end_flush();
?>
</body>
</html>
