<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "doctor_registration";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$registration_success = false; // Flag to track successful registration
$error_message = ""; // To store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $birthdate = $conn->real_escape_string($_POST['birthdate']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $email = $conn->real_escape_string($_POST['email']);
    $mobile = $conn->real_escape_string($_POST['mobile']);
    $optional_mobile = $conn->real_escape_string($_POST['optional_mobile']);
    $qualification = $conn->real_escape_string($_POST['qualification']);
    $specialization = $conn->real_escape_string($_POST['specialization']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Check if username or email already exists
    $check_sql = "SELECT id FROM doctors WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    if ($check_stmt) {
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $error_message = "Username or email already exists!";
        } else {
            // Insert data into the database
            $sql = "INSERT INTO doctors (full_name, birthdate, gender, email, mobile, optional_mobile, qualification, specialization, username, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssssssssss", $full_name, $birthdate, $gender, $email, $mobile, $optional_mobile, $qualification, $specialization, $username, $password);
                if ($stmt->execute()) {
                    $registration_success = true;
                } else {
                    $error_message = "Execution Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "SQL Preparation Error: " . $conn->error;
            }
        }
        $check_stmt->close();
    } else {
        $error_message = "Check Preparation Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration Form</title>
    <link rel="stylesheet" href="styles-doc.css">
    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            if (password != confirmPassword) {
                document.getElementById("passwordError").style.display = "block";
                return false;
            } else {
                document.getElementById("passwordError").style.display = "none";
                return true;
            }
        }

        function handleFormSubmit(event) {
            if (!validatePassword()) {
                event.preventDefault();
                return false;
            }
            
            // Let the form submit normally - PHP will handle the database operations
            return true;
        }
    </script>
</head>
<body>
    <div class="registration-form">
        <h2>Doctor Registration Form</h2>

        <!-- Error Message -->
        <?php if (!empty($error_message)): ?>
            <p id="errorMessage" style="font-family: 'Courier New', Courier, monospace; font-size: 14px; color: red; text-align: center; margin-top: 10px;">
                <?php echo htmlspecialchars($error_message); ?>
            </p>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if ($registration_success): ?>
            <p id="successMessage" style="font-family: 'Courier New', Courier, monospace; font-size: 14px; color: green; text-align: center; margin-top: 10px;">
                Registration Successful! 
            </p>
            <script>
                setTimeout(function() {
                    window.location.href = "login.html";
                }, 3000);
            </script>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return handleFormSubmit(event)">
            <!-- Your form fields remain the same -->
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" maxlength="255" required>
            
            <label for="birthdate">Birthdate:</label>
            <input type="date" id="birthdate" name="birthdate" required>
            
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" maxlength="255" required>
            
            <label for="mobile">Mobile Number:</label>
            <input type="text" id="mobile" name="mobile" maxlength="15" required>
            
            <label for="optional_mobile">Optional Mobile:</label>
            <input type="text" id="optional_mobile" name="optional_mobile" maxlength="15">
            
            <label for="qualification">Qualification:</label>
            <input type="text" id="qualification" name="qualification" maxlength="255" required>
            
            <label for="specialization">Specialization:</label>
            <input type="text" id="specialization" name="specialization" maxlength="255" required>
            
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" maxlength="255" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <p id="passwordError" style="color: red; display: none;">Passwords do not match!</p>
            
            <div class="submit">
                <button type="submit">Submit</button>
                <button type="reset">Reset</button>
            </div>
        </form>
    </div>
</body>
</html>