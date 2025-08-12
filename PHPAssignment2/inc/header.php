<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current file name without query params
$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($pageTitle ?? 'Lorris Media') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc ?? '') ?>" />
    <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
<header>
    <div class="header-inner">
        <a href="index.php" class="logo">Lorris Media</a>
        <nav>
            <ul>
                <?php if ($currentPage === 'login.php'): ?>
                    <li><a href="index.php">Home</a></li>
                <?php elseif ($currentPage === 'register.php'): ?>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php else: ?>
                    <li><a href="index.php">Home</a></li>
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <li><a href="create.php">New Post</a></li>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="profile.php?id=<?= $_SESSION['user_id'] ?>">Profile</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<main>
