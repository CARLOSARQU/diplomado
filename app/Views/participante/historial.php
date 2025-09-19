<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Historial de Asistencias<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= site_url('mi-panel') ?>">Mi Panel</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Historial de Asistencias</li>
                </ol>
            </nav>
            <h1 class="h1">Historial de Asistencias</h1>
        </div>
    </div>

    <div class="content__boxed">
        <div class="content__wrap">

            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="demo-pli-clock me-2 text-success"></i> Mis Asistencias
                        </h4>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Módulo</th>
                                    <th>Sesión</th>
                                    <th>Fecha y Hora</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($historial)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="demo-pli-information fs-2 mb-2 d-block"></i>
                                            No tienes asistencias registradas.
                                            <!--<br><small>User ID: <?= session('user_id') ?> | Rol: <?= session('rol_nombre') ?></small>-->
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($historial as $item): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <?= esc($item['modulo_nombre'] ?? 'N/A') ?>
                                            </td>
                                            <td class="align-middle"><?= esc($item['titulo'] ?? 'N/A') ?></td>
                                            <td class="align-middle">
                                                <?= isset($item['hora_registro']) ? date('d/m/Y h:i A', strtotime($item['hora_registro'])) : 'N/A' ?>
                                            </td>
                                            <td class="align-middle text-center">
                                                <?php 
                                                $estado = $item['observaciones'] ?? 'presente';
                                                $badge_class = $estado === 'tarde' ? 'bg-warning' : 'bg-success';
                                                ?>
                                                <span class="badge <?= $badge_class ?>"><?= esc($estado) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?= $this->endSection() ?>