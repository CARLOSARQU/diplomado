<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
    Asignar Docentes a <?= esc($curso['nombre']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="<?= site_url('cursos') ?>">Cursos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Asignar Docentes</li>
                        </ol>
                    </nav>
                    <h1 class="h1 mb-1">Asignar Docentes</h1>
                    <p class="text-muted mb-0">Selecciona los docentes que impartirán el curso <strong><?= esc($curso['nombre']) ?></strong>.</p>
                </div>
                <div class="text-end">
                    <a href="<?= site_url('cursos') ?>" class="btn btn-outline-secondary">Volver a Cursos</a>
                </div>
            </div>
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

            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">Docentes Disponibles</h5>
                                <small class="text-muted">Rol: Admin — Total: <?= esc(count($docentes)) ?></small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input class="form-check-input" type="checkbox" id="selectAll"> <label for="selectAll" class="form-label mb-0 ms-1">Seleccionar todo</label>
                                </div>
                                <div>
                                    <input id="filterDocentes" type="search" class="form-control" placeholder="Buscar docente (nombre, apellido)" style="min-width:220px;">
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form action="<?= site_url('cursos/asignar/' . $curso['id']) ?>" method="post" id="assignForm">
                                <?= csrf_field() ?>

                                <div class="list-group" id="docentesList" style="max-height:460px; overflow:auto;">
                                    <?php if (empty($docentes)): ?>
                                        <div class="text-center text-muted py-4">
                                            <i class="demo-pli-information fs-2"></i>
                                            <div>No hay docentes registrados con ese rol.</div>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($docentes as $docente): ?>
                                            <label class="list-group-item d-flex align-items-center docente-item">
                                                <input class="form-check-input me-3 docente-checkbox" 
                                                       type="checkbox" 
                                                       name="docentes[]" 
                                                       value="<?= $docente['id'] ?>"
                                                       <?= in_array($docente['id'], $asignados) ? 'checked' : '' ?> >
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong><?= esc($docente['nombres'] . ' ' . $docente['apellidos']) ?></strong>
                                                            <div class="small text-muted"><?= esc($docente['email'] ?? '') ?></div>
                                                        </div>
                                                        <?php if (in_array($docente['id'], $asignados)): ?>
                                                            <span class="badge bg-primary">Asignado</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <div class="mt-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="button" id="clearSelection" class="btn btn-link">Limpiar selección</button>
                                    </div>
                                    <div>
                                        <a href="<?= site_url('cursos') ?>" class="btn btn-outline-secondary">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">Guardar Asignaciones</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Resumen</h5>
                            <p class="mb-1">Curso: <strong><?= esc($curso['nombre']) ?></strong></p>
                            <p class="mb-1">Docentes totales: <strong><?= esc(count($docentes)) ?></strong></p>
                            <p class="mb-0">Asignados actualmente: <strong><?= esc(count($asignados)) ?></strong></p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Ayuda rápida</h6>
                            <ul class="small mb-0">
                                <li>Usa la búsqueda para filtrar docentes por nombre o apellido.</li>
                                <li>Selecciona "Seleccionar todo" para marcar todos los visibles.</li>
                                <li>Los cambios se guardan al presionar "Guardar Asignaciones".</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</section>

<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Select / Deselect all visible
    const selectAll = document.getElementById('selectAll');
    const filterInput = document.getElementById('filterDocentes');
    const list = document.getElementById('docentesList');

    selectAll?.addEventListener('change', function () {
        const checkboxes = list.querySelectorAll('.docente-item:not(.d-none) .docente-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    });

    // Filter list by text
    filterInput?.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        const items = list.querySelectorAll('.docente-item');
        items.forEach(item => {
            const text = item.textContent.trim().toLowerCase();
            if (q === '' || text.indexOf(q) !== -1) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
    });

    // Clear selection
    document.getElementById('clearSelection')?.addEventListener('click', function () {
        const checkboxes = list.querySelectorAll('.docente-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        selectAll.checked = false;
    });

    // Auto close alerts after 6s
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(a => setTimeout(() => { try { bootstrap.Alert.getOrCreateInstance(a).close(); } catch(e){} }, 6000));
});
</script>
<?= $this->endSection() ?>
