<?php
require_once 'classes/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

session_start();

// Redirect if not logged in
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userObj = new User();
$userId = $_SESSION['user_id'];
$user = $userObj->getById($userId);

if (!$user) {
    die("User not found.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $profileImage = $user['profile_image'];

    // Handle image upload if new file submitted
    if (!empty($_FILES['profile_image']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $imageName = time() . '_' . basename($_FILES['profile_image']['name']);
        $targetFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
            $profileImage = $imageName;
        } else {
            $error = "Failed to upload profile image.";
        }
    }

    if (!$error) {
        if ($userObj->update($userId, $name, $email, $profileImage)) {
            $success = "Profile updated successfully!";
            // Refresh user data
            $user = $userObj->getById($userId);
        } else {
            $error = "Failed to update profile.";
        }
    }
}

?>

<?php include 'inc/header.php'; ?>

<h2>Edit Profile</h2>

<?php if ($error): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

    <?php if (!empty($user['profile_image'])): ?>
        <p>Current Profile Image:</p>
        <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" style="max-width:150px;"><br><br>
    <?php endif; ?>

    <label>Change Profile Image:</label><br>
    <input type="file" name="profile_image"><br><br>

    <button type="submit">Update Profile</button>
</form>

<?php include 'inc/footer.php'; ?>

