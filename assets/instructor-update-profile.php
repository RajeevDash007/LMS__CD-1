<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: auth.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config.php';

    $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

    if (!$connection) {
        echo "Database connection error: " . mysqli_connect_error();
        exit();
    }

    $instructorId = $_SESSION['user_id'];
    $instructorName = $_POST['instructor_name'];
    $instructorPhoto = $_POST['instructor_photo'];

    // Update the instructor's name and/or photo in the database
    $query = "UPDATE instructors SET name = ?, photo = ? WHERE instructor_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ssi', $instructorName, $instructorPhoto, $instructorId);

    if (mysqli_stmt_execute($stmt)) {
        echo "Profile updated successfully.";
        
    } else {
        echo "Error updating profile: " . mysqli_error($connection);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
} else {
    echo "Invalid request.";
}
?>
