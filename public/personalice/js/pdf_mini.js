
document.addEventListener('DOMContentLoaded', () => {    
    // Cargar miniaturas de PDF
    const pdfCanvases = document.querySelectorAll("canvas[id^='pdf-preview-']");

    pdfCanvases.forEach(canvas => {
        const pdfId = canvas.id.split("pdf-preview-")[1];
        const pdfUrl = "<?= base_url('assets/pdf/') ?>" + document.querySelector(`[id^='pdf-preview-${pdfId}']`).closest("a").getAttribute("href").split('/').pop();

        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        loadingTask.promise.then(pdf => {
            // Cargamos la primera página
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