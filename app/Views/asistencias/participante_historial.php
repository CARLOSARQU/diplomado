<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Mi Historial de Asistencias<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <div class="breadcrumb mb-2">
                <a href="<?= site_url('/') ?>">Inicio</a><span class="breadcrumb-separator">/</span>
                <span>Mi Historial de Asistencias</span>
            </div>
            <h1 class="h1">Mi Historial de Asistencias</h1>
            <p class="text-muted">Aquí puedes ver todas las asistencias registradas en tus sesiones.</p>
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

            <!-- CARD HISTORIAL -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">
                        <i class="demo-pli-clock me-2 text-primary"></i> Historial
                    </h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Sesión</th>
                                    <th>Fecha y Hora de la Sesión</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($historial)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            <i class="demo-pli-information fs-2 mb-2 d-block"></i>
                                            No tienes asistencias registradas.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($historial as $item): ?>
                                        <tr>
                                            <td class="align-middle"><?= esc($item['titulo']) ?></td>
                                            <td class="align-middle">
    <?= date('d/m/Y h:i:s A', strtotime($item['fecha_asistencia'])) ?>
</td>
<td class="text-center align-middle">
                                                <?php if (!empty($item['estado'])): ?>
                                                    <span class="badge <?= strtolower($item['estado']) === 'presente' || $item['estado'] === 'present' ? 'bg-success' : 'bg-secondary' ?> text-capitalize">
                                                        <?= esc($item['estado']) ?>
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
            <!-- /CARD HISTORIAL -->

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
            try { bootstrap.Alert.getOrCreateInstance(alertEl).close(); } catch(e) {}
        }, 6000);
    });
});
</script>
<?= $this->endSection() ?>
