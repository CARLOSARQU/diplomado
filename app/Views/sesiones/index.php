<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
    Sesiones de <?= esc($modulo['nombre']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <div class="breadcrumb mb-2">
                <a href="<?= site_url('cursos') ?>">Cursos</a>
                <span class="breadcrumb-separator">/</span>
                <a href="<?= site_url('cursos/' . $curso['id'] . '/modulos') ?>"><?= esc($curso['nombre']) ?></a>
                <span class="breadcrumb-separator">/</span>
                <span><?= esc($modulo['nombre']) ?></span>
            </div>
            <h1 class="h1">Gestión de Sesiones</h1>
            <p class="text-muted">Administra las sesiones para el módulo "<?= esc($modulo['nombre']) ?>".</p>
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

            <!-- CARD SESIONES -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="demo-pli-list-view me-2 text-primary"></i> Listado de Sesiones
                        </h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSesionModal">
                            <i class="demo-pli-add me-2"></i> Crear Nueva Sesión
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Título de la Sesión</th>
                                    <th>Fecha y Hora</th>
                                    <th class="text-center">Asistencia Habilitada</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sesiones)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="demo-pli-information fs-2 mb-2 d-block"></i>
                                            Este módulo no tiene sesiones todavía.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($sesiones as $sesion): ?>
                                        <tr>
                                            <td class="align-middle"><strong><?= esc($sesion['titulo']) ?></strong></td>
                                            <td class="align-middle">
                                                <?= date('d/m/Y', strtotime($sesion['fecha'])) ?>
                                                <br class="d-md-none">
                                                <small class="text-muted">de <?= date('h:i A', strtotime($sesion['hora_inicio'])) ?> a <?= date('h:i A', strtotime($sesion['hora_fin'])) ?></small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if ($sesion['asistencia_habilitada'] == '1'): ?>
                                                    <span class="badge bg-success">Sí</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <a href="<?= site_url('sesiones/' . $sesion['id'] . '/asistencias') ?>" class="btn btn-sm btn-info" title="Ver Asistencias">
                                                    <i class="demo-psi-receipt-4"></i> Asistencias
                                                </a>

                                                <button type="button" class="btn btn-sm btn-warning edit-btn"
                                                    data-bs-toggle="modal" data-bs-target="#editSesionModal"
                                                    data-id="<?= $sesion['id'] ?>"
                                                    data-titulo="<?= esc($sesion['titulo']) ?>"
                                                    data-descripcion="<?= esc($sesion['descripcion'] ?? '') ?>"
                                                    data-fecha="<?= esc($sesion['fecha']) ?>"
                                                    data-hora_inicio="<?= esc($sesion['hora_inicio']) ?>"
                                                    data-hora_fin="<?= esc($sesion['hora_fin']) ?>"
                                                    data-asistencia_habilitada="<?= esc($sesion['asistencia_habilitada']) ?>"
                                                    title="Editar Sesión">
                                                    <i class="demo-psi-pen-5"></i>
                                                </button>

                                                <form action="<?= site_url('sesiones/delete/' . $sesion['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Estás seguro? Se eliminarán también todas las asistencias registradas para esta sesión.');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                        <i class="demo-psi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /CARD SESIONES -->

        </div>
    </div>

</section>

<!-- MODALES -->
<div class="modal fade" id="createSesionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Crear Nueva Sesión</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="<?= site_url('modulos/' . $modulo['id'] . '/sesiones') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="modulo_id" value="<?= $modulo['id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título de la Sesión</label>
                        <input type="text" class="form-control" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción (Opcional)</label>
                        <textarea class="form-control" name="descripcion" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" name="fecha" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hora de Inicio</label>
                            <input type="time" class="form-control" name="hora_inicio" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hora de Fin</label>
                            <input type="time" class="form-control" name="hora_fin" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Habilitar Asistencia</label>
                        <select name="asistencia_habilitada" class="form-select">
                            <option value="1" selected>Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Sesión</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editSesionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Editar Sesión</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="editSesionForm" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="modulo_id" value="<?= $modulo['id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título de la Sesión</label>
                        <input type="text" class="form-control" id="edit_titulo" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción (Opcional)</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="edit_fecha" name="fecha" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hora de Inicio</label>
                            <input type="time" class="form-control" id="edit_hora_inicio" name="hora_inicio" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hora de Fin</label>
                            <input type="time" class="form-control" id="edit_hora_fin" name="hora_fin" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Habilitar Asistencia</label>
                        <select id="edit_asistencia_habilitada" name="asistencia_habilitada" class="form-select">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editSesionModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');

        const form = document.getElementById('editSesionForm');
        form.action = `<?= site_url('sesiones/update/') ?>${id}`;

        document.getElementById('edit_titulo').value = button.getAttribute('data-titulo') || '';
        document.getElementById('edit_descripcion').value = button.getAttribute('data-descripcion') || '';
        document.getElementById('edit_fecha').value = button.getAttribute('data-fecha') || '';
        document.getElementById('edit_hora_inicio').value = button.getAttribute('data-hora_inicio') || '';
        document.getElementById('edit_hora_fin').value = button.getAttribute('data-hora_fin') || '';
        document.getElementById('edit_asistencia_habilitada').value = button.getAttribute('data-asistencia_habilitada') || '1';
    });

    // Cerrar alertas automáticamente después de 6 segundos (misma UX que en cursos)
    document.querySelectorAll('.alert').forEach(alertEl => {
        setTimeout(() => {
            try { bootstrap.Alert.getOrCreateInstance(alertEl).close(); } catch(e) {}
        }, 6000);
    });
});
</script>
<?= $this->endSection() ?>
