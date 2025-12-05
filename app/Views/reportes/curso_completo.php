<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Reporte de Participantes<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* Estilos personalizados */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #6610f2);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #6f42c1);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
    }

    .table-header-modulo {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .nota-aprobada {
        color: #0d6efd;
        font-weight: bold;
    }

    .nota-desaprobada {
        color: #dc3545;
        font-weight: bold;
    }

    .monto-pagado {
        color: #198754;
    }

    .monto-cero {
        color: #adb5bd;
    }

    /* Sticky columns */
    .sticky-col {
        position: sticky;
        left: 0;
        background-color: #fff;
        z-index: 10;
        border-right: 2px solid #dee2e6 !important;
    }

    /* Estilo específico para retirados */
    .row-retirado {
        background-color: #ffeaea !important;
        color: #b02a37;
    }

    .row-retirado .sticky-col {
        background-color: #ffeaea !important;
    }

    .badge-retirado {
        background-color: #dc3545;
        color: white;
        font-size: 0.7rem;
        margin-left: 5px;
        padding: 2px 5px;
        border-radius: 4px;
    }
</style>

<section class="content">
    <div class="content__boxed">
        <div class="content__wrap">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('cursos') ?>">Cursos</a></li>
                    <li class="breadcrumb-item active">Reporte General</li>
                </ol>
            </nav>

            <!-- Alertas de éxito/error -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Reporte: <?= esc($curso['nombre']) ?></h2>
                    <p class="text-muted mb-0">Detalle consolidado de Notas y Pagos por Módulo</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-success" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>

                    <!-- ACTUALIZA ESTE BOTÓN -->
                    <a href="<?= site_url('reportes/exportar-excel/' . $curso['id']) ?>" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>Descargar Excel
                    </a>
                </div>
            </div>

            <!-- KPIs -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-primary text-white shadow h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-1">PARTICIPANTES</h6>
                                    <h3 class="mb-0"><?= esc($total_participantes) ?></h3>
                                </div>
                                <div class="text-white-50"><i class="fas fa-users fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-success text-white shadow h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-1">RECAUDACIÓN TOTAL</h6>
                                    <h3 class="mb-0">S/ <?= number_format($total_recaudado, 2) ?></h3>
                                </div>
                                <div class="text-white-50"><i class="fas fa-money-bill-wave fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-info text-white shadow h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-1">MÓDULOS</h6>
                                    <h3 class="mb-0"><?= esc($total_modulos) ?></h3>
                                </div>
                                <div class="text-white-50"><i class="fas fa-layer-group fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-warning text-white shadow h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-1">PROM. APROBACIÓN</h6>
                                    <h3 class="mb-0"><?= esc($porcentaje_aprobacion) ?>%</h3>
                                </div>
                                <div class="text-white-50"><i class="fas fa-chart-line fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla Principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Matriz de Pagos y Notas</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <!-- Encabezados Fijos -->
                                    <th rowspan="2" class="align-middle" style="width: 50px;">N°</th>
                                    <th rowspan="2" class="align-middle" style="width: 100px;">DNI</th>
                                    <th rowspan="2" class="align-middle sticky-col text-start ps-3"
                                        style="min-width: 250px;">
                                        APELLIDOS Y NOMBRES
                                    </th>

                                    <!-- Módulos Dinámicos -->
                                    <?php foreach ($modulos as $mod): ?>
                                        <th colspan="2" class="table-header-modulo py-2">
                                            MÓDULO <?= esc($mod['orden']) ?>
                                            <div class="small text-muted fw-normal text-truncate"
                                                style="max-width: 150px; margin: 0 auto;">
                                                <?= esc($mod['nombre']) ?>
                                            </div>
                                        </th>
                                    <?php endforeach; ?>

                                    <!-- NUEVA COLUMNA: Acciones -->
                                    <th rowspan="2" class="align-middle"
                                        style="width: 100px; background-color: #f8f9fa;">ACCIONES</th>
                                </tr>
                                <tr>
                                    <!-- Sub-columnas Dinámicas -->
                                    <?php foreach ($modulos as $mod): ?>
                                        <th style="font-size: 0.8rem; width: 80px;">MONTO</th>
                                        <th style="font-size: 0.8rem; width: 60px;">NOTA</th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reporte)): ?>
                                    <tr>
                                        <td colspan="<?= 4 + (count($modulos) * 2) ?>" class="text-center py-5">
                                            <i class="fas fa-user-slash text-muted fa-3x mb-3"></i>
                                            <p class="text-muted">No hay participantes registrados en este curso.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reporte as $fila): ?>
                                        <?php
                                        // Lógica visual para retirados
                                        $esRetirado = ($fila['activo'] == 0);
                                        $claseFila = $esRetirado ? 'row-retirado' : '';

                                        // Obtener el ID del usuario real para el formulario (usamos el DNI para buscarlo o idealmente pasar el ID en el controller)
                                        // En el controller estamos pasando el DNI y nombre, pero necesitamos el ID.
                                        // NOTA: Asumo que en el array $fila del controlador agregaste 'participante_id'. 
                                        // Si no, necesitamos agregarlo en el controlador (ver abajo nota).
                                        ?>
                                        <tr class="<?= $claseFila ?>">
                                            <td class="text-center fw-bold text-muted"><?= esc($fila['numero']) ?></td>
                                            <td class="text-center font-monospace"><?= esc($fila['dni']) ?></td>
                                            <td class="sticky-col fw-medium ps-3">
                                                <?php if ($esRetirado): ?>
                                                    <span style="text-decoration: line-through;">
                                                        <?= esc($fila['nombre']) ?>
                                                    </span>
                                                    <span class="badge-retirado">RETIRADO</span>
                                                <?php else: ?>
                                                    <?= esc($fila['nombre']) ?>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Datos Módulos -->
                                            <?php foreach ($fila['modulos'] as $data): ?>
                                                <td class="text-center">
                                                    <?php if ($data['monto'] > 0): ?>
                                                        <span class="monto-pagado fw-bold">S/
                                                            <?= number_format($data['monto'], 0) ?></span>
                                                    <?php else: ?>
                                                        <span class="monto-cero">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center bg-light">
                                                    <?php if ($data['nota'] !== null): ?>
                                                        <span class="<?= $data['nota'] >= 11 ? 'nota-aprobada' : 'nota-desaprobada' ?>">
                                                            <?= esc($data['nota']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted small">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>

                                            <!-- CELDA DE ACCIONES -->
                                            <td class="text-center align-middle">
                                                <form action="<?= site_url('reportes/cambiar-estado-participante') ?>"
                                                    method="post"
                                                    onsubmit="return confirm('¿Estás seguro de que deseas <?= $esRetirado ? 'reactivar' : 'retirar' ?> a este participante?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="curso_id" value="<?= esc($curso['id']) ?>">

                                                    <!-- NOTA CRÍTICA: Debes asegurarte que 'participante_id' viaja desde el controller en $fila -->
                                                    <!-- En el paso anterior del controller, agregué el ID a $fila? -->
                                                    <!-- Voy a corregir el controller abajo por si acaso para asegurar que el ID viaje -->
                                                    <input type="hidden" name="participante_id"
                                                        value="<?= isset($fila['participante_id']) ? esc($fila['participante_id']) : '' ?>">

                                                    <?php if ($esRetirado): ?>
                                                        <input type="hidden" name="nuevo_estado" value="1">
                                                        <button type="submit" class="btn btn-xs btn-outline-success"
                                                            title="Reactivar Participante">
                                                            <i class="fas fa-undo-alt"></i> Reactivar
                                                        </button>
                                                    <?php else: ?>
                                                        <input type="hidden" name="nuevo_estado" value="0">
                                                        <button type="submit" class="btn btn-xs btn-outline-danger"
                                                            title="Retirar Participante">
                                                            <i class="fas fa-user-times"></i> Retirar
                                                        </button>
                                                    <?php endif; ?>
                                                </form>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light text-muted small">
                    <div class="d-flex justify-content-between">
                        <span>
                            <i class="fas fa-info-circle me-1"></i>
                            Notas en <span class="text-danger fw-bold">rojo</span>: desaprobado.
                            Participantes en <span style="color: #b02a37; font-weight: bold;">rojo</span>: retirados.
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Script para animaciones y cierre de alertas -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Cerrar alertas automáticamente
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(a => setTimeout(() => {
            try { bootstrap.Alert.getOrCreateInstance(a).close(); } catch (e) { }
        }, 5000));

        // Animación simple de entrada
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.3s ease';

                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 50);
            }, index * 100);
        });
    });
</script>
<?= $this->endSection() ?>