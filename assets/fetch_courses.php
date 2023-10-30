<?php
include_once('./config.php');

if (isset($_GET['sem'])) {
    $selectedSemester = $_GET['sem'];

    $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

    if ($connection) {
        $query = "SELECT course_id, course_name FROM courses WHERE course_semester = '$selectedSemester'";
        $result = mysqli_query($connection, $query);

        $courses = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $courses[] = $row;
        }

        mysqli_close($connection);

        // Return the courses as JSON
        header('Content-Type: application/json');
        echo json_encode($courses);
    }
}
?>