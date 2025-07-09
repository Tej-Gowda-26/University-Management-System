<?php
session_start();

if ($_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$db = "UniversityManagementSystem";

$conn = new mysqli($servername, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_code = $_POST['course_code'];
    $student_roll_number = $_POST['student_roll_number'];
    $total_classes = $_POST['total_classes'];
    $attended_classes = $_POST['attended_classes'];

    $percentage = ($attended_classes / $total_classes) * 100;

    $check_sql = "SELECT * FROM attendance WHERE course_code = ? AND student_roll_number = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $course_code, $student_roll_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $update_sql = "UPDATE attendance SET total_classes = ?, attended_classes = ?, percentage = ? WHERE course_code = ? AND student_roll_number = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iiiss", $total_classes, $attended_classes, $percentage, $course_code, $student_roll_number);
        if ($update_stmt->execute()) {
            header("Location: faculty_dashboard.php?attendance_updated=true");
            exit();
        } else {
            header("Location: faculty_dashboard.php?attendance_update_error=true");
            exit();
        }
    } else {
        $insert_sql = "INSERT INTO attendance (course_code, student_roll_number, total_classes, attended_classes, percentage) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssidd", $course_code, $student_roll_number, $total_classes, $attended_classes, $percentage);

        if ($insert_stmt->execute()) {
            header("Location: faculty_dashboard.php?attendance_inserted=true");
            exit();
        } else {
            header("Location: faculty_dashboard.php?attendance_insert_error=true");
            exit();
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
?>
