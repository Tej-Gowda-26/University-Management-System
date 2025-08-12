<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$db = "UniversityManagementSystem";
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $roll_number = $_POST['roll_number'];
    $section = $_POST['section'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
    $department = $_POST['department'];
    $programme = $_POST['programme'];
    $batch = $_POST['batch'];
    $admission_type = $_POST['admission_type'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Prepare the insert statement
    $sql = "INSERT INTO student (username, full_name, roll_number, section, semester, year, department, programme, batch, admission_type, date_of_birth, gender, blood_group, phone_number, email, address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssssss", $username, $full_name, $roll_number, $section, $semester, $year, $department, $programme, $batch, $admission_type, $date_of_birth, $gender, $blood_group, $phone_number, $email, $address);

    // Execute statement and check for success
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student added successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding student: " . $stmt->error]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
