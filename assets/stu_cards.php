<?php
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you receive the selected semester from the frontend
    $selectedSemester = $_POST["selected_semester"]; // Change this according to your needs

    // Connect to your MySQL database (you should have these values defined)
    require_once('../config.php');

    $connection = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Prepare and execute a query to fetch course data for the selected semester
    $query = "SELECT course_name, course_credits ,course_outline FROM courses WHERE course_semester = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $selectedSemester);
    $stmt->execute();

    // Get the result of the query
    $result = $stmt->get_result();

    $coursesData = [];

    while ($row = $result->fetch_assoc()) {
        $coursesData[] = $row;
    }

    // Close the database connection
    $stmt->close();
    $connection->close();

    // Send the course data to the frontend as JSON
    header("Content-Type: application/json");
    echo json_encode($coursesData);
} else {
    // Handle invalid requests or other error cases
    http_response_code(400); // Bad Request
    echo "Invalid request.";
}
?>