<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: auth.php');
    exit();
}
include_once 'config.php';

// Establish database connection
$connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$connection) {
    echo "Database connection error: " . mysqli_connect_error();
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['courseId'])) {
    $courseId = $_POST['courseId'];
    $courseDescription = $_POST['courseDescription'];
    $courseName = $_POST['courseName'];
    $courseCredits = $_POST['courseCredits'];
    $courseSemester = $_POST['courseSemester'];

    // Handle file upload (course outline)
    $courseOutlineDestination = '';  // Set this to the correct path for the uploaded course outline
    if (isset($_FILES['courseOutline']) && $_FILES['courseOutline']['error'] === UPLOAD_ERR_OK) {
        $courseOutlineFile = $_FILES['courseOutline']['tmp_name'];
        $courseOutlineFileName = $_FILES['courseOutline']['name'];
        $courseOutlineDestination = 'course-outline/' . $courseOutlineFileName;

        if (move_uploaded_file($courseOutlineFile, $courseOutlineDestination)) {
            echo "Course outline file uploaded successfully.";
        } else {
            echo "Error moving uploaded outline file.";
            exit();
        }
    }

    // Update the course details in the database
    $query = "UPDATE courses 
              SET course_name = ?, 
                  course_description = ?, 
                  course_credits = ?, 
                  course_semester = ?, 
                  course_outline = ? 
              WHERE course_id = ?";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'sssssi', $courseName, $courseDescription, $courseCredits, $courseSemester, $courseOutlineDestination, $courseId);

    if (mysqli_stmt_execute($stmt)) {
        echo "Course details updated successfully.";
    } else {
        echo "Error updating course details: " . mysqli_error($connection);
    }
    header('Location: instructor-dashboard.php');
    exit();
} else {
    echo "Invalid request.";
}

mysqli_close($connection);
?>
