<?php
session_start();


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: auth.php');
    exit();
}
require_once('config.php');
$connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if ($connection) {
    $query = "SELECT name, photo FROM students WHERE email=?";
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/dashboard.css">
    <link rel="stylesheet" href="./assets/course.css">
    <link rel="stylesheet" href="./assets/stu-cards.css"> 
    <link rel="stylesheet" href="https://cdn.lineicons.com/4.0/lineicons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="./assets/swiper-bundle.min.css">
    <link rel="stylesheet" href="./assets/stu-assign.css"> 
    <script src="https://cdn.jsdelivr.net/npm/swiper@10.2.0/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://rawgit.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <title>Student Dashboard</title>
    <style>
        .fc-today{
            background-color: #E35335 !important;
        }
        .btn-toggle {
    background-color: #3498db; 
    color: #fff; 
    border: none;
}

.btn-toggle .handle {
    background-color: #fff;
}

.btn-toggle[aria-pressed="true"] {
    background-color: #000; 
}

.btn-toggle[aria-pressed="true"] .handle {
    background-color: #fff;
}


#fetchCoursesButton {
  background-color: #007bff;
  border-radius: 8px;
  border: none;
  box-sizing: border-box;
  color: #FFFFFF;
  cursor: pointer;
  display: inline-block;
  font-family: "Haas Grot Text R Web", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 14px;
  font-weight: 500;
  height: 35px;
  line-height: 20px;
  list-style: none;
  margin-left: 5px;
  outline: none;
  padding: 10px 16px;
  position: relative;
  text-align: center;
  text-decoration: none;
  transition: background-color 0.3s, color 0.3s, transform 0.3s;
  vertical-align: baseline;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  transform-style: preserve-3d; /* Enable 3D transformations */
}

#fetchCoursesButton:hover,
#fetchCoursesButton:focus {
  background-color: #7199ce;
  transform: translateY(-2px); /* Lift the button on hover/focus */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
}

.dark-mode label {
    
    color: white; /* White text color */
  }

  #semesterSelect {
    background-color: #7c87aa;
    color: white;
    padding: 5px;
    border: 3px solid #555;
    border-radius: 5px;
    font-size: 16px;
    font-weight:bold;
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

        .btn-primary:hover {
            color: #fff;
            background-color: #e57639;
            border-color: #e57639;
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
            background-color: #bec1c4;
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

        button.substyle {
  padding: 10px 20px;
  background-color: #007bff; /* Green color */
  color: #fff; /* White text color */
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

/* Hover effect for the button */
button.substyle:hover {
  background-color: #1c6bc0; /* Darker green color on hover */
}

/* Disable styles for the button when it's in a disabled state */
button.substyle:disabled {
  background-color: #dddddd; /* Light gray background for disabled state */
  color: #666666; /* Dark gray text color for disabled state */
  cursor: not-allowed;
}
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg align-items-start">
        <button class="navbar-toggler" type="button" id="menu-toggle">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="col-4 col-sm-3">
        <h4 style="margin-top: 10px;color:#666;">Student Dashboard</h1>
        </div>

        <div class="col-4 col-md-5 d-none d-md-flex  flex-column">
            <!-- Pesquisa responsiva -->
            <div class="input-group m-2 d-none d-md-flex">
                <input type="search" class="form-control animated-search-filter search" id="pesquisageral" name="pesquisageral" placeholder="search infos and modules" aria-label="Pesquise" aria-describedby="button-addon2">
            </div>

        </div>

        <div class="col-4">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto mt-md-2">
                    <li class="nav-item pl-4 dropdown">
                        <img src="<?php echo $user_photo; ?>" class="rounded-circle" alt="Student Photo" style="width: 40px; height: 40px; object-fit: cover; cursor:pointer;" data-toggle="dropdown">
                        <div class="dropdown-menu dropdown-menu-right" style="position: static; float: left;">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profileModal">My Profile</a>
                            <a class="dropdown-item" href="./assets/logout.php" style="color: crimson;">Log Out</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

    </nav>
    <!-- /#Navbar -->

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
                <a class="d-flex align-items-center border-bottom p-3 text-secondary acad" id="acad"><i class="lni lni-graduation size-sm pr-4 font-24"></i></i>Assignments</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary adm" id="adm"><i class="lni lni-briefcase size-sm pr-4 font-24"></i>Time Table</a>
                <!-- <a class="d-flex align-items-center border-bottom p-3 text-secondary beneficios"><i
                        class="lni lni-handshake size-sm pr-4 font-24"></i>Statistics</a> -->
                <!-- <a class="d-flex align-items-center border-bottom p-3 text-secondary visoes"
                    style="white-space: nowrap;"><i class="lni lni-files size-sm pr-4 font-24"></i>extra sect</a> -->
                
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
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
                            <form id="profileForm" method="POST" action="./assets/student-update-profile.php">
                                <div class="m-b-25" style="margin-left: 330px;" >
                                    <img src="<?php echo $user_photo; ?>" alt="User-Profile-Image" style="width: 100px; height: auto; border-radius: 50%;">
                                </div>
                                <div class="form-group">
                                    <label for="instructorName">Name</label>
                                    <input type="text" class="form-control" id="studentName" name="student_name" value="<?php echo $user_name; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="instructorPhoto">Photo</label>
                                    <input type="text" class="form-control-file" id="studentPhoto" name="student_photo" value="<?php echo $user_photo; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                            <div class="modal-header" style="border-bottom: 1px solid #000; margin-top:20px;margin-bottom:20px;">
                                <h5 class="modal-title" id="profileModalLabel" style="color: red;">Change your password</h5>
                            </div>
                            <form id="changePasswordForm" method="POST" action="./assets/student-change-password.php">
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
            <div class="container-fluid px-4">
                <!-- Mode Escuro para dispositivos mobile -->
                <div class="row mx-auto mt-3 justify-content-center d-flex d-md-none">
                    <button type="button" class="btn btn-sm btn-toggle" data-toggle="button" aria-pressed="false" autocomplete="off" onclick="toggleDarkLight()">
                        <div class="handle"></div>
                    </button>
                    <p class="mb-0">Dark mode </p>
                </div>
                <!-- Pesquisa mobile responsiva -->
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
                                                <h4 class="text-primary pt-3 pt-sm-5 pl-3 pl-lg-4 pr-3">
                                                Hi <?php echo $user_name; ?>, welcome</h4>
                                            </div>
                                            <div class="col-sm-6 d-flex d-lg-block d-lg-block align-items-center justify-content-center order-0 order-sm-1">

                                            </div>
                                            <div class="col-12 order-2 order-sm-1">
                                                <p class="px-3 pt-2 pb-3 text-banner" style="color: #5584bc; font-size: 15px">Welcome to the student dashboard! Here, you'll find all the tools and resources to make your learning journey successful. If you ever need assistance or have questions, our support team is here to help. Happy learning!"</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="student-course-selection">
                                    <label for="semesterSelect" style="font-weight:bold;">Select Semester:</label>
                                    <select id="semesterSelect">
                                        <option value="1">Semester 1</option>
                                        <option value="2">Semester 2</option>
                                        <option value="3">Semester 3</option>
                                        <option value="4">Semester 4</option>
                                        <option value="5">Semester 5</option>
                                        <option value="6">Semester 6</option>
                                        <option value="7">Semester 7</option>
                                        <option value="8">Semester 8</option>
                                        <option value="9">Semester 9</option>
                                        <option value="10">Semester 10</option>
        
                                    </select>
                                <button id="fetchCoursesButton">Fetch Courses</button>
                                </div>
                                
                            <div class="slide-container swiper">
                            <div class="slide-content">
            <div class="card-wrapper swiper-wrapper" id="dynamic-card-wrapper"></div>
        </div>
                                        
                            
                                
                                <div class="swiper-button-next swiper-navBtn" style="display: none;"></div>
                                <div class="swiper-button-prev swiper-navBtn" style="display: none;"></div>
                                <div class="swiper-pagination"></div>
                            </div>
                            <script src="./assets/swiper-bundle.min.js"></script>
                            <script >
                                var dynamicCardWrapper = document.getElementById("dynamic-card-wrapper");
                                document.getElementById("fetchCoursesButton").addEventListener("click", function () {
                                var selectedSemester = document.getElementById("semesterSelect").value;

                            // Make an AJAX request to fetch course data
                            fetch('./assets/stu_cards.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                body: 'selected_semester=' + selectedSemester,
                                })
                                .then(function (response) {
                                 return response.json();
                                })
                                .then(function (data) {
                            // Update the coursesData array with the fetched data
                            coursesData = data;

                            // Dynamically create and populate the course cards
                            dynamicCardWrapper.innerHTML = ''; // Clear existing cards

                        coursesData.forEach(function (course) {
                        var card = document.createElement("div");
                        card.className = "card swiper-slide";
                    
                        card.innerHTML = `
                        <a class="description" href="${course.course_outline}" target="_blank">
                        <div class="card-content">
                            <h2 class="name">${course.course_name}</h2>
                            <p class="description">Credits: ${course.course_credits}</p>
                            
                        </div>
                        </a>
                        
                         `;
                         
                         
                         
                        dynamicCardWrapper.appendChild(card);
                        });



                        var swiper = new Swiper(".slide-content", {
            slidesPerView: 3,
            spaceBetween: 25,
            loop: false,
            centerSlide: true,
            fade: true,
            grabCursor: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
                dynamicBullets: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 0,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 25,
                },
            },
            
        });
        
        
    })
    .catch(function (error) {
        console.error('Error:', error);
    });
});
                            </script>
                            



                                
                                 <?php
                                 



// Database connection settings
require_once('./config.php');


if (isset($_SESSION['user_email'])) {
    // Retrieve the user's email from the session
    $user_email = $_SESSION['user_email'];
}

try {
    // Create a database connection
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT student_id FROM students WHERE email = :user_email");
    $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && isset($result['student_id'])) {
        $student_id = $result['student_id'];

    // Fetch assignments for the logged-in student's batch
    $stmt = $pdo->prepare("SELECT a.AssignmentTitle, a.AssignmentDueDate, a.AssignmentMarks, c.course_name, i.name AS instructor_name
                        FROM assignment a
                        INNER JOIN instructors i ON a.instructor_id = i.instructor_id
                        INNER JOIN courses c ON a.course_id = c.course_id
                        WHERE a.batch = (SELECT batch FROM students WHERE student_id = :student_id)");
    
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $assignments = array_reverse($assignments);

}
}
 catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?> 


 <div class="table">
    <section class="table_body">
        <table>
            <thead>
                <tr>
                    <th>Sl No.</th>
                    <th>Title</th>
                    <th>Deadline</th>
                    <th>Marks</th>
                    <th>Course</th>
                    <th>Instructor</th>
                </tr>
            </thead>
            <tbody>
                <?php $index = 1; // Initialize a counter variable ?>
                <?php foreach ($assignments as $assignment) : ?>
                    <tr>
                        <td><?= $index ?></td>
                        <td><?= $assignment['AssignmentTitle'] ?></td>
                        <td><?= $assignment['AssignmentDueDate'] ?></td>
                        <td><?= $assignment['AssignmentMarks'] ?></td>
                        <td><?= $assignment['course_name'] ?></td>
                        <td><?= $assignment['instructor_name'] ?></td>
                    </tr>
                    <?php $index++; // Increment the counter variable ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>  


                                
                            </div>
                        </div>

                        <div class="acad display fadeInUp" style="display: none">
                            <h3 class="mt-4">All assignments details</h3>
                            <div class="container">
                                <div class="row mb-5">
                                    <p class="lead w-100"></p>
                                    <!-- add your code here -->
                                    <?php
                                 



// Database connection settings
require_once('./config.php');



if (isset($_SESSION['user_email'])) {
    // Retrieve the user's email from the session
    $user_email = $_SESSION['user_email'];
}

try {
    // Create a database connection
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT student_id FROM students WHERE email = :user_email");
    $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && isset($result['student_id'])) {
        $student_id = $result['student_id'];

    // Fetch assignments for the logged-in student's batch
    $stmt = $pdo->prepare("SELECT a.AssignmentID, a.AssignmentTitle, a.course_name, a.AssignmentQuestionURL, a.AssignmentFileURL, i.name AS instructor_name
                        FROM assignment a
                        INNER JOIN instructors i ON a.instructor_id = i.instructor_id
                        WHERE a.batch = (SELECT batch FROM students WHERE student_id = :student_id)");
    
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

}
}
 catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}


if (isset($_POST['submit'])) {
    $submissionFiles = $_FILES['submission_file'];

    foreach ($_POST['assignment_id'] as $assignmentID) {
        if (isset($submissionFiles['tmp_name'][$assignmentID]) && isset($_POST['assignment_id'][$assignmentID])) {
            $tempFile = $submissionFiles['tmp_name'][$assignmentID];

            // Check if a file was uploaded for this assignment
            if (!empty($tempFile)) {
                $submissionFileName = $submissionFiles['name'][$assignmentID];

                // Define the directory where uploaded files will be stored
                $uploadDirectory = 'submit/'; // Change this to your desired directory

                if (!is_dir($uploadDirectory)) {
                    if (!mkdir($uploadDirectory, 0755, true)) {
                        // Handle directory creation failure
                        echo "Failed to create the directory.";
                        exit;
                    }
                }

                // Generate a unique filename for the uploaded file
                $uniqueFileName = uniqid() . '_' . $submissionFileName;
                $submissionFilePath = $uploadDirectory . $uniqueFileName;

                // Move the uploaded file to the specified directory with the unique filename
                if (move_uploaded_file($tempFile, $submissionFilePath)) {
                    // File uploaded successfully
                    // Insert the submission into the AssignmentSubmissions table.
                    $insertSubmissionQuery = "INSERT INTO assignmentsubmissions(student_id, AssignmentID, SubmissionDate, FilePath) VALUES (:student_id, :AssignmentID, NOW(), :filePath)";
                    $stmt = $pdo->prepare($insertSubmissionQuery);
                    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                    $stmt->bindParam(':AssignmentID', $assignmentID, PDO::PARAM_INT);
                    $stmt->bindParam(':filePath', $submissionFilePath, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        // Update the submission status to 'Closed' for the specific student and assignment
                        $updateSubmissionStatusQuery = "UPDATE assignmentsubmissions SET Status = 'Closed' WHERE student_id = :student_id AND AssignmentID = :assignment_id";
                        $stmt = $pdo->prepare($updateSubmissionStatusQuery);
                        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                        $stmt->bindParam(':assignment_id', $assignmentID, PDO::PARAM_INT);
                        $stmt->execute();

                        echo "File uploaded and submission record inserted successfully!";
                    } else {
                        echo "Submission record insertion failed.";
                        var_dump($stmt->errorInfo());
                    }
                } else {
                    echo "File upload failed for assignment $assignmentID.";
                    var_dump(error_get_last());
                }
            }
        }
    }
}
?>


<div class="table" style="width:80vw;">
    <section class="table_body">
        <form method="POST" action="" enctype="multipart/form-data">
            <table>
                <thead>
                    <tr>
                        <th>Sl No.</th>
                        <th>Title</th>
                        <th>Course</th>
                        <th>Question File</th>
                        <th>Submission file</th>
                        <th>Submit</th>
                        <th>Marks Given</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $assignments = array_reverse($assignments);
                    $index = 1; // Initialize a counter variable ?>
                    <?php foreach ($assignments as $assignment) : ?>
                        <tr>
                            <td><?= $index ?></td>
                            <td><?= $assignment['AssignmentTitle'] ?></td>
                            <td><?= $assignment['course_name'] ?></td>
                            <td><a href="<?= $assignment['AssignmentQuestionURL'] ?>" target="_blank">View Assignment</a></td>
                            <!-- <td><input type="file" name="submission_file[]"></td>  -->
                            <td>
                            <input type="file" name="submission_file[<?= $assignment['AssignmentID'] ?>]">
                                <!-- Include a hidden input field for AssignmentID -->
                                <input type="hidden" name="assignment_id[<?= $assignment['AssignmentID'] ?>]" value="<?= $assignment['AssignmentID'] ?>">
                            </td> 
                            <!-- <td><button type="submit" name="submit">Submit</button></td> -->
                            <td>
                                <?php
                                // Check the submission status for this assignment
                                $checkSubmissionStatusQuery = "SELECT Status FROM assignmentsubmissions WHERE student_id = :student_id AND AssignmentID = :assignment_id";
                                $stmt = $pdo->prepare($checkSubmissionStatusQuery);
                                $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
                                $stmt->bindParam(':assignment_id', $assignment['AssignmentID'], PDO::PARAM_INT);
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                               if ($result && $result['Status'] == 'Closed') {
        echo "Submission Closed"; // Display "Submission Closed" message
    } else {
        echo '<button type="submit" name="submit" class="substyle">Submit</button>';
    }
                                
                                ?>
                            </td>

                            <td>
    <?php
    $checkmarksQuery = "SELECT marks_given FROM assignmentsubmissions WHERE student_id = :student_id AND AssignmentID = :assignment_id";
    $stmt = $pdo->prepare($checkmarksQuery);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->bindParam(':assignment_id', $assignment['AssignmentID'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if a result was found
    if ($result) {
        echo $result['marks_given'];
    } else {
        echo "Marks not given"; // or any default value or message
    }
    ?>
</td>
                    
                        </tr>
                        <?php $index++; // Increment the counter variable ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </section>
</div>




                                    <div class="animated-search-filter sysacad grid fadeInUp delay-1">


                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="adm display fadeInUp" style="display: none">
                            <h3 class="mt-4">Time Table</h3>
                            <div class="container">
                                <div class="row mb-5">
                                <!-- <div class="col-md-6">
                                        <label for="courseSearch">Search by Course Name:</label>
                                        <input type="text" class="form-control" id="courseSearch" placeholder="Enter course name">
                                    </div> -->
                                <div>
                                    <p></p>
                                    
                                            <!-- add your code here -->
                                            <?php
require_once('./config.php');
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$timeSlots = ['9:30 AM - 10:30 AM', '10:30 AM - 11:30 AM', '11:30 AM - 12:30 PM', '14:00 PM - 15:00 PM', '15:00 PM - 16:00 PM', '16:00 PM - 17:00 PM'];

echo '<div id="timetable-container" class="table-responsive timetable">';
echo '<table class="table table-bordered">';
echo '<thead><tr><th>Time</th><th>' . implode('</th><th>', $days) . '</th></tr></thead>';
echo '<tbody class="timetable-tbody">';

foreach ($timeSlots as $timeSlot) {
    echo '<tr>';
    echo '<td>' . $timeSlot . '</td>';

    foreach ($days as $day) {
        $query = "SELECT id, semester, day, start_time, end_time, course_name, room_no 
                  FROM student_timetable
                  WHERE day = '$day' 
                      AND start_time <= '$timeSlot' 
                      AND end_time > '$timeSlot'
                      AND semester = (SELECT batch FROM students WHERE student_id = $student_id)";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo '<td>';
            echo '' . $row['course_name'] . '<br>';
            // echo '( ' . $row['semester'] . ' )' . '<br>';
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
                                        <button onclick="generatePDF()" class="btn btn-primary" style="background-color: #e57639; border-color:#e57639">Download Timetable as PDF</button>
            
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
                        </div>

                        <!-- <div class="beneficios display fadeInUp" style="display: none">
                            <div class="container">
                                <div class="mb-5">
                                    <h3 class="my-4">Statistics</h3>
                                     add your code here 
                                </div>
                            </div>
                        </div>  Controle de Display -->

                        <div class="visoes display fadeInUp" style="display: none">
                            <h3 class="mt-4">extra section</h3>
                            <div class="container">
                                <div class="row mb-5">
                                    <p class="lead w-100"></p>
                                    <!-- add your code here -->

                                    <div class="animated-search-filter grid fadeInUp delay-1">

                                    </div>
                                </div>
                            </div>
                        </div> 

                        

                    </div> <!-- Fim Coluna 8 -->

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

                </div>
            </div>

            <!-- Assinatura de E-mail -->
            <div class="row assinatura display fadeInUp" style="display: none">
                <div class="col-md-12">

                </div>
            </div> <!-- Controle de Display -->

        </div>
        <!-- Texto do rodapé -->


    </div>
    <!-- /#page-content-wrapper -->

    <!-- iframe do sistema antigo -->
    <div class="d-none page-loader" id="page-content-frame">
        <div class="container-fluid fadeInUp delay-1 py-2">
            <div class="embed-responsive embed-responsive-4by3">
                <iframe id="iframe" class="embed-responsive-item" src=""></iframe>
            </div>
        </div>
    </div>

    </div>
    <!-- /#wrapper -->

    <!-- Modal ATALHOS -->
    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Adicionar atalhos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-short">
                    <div class="modal-body shortcuts" style="max-height: 30rem;">
                        <!-- Itens -->

                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Contabilidade</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Departamento Pessoal</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Banco de Horas</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Nota Fiscal de Serviço</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Pedido de Admissão</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Protocolo Geral</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Transporte</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Cobrança</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Departamento Financeiro</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Solicitação de Conta</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Solicitação de Serviço de Informática</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>
                        <div class="card border-0">
                            <a href="#" class="rounded-lg border-0 d-flex justify-content-center cards-short--disable">
                                <span class="py-4 px-3 text-center">Sindicâncias</span>
                            </a>
                            <span class="close-card transform-45"></span>
                        </div>

                    </div>
                    <div class="modal-footer" style="justify-content: flex-start;">
                        <a class="btn-sm button-link cyan-color mr-auto" data-dismiss="modal"><i class="lni-close pr-2"></i>Fechar</a>
                        <a class="btn-sm button-link blue-color submit"><i class="lni-check-mark-circle pr-2"></i>Adicionar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar comunicados -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Publish</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div aria-live="polite" aria-atomic="true" style="position: relative; min-height: 200px;">
                        <div class="toast" style="position: absolute; left: 0; right: 0;z-index: 10">
                            <div class="toast-header">
                                <strong class="mr-auto">Confirmar publicação</strong>
                                <small>Cancelar</small>
                                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="toast-body">
                                Tem certeza que deseja publicar este comunicado/notícia?<br />
                                <a class="btn-sm button-link blue-color float-right mb-2 submit confirm"><i class="lni-check-mark-circle pr-2"></i>Publicar</a>
                            </div>
                        </div>

                        <form>
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Título da mensagem</label>
                                <input type="text" class="form-control" id="recipient-name">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">Categoria</label>
                                </div>
                                <select class="custom-select" id="inputGroupSelect01">
                                    <option selected>Selecione...</option>
                                    <option value="1">Comunicados</option>
                                    <option value="2">Notícias</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="message-text" class="col-form-label">Comunicado (max 200
                                    carac.)</label>
                                <textarea class="form-control" id="message-text" rows="5"></textarea>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer" style="justify-content: flex-start;">
                        <a class="btn-sm button-link cyan-color mr-auto" data-dismiss="modal"><i class="lni lni-close pr-2"></i>Close</a>
                        <a class="btn-sm button-link blue-color confirm"><i class="lni lni-plus pr-2"></i>Continue</a>
                    </div>
                </div>
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
                    // tabs elems
                    this.tabs = [].slice.call(this.el.querySelectorAll("nav > ul > li"));
                    // content items
                    this.items = [].slice.call(
                        this.el.querySelectorAll(".content-wrap > section")
                    );
                    // current index
                    this.current = -1;
                    // show current content item
                    this._show();
                    // init events
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
                    // change current
                    this.current =
                        idx != undefined ?
                        idx :
                        this.options.start >= 0 && this.options.start < this.items.length ?
                        this.options.start :
                        0;
                    this.tabs[this.current].className = "tab-current";
                    this.items[this.current].className = "content-current";
                };

                // add to global namespace
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
                    //Se for diferente de target _blank ele abrirá em iframe

                    event
                        .preventDefault(); //cancela a ação padrão do click (cancela o redirecionamento a href)
                    var url = $(this).attr(
                        "href"); //pega o atributo href do card clicado e passa para a variavel URL
                    $(".loader, .lds-ring").fadeIn(); //inicia o loader
                    $("#iframe").attr("src", url); //insere a url correta para rodar no iframe

                    $("#page-content-wrapper, .page-loader").toggleClass(
                        "d-none"); //para o iframe ser exibido o content principal deve ser ocultado

                    /* Manipula o iframe para aplicar correções no estilo da intranet antiga
                     ** Oculta os menus de topo, entre outros itens da antiga intranet */
                    $("#iframe").on("load", function() {
                        $("#iframe")
                            .contents()
                            .find("head")
                            .append(
                                "<style>#pc_user { display: none;} #pc_sair { display: none;} #pc_fundomenu { display: none;}#pc_busca { display: none;} #PC_brilho { display: none !important; } #pc_centro { position: inherit !important; }</style>"
                            );

                        $(".loader, .lds-ring").fadeOut(); //encerra o loader
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
                    .parent(); //verifica todos os itens com a classe e pega o elemento completo
                $(appendItems).removeClass("cards-short--disable");
                $(".block.shortcuts").append(appendItems); //insere o(os) item(ns) nos atalhos
            });

            $(".remove").click(function() {
                var returnItems = $(".block.shortcuts")
                    .find(".cards-short--disable")
                    .parent(); //verifica todos os itens com a classe e pega o elemento completo
                $(".modal-body.shortcuts").append(returnItems); //insere o(os) item(ns) no modal
            });

            function toggleDarkLight() {
                var body = document.getElementById("page-content-wrapper");
                var frame = document.getElementById("page-content-frame");
                if ($(body).hasClass("dark-mode")) {
                    //a condição verifica se no elemento content principal existe a classe "dark-mode"
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
                }, 500); //Scroll top para suavizar a troca de tela
                $(this).addClass("active");

                //Alterna a exibição de telas

                //Array de classes verificadas na Sidenav
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