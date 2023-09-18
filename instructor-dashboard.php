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

    $connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

    if ($connection) {
        $query = "INSERT INTO courses (course_name, course_credits, course_description, instructor_id,course_semester) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "sisii", $courseName, $courseCredits, $courseDescription, $_SESSION['user_id'],$courseSemester);
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
    $query = "SELECT courses.course_name, instructors.name AS instructor_name, courses.course_credits
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
    <link rel="stylesheet" href="https://cdn.lineicons.com/4.0/lineicons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <title>Instructor Dashboard</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg align-items-start">
        <button class="navbar-toggler" type="button" id="menu-toggle">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="col-4 col-sm-3">
            <img src="https://www.logolynx.com/images/logolynx/0d/0d423bda04a7a684609b06cd46bb699b.png" class="img-fluid logo ml-sm-4" style="opacity: .5;width:100px;" />
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
                        <img src="<?php echo $user_photo; ?>" class="rounded-circle" alt="Instructor Photo" style="width: 40px; height: 40px; object-fit: cover; cursor:pointer;">
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
                <a class="d-flex align-items-center border-bottom p-3 text-secondary visoes" style="white-space: nowrap;"><i class="lni lni-files size-sm pr-4 font-24"></i>extra sect</a>
            </div>
        </div>
        <div id="page-content-wrapper">

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
                                                <h4 class="text-primary pt-3 pt-sm-5 pl-3 pl-lg-4 pr-3">Hi <?php echo $user_name; ?>, welcome</h4>
                                            </div>
                                            <div class="col-sm-6 d-flex d-lg-block d-lg-block align-items-center justify-content-center order-0 order-sm-1">

                                            </div>
                                            <div class="col-12 order-2 order-sm-1">
                                                <p class="px-3 pt-2 pb-3 text-banner" style="color: #5584bc; font-size: 15px">Lorem ipsum dolor sit amet,
                                                    consectetur adipiscing elit. Morbi sed metus quis mauris tempor
                                                    lacinia. Etiam maximus arcu a erat dapibus tempus eget et justo. Nam
                                                    eget iaculis arcu, eu aliquam risus.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container mt-4">
                                    <?php if (!empty($courses)) : ?>
                                        <h2>Your Courses</h2>
                                        <div class="row">
                                            <?php foreach ($courses as $course) : ?>
                                                <div class="col-md-3">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?php echo $course['course_name']; ?></h5>
                                                            <p class="card-text">Credits: <?php echo $course['course_credits']; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="container mt-4 col-md-6" style="margin-left:0px;">
                                    <h2>Create a Course</h2>
                                    <form method="POST">
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
                                <div class="row mb-5">
                                    <p class="lead w-100">Options panel</p>
                                    <!-- add your code here -->


                                    <div class="animated-search-filter sysacad grid fadeInUp delay-1">


                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="adm display fadeInUp" style="display: none">
                            <h3 class="mt-4">Time Table</h3>
                            <div class="container">
                                <div class="row mb-5">
                                    <p class="lead w-100"></p>
                                    <div class="animated-search-filter adm grid fadeInUp delay-1">
                                        <!-- add your code here -->

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="beneficios display fadeInUp" style="display: none">
                            <div class="container">
                                <div class="mb-5">
                                    <h3 class="my-4">Statistics</h3>
                                    <!-- add your code here -->
                                </div>
                            </div>
                        </div>

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
                                        <p class="text-center mb-3">Calender</p>
                                    </div>
                                </div>

                                <div class="card shadow-card rounded-lg border-0 px-3 pb-4 mb-4">
                                    <p class="text-center mb-0 mt-3">Extra things</p>


                                </div>

                            </div>

                            <div class="row mb-5 mt-2 fadeInUp delay-2">
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