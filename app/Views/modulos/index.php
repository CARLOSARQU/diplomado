<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Módulos de <?= esc($curso['nombre']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <div class="breadcrumb mb-2">
                <a href="<?= site_url('cursos') ?>">Cursos</a>
                <span class="breadcrumb-separator">/</span>
                <span><?= esc($curso['nombre']) ?></span>
            </div>
            <h1 class="h1">Gestión de Módulos</h1>
            <p class="text-muted">Administra los módulos para el curso "<?= esc($curso['nombre']) ?>".</p>
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

            <!-- CARD MÓDULOS -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="demo-pli-list-view me-2 text-primary"></i> Listado de Módulos
                        </h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createModuloModal">
                            <i class="demo-pli-add me-2"></i> Crear Nuevo Módulo
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">Orden</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($modulos)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="demo-pli-information fs-2 mb-2 d-block"></i>
                                            Este curso no tiene módulos todavía.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($modulos as $modulo): ?>
                                        <tr>
                                            <td class="text-center align-middle">
                                                <span
                                                    class="badge bg-primary rounded-pill fs-6"><?= esc($modulo['orden']) ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <strong><?= esc($modulo['nombre']) ?></strong>
                                            </td>
                                            <td class="align-middle"><?= esc($modulo['descripcion']) ?></td>
                                            <td class="text-center align-middle">
                                                <a href="<?= site_url('modulos/' . $modulo['id'] . '/sesiones') ?>"
                                                    class="btn btn-sm btn-info" title="Gestionar Sesiones">
                                                    <i class="demo-psi-clock"></i> Sesiones
                                                </a>
                                                    <!-- Botón para gestionar notas -->
    <a href="<?= site_url('notas/modulo/' . $modulo['id']) ?>" 
        class="btn btn-sm btn-success" 
        title="Gestionar Notas">
        <i class="demo-pli-file-edit me-1"></i> Notas
    </a>
                                                <button type="button" class="btn btn-sm btn-warning edit-btn"
                                                    data-bs-toggle="modal" data-bs-target="#editModuloModal"
                                                    data-id="<?= $modulo['id'] ?>" data-nombre="<?= esc($modulo['nombre']) ?>"
                                                    data-descripcion="<?= esc($modulo['descripcion'] ?? '') ?>"
                                                    data-orden="<?= esc($modulo['orden']) ?>" title="Editar Módulo">
                                                    <i class="demo-psi-pen-5"></i>
                                                </button>
                                                <form action="<?= site_url('modulos/delete/' . $modulo['id']) ?>" method="post"
                                                    class="d-inline"
                                                    onsubmit="return confirm('¿Estás seguro de que quieres eliminar este módulo y todas sus sesiones?');">
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
        </div>
    </div>

</section>

<!-- MODALES -->
<div class="modal fade" id="createModuloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Módulo</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('cursos/' . $curso['id'] . '/modulos') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nombre</label><input type="text" class="form-control"
                            name="nombre" required></div>
                    <div class="mb-3"><label class="form-label">Descripción (Opcional)</label><textarea
                            class="form-control" name="descripcion" rows="3"></textarea></div>
                    <div class="mb-3"><label class="form-label">Orden</label><input type="number" class="form-control"
                            name="orden" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModuloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Módulo</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <form id="editModuloForm" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nombre</label><input type="text" class="form-control"
                            id="edit_nombre" name="nombre" required></div>
                    <div class="mb-3"><label class="form-label">Descripción (Opcional)</label><textarea
                            class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea></div>
                    <div class="mb-3"><label class="form-label">Orden</label><input type="number" class="form-control"
                            id="edit_orden" name="orden" required></div>
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
        const editModal = document.getElementById('editModuloModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nombre = button.getAttribute('data-nombre');
            const descripcion = button.getAttribute('data-descripcion');
            const orden = button.getAttribute('data-orden');

            const form = document.getElementById('editModuloForm');
            form.action = `<?= site_url('modulos/update/') ?>${id}`;

            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('edit_orden').value = orden;
        });

        // Cerrar alertas automáticamente después de 6 segundos
        document.querySelectorAll('.alert').forEach(alertEl => {
            setTimeout(() => {
                try { bootstrap.Alert.getOrCreateInstance(alertEl).close(); } catch (e) { }
            }, 6000);
        });
    });
</script>
<?= $this->endSection() ?>