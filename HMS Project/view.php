<?php
session_start();


// Debugging: Check if session is set
if (!isset($_SESSION['patient_id'])) {
    echo "Session is not set. Redirecting...";
    exit();
}

$patientId = $_SESSION['patient_id'];


// Proceed with fetching the user's appointments


// Database connection
$servername = "localhost";
$username = "u835275566_P";
$password = "Paresh@2331";
$dbname = "u835275566_P";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch appointments for the logged-in patient only
    $stmt = $conn->prepare("SELECT a.*, 
                                   p.fullname AS patient_name, 
                                   p.	mobile AS patient_contact, 
                                   d.full_name AS doctor_name, 
                                   d.specialization 
                            FROM appointments a 
                            JOIN patient p ON a.patient_id = p.id 
                            JOIN doctors d ON a.doctor_id = d.id 
                            WHERE a.patient_id = :patient_id 
                            ORDER BY a.appointment_date ASC");
    $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #007bb5;
            font-size: 28px;
            margin-bottom: 25px;
            border-bottom: 2px solid #007bb5;
            padding-bottom: 10px;
        }

        .appointment-card {
            background-color: #fff;
            border-left: 4px solid #007bb5;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .patient-name {
            font-size: 18px;
            font-weight: bold;
            color: #007bb5;
        }

        .appointment-date {
            background-color: #007bb5;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
        }

        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            font-size: 15px;
        }

        .detail-item strong {
            color: #007bb5;
            display: block;
            margin-bottom: 3px;
        }

        

        .status-pending {
            background-color: #FFC107;
            color: #333;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 13px;
        }

        .status.Approved {
    background: #27ae60;
    color: #fff;
}
        .status.Rejected {
    background: #e74c3c;
    color: #fff;
}


        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 15px;
            }

            .appointment-details {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>My Appointments</h1>

    <?php if (empty($appointments)): ?>
        <p>No appointments found.</p>
    <?php else: ?>
        <?php foreach ($appointments as $appointment): ?>
            <div class="appointment-card">
                <div class="appointment-header">
                    <span class="patient-name"><?= htmlspecialchars($appointment['patient_name']) ?></span>
                    <span class="appointment-date">
                        <?= date("d M Y, h:i A", strtotime($appointment['appointment_date'])) ?>
                    </span>
                </div>
                <div class="appointment-details">
                    <div class="detail-item">
                        <strong>Doctor</strong>
                        Dr. <?= htmlspecialchars($appointment['doctor_name']) ?> (<?= htmlspecialchars($appointment['specialization']) ?>)
                    </div>
                    <div class="detail-item">
                        <strong>Contact</strong>
                        <?= htmlspecialchars($appointment['patient_contact']) ?>
                    </div>
                    <div class="detail-item">
                        <strong>Status</strong>
                        <span class="status-<?= strtolower($appointment['status']) ?>">
                            <?= htmlspecialchars($appointment['status']) ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <strong>Reason</strong>
                        <?= htmlspecialchars($appointment['reason']) ?: 'N/A' ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
