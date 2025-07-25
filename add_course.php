<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

try {
    // Database connection using PDO
    $host = "localhost";
    $dbname = "UniversityManagementSystem";
    $username = "root"; // Replace with your actual username
    $password = ""; // Replace with your actual password

    // Create a PDO instance to connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted via POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Collect form data
        $courseCode = $_POST['courseCode'];
        $courseName = $_POST['courseName'];
        $numberOfSessions = $_POST['numberOfSessions'];
        $courseCredits = $_POST['courseCredits'];
        $facultyId = $_POST['facultyId'];
        $programme = $_POST['programme'];
        $semester = $_POST['semester'];
        $batch = $_POST['batch'];

        // Validate that the facultyId exists in the faculty table
        $stmt = $pdo->prepare("SELECT id FROM faculty WHERE id = ?");
        $stmt->execute([$facultyId]);
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Faculty ID not found.']);
            exit();
        }

        // Begin a transaction to ensure atomicity
        $pdo->beginTransaction();

        // Insert into course table
        $stmt = $pdo->prepare("INSERT INTO course (course_code, course_name, number_of_sessions, course_credits, faculty_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$courseCode, $courseName, $numberOfSessions, $courseCredits, $facultyId]);

        // Insert into course_mapping table
        $stmt = $pdo->prepare("INSERT INTO course_mapping (course_code, programme, semester, batch) VALUES (?, ?, ?, ?)");
        $stmt->execute([$courseCode, $programme, $semester, $batch]);

        // Commit the transaction
        $pdo->commit();

        // Return success response
        echo json_encode(['success' => true, 'message' => 'Course and course mapping added successfully!']);
    }
} catch (PDOException $e) {
    // In case of an error, roll back the transaction and return the error message
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
