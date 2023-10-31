<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../auth.php');
    exit();
}

if (isset($_POST['marks']) && is_array($_POST['marks'])) {
    require_once('../config.php');
    $connection = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $instructorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if ($instructorId !== null) {
        foreach ($_POST['marks'] as $studentId => $assignedMarks) {
            $assignmentId = $_POST['assignment_id'];
            $sql = "UPDATE assignmentsubmissions SET marks_given = $assignedMarks WHERE student_id = $studentId AND AssignmentID = $assignmentId";
            $result = $connection->query($sql);

            if (!$result) {
                echo "Error updating marks for student with ID $studentId and Assignment ID $assignmentId";
            }
        }

        echo "Marks assigned successfully.";
        header('Location: ../instructor-dashboard.php');
        exit();
    } else {
        echo "Instructor not logged in.";
    }

    $connection->close();
} else {
    echo "No marks data submitted.";
}
?>
