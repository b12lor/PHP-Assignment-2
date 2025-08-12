<?php
require_once 'classes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Post.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$postObj = new Post();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = (int) $_GET['id'];


    if ($postObj->deletePost($postId)) {
        $message = "Post deleted successfully!";
    } else {
        $message = "Error deleting post.";
    }
} else {
    $message = "Invalid post ID.";
}


header("Location: index.php?msg=" . urlencode($message));
exit;

