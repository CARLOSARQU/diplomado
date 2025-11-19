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
                    <?php if (empty($historial)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="demo-pli-information fs-2 mb-2 d-block"></i>
                            No tienes asistencias registradas.
                        </div>
                    <?php else: ?>
                        <?php
                        // Agrupar asistencias por curso y módulo, manteniendo el orden original del historial
                        $asistenciasPorCurso = [];
                        $ordenModulos = []; // Para mantener el orden de aparición de los módulos
                        
                        foreach ($historial as $item) {
                            $curso_nombre = $item['curso_nombre'] ?? 'N/A';
                            $modulo_nombre = $item['modulo_nombre'] ?? 'N/A';
                            
                            if (!isset($asistenciasPorCurso[$curso_nombre])) {
                                $asistenciasPorCurso[$curso_nombre] = [];
                                $ordenModulos[$curso_nombre] = [];
                            }
                            
                            if (!isset($asistenciasPorCurso[$curso_nombre][$modulo_nombre])) {
                                $asistenciasPorCurso[$curso_nombre][$modulo_nombre] = [];
                                // Guardar el orden en que aparecen los módulos
                                $ordenModulos[$curso_nombre][] = $modulo_nombre;
                            }
                            
                            $asistenciasPorCurso[$curso_nombre][$modulo_nombre][] = $item;
                        }
                        
                        // Invertir el orden de los módulos para cada curso
                        foreach ($ordenModulos as $curso => $modulos) {
                            $modulosInvertidos = array_reverse($modulos);
                            $nuevoOrden = [];
                            foreach ($modulosInvertidos as $modulo) {
                                $nuevoOrden[$modulo] = $asistenciasPorCurso[$curso][$modulo];
                            }
                            $asistenciasPorCurso[$curso] = $nuevoOrden;
                        }
                        
                        $contador = 0;
                        ?>

                        <div class="accordion" id="accordionAsistencias">
                            <?php foreach ($asistenciasPorCurso as $curso => $modulos): ?>
                                <div class="mb-3">
                                    <h5 class="mb-3 text-primary">
                                        <i class="demo-pli-book me-2"></i><?= esc($curso) ?>
                                    </h5>
                                    
                                    <?php 
                                    // Invertir el orden de los módulos para mostrar el último primero
                                    $modulosInvertidos = array_reverse($modulos, true);
                                    $moduloIndex = 0;
                                    ?>
                                    
                                    <?php foreach ($modulosInvertidos as $modulo => $asistencias): ?>
                                        <?php 
                                        $collapseId = 'collapse' . $contador;
                                        // Solo el primer módulo (que es el último cronológicamente) estará abierto
                                        $showClass = ($moduloIndex === 0) ? 'show' : '';
                                        $collapsed = ($moduloIndex === 0) ? '' : 'collapsed';
                                        $contador++;
                                        $moduloIndex++;
                                        ?>
                                        
                                        <div class="card border mb-2">
                                            <div class="card-header bg-light p-0" id="heading<?= $contador ?>">
                                                <button class="btn btn-link w-100 text-start text-decoration-none <?= $collapsed ?>" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#<?= $collapseId ?>" 
                                                        aria-expanded="<?= $showClass ? 'true' : 'false' ?>" 
                                                        aria-controls="<?= $collapseId ?>">
                                                    <div class="d-flex justify-content-between align-items-center py-2 px-3">
                                                        <span class="fw-semibold text-dark">
                                                            <i class="demo-pli-folder me-2"></i><?= esc($modulo) ?>
                                                        </span>
                                                        <span class="badge bg-primary"><?= count($asistencias) ?> sesiones</span>
                                                    </div>
                                                </button>
                                            </div>

                                            <div id="<?= $collapseId ?>" 
                                                 class="collapse <?= $showClass ?>" 
                                                 aria-labelledby="heading<?= $contador ?>" 
                                                 data-bs-parent="#accordionAsistencias">
                                                <div class="card-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-sm mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Sesión</th>
                                                                    <th>Fecha Sesión</th>
                                                                    <th>Hora Registro</th>
                                                                    <th class="text-center">Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($asistencias as $item): ?>
                                                                    <tr>
                                                                        <td class="align-middle">
                                                                            <?= esc($item['titulo'] ?? 'N/A') ?>
                                                                        </td>
                                                                        <td class="align-middle">
                                                                            <?= isset($item['fecha']) ? date('d/m/Y', strtotime($item['fecha'])) : 'N/A' ?>
                                                                            <br>
                                                                            <small class="text-muted">
                                                                                <?= isset($item['hora_inicio']) ? date('h:i A', strtotime($item['hora_inicio'])) : '' ?>
                                                                                <?= isset($item['hora_fin']) ? ' - ' . date('h:i A', strtotime($item['hora_fin'])) : '' ?>
                                                                            </small>
                                                                        </td>
                                                                        <td class="align-middle">
                                                                            <?= isset($item['hora_registro']) ? date('d/m/Y h:i A', strtotime($item['hora_registro'])) : 'N/A' ?>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <?php 
                                                                            $estado = $item['observaciones'] ?? 'presente';
                                                                            $badge_class = $estado === 'tarde' ? 'bg-warning text-dark' : 
                                                                                         ($estado === 'ausente' ? 'bg-danger' : 'bg-success');
                                                                            ?>
                                                                            <span class="badge <?= $badge_class ?>">
                                                                                <?= esc(ucfirst($estado)) ?>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?= $this->endSection() ?>