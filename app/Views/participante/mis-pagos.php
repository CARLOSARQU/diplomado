<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Mis Pagos<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= site_url('mi-panel') ?>">Mi Panel</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mis Pagos</li>
                </ol>
            </nav>
            <h1 class="h1">Mis Pagos</h1>
            <h3 class="text-muted mb-0">
                Gestiona tus comprobantes de pago por módulo
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

            <!-- MÓDULOS SIN PAGO O CON PAGO RECHAZADO -->
            <?php if (!empty($modulos_sin_pago)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning-subtle py-3">
                    <h5 class="card-title mb-0 text-warning-emphasis">
                        <i class="demo-pli-exclamation-triangle me-2"></i>
                        Módulos Pendientes de Pago
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($modulos_sin_pago as $modulo): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border <?= !empty($modulo['estado_pago']) && $modulo['estado_pago'] === 'rechazado' ? 'border-danger' : 'border-warning' ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0"><?= esc($modulo['modulo_nombre']) ?></h6>
                                        <?php if (!empty($modulo['estado_pago']) && $modulo['estado_pago'] === 'rechazado'): ?>
                                            <span class="badge bg-danger">Rechazado</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if (!empty($modulo['costo'])): ?>
                                        <p class="card-text small text-muted mb-2">
                                            <strong>Costo:</strong> S/ <?= number_format($modulo['costo'], 2) ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($modulo['estado_pago']) && $modulo['estado_pago'] === 'rechazado' && !empty($modulo['motivo_rechazo'])): ?>
                                        <div class="alert alert-danger alert-sm p-2 mb-2">
                                            <small>
                                                <strong>Motivo:</strong><br>
                                                <?= esc($modulo['motivo_rechazo']) ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-grid">
                                        <a href="<?= site_url('participante/subir-comprobante/' . $modulo['id']) ?>" 
                                           class="btn <?= !empty($modulo['estado_pago']) && $modulo['estado_pago'] === 'rechazado' ? 'btn-danger' : 'btn-warning' ?> btn-sm">
                                            <i class="<?= !empty($modulo['estado_pago']) && $modulo['estado_pago'] === 'rechazado' ? 'demo-pli-repeat-2' : 'demo-pli-add' ?> me-1"></i> 
                                            <?= !empty($modulo['estado_pago']) && $modulo['estado_pago'] === 'rechazado' ? 'Reenviar Comprobante' : 'Subir Comprobante' ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- COMPROBANTES SUBIDOS -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="demo-pli-credit-card me-2 text-primary"></i>
                        Historial de Comprobantes
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pagos_existentes)): ?>
                        <div class="text-center py-5">
                            <div class="display-1 text-muted mb-3">
                                <i class="demo-pli-credit-card"></i>
                            </div>
                            <h5 class="text-muted mb-2">No hay comprobantes subidos</h5>
                            <p class="text-muted">
                                Los comprobantes que subas aparecerán aquí con su estado de revisión.
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Módulo</th>
                                        <th>Monto</th>
                                        <?php if (array_filter($pagos_existentes, fn($p) => !empty($p['metodo_pago']))): ?>
                                            <th>Método</th>
                                            <th>ID Pago</th>
                                        <?php endif; ?>
                                        <th>Fecha</th>
                                        <th class="text-center">Estado</th>
                                        <th>Comprobante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagos_existentes as $pago): ?>
                                    <tr>
                                        <td><?= esc($pago['modulo_nombre']) ?></td>
                                        <td><strong>S/ <?= number_format($pago['monto'], 2) ?></strong></td>
                                        
                                        <?php if (array_filter($pagos_existentes, fn($p) => !empty($p['metodo_pago']))): ?>
                                            <td>
                                                <?php if (!empty($pago['metodo_pago'])): ?>
                                                    <span class="badge bg-info">
                                                        <?php
                                                        $metodos = [
                                                            'banco_nacion' => 'Banco de la Nación',
                                                            'pagalo_pe' => 'Pagalo.pe',
                                                            'caja' => 'Caja'
                                                        ];
                                                        echo $metodos[$pago['metodo_pago']] ?? $pago['metodo_pago'];
                                                        ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($pago['identificador_pago'])): ?>
                                                    <code><?= esc($pago['identificador_pago']) ?></code>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                        
                                        <td><?= date('d/m/Y', strtotime($pago['fecha_pago'])) ?></td>
                                        <td class="text-center">
                                            <?php
                                            $estados = [
                                                'pendiente' => ['bg-secondary', 'Pendiente'],
                                                'en_revision' => ['bg-warning', 'En Revisión'],
                                                'aprobado' => ['bg-success', 'Aprobado'],
                                                'rechazado' => ['bg-danger', 'Rechazado']
                                            ];
                                            $estado_info = $estados[$pago['estado']] ?? ['bg-secondary', 'Desconocido'];
                                            ?>
                                            <span class="badge <?= $estado_info[0] ?>"><?= $estado_info[1] ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('uploads/comprobantes/' . $pago['archivo_comprobante']) ?>" 
                                               target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="demo-pli-file me-1"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    <!-- Fila de detalles para pagos rechazados o aprobados con observaciones -->
                                    <?php if ($pago['estado'] == 'rechazado' && !empty($pago['observaciones_admin'])): ?>
                                    <tr>
                                        <td colspan="<?= array_filter($pagos_existentes, fn($p) => !empty($p['metodo_pago'])) ? '7' : '5' ?>" class="bg-danger-subtle">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="demo-pli-close text-danger mt-1"></i>
                                                <div class="flex-grow-1">
                                                    <small class="text-danger">
                                                        <strong>Motivo del rechazo:</strong> <?= esc($pago['observaciones_admin']) ?>
                                                        <?php if (!empty($pago['revisor_nombres'])): ?>
                                                            <br><strong>Revisado por:</strong> <?= esc($pago['revisor_nombres'] . ' ' . $pago['revisor_apellidos']) ?>
                                                            el <?= date('d/m/Y H:i', strtotime($pago['fecha_revision'])) ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <a href="<?= site_url('participante/subir-comprobante/' . $pago['modulo_id']) ?>" 
                                                   class="btn btn-sm btn-danger">
                                                    <i class="demo-pli-repeat-2 me-1"></i> Reenviar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php elseif ($pago['estado'] == 'aprobado' && !empty($pago['observaciones_admin'])): ?>
                                    <tr>
                                        <td colspan="<?= array_filter($pagos_existentes, fn($p) => !empty($p['metodo_pago'])) ? '7' : '5' ?>" class="bg-success-subtle">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="demo-pli-check text-success mt-1"></i>
                                                <small class="text-success flex-grow-1">
                                                    <strong>Observaciones:</strong> <?= esc($pago['observaciones_admin']) ?>
                                                    <?php if (!empty($pago['revisor_nombres'])): ?>
                                                        <br><strong>Aprobado por:</strong> <?= esc($pago['revisor_nombres'] . ' ' . $pago['revisor_apellidos']) ?>
                                                        el <?= date('d/m/Y H:i', strtotime($pago['fecha_revision'])) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</section>

<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cerrar alertas automáticamente después de 6 segundos
    document.querySelectorAll('.alert:not(.alert-sm)').forEach(alertEl => {
        setTimeout(() => {
            try { 
                bootstrap.Alert.getOrCreateInstance(alertEl).close(); 
            } catch(e) {
                alertEl.style.display = 'none';
            }
        }, 6000);
    });
});
</script>
<?= $this->endSection() ?>