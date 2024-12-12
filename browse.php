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

// Fetch all courses from the database
$query = "SELECT * FROM courses ORDER BY popularity DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$courses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Content - LearningHub</title>
    <style>
        /* Add your styles here */
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

        .courses-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }

        .course-card {
            width: calc(33.33% - 20px);
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .course-card:hover {
            transform: translateY(-5px);
        }

        .course-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .course-card .content {
            padding: 15px;
        }

        .course-card h3 {
            margin: 0;
            font-size: 18px;
        }

        .course-card p {
            font-size: 14px;
            color: #555;
        }

        .course-card .category {
            font-size: 12px;
            color: #007BFF;
            margin-top: 10px;
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
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Browse Courses Section -->
<section class="courses-container">
    <?php foreach ($courses as $course): ?>
        <div class="course-card">
            <img src="<?php echo $course['image_url']; ?>" alt="Course Image">
            <div class="content">
                <h3><?php echo $course['title']; ?></h3>
                <p><?php echo substr($course['description'], 0, 100); ?>...</p>
                <span class="category"><?php echo $course['category']; ?></span>
                <br>
                <!-- Corrected link to course_details.php -->
                <a href="course_details.php?id=<?php echo $course['course_id']; ?>" class="cta-button">View Details</a>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<!-- Footer Section -->
<footer>
    <p>&copy; 2024 LearningHub. All rights reserved.</p>
</footer>

</body>
</html>
