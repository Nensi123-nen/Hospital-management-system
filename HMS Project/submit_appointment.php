<?php
session_start();

// Database credentials
$servername = "localhost";
$db_username = "u835275566_P";
$db_password = "Paresh@2331";
$dbname = "u835275566_P";

// Establish database connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$logged_in_patient_id = $_SESSION['patient_id'] ?? null;
$patientData = [
    'fullname' => '',
    'email' => '',
    'mobile' => ''
];

if ($logged_in_patient_id) {
    $stmt = $conn->prepare("SELECT fullname, email, mobile FROM patient WHERE id = ?");
    $stmt->execute([$logged_in_patient_id]);
    $patientData = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch specializations and doctors
$specializations = [];
$doctors = [];

try {
    // Fetch unique specializations
    $spec_result = $conn->query("SELECT DISTINCT specialization FROM doctors");
    while ($row = $spec_result->fetch(PDO::FETCH_ASSOC)) {
        $specializations[] = $row['specialization'];
    }

    // Fetch all doctors grouped by specialization
    $doc_result = $conn->query("SELECT id, full_name, specialization, qualification FROM doctors");
    while ($row = $doc_result->fetch(PDO::FETCH_ASSOC)) {
        $doctors[$row['specialization']][] = $row;
    }
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = trim($_POST['patient_name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $appointment_date = $_POST['date'];
    $department = $_POST['specialization']; // renamed for clarity
    $doctor_id = $_POST['doctor'];
    $reason = trim($_POST['reason'] ?? '');

    if (empty($patient_name) || empty($email) || empty($contact) || empty($appointment_date) || empty($department) || empty($doctor_id)) {
        die("All fields are required!");
    }

    try {
        // Get patient ID from patient table
        $stmt = $conn->prepare("SELECT id FROM patient WHERE fullname = ?");
        $stmt->execute([$patient_name]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$patient) {
            die("Error: Patient not found in the database!");
        }

        $patient_id = $patient['id'];

        // Insert appointment
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, department, reason, status) 
                                VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$patient_id, $doctor_id, $appointment_date, $department, $reason]);

        header("Location: appointment_success.html");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
            color: #333;
        }

        h2 {
            color: #007bb5;
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #007bb5;
            color: white;
            padding: 15px;
            font-size: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }

        button[type="submit"] {
            background-color: #007bb5;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: auto;
            margin: 10px auto;
            display: block;
        }

        button[type="submit"]:hover {
            background-color: #005f86;
        }

        .doctor-qualification {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
            font-style: italic;
            display: block;
        }

        @media (max-width: 600px) {
            form {
                padding: 20px;
            }

            th, td {
                padding: 10px;
            }

            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <h2>Appointment Booking Form</h2>
    <form action="" method="post">
        <table>
            <tr>
                <th colspan="2">Book an Appointment</th>
            </tr>
<tr>
    <td>Patient Name:</td>
    <td>
        <input type="text" name="patient_name" required 
               value="<?= htmlspecialchars($patientData['fullname'] ?? '') ?>" readonly>
    </td>
</tr>
<tr>
    <td>Email:</td>
    <td>
        <input type="email" name="email" required 
               value="<?= htmlspecialchars($patientData['email'] ?? '') ?>" readonly>
    </td>
</tr>
<tr>
    <td>Contact Number:</td>
    <td>
        <input type="tel" name="contact" required 
               value="<?= htmlspecialchars($patientData['mobile'] ?? '') ?>" readonly>
    </td>
</tr>

            <tr>
                <td>Preferred Date:</td>
                <td><input type="date" name="date" required></td>
            </tr>
            <tr>
                <td>Select Department (Specialization):</td>
                <td>
                    <select name="specialization" id="specialization" required onchange="filterDoctors()">
                        <option value="">-- Select Department --</option>
                        <?php foreach ($specializations as $spec): ?>
                            <option value="<?= htmlspecialchars($spec) ?>"><?= htmlspecialchars($spec) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Select Doctor:</td>
                <td>
                    <select name="doctor" id="doctor" required>
                        <option value="">-- Select Doctor --</option>
                    </select>
                    <span id="qualification-display" class="doctor-qualification"></span>
                </td>
            </tr>
            <tr>
                <td>Reason (optional):</td>
                <td><input type="text" name="reason"></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;">
                    <button type="submit">Book Appointment</button>
                </td>
            </tr>
        </table>
    </form>

    <script>
        const doctorData = <?= json_encode($doctors) ?>;

        function filterDoctors() {
            const specialization = document.getElementById("specialization").value;
            const doctorSelect = document.getElementById("doctor");
            const qualificationDisplay = document.getElementById("qualification-display");

            doctorSelect.innerHTML = `<option value="">-- Select Doctor --</option>`;
            qualificationDisplay.textContent = "";

            if (doctorData[specialization]) {
                doctorData[specialization].forEach(doc => {
                    const option = document.createElement("option");
                    option.value = doc.id;
                    option.textContent = ` ${doc.full_name} (${doc.specialization})`;
                    option.setAttribute("data-qualification", doc.qualification);
                    doctorSelect.appendChild(option);
                });
            }
        }

        document.getElementById('doctor').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const qualification = selectedOption.getAttribute('data-qualification');
            document.getElementById('qualification-display').textContent = qualification || '';
        });
    </script>
</body>
</html>
