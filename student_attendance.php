<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$studentUsername = $_SESSION['username'];

$servername = "localhost";
$username = "root";
$password = "";
$db = "UniversityManagementSystem";

$conn = new mysqli($servername, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT roll_number FROM student WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentUsername);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$studentRollNumber = $row['roll_number'];

$stmt->close();

$coursesSql = "SELECT course_code, course_name FROM course ORDER BY course_code";
$stmt = $conn->prepare($coursesSql);
$stmt->execute();
$result = $stmt->get_result();

$allCourses = [];
while ($course = $result->fetch_assoc()) {
    $allCourses[] = $course;
}

$stmt->close();

$attendanceSql = "
    SELECT 
        a.course_code, 
        a.attended_classes,
        a.total_classes,
        a.percentage
    FROM attendance a
    WHERE a.student_roll_number = ?
";
$stmt = $conn->prepare($attendanceSql);
$stmt->bind_param("s", $studentRollNumber);
$stmt->execute();
$result = $stmt->get_result();

$updatedAttendance = [];
while ($attendance = $result->fetch_assoc()) {
    $updatedAttendance[$attendance['course_code']] = [
        'attended_classes' => $attendance['attended_classes'],
        'total_classes' => $attendance['total_classes'],
        'attendance_percentage' => $attendance['percentage'],
    ];
}

$stmt->close();

$courseAttendanceData = [];
foreach ($allCourses as $course) {
    $courseCode = $course['course_code'];
    $attendance = isset($updatedAttendance[$courseCode]) ? $updatedAttendance[$courseCode] : [
        'attended_classes' => 0,
        'total_classes' => 0,
        'attendance_percentage' => 0,
    ];

    $maxClasses = $attendance['total_classes'];
    $attendedClasses = $attendance['attended_classes'];
    $attendancePercentage = $attendance['attendance_percentage'];

    if ($attendancePercentage == 0 && $maxClasses > 0) {
        $attendancePercentage = ($attendedClasses / $maxClasses) * 100;
    }

    $courseAttendanceData[] = [
        'course_code' => $courseCode,
        'course_name' => $course['course_name'],
        'max_classes' => $maxClasses, // Ensure it's always set
        'attended_classes' => $attendedClasses,
        'attendance_percentage' => round($attendancePercentage, 2)
    ];
}

echo json_encode($courseAttendanceData);