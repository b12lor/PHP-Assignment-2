<?php
require_once 'classes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Post.php';

// Start session if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$postObj = new Post();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid post ID.");
}

$postId = (int) $_GET['id'];
$post = $postObj->getPost($postId);  // Changed from getById() to getPost()

if (!$post) {
    die("Post not found.");
}
?>

<?php include 'inc/header.php'; ?>

<h2><?= htmlspecialchars($post['title']) ?></h2>
<p>By: <?= htmlspecialchars($post['author'] ?? 'Unknown') ?> | Posted on: <?= htmlspecialchars($post['created_at']) ?></p>

<?php if (!empty($post['image'])): ?>
    <img src="uploads/posts/<?= htmlspecialchars($post['image']) ?>" alt="Post Image" style="max-width: 100%; height: auto;">
<?php endif; ?>

<p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

<?php include 'inc/footer.php'; ?>
