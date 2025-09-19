<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Revisar Comprobante<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="content__boxed">
        <div class="content__wrap">
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('pagos') ?>">Gestión de Pagos</a></li>
                    <li class="breadcrumb-item active">Comprobante #<?= $pago['id'] ?></li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Comprobante #<?= $pago['id'] ?></h2>
                    <p class="text-muted mb-0">Revisar y gestionar comprobante de pago</p>
                </div>
                <div>
                    <?php
                    $statusClass = match($pago['estado']) {
                        'aprobado' => 'bg-success',
                        'rechazado' => 'bg-danger',
                        'en_revision' => 'bg-info',
                        default => 'bg-warning'
                    };
                    ?>
                    <span class="badge <?= $statusClass ?> fs-6">
                        <?= ucfirst($pago['estado']) ?>
                    </span>
                </div>
            </div>

            <div class="row">
                <!-- Información del comprobante -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary ">
                            <h6 class="card-title mb-0 text-white">
                                <i class="fas fa-info-circle me-2"></i>Información del Comprobante
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">PARTICIPANTE</label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <div class="avatar-initial bg-primary rounded-circle">
                                            <?= strtoupper(substr($pago['participante_nombres'], 0, 1)) ?>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?= esc($pago['participante_nombres'] . ' ' . $pago['participante_apellidos']) ?></div>
                                        <small class="text-muted">DNI: <?= esc($pago['participante_dni'] ?? 'N/A') ?></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="text-muted small">CURSO</label>
                                    <p class="mb-0 fw-medium"><?= esc($pago['curso_nombre']) ?></p>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small">MÓDULO</label>
                                    <p class="mb-0 fw-medium"><?= esc($pago['modulo_nombre']) ?></p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="text-muted small">MONTO</label>
                                    <div class="d-flex align-items-center">
                                        <p class="mb-0 h5 text-success flex-grow-1 me-2" id="monto-display">
                                            S/ <?= number_format($pago['monto'], 2) ?>
                                        </p>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="editarMonto()">
                                            <i class="fas fa-edit">Editar</i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small">MÉTODO</label>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark me-2" id="metodo-display">
                                            <?= ucfirst(str_replace('_', ' ', $pago['metodo_pago'])) ?>
                                        </span>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="editarMetodo()">
                                            <i class="fas fa-edit">Editar</i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">IDENTIFICADOR DE PAGO</label>
                                <div class="d-flex align-items-center">
                                    <p class="mb-0 font-monospace bg-light p-2 rounded flex-grow-1 me-2" id="identificador-display">
                                        <?= esc($pago['identificador_pago']) ?>
                                    </p>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="editarIdentificador()">
                                        <i class="fas fa-edit">Editar</i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">FECHA DE PAGO</label>
                                <div class="d-flex align-items-center">
                                    <p class="mb-0 flex-grow-1 me-2" id="fecha-display">
                                        <i class="fas fa-calendar me-2"></i>
                                        <?= date('d/m/Y', strtotime($pago['fecha_pago'])) ?>
                                    </p>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="editarFecha()">
                                        <i class="fas fa-edit">Editar</i>
                                    </button>
                                </div>
                            </div>

                            <?php if (!empty($pago['observaciones'])): ?>
                                <div class="mb-3">
                                    <label class="text-muted small">OBSERVACIONES DEL USUARIO</label>
                                    <div class="alert alert-light border-start border-info border-4 mb-0">
                                        <?= esc($pago['observaciones']) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($pago['observaciones_admin'])): ?>
                                <div class="mb-3">
                                    <label class="text-muted small">OBSERVACIONES ADMINISTRATIVAS</label>
                                    <div class="alert alert-light border-start border-warning border-4 mb-0">
                                        <?= esc($pago['observaciones_admin']) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <?php if ($pago['estado'] !== 'aprobado'): ?>
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-cogs me-2"></i>Acciones
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button onclick="aprobarPago()" class="btn btn-success">
                                        <i class="fas fa-check me-2"></i>Aprobar Comprobante
                                    </button>
                                    
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rechazarModal">
                                        <i class="fas fa-times me-2"></i>Rechazar Comprobante
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card shadow-sm border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                <h6 class="text-success">Comprobante Aprobado</h6>
                                <p class="text-muted small mb-0">Este comprobante ya ha sido procesado y aprobado.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Vista previa del comprobante -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-file-image me-2"></i>Comprobante de Pago
                            </h6>
                            <a href="<?= base_url('uploads/comprobantes/' . $pago['archivo_comprobante']) ?>" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt me-2"></i>Abrir en nueva pestaña
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="text-center p-4">
                                <?php 
                                $file = base_url('uploads/comprobantes/' . $pago['archivo_comprobante']); 
                                $ext = pathinfo($pago['archivo_comprobante'], PATHINFO_EXTENSION); 
                                ?>
                                
                                <?php if (strtolower($ext) === 'pdf'): ?>
                                    <div class="mb-3">
                                        <i class="fas fa-file-pdf text-danger fa-3x mb-2"></i>
                                        <p class="text-muted">Documento PDF</p>
                                    </div>
                                    <iframe src="<?= $file ?>" 
                                            style="width:100%; height:70vh; border: 1px solid #dee2e6; border-radius: 0.375rem;"
                                            class="shadow-sm">
                                    </iframe>
                                <?php else: ?>
                                    <img src="<?= $file ?>" 
                                         class="img-fluid rounded shadow" 
                                         alt="Comprobante de pago"
                                         style="max-height: 70vh; max-width: 100%;">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón volver -->
            <div class="mt-4">
                <a href="<?= base_url('pagos') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Gestión de Pagos
                </a>
            </div>

        </div>
    </div>
</section>

<!-- Modal Rechazo -->
<div class="modal fade" id="rechazarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('pagos/rechazar/' . $pago['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle me-2"></i>
                        Rechazar Comprobante
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Esta acción rechazará el comprobante de pago. El participante será notificado del rechazo.
                    </div>
                    
                    <div class="mb-3">
                        <label for="observaciones_admin" class="form-label">
                            <strong>Motivo del rechazo *</strong>
                        </label>
                        <textarea name="observaciones_admin" 
                                  id="observaciones_admin"
                                  class="form-control" 
                                  rows="4" 
                                  placeholder="Explique detalladamente por qué se rechaza este comprobante..."
                                  required></textarea>
                        <div class="form-text">
                            Sea específico para que el participante pueda corregir el problema.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban me-2"></i>Rechazar Comprobante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Monto -->
<div class="modal fade" id="editarMontoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('pagos/editar-datos/' . $pago['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-dollar-sign me-2"></i>
                        Editar Monto del Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        El monto debe ser mayor a S/ 0.00, verifica bien el monto.
                    </div>
                    
                    <div class="mb-3">
                        <label for="monto" class="form-label">
                            <strong>Monto del Pago *</strong>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" 
                                   name="monto" 
                                   id="monto" 
                                   class="form-control" 
                                   value="<?= number_format($pago['monto'], 2, '.', '') ?>"
                                   step="0.01"
                                   min="0.01"
                                   max="99999.99"
                                   placeholder="0.00"
                                   required>
                        </div>
                        <div class="form-text">
                            Ingrese el monto con hasta 2 decimales.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Fecha de Pago -->
<div class="modal fade" id="editarFechaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('pagos/editar-datos/' . $pago['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-edit me-2"></i>
                        Editar Fecha de Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        La fecha no puede ser futura ni anterior al año 2020.
                    </div>
                    
                    <div class="mb-3">
                        <label for="fecha_pago" class="form-label">
                            <strong>Fecha de Pago *</strong>
                        </label>
                        <input type="date" 
                               name="fecha_pago" 
                               id="fecha_pago" 
                               class="form-control" 
                               value="<?= date('Y-m-d', strtotime($pago['fecha_pago'])) ?>"
                               min="2020-01-01"
                               max="<?= date('Y-m-d') ?>"
                               required>
                        <div class="form-text">
                            Seleccione la fecha correcta del pago.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Método de Pago -->
<div class="modal fade" id="editarMetodoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('pagos/editar-datos/' . $pago['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Editar Método de Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label">
                            <strong>Método de Pago *</strong>
                        </label>
                        <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                            <?php foreach ($metodosPago as $key => $nombre): ?>
                                <option value="<?= $key ?>" <?= $pago['metodo_pago'] === $key ? 'selected' : '' ?>>
                                    <?= esc($nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Identificador -->
<div class="modal fade" id="editarIdentificadorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('pagos/editar-datos/' . $pago['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Editar Identificador de Pago
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        El identificador debe ser único y tener entre 5 y 20 caracteres alfanuméricos.
                    </div>
                    
                    <div class="mb-3">
                        <label for="identificador_pago" class="form-label">
                            <strong>Identificador de Pago *</strong>
                        </label>
                        <input type="text" 
                               name="identificador_pago" 
                               id="identificador_pago" 
                               class="form-control font-monospace" 
                               value="<?= esc($pago['identificador_pago']) ?>"
                               placeholder="Ej: TXN123456"
                               maxlength="20"
                               required>
                        <div class="form-text">
                            Solo letras y números. Mínimo 5 caracteres, máximo 20.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulario oculto para aprobación -->
<form id="aprobarForm" action="<?= base_url('pagos/aprobar/' . $pago['id']) ?>" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="observaciones_admin" value="">
</form>

<script>
function aprobarPago() {
    if (confirm('¿Está seguro de que desea aprobar este comprobante de pago?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('aprobarForm').submit();
    }
}

function editarMonto() {
    const modal = new bootstrap.Modal(document.getElementById('editarMontoModal'));
    modal.show();
}

function editarFecha() {
    const modal = new bootstrap.Modal(document.getElementById('editarFechaModal'));
    modal.show();
}

function editarMetodo() {
    const modal = new bootstrap.Modal(document.getElementById('editarMetodoModal'));
    modal.show();
}

function editarIdentificador() {
    const modal = new bootstrap.Modal(document.getElementById('editarIdentificadorModal'));
    modal.show();
}

    // Validación del monto en tiempo real
    const montoInput = document.getElementById('monto');
    if (montoInput) {
        montoInput.addEventListener('input', function() {
            const valor = parseFloat(this.value);
            const esValido = valor > 0 && valor <= 99999.99;
            
            if (this.value.length > 0 && (!esValido || isNaN(valor))) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    // Validación del identificador en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const identificadorInput = document.getElementById('identificador_pago');
    if (identificadorInput) {
        identificadorInput.addEventListener('input', function() {
            const valor = this.value;
            const esValido = /^[a-zA-Z0-9]{5,20}$/.test(valor);
            
            if (valor.length > 0 && !esValido) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    // Asegurar que Bootstrap esté funcionando
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap no está cargado. Los modales no funcionarán correctamente.');
        // Fallback para navegadores sin Bootstrap
        window.editarMonto = function() {
            const nuevoMonto = prompt('Ingrese el nuevo monto (ejemplo: 150.50):');
            if (nuevoMonto && nuevoMonto.trim()) {
                const monto = parseFloat(nuevoMonto.trim());
                if (!isNaN(monto) && monto > 0) {
                    enviarEdicion('monto', monto);
                } else {
                    alert('El monto debe ser un número mayor a 0.');
                }
            }
        };
        
        window.editarFecha = function() {
            const nuevaFecha = prompt('Ingrese la nueva fecha (YYYY-MM-DD):');
            if (nuevaFecha && nuevaFecha.trim()) {
                enviarEdicion('fecha_pago', nuevaFecha.trim());
            }
        };
        
        window.editarMetodo = function() {
            const nuevoMetodo = prompt('Ingrese el nuevo método de pago (banco_nacion, pagalo_pe, caja, transferencia, deposito, otro):');
            if (nuevoMetodo && nuevoMetodo.trim()) {
                enviarEdicion('metodo_pago', nuevoMetodo.trim());
            }
        };
        
        window.editarIdentificador = function() {
            const nuevoId = prompt('Ingrese el nuevo identificador (5-20 caracteres alfanuméricos):');
            if (nuevoId && nuevoId.trim()) {
                enviarEdicion('identificador_pago', nuevoId.trim());
            }
        };
    }
});

function enviarEdicion(campo, valor) {
    const form = document.createElement('form');
    form.method = 'post';
    form.action = '<?= base_url('pagos/editar-datos/' . $pago['id']) ?>';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= csrf_token() ?>';
    csrfInput.value = '<?= csrf_hash() ?>';
    
    const campoInput = document.createElement('input');
    campoInput.type = 'hidden';
    campoInput.name = campo;
    campoInput.value = valor;
    
    form.appendChild(csrfInput);
    form.appendChild(campoInput);
    document.body.appendChild(form);
    form.submit();
}
</script>

<?= $this->endSection() ?>