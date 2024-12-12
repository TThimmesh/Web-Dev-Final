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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle course creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_course'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $content = $_POST['content'] ?? '';

    if ($title && $description && $image_url) {
        $stmt = $pdo->prepare("INSERT INTO courses (user_id, title, description, image_url, content, popularity, recommended) VALUES (?, ?, ?, ?, ?, 0, 0)");
        $stmt->execute([$_SESSION['user_id'], $title, $description, $image_url, $content]);
        $success = "Course created successfully!";
    } else {
        $error = "All fields are required to create a course.";
    }
}

// Fetch all courses created by the user
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM courses WHERE user_id = ?");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LearningHub</title>
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

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
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

<!-- Dashboard Section -->
<section class="section">
    <h2>Welcome to Your Dashboard</h2>

    <?php if (isset($success)): ?>
        <div class="message success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <h3>Create a New Course</h3>
    <form method="POST" action="">
        <label for="title">Course Title</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Course Description</label>
        <textarea id="description" name="description" rows="5" required></textarea>

        <label for="image_url">Image URL</label>
        <input type="text" id="image_url" name="image_url" required>

        <label for="content">Course Content</label>
        <textarea id="content" name="content" rows="5"></textarea>

        <button type="submit" name="create_course">Create Course</button>
    </form>

    <h3>Your Created Courses</h3>
    <div class="course-cards">
        <?php foreach ($courses as $course): ?>
            <div class="course-card">
                <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Course Image">
                <div class="course-card-body">
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <a href="dashboard.php?delete_course_id=<?php echo $course['course_id']; ?>"
                       class="cta-button"
                       onclick="return confirm('Are you sure you want to delete this course?');">Delete Course</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

</body>
</html>
