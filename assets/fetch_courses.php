<?php
include_once('./config.php');

if (isset($_GET['sem'])) {
    $selectedSemester = $_GET['sem'];

    $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

    if ($connection) {
        $query = "SELECT course_id, course_name FROM courses WHERE course_semester = '$selectedSemester'";
        $result = mysqli_query($connection, $query);

        if (mysqli_num_rows($result) > 0) {
            // There are matching courses, so return them as JSON
            $courses = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $courses[] = $row;
            }

            mysqli_close($connection);

            header('Content-Type: application/json');
            echo json_encode($courses);
        } else {
            // No matching courses found, return only "Select course" option
            $noCourses = array(array('course_id' => 'select_course', 'course_name' => 'Select course'));
            header('Content-Type: application/json');
            echo json_encode($noCourses);
        }
    }
}
?>