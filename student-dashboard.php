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

    <title>Student Dashboard</title>
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
            <!-- Pesquisa responsiva -->
            <div class="input-group m-2 d-none d-md-flex">
                <input type="search" class="form-control animated-search-filter search" id="pesquisageral" name="pesquisageral" placeholder="search infos and modules" aria-label="Pesquise" aria-describedby="button-addon2">
            </div>

        </div>

        <div class="col-4">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto mt-md-2">
                    <li class="nav-item pl-4 dropdown">
                        <a class="nav-link dropdown-toggle" style="white-space: normal" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="lni lni-user border rounded-circle border-primary p-1 mr-1"></i> Options<span class="sr-only">(current)</span>
                        </a>

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
                <a class="d-flex align-items-center border-bottom p-3 text-secondary adm" id="adm"><i class="lni lni-briefcase size-sm pr-4 font-24"></i>To Do Tasks</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary beneficios"><i
                        class="lni lni-handshake size-sm pr-4 font-24"></i>Statistics</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary visoes"
                    style="white-space: nowrap;"><i class="lni lni-files size-sm pr-4 font-24"></i>extra sect</a>
                
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
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
                                                <h4 class="text-primary pt-3 pt-sm-5 pl-3 pl-lg-4 pr-3">
                                                Hi <?php echo $user_name; ?>, welcome</h4>
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
                                <!-- <div class="row my-4 fadeInUp delay-1">
                                    <div id="card-carousel" class="carousel slide w-100" data-ride="carousel">
                                        <div class="carousel-inner">

                                        </div>

                                        <a class="carousel-control-prev" href="#card-carousel" role="button" data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="carousel-control-next" href="#card-carousel" role="button" data-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                    </div>
                                </div> -->
                                <!-- <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const cardData = [{
                                                header: "Setup Guides",
                                                content: "Our step-by-step guides help you configure the features you need."
                                            },
                                            {
                                                header: "Setup Guides",
                                                content: "Our step-by-step guides help you configure the features you need."
                                            },
                                            {
                                                header: "Setup Guides",
                                                content: "Our step-by-step guides help you configure the features you need."
                                            },
                                            {
                                                header: "Setup Guides",
                                                content: "Our step-by-step guides help you configure the features you need."
                                            },
                                            {
                                                header: "Setup Guides",
                                                content: "Our step-by-step guides help you configure the features you need."
                                            },
                                            {
                                                header: "Setup Guides",
                                                content: "Our step-by-step guides help you configure the features you need."
                                            },

                                        ];

                                        const carouselInner = document.querySelector(".carousel-inner");
                                        const itemsPerSlide = 3;

                                        for (let i = 0; i < cardData.length; i += itemsPerSlide) {
                                            const item = document.createElement("div");
                                            item.classList.add("carousel-item");
                                            if (i === 0) {
                                                item.classList.add("active");
                                            }

                                            const cardsInSlide = cardData.slice(i, i + itemsPerSlide);
                                            const cardsHTML = cardsInSlide.map(card => `
                <div class="col-md-4">
                    <div class="card card_course">
                        <h2 class="headerc">${card.header}</h2>
                        <p class="contentc">${card.content}</p>
                    </div>
                </div>`).join('');

                                            item.innerHTML = `<div class="row">${cardsHTML}</div>`;
                                            carouselInner.appendChild(item);
                                        }
                                    });
                                </script> -->
                            <div class="slide-container swiper">
                                <div class="slide-content">
                                    <div class="card-wrapper swiper-wrapper">
                                        <div class="card swiper-slide">
                                        <div class="card-content">
                                <h2 class="name">DSA</h2>
                                <p class="description">data structure and algorithms</p>
                            </div>

                            
                                            
                                </div>

                                <div class="card swiper-slide">
                                        <div class="card-content">
                                <h2 class="name">DSA</h2>
                                <p class="description">data structure and algorithms</p>
                            </div>

                            
                                            
                                </div>

                                <div class="card swiper-slide">
                                        <div class="card-content">
                                <h2 class="name">DSA</h2>
                                <p class="description">data structure and algorithms</p>
                            </div>

                            
                                            
                                </div>

                                <div class="card swiper-slide">
                                        <div class="card-content">
                                <h2 class="name">DSA</h2>
                                <p class="description">data structure and algorithms</p>
                            </div>

                            
                                            
                                </div>

                                <div class="card swiper-slide">
                                        <div class="card-content">
                                <h2 class="name">DSA</h2>
                                <p class="description">data structure and algorithms</p>
                            </div>

                            
                                            
                                </div>
                                    </div>

                            
                                </div>
                                <div class="swiper-button-next swiper-navBtn"></div>
                                <div class="swiper-button-prev swiper-navBtn"></div>
                                <div class="swiper-pagination"></div>
                            </div>
                            <script src="./assets/swiper-bundle.min.js"></script>
                            <script >
                                var swiper = new Swiper(".slide-content", {
    slidesPerView: 3,
    spaceBetween: 25,
    
    loop: true,
    centerSlide:'true',
    fade:'true',
    grabCursor:'true',
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

    // breakpoints{
    //     0: {
    //         slidesPerView: 1,
    //     },
    //     520: {
    //         slidesPerView: 2,
    //     },
    //     950: {
    //         slidesPerView: 3,
    //     }
    // }
  });
                            </script>
                            



                                <!-- <div class="row mb-5 mt-2 fadeInUp delay-1">
                                    <div class="col-md-12 mt-4 mt-md-0">
                                        <div class="card shadow-card rounded-lg border-0 d-flex align-items-center justify-content-center p-4 fadeInUp">
                                            <p class="text-center mb-3">Assignment list table Below</p>

                                        </div>
                                    </div>
                                </div> -->
                                <div class="table">
                                    <!-- <section class="table_header">
                                        <h1>Asiignment Table</h1> 
                                        </section> -->
                                    <section class="table_body">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Sl No.</th>
                                                    <th>Course Name</th>
                                                    <th>Topic Name</th>
                                                    <th>Marks</th>
                                                    <th>Deadline</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>1</th>
                                                    <th>DSA</th>
                                                    <th>Trees</th>
                                                    <th>10</th>
                                                    <th><strong>15 aug</strong></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                
                                                
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
                                    <p class="lead w-100">Options panel</p>
                                    <!-- add your code here -->


                                    <div class="animated-search-filter sysacad grid fadeInUp delay-1">


                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="adm display fadeInUp" style="display: none">
                            <h3 class="mt-4">To Do tasks</h3>
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