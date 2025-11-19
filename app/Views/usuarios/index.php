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
                    <h5 class="alert-heading">Errores de Validación</h5>
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">
                            <i class="demo-pli-male fs-5 me-2 text-primary"></i> Listado de Usuarios
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="<?= site_url('usuarios/exportar-excel') ?>" class="btn btn-success">
                                <i class="demo-pli-download fs-5 me-2"></i> Exportar Excel
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                                <i class="demo-pli-add-user fs-5 me-2"></i> Crear Usuario
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre Completo</th>
                                    <th>Contacto</th>
                                    <th>Escuela Profesional</th>
                                    <th class="text-center">Rol</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="demo-pli-information fs-2 mb-2 d-block"></i>
                                            No hay usuarios registrados.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <strong><?= esc($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></strong>
                                            </td>
                                            <td class="align-middle">
                                                <small class="d-block"><i class="demo-pli-mail me-1"></i><?= esc($usuario['correo']) ?></small>
                                                <small class="d-block"><i class="demo-pli-id-card me-1"></i>DNI: <?= esc($usuario['dni']) ?></small>
                                            </td>
                                            <td class="align-middle">
                                                <small><?= esc($usuario['escuela_profesional'] ?? '-') ?></small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php
                                                $badgeColor = match($usuario['rol_nombre']) {
                                                    'superadmin' => 'bg-danger',
                                                    'admin' => 'bg-warning text-dark',
                                                    'docente' => 'bg-info',
                                                    'usuario' => 'bg-primary',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="badge <?= $badgeColor ?> text-capitalize"><?= esc($usuario['rol_nombre']) ?></span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?= $usuario['estado'] == '1'
                                                    ? '<span class="badge bg-success">Activo</span>'
                                                    : '<span class="badge bg-danger">Inactivo</span>' ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="btn-group" role="group">
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
                                                        <form action="<?= site_url('usuarios/' . $usuario['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario? Esta acción no se puede deshacer.');">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar Usuario">
                                                                <i class="demo-psi-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
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

<!-- MODAL CREAR USUARIO -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createUserModalLabel">
                    <i class="demo-pli-add-user me-2"></i>Crear Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('usuarios') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Nombres -->
                        <div class="col-md-6">
                            <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required>
                        </div>

                        <!-- Apellidos -->
                        <div class="col-md-6">
                            <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                        </div>

                        <!-- Correo -->
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>

                        <!-- DNI -->
                        <div class="col-md-6">
                            <label for="dni" class="form-label">DNI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="dni" name="dni" maxlength="8" pattern="[0-9]{8}" required>
                            <small class="text-muted">Debe tener 8 dígitos</small>
                        </div>

                        <!-- Escuela Profesional -->
                        <div class="col-md-12">
                            <label for="escuela_profesional" class="form-label">Escuela Profesional</label>
                            <input type="text" class="form-control" id="escuela_profesional" name="escuela_profesional">
                        </div>

                        <!-- Rol -->
                        <div class="col-md-6">
                            <label for="rol_id" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select" id="rol_id" name="rol_id" required>
                                <option value="">Seleccionar rol...</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol['id'] ?>"><?= esc($rol['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <!-- Contraseña -->
                        <div class="col-md-12">
                            <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="demo-pli-close me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="demo-pli-check me-2"></i>Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR USUARIO -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="demo-psi-pen-5 me-2"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Nombres -->
                        <div class="col-md-6">
                            <label for="edit_nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nombres" name="nombres" required>
                        </div>

                        <!-- Apellidos -->
                        <div class="col-md-6">
                            <label for="edit_apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_apellidos" name="apellidos" required>
                        </div>

                        <!-- Correo -->
                        <div class="col-md-6">
                            <label for="edit_correo" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_correo" name="correo" required>
                        </div>

                        <!-- DNI -->
                        <div class="col-md-6">
                            <label for="edit_dni" class="form-label">DNI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_dni" name="dni" maxlength="8" pattern="[0-9]{8}" required>
                            <small class="text-muted">Debe tener 8 dígitos</small>
                        </div>

                        <!-- Escuela Profesional -->
                        <div class="col-md-12">
                            <label for="edit_escuela" class="form-label">Escuela Profesional</label>
                            <input type="text" class="form-control" id="edit_escuela" name="escuela_profesional">
                        </div>

                        <!-- Rol -->
                        <div class="col-md-6">
                            <label for="edit_rol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_rol" name="rol_id" required>
                                <option value="">Seleccionar rol...</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol['id'] ?>"><?= esc($rol['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label for="edit_estado" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_estado" name="estado" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <!-- Contraseña (opcional) -->
                        <div class="col-md-12">
                            <label for="edit_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="edit_password" name="password" minlength="6">
                            <small class="text-muted">Dejar en blanco para mantener la contraseña actual. Mínimo 6 caracteres si se cambia.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="demo-pli-close me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="demo-pli-check me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    // Cargar datos en el modal de edición
    const editModal = document.getElementById('editUserModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const form = document.getElementById('editUserForm');
        
        // Establecer la acción del formulario
        form.action = `<?= site_url('usuarios/') ?>${id}`;
        
        // Llenar los campos del formulario
        document.getElementById('edit_nombres').value = button.getAttribute('data-nombres');
        document.getElementById('edit_apellidos').value = button.getAttribute('data-apellidos');
        document.getElementById('edit_correo').value = button.getAttribute('data-correo');
        document.getElementById('edit_dni').value = button.getAttribute('data-dni');
        document.getElementById('edit_escuela').value = button.getAttribute('data-escuela_profesional');
        document.getElementById('edit_rol').value = button.getAttribute('data-rol_id');
        document.getElementById('edit_estado').value = button.getAttribute('data-estado');
        document.getElementById('edit_password').value = ''; // Limpiar contraseña
    });

    // Validación adicional para DNI (solo números)
    document.querySelectorAll('input[name="dni"]').forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    // Limpiar formulario de creación al cerrar modal
    const createModal = document.getElementById('createUserModal');
    createModal.addEventListener('hidden.bs.modal', function () {
        document.querySelector('#createUserModal form').reset();
    });
});
</script>
<?= $this->endSection() ?>