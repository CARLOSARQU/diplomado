<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Gestión de Pagos<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section id="content" class="content">
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Gestión de Pagos</li>
                </ol>
            </nav>
            <h1 class="h1 mb-2">Gestión de Pagos</h1>
            <p class="text-muted mb-0">Revisar, aprobar o rechazar comprobantes subidos por participantes.</p>
        </div>
    </div>

    <div class="content__boxed">
        <div class="content__wrap">

            <!-- Mensajes de estado -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Panel de filtros mejorado -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros de búsqueda
                    </h5>
                </div>
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Curso</label>
                            <select name="curso_id" class="form-select">
                                <option value="">-- Todos los cursos --</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?= $curso['id'] ?>" <?= isset($filtros['curso_id']) && $filtros['curso_id'] == $curso['id'] ? 'selected' : '' ?>>
                                        <?= esc($curso['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="">-- Todos --</option>
                                <option value="pendiente" <?= (isset($filtros['estado']) && $filtros['estado'] == 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
                                <option value="en_revision" <?= (isset($filtros['estado']) && $filtros['estado'] == 'en_revision') ? 'selected' : '' ?>>En revisión</option>
                                <option value="aprobado" <?= (isset($filtros['estado']) && $filtros['estado'] == 'aprobado') ? 'selected' : '' ?>>Aprobado</option>
                                <option value="rechazado" <?= (isset($filtros['estado']) && $filtros['estado'] == 'rechazado') ? 'selected' : '' ?>>Rechazado</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Método de pago</label>
                            <select name="metodo_pago" class="form-select">
                                <option value="">-- Todos --</option>
                                <option value="banco_nacion" <?= (isset($filtros['metodo_pago']) && $filtros['metodo_pago'] == 'banco_nacion') ? 'selected' : '' ?>>Banco de la Nación
                                </option>
                                <option value="pagalo_pe" <?= (isset($filtros['metodo_pago']) && $filtros['metodo_pago'] == 'pagalo_pe') ? 'selected' : '' ?>>Pagalo.pe</option>
                                <option value="caja" <?= (isset($filtros['metodo_pago']) && $filtros['metodo_pago'] == 'caja') ? 'selected' : '' ?>>Caja</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control"
                                value="<?= $filtros['fecha_desde'] ?? '' ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control"
                                value="<?= $filtros['fecha_hasta'] ?? '' ?>">
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search">Buscar</i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla mejorada -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>Comprobantes de pago
                    </h5>
                    <small class="text-muted"><?= count($pagos) ?> registros encontrados</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">#</th>
                                    <th class="border-0">Participante</th>
                                    <th class="border-0">DNI</th>
                                    <th class="border-0">Curso</th>
                                    <th class="border-0">Módulo</th>
                                    <th class="border-0">Método</th>
                                    <th class="border-0">Monto</th>
                                    <th class="border-0">Fecha</th>
                                    <th class="border-0">Estado</th>
                                    <th class="border-0 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pagos)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p class="mb-0">No hay comprobantes que coincidan con los filtros.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pagos as $p): ?>
                                        <tr>
                                            <td class="fw-medium">#<?= $p['id'] ?></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium"><?= esc($p['participante_nombres']) ?></span>
                                                    <small class="text-muted"><?= esc($p['participante_apellidos']) ?></small>
                                                </div>
                                            </td>
                                            <td><code><?= esc($p['participante_dni']) ?></code></td>
                                            <td>
                                                <small
                                                    class="text-primary fw-medium"><?= esc($p['curso_nombre'] ?? 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <small class="text-info"><?= esc($p['modulo_nombre'] ?? 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $metodo_icons = [
                                                    'banco_nacion' => 'fas fa-university text-primary',
                                                    'pagalo_pe' => 'fas fa-mobile-alt text-success',
                                                    'caja' => 'fas fa-cash-register text-warning'
                                                ];
                                                $icon = $metodo_icons[$p['metodo_pago']] ?? 'fas fa-credit-card';
                                                ?>
                                                <div class="d-flex align-items-center">
                                                    <i class="<?= $icon ?> me-2"></i>
                                                    <small><?= esc(ucwords(str_replace('_', ' ', $p['metodo_pago']))) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">S/
                                                    <?= number_format($p['monto'], 2) ?></span>
                                            </td>
                                            <td>
                                                <small><?= date('d/m/Y', strtotime($p['fecha_pago'])) ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_classes = [
                                                    'aprobado' => 'bg-success',
                                                    'rechazado' => 'bg-danger',
                                                    'en_revision' => 'bg-info',
                                                    'pendiente' => 'bg-warning'
                                                ];
                                                $badge_class = $badge_classes[$p['estado']] ?? 'bg-secondary';
                                                ?>
                                                <span class="badge <?= $badge_class ?> text-capitalize">
                                                    <?= str_replace('_', ' ', $p['estado']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?= base_url('pagos/revisar/' . $p['id']) ?>"
                                                        class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip"
                                                        title="Ver detalles">
                                                        <i class="fas fa-eye">Ver</i>
                                                    </a>

                                                    <button type="button" class="btn btn-outline-info btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#previewModal"
                                                        onclick="loadPreview(<?= $p['id'] ?>, '<?= esc($p['archivo_comprobante']) ?>', '<?= esc($p['observaciones']) ?>')"
                                                        title="Vista previa">
                                                        <i class="fas fa-search-plus">Previa</i>
                                                    </button>

                                                    <?php if (!in_array($p['estado'], ['aprobado', 'rechazado'])): ?>
                                                        <form action="<?= base_url('pagos/aprobar/' . $p['id']) ?>" method="post"
                                                            class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="observaciones_admin" value="">
                                                            <button class="btn btn-outline-success btn-sm"
                                                                onclick="return confirm('¿Aprobar este pago?')"
                                                                data-bs-toggle="tooltip" title="Aprobar pago">
                                                                <i class="fas fa-check">✓</i>
                                                            </button>
                                                        </form>

                                                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#rechazarModal"
                                                            onclick="setupRejectModal(<?= $p['id'] ?>)" title="Rechazar pago">
                                                            <i class="fas fa-times">✗</i>
                                                        </button>
                                                    <?php else: ?>
                                                        <?php if ($p['estado'] === 'aprobado'): ?>
                                                            <span class="badge bg-success" title="Pago aprobado">
                                                                <i class="fas fa-check-circle">Aprobado</i>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger"
                                                                title="Pago rechazado - Usuario puede reenviar">
                                                                <i class="fas fa-times-circle">Rechazado</i>
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Acciones adicionales -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="<?= site_url('pagos/reportes') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-chart-bar me-2"></i>Ver reportes
                </a>

                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Última actualización: <?= date('d/m/Y H:i') ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Modal Preview Universal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>Vista previa - Comprobante <span id="previewPaymentId">#</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
                <hr>
                <div class="mt-3">
                    <h6 class="fw-bold">Observaciones del usuario:</h6>
                    <p id="previewObservaciones" class="text-muted fst-italic">Sin observaciones</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rechazo Universal -->
<div class="modal fade" id="rechazarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="rejectForm" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle me-2"></i>Rechazar comprobante
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atención:</strong> Esta acción rechazará el comprobante y enviará una notificación al
                        participante.
                    </div>
                    <div class="mb-3">
                        <label for="observaciones_admin" class="form-label fw-bold">Motivo del rechazo *</label>
                        <textarea name="observaciones_admin" id="observaciones_admin" class="form-control" rows="4"
                            required
                            placeholder="Describe el motivo por el cual se rechaza este comprobante..."></textarea>
                        <div class="form-text">Este mensaje será visible para el participante.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban me-2"></i>Rechazar comprobante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Función para cargar preview
    function loadPreview(paymentId, filename, observaciones) {
        document.getElementById('previewPaymentId').textContent = '#' + paymentId;
        document.getElementById('previewObservaciones').textContent = observaciones || 'Sin observaciones';

        const previewContent = document.getElementById('previewContent');
        const fileUrl = '<?= base_url('uploads/comprobantes/') ?>' + filename;
        const extension = filename.split('.').pop().toLowerCase();

        if (extension === 'pdf') {
            previewContent.innerHTML = `<iframe src="${fileUrl}" style="width:100%; height:70vh;" frameborder="0"></iframe>`;
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
            previewContent.innerHTML = `<img src="${fileUrl}" alt="Comprobante" class="img-fluid" style="max-height:70vh;" />`;
        } else {
            previewContent.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-file me-2"></i>
                Archivo no previsualizable. <a href="${fileUrl}" target="_blank" class="alert-link">Descargar archivo</a>
            </div>`;
        }
    }

    // Función para configurar modal de rechazo
    function setupRejectModal(paymentId) {
        const form = document.getElementById('rejectForm');
        form.action = '<?= base_url('pagos/rechazar/') ?>' + paymentId;
        document.getElementById('observaciones_admin').value = '';
    }

    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<?= $this->endSection() ?>