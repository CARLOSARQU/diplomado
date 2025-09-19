<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Panel del Participante<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item active" aria-current="page">Mi Panel</li>
                </ol>
            </nav>
            <h1 class="h1">Mi Panel</h1>
            <h3 class="text-muted mb-0">
                Bienvenido, <strong><?= esc(session('nombres'))?> <?= esc(session('apellidos')) ?></strong>.  
                Aquí puedes registrar tu asistencia a las sesiones programadas para hoy.
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
                <!-- SESIONES DISPONIBLES -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="demo-pli-calendar-4 me-2 text-primary"></i>
                            Sesiones de Hoy (<?= date('d/m/Y') ?>)
                        </h4>
                        <!--<div class="text-muted small">
                            <i class="demo-pli-information me-1"></i>
                            Solo aparecen sesiones de tus cursos inscritos
                        </div>-->
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($sesiones_hoy)): ?>
                        <div class="text-center py-5">
                            <div class="display-1 text-muted mb-3">
                                <i class="demo-pli-calendar-remove"></i>
                            </div>
                            <h5 class="text-muted mb-2">No hay sesiones programadas para hoy</h5>
                            <p class="text-muted mb-3">
                                <?php if ($estadisticas['cursos_inscritos'] == 0): ?>
                                    Aún no estás inscrito en ningún curso.
                                <?php else: ?>
                                    Las sesiones de tus cursos inscritos aparecerán aquí cuando estén programadas.
                                <?php endif; ?>
                            </p>
                            <!--<?php if ($estadisticas['cursos_inscritos'] > 0): ?>
                                <a href="<?= site_url('participante/mis-cursos') ?>" class="btn btn-outline-primary">
                                    <i class="demo-pli-book me-1"></i> Ver Mis Cursos
                                </a>
                            <?php endif; ?>-->
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($sesiones_hoy as $sesion): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h4 class="mb-1"><?= esc($sesion['titulo']) ?></h4>
                                            <small class="text-muted">
                                                <?= date('h:i A', strtotime($sesion['hora_inicio'])) ?> - 
                                                <?= date('h:i A', strtotime($sesion['hora_fin'])) ?>
                                            </small>
                                        </div>
                                        <p class="mb-1 text-muted small">
                                            <!--<strong>Curso:</strong> <?= esc($sesion['curso_nombre']) ?>-->
                                            <?php if (!empty($sesion['modulo_nombre'])): ?>
                                                <span class="mx-2">·</span>
                                                <strong>Módulo:</strong> <?= esc($sesion['modulo_nombre']) ?>
                                            <?php endif; ?>
                                        </p>
                                        <?php if ($sesion['ya_registro_asistencia']): ?>
                                            <small class="text-success">
                                                <i class="demo-pli-check me-1"></i>
                                                Asistencia registrada el <?= date('d/m/Y H:i', strtotime($sesion['hora_registro'])) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ms-3">
                                        <?php if ($sesion['ya_registro_asistencia']): ?>
                                            <span class="badge bg-success">
                                                <i class="demo-pli-check me-1"></i>Registrado
                                            </span>
                                        <?php else: ?>
                                            <form action="<?= site_url('asistencia/registrar/' . $sesion['id']) ?>" method="post" class="m-0">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="demo-pli-check me-1"></i> Marcar Asistencia
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="demo-pli-information me-1"></i>
                                Solo puedes registrar asistencia durante las horas programadas de la sesión
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <br>
            <!-- /SESIONES DISPONIBLES -->
            <!-- ESTADÍSTICAS RÁPIDAS -->
            <div class="row mb-4">
                <!--<div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="display-6 text-primary">
                                <i class="demo-pli-book"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?= $estadisticas['cursos_inscritos'] ?></h4>
                            <p class="text-muted mb-0">Cursos Inscritos</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="display-6 text-success">
                                <i class="demo-pli-check-2"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?= $estadisticas['cursos_activos'] ?></h4>
                            <p class="text-muted mb-0">Cursos Activos</p>
                        </div>
                    </div>
                </div>-->
                <div class="col-sm-6 col-lg-6">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="display-6 text-info">
                                <i class="demo-pli-calendar-4"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?= $estadisticas['total_asistencias'] ?></h4>
                            <p class="text-muted mb-0">Asistencias Registradas</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-6">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="display-6 text-warning">
                                <i class="demo-pli-statistic"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?= $estadisticas['porcentaje_asistencia'] ?>%</h4>
                            <p class="text-muted mb-0">Promedio de Asistencia</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCESOS RÁPIDOS -->
            <?php if ($estadisticas['cursos_inscritos'] > 0): ?>
            <div class="row mt-4">
                <!--<div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="display-6 text-info mb-2">
                                <i class="demo-pli-book"></i>
                            </div>
                            <h5 class="card-title">Mis Cursos</h5>
                            <p class="card-text text-muted">
                                Ver todos los cursos en los que estás inscrito y tu progreso.
                            </p>
                            <a href="<?= site_url('participante/mis-cursos') ?>" class="btn btn-outline-info">
                                <i class="demo-pli-book me-1"></i> Ver Cursos
                            </a>
                        </div>
                    </div>
                </div>-->
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="display-6 text-success mb-2">
                                <i class="demo-pli-clock"></i>
                            </div>
                            <h5 class="card-title">Historial de Asistencias</h5>
                            <p class="card-text text-muted">
                                Revisa todas tus asistencias registradas por curso.
                            </p>
                            <a href="<?= site_url('participante/historial') ?>" class="btn btn-outline-success">
                                <i class="demo-pli-clock me-1"></i> Ver Historial
                            </a>
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
    // Cerrar alertas automáticamente después de 8 segundos
    document.querySelectorAll('.alert').forEach(alertEl => {
        setTimeout(() => {
            try { 
                bootstrap.Alert.getOrCreateInstance(alertEl).close(); 
            } catch(e) {
                alertEl.style.display = 'none';
            }
        }, 8000);
    });

    // Confirmación para registrar asistencia
    document.querySelectorAll('form[action*="asistencia/registrar"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.disabled) {
                // Deshabilitar botón para evitar doble envío
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Registrando...';
                
                // Re-habilitar después de 5 segundos por si hay error
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="demo-pli-check me-1"></i> Marcar Asistencia';
                }, 5000);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>