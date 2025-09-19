document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalAgregarMaterial');

    // Cuando se abre el modal, cargar valores de curso y ciclo
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const curso = button.getAttribute('data-curso');
        const ciclo = button.getAttribute('data-ciclo');

        document.getElementById('modal-curso').value = curso;
        document.getElementById('modal-ciclo').value = ciclo;
    });

    // Función que alterna entre mostrar URL o archivo
    function toggleTipoInputs() {
        const tipo = document.getElementById('tipoSelect').value;
        const urlInput = document.getElementById('urlInput');
        const fileInput = document.getElementById('fileInput');

        if (tipo === 'PDF') {
            urlInput.style.display = 'none';
            urlInput.required = false;

            fileInput.style.display = 'block';
            fileInput.required = true;
        } else {
            urlInput.style.display = 'block';
            urlInput.required = true;

            fileInput.style.display = 'none';
            fileInput.required = false;
        }
    }

    // Escuchar cambios en el tipo de material
    document.getElementById('tipoSelect').addEventListener('change', toggleTipoInputs);

    // Ejecutar una vez al cargar para dejar el estado correcto
    toggleTipoInputs();

    function toggleEditTipoInputs() {
        const tipo = document.getElementById('edit-tipo').value;
        const urlGroup = document.getElementById('edit-url-group');
        const fileGroup = document.getElementById('edit-file-group');

        if (tipo === 'PDF') {
            urlGroup.classList.add('d-none');
            fileGroup.classList.remove('d-none');
        } else {
            urlGroup.classList.remove('d-none');
            fileGroup.classList.add('d-none');
        }
    }

    // Abrir modal de edición con datos rellenados
    document.querySelectorAll('.editar-material').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-nombre').value = this.dataset.nombre;
            document.getElementById('edit-tipo').value = this.dataset.tipo;
            document.getElementById('edit-tipo-hidden').value = this.dataset.tipo;
            document.getElementById('edit-url').value = this.dataset.url;
            document.getElementById('edit-curso-nombre').value = this.dataset.curso;

            toggleEditTipoInputs();

            const modal = new bootstrap.Modal(document.getElementById('modalEditarMaterial'));
            modal.show();
        });
    });

    document.getElementById('edit-tipo').addEventListener('change', toggleEditTipoInputs);

    // Cargar miniaturas de PDF
    const pdfCanvases = document.querySelectorAll("canvas[id^='pdf-preview-']");

    pdfCanvases.forEach(canvas => {
        const pdfId = canvas.id.split("pdf-preview-")[1];
        const anchor = document.querySelector(`[id^='pdf-preview-${pdfId}']`).closest("a");
        const pdfFileName = anchor.getAttribute("href").split('/').pop();
        const pdfUrl = "/assets/pdf/" + pdfFileName; // Ajusta esta ruta si es necesario

        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        loadingTask.promise.then(pdf => {
            return pdf.getPage(1).then(page => {
                const viewport = page.getViewport({ scale: 1 });
                const context = canvas.getContext('2d');

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                return page.render(renderContext).promise;
            });
        }).catch(error => {
            console.error("Error al cargar PDF: ", error);
        });
    });
});
