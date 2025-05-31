<?php
session_start();

// Check if the patient is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$loggedInPatientId = $_SESSION['patient_id'];

// DB connection
$host = "localhost";
$dbname = "u835275566_P";
$username = "u835275566_P";
$password = "Paresh@2331";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

// Handle individual check-in
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkin_id'])) {
    $appointment_id = $_POST['checkin_id'];

    // Prevent duplicate check-in
    $check_sql = "SELECT id FROM patient_checkins WHERE appointment_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->execute([$appointment_id]);

    if ($stmt_check->rowCount() == 0) {
        $insert_sql = "
            INSERT INTO patient_checkins (appointment_id, checkin_time, status, created_at)
            VALUES (?, NOW(), 'Checked-in', NOW())
        ";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->execute([$appointment_id]);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Get already checked-in appointments for logged-in patient
$sql_checked_in = "
    SELECT 
        a.id AS appointment_id,
        p.fullname AS patient_name,
        a.appointment_date,
        d.full_name AS doctor_name,
        a.reason,
        pc.checkin_time,
        pc.checkout_time,
        pc.status AS checkin_status,
        pc.nurse_notes
    FROM appointments a
    JOIN patient p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    JOIN patient_checkins pc ON pc.appointment_id = a.id
    WHERE a.status = 'Completed' AND a.patient_id = :patient_id
    ORDER BY a.appointment_date DESC
";
$stmt1 = $conn->prepare($sql_checked_in);
$stmt1->execute([':patient_id' => $loggedInPatientId]);
$checked_in = $stmt1->fetchAll(PDO::FETCH_ASSOC);

// Get not checked-in appointments for logged-in patient
$sql_not_checked_in = "
    SELECT 
        a.id AS appointment_id,
        p.fullname AS patient_name,
        a.appointment_date,
        d.full_name AS doctor_name,
        a.reason
    FROM appointments a
    JOIN patient p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
    LEFT JOIN patient_checkins pc ON pc.appointment_id = a.id
    WHERE a.status = 'Completed' AND pc.id IS NULL AND a.patient_id = :patient_id
    ORDER BY a.appointment_date DESC
";
$stmt2 = $conn->prepare($sql_not_checked_in);
$stmt2->execute([':patient_id' => $loggedInPatientId]);
$not_checked_in = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Check-ins</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #007bb5;
            border-bottom: 2px solid #007bb5;
            margin-bottom: 20px;
        }

        .patient-card {
            border-left: 4px solid #007bb5;
            background: #fff;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .details {
            margin: 5px 0;
            color: #444;
        }

        .patient-name {
            font-size: 18px;
            font-weight: bold;
            color: #007bb5;
        }

        form {
            display: inline;
        }

        button {
            background: #007bb5;
            color: white;
            padding: 7px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #005f8a;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            button {
                width: 100%;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Appointments Ready for Check-in</h2>
    <?php if (count($not_checked_in) > 0): ?>
        <?php foreach ($not_checked_in as $row): ?>
            <div class="patient-card">
                <p class="patient-name"><?= htmlspecialchars($row['patient_name']) ?></p>
                <p class="details"><strong>Date:</strong> <?= date("d-m-Y", strtotime($row['appointment_date'])) ?></p>
                <p class="details"><strong>Doctor:</strong> Dr. <?= htmlspecialchars($row['doctor_name']) ?></p>
                <p class="details"><strong>Reason:</strong> <?= htmlspecialchars($row['reason']) ?></p>
                <form method="POST">
                    <input type="hidden" name="checkin_id" value="<?= $row['appointment_id'] ?>">
                    <button type="submit">Check-in</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>All appointments are already checked in 🎉</p>
    <?php endif; ?>
</div>

<div class="container">
    <h2>Already Checked-in Appointments</h2>
    <?php if (count($checked_in) > 0): ?>
        <?php foreach ($checked_in as $row): ?>
            <div class="patient-card">
                <p class="patient-name"><?= htmlspecialchars($row['patient_name']) ?></p>
                <p class="details"><strong>Date:</strong> <?= date("d-m-Y", strtotime($row['appointment_date'])) ?></p>
                <p class="details"><strong>Doctor:</strong> Dr. <?= htmlspecialchars($row['doctor_name']) ?></p>
                <p class="details"><strong>Status:</strong> <?= $row['checkin_status'] ?></p>
                <p class="details"><strong>Check-in:</strong> <?= date("d-m-Y h:i A", strtotime($row['checkin_time'])) ?></p>
                <p class="details"><strong>Checkout:</strong> <?= $row['checkout_time'] ? date("d-m-Y h:i A", strtotime($row['checkout_time'])) : 'Not yet' ?></p>
                <p class="details"><strong>Nurse Notes:</strong> <?= $row['nurse_notes'] ?? 'No notes yet' ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No check-ins done yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
