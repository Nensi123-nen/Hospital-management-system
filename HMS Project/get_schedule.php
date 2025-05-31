<?php
header("Content-Type: application/json");

$servername = "localhost";
$db_username = "u835275566_P";
$db_password = "Paresh@2331";
$dbname = "u835275566_P";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Join doctors and schedule table and fetch required fields
$sql = "SELECT d.full_name AS name, d.specialization, 
               s.day, s.time_slot, s.status 
        FROM doctor_schedule s
        JOIN doctors d ON s.doctor_id = d.id
        ORDER BY d.full_name, FIELD(s.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>
