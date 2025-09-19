<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
    Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- MAIN CONTENT SECTION usando la estructura de Nifty -->
<section id="content" class="content">
    
    <!-- Content Header -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <h1 class="h1">Dashboard</h1>
            <p class="text-muted">Bienvenido de nuevo, <?= esc(session('nombres')) ?>.</p>
        </div>
    </div>
    <!-- END - Content Header -->

    <!-- Content Body -->
    <div class="content__boxed">
        <div class="content__wrap">

            <?php if (session('rol_nombre') === 'superadmin'): ?>
                <!-- Stats Cards Row -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-xl-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="display-1 text-primary fw-bold"><?= esc($total_usuarios ?? 0) ?></div>
                                <p class="h5 mt-3 mb-2">Usuarios Totales</p>
                                <small class="text-muted">Todos los roles en el sistema.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-xl-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="display-1 text-success fw-bold"><?= esc($total_cursos_activos ?? 0) ?></div>
                                <p class="h5 mt-3 mb-2">Cursos Activos</p>
                                <small class="text-muted">Total de cursos activos en el sistema.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END - Stats Cards Row -->
            <?php endif; ?>

            <?php if (session('rol_nombre') === 'admin'): ?>
                <!-- Admin Stats Cards Row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card text-white h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);">
                            <div class="card-body text-center p-4">
                                <div class="display-4 fw-bold"><?= esc($total_cursos_asignados ?? 0) ?></div>
                                <p class="h5 mt-3 mb-2">Mis Cursos Asignados</p>
                                <small class="opacity-75">Total de cursos activos que tienes a tu cargo.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-3">
                                    <i class="demo-pli-rocket me-2 text-primary"></i>
                                    Acceso Rápido
                                </h5>
                                <p class="card-text text-muted mb-4">Navega directamente a la gestión de módulos de tus cursos.</p>
                                <a href="<?= site_url('cursos') ?>" class="btn btn-primary">
                                    <i class="demo-pli-data-matrix me-2"></i>
                                    Gestionar mis cursos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END - Admin Stats Cards Row -->

                <!-- Course List Card -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h4 class="card-title mb-0">
                                    <i class="demo-pli-list-view me-2 text-primary"></i>
                                    Listado de Mis Cursos Activos
                                </h4>
                            </div>
                            <div class="card-body">
                                <?php if (empty($cursos_asignados)): ?>
                                    <div class="text-center py-4">
                                        <i class="demo-pli-information display-4 text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No tienes cursos activos asignados en este momento.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($cursos_asignados as $curso): ?>
                                            <a href="<?= site_url('cursos/' . $curso['id'] . '/modulos') ?>" 
                                               class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="demo-pli-book text-primary me-2"></i>
                                                        <strong class="text-dark"><?= esc($curso['nombre']) ?></strong>
                                                    </div>
                                                    <small class="text-muted"><?= esc($curso['descripcion']) ?></small>
                                                </div>
                                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                                    <i class="demo-pli-gear me-1"></i>
                                                    Gestionar Módulos
                                                </span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END - Course List Card -->
            <?php endif; ?>

        </div>
    </div>
    <!-- END - Content Body -->
    
</section>
<!-- END - MAIN CONTENT SECTION -->

<?= $this->endSection() ?>