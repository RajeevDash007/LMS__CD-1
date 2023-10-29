<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: auth.php');
    exit();
}
require_once('config.php');
$connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if ($connection) {
    $query = "SELECT name, photo FROM instructors WHERE email=?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_email']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_name, $user_photo);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
} else {
    echo "Database connection error.";
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseName = $_POST["course_name"];
    $courseCredits = $_POST["course_credits"];
    $courseDescription = $_POST["course_description"];
    $courseSemester = $_POST['course_semester'];

    // Check if the file input is set and not empty
    if (isset($_FILES['course_outline']) && is_uploaded_file($_FILES['course_outline']['tmp_name'])) {
        $file = $_FILES['course_outline'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];

        $destinationFolder =  'course-outline/';
        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0777, true);
        }

        $destination = $destinationFolder . $fileName;
        move_uploaded_file($fileTmpName, $destination);
    } else {
        echo "File upload error.";
        exit();
    }
    $file = $_FILES['course_outline'];


    $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

    if ($connection) {
        $query = "INSERT INTO courses (course_name, course_credits, course_description, instructor_id, course_semester, course_outline) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "sisiss", $courseName, $courseCredits, $courseDescription, $_SESSION['user_id'], $courseSemester, $destination);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($connection);

        echo "Course created successfully.";
    } else {
        echo "Database connection error.";
    }
}

$courses = [];
$connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if ($connection) {
    $query = "SELECT 
                courses.course_name, 
                instructors.name AS instructor_name, 
                courses.course_id,
                courses.course_credits, 
                courses.course_semester, 
                courses.course_description, 
                courses.course_outline
              FROM courses
              JOIN instructors ON courses.instructor_id = instructors.instructor_id
              WHERE instructors.instructor_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
    $courseOptions = '';
    foreach ($courses as $course) {
        $courseName = $course['course_name'];
        $courseOptions .= "<option value='$courseName'>$courseName</option>";
    }
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
} else {
    echo "Database connection error.";
    exit();
}
$instructorId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if ($connection) {
    $query = "SELECT DISTINCT course_semester FROM courses WHERE instructor_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $instructorId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $semester);

    $semesterOptions = '';
    while (mysqli_stmt_fetch($stmt)) {
        $semesterOptions .= "<option value='$semester'>Semester $semester</option>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);
} else {
    echo "Database connection error.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/dashboard.css">
    <link rel="stylesheet" href="./assets/course.css">
    <link rel="stylesheet" href="https://cdn.lineicons.com/4.0/lineicons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.8.0/html2pdf.bundle.min.js" integrity="sha512-w3u9q/DeneCSwUDjhiMNibTRh/1i/gScBVp2imNVAMCt6cUHIw6xzhzcPFIaL3Q1EbI2l+nu17q2aLJJLo4ZYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        .custom-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
            margin-bottom: 20px;
            max-height: 130px;
            background: linear-gradient(135deg, #17ead9 0%, #6078ea 100%);
            border: none;
        }

        .custom-card .card-title {
            color: #E35335;
            font-size: 18px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .custom-card .card-text {
            color: #666;
            font-weight: 500;
        }

        .custom-card .card-semester {
            color: #666;
            font-weight: 500;
            margin-top: -15px;
        }

        body .dark-mode label {
            color: #fff !important;
        }

        body .dark-mode .timetable tbody tr:nth-child(odd) {
            color: white;
        }

        body .dark-mode .timetable tbody tr:nth-child(even) {
            background-color: #FFF5EE;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        .btn-toggle:before {
            content: "Off";
            left: -4rem;
            color: #666 !important;
        }

        .btn-toggle>.handle {
            background: #666;
            width: 0.9rem;
            height: 0.9rem;
        }

        .rwd-table {
            margin: auto;
            border-collapse: collapse;
        }

        .rwd-table thead tr:first-child {
            border-top: none;
            background: #428bca;
            color: #fff;
        }

        .rwd-table tr {
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            background-color: #f5f9fc;
        }

        .rwd-table tr:nth-child(even) {
            background-color: #ebf3f9;
        }

        .rwd-table th {
            display: none;
        }

        .rwd-table td {
            display: block;
        }

        .rwd-table td:first-child {
            margin-top: .5em;
        }

        .rwd-table td:last-child {
            margin-bottom: .5em;
        }

        .rwd-table td:before {
            content: attr(data-th) ": ";
            font-weight: bold;
            width: 120px;
            display: inline-block;
            color: #000;
        }

        .rwd-table th,
        .rwd-table td {
            text-align: left;
        }

        .rwd-table {
            color: #333;
            border-radius: .4em;
            overflow: hidden;
        }

        .rwd-table tr {
            border-color: #bfbfbf;
        }

        .rwd-table th,
        .rwd-table td {
            padding: .5em 1em;
        }

        .timetable table {
            width: 100%;
            margin-bottom: 20px;
        }

        .timetable th,
        .timetable td {
            text-align: center;
            vertical-align: middle;
            padding: 15px;
        }

        .timetable th {
            background-color: #9EC0EA;
        }

        .timetable tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        @media screen and (max-width: 601px) {
            .rwd-table tr:nth-child(2) {
                border-top: none;
            }
        }

        @media screen and (min-width: 600px) {
            .rwd-table tr:hover:not(:first-child) {
                background-color: #d8e7f3;
            }

            .rwd-table td:before {
                display: none;
            }

            .rwd-table th,
            .rwd-table td {
                display: table-cell;
                padding: .25em .5em;
            }

            .rwd-table th:first-child,
            .rwd-table td:first-child {
                padding-left: 0;
            }

            .rwd-table th:last-child,
            .rwd-table td:last-child {
                padding-right: 0;
            }

            .rwd-table th,
            .rwd-table td {
                padding: 1em !important;
            }
        }

        .fc-today {
            background-color: #E35335 !important;
        }

        .course-title:hover {
            text-decoration: underline;
        }
    </style>
    <title>Instructor Dashboard</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg align-items-start">
        <button class="navbar-toggler" type="button" id="menu-toggle">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="col-4 col-sm-3">
            <h4 style="margin-top: 10px;color:#666;">Instructor Dashboard</h1>
        </div>

        <div class="col-4 col-md-5 d-none d-md-flex  flex-column">
            <div class="input-group m-2 d-none d-md-flex">
                <input type="search" class="form-control animated-search-filter search" id="pesquisageral" name="pesquisageral" placeholder="search infos and modules" aria-label="Pesquise" aria-describedby="button-addon2">
            </div>
        </div>

        <div class="col-4">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto mt-md-2">
                    <li class="nav-item pl-4 dropdown">
                        <img src="<?php echo $user_photo; ?>" class="rounded-circle" alt="Instructor Photo" style="width: 40px; height: 40px; object-fit: cover; cursor:pointer;" data-toggle="dropdown">
                        <div class="dropdown-menu dropdown-menu-right" style="position: static; float: left;">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profileModal">My Profile</a>
                            <a class="dropdown-item" href="./assets/logout.php" style="color: crimson;">Log Out</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="ml-5 mr-3 px-3 bg-white" id="collapseSearch" style="max-height: 50vh;overflow: auto;">
        <div class="container px-4">
            <div class="row" id="result">



            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper" style="z-index: 1">
            <div class="list-group list-group-flush bg-white" id="sidenav">
                <a class="d-flex align-items-center border-bottom p-3 text-secondary home active"><i class="lni lni-home size-sm pr-4 font-24"></i>Home</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary acad" id="acad"><i class="lni lni-graduation size-sm pr-4 font-24"></i></i>Assign tasks</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary adm" id="adm"><i class="lni lni-briefcase size-sm pr-4 font-24"></i>Time Table</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary beneficios"><i class="lni lni-handshake size-sm pr-4 font-24"></i>Statistics</a>
                <!-- <a class="d-flex align-items-center border-bottom p-3 text-secondary visoes" style="white-space: nowrap;"><i class="lni lni-files size-sm pr-4 font-24"></i>extra sect</a> -->
            </div>
        </div>
        <div id="page-content-wrapper">
            <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog" role="document" style="min-width: 800px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="profileModalLabel">My Profile</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="profileForm" method="POST" action="./assets/instructor-update-profile.php">
                                <div class="m-b-25" style="margin-left: 330px;">
                                    <img src="<?php echo $user_photo; ?>" alt="User-Profile-Image" style="width: 100px; height: auto; border-radius: 50%;">
                                </div>
                                <div class="form-group">
                                    <label for="instructorName">Name</label>
                                    <input type="text" class="form-control" id="instructorName" name="instructor_name" value="<?php echo $user_name; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="instructorPhoto">Photo</label>
                                    <input type="text" class="form-control-file" id="instructorPhoto" name="instructor_photo" value="<?php echo $user_photo; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                            <div class="modal-header" style="border-bottom: 1px solid #000; margin-top:20px;margin-bottom:20px;">
                                <h5 class="modal-title" id="profileModalLabel" style="color: red;">Change your password</h5>
                            </div>
                            <form id="changePasswordForm" method="POST" action="./assets/instructor-change-password.php">
                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword" name="current_password">
                                </div>
                                <div class="form-group">
                                    <label for="newPassword">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" name="new_password">
                                </div>
                                <div class="form-group">
                                    <label for="confirmNewPassword">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmNewPassword" name="confirm_new_password">
                                </div>
                                <button type="submit" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="courseModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog" role="document" style="max-width:800px; border-radius:20px;">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #F06105; color: white;">
                            <h5 class="modal-title" id="courseModalLabel">Edit Course Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="courseForm" method="POST" action="./update_course.php" enctype="multipart/form-data">
                                <input type="hidden" id="courseId" name="courseId" value="<?php echo $course['course_id']; ?>">
                                <div class="form-group">
                                    <label for="courseName">Course Name</label>
                                    <input type="text" class="form-control" id="courseName" name="courseName" value="<?php echo $course['course_name']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="courseDescription">Course Description</label>
                                    <textarea class="form-control" id="courseDescription" name="courseDescription"><?php echo $course['course_description']; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="courseCredits">Course Credits</label>
                                    <input type="number" class="form-control" id="courseCredits" name="courseCredits" value="<?php echo $course['course_credits']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="courseSemester">Course Semester</label>
                                    <input type="text" class="form-control" id="courseSemester" name="courseSemester" value="<?php echo $course['course_semester']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="courseOutline">Course Outline (PDF)</label>
                                    <input type="file" class="form-control" id="courseOutline" name="courseOutline" accept=".pdf">
                                </div>
                                <iframe id="courseOutlineIframe" src="<?php echo $course['course_outline']; ?>" frameborder="0" width="100%" height="500"></iframe>
                                <button type="submit" class="btn btn-primary">Update Course</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-4">

                <div class="row mx-auto mt-3 justify-content-center d-flex d-md-none">
                    <button type="button" class="btn btn-sm btn-toggle" data-toggle="button" aria-pressed="false" autocomplete="off" onclick="toggleDarkLight()">
                        <div class="handle"></div>
                    </button>
                    <p class="mb-0">Dark mode </p>
                </div>

                <div class="input-group m-2 d-flex d-md-none mx-auto mt-4 w-100">
                    <input type="search" class="form-control" placeholder="Search information and modules in general" aria-label="Pesquise" aria-describedby="button-addon2">
                </div>
                <div class="row py-3">
                    <div class="col-md-8">
                        <div class="home display fadeInUp" style="display: block">
                            <div class="container">
                                <div class="row my-3 my-md-5">
                                    <div class="card rounded-lg border-0 cards-short w-100">
                                        <div class="row">
                                            <div class="col-sm-6 order-1 order-sm-1">
                                                <h4 class="text-primary pt-3 pt-sm-5 pl-3 pl-lg-4 pr-3">Hi <?php echo $user_name; ?>, welcome</h4>
                                            </div>
                                            <div class="col-sm-6 d-flex d-lg-block d-lg-block align-items-center justify-content-center order-0 order-sm-1">

                                            </div>
                                            <div class="col-12 order-2 order-sm-1">
                                                <p class="px-3 pt-2 pb-3 text-banner" style="color: #5584bc; font-size: 15px">Welcome to our Instructor Dashboard, a streamlined hub designed to enhance teaching efficiency. Manage courses, communicate with students, track performance and access resources. Let's elevate education, together!
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container mt-4">
                                    <?php if (!empty($courses)) : ?>
                                        <h2 style="margin-bottom: 30px;">Your Courses</h2>
                                        <div class="row">
                                            <?php foreach ($courses as $course) : ?>
                                                <div class="col-md-3">
                                                    <div class="card custom-card">
                                                        <div class="card-body">
                                                            <h5 class="card-title course-title" data-toggle="modal" data-target="#courseModal" data-courseid="<?php echo $course['course_id']; ?>" data-description="<?php echo $course['course_description']; ?>" data-credits="<?php echo $course['course_credits']; ?>" data-semester="<?php echo $course['course_semester']; ?>" data-outline="<?php echo isset($course['course_outline']) ? $course['course_outline'] : ''; ?>" style="cursor: pointer;"><?php echo $course['course_name']; ?></h5>
                                                            <p class="card-text">Credits: <b><?php echo $course['course_credits']; ?></b></p>
                                                            <p class="card-semester">Semester: <b><?php echo $course['course_semester']; ?></b></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $('.course-title').click(function() {
                                           
                                            const courseId = $(this).data('courseid');
                                            const courseName = $(this).text();
                                            $('#courseId').val(courseId);
                                            $('#courseName').val(courseName);

                                            $('#courseModal').modal('show');
                                        });
                                    });
                                </script>


                                <div class="container mt-4 col-md-6" style="margin-left:0px;">
                                    <h2 style="margin-bottom:20px;">Create a Course</h2>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="courseName">Course Name</label>
                                            <input type="text" class="form-control" id="courseName" name="course_name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="courseCredits">Course Credits</label>
                                            <input type="number" class="form-control" id="courseCredits" name="course_credits" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="courseDescription">Course Description</label>
                                            <textarea class="form-control" id="courseDescription" name="course_description" rows="3" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="courseOutline">Course Outline (PDF)</label>
                                            <input type="file" class="form-control" id="courseOutline" name="course_outline" accept=".pdf" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="courseSemester">Course Semester</label>
                                            <select class="form-control" id="courseSemester" name="course_semester" required>
                                                <option value="">Select Semester</option>
                                                <option value="1">1st Semester</option>
                                                <option value="2">2nd Semester</option>
                                                <option value="3">3rd Semester</option>
                                                <option value="4">4th Semester</option>
                                                <option value="5">5th Semester</option>
                                                <option value="6">6th Semester</option>
                                                <option value="7">7th Semester</option>
                                                <option value="8">8th Semester</option>
                                                <option value="9">9th Semester</option>
                                                <option value="10">10th Semester</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Create Course</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="acad display fadeInUp" style="display: none">
                            <h3 class="mt-4">Assign Tasks</h3>
                            <div class="container">
                                <div id="successMessage" style="display: none; color:#6fc420;">
                                    <div class="ui positive save message">
                                        <i class="close close-positive icon"></i>
                                        <div class="header">
                                            <i class="fas fa-check-circle"></i> Success
                                        </div>
                                        <p>Task assigned.
                                        </p>
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <div class="container mt-4 col-md-5" style="margin-left:0px;">
                                        <!-- add your code here -->
                                        <form id="assignmentForm" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="title">Assignment Title:</label>
                                                <input type="text" class="form-control" name="title" id="title" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="question_file">Assignment Question File:</label>
                                                <input type="file" class="form-control" name="question_file" id="question_file" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="due_date">Assignment Due Date:</label>
                                                <input type="date" class="form-control" name="due_date" id="due_date" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="marks">Assignment Marks:</label>
                                                <input type="number" min="0" max="100" class="form-control" name="marks" id="marks" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="semester">Select Semester:</label>
                                                <select name="semester" class="form-control" id="semester" required>
                                                    <option value="">Select Semester</option>
                                                    <?php echo $semesterOptions; ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="course">Select Course:</label>
                                                <select id="courseSelect" class="form-control" name="course_name">
                                                    <?php echo $courseOptions; ?>
                                                </select>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Create Assignment</button>
                                        </form>
                                        <script>
                                            document.getElementById('assignmentForm').addEventListener('submit', function(event) {
                                                event.preventDefault();
                                                var formData = new FormData(this);
                                                var xhr = new XMLHttpRequest();
                                                xhr.open('POST', 'create_assignment.php', true);
                                                xhr.onload = function() {
                                                    if (xhr.status === 200) {
                                                        document.getElementById('successMessage').style.display = 'block';
                                                        setTimeout(function() {
                                                            document.getElementById('assignmentForm').reset();
                                                            document.getElementById('successMessage').style.display = 'none';
                                                        }, 3000);
                                                    } else {
                                                        console.error('Form submission failed with status ' + xhr.status);
                                                    }
                                                };
                                                xhr.send(formData);
                                            });
                                        </script>



                                        <div class="animated-search-filter sysacad grid fadeInUp delay-1">


                                        </div>

                                    </div>
                                    <div class="container mt-4 col-md-5 table-container" style="margin-left:0px;">
                                        <h2 style="margin-top:-65px;margin-bottom:40px;">Assignments List</h2>
                                        <table class="table rwd-table">
                                            <thead>
                                                <tr>
                                                    <th>Assg. Name</th>
                                                    <th>Semester</th>
                                                    <th>Deadline</th>
                                                </tr>
                                            </thead>
                                            <tbody id="assignmentTableBody">
                                                <?php
                                                include './assets/fetch_assignments.php';
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="adm display fadeInUp" style="display: none">
                            <h3 class="mt-4">Time Table</h3>
                            <div class="container" style="margin-top: 40px;">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="courseSearch">Search by Course Name:</label>
                                        <input type="text" class="form-control" id="courseSearch" placeholder="Enter course name">
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <!-- add your code here -->
                                    <?php
                                    require_once('./config.php');
                                    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }
                                    $instructor_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
                                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                                    echo '<div  id="timetable-container" class="table-responsive timetable">';
                                    echo '<table class="table table-bordered">';
                                    echo '<thead><tr><th>Time</th><th>' . implode('</th><th>', $days) . '</th></tr></thead>';
                                    echo '<tbody class="timetable-tbody">';

                                    $timeSlots = ['9:30 AM - 10:30 AM', '10:30 AM - 11:30 AM', '11:30 AM - 12:30 PM', '2:00 PM - 3:00 PM', '3:00 PM - 4:00 PM', '4:00 PM - 5:00 PM'];
                                    foreach ($timeSlots as $timeSlot) {
                                        echo '<tr>';
                                        echo '<td>' . $timeSlot . '</td>';

                                        foreach ($days as $day) {
                                            $query = "SELECT c.course_name, cl.semester, cl.room_no 
                                            FROM classes cl
                                            JOIN courses c ON cl.course_id = c.course_id
                                            WHERE cl.instructor_id = $instructor_id 
                                                AND cl.day = '$day' 
                                                AND cl.start_time <= '$timeSlot' 
                                                AND cl.end_time > '$timeSlot'";
                                            $result = $conn->query($query);

                                            if ($result->num_rows > 0) {
                                                $row = $result->fetch_assoc();
                                                echo '<td>';
                                                echo '' . $row['course_name'] . '<br>';
                                                echo '( ' . $row['semester'] . ' )' . '<br>';
                                                echo '( ' . $row['room_no'] . ' )';
                                                echo '</td>';
                                            } else {
                                                echo '<td></td>';
                                            }
                                        }

                                        echo '</tr>';
                                    }

                                    echo '</tbody>';
                                    echo '</table>';
                                    echo '</div>';
                                    $conn->close();
                                    ?>
                                    <div class="col-md-6">
                                        <button onclick="generatePDF()" class="btn btn-primary">Download Timetable as PDF</button>
                                    </div>
                                    <script>
                                        function generatePDF() {
                                            const element = document.getElementById('timetable-container');
                                            html2pdf(element);
                                        }
                                    </script>
                                    <div class="animated-search-filter adm grid fadeInUp delay-1">

                                    </div>
                                </div>
                            </div>
                        </div>


                        <script>
                            $(document).ready(function() {
                                const originalTableHtml = $('.timetable .timetable-tbody').html();

                                $('#courseSearch').on('input', function() {
                                    const searchTerm = $(this).val().toLowerCase();
                                    const tableBody = $('.timetable-tbody');

                                    if (searchTerm === '') {
                                        tableBody.html(originalTableHtml);
                                    } else {
                                        tableBody.find('tr').each(function() {
                                            let found = false;
                                            $(this).find('td:gt(0)').each(function() {
                                                const cellContent = $(this).text();
                                                if (cellContent.toLowerCase().includes(searchTerm)) {
                                                    found = true;
                                                    const lines = cellContent.split('(');
                                                    const formattedContent = lines.join('<br>(');
                                                    $(this).html(formattedContent);
                                                } else {
                                                    $(this).html('');
                                                }
                                            });
                                            if (!found) {
                                                $(this).hide();
                                            } else {
                                                $(this).show();
                                            }
                                        });
                                    }
                                });
                            });
                        </script>
                       

                        <div class="beneficios display fadeInUp" style="display: none">
                            <div class="container">
                                <div class="mb-5">
                                    <h3 class="my-4">Statistics</h3>
                                    

                                </div>
                            </div>
                        </div>

                        <!-- <div class="visoes display fadeInUp" style="display: none">
                            <h3 class="mt-4">extra section</h3>
                            <div class="container">
                                <div class="row mb-5">
                                    <p class="lead w-100"></p>
                                   

                                    <div class="animated-search-filter grid fadeInUp delay-1">

                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>

                    <div class="col-md-4 fadeInUp atalhos">
                        <div class="row mx-auto mt-3 justify-content-center d-none d-md-flex">
                            <button type="button" class="btn btn-sm btn-toggle" data-toggle="button" aria-pressed="false" autocomplete="off" onclick="toggleDarkLight()">
                                <div class="handle"></div>
                            </button>
                            <p class="mb-0">Dark mode </p>
                        </div>

                        <div class="row">
                            <div class="container mt-3">

                                <div class="my-4 mt-md-0">
                                    <div class="card shadow-card rounded-lg border-0 d-flex align-items-center justify-content-center p-4">
                                        <div id="calendar"></div>
                                    </div>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $('#calendar').fullCalendar({
                                            header: {
                                                left: 'prev',
                                                center: 'title',
                                                right: 'next'
                                            },
                                            defaultDate: new Date(),
                                            editable: true,
                                            eventLimit: true,
                                            events: [
                                                //add all the holidays in this format
                                                {
                                                    title: 'Event 1',
                                                    start: '2022-01-01',
                                                    end: '2022-01-03'
                                                },
                                            ]
                                        });
                                    });
                                </script>

                                <!-- <div class="card shadow-card rounded-lg border-0 px-3 pb-4 mb-4">
                                    <p class="text-center mb-0 mt-3">Extra things</p>


                                </div> -->

                            </div>

                            <!-- <div class="row mb-5 mt-2 fadeInUp delay-2">
                                <div class="col-md-12 mt-4 mt-md-0 notice-container">
                                    <div class="card shadow-card rounded-lg border-0 d-flex align-items-center justify-content-center p-4 fadeInUp">
                                        <p class="text-center mb-3">Notice</p>

                                        <div class="input-group m-2 d-flex">
                                            <input type="search" class="form-control" placeholder="Localizar por Nome, RU, E-mail ou Departamento" aria-label="Pesquise" aria-describedby="button-addon2">
                                        </div>

                                        <div class="rounded ramal-box m-2 px-2 w-100" style="height: 280px; overflow-y: scroll">
                                            <div class="contacts">
                                                <div class="" unselectable="on"><b>John Doe</b> <br> <span class="text-primary">3343-7198</span><br> ASSESSORIA
                                                    DE
                                                    COMUNICAÇÃO <br>gabriel.toledo@codepen.io<br>
                                                </div>
                                                <input class="check" type="checkbox">
                                                <div class="heart"></div>
                                                </input>
                                            </div>
                                            <div class="contacts">
                                                <div class="" unselectable="on"><b>John Doe</b> <br> <span class="text-primary">3343-7198</span><br> ASSESSORIA
                                                    DE
                                                    COMUNICAÇÃO <br>gabriel.toledo@codepen.io<br>
                                                </div>
                                                <input class="check" type="checkbox">
                                                <div class="heart"></div>
                                                </input>
                                            </div>
                                            <div class="contacts">
                                                <div class="" unselectable="on"><b>John Doe</b> <br> <span style="color:red">Ramal
                                                        não cadastrado</span><br>
                                                    ASSESSORIA
                                                    DE COMUNICAÇÃO <br>gabriel.toledo@codepen.io<br>
                                                </div>
                                                <input class="check" type="checkbox">
                                                <div class="heart"></div>
                                                </input>
                                            </div>
                                            <div class="contacts">
                                                <div class="" unselectable="on"><b>John Doe</b> <br> <span class="text-primary">3343-7198</span><br> ASSESSORIA
                                                    DE
                                                    COMUNICAÇÃO <br>gabriel.toledo@codepen.io<br>
                                                </div>
                                                <input class="check" type="checkbox">
                                                <div class="heart"></div>
                                                </input>
                                            </div>
                                            <div class="contacts">
                                                <div class="" unselectable="on"><b>John Doe</b> <br> <span style="color:red">Ramal
                                                        não cadastrado</span><br>
                                                    ASSESSORIA
                                                    DE COMUNICAÇÃO <br>gabriel.toledo@codepen.io<br>
                                                </div>
                                                <input class="check" type="checkbox">
                                                <div class="heart"></div>
                                                </input>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>









                    <script>
                        (function(window) {
                            "use strict";

                            function extend(a, b) {
                                for (var key in b) {
                                    if (b.hasOwnProperty(key)) {
                                        a[key] = b[key];
                                    }
                                }
                                return a;
                            }

                            function CBPFWTabs(el, options) {
                                this.el = el;
                                this.options = extend({}, this.options);
                                extend(this.options, options);
                                this._init();
                            }

                            CBPFWTabs.prototype.options = {
                                start: 0
                            };

                            CBPFWTabs.prototype._init = function() {

                                this.tabs = [].slice.call(this.el.querySelectorAll("nav > ul > li"));

                                this.items = [].slice.call(
                                    this.el.querySelectorAll(".content-wrap > section")
                                );

                                this.current = -1;

                                this._show();

                                this._initEvents();
                            };

                            CBPFWTabs.prototype._initEvents = function() {
                                var self = this;
                                this.tabs.forEach(function(tab, idx) {
                                    tab.addEventListener("click", function(ev) {
                                        ev.preventDefault();
                                        self._show(idx);
                                    });
                                });
                            };

                            CBPFWTabs.prototype._show = function(idx) {
                                if (this.current >= 0) {
                                    this.tabs[this.current].className = this.items[this.current].className =
                                        "";
                                }

                                this.current =
                                    idx != undefined ?
                                    idx :
                                    this.options.start >= 0 && this.options.start < this.items.length ?
                                    this.options.start :
                                    0;
                                this.tabs[this.current].className = "tab-current";
                                this.items[this.current].className = "content-current";
                            };


                            window.CBPFWTabs = CBPFWTabs;
                        })(window);

                        (function() {
                            [].slice.call(document.querySelectorAll(".tabs")).forEach(function(el) {
                                new CBPFWTabs(el);
                            });
                        })();

                        function renderList(filter = "") {
                            let inputHtml = "";
                            let filteredList = [];
                            let linkList = [];

                            if (filter.length > 0) {
                                filteredList = lstsis.filter((item, index) => {
                                    if (item.toLowerCase().includes(filter.toLowerCase())) {
                                        linkList.push(lstsisurl[index]);
                                        return true;
                                    } else {
                                        return false;
                                    }
                                });
                            }

                            $("#result").html(inputHtml);
                        }

                        $("#pesquisageral").on("input", function(e) {
                            let pesquisageral = $("#pesquisageral").val();
                            renderList(pesquisageral);
                        });

                        renderList();


                        $(document).ready(function() {
                            $.get(
                                "http://apidev.accuweather.com/currentconditions/v1/45883.json?language=pt&apikey=hoArfRosT1215",
                                function(data) {
                                    $("#temperatura").html(data[0].Temperature.Metric.Value + " °c");
                                    $("#clima").html("Céu: " + data[0].WeatherText);
                                    $("#icone").attr(
                                        "src",
                                        "https://vortex.accuweather.com/adc2010/images/slate/icons/" +
                                        data[0].WeatherIcon +
                                        ".svg"
                                    );
                                }
                            );
                        });

                        $("a.cards-func").click(function(event) {
                            if ($(this).attr("target") != "_blank") {


                                event
                                    .preventDefault();
                                var url = $(this).attr(
                                    "href");
                                $(".loader, .lds-ring").fadeIn();
                                $("#iframe").attr("src", url);

                                $("#page-content-wrapper, .page-loader").toggleClass(
                                    "d-none");

                                /* Manipula o iframe para aplicar correções no estilo da intranet antiga
                                 ** Oculta os menus de topo, entre outros itens da antiga intranet */
                                $("#iframe").on("load", function() {
                                    $("#iframe")
                                        .contents()
                                        .find("head")
                                        .append(
                                            "<style>#pc_user { display: none;} #pc_sair { display: none;} #pc_fundomenu { display: none;}#pc_busca { display: none;} #PC_brilho { display: none !important; } #pc_centro { position: inherit !important; }</style>"
                                        );

                                    $(".loader, .lds-ring").fadeOut();
                                });
                            }
                        });

                        $(".close-card").click(function() {
                            $(this).prev().toggleClass("cards-short--disable");
                            $(this).prev().toggleClass("cards-short");
                            $(this).toggleClass("transform-45");
                        });

                        $(".form-short .submit").click(function() {
                            var appendItems = $(".modal-body.shortcuts").find(".cards-short")
                                .parent();
                            $(appendItems).removeClass("cards-short--disable");
                            $(".block.shortcuts").append(appendItems);
                        });

                        $(".remove").click(function() {
                            var returnItems = $(".block.shortcuts")
                                .find(".cards-short--disable")
                                .parent();
                            $(".modal-body.shortcuts").append(returnItems);
                        });

                        function toggleDarkLight() {
                            var body = document.getElementById("page-content-wrapper");
                            var frame = document.getElementById("page-content-frame");
                            if ($(body).hasClass("dark-mode")) {
                                body.className = "";
                                frame.className = "page-loader d-none";
                            } else {
                                body.className = "dark-mode";
                                frame.className = "page-loader dark-mode d-none";
                            }

                            /* Troca a imagem de bem-vindo para o modo dark
                             ** Na imagem existem dois atributos de imagem
                             ** Quando o modo dark é alternado, esse links também precisar ser alternados */
                            var _this = $("#welcome");
                            var current = _this.attr("src");
                            var swap = _this.attr("data-swap");
                            _this.attr("src", swap).attr("data-swap", current);
                        }

                        $("#menu-toggle").click(function(e) {
                            e.preventDefault();
                            $("#wrapper").toggleClass("toggled");
                        });

                        $("#myModal").on("shown.bs.modal", function() {
                            $("#myInput").trigger("focus");
                        });

                        function geral() {
                            $(".display").css("display", "none");
                            $(".atalhos").css("display", "block");
                            $("#page-content-wrapper").removeClass("d-none");
                            $(".page-loader").addClass("d-none");
                        }

                        //Click Sidenav menu
                        $(".list-group a").click(function() {
                            $(".list-group a.text-secondary").removeClass("active");
                            $("html, body").animate({
                                scrollTop: 0
                            }, 500);
                            $(this).addClass("active");


                            const arrayMenu = ["home", "acad", "adm", "beneficios", "visoes", "info"];

                            for (var i = 0; i < arrayMenu.length; i++) {
                                if ($(this).hasClass(arrayMenu[i])) {
                                    geral();
                                    $("." + arrayMenu[i] + ".display").css("display", "block");
                                }
                            }
                        });
                    </script>


</body>

</html>