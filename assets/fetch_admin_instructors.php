<?php
// Assuming you have a database connection established
require_once(__DIR__ . '/../config.php');


$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to retrieve data from the 'instructor' table
$sql = "SELECT name, email FROM instructors";
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
