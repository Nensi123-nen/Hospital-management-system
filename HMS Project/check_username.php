<?php
// Database connection using PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=u835275566_P", "u835275566_P", "Paresh@2331");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed.");
}

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $stmt = $conn->prepare("SELECT id FROM doctors WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "taken";
    } else {
        echo "available";
    }
}
?>
