<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$conn = new mysqli("localhost", "u835275566_P", "Paresh@2331", "u835275566_P");

// Fetch patients with approved appointments
$patients_result = $conn->query("
    SELECT a.id AS appointment_id, p.fullname 
    FROM appointments a 
    JOIN patient p ON a.patient_id = p.id 
    WHERE a.status = 'Approved' AND a.record_uploaded = 'No'
");

$selected_data = null;

// If a patient is selected from the dropdown
if (isset($_POST['patient_select'])) {
    $appointment_id = $_POST['patient_select'];
    $sql = "SELECT a.*, p.fullname AS patient_name, d.full_name AS doctor_name
            FROM appointments a
            JOIN patient p ON a.patient_id = p.id
            JOIN doctors d ON a.doctor_id = d.id
            WHERE a.id = $appointment_id";

    $selected_data = $conn->query($sql)->fetch_assoc();
}

// Handle form submission
if (isset($_POST['submit_record'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_id = $_POST['appointment_id'];
    $visit_date = $_POST['visit_date'];
    $notes = $_POST['notes'];

    // Upload image
    $targetDir = "uploads/";
    $fileName = basename($_FILES["prescription_img"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    move_uploaded_file($_FILES["prescription_img"]["tmp_name"], $targetFilePath);

    // Update appointment record_uploaded flag
    $update_query = "UPDATE appointments SET record_uploaded = 'Yes' WHERE id = '$appointment_id'";
    mysqli_query($conn, $update_query);

    // Insert into medical_records
    $insert = "INSERT INTO medical_records 
        (patient_id, doctor_id, visit_date, prescription, notes, prescription_image, created_at)
        VALUES ('$patient_id', '$doctor_id', '$visit_date', '$fileName', '$notes', '$targetFilePath', NOW())";

    if ($conn->query($insert)) {
        $message = "Medical record uploaded successfully.";
    } else {
        $message = "Upload failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medical Record Upload</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; }
        label { display: block; margin: 15px 0 5px; }
        select, input, textarea { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
        .btn { margin-top: 15px; padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .message { margin-top: 15px; font-weight: bold; color: green; }
    </style>
</head>
<body>
<div class="container">
    <h2>Upload Medical Record</h2>

    <?php if (isset($message)) echo "<div class='message'>$message</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="patient_select">Select Patient (Approved Appointments Only):</label>
        <select name="patient_select" id="patient_select" onchange="this.form.submit()">
            <option value="">-- Choose Patient --</option>
            <?php while ($row = $patients_result->fetch_assoc()) { ?>
                <option value="<?= $row['appointment_id'] ?>" <?= (isset($selected_data) && $selected_data['id'] == $row['appointment_id']) ? 'selected' : '' ?>>
                    <?= $row['fullname'] ?>
                </option>
            <?php } ?>
        </select>
    </form>

    <?php if ($selected_data): ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="appointment_id" value="<?= $selected_data['id'] ?>">
            <input type="hidden" name="patient_id" value="<?= $selected_data['patient_id'] ?>">
            <input type="hidden" name="doctor_id" value="<?= $selected_data['doctor_id'] ?>">

            <label>Patient Name:</label>
            <input type="text" value="<?= $selected_data['patient_name'] ?>" readonly>

            <label>Doctor Name:</label>
            <input type="text" value="<?= $selected_data['doctor_name'] ?>" readonly>

            <label>Visit Date:</label>
            <input type="date" name="visit_date" value="<?= date('Y-m-d') ?>" required>

            <label>Upload Prescription Image:</label>
            <input type="file" name="prescription_img" accept="image/*" required>

            <label>Doctor Notes:</label>
            <textarea name="notes" placeholder="Enter doctor's notes here..."></textarea>

            <button type="submit" class="btn" name="submit_record">Submit Record</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
