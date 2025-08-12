<?php
require_once 'classes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Post.php';

// Start session if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$postObj = new Post();

// Handle form submit
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');


    $imageFile = $_FILES['image'] ?? null;

    // Check if user is logged in
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        $message = "You must be logged in to create a post.";
    } elseif ($title && $content) {
        $success = $postObj->createPost($userId, $title, $content, $imageFile);
        if ($success) {
            $message = "Post created successfully!";
        } else {
            $message = "Error creating post.";
        }
    } else {
        $message = "Title and content are required.";
    }
}
?>

<?php include 'inc/header.php'; ?>

<h2>Create New Post</h2>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Content:</label><br>
    <textarea name="content" rows="6" required></textarea><br><br>

    <label>Image:</label><br>
    <input type="file" name="image"><br><br>

    <input type="submit" value="Create Post">
</form>

<?php include 'inc/footer.php'; ?>
