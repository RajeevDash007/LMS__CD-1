<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !=='admin') {
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
    $instructorName = $_POST['admin_name'];
    $instructorPhoto = $_POST['admin_photo'];

    // Update the instructor's name and/or photo in the database
    $query = "UPDATE administrators SET name = ?, photo = ? WHERE admin_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ssi', $instructorName, $instructorPhoto, $instructorId);

    if (mysqli_stmt_execute($stmt)) {
        echo "Profile updated successfully.";
        
    } else {
        echo "Error updating profile: " . mysqli_error($connection);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    header('Location: ../admin-dashboard.php');
    exit();
} else {
    echo "Invalid request.";
}
?>
