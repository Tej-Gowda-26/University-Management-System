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
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM student WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentUsername);
$stmt->execute();
$result = $stmt->get_result();

$studentDetails = "";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $studentDetails = array(
        "Full Name" => $row['full_name'],
        "Roll Number" => $row['roll_number'],
        "Section" => $row['section'],
        "Semester" => $row['semester'],
        "Year" => $row['year'],
        "Department" => $row['department'],
        "Programme" => $row['programme'],
        "Batch" => $row['batch'],
        "Admission Type" => $row['admission_type'],
        "Date of Birth" => $row['date_of_birth'],
        "Gender" => $row['gender'],
        "Blood Group" => $row['blood_group'],
        "Phone Number" => $row['phone_number'],
        "Email" => $row['email'],
        "Address" => $row['address'],
        "Last Login" => $row['last_login']
    );

    $_SESSION['semester'] = $row['semester'];
    $_SESSION['programme'] = $row['programme'];
    $_SESSION['batch'] = $row['batch'];
} else {
    $studentDetails = null;
}

$stmt->close();
$conn->close();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="student_style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="welcome-message">Welcome, <?php echo $studentDetails['Full Name']; ?>!</div>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>

        <div class="menu-row">
            <div class="menu-item" id="studentDetails">Profile</div>
            <div class="menu-item" id="courseDetails">Course</div>
            <div class="menu-item" id="attendanceDetails">Attendance</div>
            <div class="menu-item">Marks</div>
            <div class="menu-item" id="manageTimetable">Timetable</div>
            <div class="menu-item">Exam</div>
            <div class="menu-item">Fee Detail</div>
        </div>

        <div class="blue-box" id="blueBox">
        </div>
    </div>

    <script>
        const studentDetails = <?php echo json_encode($studentDetails); ?>;
        const studentDetailsMenuItem = document.getElementById('studentDetails');
        const courseDetailsMenuItem = document.getElementById('courseDetails');
        const attendanceDetailsMenuItem = document.getElementById('attendanceDetails');
        const blueBox = document.getElementById('blueBox');

        function generateStudentDetailsHtml(details) {
            if (!details) {
                return "<p>Student details not found.</p>";
            }

            let html = "<h3>Student Details</h3>";
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

        function generateAttendanceHtml(courseData) {
            if (!courseData || courseData.length === 0) {
                return "<p>No attendance records found.</p>";
            }

            let html = "<h3>Course Attendance</h3>";
            html += "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            html += "<thead><tr><th>Course Name</th><th>Course Code</th><th>Max Classes</th><th>Attended Classes</th><th>Attendance Percentage</th></tr></thead>";
            html += "<tbody>";

            courseData.forEach(function(course) {
                html += `<tr>
                            <td>${course.course_name}</td>
                            <td>${course.course_code}</td>
                            <td>${course.max_classes}</td>
                            <td>${course.attended_classes}</td>
                            <td>${course.attendance_percentage}%</td>
                          </tr>`;
            });

            html += "</tbody>";
            html += "</table>";
            return html;
        }

        studentDetailsMenuItem.addEventListener('click', function() {
            blueBox.innerHTML = generateStudentDetailsHtml(studentDetails);
        });

        courseDetailsMenuItem.addEventListener('click', function() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'course.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    blueBox.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });

        attendanceDetailsMenuItem.addEventListener('click', function() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'student_attendance.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const courseData = JSON.parse(xhr.responseText);
                        blueBox.innerHTML = generateAttendanceHtml(courseData);
                    } catch (e) {
                        blueBox.innerHTML = "<p>Error loading attendance data.</p>";
                    }
                }
            };
            xhr.send();
        });

    studentDetailsMenuItem.addEventListener('click', function() {
    blueBox.innerHTML = generateStudentDetailsHtml(studentDetails);
});

courseDetailsMenuItem.addEventListener('click', function() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'course.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            blueBox.innerHTML = xhr.responseText;
        }
    };
    xhr.send();
});

attendanceDetailsMenuItem.addEventListener('click', function() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'student_attendance.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const courseData = JSON.parse(xhr.responseText);
                blueBox.innerHTML = generateAttendanceHtml(courseData);
            } catch (e) {
                blueBox.innerHTML = "<p>Error loading attendance data.</p>";
            }
        }
    };
    xhr.send();
});

const marksMenuItem = document.querySelector('.menu-item:nth-child(4)');  

marksMenuItem.addEventListener('click', function() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'student_marks.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const marksData = JSON.parse(xhr.responseText);
                blueBox.innerHTML = generateMarksHtml(marksData);
            } catch (e) {
                blueBox.innerHTML = "<p>Error loading marks data.</p>";
            }
        }
    };
    xhr.send();
});

function generateMarksHtml(marksData) {
    if (!marksData || marksData.length === 0) {
        return "<p>No marks found.</p>";
    }

    let html = "<h3>Marks Details</h3>";
    html += "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    html += "<thead><tr><th>Course Name</th><th>Course Code</th><th>Term Test 1</th><th>Term Test 2</th><th>Assignment</th><th>Quiz</th><th>Semester End Exam</th><th>Total Marks</th></tr></thead>";
    html += "<tbody>";

    marksData.forEach(function(marks) {
        html += `<tr>
                    <td>${marks.course_name}</td>
                    <td>${marks.course_code}</td>
                    <td>${marks.term_test_1_obtained} / ${marks.term_test_1_max}</td>
                    <td>${marks.term_test_2_obtained} / ${marks.term_test_2_max}</td>
                    <td>${marks.assignment_obtained} / ${marks.assignment_max}</td>
                    <td>${marks.quiz_obtained} / ${marks.quiz_max}</td>
                    <td>${marks.semester_end_exam_obtained} / ${marks.semester_end_exam_max}</td>
                    <td>${marks.total_marks}</td>
                  </tr>`;
    });

    html += "</tbody>";
    html += "</table>";
    return html;
}
    </script>
</body>
</html>
