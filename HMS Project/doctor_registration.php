<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection using PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=u835275566_P", "u835275566_P", "Paresh@2331");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$registration_success = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $optional_mobile = $_POST['optional_mobile'];
    $qualification = $_POST['qualification'];
    $specialization = $_POST['specialization'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Check if username or email already exists
    $check_sql = "SELECT id FROM doctors WHERE username = :username OR email = :email";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':username', $username);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        $error_message = "Username or email already exists!";
    } else {
        // Insert data into the database
        $sql = "INSERT INTO doctors (full_name, birthdate, gender, email, mobile, optional_mobile, qualification, specialization, username, password) 
                VALUES (:full_name, :birthdate, :gender, :email, :mobile, :optional_mobile, :qualification, :specialization, :username, :password)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mobile', $mobile);
        $stmt->bindParam(':optional_mobile', $optional_mobile);
        $stmt->bindParam(':qualification', $qualification);
        $stmt->bindParam(':specialization', $specialization);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            $registration_success = true;
        } else {
            $error_message = "Registration failed. Please try again.";
        }
    }
}
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
            if (password !== confirmPassword) {
                document.getElementById("passwordError").style.display = "block";
                return false;
            } else {
                document.getElementById("passwordError").style.display = "none";
                return true;
            }
        }

        function handleFormSubmit(event) {
            if (!validatePassword()) {
                event.preventDefault(); // Prevent submission if passwords don't match
            }
        }
    </script>
</head>
<body>
    <div class="registration-form">
        <h2>Doctor Registration Form</h2>

        <!-- Error Message -->
        <?php if (!empty($error_message)): ?>
            <p style="color: red; text-align: center;">
                <?php echo htmlspecialchars($error_message); ?>
            </p>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if ($registration_success): ?>
            <p style="color: green; text-align: center;">
                Registration Successful!
            </p>
            <script>
                setTimeout(function() {
                    window.location.href = "login.html";
                }, 3000);
                
            </script>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="handleFormSubmit(event)">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" required>
            
            <label for="birthdate">Birthdate:</label>
            <input type="date" id="birthdate" name="birthdate" required>
            
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="mobile">Mobile Number:</label>
            <input type="text" id="mobile" name="mobile" required>
            
            <label for="optional_mobile">Optional Mobile:</label>
            <input type="text" id="optional_mobile" name="optional_mobile">
            
            <label for="qualification">Qualification:</label>
            <input type="text" id="qualification" name="qualification" required>
            
            <label for="specialization">Specialization:</label>
            <input type="text" id="specialization" name="specialization" required>
            
           <label for="username">Username:</label>
<input type="text" id="username" name="username" required onkeyup="checkUsernameAvailability()">
<span id="username-status"></span>

            
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
    <script>
        <script>
    function checkUsernameAvailability() {
        const username = document.getElementById("username").value;
        const status = document.getElementById("username-status");

        if (username.trim() === "") {
            status.innerHTML = "";
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "check_username.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (this.responseText.trim() === "taken") {
                status.style.color = "red";
                status.innerHTML = "Username is already taken.";
            } else {
                status.style.color = "green";
                status.innerHTML = "Username is available.";
            }
        };

        xhr.send("username=" + encodeURIComponent(username));
    }
</script>

    </script>
</body>
</html>
