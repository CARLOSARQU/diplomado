<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Mis Notas<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= site_url('participante/dashboard') ?>">Mi Panel</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mis Notas</li>
                </ol>
            </nav>
            <h1 class="h1">Mis Notas</h1>
            <h3 class="text-muted mb-0">
                Aquí puedes ver tus calificaciones por módulo en cada curso inscrito.
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

            <!-- ESTADÍSTICAS GENERALES 
            <div class="row mb-4">
                <div class="col-sm-6 col-lg-3">
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
                            <div class="display-6 text-info">
                                <i class="demo-pli-file-zip"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?= $estadisticas['total_modulos'] ?></h4>
                            <p class="text-muted mb-0">Total Módulos</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="display-6 text-success">
                                <i class="demo-pli-check-2"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?= $estadisticas['modulos_calificados'] ?></h4>
                            <p class="text-muted mb-0">Módulos Calificados</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="display-6 text-warning">
                                <i class="demo-pli-statistic"></i>
                            </div>
                            <h4 class="mt-2 mb-1">
                                <?= $estadisticas['promedio_general'] !== null ? number_format($estadisticas['promedio_general'], 2) : '-' ?>
                            </h4>
                            <p class="text-muted mb-0">Promedio General</p>
                        </div>
                    </div>
                </div>
            </div>
             /ESTADÍSTICAS GENERALES -->

            <!-- MIS CURSOS Y NOTAS -->
            <?php if (empty($mis_cursos)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-center py-5">
                            <div class="display-1 text-muted mb-3">
                                <i class="demo-pli-book"></i>
                            </div>
                            <h5 class="text-muted mb-2">No estás inscrito en ningún curso</h5>
                            <p class="text-muted mb-3">
                                Cuando te inscriban en cursos, tus notas aparecerán aquí.
                            </p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($mis_cursos as $curso): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-1">
                                        <i class="demo-pli-book me-2 text-primary"></i>
                                        <?= esc($curso['curso_nombre']) ?>
                                    </h4>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($curso['fecha_inicio'])) ?> -
                                        <?= date('d/m/Y', strtotime($curso['fecha_fin'])) ?>
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span
                                        class="badge bg-<?= $curso['estado'] == 'activo' ? 'success' : ($curso['estado'] == 'finalizado' ? 'secondary' : 'warning') ?>">
                                        <?= ucfirst($curso['estado']) ?>
                                    </span>
                                    <?php if ($curso['promedio'] !== null): ?>
                                        <div class="mt-2">
                                            <span
                                                class="badge bg-<?= $curso['promedio'] >= 14 ? 'success' : ($curso['promedio'] >= 11 ? 'warning' : 'danger') ?> fs-6">
                                                Promedio: <?= number_format($curso['promedio'], 2) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Barra de Progreso -->
                            <div class="mb-3">
                                <!--<div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Progreso de Calificaciones</small>
                                    <small
                                        class="text-muted"><?= $curso['modulos_calificados'] ?>/<?= $curso['total_modulos'] ?>
                                        módulos</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: <?= $curso['progreso_porcentaje'] ?>%"
                                        aria-valuenow="<?= $curso['progreso_porcentaje'] ?>" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>-->
                            </div>

                            <!-- Tabla de Notas por Módulo -->
                            <?php if (empty($curso['modulos'])): ?>
                                <div class="alert alert-info mb-0">
                                    <i class="demo-pli-information me-2"></i>
                                    Este curso aún no tiene módulos registrados.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="60">#</th>
                                                <th>Módulo</th>
                                                <th width="100" class="text-center">Nota</th>
                                                <th width="120" class="text-center">Estado</th>
                                                <th width="150">Fecha Registro</th>
                                                <th>Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($curso['modulos'] as $modulo): ?>

                                                <?php if ($modulo['orden'] == 0)
                                                    continue; ?> <!-- OCULTA EL PRIMER MÓDULO -->

                                                <tr>
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary"><?= $modulo['orden'] ?></span>
                                                    </td>
                                                    <td>
                                                        <strong><?= esc($modulo['modulo_nombre']) ?></strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($modulo['nota'] !== null): ?>
                                                            <span
                                                                class="badge fs-6 bg-<?= $modulo['nota'] >= 14 ? 'success' : ($modulo['nota'] >= 11 ? 'warning' : 'danger') ?>">
                                                                <?= number_format($modulo['nota'], 2) ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($modulo['tiene_nota']): ?>
                                                            <?php if ($modulo['nota'] >= 11): ?>
                                                                <span class="badge bg-success">
                                                                    <i class="demo-pli-check me-1"></i>Aprobado
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">
                                                                    <i class="demo-pli-close me-1"></i>Desaprobado
                                                                </span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Pendiente</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($modulo['fecha_registro']): ?>
                                                            <small class="text-muted">
                                                                <?= date('d/m/Y H:i', strtotime($modulo['fecha_registro'])) ?>
                                                            </small>
                                                        <?php else: ?>
                                                            <small class="text-muted">-</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($modulo['observaciones']): ?>
                                                            <small class="text-muted"><?= esc($modulo['observaciones']) ?></small>
                                                        <?php else: ?>
                                                            <small class="text-muted">-</small>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>

                                            <?php endforeach; ?>

                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="demo-pli-information me-1"></i>
                                        Las notas son registradas por los docentes del curso
                                    </small>
                                </div>
                                <div class="col-md-6 text-end">
                                    <?php if ($curso['promedio'] !== null): ?>
                                        <small class="text-muted">
                                            Promedio Final:
                                            <strong
                                                class="text-<?= $curso['promedio'] >= 14 ? 'success' : ($curso['promedio'] >= 11 ? 'warning' : 'danger') ?>">
                                                <?= number_format($curso['promedio'], 2) ?>
                                            </strong>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- /MIS CURSOS Y NOTAS -->

            <!-- LEYENDA -->
            <?php if (!empty($mis_cursos)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="demo-pli-information me-2"></i>Leyenda de Calificaciones
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-1">
                                        <span class="badge bg-success">14 - 20</span>
                                        <span class="ms-2 text-muted">Aprobado</span>
                                    </li>
                                    <li class="mb-1">
                                        <span class="badge bg-warning">11 - 13</span>
                                        <span class="ms-2 text-muted">Aprobado (Regular)</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-1">
                                        <span class="badge bg-danger">0 - 10</span>
                                        <span class="ms-2 text-muted">Desaprobado</span>
                                    </li>
                                    <li class="mb-1">
                                        <span class="badge bg-secondary">-</span>
                                        <span class="ms-2 text-muted">Pendiente de calificación</span>
                                    </li>
                                </ul>
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
                } catch (e) {
                    alertEl.style.display = 'none';
                }
            }, 8000);
        });
    });
</script>
<?= $this->endSection() ?>