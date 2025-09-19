<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Subir Comprobante<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section id="content" class="content">

    <!-- HEADER -->
    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= site_url('mi-panel') ?>">Mi Panel</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('participante/mis-pagos') ?>">Mis Pagos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Subir Comprobante</li>
                </ol>
            </nav>
            <h1 class="h1">Subir Comprobante</h1>
            <h2 class="text-muted mb-0 fw-bold">
                <!--<?= esc($modulo['curso_nombre']) ?> – --><?= esc($modulo['nombre']) ?>
            </h2>
            <br>
        </div>
    </div>
    <!-- /HEADER -->

    <div class="content__boxed">
        <div class="content__wrap">

            <!-- ALERTAS -->
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <!-- /ALERTAS -->

            <div class="row">
                <!-- FORMULARIO -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="card-title mb-0 text-white">
                                <i class="fas fa-upload me-2"></i>Datos del Comprobante
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= site_url('participante/procesar-comprobante') ?>" 
                                  method="post" enctype="multipart/form-data" id="comprobanteForm">
                                <?= csrf_field() ?>
                                <input type="hidden" name="modulo_id" value="<?= esc($modulo['id']) ?>">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="monto" class="form-label">Monto (S/.) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0.1" name="monto" id="monto" 
                                               class="form-control <?= isset($errors['monto']) ? 'is-invalid' : '' ?>"
                                               value="<?= old('monto') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha_pago" class="form-label">Fecha del Pago <span class="text-danger">*</span></label>
                                        <input type="date" name="fecha_pago" id="fecha_pago" 
                                               class="form-control <?= isset($errors['fecha_pago']) ? 'is-invalid' : '' ?>"
                                               value="<?= old('fecha_pago') ?>" max="<?= date('Y-m-d') ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="comprobante" class="form-label">Archivo Comprobante <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="file" name="comprobante" id="comprobante" 
                                               class="form-control <?= isset($errors['comprobante']) ? 'is-invalid' : '' ?>"
                                               accept=".jpg,.jpeg,.png,.pdf" required>
                                        <button type="button" class="btn btn-outline-secondary" id="clearFile">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Formatos permitidos: JPG, PNG o PDF. Máx. 5MB</div>
                                    
                                    <!-- Información del archivo seleccionado -->
                                    <div id="fileInfo" class="mt-2" style="display: none;">
                                        <div class="alert alert-info py-2 mb-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file me-2"></i>
                                                <div class="flex-grow-1">
                                                    <div class="fw-medium" id="fileName"></div>
                                                    <small class="text-muted" id="fileSize"></small>
                                                </div>
                                                <div class="badge bg-success" id="fileStatus">
                                                    <i class="fas fa-check me-1"></i>Válido
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Observaciones (opcional)</label>
                                    <textarea name="observaciones" id="observaciones" rows="3" 
                                              class="form-control" placeholder="Agregue cualquier comentario adicional sobre el pago..."><?= old('observaciones') ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="<?= site_url('participante/mis-pagos') ?>" class="btn btn-outline-secondary">
                                        <i class="demo-pli-left-4 me-1"></i> Volver
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="demo-pli-upload me-1"></i> Subir Comprobante
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- VISTA PREVIA -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm" id="previewCard" style="display: none;">
                        <div class="card-header bg-info ">
                            <h6 class="card-title mb-0 text-white">
                                <i class="fas fa-eye me-2"></i>Vista Previa del Comprobante
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="previewContainer" class="text-center">
                                <!-- Aquí se mostrará la vista previa -->
                            </div>
                        </div>
                    </div>

                    <!-- Placeholder cuando no hay archivo -->
                    <div class="card border-2 border-dashed border-light" id="placeholderCard">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-cloud-upload-alt text-muted fa-4x mb-3"></i>
                            <h5 class="text-muted">Seleccione un archivo</h5>
                            <p class="text-muted mb-0">La vista previa aparecerá aquí</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-sm-down modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">Vista previa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center">
        <!-- Contenedor dinámico: o imagen o iframe/embed -->
        <img id="modalImage" class="img-fluid d-none" alt="Vista previa completa" style="max-height:90vh; object-fit:contain;">
        <iframe id="modalPdf" class="d-none" style="width:100%; height:90vh; border:0;"></iframe>
      </div>
    </div>
  </div>
</div>

</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('comprobante');
    const clearBtn = document.getElementById('clearFile');
    const previewCard = document.getElementById('previewCard');
    const placeholderCard = document.getElementById('placeholderCard');
    const previewContainer = document.getElementById('previewContainer');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileStatus = document.getElementById('fileStatus');
    const submitBtn = document.getElementById('submitBtn');

    const modalEl = document.getElementById('previewModal');
    const bsModal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const modalImage = document.getElementById('modalImage');
    const modalPdf = document.getElementById('modalPdf');

    let currentObjectUrl = null;

    // Tamaño máximo en bytes (5MB)
    const maxSize = 5 * 1024 * 1024;
    // Tipos de archivo permitidos
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (!file) {
            hidePreview();
            return;
        }

        // Validar archivo
        const isValidType = allowedTypes.includes(file.type);
        const isValidSize = file.size <= maxSize;

        // Mostrar información del archivo
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileInfo.style.display = 'block';

        if (!isValidType) {
            showError('Tipo de archivo no permitido. Use JPG, PNG o PDF.');
            return;
        }

        if (!isValidSize) {
            showError('El archivo es demasiado grande. Máximo 5MB.');
            return;
        }

        // Archivo válido
        showSuccess();
        showPreview(file);
    });

    clearBtn.addEventListener('click', function() {
        if (fileInput) fileInput.value = '';
        hidePreview();
    });

    function showPreview(file) {
        // limpiar previos object URLs
        revokeObjectUrl();

        previewContainer.innerHTML = '';

        // crear object URL (más eficiente que base64 en móviles)
        const url = URL.createObjectURL(file);
        currentObjectUrl = url;

        if (file.type === 'application/pdf') {
            showPdfPreview(url, file);
        } else {
            showImagePreview(url, file);
        }

        previewCard.style.display = 'block';
        placeholderCard.style.display = 'none';
    }

    function showImagePreview(url, file) {
        const img = document.createElement('img');
        img.src = url;
        img.className = 'img-fluid rounded shadow';
        // estilos responsivos
        img.style.maxHeight = '50vh';
        img.style.width = 'auto';
        img.style.maxWidth = '100%';
        img.alt = 'Vista previa del comprobante';

        // click para abrir modal a pantalla completa (útil en móvil)
        img.addEventListener('click', function() {
            if (!modalImage || !bsModal) {
                // fallback: abrir en nueva pestaña
                window.open(url, '_blank');
                return;
            }
            modalImage.src = url;
            modalImage.classList.remove('d-none');
            modalPdf.classList.add('d-none');
            bsModal.show();
        });

        previewContainer.appendChild(img);

        // Opcional: botón para abrir en nueva pestaña
        const openBtn = document.createElement('div');
        openBtn.className = 'mt-2 d-grid';
        openBtn.innerHTML = `<button type="button" class="btn btn-outline-primary">Ver a pantalla completa</button>`;
        openBtn.querySelector('button').addEventListener('click', () => {
            if (bsModal) {
                modalImage.src = url;
                modalImage.classList.remove('d-none');
                modalPdf.classList.add('d-none');
                bsModal.show();
            } else {
                window.open(url, '_blank');
            }
        });
        previewContainer.appendChild(openBtn);
    }

    function showPdfPreview(url, file) {
        const isMobile = window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        const pdfContainer = document.createElement('div');
        pdfContainer.className = 'text-center';

        pdfContainer.innerHTML = `
            <div class="mb-2">
                <i class="fas fa-file-pdf text-danger fa-2x mb-2"></i>
                <p class="text-muted mb-1 small">Documento PDF</p>
                <p class="fw-medium small mb-0">${file.name}</p>
                <p class="text-muted small">${formatFileSize(file.size)}</p>
            </div>
        `;

        const btnGroup = document.createElement('div');
        btnGroup.className = 'd-grid gap-2';

        const openFullBtn = document.createElement('button');
        openFullBtn.type = 'button';
        openFullBtn.className = 'btn btn-outline-primary';
        openFullBtn.innerHTML = `<i class="fas fa-external-link-alt me-2"></i> Ver a pantalla completa`;
        openFullBtn.addEventListener('click', function() {
            if (!bsModal) {
                // fallback
                window.open(url, '_blank');
                return;
            }
            // mostrar iframe dentro del modal
            modalPdf.src = url;
            modalPdf.classList.remove('d-none');
            modalImage.classList.add('d-none');
            bsModal.show();
        });

        const openTabBtn = document.createElement('button');
        openTabBtn.type = 'button';
        openTabBtn.className = 'btn btn-outline-secondary';
        openTabBtn.innerHTML = `<i class="fas fa-external-link-alt me-2"></i> Abrir en nueva pestaña`;
        openTabBtn.addEventListener('click', function() {
            window.open(url, '_blank');
        });

        btnGroup.appendChild(openFullBtn);
        btnGroup.appendChild(openTabBtn);
        pdfContainer.appendChild(btnGroup);

        // Para escritorio también agregamos iframe embebido (si no es móvil)
        if (!isMobile) {
            const iframeWrap = document.createElement('div');
            iframeWrap.className = 'mt-3';
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.height = '60vh';
            iframe.frameBorder = '0';
            iframeWrap.appendChild(iframe);
            pdfContainer.appendChild(iframeWrap);
        } else {
            // Móvil: show small note
            const info = document.createElement('div');
            info.className = 'alert alert-info mt-3';
            info.innerHTML = `<i class="fas fa-info-circle me-2"></i> Vista previa limitada en móvil. Use "Ver a pantalla completa" o "Abrir en nueva pestaña".`;
            pdfContainer.appendChild(info);
        }

        previewContainer.appendChild(pdfContainer);
    }

    function hidePreview() {
        previewCard.style.display = 'none';
        placeholderCard.style.display = 'block';
        fileInfo.style.display = 'none';
        previewContainer.innerHTML = '';
        submitBtn.disabled = false;
        fileStatus.className = 'badge bg-secondary';
        fileStatus.innerHTML = '<i class="fas fa-file me-1"></i>Sin archivo';
        revokeObjectUrl();
    }

    function revokeObjectUrl() {
        if (currentObjectUrl) {
            URL.revokeObjectURL(currentObjectUrl);
            currentObjectUrl = null;
        }
        // limpiar modal srcs
        if (modalImage) modalImage.src = '';
        if (modalPdf) modalPdf.src = '';
    }

    function showError(message) {
        fileStatus.className = 'badge bg-danger';
        fileStatus.innerHTML = '<i class="fas fa-times me-1"></i>Error';

        // Mostrar mensaje de error
        let errorDiv = document.getElementById('fileError');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'fileError';
            errorDiv.className = 'alert alert-danger py-2 mt-2';
            fileInfo.appendChild(errorDiv);
        }
        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${message}`;

        submitBtn.disabled = true;
        hidePreview();
    }

    function showSuccess() {
        fileStatus.className = 'badge bg-success';
        fileStatus.innerHTML = '<i class="fas fa-check me-1"></i>Válido';

        // Remover mensaje de error si existe
        const errorDiv = document.getElementById('fileError');
        if (errorDiv) {
            errorDiv.remove();
        }

        submitBtn.disabled = false;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Drag and drop (igual)
    const form = document.getElementById('comprobanteForm');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        form.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        form.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        form.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        form.classList.add('bg-light');
    }

    function unhighlight(e) {
        form.classList.remove('bg-light');
    }

    form.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    }

    // Validación del formulario antes del envío (igual)
    document.getElementById('comprobanteForm').addEventListener('submit', function(e) {
        const file = fileInput.files[0];
        if (!file) {
            e.preventDefault();
            alert('Por favor seleccione un archivo comprobante.');
            return false;
        }
        if (!allowedTypes.includes(file.type) || file.size > maxSize) {
            e.preventDefault();
            alert('El archivo seleccionado no es válido.');
            return false;
        }
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Subiendo...';
    });

    // Establecer fecha máxima como hoy
    document.getElementById('fecha_pago').max = new Date().toISOString().split('T')[0];

    // Cerrar modal limpia srcs
    if (modalEl) {
      modalEl.addEventListener('hidden.bs.modal', function () {
        if (modalImage) { modalImage.src = ''; modalImage.classList.add('d-none'); }
        if (modalPdf) { modalPdf.src = ''; modalPdf.classList.add('d-none'); }
      });
    }
});
</script>

<style>
/* Ajustes pequeños para mobile */
#previewContainer img {
    transition: transform 0.25s ease;
    touch-action: manipulation;
}

#previewContainer img:hover {
    transform: scale(1.02);
    cursor: pointer;
}

/* Modal fullscreen para móvil, ya maneja bootstrap con modal-fullscreen-sm-down */
.modal-fullscreen-sm-down .modal-body {
    padding: 0.5rem;
}

/* Limitar altura general de previsualización para móviles */
@media (max-width: 768px) {
    #previewContainer iframe, #previewContainer img {
        max-height: 50vh;
    }
}
</style>

<?= $this->endSection() ?>