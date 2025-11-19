<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Gestionar Notas - <?= esc($modulo['nombre']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('cursos') ?>">Cursos</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('cursos/' . $curso['id'] . '/modulos') ?>"><?= esc($curso['nombre']) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gestionar Notas</li>
                </ol>
            </nav>
            <h1 class="h1">Gestionar Notas</h1>
            <h3 class="text-muted mb-0">
                <strong>Módulo:</strong> <?= esc($modulo['nombre']) ?>
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

            <!-- ESTADÍSTICAS DEL MÓDULO -->
            <?php if ($estadisticas && $estadisticas['total_notas'] > 0): ?>
            <div class="row mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="display-6 text-primary">
                                <i class="demo-pli-file-edit"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?= $estadisticas['total_notas'] ?></h4>
                            <p class="text-muted mb-0">Notas Registradas</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="display-6 text-info">
                                <i class="demo-pli-statistic"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?= number_format($estadisticas['promedio'], 2) ?></h4>
                            <p class="text-muted mb-0">Promedio</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="display-6 text-success">
                                <i class="demo-pli-check-2"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?= $estadisticas['aprobados'] ?></h4>
                            <p class="text-muted mb-0">Aprobados</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="display-6 text-danger">
                                <i class="demo-pli-close"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?= $estadisticas['desaprobados'] ?></h4>
                            <p class="text-muted mb-0">Desaprobados</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!-- /ESTADÍSTICAS DEL MÓDULO -->

            <!-- INFORMACIÓN DEL CURSO Y MÓDULO -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="demo-pli-book me-2 text-primary"></i>
                                Información del Curso
                            </h5>
                            <table class="table table-sm">
                                <tr>
                                    <td width="120"><strong>Curso:</strong></td>
                                    <td><?= esc($curso['nombre']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        <span class="badge bg-<?= $curso['estado'] == 'activo' ? 'success' : ($curso['estado'] == 'finalizado' ? 'secondary' : 'warning') ?>">
                                            <?= ucfirst($curso['estado']) ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="demo-pli-file-zip me-2 text-info"></i>
                                Información del Módulo
                            </h5>
                            <table class="table table-sm">
                                <tr>
                                    <td width="120"><strong>Módulo:</strong></td>
                                    <td><?= esc($modulo['nombre']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Orden:</strong></td>
                                    <td><span class="badge bg-secondary"><?= $modulo['orden'] ?></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /INFORMACIÓN DEL CURSO Y MÓDULO -->

            <!-- TABLA DE PARTICIPANTES Y NOTAS -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="demo-pli-users me-2 text-primary"></i>
                            Participantes y Notas (<?= count($participantes) ?>)
                        </h4>
                        <div>
                            <button type="button" class="btn btn-sm btn-success" onclick="guardarTodasNotas()">
                                <i class="demo-pli-save me-1"></i> Guardar Todas
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($participantes)): ?>
                        <div class="text-center py-5">
                            <div class="display-1 text-muted mb-3">
                                <i class="demo-pli-user"></i>
                            </div>
                            <h5 class="text-muted mb-2">No hay participantes inscritos</h5>
                            <p class="text-muted mb-3">
                                Inscribe participantes al curso para poder registrar sus notas.
                            </p>
                            <a href="<?= site_url('cursos/inscribir/' . $curso['id']) ?>" class="btn btn-outline-primary">
                                <i class="demo-pli-add me-1"></i> Inscribir Participantes
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>DNI</th>
                                        <th>Apellidos y Nombres</th>
                                        <th width="150">Nota (0-20)</th>
                                        <th>Observaciones</th>
                                        <th width="180">Última Actualización</th>
                                        <th width="150" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($participantes as $index => $participante): ?>
                                        <tr data-participante-id="<?= $participante['participante_id'] ?>">
                                            <td class="text-center"><?= $index + 1 ?></td>
                                            <td><?= esc($participante['dni']) ?></td>
                                            <td>
                                                <strong><?= esc($participante['apellidos']) ?>, <?= esc($participante['nombres']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= esc($participante['correo']) ?></small>
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       class="form-control form-control-sm nota-input" 
                                                       data-participante-id="<?= $participante['participante_id'] ?>"
                                                       value="<?= $participante['nota'] !== null ? number_format($participante['nota'], 2, '.', '') : '' ?>" 
                                                       min="0" 
                                                       max="20" 
                                                       step="0.01"
                                                       placeholder="0.00">
                                            </td>
                                            <td>
                                                <textarea class="form-control form-control-sm observaciones-input" 
                                                          data-participante-id="<?= $participante['participante_id'] ?>"
                                                          rows="2" 
                                                          placeholder="Opcional"><?= esc($participante['observaciones'] ?? '') ?></textarea>
                                            </td>
                                            <td>
                                                <?php if ($participante['fecha_registro']): ?>
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y', strtotime($participante['fecha_registro'])) ?>
                                                        <br>
                                                        <?= date('H:i', strtotime($participante['fecha_registro'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <small class="text-muted">Sin registro</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary guardar-individual"
                                                        data-participante-id="<?= $participante['participante_id'] ?>"
                                                        onclick="guardarNotaIndividual(<?= $participante['participante_id'] ?>)">
                                                    <i class="demo-pli-save me-1"></i> Guardar
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- LEYENDA -->
                        <div class="mt-4">
                            <div class="alert alert-info mb-0">
                                <h6 class="alert-heading">
                                    <i class="demo-pli-information me-2"></i>Instrucciones
                                </h6>
                                <ul class="mb-0">
                                    <li>Las notas deben estar en el rango de <strong>0 a 20</strong></li>
                                    <li>Puede usar decimales (ejemplo: 15.50)</li>
                                    <li>Las observaciones son opcionales</li>
                                    <li>Puede guardar individualmente o todas a la vez</li>
                                    <li>Nota mínima aprobatoria: <strong>11.00</strong></li>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- /TABLA DE PARTICIPANTES Y NOTAS -->

        </div>
    </div>

</section>

<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
// Variables globales
const moduloId = <?= $modulo['id'] ?>;
const baseUrl = '<?= site_url() ?>';

// Cerrar alertas automáticamente
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.alert').forEach(alertEl => {
        setTimeout(() => {
            try { 
                bootstrap.Alert.getOrCreateInstance(alertEl).close(); 
            } catch(e) {
                alertEl.style.display = 'none';
            }
        }, 8000);
    });
});

// Validar nota al escribir
document.querySelectorAll('.nota-input').forEach(input => {
    input.addEventListener('input', function() {
        let valor = parseFloat(this.value);
        if (valor < 0) this.value = 0;
        if (valor > 20) this.value = 20;
        
        // Cambiar color según la nota
        this.classList.remove('border-success', 'border-warning', 'border-danger');
        if (valor >= 14) {
            this.classList.add('border-success');
        } else if (valor >= 11) {
            this.classList.add('border-warning');
        } else if (valor >= 0) {
            this.classList.add('border-danger');
        }
    });
});

// Guardar nota individual
function guardarNotaIndividual(participanteId) {
    const row = document.querySelector(`tr[data-participante-id="${participanteId}"]`);
    const notaInput = row.querySelector('.nota-input');
    const observacionesInput = row.querySelector('.observaciones-input');
    const btn = row.querySelector('.guardar-individual');
    
    const nota = notaInput.value.trim();
    const observaciones = observacionesInput.value.trim();
    
    // Validar nota
    if (nota !== '' && (parseFloat(nota) < 0 || parseFloat(nota) > 20)) {
        mostrarAlerta('error', 'La nota debe estar entre 0 y 20');
        return;
    }
    
    // Deshabilitar botón
    btn.disabled = true;
    const btnTextOriginal = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
    
    // Preparar datos
    const formData = new FormData();
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    formData.append('modulo_id', moduloId);
    formData.append('participante_id', participanteId);
    formData.append('nota', nota);
    formData.append('observaciones', observaciones);
    
    // Enviar petición
    fetch(`${baseUrl}/notas/guardar`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('success', data.message);
            // Animar la fila
            row.classList.add('table-success');
            setTimeout(() => row.classList.remove('table-success'), 2000);
        } else {
            mostrarAlerta('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('error', 'Error al guardar la nota');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = btnTextOriginal;
    });
}

// Guardar todas las notas
function guardarTodasNotas() {
    const rows = document.querySelectorAll('tbody tr[data-participante-id]');
    let notasGuardadas = 0;
    let errores = 0;
    
    if (rows.length === 0) {
        mostrarAlerta('warning', 'No hay participantes para guardar notas');
        return;
    }
    
    if (!confirm(`¿Está seguro de guardar todas las notas (${rows.length} participantes)?`)) {
        return;
    }
    
    // Deshabilitar botón de guardar todas
    const btnGuardarTodas = event.target;
    btnGuardarTodas.disabled = true;
    btnGuardarTodas.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
    
    // Array de promesas
    const promesas = Array.from(rows).map(row => {
        const participanteId = row.dataset.participanteId;
        const notaInput = row.querySelector('.nota-input');
        const observacionesInput = row.querySelector('.observaciones-input');
        
        const nota = notaInput.value.trim();
        const observaciones = observacionesInput.value.trim();
        
        // Solo guardar si hay nota
        if (nota === '') {
            return Promise.resolve({success: true, skipped: true});
        }
        
        // Validar rango
        if (parseFloat(nota) < 0 || parseFloat(nota) > 20) {
            return Promise.resolve({success: false, skipped: false});
        }
        
        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        formData.append('modulo_id', moduloId);
        formData.append('participante_id', participanteId);
        formData.append('nota', nota);
        formData.append('observaciones', observaciones);
        
        return fetch(`${baseUrl}/notas/guardar`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .catch(() => ({success: false}));
    });
    
    // Procesar todas las promesas
    Promise.all(promesas)
        .then(resultados => {
            resultados.forEach((resultado, index) => {
                if (resultado.skipped) {
                    return;
                }
                if (resultado.success) {
                    notasGuardadas++;
                    rows[index].classList.add('table-success');
                } else {
                    errores++;
                    rows[index].classList.add('table-danger');
                }
            });
            
            // Mostrar resultado
            if (errores === 0) {
                mostrarAlerta('success', `Se guardaron exitosamente ${notasGuardadas} notas`);
                setTimeout(() => location.reload(), 2000);
            } else {
                mostrarAlerta('warning', `Se guardaron ${notasGuardadas} notas con ${errores} errores`);
            }
        })
        .finally(() => {
            btnGuardarTodas.disabled = false;
            btnGuardarTodas.innerHTML = '<i class="demo-pli-save me-1"></i> Guardar Todas';
            
            // Limpiar clases después de 3 segundos
            setTimeout(() => {
                rows.forEach(row => {
                    row.classList.remove('table-success', 'table-danger');
                });
            }, 3000);
        });
}

// Función para mostrar alertas
function mostrarAlerta(tipo, mensaje) {
    const alertaHtml = `
        <div class="alert alert-${tipo === 'error' ? 'danger' : tipo} alert-dismissible fade show" role="alert">
            <i class="demo-pli-${tipo === 'success' ? 'check-circle-2' : 'exclamation-triangle'} me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const contenedor = document.querySelector('.content__wrap');
    const primeraCard = contenedor.querySelector('.card');
    primeraCard.insertAdjacentHTML('beforebegin', alertaHtml);
    
    // Auto cerrar después de 5 segundos
    setTimeout(() => {
        const alerta = contenedor.querySelector('.alert');
        if (alerta) {
            bootstrap.Alert.getOrCreateInstance(alerta).close();
        }
    }, 5000);
}
</script>
<?= $this->endSection() ?>