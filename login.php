<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>

    <?php
    require_once('config.php');

    function getUserType($email, $password, $connection) {
        $query = "SELECT 'student' AS user_type FROM students WHERE email=? AND password=?
                  UNION
                  SELECT 'teacher' AS user_type FROM instructors WHERE email=? AND password=?
                  UNION
                  SELECT 'admin' AS user_type FROM administrators WHERE email=? AND password=?";
        
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $email, $password, $email, $password, $email, $password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $user_type);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        return $user_type;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

        if ($connection) {
            $userType = getUserType($email, $password, $connection);
            mysqli_close($connection);

            if ($userType === "student") {
                header("Location: student-dashboard.php");
                exit();
            } elseif ($userType === "teacher") {
                header("Location: instructor-dashboard.php");
                exit();
            } elseif ($userType === "admin") {
                header("Location: admin-dashboard.php");
                exit();
            } else {
                echo "Invalid credentials. Please try again.";
            }
        } else {
            echo "Database connection error.";
        }
    }
    ?>
</body>

</html>