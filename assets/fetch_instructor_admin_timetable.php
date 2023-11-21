<?php
// Include your database configuration and connection code here
require_once('../config.php');

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$instructor_id = isset($_POST['instructor_id']) ? $_POST['instructor_id'] : null;
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$timeSlots = ['9:30 AM - 10:30 AM', '10:30 AM - 11:30 AM', '11:30 AM - 12:30 PM', '14:00 PM - 15:00 PM', '15:00 PM - 16:00 PM', '16:00 PM - 17:00 PM'];

echo '<div id="instructor-timetable-container" class="table-responsive timetable">';
echo '<table class="table table-bordered">';
echo '<thead><tr><th colspan="6">Instructor Timetable</th></tr><tr><th>Time</th><th>' . implode('</th><th>', $days) . '</th></tr></thead>';
echo '<tbody class="timetable-tbody">';

foreach ($timeSlots as $timeSlot) {
    echo '<tr>';
    echo '<td>' . $timeSlot . '</td>';

    foreach ($days as $day) {
        $query = "SELECT class_id, semester, day, start_time, end_time, course_id, room_no 
                  FROM classes
                  WHERE instructor_id = '$instructor_id'
                      AND day = '$day' 
                      AND start_time <= '$timeSlot' 
                      AND end_time > '$timeSlot'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo '<td>';
            // Assuming you have a table named 'courses' for course details
            $courseQuery = "SELECT course_name FROM courses WHERE course_id = '{$row['course_id']}'";
            $courseResult = $conn->query($courseQuery);
            $courseRow = $courseResult->fetch_assoc();

            echo '' . $courseRow['course_name'] . '<br>';
            echo '( ' . $row['semester'] . ' )'. '<br>';
            echo '( ' . $row['room_no'] . ' )';
            echo '</td>';
        } else {
            echo '<td></td>';
        }
    }

    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
echo '</div>';
$conn->close();
?>