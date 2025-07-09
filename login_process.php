<?php
session_start();

$servername = "localhost";
$username = "root"; 
$password = "";  
$db = "UniversityManagementSystem"; 

$conn = new mysqli($servername, $username, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userRole = $_POST['role'];
$userUsername = $_POST['username'];
$userPassword = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ? AND role = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $userUsername, $userRole);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if ($userPassword == $row['password']) {
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['username'] = $userUsername;
        $_SESSION['role'] = $userRole;

        if ($userRole == 'staff') {
            header("Location: admin_dashboard.php?success=logged_in_successfully");
            exit();
        } elseif ($userRole == 'student') {
            header("Location: student_dashboard.php?success=logged_in_successfully");
            exit();
        } elseif ($userRole == 'faculty') {
            header("Location: faculty_dashboard.php?success=logged_in_successfully");
            exit();
        }
    } else {
        header("Location: login.php?error=Invalid username or password.");
        exit();
    }
} else {
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userUsername);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: login.php?error=The role you've selected doesn't match the details provided. Please select the correct role and try again.");
        exit();
    } else {
        $errorMsg = $userRole == 'staff' ? 
            "Please contact your administrator for assistance." : 
            "Your details are not registered. Please contact the administrative staff.";
        header("Location: login.php?error=" . urlencode($errorMsg));
        exit();
    }
}
$stmt->close();
$conn->close();
?>