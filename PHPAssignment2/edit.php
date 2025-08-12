<?php
require_once 'classes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Post.php';
require_once 'inc/header.php';

// Start session if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$postObj = new Post();

// Check if the ID is valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid post ID.");
}

$postId = (int) $_GET['id'];

// Fetch the post data
$post = $postObj->getPost($postId);
if (!$post) {
    die("Post not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image = $post['image']; // default to existing image

    // Handle new image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = './uploads/posts/';
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = basename($_FILES['image']['name']);
        }
    }

    if ($postObj->updatePost($postId, $title, $content, $image)) {
        header("Location: post.php?id=$postId&msg=" . urlencode("Post updated successfully!"));
        exit;
    } else {
        $error = "Failed to update post.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<h2>Edit Post</h2>
<form method="post" enctype="multipart/form-data">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>

    <label>Content:</label><br>
    <textarea name="content" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>

    <label>Current Image:</label><br>
    <?php if (!empty($post['image'])): ?>
        <img src="uploads/posts/<?php echo htmlspecialchars($post['image']); ?>" width="150"><br>
    <?php endif; ?>

    <label>Change Image :</label><br>
    <input type="file" name="image"><br><br>

    <button type="submit">Update Post</button>
</form>
</body>
</html>

<?php require_once 'inc/footer.php'; ?>