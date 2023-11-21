<?php
// Assuming you have a database connection established
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'lms';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the selected semester from the request
$admin_selectedSemester = $_GET['semester'];

// SQL query to retrieve data from the 'student' table based on the selected semester
$sql = "SELECT name, email FROM students WHERE batch = $admin_selectedSemester";
$result = $conn->query($sql);

// Display data in the table
if ($result->num_rows > 0) {
    $index = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $index . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "</tr>";
        $index++;
    }
} else {
    echo "<tr><td colspan='3'>No records found</td></tr>";
}

$conn->close();
?>
