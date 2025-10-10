<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
    Gestión de Usuarios
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <div class="breadcrumb mb-2">
                <a href="<?= site_url('dashboard') ?>">Inicio</a><span class="breadcrumb-separator">/</span>
                <span>Usuarios</span>
            </div>
            <h1 class="h1">Gestión de Usuarios</h1>
            <p class="text-muted">Crea, edita y administra a todos los usuarios del sistema.</p>
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

            <!-- CARD USUARIOS -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="demo-pli-male fs-5 me-2 text-primary"></i> Listado de Usuarios
                        </h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="demo-pli-add-user fs-5 me-2"></i> Crear Nuevo Usuario
                        </button>
                        <a href="<?= site_url('usuarios/exportar-excel') ?>" class="btn btn-success">
    <i class="demo-pli-download fs-5 me-2"></i> Exportar a Excel
</a>

                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre Completo</th>
                                    <th>Contacto</th>
                                    <th class="text-center">Rol</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="demo-pli-information fs-2 mb-2 d-block"></i>
                                            No hay usuarios registrados.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td class="align-middle"><strong><?= esc($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></strong></td>
                                            <td class="align-middle">
                                                <small>Correo: <?= esc($usuario['correo']) ?><br>DNI: <?= esc($usuario['dni']) ?></small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-purple text-capitalize"><?= esc($usuario['rol_nombre']) ?></span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?= $usuario['estado'] == '1'
                                                    ? '<span class="badge bg-success">Activo</span>'
                                                    : '<span class="badge bg-danger">Inactivo</span>' ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-sm btn-warning edit-btn"
                                                    data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                    data-id="<?= $usuario['id'] ?>"
                                                    data-nombres="<?= esc($usuario['nombres']) ?>"
                                                    data-apellidos="<?= esc($usuario['apellidos']) ?>"
                                                    data-correo="<?= esc($usuario['correo']) ?>"
                                                    data-dni="<?= esc($usuario['dni']) ?>"
                                                    data-escuela_profesional="<?= esc($usuario['escuela_profesional'] ?? '') ?>"
                                                    data-rol_id="<?= $usuario['rol_id'] ?>"
                                                    data-estado="<?= $usuario['estado'] ?>"
                                                    title="Editar Usuario">
                                                    <i class="demo-psi-pen-5"></i>
                                                </button>
                                                <?php if ($usuario['id'] != session('user_id')): ?>
                                                    <form action="<?= site_url('usuarios/' . $usuario['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="demo-psi-trash"></i></button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /CARD USUARIOS -->

        </div>
    </div>

</section>

<!-- MODALES -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Crear Nuevo Usuario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="<?= site_url('usuarios') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <!-- Campos del formulario de creación -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Editar Usuario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="editUserForm" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <!-- Campos del formulario de edición -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /MODALES -->

<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.alert').forEach(alertEl => {
        setTimeout(() => {
            try { bootstrap.Alert.getOrCreateInstance(alertEl).close(); } catch(e) {}
        }, 6000);
    });

    const editModal = document.getElementById('editUserModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const form = document.getElementById('editUserForm');
        form.action = `<?= site_url('usuarios/') ?>${id}`;
        document.getElementById('edit_nombres').value = button.getAttribute('data-nombres');
        document.getElementById('edit_apellidos').value = button.getAttribute('data-apellidos');
        document.getElementById('edit_correo').value = button.getAttribute('data-correo');
        document.getElementById('edit_dni').value = button.getAttribute('data-dni');
        document.getElementById('edit_escuela').value = button.getAttribute('data-escuela_profesional');
        document.getElementById('edit_rol').value = button.getAttribute('data-rol_id');
        document.getElementById('edit_estado').value = button.getAttribute('data-estado');
    });
});
</script>
<?= $this->endSection() ?>
