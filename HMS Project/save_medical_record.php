if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form inputs
    $appointment_id = $_POST['patient_select'];
    $doctor_notes = $_POST['doctor_notes'];
    $visit_date = $_POST['visit_date'];
    
    // Handle file upload
    $image_name = $_FILES['prescription_image']['name'];
    $temp_name = $_FILES['prescription_image']['tmp_name'];
    $folder = "uploads/" . $image_name;

    if (move_uploaded_file($temp_name, $folder)) {
        // Save medical record to database
        $insert_query = "INSERT INTO medical_records (appointment_id, doctor_notes, visit_date, prescription_image) 
                         VALUES ('$appointment_id', '$doctor_notes', '$visit_date', '$image_name')";
        if (mysqli_query($conn, $insert_query)) {
            
            // ✅ STEP 3: Mark appointment as record_uploaded = 'Yes'
            $update_query = "UPDATE appointments SET record_uploaded = 'Yes' WHERE appointment_id = '$appointment_id'";
            mysqli_query($conn, $update_query);
            
            echo "<script>alert('Medical record uploaded successfully.'); window.location.href='medical_record_upload.php';</script>";
        } else {
            echo "Error saving medical record: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload prescription image.";
    }
}
