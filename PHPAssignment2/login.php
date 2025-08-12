<?php
require_once 'classes/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';


// Start session if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userObj = new User();

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Find user by email
    $user = $userObj->getUserByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        // Login success — store session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        header("Location: index.php?msg=" . urlencode("Welcome back, {$user['name']}!"));
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

include 'inc/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class=>
<h2>Login</h2>

<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>

<?php
include 'inc/footer.php';
?>