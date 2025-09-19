<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <title><?= $this->renderSection('title') ?? 'Sistema de Cursos' ?></title>
    
    <!-- Fonts [ OPTIONAL ] -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS [ REQUIRED ] -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">

    <!-- Nifty CSS [ REQUIRED ] -->
    <link rel="stylesheet" href="<?= base_url('assets/css/nifty.min.css'); ?>">

    <!-- Nifty Demo Icons [ OPTIONAL ] -->
    <link rel="stylesheet" href="<?= base_url('assets/css/demo-purpose/demo-icons.min.css'); ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/custom.css'); ?>">

    <!-- Datatable -->
    <link rel="stylesheet" href="<?= base_url('personalice/css/dataTables.bootstrap5.css'); ?>">
    
    <?= $this->renderSection('page_css') ?>
</head>

<body class="jumping">
    
    <!-- PAGE CONTAINER -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <div id="root" class="root mn--max hd--expanded">
        
        <!-- HEADER -->
        <header class="header">
            <div class="header__inner">

                <!-- Brand -->
                <div class="header__brand">
                    <div class="brand-wrap">
                        <!-- Brand title -->
                        <div class="brand-title"></div>
                    </div>
                </div>
                <!-- End - Brand -->

                <div class="header__content">
                    
                    <!-- Content Header - Left Side: Navigation Toggler -->
                    <div class="header__content">
                        <button type="button" class="nav-toggler header__btn btn btn-icon btn-sm" aria-label="Nav Toggler">
                            <i class="demo-psi-view-list"></i>
                        </button>
                    </div>
                    <!-- End - Content Header - Left Side -->

                    <!-- Content Header - Center: Title -->
                    <div class="header__content text-center">
                        <div>
                            <h3 class="mb-0 me-3 text-white">INTELIGENCIA ARTIFICIAL APLICADO A LA INVESTIGACION CIENTIFICA</h3>
                        </div>
                    </div>
                    <!-- End - Content Header - Center -->

                </div>
                
            </div>
        </header>
        <!-- END - HEADER -->

        <!-- NAVIGATION -->
        <nav id="mainnav-container" class="mainnav">
            <div class="mainnav__inner">
                
                <!-- Navigation menu -->
                <div class="mainnav__top-content scrollable-content pb-5">
                    
                    <!-- Profile Widget -->
                    <div class="mainnav__profile mt-3 d-flex3">
                        <div class="mt-2 d-mn-max"></div>
                        <!--<div class="mininav-toggle text-center py-2">
                            <img class="mainnav__avatar img-md" src="<?= base_url('assets/img/profile-photos/admin.png'); ?>" alt="Profile Picture">
                        </div>-->
                        <div class="mininav-content collapse d-mn-max">
                            <div class="d-grid">
                                <span class="d-flex justify-content-center align-items-center">
                                    <h4 class="mb-0 me-3 text-center"><?= esc(session('nombres')) ?></h4>
                                </span>
                                <!--<span class="badge bg-success mx-auto"><?= esc(ucfirst(session('rol_nombre'))) ?></span>-->
                            </div>
                        </div>
                    </div>
                    <!-- End - Profile widget -->

                    <!-- Navigation Category -->
                    <div class="mainnav__categoriy py-3">
                        <h6 class="mainnav__caption mt-0 px-3 fw-bold">Navegación</h6>
                        <ul class="mainnav__menu nav flex-column">
                            <?php
                            if (session('rol_nombre') === 'usuario') {
                                $panel_url = site_url('mi-panel');
                                $panel_label = 'Mi Panel';
                            } else {
                                $panel_url = site_url('dashboard');
                                $panel_label = 'Dashboard';
                            }
                            ?>
                            <li class="nav-item">
                                <a href="<?= $panel_url ?>" class="nav-link mininav-toggle">
                                    <i class="demo-pli-home fs-5 me-2"></i>
                                    <span class="nav-label mininav-content ms-1"><?= $panel_label ?></span>
                                </a>
                            </li>
                            
                            <?php if (session('rol_nombre') === 'admin' || session('rol_nombre') === 'superadmin'): ?>
                                <li class="nav-item">
                                    <a href="<?= site_url('cursos') ?>" class="nav-link mininav-toggle">
                                        <i class="demo-pli-data-matrix fs-5 me-2"></i>
                                        <span class="nav-label mininav-content ms-1">Gestionar Cursos</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= site_url('reportes/asistencias') ?>" class="nav-link mininav-toggle">
                                        <i class="demo-pli-bar-chart fs-5 me-2"></i>
                                        <span class="nav-label mininav-content ms-1">Reportes</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= site_url('pagos') ?>" class="nav-link mininav-toggle">
                                        <i class="demo-pli-credit-card-2"></i> 
                                        <span class="nav-label mininav-content ms-1">Gestión de Pagos</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if (session('rol_nombre') === 'superadmin'): ?>
                                <li class="nav-item">
                                    <a href="<?= site_url('usuarios') ?>" class="nav-link mininav-toggle">
                                        <i class="demo-pli-male-female fs-5 me-2"></i>
                                        <span class="nav-label mininav-content ms-1">Gestionar Usuarios</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if (session('rol_nombre') === 'usuario'): ?>
                                <li class="nav-item">
                                    <a href="<?= site_url('participante/historial') ?>" class="nav-link mininav-toggle">
                                        <i class="demo-pli-pen-5 fs-5 me-2"></i>
                                        <span class="nav-label mininav-content ms-1">Mis Asistencias</span>
                                    </a>
                                </li>
                                <!--<li class="nav-item">
                                    <a href="<?= site_url('participante/mis-cursos') ?>" class="nav-link mininav-toggle">
                                        <i class="demo-pli-book fs-5 me-2"></i>
                                        <span class="nav-label mininav-content ms-1">Mis cursos</span>
                                    </a>
                                </li>-->
                                <li class="nav-item">
                                    <a href="<?= site_url('participante/mis-pagos') ?>" class="nav-link mininav-toggle">
                                        <i class="demo-pli-credit-card-2"></i> 
                                        <span class="nav-label mininav-content ms-1">&nbsp;&nbsp;Mis pagos</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <!-- End - Navigation Category -->

                </div>
                <!-- End - Navigation menu -->

                <!-- Navigation footer -->
                <div class="mainnav__bottom-content border-top pb-2">
                    <ul id="mainnav" class="mainnav__menu nav flex-column">
                        <?php if (session('rol_nombre') === 'usuario'): ?>
                            <li class="nav-item">
                                <a href="<?= site_url('logout'); ?>" class="nav-link mininav-toggle collapsed">
                                    <i class="demo-pli-unlock fs-5 me-2"></i>
                                    <span class="nav-label ms-1">Salir</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (session('rol_nombre') === 'admin' || session('rol_nombre') === 'superadmin'): ?>
                            <li class="nav-item">
                                <a href="<?= site_url('auth/logout'); ?>" class="nav-link mininav-toggle collapsed">
                                    <i class="demo-pli-unlock fs-5 me-2"></i>
                                    <span class="nav-label ms-1">Salir</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!-- End - Navigation footer -->

            </div>
        </nav>
        <!-- END - NAVIGATION -->

        <!-- MAIN CONTENT -->
        <!-- Aquí es donde va el contenido específico de cada página -->
        <?= $this->renderSection('content') ?>
        <!-- END - MAIN CONTENT -->

    </div>
    <!-- END - PAGE CONTAINER -->
    
    <!-- SCROLL TO TOP -->
    <div class="scroll-container">
        <a href="#root" class="scroll-page rounded-circle ratio ratio-1x1" aria-label="Scroll button"></a>
    </div>
    <!-- END - SCROLL TO TOP -->
    
    <!-- JAVASCRIPTS -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    
    <!-- Jquery [ REQUIRED ] -->
    <script src="<?= base_url('personalice/js/frameworks/jquery-3.7.1.min.js'); ?>"></script>
    
    <!-- DataTables [ OPTIONAL ] -->
    <script src="<?= base_url('personalice/js/frameworks/dataTables.js'); ?>"></script>
    <script src="<?= base_url('personalice/js/frameworks/dataTables.bootstrap5.js'); ?>"></script>
    
    <!-- Popper JS [ OPTIONAL ] -->
    <script src="<?= base_url('assets/vendors/popperjs/popper.min.js'); ?>" defer></script>

    <!-- Bootstrap JS [ OPTIONAL ] -->
    <script src="<?= base_url('assets/vendors/bootstrap/bootstrap.min.js'); ?>" defer></script>

    <!-- Nifty JS [ REQUIRED ] -->
    <script src="<?= base_url('assets/js/nifty.js'); ?>" defer></script>
    
    <?= $this->renderSection('page_js') ?>
    
</body>
</html>