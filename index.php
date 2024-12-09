<?php

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


$creator_id = $_GET['creator'];

try {

    $query_creator = "SELECT u.username, cr.name, cr.bio, cr.profile_picture, cr.verified
                      FROM creators cr
                      JOIN users u ON cr.user_id = u.user_id
                      WHERE cr.creator_id = :creator_id";
    $stmt_creator = $pdo->prepare($query_creator);
    $stmt_creator->bindParam(':creator_id', $creator_id, PDO::PARAM_INT);
    $stmt_creator->execute();
    $creator = $stmt_creator->fetch(PDO::FETCH_ASSOC);


    $query_content = "SELECT title, description, video_url FROM content WHERE creator_id = :creator_id";
    $stmt_content = $pdo->prepare($query_content);
    $stmt_content->bindParam(':creator_id', $creator_id, PDO::PARAM_INT);
    $stmt_content->execute();
    $content_result = $stmt_content->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($creator['name']); ?> - Creator Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="background-image">
            <h1><?php echo htmlspecialchars($creator['name']); ?>'s Creator Page</h1>
        </div>
    </header>

    <main>
        <section class="creator-info">
            <h2><?php echo htmlspecialchars($creator['name']); ?> <?php echo $creator['verified'] ? '✔' : ''; ?></h2>
            <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($creator['bio'])); ?></p>
            <img src="<?php echo htmlspecialchars($creator['profile_picture']); ?>" alt="Profile Picture">
        </section>

        <section class="creator-content">
            <h3>Content</h3>
            <?php while ($content = $content_result->fetch_assoc()): ?>
            <div class="content-item">
                <h4><?php echo htmlspecialchars($content['title']); ?></h4>
                <iframe src="<?php echo htmlspecialchars($content['video_url']); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <p><?php echo nl2br(htmlspecialchars($content['description'])); ?></p>
            </div>
            <?php endwhile; ?>
        </section>
    </main>
</body>
</html>
