<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Reportes de Pagos<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="content__boxed">
        <div class="content__wrap">
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('pagos') ?>">Gestión de Pagos</a></li>
                    <li class="breadcrumb-item active">Reportes</li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Reportes de Pagos</h2>
                    <p class="text-muted mb-0">Análisis y estadísticas de los comprobantes de pago</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-success" onclick="exportarReporte()">
                        <i class="fas fa-file-excel me-2"></i>Exportar Excel
                    </button>
                    <button class="btn btn-outline-danger" onclick="exportarPDF()">
                        <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                    </button>
                </div>
            </div>

            <!-- Métricas principales -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-success text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-1">INGRESOS TOTALES</h6>
                                    <h3 class="mb-0">S/ <?= number_format($ingresos, 2) ?></h3>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-coins fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-info text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-1">TOTAL COMPROBANTES</h6>
                                    <h3 class="mb-0"><?= array_sum(array_column($estadoCursoModulo, 'total')) ?></h3>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-receipt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-warning text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-1">PARTICIPANTES CON PENDIENTES</h6>
                                    <h3 class="mb-0"><?= count($participantesPendientes) ?></h3>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-user-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-gradient-primary text-white shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-1">CURSOS ACTIVOS</h6>
                                    <h3 class="mb-0"><?= count(array_unique(array_column($estadoCursoModulo, 'curso'))) ?></h3>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-graduation-cap fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Estados por curso/módulo -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-pie me-2"></i>Estados por Curso y Módulo
                            </h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-1"></i>Filtrar
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="filtrarTabla('todos')">Todos</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filtrarTabla('aprobado')">Solo Aprobados</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filtrarTabla('pendiente')">Solo Pendientes</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filtrarTabla('rechazado')">Solo Rechazados</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="tablaEstados">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Curso</th>
                                            <th>Módulo</th>
                                            <th>Estado</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="pe-3 text-end">Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalGeneral = array_sum(array_column($estadoCursoModulo, 'total'));
                                        foreach ($estadoCursoModulo as $r): 
                                            $porcentaje = $totalGeneral > 0 ? ($r['total'] / $totalGeneral) * 100 : 0;
                                            $badgeClass = match($r['estado']) {
                                                'aprobado' => 'bg-success',
                                                'rechazado' => 'bg-danger',
                                                'en_revision' => 'bg-info',
                                                default => 'bg-warning'
                                            };
                                        ?>
                                            <tr data-estado="<?= $r['estado'] ?>">
                                                <td class="ps-3">
                                                    <div class="fw-medium"><?= esc($r['curso']) ?></div>
                                                </td>
                                                <td>
                                                    <span class="text-muted"><?= esc($r['modulo']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= ucfirst($r['estado']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-bold"><?= $r['total'] ?></span>
                                                </td>
                                                <td class="pe-3 text-end">
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                                            <div class="progress-bar <?= str_replace('bg-', 'bg-', $badgeClass) ?>" 
                                                                 style="width: <?= $porcentaje ?>%"></div>
                                                        </div>
                                                        <span class="small text-muted"><?= number_format($porcentaje, 1) ?>%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participantes con pendientes -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-users me-2"></i>Participantes con Pendientes
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($participantesPendientes)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                    <h6 class="text-success">¡Excelente!</h6>
                                    <p class="text-muted mb-0">No hay participantes con pagos pendientes.</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                                    <?php foreach ($participantesPendientes as $index => $p): ?>
                                        <div class="list-group-item border-0 px-0 <?= $index < count($participantesPendientes) - 1 ? 'border-bottom' : '' ?>">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <div class="avatar-initial bg-warning rounded-circle">
                                                        <?= strtoupper(substr($p['nombres'], 0, 1)) ?>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-medium"><?= esc($p['nombres'] . ' ' . $p['apellidos']) ?></div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        <?= $p['pendientes'] ?> pendiente<?= $p['pendientes'] > 1 ? 's' : '' ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <span class="badge bg-warning">
                                                        <?= $p['pendientes'] ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de estados (placeholder) -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Distribución de Estados de Pagos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <?php
                        $estados = [];
                        foreach ($estadoCursoModulo as $r) {
                            if (!isset($estados[$r['estado']])) {
                                $estados[$r['estado']] = 0;
                            }
                            $estados[$r['estado']] += $r['total'];
                        }
                        
                        $colores = [
                            'aprobado' => 'success',
                            'pendiente' => 'warning',
                            'rechazado' => 'danger',
                            'en_revision' => 'info'
                        ];
                        ?>
                        
                        <?php foreach ($estados as $estado => $cantidad): ?>
                            <div class="col-md-3 mb-3">
                                <div class="card border-<?= $colores[$estado] ?? 'secondary' ?> shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-<?= $estado === 'aprobado' ? 'check-circle' : ($estado === 'rechazado' ? 'times-circle' : 'clock') ?> 
                                           text-<?= $colores[$estado] ?? 'secondary' ?> fa-2x mb-2"></i>
                                        <h4 class="text-<?= $colores[$estado] ?? 'secondary' ?>"><?= $cantidad ?></h4>
                                        <p class="text-muted mb-0"><?= ucfirst($estado) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Botón volver -->
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?= base_url('pagos') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Gestión de Pagos
                </a>
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Último reporte generado: <?= date('d/m/Y H:i') ?>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
function filtrarTabla(filtro) {
    const filas = document.querySelectorAll('#tablaEstados tbody tr');
    
    filas.forEach(fila => {
        const estado = fila.getAttribute('data-estado');
        if (filtro === 'todos' || estado === filtro) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}

function exportarReporte() {
    // Aquí implementarías la lógica para exportar a Excel
    alert('Funcionalidad de exportación a Excel - Por implementar');
}

function exportarPDF() {
    // Aquí implementarías la lógica para exportar a PDF
    alert('Funcionalidad de exportación a PDF - Por implementar');
}

// Asegurar que Bootstrap esté funcionando
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap no está cargado. Los dropdowns no funcionarán correctamente.');
    }
    
    // Agregar animaciones a las tarjetas de métricas
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

<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8, #6f42c1);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #6610f2);
}

.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.avatar-sm {
    width: 2rem;
    height: 2rem;
}

.avatar-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    transition: all 0.3s ease;
}
</style>

<?= $this->endSection() ?>