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

// Handle Registration
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if password and confirm password match
    if ($password === $confirm_password) {
        // Check if username or email already exists
        $checkQuery = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
        $stmt = $pdo->prepare($checkQuery);
        $stmt->execute([':username' => $username, ':email' => $email]);
        $exists = $stmt->fetchColumn();

        if ($exists > 0) {
            echo "Username or email already exists. Please choose another.";
        } else {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            try {
                $query = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password_hash' => $password_hash,
                ]);
                echo "Registration successful! Please <a href='login.php'>log in</a>.";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    } else {
        echo "Passwords do not match.";
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $query = "SELECT user_id, password_hash FROM users WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Store user session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $username;

            // Redirect to a personalized page (e.g., dashboard)
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Invalid username or password.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
        }

        button:hover {
            background: #0056b3;
        }

        .toggle {
            text-align: center;
            margin-top: 10px;
        }

        .toggle a {
            color: #007BFF;
            text-decoration: none;
        }

        .toggle a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>

        <div class="toggle">
            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        </div>
    </div>

    <div class="container">
        <h2>Register</h2>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" name="register">Register</button>
        </form>

        <div class="toggle">
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </div>
</body>
</html>
