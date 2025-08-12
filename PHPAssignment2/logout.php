<?php
session_start();


$_SESSION = [];


session_destroy();


header("Location: login.php?msg=" . urlencode("You have been logged out."));
exit;

