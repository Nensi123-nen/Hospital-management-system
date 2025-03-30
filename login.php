<?php
// login.php
session_start();

// Database configuration
$host = "localhost";
$dbname = "doctor_registration";
$db_username = "root";
$db_password = "";

// Connect to database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? '';

    // Validate inputs
    if (empty($username) || empty($password) || empty($user_type)) {
        $_SESSION['login_error'] = "All fields are required";
        header("Location: login_form.php");
        exit();
    }

    // Determine table
    $table = ($user_type == 'doctor') ? 'doctors' : 'patients';
    
    try {
        // Check credentials
        $stmt = $conn->prepare("SELECT id, username, password FROM $table WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user_type;
            $_SESSION['logged_in'] = true;

            // Redirect to home page
            header("Location: home_page.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid username or password";
            header("Location: login_form.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = "Database error: " . $e->getMessage();
        header("Location: login_form.php");
        exit();
    }
}
?>