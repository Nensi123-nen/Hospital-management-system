<?php
// Show all PHP errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$host = "localhost";
$dbname = "u835275566_P";
$username = "u835275566_P";
$password = "Paresh@2331";

// Create DB connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Validate POST data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bill_id = isset($_POST['bill_id']) ? intval($_POST['bill_id']) : 0;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0.00;
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

    if ($bill_id == 0 || $amount == 0 || $payment_method == '') {
        die("❗ Invalid input data received. Please check your form fields.");
    }

    $payment_date = date("Y-m-d H:i:s");
    $transaction_id = uniqid('TXN');
    $status = "Success";
    $notes = "Payment done via $payment_method";
    $created_at = date("Y-m-d H:i:s");

    // Prepare SQL query
    $sql = "INSERT INTO payments (bill_id, amount, payment_date, transaction_id, payment_method, status, notes, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("idssssss", $bill_id, $amount, $payment_date, $transaction_id, $payment_method, $status, $notes, $created_at);

    if ($stmt->execute()) {
        echo "<h2>✅ Payment Successful!</h2>";
        echo "<p><strong>Transaction ID:</strong> $transaction_id</p>";
        echo "<p><strong>Amount Paid:</strong> ₹$amount</p>";
        echo "<p><strong>Method:</strong> $payment_method</p>";
        echo "<br><a href='payment.html'>← Back to Payment Page</a>";
    } else {
        echo "<h2>❌ Payment Failed!</h2>";
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
} else {
    echo "⚠️ This page only handles POST requests.";
}

$conn->close();
?>
