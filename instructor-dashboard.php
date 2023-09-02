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
                        <a class="nav-link dropdown-toggle" style="white-space: normal" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="lni lni-user border rounded-circle border-primary p-1 mr-1"></i> Options<span class="sr-only">(current)</span>
                        </a>
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
                <!-- <a class="d-flex align-items-center border-bottom p-3 text-secondary acad" id="acad"><i class="lni lni-graduation size-sm pr-4 font-24"></i></i>Assign tasks</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary adm" id="adm"><i class="lni lni-briefcase size-sm pr-4 font-24"></i>Time Table</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary beneficios"><i
                        class="lni lni-handshake size-sm pr-4 font-24"></i>Statistics</a>
                <a class="d-flex align-items-center border-bottom p-3 text-secondary visoes"
                    style="white-space: nowrap;"><i class="lni lni-files size-sm pr-4 font-24"></i>extra sect</a> -->
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
                                                <h4 class="text-primary pt-3 pt-sm-5 pl-3 pl-lg-4 pr-3">Hi, welcome</h4>
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
                                <div class="row my-4 fadeInUp delay-1">
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
                                </div>
                                <script>
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
                                </script>
        
</body>
</html>