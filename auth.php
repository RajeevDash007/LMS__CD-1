<!DOCTYPE html>
<html>

<head>
    <title>AUTH PAGE</title>
    <link rel="stylesheet" type="text/css" href="./assets/auth.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
</head>

<body>
    <div class="main">
        <input type="checkbox" id="chk" aria-hidden="true">
        <div class="Registration">
            <form method="POST">
                <label for="chk" aria-hidden="true">Sign up</label>
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>

                <div class="selectField">
                    <select name="role" id="role" onchange="toggleBatchOption()" required>
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="administrator">Administrator</option>
                    </select>
                </div>

                <div id="batchDiv">
                    <input type="text" name="batch" placeholder="Batch">
                </div>

                <input type="text" name="photo" placeholder="Photo URL">
                <button type="submit">Register</button>
            </form>
        </div>

        <?php
        require_once('config.php');

        $response = array();
        $response['success'] = false;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST["name"];
            $email = $_POST["email"];
            $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $role = $_POST["role"];
            $photo = $_POST["photo"];

            try {
                $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                switch ($role) {
                    case 'student':
                        $batch = isset($_POST["batch"]) ? $_POST["batch"] : null;
                        $stmt = $conn->prepare("INSERT INTO Students (name, email, password, batch, photo) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$name, $email, $password, $batch, $photo]);
                        break;
                    case 'instructor':
                        $stmt = $conn->prepare("INSERT INTO Instructors (name, email, password, photo) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$name, $email, $password, $photo]);
                        break;
                    case 'administrator':
                        $stmt = $conn->prepare("INSERT INTO Administrators (name, email, password) VALUES (?, ?, ?)");
                        $stmt->execute([$name, $email, $password]);
                        break;
                    default:
                        break;
                }

                $response['success'] = true;
                $response['message'] = 'Registration successful! You can now login.';
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['message'] = 'Error: ' . $e->getMessage();
            }
        }

        if ($response['success']) {
            echo "<script>alert('Registration successful! You can now login.');</script>";
        }
        ?>

        <div class="login">
            <form method="POST">
                <label for="chk" aria-hidden="true">Login</label>
                <input type="email" name="email" placeholder="email" required><br>
                <input type="password" name="password" placeholder="password" required><br>
                <button type="submit">Login</button>
            </form>
        </div>

        <?php
        session_start();
        require_once('config.php');

        function getUserTypeAndId($email, $password, $connection)
        {
            $query = "SELECT 'student' AS user_type, student_id AS user_id, password FROM students WHERE email=?
              UNION
              SELECT 'teacher' AS user_type, instructor_id AS user_id, password FROM instructors WHERE email=?
              UNION
              SELECT 'admin' AS user_type, admin_id AS user_id, password FROM administrators WHERE email=?";

            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "sss", $email, $email, $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $user_type, $user_id, $hashed_password);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if (password_verify($password, $hashed_password)) {
                return array($user_type, $user_id);
            } else {
                return array(null, null);
            }
        }


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST["email"];
            $password = $_POST["password"];

            $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

            if ($connection) {
                list($userType, $userId) = getUserTypeAndId($email, $password, $connection);
                mysqli_close($connection);

                if ($userType) {
                    $_SESSION['user_type'] = $userType;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_id'] = $userId;
                    switch ($userType) {
                        case 'student':
                            header("Location: student-dashboard.php");
                            exit();
                        case 'teacher':
                            header("Location: instructor-dashboard.php");
                            exit();
                        case 'admin':
                            header("Location: admin-dashboard.php");
                            exit();
                    }
                } else {
                    echo "Invalid credentials. Please try again.";
                }
            } else {
                echo "Database connection error.";
            }
        }
        ?>
    </div>
    <script>
        function toggleBatchOption() {
            var roleSelect = document.getElementById("role");
            var batchDiv = document.getElementById("batchDiv");
            if (roleSelect.value === "student") {
                batchDiv.style.display = "block";
            } else {
                batchDiv.style.display = "none";
            }
        }
    </script>
</body>

</html>