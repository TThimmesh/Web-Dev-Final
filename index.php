<?php
// Start Session
session_start();

// Database Configuration
$host = 'localhost';
$dbname = 'learning_site';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
} else {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Learning Site</title>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p class="welcome-message">You are successfully logged in.</p>
        <a href="index.php?logout=true">
            <button class="btn">Logout</button>
        </a>
    </div>
</body>
</html>
