<?php
// Include your database configuration and connection code here
require_once('../config.php');

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$selectedSemester = isset($_POST['semester_Select']) ? $_POST['semester_Select'] : null;
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$timeSlots = ['9:30 AM - 10:30 AM', '10:30 AM - 11:30 AM', '11:30 AM - 12:30 PM', '14:00 PM - 15:00 PM', '15:00 PM - 16:00 PM', '16:00 PM - 17:00 PM'];

echo '<table class="table table-bordered">';
echo '<thead><tr><th colspan="6">Student Timetable</th></tr><tr><th>Time</th><th>' . implode('</th><th>', $days) . '</th></tr></thead>';
echo '<tbody class="timetable-tbody">';

foreach ($timeSlots as $timeSlot) {
    echo '<tr>';
    echo '<td>' . $timeSlot . '</td>';

    foreach ($days as $day) {
        $query = "SELECT day, start_time, end_time, course_name, room_no 
            FROM student_timetable
            WHERE semester = '$selectedSemester'
              AND day = '$day' 
              AND start_time <= '$timeSlot' 
              AND end_time > '$timeSlot'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo '<td>';
            echo '' . $row['course_name'] . '<br>';
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

$conn->close();
?>