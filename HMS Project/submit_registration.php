<?php
// Enable error reporting
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
    $fullname = $_POST['fullname'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $optional_mobile = !empty($_POST['optional-mobile']) ? $_POST['optional-mobile'] : null;
    $address = $_POST['address'];
    $city = $_POST['city'];
    $bloodgroup = $_POST['bloodgroup'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Check if username or email already exists
    $check_sql = "SELECT id FROM patient WHERE username = :username OR email = :email";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':username', $username);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        $error_message = "Username or email already exists!";
    } else {
        // Insert data into the database
        $sql = "INSERT INTO patient (fullname, birthdate, gender, email, mobile, optional_mobile, address, city, bloodgroup, username, password, created_at) 
                VALUES (:fullname, :birthdate, :gender, :email, :mobile, :optional_mobile, :address, :city, :bloodgroup, :username, :password, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mobile', $mobile);
        $stmt->bindParam(':optional_mobile', $optional_mobile);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':bloodgroup', $bloodgroup);
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
    <title>Registration Page</title>
    <link rel="stylesheet" href="styles-pat.css">
    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value;
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
        <h2>Patient Registration</h2>

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
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="birthdate">Birthdate:</label>
            <input type="date" id="birthdate" name="birthdate" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="mobile">Mobile Number:</label>
            <input type="text" id="mobile" name="mobile" required>

            <label for="optional-mobile">Optional Mobile Number:</label>
            <input type="text" id="optional-mobile" name="optional-mobile">

            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="3" required></textarea>

            <label for="city">City:</label>
            <select id="city" name="city" required>
                <option value="" disabled selected>Select City</option>
                <option value="ahmedabad">Ahmedabad</option>
                <option value="vadodara">Vadodara</option>
                <option value="rajkot">Rajkot</option>
                <option value="Gandhinagar">Gandhinagar</option>
                <option value="Jamnagar">Jamnagar</option>
                <option value="Junagadh">Junagadh</option>
                <option value="Anand">Anand</option>
                <option value="Bhavnagar">Bhavnagar</option>
                <option value="Surat">Surat</option>
                <option value="Porbandar">Porbandar</option>
                <option value="Bharuch">Bharuch</option>
                <option value="Bhuj">Bhuj</option>
                <option value="Mehsana">Mehsana</option>
                <option value="Navsari">Navsari</option>
                <option value="Surendranagar">Surendranagar</option>
            </select>

            <label for="bloodgroup">Blood Group:</label>
            <select id="bloodgroup" name="bloodgroup" required>
                <option value="" disabled selected>Select Blood Group</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>

            <label for="username">Username:</label>
<input type="text" id="username" name="username" required onkeyup="checkUsernameAvailability()">
<span id="username-status"></span>


            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" required>

            <span id="passwordError" style="color: red; display: none;">Passwords do not match</span>

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
