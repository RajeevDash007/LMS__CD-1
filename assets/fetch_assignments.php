<?php
include 'config.php'; 

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$instructorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$sql = "SELECT AssignmentTitle, batch, AssignmentDueDate FROM assignment WHERE instructor_id = $instructorId ORDER BY AssignmentDueDate ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deadline = strtotime($row['AssignmentDueDate']);
        $currentTime = time();
        if ($deadline > $currentTime) {
            echo '<tr class="assglist">' .
                '<td>' . $row['AssignmentTitle'] . '</td>' .
                '<td>' . $row['batch'] . '</td>' .
                '<td>' . $row['AssignmentDueDate'] . '</td>' .
                '</tr>';
        }
    }
} else {
    echo "<tr><td colspan='3'>No assignments found.</td></tr>";
}

$conn->close();
?>
