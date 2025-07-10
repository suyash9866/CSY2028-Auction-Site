<!DOCTYPE html>
<html>
<head>
    <title>header</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
<header>
    <h1>
        <span class="C">C</span>
        <span class="a">a</span>
        <span class="r">r</span>
        <span class="b">b</span>
        <span class="u">u</span>
        <span class="y">y</span>
    </h1>

    <?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    $searchValue = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
    ?>

    <form action="<?= htmlspecialchars($currentPage) ?>" method="get">
        <input type="text" name="search" placeholder="Search for a car" value="<?= $searchValue ?>" />

        <?php if ($currentPage === 'category.php' && isset($_GET['id'])): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id']) ?>">
        <?php endif; ?>

        <input type="submit" value="Search" />
    </form>
</header>
</body>
</html>
