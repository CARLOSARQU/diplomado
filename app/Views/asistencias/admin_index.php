<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Asistencias de la Sesión
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <div class="breadcrumb mb-2">
                <a href="<?= site_url('cursos') ?>">Cursos</a><span class="breadcrumb-separator">/</span>
                <a href="<?= site_url('cursos/' . $curso['id'] . '/modulos') ?>"><?= esc($curso['nombre']) ?></a><span
                    class="breadcrumb-separator">/</span>
                <a href="<?= site_url('modulos/' . $modulo['id'] . '/sesiones') ?>"><?= esc($modulo['nombre']) ?></a><span
                    class="breadcrumb-separator">/</span>
                <span>Asistencias</span>
            </div>
            <h1 class="h1">Lista de Asistencia</h1>
            <p class="text-muted">Participantes que asistieron a la sesión: "<?= esc($sesion['titulo']) ?>"</p>
        </div>
    </div>
    <!-- /HEADER -->

    <div class="content__boxed">
        <div class="content__wrap">

            <!-- ALERTAS -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">Error</h5>
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <!-- /ALERTAS -->

            <!-- CARD ASISTENCIAS -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="demo-pli-list-view me-2 text-primary"></i> Asistencias
                        </h4>
                        <small class="text-muted">Sesión: <?= esc($sesion['titulo']) ?></small>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Nombres y Apellidos</th>
                                    <th>DNI</th>
                                    <th>Fecha y Hora de Registro</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($asistencias)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="demo-pli-information fs-2 mb-2 d-block"></i>
                                            Nadie ha registrado asistencia para esta sesión todavía.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($asistencias as $asistencia): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <strong><?= esc($asistencia['nombres'] . ' ' . $asistencia['apellidos']) ?></strong>
                                            </td>
                                            <td class="align-middle"><?= esc($asistencia['dni']) ?></td>
                                            <td class="align-middle">
                                                <?= date('d/m/Y h:i:s A', strtotime($asistencia['hora_registro'])) ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if (!empty($asistencia['observaciones'])): ?>
                                                    <span
                                                        class="badge <?= strtolower($asistencia['observaciones']) === 'presente' ? 'bg-success' : 'bg-warning' ?> text-capitalize">
                                                        <?= esc($asistencia['observaciones']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">--</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /CARD ASISTENCIAS -->

        </div>
    </div>

</section>

<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Cerrar alertas automáticamente después de 6 segundos
        document.querySelectorAll('.alert').forEach(alertEl => {
            setTimeout(() => {
                try { bootstrap.Alert.getOrCreateInstance(alertEl).close(); } catch (e) { }
            }, 6000);
        });
    });
</script>
<?= $this->endSection() ?>
