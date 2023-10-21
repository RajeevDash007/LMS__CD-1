<?php
session_start();
require_once('../config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmNewPassword = $_POST["confirm_new_password"];

    if ($newPassword != $confirmNewPassword) {
        echo "New password and confirm password do not match.";
    } else {
        $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
        if ($connection) {
            $email = $_SESSION['user_email'];
            $query = "SELECT password FROM students WHERE email=?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $hashed_password);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if (password_verify($currentPassword, $hashed_password)) {
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE students SET password=? WHERE email=?";
                $updateStmt = mysqli_prepare($connection, $updateQuery);
                mysqli_stmt_bind_param($updateStmt, "ss", $newHashedPassword, $email);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);

                echo "Password updated successfully.";
            } else {
                echo "Current password is incorrect.";
            }

            mysqli_close($connection);
        } else {
            echo "Database connection error.";
        }
    }
}
?>
