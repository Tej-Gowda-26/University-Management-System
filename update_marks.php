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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate course_code
    if (!isset($_POST['course_code']) || empty(trim($_POST['course_code']))) {
        die("Error: Course code is missing.");
    }
    $course_code = trim($_POST['course_code']);

    // Sanitize and validate other inputs
    $student_roll_number = $_POST['student_roll_number'] ?? '';
    $term_test_1_max = $_POST['term_test_1_max'] ?? 0;
    $term_test_1_obtained = $_POST['term_test_1_obtained'] ?? 0;
    $term_test_2_max = $_POST['term_test_2_max'] ?? 0;
    $term_test_2_obtained = $_POST['term_test_2_obtained'] ?? 0;
    $assignment_max = $_POST['assignment_max'] ?? 0;
    $assignment_obtained = $_POST['assignment_obtained'] ?? 0;
    $quiz_max = $_POST['quiz_max'] ?? 0;
    $quiz_obtained = $_POST['quiz_obtained'] ?? 0;
    $semester_end_exam_max = $_POST['semester_end_exam_max'] ?? 0;
    $semester_end_exam_obtained = $_POST['semester_end_exam_obtained'] ?? 0;
    $total_marks = $_POST['total_marks'] ?? 0;

    $fields = [
        $term_test_1_max, $term_test_1_obtained, 
        $term_test_2_max, $term_test_2_obtained, 
        $assignment_max, $assignment_obtained,
        $quiz_max, $quiz_obtained, 
        $semester_end_exam_max, $semester_end_exam_obtained, $total_marks
    ];
    foreach ($fields as &$value) {
        $value = is_numeric($value) ? (int)$value : 0;
    }

    $check_sql = "SELECT 1 FROM marks WHERE course_code = ? AND student_roll_number = ?";
    if ($check_stmt = $conn->prepare($check_sql)) {
        $check_stmt->bind_param("ss", $course_code, $student_roll_number);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $update_sql = "UPDATE marks SET 
                term_test_1_max = ?, term_test_1_obtained = ?, 
                term_test_2_max = ?, term_test_2_obtained = ?, 
                assignment_max = ?, assignment_obtained = ?, 
                quiz_max = ?, quiz_obtained = ?, 
                semester_end_exam_max = ?, semester_end_exam_obtained = ?, 
                total_marks = ? WHERE course_code = ? AND student_roll_number = ?";

            if ($update_stmt = $conn->prepare($update_sql)) {
                $update_stmt->bind_param(
                    "iiiiiiiiiiiss",
                    $term_test_1_max, $term_test_1_obtained,
                    $term_test_2_max, $term_test_2_obtained,
                    $assignment_max, $assignment_obtained,
                    $quiz_max, $quiz_obtained,
                    $semester_end_exam_max, $semester_end_exam_obtained,
                    $total_marks, $course_code, $student_roll_number
                );

                if ($update_stmt->execute()) {
                    echo "<script>alert('Marks updated successfully.'); window.location.href = 'faculty_dashboard.php';</script>";
                } else {
                    echo "<script>alert('Error updating marks.'); window.location.href = 'faculty_dashboard.php';</script>";
                }

                $update_stmt->close();
            }
        } else {
            $insert_sql = "INSERT INTO marks (
                course_code, student_roll_number, 
                term_test_1_max, term_test_1_obtained,
                term_test_2_max, term_test_2_obtained,
                assignment_max, assignment_obtained,
                quiz_max, quiz_obtained, semester_end_exam_max, 
                semester_end_exam_obtained, total_marks) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            if ($insert_stmt = $conn->prepare($insert_sql)) {
                $insert_stmt->bind_param(
                    "ssiiiiiiiiiii",
                    $course_code, $student_roll_number,
                    $term_test_1_max, $term_test_1_obtained,
                    $term_test_2_max, $term_test_2_obtained,
                    $assignment_max, $assignment_obtained,
                    $quiz_max, $quiz_obtained,
                    $semester_end_exam_max, $semester_end_exam_obtained,
                    $total_marks
                );

                if ($insert_stmt->execute()) {
                    echo "<script>alert('Marks inserted successfully.'); window.location.href = 'faculty_dashboard.php';</script>";
                } else {
                    echo "<script>alert('Error inserting marks.'); window.location.href = 'faculty_dashboard.php';</script>";
                }

                $insert_stmt->close();
            }
        }

        $check_stmt->close();
    }
}

$conn->close();
?>
