<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$host = "localhost";
$dbname = "doctor_registration";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize message variables
$_SESSION['error_message'] = '';
$_SESSION['success_message'] = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process and validate form data
    $fullname = $_POST['fullname'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $email = $_POST['email'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $optional_mobile = $_POST['optional-mobile'] ?? null;
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $username = $_POST['Username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Process blood group
    $bloodgroup = $_POST['bloodgroup'] ?? '';
    if (strpos($bloodgroup, '-') !== false) {
        $parts = explode('-', $bloodgroup);
        $bloodgroup = strtoupper($parts[0]) . $parts[1][0]; // Converts "a-positive" to "A+"
    }

    // Validate required fields
    $required = ['fullname', 'birthdate', 'gender', 'email', 'mobile', 
                'address', 'city', 'bloodgroup', 'Username', 'password'];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error_message'] = "Error: Missing required field '$field'";
            header("Location: registration.html"); // Redirect back to form
            exit();
        }
    }

    // Validate blood group format
    if (!preg_match('/^(A|B|AB|O)[+-]$/', $bloodgroup)) {
        $_SESSION['error_message'] = "Error: Invalid blood group format";
        header("Location: registration.html");
        exit();
    }

    try {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert into database
        $sql = "INSERT INTO patient (...) VALUES (...)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([...]);
        
        $_SESSION['success_message'] = "Registration Successful!";
        header("Location: registration.html");
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: registration.html");
        exit();
    }
}
?>