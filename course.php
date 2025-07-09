<?php
session_start();

if ($_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$studentSemester = isset($_SESSION['semester']) ? $_SESSION['semester'] : '';
$studentProgramme = isset($_SESSION['programme']) ? $_SESSION['programme'] : '';
$studentBatch = isset($_SESSION['batch']) ? $_SESSION['batch'] : '';

if (empty($studentSemester) || empty($studentProgramme) || empty($studentBatch)) {
    die("Required session data is missing or invalid.");
}

$servername = "localhost";
$username = "root";
$password = "";
$db = "UniversityManagementSystem";

$conn = new mysqli($servername, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT course.course_code, course.course_name, course.number_of_sessions, 
               course.course_credits, faculty.full_name AS faculty_name
        FROM course
        JOIN course_mapping ON course.course_code = course_mapping.course_code
        JOIN faculty ON course.faculty_id = faculty.faculty_id
        WHERE course_mapping.semester = ? AND course_mapping.programme = ? AND course_mapping.batch = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $studentSemester, $studentProgramme, $studentBatch);
$stmt->execute();
$result = $stmt->get_result();

$courseDetails = "";
if ($result->num_rows > 0) {
    $courseDetails = "<h3>Course Details</h3>
                      <table>
                          <thead>
                              <tr>
                                  <th>Course Name</th>
                                  <th>Course Code</th>
                                  <th>Number of Sessions</th>
                                  <th>Course Credits</th>
                                  <th>Faculty Name</th>
                              </tr>
                          </thead>
                          <tbody>";
    while ($row = $result->fetch_assoc()) {
        $courseDetails .= "<tr>
                               <td>{$row['course_name']}</td>
                               <td>{$row['course_code']}</td>
                               <td>{$row['number_of_sessions']}</td>
                               <td>{$row['course_credits']}</td>
                               <td>{$row['faculty_name']}</td>
                           </tr>";
    }
    $courseDetails .= "</tbody></table>";
} else {
    $courseDetails = "<p>No courses found for your semester, programme, and batch.</p>";
}

$stmt->close();
$conn->close();

echo $courseDetails;
?>
