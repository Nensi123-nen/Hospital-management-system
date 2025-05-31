<?php
// get_doctors.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "u835275566_P";
$password = "Paresh@2331";
$dbname = "u835275566_P"; // Make sure this is your actual DB name

// Use the same variable names here
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, full_name AS name, specialization FROM doctors";
$result = $conn->query($sql);

$doctors = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($doctors);

$conn->close();
?>
