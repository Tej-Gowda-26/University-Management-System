<?php
session_start();

if ($_SESSION['role'] !== 'staff') {
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

$staffUsername = $_SESSION['username'];

$sql = "SELECT * FROM staff WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staffUsername);
$stmt->execute();
$result = $stmt->get_result();

$staffDetails = null;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $staffDetails = array(
        "Full Name" => $row['full_name'],
        "Staff ID" => $row['staff_id'],
        "Department" => $row['department'],
        "Role" => $row['role'],
        "Highest Qualification" => $row['highest_qualification'],
        "Years of Experience" => $row['years_of_experience'],
        "Gender" => $row['gender'],
        "Phone Number" => $row['phone_number'],
        "Email" => $row['email'],
        "Date of Joining" => $row['date_of_joining'],
        "Status" => $row['status'],
        "Last Login" => $row['last_login']
    );
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        /* Add Student Form Styles */
        #addStudentForm .form-row {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        #addStudentForm label {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            width: 150px;
        }

        #addStudentForm {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #addStudentForm input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid black;
            border-radius: 5px;
            width: calc(100% - 170px);
            background-color: #f9f9f9;
        }

        #addStudentForm input:focus {
            border: 1px solid black;
            outline: none;
        }

        #addStudentForm button {
            padding: 12px;
            font-size: 18px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #addStudentForm button:hover {
            background-color: var(--hover-color);
        }

        #responseMessage {
            font-size: 16px;
            color: #333;
            margin-top: 20px;
            text-align: center;
        }

    
h4 {
    text-align: center; 
    font-size: 30px;
    font-weight: bold; 
    color: #333; 
    margin-top: 0px; 
    margin-bottom: 20px; 

}

#addFacultyForm .form-row {
    display: flex;
    align-items: center;
    justify-content: space-between; 
    gap: 15px;
}

#addFacultyForm label {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    width: 200px;
}

#addFacultyForm input, #addFacultyForm select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid black;
    border-radius: 5px;
    width: calc(100% - 220px); 
    background-color: #f9f9f9;
}

#addFacultyForm {
    display: flex;
    flex-direction: column;
    gap: 15px;
    max-width: 800px;
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

#addFacultyForm input:focus, #addFacultyForm select:focus {
    border: 1px solid black;
    outline: none;
}

#addFacultyForm button {
    padding: 12px;
    font-size: 18px;
    background-color: var(--primary-color);
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
}

#addFacultyForm button:hover {
    background-color: var(--hover-color);
}

#responseMessage {
    font-size: 16px;
    color: #333;
    margin-top: 5px;
    text-align: center;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.dropdown-content.show {
    display: block;
}

#addUserForm {
    display: flex;
    flex-direction: column;
    gap: 15px;
    max-width: 800px;
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

#addUserForm .form-row {
    display: flex;
    align-items: center;
    gap: 15px;
}

#addUserForm label {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    width: 200px;
}

#addUserForm input, #addUserForm select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid black;
    border-radius: 5px;
    width: calc(100% - 243px);
    background-color: #f9f9f9;
}

#addUserForm select {
    width: calc(100% - 220px);  
}

#addUserForm input:focus, #addUserForm select:focus {
    border: 1px solid black;
    outline: none;
}

#addUserForm button {
    padding: 12px;
    font-size: 18px;
    background-color: var(--primary-color);
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
}

#addUserForm button:hover {
    background-color: var(--hover-color);
}

#responseMessage {
    font-size: 16px;
    color: #333;
    margin-top: 5px;
    text-align: center;
}

#addCourseForm {
    display: flex;
    flex-direction: column;
    gap: 15px;
    max-width: 800px;
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

#addCourseForm .form-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

#addCourseForm label {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    width: 200px;
}

#addCourseForm input,
#addCourseForm select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid black;
    border-radius: 5px;
    width: calc(100% - 220px); 
    background-color: #f9f9f9;
}

#addCourseForm input:focus,
#addCourseForm select:focus {
    border: 1px solid black;
    outline: none;
}

#addCourseForm button {
    padding: 12px;
    font-size: 18px;
    background-color: var(--primary-color);
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
}

#addCourseForm button:hover {
    background-color: var(--hover-color);
}

#responseMessage {
    font-size: 16px;
    color: #333;
    margin-top: 5px;
    text-align: center;
}

#addCourseForm h4 {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
}

    </style>
</head>
<body>
    <div class="header">
        <div class="welcome-message">Welcome, <?php echo $staffDetails['Full Name']; ?>!</div>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <div class="menu-row">
        <div class="menu-item" id="staffDetails">Staff Details</div>
        <div class="menu-item" id="manageStudent">
            Manage Student
            <div class="dropdown-content">
                <div class="dropdown-item">View Student</div>
                <div class="dropdown-item" id="addStudentMenuItem">Add Student</div>
                <div class="dropdown-item">Update Student</div>
                <div class="dropdown-item">Delete Student</div>
            </div>
        </div>

        <div class="menu-item" id="manageFaculty">
            Manage Faculty
            <div class="dropdown-content">
                <div class="dropdown-item">View Faculty</div>
                <div class="dropdown-item" id="addFacultyMenuItem">Add Faculty</div>
                <div class="dropdown-item">Update Faculty</div>
                <div class="dropdown-item">Delete Faculty</div>
            </div>
        </div>
        
        <div class="menu-item" id="addCourseMenuItem">Manage Course</div>
        <div class="menu-item" id="manageFees">Manage Fees</div>
        <div class="menu-item" id="addUsersMenuItem">Add User</div>
    </div>
    <div class="blue-box" id="blueBox">
        <!-- Staff details will be displayed here inside the table -->
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const staffDetails = <?php echo json_encode($staffDetails); ?>;
    const staffDetailsMenuItem = document.getElementById('staffDetails');
    const blueBox = document.getElementById('blueBox');
    const manageStudentMenuItem = document.getElementById('manageStudent');
    const manageFacultyMenuItem = document.getElementById('manageFaculty');
    const addStudentMenuItem = document.getElementById('addStudentMenuItem');
    const addFacultyMenuItem = document.getElementById('addFacultyMenuItem');
    const addUsersMenuItem = document.getElementById('addUsersMenuItem');
    const addCourseMenuItem = document.getElementById('addCourseMenuItem'); 
 

    function generateStaffDetailsHtml(details) {
        if (!details || Object.keys(details).length === 0) {
            return "<p>Staff details not found.</p>";
        }

        let html = "<h3>Staff Details</h3>";
        html += "<table style='width: 100%; border-collapse: collapse;'>";

        for (const key in details) {
            if (details.hasOwnProperty(key)) {
                html += `<tr><td style='padding: 8px; border: 1px solid #ddd; font-weight: bold;'>${key}</td><td style='padding: 8px; border: 1px solid #ddd;'>${details[key]}</td></tr>`;
            }
        }

        html += "</table>";
        return html;
    }

    staffDetailsMenuItem.addEventListener('click', function() {
        blueBox.innerHTML = generateStaffDetailsHtml(staffDetails);
    });

    function toggleDropdown(dropdownContent) {
        if (dropdownContent) {
            dropdownContent.classList.toggle('show');
        }
    }

    manageStudentMenuItem.addEventListener('click', function() {
        const dropdownContent = manageStudentMenuItem.querySelector('.dropdown-content');
        toggleDropdown(dropdownContent);
    });

    manageFacultyMenuItem.addEventListener('click', function() {
        const dropdownContent = manageFacultyMenuItem.querySelector('.dropdown-content');
        toggleDropdown(dropdownContent);
    });

    function hideDropdownOnSelect(dropdownItem) {
        dropdownItem.addEventListener('click', function() {
            const dropdownContent = this.closest('.dropdown-content');
            if (dropdownContent) {
                dropdownContent.classList.remove('show');
            }
        });
    }

    const studentDropdownItems = manageStudentMenuItem.querySelectorAll('.dropdown-item');
    studentDropdownItems.forEach(item => hideDropdownOnSelect(item));

    const facultyDropdownItems = manageFacultyMenuItem.querySelectorAll('.dropdown-item');
    facultyDropdownItems.forEach(item => hideDropdownOnSelect(item));

    if (addStudentMenuItem) {
        addStudentMenuItem.addEventListener('click', function(event) {
            event.stopPropagation();
            const addStudentFormHtml = `<h3>Add Student</h3>
                    <form id="addStudentForm">
                        <div class="form-row"><label for="username">User Name</label><input type="text" id="username" name="username" required></div>
                        <div class="form-row"><label for="full_name">Full Name</label><input type="text" id="full_name" name="full_name" required></div>
                        <div class="form-row"><label for="roll_number">Roll Number</label><input type="text" id="roll_number" name="roll_number" required></div>
                        <div class="form-row"><label for="section">Section</label><input type="text" id="section" name="section" required></div>
                        <div class="form-row"><label for="semester">Semester</label><input type="text" id="semester" name="semester" required></div>
                        <div class="form-row"><label for="year">Year</label><input type="text" id="year" name="year" required></div>
                        <div class="form-row"><label for="department">Department</label><input type="text" id="department" name="department" required></div>
                        <div class="form-row"><label for="programme">Programme</label><input type="text" id="programme" name="programme" required></div>
                        <div class="form-row"><label for="batch">Batch</label><input type="text" id="batch" name="batch" required></div>
                        <div class="form-row"><label for="admission_type">Admission Type</label><input type="text" id="admission_type" name="admission_type" required></div>
                        <div class="form-row"><label for="date_of_birth">Date of Birth</label><input type="date" id="date_of_birth" name="date_of_birth" required></div>
                        <div class="form-row"><label for="gender">Gender</label> <input type="text" id="gender" name="gender" required></div>
                        <div class="form-row"><label for="blood_group">Blood Group</label><input type="text" id="blood_group" name="blood_group" required></div>
                        <div class="form-row"><label for="phone_number">Phone Number</label><input type="text" id="phone_number" name="phone_number" required></div>
                        <div class="form-row"><label for="email">Email</label><input type="email" id="email" name="email" required></div>
                        <div class="form-row"><label for="address">Address</label><input type="text" id="address" name="address" required></div>
                        <button type="submit">Submit</button>
                    </form>
                    <div id="responseMessage"></div>
                `
            blueBox.innerHTML = addStudentFormHtml;
            attachFormSubmitHandler();
        });
    }

    function attachFormSubmitHandler() {
        const form = document.getElementById('addStudentForm');
        form.addEventListener('submit', function (event) {
            event.preventDefault(); 

            const formData = new FormData(form);

            fetch('add_student.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const responseMessage = document.getElementById('responseMessage');
                if (data.success) {
                    responseMessage.textContent = 'Student added successfully!';
                    responseMessage.style.color = 'green';
                } else {
                    responseMessage.textContent = data.message || 'Error adding student.';
                    responseMessage.style.color = 'red';
                }
            })
            .catch(error => {
                const responseMessage = document.getElementById('responseMessage');
                responseMessage.textContent = 'An error occurred.';
                responseMessage.style.color = 'red';
            });
        });
    }

    if (addFacultyMenuItem) {
        addFacultyMenuItem.addEventListener('click', function(event) {
            event.stopPropagation();
            const addFacultyFormHtml = `<h4> Add Faculty </h4>
            <form id="addFacultyForm">
                <div class="form-row">
                    <label for="username">User Name</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-row">
                    <label for="facultyFullName">Full Name</label>
                    <input type="text" id="facultyFullName" name="facultyFullName" required>
                </div>
                <div class="form-row">
                    <label for="facultyId">Faculty ID</label>
                    <input type="text" id="facultyId" name="facultyId" required>
                </div>
                <div class="form-row">
                    <label for="department">Department</label>
                    <input type="text" id="department" name="department" required>
                </div>
                <div class="form-row">
                    <label for="role">Role</label>
                    <input type="text" id="role" name="role" required>
                </div>
                <div class="form-row">
                    <label for="courseshandled">Courses Handled</label>
                    <input type="text" id="courseshandled" name="courseshandled" required>
                </div>
                <div class="form-row">
                    <label for="highestQualification">Highest Qualification</label>
                    <input type="text" id="highestQualification" name="highestQualification" required>
                </div>
                <div class="form-row">
                    <label for="yearsOfExperience">Years of Experience</label>
                    <input type="number" id="yearsOfExperience" name="yearsOfExperience" required>
                </div>
                <div class="form-row"><label for="gender">Gender</label> <input type="text" id="gender" name="gender" required></div>
                <div class="form-row">
                    <label for="phoneNumber">Phone Number</label>
                    <input type="text" id="phoneNumber" name="phoneNumber" required>
                </div>
                <div class="form-row">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-row">
                    <label for="dateOfJoining">Date of Joining</label>
                    <input type="date" id="dateOfJoining" name="dateOfJoining" required>
                </div>
                <button type="submit">Submit</button>
            </form>
            <div id="responseMessage"></div>`;

            blueBox.innerHTML = addFacultyFormHtml;
            attachFacultyFormSubmitHandler();
        });
    }

    function attachFacultyFormSubmitHandler() {
    const form = document.getElementById('addFacultyForm');
    form.addEventListener('submit', function (event) {
        event.preventDefault(); 

        const formData = new FormData(form);

        fetch('add_faculty.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const responseMessage = document.getElementById('responseMessage');
            if (data.success) {
                responseMessage.textContent = 'Faculty added successfully!';
                responseMessage.style.color = 'green';
            } else {
                responseMessage.textContent = data.message || 'Error adding faculty.';
                responseMessage.style.color = 'red';
            }
        })
        .catch(error => {
            const responseMessage = document.getElementById('responseMessage');
            responseMessage.textContent = 'An error occurred.';
            responseMessage.style.color = 'red';
        });
    });
}

addUsersMenuItem.addEventListener('click', function(event) {
    event.stopPropagation();
    const addUserFormHtml = `
        <h4> Add User </h4>
        <form id="addUserForm">
            <div class="form-row">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-row">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-row">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <button type="submit">Submit</button>
        </form>
        <div id="responseMessage"></div>
    `;
    blueBox.innerHTML = addUserFormHtml;
    attachUserFormSubmitHandler(); 
});

function attachUserFormSubmitHandler() {
    const form = document.getElementById('addUserForm');
    form.addEventListener('submit', function(event) {
        event.preventDefault(); 

        const formData = new FormData(form);

        fetch('add_users.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const responseMessage = document.getElementById('responseMessage');
            if (data.success) {
                responseMessage.textContent = 'User added successfully!';
                responseMessage.style.color = 'green';
            } else {
                responseMessage.textContent = data.message || 'Error adding user.';
                responseMessage.style.color = 'red';
            }
        })
        .catch(error => {
            const responseMessage = document.getElementById('responseMessage');
            responseMessage.textContent = 'An error occurred: ' + error.message;
            responseMessage.style.color = 'red';
            console.error('Error details:', error); 
        });
    });
}

if (addCourseMenuItem) {
    addCourseMenuItem.addEventListener('click', function(event) {
        event.stopPropagation();
        const addCourseFormHtml = `
            <h4> Add Course </h4>
            <form id="addCourseForm">
                <div class="form-row">
                    <label for="programme">Programme</label>
                    <input id="programme" name="programme" required>
                </div>
                <div class="form-row">
                    <label for="semester">Semester</label>
                    <input id="semester" name="semester" required>
                </div>
                <div class="form-row">
                    <label for="batch">Batch</label>
                    <input id="batch" name="batch" required>
                </div>
                <div class="form-row">
                    <label for="courseCode">Course Code</label>
                    <input type="text" id="courseCode" name="courseCode" required>
                </div>
                <div class="form-row">
                    <label for="courseName">Course Name</label>
                    <input type="text" id="courseName" name="courseName" required>
                </div>
                <div class="form-row">
                    <label for="numberOfSessions">Number of Sessions</label>
                    <input type="number" id="numberOfSessions" name="numberOfSessions" required>
                </div>
                <div class="form-row">
                    <label for="courseCredits">Course Credits</label>
                    <input type="number" id="courseCredits" name="courseCredits" required>
                </div>
                <div class="form-row">
                    <label for="facultyId">Faculty ID</label>
                    <input type="text" id="facultyId" name="facultyId" required>
                </div>
                <button type="submit">Submit</button>
            </form>
            <div id="responseMessage"></div>`;

        blueBox.innerHTML = addCourseFormHtml;

        attachCourseFormSubmitHandler();
    });
}

function attachCourseFormSubmitHandler() {
    const form = document.getElementById('addCourseForm');
    form.addEventListener('submit', function(event) {
        event.preventDefault(); 

        const formData = new FormData(form);

        fetch('add_course.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const responseMessage = document.getElementById('responseMessage');
            if (data.success) {
                responseMessage.textContent = 'Course added successfully!';
                responseMessage.style.color = 'green';
            } else {
                responseMessage.textContent = data.message || 'Error adding course.';
                responseMessage.style.color = 'red';
            }
        })
        .catch(error => {
            const responseMessage = document.getElementById('responseMessage');
            responseMessage.textContent = 'An error occurred.';
            responseMessage.style.color = 'red';
        });
    });
}

});
    </script>
</body>
</html>