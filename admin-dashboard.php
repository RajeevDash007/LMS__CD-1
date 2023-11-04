<?php
session_start();


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: auth.php');
    exit();
}
require_once('config.php');
$connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if ($connection) {
    $query = "SELECT name, photo FROM administrators WHERE email=?";
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
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $instructorId = $_POST["instructor_id"];
//     $day = $_POST["day"];
//     $startTime = $_POST["start_time"];
//     $endTime = $_POST["end_time"];
//     $courseId = $_POST["course_id"];
//     $semester = $_POST["semester"];
//     $roomNo = $_POST["room_no"];

//     $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

//     if ($connection) {
//         $query = "INSERT INTO classes (instructor_id, day, start_time, end_time, course_id, semester, room_no)
//                   VALUES (?, ?, ?, ?, ?, ?, ?)";
//         $stmt = mysqli_prepare($connection, $query);
//         mysqli_stmt_bind_param($stmt, "isssiss", $instructorId, $day, $startTime, $endTime, $courseId, $semester, $roomNo);

//         if (mysqli_stmt_execute($stmt)) {
//             mysqli_stmt_close($stmt);
//             mysqli_close($connection);
//             header("Location: admin-dashboard.php"); // Redirect to admin dashboard after successful insertion
//             exit();
//         } else {
//             echo "Error: " . mysqli_error($connection); // Handle insertion error
//         }
//     } else {
//         echo "Database connection error.";
//     }
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/dashboard.css">
    <link rel="stylesheet" href="./assets/stu-cards.css">
    <link rel="stylesheet" href="https://cdn.lineicons.com/4.0/lineicons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <link rel="stylesheet" href="./assets/swiper-bundle.min.css">
    <title>Admin Dashboard</title>
    <style>
    #fetchCoursesButton {
  background-color: #007bff;
  border-radius: 8px;
  border-style: none;
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
  transition: color 100ms;
  vertical-align: baseline;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
}

#fetchTimetableButton{
    background-color: #007bff;
  border-radius: 8px;
  border-style: none;
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
  transition: color 100ms;
  vertical-align: baseline;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;

}

#fetchTimetableButton:hover,
#fetchTimetableButton:focus {
  background-color: #7199ce;
}

#fetchCoursesButton:hover,
#fetchCoursesButton:focus {
  background-color: #7199ce;
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

        /* Dark mode styles */
.dark-mode .timetable {
  background-color: #333333; /* Background color for dark mode */
  color: #FFFFFF; /* Default text color for dark mode */

}

.dark-mode .timetable tr:nth-child(odd) {
  background-color: #444444; /* Background color for even rows in dark mode */
  color: #FFFFFF; /* Text color for even rows in dark mode */
}

</style>
</head>

<body>
    <nav class="navbar navbar-expand-lg align-items-start">
        <button class="navbar-toggler" type="button" id="menu-toggle">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="col-4 col-sm-3">
        <h4 style="margin-top: 10px;color:#666;">Admin Dashboard</h1>
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
                        <img src="<?php echo $user_photo; ?>" class="rounded-circle" alt="Admin Photo" style="width: 40px; height: 40px; object-fit: cover; cursor:pointer;" data-toggle="dropdown">
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
                <a class="d-flex align-items-center border-bottom p-3 text-secondary acad" id="acad"><i class="lni lni-graduation size-sm pr-4 font-24"></i></i>Instructor Info</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary adm" id="adm"><i class="lni lni-briefcase size-sm pr-4 font-24"></i>Student Info</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary beneficios"><i
                        class="lni lni-handshake size-sm pr-4 font-24"></i>Time Table</a>
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
                            <form id="profileForm" method="POST" action="./assets/admin-update-profile.php">
                                <div class="m-b-25" style="margin-left: 330px;" >
                                    <img src="<?php echo $user_photo; ?>" alt="User-Profile-Image" style="width: 100px; height: auto; border-radius: 50%;">
                                </div>
                                <div class="form-group">
                                    <label for="instructorName">Name</label>
                                    <input type="text" class="form-control" id="adminName" name="admin_name" value="<?php echo $user_name; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="instructorPhoto">Photo</label>
                                    <input type="text" class="form-control-file" id="adminPhoto" name="admin_photo" value="<?php echo $user_photo; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                            <div class="modal-header" style="border-bottom: 1px solid #000; margin-top:20px;margin-bottom:20px;">
                                <h5 class="modal-title" id="profileModalLabel" style="color: red;">Change your password</h5>
                            </div>
                            <form id="changePasswordForm" method="POST" action="./assets/admin-change-password.php">
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
                                                Hi <?php echo $user_name; ?> , welcome</h4>
                                            </div>
                                            <div class="col-sm-6 d-flex d-lg-block d-lg-block align-items-center justify-content-center order-0 order-sm-1">

                                            </div>
                                            <div class="col-12 order-2 order-sm-1">
                                                <p class="px-3 pt-2 pb-3 text-banner" style="color: #5584bc; font-size: 15px">Welcome to the Admin Dashboard! You have successfully logged in as an admin. Feel free to manage and control the system efficiently."</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                


                                
                            </div>
                        </div>

                        <div class="acad display fadeInUp" style="display: none">
                            <h3 class="mt-4">Instructor Time Table input</h3>
                            <div class="container">
                                <div class="row mb-5">
                                    <p class="lead w-100"></p>
                                    <!-- add your code here -->
                                    <form id="timetableForm" method="POST" class="col-md-8">
                                        <div class="form-group">
                                            <label for="instructorId">Instructor:</label>
                                            <select class="form-control" name="instructor_id" id="instructorId">
                                                <option value="">Select Instructor</option>
                                                <?php
                                                include_once('./config.php');
                                                $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
                                                if ($connection) {
                                                    $query = "SELECT instructor_id, name FROM instructors";
                                                    $result = mysqli_query($connection, $query);
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo "<option value=\"{$row['instructor_id']}\">{$row['name']}</option>";
                                                    }
                                                    mysqli_close($connection);
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="day">Day:</label>
                                            <select class="form-control" name="day" id="day">
                                                <option value="Monday">Monday</option>
                                                <option value="Tuesday">Tuesday</option>
                                                <option value="Wednesday">Wednesday</option>
                                                <option value="Thursday">Thursday</option>
                                                <option value="Friday">Friday</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="startTime">Start Time:</label>
                                            <input type="time" class="form-control" name="start_time" id="startTime" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="endTime">End Time:</label>
                                            <input type="time" class="form-control" name="end_time" id="endTime" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="courseId">Course:</label>
                                            <select class="form-control" name="course_id" id="courseId">
                                                <option value="">Select Course</option>
                                                <?php
                                                include_once('./config.php');
                                                $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
                                                if ($connection) {
                                                    $query = "SELECT course_id, course_name FROM courses";
                                                    $result = mysqli_query($connection, $query);
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo "<option value=\"{$row['course_id']}\">{$row['course_name']}</option>";
                                                    }
                                                    mysqli_close($connection);
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="semester">Semester:</label>
                                            <input type="text" class="form-control" name="semester" id="semester" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="roomNo">Room Number:</label>
                                            <input type="text" class="form-control" name="room_no" id="roomNo" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Add Timetable Entry</button>
                                    </form>






                                    <div class="animated-search-filter sysacad grid fadeInUp delay-1">


                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="adm display fadeInUp" style="display: none">
                            <h3 class="mt-4">Student Time Table input</h3>
                            <h6 class="mt-2">{Select a semester to view courses and add them to the time-table}</h6>
                            <div class="container">
                                <div class="row mb-5">  
                                <p class="lead w-100"></p>             
                                        <!-- add your code here -->
                                        <div class="student-course-selection">
                                    <label for="semesterSelect">Select Semester:</label>
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
                        <a class="description" href="${course.course_outline}" target="_blank" title="Course Outline: ${course.course_name}">
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
                            
                                         <form id="studentForm" method="POST" class="col-md-8">
                                        <div class="form-group">
                                            <label for="">Semester:</label>
                                            <input type="text" class="form-control" name="semesternum" id="semesternum">
                                        </div>   
                                        <div class="form-group">
                                            <label for="day">Day:</label>
                                            <select class="form-control" name="day" id="stu_day">
                                                <option value="Monday">Monday</option>
                                                <option value="Tuesday">Tuesday</option>
                                                <option value="Wednesday">Wednesday</option>
                                                <option value="Thursday">Thursday</option>
                                                <option value="Friday">Friday</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="startTime">Start Time:</label>
                                            <input type="time" class="form-control" name="start_time" id="stu_startTime" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="endTime">End Time:</label>
                                            <input type="time" class="form-control" name="end_time" id="stu_endTime" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Course:</label>
                                            <input type="text" class="form-control" name="coursename" id="coursename">
                                        </div>
                                        <div class="form-group">
                                            <label for="roomNo">Room Number:</label>
                                            <input type="text" class="form-control" name="room_no" id="stu_roomNo" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Add Timetable Entry</button>
                                        
                                    </form> 
                                          
                                    
                                    <div class="animated-search-filter adm grid fadeInUp delay-1">
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                         <div class="beneficios display fadeInUp" style="display: none">
                            <div class="container">
                                <div class="mb-5">
                                    <h3 class="my-4">Time Table</h3>
                                    <!-- add your code here -->
                                    <div id="timetable-container" class="table-responsive timetable" style=" margin-bottom:50px;">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th colspan="6">Instructor's Time Table</th>
      </tr>
      <tr>
        <th>Time</th>
        <th>Monday</th>
        <th>Tuesday</th>
        <th>Wednesday</th>
        <th>Thursday</th>
        <th>Friday</th>
      </tr>
    </thead>
    <tbody class="timetable-tbody">
      <tr>
        <td>9:30 AM - 10:30 AM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>10:30 AM - 11:30 AM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>11:30 AM - 12:30 PM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>2:00 PM - 3:00 PM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>3:00 PM - 4:00 PM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>4:00 PM - 5:00 PM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </tbody>
  </table>
</div>


<div class="student-course-selection">
                                    <label for="semesterSelect">Select Semester:</label>
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
                                <button id ="fetchTimetableButton" style="margin-bottom:30px">Fetch Time-Table</button>
                                </div>



<div id="timetable-container" class="table-responsive timetable">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th colspan="6">Student's Time Table</th>
      </tr>
      <tr>
        <th>Time</th>
        <th>Monday</th>
        <th>Tuesday</th>
        <th>Wednesday</th>
        <th>Thursday</th>
        <th>Friday</th>
      </tr>
    </thead>
    <tbody class="timetable-tbody_stu">
      <tr>
        <td>9:30 AM - 10:30 AM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>10:30 AM - 11:30 AM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>11:30 AM - 12:30 PM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>2:00 PM - 3:00 PM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>3:00 PM - 4:00 PM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>4:00 PM - 5:00 PM</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </tbody>
  </table>
</div>
<button type="submit" class="btn btn-primary" id="submitTimetableButton">Add the time-table</button>


                                </div>
                            </div>
                        </div> <!-- Controle de Display -->

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