<?php
require_once 'classes/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userObj = new User();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$userId = (int) $_GET['id'];
$user = $userObj->getById($userId);

if (!$user) {
    die("User not found.");
}
?>

<?php include 'inc/header.php'; ?>

<h2>Your Profile</h2>

<?php if (!empty($user['profile_image'])): ?>
    <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" style="max-width: 150px; height: auto;">
<?php else: ?>
    <p>No profile image.</p>
<?php endif; ?>

<p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

<?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId): ?>
    <p><a href="edit_profile.php" class="edit-profile-button">Edit Profile</a></p>
<?php endif; ?>

<?php include 'inc/footer.php'; ?>
