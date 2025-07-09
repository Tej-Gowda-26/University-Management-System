<?php
session_start();

if ($_SESSION['role'] !== 'staff') {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$db = "UniversityManagementSystem";

$conn = new mysqli($servername, $username, $password, $db);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $fullName = $_POST['facultyFullName'];
    $facultyId = $_POST['facultyId'];
    $department = $_POST['department'];
    $role = $_POST['role'];
    $coursesHandled = $_POST['courseshandled'];
    $highestQualification = $_POST['highestQualification'];
    $yearsOfExperience = $_POST['yearsOfExperience'];
    $gender = $_POST['gender'];
    $phoneNumber = $_POST['phoneNumber'];
    $dateOfJoining = $_POST['dateOfJoining'];
    $email = $_POST['email'];

    $sql = "INSERT INTO faculty (username, full_name, faculty_id, department, role, courses_handled, highest_qualification, years_of_experience, gender, phone_number, date_of_joining, email) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissssissss", $username, $fullName, $facultyId, $department, $role, $coursesHandled, $highestQualification, $yearsOfExperience, $gender, $phoneNumber, $dateOfJoining, $email);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Faculty added successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding faculty."]);
    }

    $stmt->close();
}

$conn->close();
?>
