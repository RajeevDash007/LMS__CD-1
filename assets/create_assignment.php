<?php
require_once './config.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {   
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $title = $_POST['title'];
    $dueDate = $_POST['due_date'];
    $marks = $_POST['marks'];
    $semester = $_POST['semester'];
    $course = $_POST['course_name'];

    
    $sql = "INSERT INTO assignment (AssignmentTitle, AssignmentQuestionURL, AssignmentDueDate, AssignmentMarks, batch)
            VALUES ('$title', '$targetFileName', '$dueDate', $marks, $semester)";

    if ($conn->query($sql) === TRUE) {
        echo "Assignment created successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

 
    $conn->close();
}
