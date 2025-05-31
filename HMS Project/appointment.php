<?php
session_start();

// Redirect if doctor is not logged in
if (!isset($_SESSION['doctor_id'])) {
    echo "Doctor session not found. Redirecting...";
    exit();
}

$doctorId = $_SESSION['doctor_id'];

// Database connection
$servername = "localhost";
$username = "u835275566_P";
$password = "Paresh@2331";
$dbname = "u835275566_P";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Handle approval form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $appointmentId = $_POST['appointment_id'];

    // Check if 'reject' button was clicked
    if (isset($_POST['reject'])) {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Rejected' WHERE id = ?");
        $stmt->execute([$appointmentId]);
        header("Location: dhome.php");
        exit();
    } else {
        // Default: approve
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Approved' WHERE id = ?");
        $stmt->execute([$appointmentId]);
        header("Location: dhome.php");
        exit();
    }
}


    // ✅ Fetch appointments assigned to this doctor
    $stmt = $conn->prepare("SELECT a.*, p.fullname AS patient_name, p.mobile AS patient_contact
                            FROM appointments a
                            JOIN patient p ON a.patient_id = p.id
                            WHERE a.doctor_id = :doctor_id
                            ORDER BY a.appointment_date ASC");
    $stmt->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
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
    <title>Doctor Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .appointment-card {
            border-left: 5px solid #3498db;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: #ecf0f1;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .patient-name {
            font-size: 18px;
            font-weight: bold;
            color: #2980b9;
        }

        .appointment-date {
            font-size: 14px;
            color: #7f8c8d;
        }

        .details {
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
        }

        .details div {
            font-size: 14px;
        }

        .status {
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .status.Pending {
            background: #f39c12;
            color: #fff;
        }

        .status.Approved {
            background: #27ae60;
            color: #fff;
        }
        
        .status.Rejected {
    background: #e74c3c;
    color: #fff;
}


        .approve-button {
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .approve-button:hover {
            background-color: #27ae60;
        }

.reject-button {
    margin-top: 10px;
    padding: 8px 15px;
    background-color: #e74c3c;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    margin-left: 10px;
}

.reject-button:hover {
    background-color: #c0392b;
}

        @media (max-width: 768px) {
            .details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Doctor Appointments</h1>

    <?php if (empty($appointments)): ?>
        <p>No appointments found.</p>
    <?php else: ?>
        <?php foreach ($appointments as $appointment): ?>
            <div class="appointment-card">
                <div class="header">
                    <span class="patient-name"><?= htmlspecialchars($appointment['patient_name']) ?></span>
                    <span class="appointment-date">
                        <?= date("d M Y, h:i A", strtotime($appointment['appointment_date'])) ?>
                    </span>
                </div>
                <div class="details">
                    <div><strong>Contact:</strong> <?= htmlspecialchars($appointment['patient_contact']) ?></div>
                    <div><strong>Reason:</strong> <?= htmlspecialchars($appointment['reason']) ?: 'N/A' ?></div>
                    <div>
                        <strong>Status:</strong>
                        <span class="status <?= htmlspecialchars($appointment['status']) ?>">
                            <?= htmlspecialchars($appointment['status']) ?>
                        </span>
                    </div>
                </div>

                <?php if ($appointment['status'] === 'Pending'): ?>
    <form method="POST">
        <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
        <button class="approve-button" type="submit">Approve</button>
        <button class="reject-button" name="reject" type="submit">Reject</button>
    </form>
<?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
