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

$facultyUsername = $_SESSION['username'];

$sql = "SELECT * FROM faculty WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $facultyUsername);
$stmt->execute();
$result = $stmt->get_result();

$facultyDetails = "";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $facultyDetails = array(
        "Full Name" => $row['full_name'],
        "Faculty ID" => $row['faculty_id'],
        "Department" => $row['department'],
        "Role" => $row['role'],
        "Courses Handled" => $row['courses_handled'],
        "Highest Qualification" => $row['highest_qualification'],
        "Years of Experience" => $row['years_of_experience'],
        "Gender" => $row['gender'],
        "Phone Number" => $row['phone_number'],
        "Email" => $row['email'],
        "Date of Joining" => $row['date_of_joining'],
        "Status" => $row['status'],
        "Last Login" => $row['last_login']
    );

    if (isset($row['faculty_id'])) {
        $coursesSql = "SELECT * FROM course WHERE faculty_id = ?";
        $coursesStmt = $conn->prepare($coursesSql);
        $coursesStmt->bind_param("i", $row['faculty_id']);
        $coursesStmt->execute();
        $coursesResult = $coursesStmt->get_result();
        $courses = [];
        while ($course = $coursesResult->fetch_assoc()) {
            $courses[] = $course;
        }
    } else {
        $courses = [];
    }
} else {
    $facultyDetails = null;
    $courses = [];
}

$stmt->close();
$conn->close();
?>

<?php
if (isset($_GET['attendance_inserted']) && $_GET['attendance_inserted'] == 'true') {
    echo "<script>alert('Attendance inserted successfully.');</script>";
}

if (isset($_GET['attendance_insert_error']) && $_GET['attendance_insert_error'] == 'true') {
    echo "<script>alert('Error inserting attendance.');</script>";
}

if (isset($_GET['attendance_updated']) && $_GET['attendance_updated'] == 'true') {
    echo "<script>alert('Attendance updated successfully.');</script>";
}

if (isset($_GET['attendance_update_error']) && $_GET['attendance_update_error'] == 'true') {
    echo "<script>alert('Error updating attendance.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="student_style.css">
    <style>
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .attendance-table th,
        .attendance-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        .attendance-table th {
            width: 200px;
            font-weight: bold;
            background-color: #b1c4ff;
            color: white;
        }

        .attendance-table td input[type="number"],
        .attendance-table td input[type="text"] {
            width: 200px;
            padding: 8px;
            font-size: 14px;
            margin: 0;
            border: 1px solid #ccc;
            height: 30px;
        }

        .attendance-table td label {
            margin-right: 10px;
        }

        .submit-button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .submit-button-container button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            width: auto;
        }

        .submit-button-container button:hover {
            background-color: #45a049;
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .marks-table th,
        .marks-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        .marks-table th {
            width: 200px;
            font-weight: bold;
        }

        .marks-table td input[type="number"],
        .marks-table td input[type="text"] {
            width: 200px;
            padding: 8px;
            font-size: 14px;
            margin: 0;
        }

        .marks-table td label {
            margin-right: 10px;
        }

        .marks-row {
            margin-bottom: 20px;
        }

        .marks-row label {
            font-weight: bold;
            font-size: 18px;
            margin-right: 15px;
        }

        .marks-row input[type="text"] {
            font-size: 18px;
            padding: 12px;
            width: 300px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
        }

        .total-row {
            margin-bottom: 20px;
        }

        .total-row label {
            font-weight: bold;
            font-size: 18px;
            margin-right: 15px;
        }

        .total-row input[type="number"] {
            font-size: 18px;
            padding: 12px;
            width: 300px;
            border: 1px solid #ccc;
            margin-top: 15px;
        }

        .submit-button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .submit-button-container button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            width: auto;
        }

        .submit-button-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="welcome-message">Welcome, <?php echo $facultyDetails['Full Name']; ?>!</div>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <div class="menu-row">
        <div class="menu-item" id="facultyDetails">Faculty Details</div>
        <div class="menu-item" id="updateAttendance">Update Attendance</div>
        <div class="menu-item" id="updateMarks">Update Marks</div>
        <div class="menu-item" id="viewStudents">View Students</div>
    </div>

    <div class="blue-box" id="blueBox">
        <!-- Content will be dynamically added here -->
    </div>

    <script>
        const facultyDetails = <?php echo json_encode($facultyDetails); ?>;
        const courses = <?php echo json_encode($courses); ?>;
        const facultyDetailsMenuItem = document.getElementById('facultyDetails');
        const blueBox = document.getElementById('blueBox');
        const updateAttendanceMenuItem = document.getElementById('updateAttendance');
        const updateMarksMenuItem = document.getElementById('updateMarks');
        const viewStudentsMenuItem = document.getElementById('viewStudents');

        function generateFacultyDetailsHtml(details) {
            if (!details) {
                return "<p>Faculty details not found.</p>";
            }

            let html = "<h3>Faculty Details</h3>";
            html += "<table>";
            html += "<tbody>";

            for (const key in details) {
                if (details.hasOwnProperty(key)) {
                    html += `<tr><td class='detail-label'>${key}</td><td class='detail-value'>${details[key]}</td></tr>`;
                }
            }

            html += "</tbody>";
            html += "</table>";
            return html;
        }

        function showAttendanceForm(course_code, course_name) {
            let html = `<h3 style="margin-top: 20px;">Update Attendance for ${course_name} (${course_code})</h3>`;

            html += `
                <form action='update_attendance.php' method='POST'>
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>Student Roll Number</th>
                                <th>Total Classes</th>
                                <th>Classes Attended</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="student_roll_number" required></td>
                                <td><input type="number" name="total_classes" required></td>
                                <td><input type="number" name="attended_classes" required></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="submit-button-container">
                        <button type="submit">Submit</button>
                    </div>
                    <input type='hidden' name='course_code' value='${course_code}'>
                </form>
            `;

            blueBox.innerHTML += html;
        }

        updateAttendanceMenuItem.addEventListener('click', function () {
            let html = "<h3>Update Attendance</h3>";

            if (courses.length > 0) {
                html += "<table>";
                html += "<thead><tr><th>Course Code</th><th>Course Name</th><th>Action</th></tr></thead><tbody>";

                courses.forEach(course => {
                    html += `<tr><td>${course.course_code}</td><td>${course.course_name}</td><td><button onclick="showAttendanceForm('${course.course_code}', '${course.course_name}')">Update Attendance</button></td></tr>`;
                });

                html += "</tbody></table>";
            } else {
                html += "<p>No courses found for you.</p>";
            }

            blueBox.innerHTML = html;
        });

        function showMarksForm(course_code, course_name) {
            let formHtml = `<h3 style="margin-top: 20px;">Update Marks for ${course_name} (${course_code})</h3>`;

            formHtml += `
                <form action="update_marks.php" method="POST">
                    <div class="marks-row">
                        <label for="student_roll_number">Student Roll Number</label>
                        <input type="text" name="student_roll_number" required>
                    </div>
                    <table class="marks-table">
                        <thead>
                            <tr>
                                <th>Assessment</th>
                                <th>Max Marks</th>
                                <th>Obtained Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Term Test 1</td>
                                <td><input type="number" name="term_test_1_max" required></td>
                                <td><input type="number" name="term_test_1_obtained" required></td>
                            </tr>
                            <tr>
                                <td>Term Test 2</td>
                                <td><input type="number" name="term_test_2_max" required></td>
                                <td><input type="number" name="term_test_2_obtained" required></td>
                            </tr>
                            <tr>
                                <td>Assignment</td>
                                <td><input type="number" name="assignment_max" required></td>
                                <td><input type="number" name="assignment_obtained" required></td>
                            </tr>
                            <tr>
                                <td>Quiz</td>
                                <td><input type="number" name="quiz_max" required></td>
                                <td><input type="number" name="quiz_obtained" required></td>
                            </tr>
                            <tr>
                                <td>Semester End Exam</td>
                                <td><input type="number" name="semester_end_exam_max" required></td>
                                <td><input type="number" name="semester_end_exam_obtained" required></td>
                            </tr>
                            
                        </tbody>
                    </table>

                    <div class="total-row">
                        <label for="total_marks">Total Marks</label>
                        <input type="number" name="total_marks" required>
                    </div>

                    <div class="submit-button-container">
                        <button type="submit">Submit</button>
                    </div>
                    <input type="hidden" name="course_code" value="${course_code}">
                </form>
            `;

            blueBox.innerHTML = formHtml; // Render the form
        }

        updateMarksMenuItem.addEventListener('click', function () {
            let html = "<h3>Update Marks</h3>";

            if (courses.length > 0) {
                html += "<table>";
                html += "<thead><tr><th>Course Code</th><th>Course Name</th><th>Action</th></tr></thead><tbody>";

                courses.forEach(course => {
                    html += `<tr id="course_${course.course_code}">
                                <td>${course.course_code}</td>
                                <td>${course.course_name}</td>
                                <td><button onclick="showMarksForm('${course.course_code}', '${course.course_name}')">Update Marks</button></td>
                             </tr>`;
                });

                html += "</tbody></table>";
            } else {
                html += "<p>No courses found for you.</p>";
            }

            blueBox.innerHTML = html;
        });

        facultyDetailsMenuItem.addEventListener('click', function () {
            blueBox.innerHTML = generateFacultyDetailsHtml(facultyDetails);
        });
    </script>
</body>

</html>