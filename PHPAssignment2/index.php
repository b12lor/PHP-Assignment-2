<?php
require_once 'classes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Post.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$postObj = new Post();
$posts = $postObj->getAllPosts();

$msg = $_GET['msg'] ?? '';
?>

<?php include 'inc/header.php'; ?>

<?php if ($msg): ?>
    <p class="alert alert-success"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<h1> All Posts</h1>

<?php if (empty($posts)): ?>
    <p>No posts found.</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <article class="post">
            <h3><a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h3>

            <?php if (!empty($post['image'])): ?>
                <a href="post.php?id=<?= $post['id'] ?>" class="post-post">
                    <img src="uploads/posts/<?= htmlspecialchars($post['image']) ?>" alt="Image for <?= htmlspecialchars($post['title']) ?>">
                </a>
            <?php endif; ?>

            <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...</p>
            <p><a href="post.php?id=<?= $post['id'] ?>">Read More</a></p>
        </article>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'inc/footer.php'; ?>

