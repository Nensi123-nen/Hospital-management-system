<?php
session_start();

if (!isset($_SESSION['patient_id'])) {
    echo "You are not logged in.";
    exit();
}

$patientId = $_SESSION['patient_id'];

$servername = "localhost";
$username = "u835275566_P";
$password = "Paresh@2331";
$dbname = "u835275566_P";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmtPatient = $conn->prepare("SELECT fullname, birthdate, id FROM patient WHERE id = :id");
    $stmtPatient->bindParam(':id', $patientId);
    $stmtPatient->execute();
    $patient = $stmtPatient->fetch(PDO::FETCH_ASSOC);

    function calculateAge($dob) {
        if (!$dob) return "N/A";
        $dob = new DateTime($dob);
        $today = new DateTime();
        return $dob->diff($today)->y;
    }

    $age = isset($patient['birthdate']) ? calculateAge($patient['birthdate']) : "N/A";

    $stmtAppointments = $conn->prepare("
        SELECT a.appointment_date, a.reason, a.department, d.full_name AS doctor_name
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.patient_id = :pid AND a.status = 'Approved'
        ORDER BY a.appointment_date DESC
    ");
    $stmtAppointments->bindParam(':pid', $patientId);
    $stmtAppointments->execute();
    $appointments = $stmtAppointments->fetchAll(PDO::FETCH_ASSOC);

    $lastVisit = isset($appointments[0]) ? date("d-m-Y", strtotime($appointments[0]['appointment_date'])) : "N/A";

    $stmtRecords = $conn->prepare("
        SELECT mr.visit_date, mr.prescription, mr.notes, mr.prescription_image, d.full_name AS doctor_name
        FROM medical_records mr
        JOIN doctors d ON mr.doctor_id = d.id
        WHERE mr.patient_id = :pid
        ORDER BY mr.visit_date DESC
    ");

    $stmtRecords->bindParam(':pid', $patientId);
    $stmtRecords->execute();
    $medicalRecords = $stmtRecords->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    die("A database error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Medical History</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
            margin: 0;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h2, h3 {
            color: #007bb5;
            border-bottom: 2px solid #007bb5;
            padding-bottom: 10px;
        }

        .patient-info, .section {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bb5;
        }

        .patient-info p, .section p {
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #007bb5;
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f0f0f0;
        }

        .download-btn {
            background-color: #0288d1;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .download-btn:hover {
            background-color: #007bb5;
        }

        .prescription-img {
            max-width: 150px;
            max-height: 150px;
            border: 1px solid #ddd;
            padding: 2px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<div class="container" id="historyContent">
    <h2>Patient Medical History</h2>

    <div class="patient-info">
        <p><strong>Name:</strong> <?= htmlspecialchars($patient['fullname']) ?></p>
        <p><strong>Age:</strong> <?= $age ?> years</p>
        <p><strong>Patient ID:</strong> HMS-<?= str_pad($patient['id'], 4, '0', STR_PAD_LEFT) ?></p>
        <p><strong>Last Visit:</strong> <?= $lastVisit ?></p>
    </div>

    <button class="download-btn" onclick="downloadPDF()">Download PDF</button>

    <div class="section">
        <h3>Visit Records (Completed Appointments)</h3>
        <?php if (empty($appointments)): ?>
            <p>No completed appointments found.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Reason</th>
                    <th>Department</th>
                    <th>Doctor</th>
                </tr>
                <?php foreach ($appointments as $appt): ?>
                    <tr>
                        <td><?= date("d-m-Y", strtotime($appt['appointment_date'])) ?></td>
                        <td><?= htmlspecialchars($appt['reason'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($appt['department']) ?></td>
                        <td><?= htmlspecialchars($appt['doctor_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <div class="section">
        <h3>Medical Records (Notes & Prescription)</h3>
        <?php if (empty($medicalRecords)): ?>
            <p>No medical records found.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Doctor</th>
                    <th>Prescription</th>
                    <th>Description</th>
                </tr>
                <?php foreach ($medicalRecords as $record): ?>
                    <tr>
                        <td><?= date("d-m-Y", strtotime($record['visit_date'])) ?></td>
                        <td><?= htmlspecialchars($record['doctor_name']) ?></td>
<td>
    <?php if (!empty($record['prescription_image'])): ?>
        <?php
            $imagePath = 'https://cricbidz.com/HMS/HMS Project/' . $record['prescription_image'];
            $filename = basename($imagePath);
        ?>
        <a href="<?= $imagePath ?>" target="_blank"><?= htmlspecialchars($filename) ?></a>
    <?php else: ?>
        <span>No prescription image</span>
    <?php endif; ?>
</td>

                        <td><?= nl2br(htmlspecialchars($record['notes'] ?? '-')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
function downloadPDF() {
    const element = document.getElementById('historyContent');
    html2pdf().from(element).set({
        margin: 0.5,
        filename: 'Medical_History.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    }).save();
}
</script>

</body>
</html>