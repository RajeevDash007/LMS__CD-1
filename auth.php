<!DOCTYPE html>
<html>

<head>
    <title>AUTH PAGE</title>
    <link rel="stylesheet" type="text/css" href="./assets/auth.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
</head>

<body>
    <div class="registrationContainer">
        <div class="formHeading">
            <h2>Registration Form</h2>
        </div>
        <form method="POST">
            <div class="inputField">
                <input type="text" name="name" placeholder="Name" required>
            </div>
            <div class="inputField">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="inputField">
                <input type="password" name="password" placeholder="Password" required><br>
            </div>
            <div class="selectField">
                <select name="role" id="role" onchange="toggleBatchOption()" required>
                    <option value="student">Student</option>
                    <option value="instructor">Instructor</option>
                    <option value="administrator">Administrator</option>
                </select>
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


            <div id="batchDiv">
                <input type="text" name="batch" placeholder="Batch">
            </div>
            <div class="inputField">
                <input type="text" name="photo" placeholder="Photo URL">
            </div>
            <div class="submitButton">
                <button type="submit">Register</button>
            </div>
        </form>
    </div>

    <?php
    require_once('config.php');

    $response = array();
    $response['success'] = false;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
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
</body>

</html>