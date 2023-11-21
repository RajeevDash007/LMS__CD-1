<?php

$message = "";
$redirect = false;
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to your MySQL database
    require_once(__DIR__ . '/../config.php');


    $connection = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Get data from the form
    $semester = $_POST['semesternum'];
    $day = $_POST['stu_day'];
    $start_time = $_POST['stu_start_time'];
    $end_time = $_POST['stu_end_time'];
    $course_name = $_POST['stu_coursename'];
    $room_no = $_POST['stu_room_no'];

    // Prepare and execute an SQL query to insert the data into the table
    $sql = "INSERT INTO student_timetable (semester, day, start_time, end_time, course_name, room_no) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ssssss", $semester, $day, $start_time, $end_time, $course_name, $room_no);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        header("Location: ../admin-dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($connection); 
    }
}else {
    echo "Database connection error.";
}

    // Close the database connection
    $stmt->close();
    $connection->close();

?>
