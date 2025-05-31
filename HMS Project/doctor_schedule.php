<?php
session_start();

// DB credentials
$servername = "localhost";
$db_username = "u835275566_P";
$db_password = "Paresh@2331";
$dbname = "u835275566_P";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['doctor_id'])) {
    echo "Unauthorized access. Please log in.";
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

// Fetch doctor info
$sql = "SELECT full_name, specialization FROM doctors WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Doctor not found.";
    exit;
}

$doctor = $result->fetch_assoc();

// Handle form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['schedule']) && is_array($_POST['schedule'])) {
        foreach ($_POST['schedule'] as $day) {
            $time_key = strtolower($day) . "_slot";
            $time_slot = $_POST[$time_key] ?? '';
            $status = "Available";

            if (!empty($time_slot)) {
                $insert = $conn->prepare("INSERT INTO doctor_schedule (doctor_id, day, time_slot, status) VALUES (?, ?, ?, ?)");
                $insert->bind_param("isss", $doctor_id, $day, $time_slot, $status);
                $insert->execute();
            }
        }
        echo "<script>alert('Schedule saved successfully!'); window.location.href='dhome.php';</script>";
    } else {
        echo "<script>alert('Please select at least one day.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
            padding: 20px;
        }
        .container {
            max-width: 750px;
            margin: auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #555;
        }
        input[type="text"], select, input[disabled] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            gap: 10px;
        }
        .checkbox-item select {
            flex: 1;
        }
        .buttons {
            text-align: center;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .submit-btn {
            background-color: #28a745;
        }
        .reset-btn {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Doctor Schedule Form</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label>Doctor Name:</label>
                <input type="text" value="<?php echo htmlspecialchars($doctor['full_name']); ?>" disabled>
            </div>

            <div class="form-group">
                <label>Specialization:</label>
                <input type="text" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" disabled>
            </div>

            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">

            <div class="form-group">
                <label>Fix Schedule:</label>
                <?php
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $time_slots = [
                    '9:00 AM - 11:00 AM',
                    '11:00 AM - 1:00 PM',
                    '2:00 PM - 4:00 PM',
                    '4:00 PM - 6:00 PM'
                ];
                foreach ($days as $day) {
                    $slot_name = strtolower($day) . "_slot";
                    echo '
                    <div class="checkbox-item">
                        <input type="checkbox" id="'.$day.'" name="schedule[]" value="'.$day.'">
                        <label for="'.$day.'" style="width: 100px;">'.$day.'</label>
                        <select name="'.$slot_name.'">
                            <option value="">--Select Time Slot--</option>';
                            foreach ($time_slots as $slot) {
                                echo '<option value="'.$slot.'">'.$slot.'</option>';
                            }
                    echo '</select>
                    </div>';
                }
                ?>
            </div>

            <div class="buttons">
                <button type="reset" class="reset-btn">Reset</button>
                <button type="submit" class="submit-btn">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>
