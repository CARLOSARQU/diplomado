<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Inscribir Participantes
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
                            <li class="breadcrumb-item active" aria-current="page">Inscribir Participantes</li>
                        </ol>
                    </nav>
                    <h1 class="h1 mb-1">Inscribir Participantes</h1>
                    <p class="text-muted mb-0">Selecciona los participantes que formarán parte del curso
                        <strong><?= esc($curso['nombre']) ?></strong>.
                    </p>
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
                    <i class="demo-pli-check-circle-2 me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="demo-pli-exclamation-triangle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="demo-pli-exclamation-triangle me-2"></i>Error</h5>
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
                                <h5 class="card-title mb-0">Participantes Disponibles</h5>
                                <small class="text-muted">Rol: Usuario — Total: <span id="totalParticipantes"><?= esc(count($participantes)) ?></span></small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <input class="form-check-input" type="checkbox" id="selectAllParticipants">
                                    <label for="selectAllParticipants" class="form-label mb-0 ms-1">Seleccionar todo</label>
                                </div>
                                <div>
                                    <input id="filterParticipantes" type="search" class="form-control"
                                        placeholder="Buscar participante (nombre, apellido, DNI)"
                                        style="min-width:220px;">
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form action="<?= site_url('cursos/guardar-inscripcion/' . $curso['id']) ?>" method="post"
                                id="enrollForm">
                                <?= csrf_field() ?>

                                <div class="list-group" id="participantesList" style="max-height:460px; overflow:auto;">
                                    <?php if (empty($participantes)): ?>
                                        <div class="text-center text-muted py-4">
                                            <i class="demo-pli-information fs-2"></i>
                                            <div class="mt-2">No hay participantes registrados en el sistema.</div>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($participantes as $participante): ?>
                                            <label class="list-group-item d-flex align-items-center participante-item"
                                                   data-participante-info="<?= esc(strtolower($participante['nombres'] . ' ' . $participante['apellidos'] . ' ' . ($participante['dni'] ?? '') . ' ' . ($participante['email'] ?? ''))) ?>">
                                                <input class="form-check-input me-3 participante-checkbox" type="checkbox"
                                                    name="participantes[]" value="<?= $participante['id'] ?>"
                                                    <?= in_array($participante['id'], $inscritos) ? 'checked' : '' ?>>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong><?= esc($participante['nombres'] . ' ' . $participante['apellidos']) ?></strong>
                                                            <div class="small text-muted">
                                                                <?php if (!empty($participante['dni'])): ?>
                                                                    DNI: <?= esc($participante['dni']) ?>
                                                                <?php endif; ?>
                                                                <?php if (!empty($participante['dni']) && !empty($participante['email'])): ?>
                                                                    ·
                                                                <?php endif; ?>
                                                                <?php if (!empty($participante['email'])): ?>
                                                                    <?= esc($participante['email']) ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <?php if (in_array($participante['id'], $inscritos)): ?>
                                                            <span class="badge bg-primary">Inscrito</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($participantes)): ?>
                                <div class="mt-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="button" id="clearSelectionParticipants" class="btn btn-link">
                                            Limpiar selección
                                        </button>
                                        <span class="text-muted small ms-2">
                                            Seleccionados: <span id="selectedCount">0</span>
                                        </span>
                                    </div>
                                    <div>
                                        <a href="<?= site_url('cursos') ?>" class="btn btn-outline-secondary">Cancelar</a>
                                        <button type="button" id="quitarSeleccionados" class="btn btn-outline-danger me-2" style="display: none;">
                                            <i class="demo-psi-user-remove"></i> Quitar Seleccionados
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="demo-psi-user-plus"></i> Guardar Inscripciones
                                        </button>
                                    </div>
                                </div>
                                <?php endif; ?>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Resumen del Curso</h5>
                            <p class="mb-1">Curso: <strong><?= esc($curso['nombre']) ?></strong></p>
                            <p class="mb-1">Participantes disponibles: <strong><?= esc(count($participantes)) ?></strong></p>
                            <p class="mb-1">Inscritos actualmente: <strong id="inscritosCount"><?= esc(count($inscritos)) ?></strong></p>
                            <p class="mb-0">
                                Estado: 
                                <span class="badge <?= $curso['estado'] == 1 ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $curso['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="demo-pli-information me-2"></i>Ayuda
                            </h6>
                            <ul class="small mb-0">
                                <li>Usa la búsqueda para filtrar participantes por nombre, apellido, DNI o email.</li>
                                <li>Selecciona "Seleccionar todo" para marcar todos los participantes visibles.</li>
                                <li>Los participantes ya inscritos aparecen marcados con una etiqueta azul.</li>
                                <li>Puedes quitar participantes individuales usando el botón "Quitar".</li>
                                <li>Los cambios se guardan al presionar "Guardar Inscripciones".</li>
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
    const selectAll = document.getElementById('selectAllParticipants');
    const filterInput = document.getElementById('filterParticipantes');
    const list = document.getElementById('participantesList');
    const clearSelectionBtn = document.getElementById('clearSelectionParticipants');
    const enrollForm = document.getElementById('enrollForm');
    const selectedCountSpan = document.getElementById('selectedCount');
    const submitBtn = document.getElementById('submitBtn');
    const quitarBtn = document.getElementById('quitarSeleccionados');

    // Función para actualizar el contador de seleccionados y botones
    function updateSelectedCount() {
        const visibleChecked = list.querySelectorAll('.participante-item:not(.d-none) .participante-checkbox:checked');
        const totalChecked = list.querySelectorAll('.participante-checkbox:checked');
        
        if (selectedCountSpan) {
            selectedCountSpan.textContent = totalChecked.length;
        }
        
        // Verificar cuántos de los seleccionados ya están inscritos
        const inscritosSeleccionados = Array.from(totalChecked).filter(cb => {
            const item = cb.closest('.participante-item');
            return item.querySelector('.badge.bg-primary');
        });

        // Mostrar/ocultar botón "Quitar Seleccionados" solo si hay inscritos seleccionados
        if (quitarBtn) {
            if (inscritosSeleccionados.length > 0) {
                quitarBtn.style.display = 'inline-block';
                quitarBtn.innerHTML = `<i class="demo-psi-user-remove"></i> Quitar Seleccionados (${inscritosSeleccionados.length})`;
            } else {
                quitarBtn.style.display = 'none';
            }
        }
        
        // Actualizar estado del botón "Seleccionar todo"
        const visibleCheckboxes = list.querySelectorAll('.participante-item:not(.d-none) .participante-checkbox');
        if (selectAll && visibleCheckboxes.length > 0) {
            selectAll.checked = visibleChecked.length === visibleCheckboxes.length;
            selectAll.indeterminate = visibleChecked.length > 0 && visibleChecked.length < visibleCheckboxes.length;
        }
    }

    // Seleccionar/Deseleccionar todos los visibles
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const visibleCheckboxes = list.querySelectorAll('.participante-item:not(.d-none) .participante-checkbox');
            visibleCheckboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateSelectedCount();
        });
    }

    // Filtrar participantes
    if (filterInput) {
        filterInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const items = list.querySelectorAll('.participante-item');
            let visibleCount = 0;

            items.forEach(item => {
                const searchText = item.getAttribute('data-participante-info') || item.textContent.toLowerCase();
                const isVisible = query === '' || searchText.indexOf(query) !== -1;
                
                item.classList.toggle('d-none', !isVisible);
                if (isVisible) visibleCount++;
            });

            // Actualizar contador y estado de "Seleccionar todo"
            updateSelectedCount();
            
            // Mostrar mensaje si no hay resultados
            const noResultsMsg = list.querySelector('.no-results-message');
            if (visibleCount === 0 && query !== '') {
                if (!noResultsMsg) {
                    const msgDiv = document.createElement('div');
                    msgDiv.className = 'text-center text-muted py-4 no-results-message';
                    msgDiv.innerHTML = '<i class="demo-pli-magnify-plus fs-2"></i><div class="mt-2">No se encontraron participantes que coincidan con la búsqueda.</div>';
                    list.appendChild(msgDiv);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        });
    }

    // Limpiar selección
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const checkboxes = list.querySelectorAll('.participante-checkbox');
            checkboxes.forEach(cb => cb.checked = false);
            if (selectAll) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
            updateSelectedCount();
        });
    }

    // Actualizar contador cuando cambie cualquier checkbox individual
    if (list) {
        list.addEventListener('change', function(e) {
            if (e.target.classList.contains('participante-checkbox')) {
                updateSelectedCount();
            }
        });
    }

    // Manejar botón "Quitar Seleccionados"
    if (quitarBtn) {
        quitarBtn.addEventListener('click', function() {
            const checkboxesSeleccionados = list.querySelectorAll('.participante-checkbox:checked');
            const inscritosParaQuitar = [];
            
            // Identificar cuáles de los seleccionados están inscritos
            checkboxesSeleccionados.forEach(cb => {
                const item = cb.closest('.participante-item');
                const badgeInscrito = item.querySelector('.badge.bg-primary');
                if (badgeInscrito) {
                    inscritosParaQuitar.push({
                        id: cb.value,
                        nombre: item.querySelector('strong').textContent
                    });
                }
            });

            if (inscritosParaQuitar.length === 0) {
                alert('No hay participantes inscritos seleccionados para quitar.');
                return;
            }

            const nombres = inscritosParaQuitar.map(p => p.nombre).join(', ');
            const mensaje = inscritosParaQuitar.length === 1 
                ? `¿Está seguro de quitar a "${nombres}" del curso?`
                : `¿Está seguro de quitar a estos ${inscritosParaQuitar.length} participantes del curso?\n\n${nombres}`;

            if (confirm(mensaje)) {
                // Deshabilitar botón y mostrar estado de carga
                quitarBtn.disabled = true;
                const originalHtml = quitarBtn.innerHTML;
                quitarBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Quitando...';

                // Crear formulario dinámico para quitar inscripciones
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= site_url('cursos/quitar-inscripciones-multiples/' . $curso['id']) ?>';
                form.style.display = 'none';

                // Agregar token CSRF
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '<?= csrf_token() ?>';
                csrfInput.value = '<?= csrf_hash() ?>';
                form.appendChild(csrfInput);

                // Agregar IDs de participantes a quitar
                inscritosParaQuitar.forEach(participante => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'participantes_quitar[]';
                    input.value = participante.id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Prevenir envío doble del formulario
    if (enrollForm && submitBtn) {
        enrollForm.addEventListener('submit', function (e) {
            // Si ya está deshabilitado, prevenir envío
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }
            
            // Deshabilitar botón y mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.dataset.originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Guardando...';
            
            // Re-habilitar el botón después de 10 segundos por si hay algún error
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalHtml || 'Guardar Inscripciones';
                }
            }, 10000);
        });
    }

    // Auto-cerrar alertas después de 8 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            try {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            } catch (e) {
                // Fallback si bootstrap no está disponible
                alert.style.display = 'none';
            }
        }, 8000);
    });

    // Inicializar contador
    updateSelectedCount();
});
</script>
<?= $this->endSection() ?>