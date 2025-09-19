<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
    Gestión de Cursos
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <h1 class="h1">Gestión de Cursos</h1>
            <p class="text-muted">Administra y organiza los cursos del sistema.</p>
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
                    <h5 class="alert-heading">Error de Validación</h5>
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <!-- /ALERTAS -->

            <!-- CARD CURSOS -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="demo-pli-list-view me-2 text-primary"></i> Listado de Cursos
                        </h4>
                        <?php if (session('rol_nombre') === 'superadmin' || session('rol_nombre') === 'admin'): ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                                <i class="demo-pli-add me-2"></i> Crear Nuevo Curso
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Nombre del Curso</th>
                                    <th>Fechas</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($cursos)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="demo-pli-information fs-2 mb-2 d-block"></i>
                                            No hay cursos registrados todavía.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($cursos as $curso): ?>
                                        <tr>
                                            <td class="text-center align-middle"><?= esc($curso['id']) ?></td>
                                            <td class="align-middle">
                                                <strong><?= esc($curso['nombre']) ?></strong><br>
                                                <small class="text-muted"><?= esc($curso['descripcion']) ?></small>
                                            </td>
                                            <td class="align-middle">
                                                <small>
                                                    Inicio: <?= date('d/m/Y', strtotime($curso['fecha_inicio'])) ?><br>
                                                    Fin: <?= date('d/m/Y', strtotime($curso['fecha_fin'])) ?>
                                                </small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge <?= $curso['estado'] == '1' ? 'bg-success' : 'bg-danger' ?>">
                                                    <?= $curso['estado'] == '1' ? 'Activo' : 'Inactivo' ?>
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <a href="<?= site_url('cursos/' . $curso['id'] . '/modulos') ?>" class="btn btn-sm btn-info" title="Gestionar Módulos">
                                                    <i class="demo-psi-diagram"></i> Módulos
                                                </a>

                                                <button type="button" class="btn btn-sm btn-warning edit-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editCourseModal"
                                                    data-id="<?= $curso['id'] ?>"
                                                    data-nombre="<?= esc($curso['nombre']) ?>"
                                                    data-descripcion="<?= esc($curso['descripcion']) ?>"
                                                    data-fecha_inicio="<?= esc($curso['fecha_inicio']) ?>"
                                                    data-fecha_fin="<?= esc($curso['fecha_fin']) ?>"
                                                    data-estado="<?= esc($curso['estado']) ?>"
                                                    title="Editar Curso">
                                                    <i class="demo-psi-pen-5"></i>
                                                </button>

                                                <?php if (session('rol_nombre') === 'superadmin'): ?>
                                                    <a href="<?= site_url('cursos/asignar/' . $curso['id']) ?>" class="btn btn-sm btn-success" title="Asignar Docentes">
                                                        <i class="demo-psi-user-add"></i>
                                                    </a>
                                                    <a href="<?= site_url('cursos/inscribir/' . $curso['id']) ?>" class="btn btn-sm btn-purple" title="Inscribir Participantes">
                                                        <i class="demo-psi-add-user"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <form action="<?= site_url('cursos/delete/' . $curso['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este curso?');">
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
            <!-- /CARD CURSOS -->

        </div>
    </div>

</section>

<!-- MODALES: CREATE -->
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Curso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('cursos') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Curso</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Curso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODALES: EDIT -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Curso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCourseForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Curso (No se puede cambiar)</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="edit_fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" id="edit_fecha_fin" name="fecha_fin" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select id="edit_estado" name="estado" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
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
    const editCourseModal = document.getElementById('editCourseModal');
    editCourseModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const cursoId = button.getAttribute('data-id');
        document.getElementById('edit_nombre').value = button.getAttribute('data-nombre');
        document.getElementById('edit_descripcion').value = button.getAttribute('data-descripcion');
        document.getElementById('edit_fecha_inicio').value = button.getAttribute('data-fecha_inicio');
        document.getElementById('edit_fecha_fin').value = button.getAttribute('data-fecha_fin');
        document.getElementById('edit_estado').value = button.getAttribute('data-estado');
        // Actualiza la acción del formulario al endpoint correspondiente
        document.getElementById('editCourseForm').action = `<?= site_url('cursos/update/') ?>${cursoId}`;
    });

    // Mejora visual: cerrar alertas automáticamente después de 6s
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(a => setTimeout(() => {
        try { bootstrap.Alert.getOrCreateInstance(a).close(); } catch(e) {}
    }, 6000));
});
</script>
<?= $this->endSection() ?>
