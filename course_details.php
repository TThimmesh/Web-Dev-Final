<?php
// Start session
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

// Check if course_id is passed in the URL
if (isset($_GET['course_id']) && is_numeric($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Fetch course details from the database
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();

    if (!$course) {
        echo "Invalid or missing course_id in URL!";
        exit;
    }

    // Format the created_at date
    $created_at = new DateTime($course['created_at']);
    $formatted_date = $created_at->format('F j, Y');
} else {
    echo "Invalid or missing course_id in URL!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details - LearningHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo h1 {
            margin: 0;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .nav-links li {
            display: inline;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .section {
            padding: 50px 20px;
            text-align: center;
        }

        .course-card {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }

        .course-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .course-card-body {
            padding: 20px;
        }

        .course-card h2 {
            margin: 10px 0;
            font-size: 24px;
            color: #333;
        }

        .course-card p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }

        .course-card .created-at {
            font-size: 14px;
            color: #777;
        }

        .cta-button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }

        .cta-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<!-- Header Section -->
<header>
    <nav class="navbar">
        <div class="logo">
            <h1>LearningHub</h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="browse.php">Browse Content</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="login.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Course Details Section -->
<section class="section">
    <h2>Course Details</h2>

    <div class="course-card">
        <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Course Image">
        <div class="course-card-body">
            <h2><?php echo htmlspecialchars($course['title']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
            <p class="created-at">Created On: <?php echo $formatted_date; ?></p>

            <!-- You can add a button or action here, for example -->
            <a href="dashboard.php?delete_course_id=<?php echo $course['course_id']; ?>"
               class="cta-button"
               onclick="return confirm('Are you sure you want to delete this course?');">Delete Course</a>
        </div>
    </div>
</section>

</body>
</html>
