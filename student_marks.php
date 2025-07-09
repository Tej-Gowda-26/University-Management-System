<?php
session_start();

if ($_SESSION['role'] !== 'student') {
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
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$sql = "SELECT roll_number FROM student WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentUsername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Student not found."]);
    exit();
}

$row = $result->fetch_assoc();
$studentRollNumber = $row['roll_number'];
$stmt->close();

$coursesSql = "SELECT course_code, course_name FROM course ORDER BY course_code";
$stmt = $conn->prepare($coursesSql);
$stmt->execute();
$result = $stmt->get_result();

$allCourses = [];
while ($course = $result->fetch_assoc()) {
    $allCourses[$course['course_code']] = $course['course_name'];
}

$stmt->close();

$sql = "SELECT c.course_name, m.course_code, 
               m.term_test_1_max, m.term_test_1_obtained, 
               m.term_test_2_max, m.term_test_2_obtained, 
               m.assignment_max, m.assignment_obtained,
               m.quiz_max, m.quiz_obtained, 
               m.semester_end_exam_max, m.semester_end_exam_obtained,
               m.total_marks
        FROM marks m
        JOIN course c ON m.course_code = c.course_code
        WHERE m.student_roll_number = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentRollNumber);
$stmt->execute();
$result = $stmt->get_result();

$marksData = [];
while ($row = $result->fetch_assoc()) {
    $row['total'] = $row['term_test_1_obtained'] + $row['term_test_2_obtained'] + 
                    $row['assignment_obtained'] + $row['quiz_obtained'] + 
                    $row['semester_end_exam_obtained'];
    $marksData[$row['course_code']] = $row;
}

$stmt->close();

foreach ($allCourses as $courseCode => $courseName) {
    if (!isset($marksData[$courseCode])) {
        $marksData[$courseCode] = [
            'course_code' => $courseCode,
            'course_name' => $courseName,
            'term_test_1_max' => 0,
            'term_test_1_obtained' => 0,
            'term_test_2_max' => 0,
            'term_test_2_obtained' => 0,
            'assignment_max' => 0,
            'assignment_obtained' => 0,
            'quiz_max' => 0,
            'quiz_obtained' => 0,
            'semester_end_exam_max' => 0,
            'semester_end_exam_obtained' => 0,
            'total_marks' => 0,
            'total' => 0, 
        ];
    }
}

$marksData = array_values($marksData);

header("Content-Type: application/json");
echo json_encode($marksData);
?>