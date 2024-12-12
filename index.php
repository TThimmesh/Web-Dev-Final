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

// Fetch Popular Courses (assuming you have a 'popularity' column in your 'courses' table)
$query = "SELECT * FROM courses ORDER BY popularity DESC LIMIT 6";
$stmt = $pdo->query($query);
$popularCourses = $stmt->fetchAll();

// Fetch Recommended Courses (assuming you have a 'recommended' column in your 'courses' table)
$query = "SELECT * FROM courses WHERE recommended = 1 LIMIT 6";
$stmt = $pdo->query($query);
$recommendedCourses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearningHub - Microlearning Platform</title>
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

        .hero {
            text-align: center;
            padding: 60px 20px;
            background-color: #007BFF;
            color: white;
        }

        .cta-button {
            padding: 10px 20px;
            background-color: #fff;
            color: #007BFF;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }

        .cta-button:hover {
            background-color: #0056b3;
            color: white;
        }

        .section {
            padding: 50px 20px;
            text-align: center;
        }

        .course-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
            margin-top: 20px;
        }

        .course-card {
            width: 280px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-10px);
        }

        .course-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .course-card-body {
            padding: 15px;
        }

        .course-card h3 {
            margin: 10px 0;
            font-size: 18px;
            color: #333;
        }

        .course-card p {
            font-size: 14px;
            color: #555;
        }

        .course-card .cta-button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }

        .course-card .cta-button:hover {
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

<!-- Hero Section -->
<section class="hero">
    <h2>Learn Anytime, Anywhere</h2>
    <p>Explore top courses from creators worldwide and boost your skills with microlearning.</p>
    <a href="browse.php" class="cta-button">Browse Content</a>
</section>

<!-- Popular Courses Section -->
<section class="section">
    <h2>Popular Courses</h2>
    <div class="course-cards">
        <?php foreach ($popularCourses as $course): ?>
            <div class="course-card">
                <img src="path/to/course-image.jpg" alt="Course Image">
                <div class="course-card-body">
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <a href="course-details.php?id=<?php echo $course['course_id']; ?>" class="cta-button">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Recommended Courses Section -->
<section class="section" style="background-color: #f9f9f9;">
    <h2>Recommended Courses</h2>
    <div class="course-cards">
        <?php foreach ($recommendedCourses as $course): ?>
            <div class="course-card">
                <img src="path/to/course-image.jpg" alt="Course Image">
                <div class="course-card-body">
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <a href="course-details.php?id=<?php echo $course['course_id']; ?>" class="cta-button">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Footer Section -->
<footer>
    <p>&copy; 2024 LearningHub. All rights reserved.</p>
</footer>

</body>
</html>
