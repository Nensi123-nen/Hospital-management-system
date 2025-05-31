<?php
session_start();

// Redirect if not logged in as doctor
if (!isset($_SESSION['doctor_logged_in']) || $_SESSION['doctor_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$doctorName = $_SESSION['fullname'] ?? 'Doctor';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Doctor Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #cce7f5, #a3d5ec);

    }

    header {
      background-color: #007bb5;
      color: white;
      padding: 15px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px; 
    }

    .left-header {
      font-size: 20px;
      font-weight: bold;
    }

    nav {
      background-color: #0288d1;
      display: flex;
      justify-content: center;
      padding: 12px 0;
      margin-bottom: 10px;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin: 0 25px;
      font-weight: bold;
      display: flex;
      align-items: center;
      transition: color 0.3s ease;
    }

    nav a i {
      margin-right: 6px;
    }

    nav a:hover {
      color: #e0f7fa; /* soft blue hover */
    }

    .user-circle {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #b3e5fc;
      color: #007bb5;
      font-size: 20px;
      margin-right: 10px;
    }

    .welcome-popup {
      display: none;
      position: absolute;
      top: 45px;
      right: 0;
      background-color: #e1f5fe;
      color: #007bb5;
      padding: 10px;
      border-radius: 8px;
      box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
      white-space: nowrap;
      z-index: 10;
    }

    .container {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      padding: 40px;
      gap: 30px;
      margin-top: 30px;
    }

    .section {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      margin-top: 10px;
      padding: 30px;
      width: 300px;
      text-align: center;
      backdrop-filter: blur(8px);
      border: 2px solid #b3e5fc;
      box-shadow: 0 4px 12px rgba(0, 123, 181, 0.2);
      transition: transform 0.3s ease-in-out;
      animation: fadeInUp 0.7s ease-in-out;
    }

    .section:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(0, 123, 181, 0.3);
    }

    .section i {
      font-size: 40px;
      color: #0288d1;
      margin-bottom: 12px;
    }

    .section h3 {
      color: #007bb5;
      margin: 10px 0;
    }

    .section p {
      font-size: 14px;
      color: #0277bd;
      margin-bottom: 20px;
    }

    .btn {
      padding: 10px 18px;
      background-color: #007bb5;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn:hover {
      background-color: #005f86;
      transform: scale(1.05);
    }

    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <header>
    <div class="left-header">
      <i class="fas fa-hospital"></i> Hospital Management System
    </div>
    <div class="user-circle" onclick="toggleWelcome()">
      <i class="fas fa-user"></i>
      <div id="welcome-text" class="welcome-popup">Welcome,  <?php echo htmlspecialchars($doctorName); ?>!</div>
    </div>
  </header>

  <nav>
    <a href="#"><i class="fas fa-calendar-check"></i> Appointment</a>
    <a href="#"><i class="fas fa-user-md"></i> Doctor Schedule</a>
    <a href="#"><i class="fas fa-notes-medical"></i> Description</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>

  <div class="container">
    <div class="section">
      <i class="fas fa-calendar-alt"></i>
      <h3>Appointment</h3>
      <p>Book or view your appointments with doctors.</p>
      <button class="btn" onclick="window.location.href='appointment.php'">See Appointments</button>
    </div>

    <div class="section">
      <i class="fas fa-clock"></i>
      <h3>Doctor Schedule</h3>
      <p>Check available doctor schedules and timings.</p>
      <button class="btn" onclick="window.location.href='doctor_schedule.php'">Generate Schedules</button>
    </div>

    <div class="section">
      <i class="fas fa-file-medical-alt"></i>
      <h3>Doctors Description</h3>
      <p>Add patient diagnosis and description data.</p>
      <button class="btn" onclick="window.location.href='medical_record_upload.php'">Add Patient Disease</button>
    </div>
  </div>

  <script>
    function toggleWelcome() {
      const welcomeText = document.getElementById('welcome-text');
      welcomeText.style.display = welcomeText.style.display === 'block' ? 'none' : 'block';
    }
  </script>
</body>
</html>
