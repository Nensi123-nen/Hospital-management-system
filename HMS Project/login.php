<?php
session_start();

// Database credentials
$servername = "localhost";
$db_username = "u835275566_P";
$db_password = "Paresh@2331";
$dbname = "u835275566_P";

// Establish connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error_message = "";

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? '';

    if (empty($username) || empty($password) || empty($user_type)) {
        $error_message = "All fields are required";
    } else {
        $table = ($user_type === 'doctor') ? 'doctors' : 'patient';

        try {
            $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $error_message = "Invalid username or password.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user_type;
                $_SESSION['logged_in'] = true;
                $_SESSION['fullname'] = ($user_type === 'doctor') ? $user['full_name'] : $user['fullname'];

                if ($user_type === 'doctor') {
                    $_SESSION['doctor_logged_in'] = true;
                    $_SESSION['doctor_id'] = $user['id'];
                    header("Location: dhome.php");
                } else {
                    $_SESSION['patient_id'] = $user['id'];
                    header("Location: home page.php");
                }
                exit();
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!-- HTML FORM -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <center>
        <div class="wrapper">
            <form method="post">
                <h1>Login</h1>

                <?php if (!empty($error_message)): ?>
                    <p style="color:red;"><?php echo $error_message; ?></p>
                <?php endif; ?>

                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="user-type">
                    <label><input type="radio" name="user_type" value="doctor" required> Doctor</label>
                    <label><input type="radio" name="user_type" value="patient" required> Patient</label>
                </div>

                <button type="submit" class="btn">Login</button><br><br>

                <div class="register-link">
                    <p>
                        Create New Account?<br><br>
                        <a href="submit_registration.php">Register as Patient</a><br>
                        <a href="doctor_registration.php">Register as Doctor</a>
                    </p>
                </div>
            </form>
        </div>
    </center>
</body>
</html>
