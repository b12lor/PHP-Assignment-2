<?php
require_once 'classes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Post.php';

session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$postObj = new Post();
$userId = $_SESSION['user_id'];
$posts = $postObj->getPostsByUser($userId);

$pageTitle = "Dashboard";
include 'inc/header.php';
?>

<h1>Your Posts</h1>

<p><a href="create.php">Create New Post</a></p>

<?php if (empty($posts)): ?>
    <p>You have not created any posts yet.</p>
<?php else: ?>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <strong><?= htmlspecialchars($post['title']) ?></strong>
                <a href="edit.php?id=<?= $post['id'] ?>">Edit</a> |
                <a href="delete.php?id=<?= $post['id'] ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php include 'inc/footer.php'; ?>

