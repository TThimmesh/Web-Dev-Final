<?php
// Database configuration
$host = 'localhost';
$dbname = 'skillshare_site';
$user = 'taylor';
$pass = '1655897';
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
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Start session for login management
session_start();

// Handle Registration
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $query = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $password_hash,
        ]);
        echo "Registration successful!";
    } catch (PDOException $e) {
        echo "Error during registration: " . $e->getMessage();
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $query = "SELECT user_id, password_hash FROM users WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $username;
            echo "Login successful!";
        } else {
            echo "Invalid username or password.";
        }
    } catch (PDOException $e) {
        echo "Error during login: " . $e->getMessage();
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Protect Content
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view this page.";
    exit;
}

// Fetch creator data
$creator_id = $_GET['creator'] ?? null;
if ($creator_id) {
    try {
        $query_creator = "SELECT u.username, cr.name, cr.bio, cr.profile_picture, cr.verified
                          FROM creators cr
                          JOIN users u ON cr.user_id = u.user_id
                          WHERE cr.creator_id = :creator_id";
        $stmt_creator = $pdo->prepare($query_creator);
        $stmt_creator->bindParam(':creator_id', $creator_id, PDO::PARAM_INT);
        $stmt_creator->execute();
        $creator = $stmt_creator->fetch();

        $query_content = "SELECT title, description, video_url FROM content WHERE creator_id = :creator_id";
        $stmt_content = $pdo->prepare($query_content);
        $stmt_content->bindParam(':creator_id', $creator_id, PDO::PARAM_INT);
        $stmt_content->execute();
        $content_result = $stmt_content->fetchAll();
    } catch (PDOException $e) {
        echo "Error fetching creator data: " . $e->getMessage();
    }
}

$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($creator['name'] ?? 'Creator Page'); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <div class="background-image">
        <h1><?php echo htmlspecialchars($creator['name'] ?? 'Welcome'); ?>'s Creator Page</h1>
    </div>
</header>

<main>
    <section class="creator-info">
        <h2>
            <?php echo htmlspecialchars($creator['name'] ?? 'Unknown'); ?>
            <?php echo !empty($creator['verified']) ? '✔' : ''; ?>
        </h2>
        <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($creator['bio'] ?? '')); ?></p>
        <img src="<?php echo htmlspecialchars($creator['profile_picture'] ?? 'default.jpg'); ?>" alt="Profile Picture">
    </section>

    <section class="creator-content">
        <h3>Content</h3>
        <?php if (!empty($content_result)): ?>
            <?php foreach ($content_result as $content): ?>
            <div class="content-item">
                <h4><?php echo htmlspecialchars($content['title']); ?></h4>
                <iframe src="<?php echo htmlspecialchars($content['video_url']); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <p><?php echo nl2br(htmlspecialchars($content['description'])); ?></p>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No content available.</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
