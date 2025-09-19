<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Mis Cursos<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= site_url('participante/dashboard') ?>">Mi Panel</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mis Cursos</li>
                </ol>
            </nav>
            <h1 class="h1">Mis Cursos</h1>
            <h3 class="text-muted mb-0">
                Aquí puedes ver todos los cursos en los que estás inscrito y tu progreso de asistencia.
            </h3>
            <br>
        </div>
    </div>
    <!-- /HEADER -->

    <div class="content__boxed">
        <div class="content__wrap">

            <!-- ALERTAS -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="demo-pli-check-circle-2 me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="demo-pli-exclamation-triangle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <!-- /ALERTAS -->

            <?php if (empty($mis_cursos)): ?>
                <!-- NO HAY CURSOS -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="display-1 text-muted mb-3">
                            <i class="demo-pli-book"></i>
                        </div>
                        <h4 class="text-muted mb-3">No estás inscrito en ningún curso</h4>
                        <p class="text-muted mb-4">
                            Cuando te inscriban en cursos, aparecerán aquí con toda la información sobre tu progreso y asistencias.
                        </p>
                        <a href="<?= site_url('participante/dashboard') ?>" class="btn btn-primary">
                            <i class="demo-pli-home me-1"></i> Volver al Panel
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- LISTA DE CURSOS -->
                <div class="row">
                    <?php foreach ($mis_cursos as $curso): ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white py-3 border-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1"><?= esc($curso['nombre']) ?></h5>
                                            <div class="d-flex align-items-center">
                                                <?php if ($curso['estado'] == 1): ?>
                                                    <span class="badge bg-success me-2">
                                                        <i class="demo-pli-check me-1"></i>Activo
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary me-2">
                                                        <i class="demo-pli-pause me-1"></i>Inactivo
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php
                                                $porcentaje = $curso['estadisticas']['porcentaje'];
                                                $badge_class = 'bg-danger';
                                                if ($porcentaje >= 80) $badge_class = 'bg-success';
                                                elseif ($porcentaje >= 60) $badge_class = 'bg-warning';
                                                ?>
                                                <span class="badge <?= $badge_class ?>">
                                                    <?= $porcentaje ?>% Asistencia
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body pt-0">
                                    <?php if (!empty($curso['descripcion'])): ?>
                                        <p class="text-muted small mb-3"><?= esc(substr($curso['descripcion'], 0, 120)) ?><?= strlen($curso['descripcion']) > 120 ? '...' : '' ?></p>
                                    <?php endif; ?>

                                    <!-- FECHAS DEL CURSO -->
                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <small class="text-muted d-block">Inicio</small>
                                                <strong class="text-primary">
                                                    <?= date('d/m/Y', strtotime($curso['fecha_inicio'])) ?>
                                                </strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Fin</small>
                                            <strong class="text-<?= strtotime($curso['fecha_fin']) < time() ? 'muted' : 'success' ?>">
                                                <?= date('d/m/Y', strtotime($curso['fecha_fin'])) ?>
                                            </strong>
                                        </div>
                                    </div>

                                    <!-- ESTADÍSTICAS DE ASISTENCIA -->
                                    <div class="bg-light p-3 rounded mb-3">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="border-end">
                                                    <div class="h5 mb-0 text-primary">
                                                        <?= $curso['estadisticas']['asistencias'] ?>
                                                    </div>
                                                    <small class="text-muted">Asistencias</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="h5 mb-0 text-info">
                                                    <?= $curso['estadisticas']['total_sesiones'] ?>
                                                </div>
                                                <small class="text-muted">Sesiones</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- BARRA DE PROGRESO -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">Progreso de Asistencia</small>
                                            <small class="text-muted"><?= $porcentaje ?>%</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <?php
                                            $progress_class = 'bg-danger';
                                            if ($porcentaje >= 80) $progress_class = 'bg-success';
                                            elseif ($porcentaje >= 60) $progress_class = 'bg-warning';
                                            ?>
                                            <div class="progress-bar <?= $progress_class ?>" 
                                                 style="width: <?= $porcentaje ?>%"></div>
                                        </div>
                                    </div>

                                    <!-- FECHA DE INSCRIPCIÓN -->
                                    <div class="text-center">
                                        <small class="text-muted">
                                            <i class="demo-pli-calendar-4 me-1"></i>
                                            Inscrito el <?= date('d/m/Y', strtotime($curso['fecha_inscripcion'])) ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="card-footer bg-white border-0 pt-0">
                                    <div class="d-grid">
                                        <a href="<?= site_url('participante/historial') ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="demo-pli-clock me-1"></i>
                                            Ver Historial de Asistencias
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- RESUMEN GENERAL -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title mb-0">
                                    <i class="demo-pli-statistic me-2 text-info"></i>
                                    Resumen General
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-sm-3">
                                        <div class="h4 text-primary mb-1"><?= count($mis_cursos) ?></div>
                                        <small class="text-muted">Cursos Inscritos</small>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="h4 text-success mb-1">
                                            <?= count(array_filter($mis_cursos, fn($c) => $c['estado'] == 1)) ?>
                                        </div>
                                        <small class="text-muted">Cursos Activos</small>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="h4 text-info mb-1">
                                            <?= array_sum(array_column(array_column($mis_cursos, 'estadisticas'), 'total_sesiones')) ?>
                                        </div>
                                        <small class="text-muted">Total Sesiones</small>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="h4 text-warning mb-1">
                                            <?= array_sum(array_column(array_column($mis_cursos, 'estadisticas'), 'asistencias')) ?>
                                        </div>
                                        <small class="text-muted">Total Asistencias</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

</section>

<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cerrar alertas automáticamente después de 5 segundos
    document.querySelectorAll('.alert').forEach(alertEl => {
        setTimeout(() => {
            try { 
                bootstrap.Alert.getOrCreateInstance(alertEl).close(); 
            } catch(e) {
                alertEl.style.display = 'none';
            }
        }, 5000);
    });

    // Efecto hover en las tarjetas
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
<?= $this->endSection() ?>