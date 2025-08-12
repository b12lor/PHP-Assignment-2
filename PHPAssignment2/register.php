<?php
require_once 'classes/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

session_start();

$userObj = new User();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    // Basic validation
    if (!$name || !$email || !$password || !$passwordConfirm) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $passwordConfirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        if ($userObj->getUserByEmail($email)) {
            $error = "Email already registered.";
        } else {
            // Register user
            if ($userObj->register($name, $email, $password)) {
                $success = "Registration successful! You may now <a href='login.php'>login</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<?php include 'inc/header.php'; ?>

<h2>Register</h2>

<?php if ($error): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php elseif ($success): ?>
    <p style="color: green;"><?= $success ?></p>
<?php endif; ?>

<form method="post" novalidate>
    <label>Name:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="password_confirm" required><br><br>

    <button type="submit">Register</button>
</form>

<?php include 'inc/footer.php'; ?>

