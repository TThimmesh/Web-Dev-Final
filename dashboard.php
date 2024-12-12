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

    // Validate inputs
    if ($title && $description && $image_url) {
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (user_id, title, description, image_url, content, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $title, $description, $image_url, $content]);

            // Success message
            $success = "Course created successfully!";
        } catch (PDOException $e) {
            // Handle database error
            $error = "Error creating course: " . $e->getMessage();
        }
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
        /* Add your styling here */
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

    <!-- Display success or error message -->
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
                    <a href="course_details.php?course_id=<?php echo $course['course_id']; ?>" class="cta-button">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

</body>
</html>
