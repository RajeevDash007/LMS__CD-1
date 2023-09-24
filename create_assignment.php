<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: auth.php');
    exit();
}
require_once './config.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $instructorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $title = $_POST['title'];
    $dueDate = $_POST['due_date'];
    $marks = $_POST['marks'];
    $semester = $_POST['semester'];
    $course = $_POST['course_name'];

    $targetDirectory = "uploads/";
    $targetFileName = $targetDirectory . basename($_FILES["question_file"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFileName, PATHINFO_EXTENSION));


    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["question_file"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }


    if ($_FILES["question_file"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }


    if ($imageFileType !== "pdf" && $imageFileType !== "docx") {
        echo "Sorry, only PDF and DOCX files are allowed.";
        $uploadOk = 0;
    }


    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["question_file"]["tmp_name"], $targetFileName)) {
            echo "The file " . htmlspecialchars(basename($_FILES["question_file"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }


    $sql = "INSERT INTO assignment (instructor_id,AssignmentTitle, AssignmentQuestionURL, AssignmentDueDate, AssignmentMarks, batch, course_name)
            VALUES ($instructorId,'$title', '$targetFileName', '$dueDate', $marks, $semester, '$course')";

    if ($conn->query($sql) === TRUE) {
        echo "Assignment created successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }


    $conn->close();
}
