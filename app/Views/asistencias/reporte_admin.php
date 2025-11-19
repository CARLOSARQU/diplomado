<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
    Reporte de Asistencias
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <div class="breadcrumb mb-2">
                <a href="<?= site_url('dashboard') ?>">Inicio</a><span class="breadcrumb-separator">/</span>
                <span>Reporte de Asistencias</span>
            </div>
            <h1 class="h1">Reporte de Asistencias</h1>
            <p class="text-muted">Consulta y exporta los registros de asistencia del sistema.</p>
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

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <!-- /ALERTAS -->

            <!-- CARD FILTROS -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0">
                        <i class="demo-pli-filter-2 fs-5 me-2 text-primary"></i> Filtros de Búsqueda
                    </h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= site_url('asistencias/reporte') ?>">
                        <div class="row g-3">
                            <!-- Curso -->
                            <div class="col-md-4">
                                <label for="curso_id" class="form-label">Curso</label>
                                <select class="form-select" id="curso_id" name="curso_id">
                                    <option value="">Todos los cursos</option>
                                    <?php foreach ($cursos as $curso): ?>
                                        <option value="<?= $curso['id'] ?>" 
                                            <?= $filtros['curso_id'] == $curso['id'] ? 'selected' : '' ?>>
                                            <?= esc($curso['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Fecha Inicio -->
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                    value="<?= esc($filtros['fecha_inicio'] ?? '') ?>" required>
                            </div>

                            <!-- Fecha Fin -->
                            <div class="col-md-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                    value="<?= esc($filtros['fecha_fin'] ?? '') ?>" required>
                            </div>

                            <!-- Botones -->
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="demo-pli-magnifi-glass me-2"></i>Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /CARD FILTROS -->

            <!-- CARD RESULTADOS -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">
                            <i class="demo-pli-file-csv fs-5 me-2 text-primary"></i> 
                            Resultados del Reporte
                            <?php if (!empty($asistencias)): ?>
                                <span class="badge bg-primary ms-2"><?= count($asistencias) ?> registros</span>
                            <?php endif; ?>
                        </h4>
                        
                        <?php if (!empty($asistencias)): ?>
                            <div class="d-flex gap-2">
                                <button onclick="exportarExcel()" class="btn btn-success">
                                    <i class="demo-pli-file-excel fs-5 me-2"></i> Exportar Excel
                                </button>
                                <button onclick="imprimirReporte()" class="btn btn-secondary">
                                    <i class="demo-pli-printer fs-5 me-2"></i> Imprimir
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <?php if (empty($filtros['fecha_inicio']) || empty($filtros['fecha_fin'])): ?>
                        <div class="text-center py-5">
                            <i class="demo-pli-information fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">Selecciona un rango de fechas para generar el reporte</h5>
                            <p class="text-muted">Utiliza los filtros de búsqueda para consultar las asistencias.</p>
                        </div>
                    <?php elseif (empty($asistencias)): ?>
                        <div class="text-center py-5">
                            <i class="demo-pli-folder-open fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">No se encontraron asistencias</h5>
                            <p class="text-muted">No hay registros de asistencia para los filtros seleccionados.</p>
                        </div>
                    <?php else: ?>
                        <!-- Resumen estadístico -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white border-0">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1"><?= count($asistencias) ?></h3>
                                        <small>Total Asistencias</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white border-0">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">
                                            <?= count(array_filter($asistencias, fn($a) => $a['observaciones'] === 'presente')) ?>
                                        </h3>
                                        <small>Presentes</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white border-0">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">
                                            <?= count(array_filter($asistencias, fn($a) => $a['observaciones'] === 'tarde')) ?>
                                        </h3>
                                        <small>Tardanzas</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white border-0">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">
                                            <?= count(array_filter($asistencias, fn($a) => $a['observaciones'] === 'ausente')) ?>
                                        </h3>
                                        <small>Ausentes</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de asistencias -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle" id="tablaAsistencias">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Sesión</th>
                                        <th>Curso</th>
                                        <th>Módulo</th>
                                        <th>Participante</th>
                                        <th>DNI</th>
                                        <th class="text-center">Hora Registro</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($asistencias as $asistencia): ?>
                                        <tr>
                                            <td>
                                                <strong><?= date('d/m/Y', strtotime($asistencia['fecha'])) ?></strong>
                                            </td>
                                            <td>
                                                <?= esc($asistencia['titulo']) ?>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="demo-pli-clock me-1"></i>
                                                    <?= date('H:i', strtotime($asistencia['hora_inicio'])) ?> - 
                                                    <?= date('H:i', strtotime($asistencia['hora_fin'])) ?>
                                                </small>
                                            </td>
                                            <td><?= esc($asistencia['curso_nombre']) ?></td>
                                            <td><small><?= esc($asistencia['modulo_nombre']) ?></small></td>
                                            <td>
                                                <strong><?= esc($asistencia['nombres'] . ' ' . $asistencia['apellidos']) ?></strong>
                                            </td>
                                            <td><?= esc($asistencia['dni']) ?></td>
                                            <td class="text-center">
                                                <small><?= date('H:i:s', strtotime($asistencia['hora_registro'])) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $badgeClass = match($asistencia['observaciones']) {
                                                    'presente' => 'bg-success',
                                                    'tarde' => 'bg-warning',
                                                    'ausente' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="badge <?= $badgeClass ?> text-capitalize">
                                                    <?= esc($asistencia['observaciones'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- /CARD RESULTADOS -->

        </div>
    </div>

</section>

<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-cerrar alertas después de 6 segundos
    document.querySelectorAll('.alert').forEach(alertEl => {
        setTimeout(() => {
            try { 
                bootstrap.Alert.getOrCreateInstance(alertEl).close(); 
            } catch(e) {}
        }, 6000);
    });

    // Validar que fecha_fin no sea menor a fecha_inicio
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');

    fechaInicio.addEventListener('change', function() {
        fechaFin.min = this.value;
    });

    fechaFin.addEventListener('change', function() {
        if (fechaInicio.value && this.value < fechaInicio.value) {
            alert('La fecha de fin no puede ser menor a la fecha de inicio');
            this.value = fechaInicio.value;
        }
    });
});

// Función para exportar a Excel
function exportarExcel() {
    const params = new URLSearchParams(window.location.search);
    const url = '<?= site_url('asistencias/exportar-reporte-excel') ?>?' + params.toString();
    window.location.href = url;
}

// Función para imprimir
function imprimirReporte() {
    window.print();
}
</script>

<style>
@media print {
    /* Ocultar elementos innecesarios al imprimir */
    .content__header,
    .card-header .btn-group,
    .btn,
    .breadcrumb,
    nav,
    aside,
    footer {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px;
    }
    
    .badge {
        border: 1px solid #000;
    }
}
</style>
<?= $this->endSection() ?>