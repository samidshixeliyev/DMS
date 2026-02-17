/* ═══════════════ DOCUMENT PREVIEW ═══════════════
 * PDF  → iframe inline
 * DOCX → fetch raw bytes → mammoth.js → HTML
 * DOC  → fetch /preview (backend converts to docx) → mammoth.js → HTML
 */
function previewDocument(attId, fileName, mimeType) {
    var ext = fileName.split('.').pop().toLowerCase();
    var isPdf = (mimeType && mimeType.indexOf('pdf') !== -1) || ext === 'pdf';
    var isDocx = ext === 'docx';
    var isDoc = ext === 'doc';

    // Set modal title and download link
    document.getElementById('previewModalTitle').innerHTML =
        '<i class="bi bi-file-earmark-text me-2"></i>' + escapeHtml(fileName);
    document.getElementById('previewDownloadBtn').href =
        '/executor/attachments/' + attId + '/download';

    // Show loading
    document.getElementById('previewModalBody').innerHTML =
        '<div class="preview-loading">' +
            '<div class="spinner-border text-primary"></div>' +
            '<p class="mt-3">Sənəd yüklənir...</p>' +
        '</div>';

    var previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    previewModal.show();

    if (isPdf) {
        // PDF: iframe
        document.getElementById('previewModalBody').innerHTML =
            '<iframe id="previewFrame" src="/executor/attachments/' + attId + '/preview#toolbar=1"></iframe>';

    } else if (isDocx || isDoc) {
        // Word: fetch bytes → mammoth.js
        // For .doc backend converts to .docx first via /preview
        var fetchUrl = isDoc
            ? '/executor/attachments/' + attId + '/preview'
            : '/executor/attachments/' + attId + '/download';

        fetch(fetchUrl)
            .then(function(response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                var ct = response.headers.get('content-type') || '';
                if (ct.indexOf('json') !== -1) {
                    // Backend returned error JSON
                    return response.json().then(function(j) { throw new Error(j.message || 'Çevrilmə xətası'); });
                }
                return response.arrayBuffer();
            })
            .then(function(arrayBuffer) {
                return mammoth.convertToHtml({ arrayBuffer: arrayBuffer });
            })
            .then(function(result) {
                var warnings = '';
                if (result.messages && result.messages.length > 0) {
                    // Ignore minor warnings
                }
                document.getElementById('previewModalBody').innerHTML =
                    '<div id="wordPreviewContainer">' +
                        '<div class="alert alert-info py-2 mb-3" style="font-size:0.8rem;">' +
                            '<i class="bi bi-info-circle me-1"></i> ' +
                            (isDoc ? '.doc faylının çevrilmiş önizləməsidir. ' : 'Word sənədinin önizləməsidir. ') +
                            'Tam formatlaşdırma üçün sənədi endirin.' +
                        '</div>' +
                        '<div class="word-render-area">' + result.value + '</div>' +
                    '</div>';
            })
            .catch(function(err) {
                document.getElementById('previewModalBody').innerHTML =
                    '<div class="text-center py-5">' +
                        '<i class="bi bi-exclamation-triangle text-warning" style="font-size:3rem;"></i>' +
                        '<p class="mt-3 text-muted">' + escapeHtml(err.message || 'Bu sənədi önizləmək mümkün olmadı.') + '</p>' +
                        '<a href="/executor/attachments/' + attId + '/download" class="btn btn-primary mt-2">' +
                            '<i class="bi bi-download me-1"></i> Sənədi endir' +
                        '</a>' +
                    '</div>';
            });

    } else {
        // Unsupported format
        document.getElementById('previewModalBody').innerHTML =
            '<div class="text-center py-5">' +
                '<i class="bi bi-file-earmark text-secondary" style="font-size:3rem;"></i>' +
                '<p class="mt-3 text-muted">Bu fayl formatı önizlənə bilmir.</p>' +
                '<a href="/executor/attachments/' + attId + '/download" class="btn btn-primary mt-2">' +
                    '<i class="bi bi-download me-1"></i> Endir' +
                '</a>' +
            '</div>';
    }
}

/**
 * Build attachment HTML for timeline items.
 */
function buildAttachmentHtml(attachments) {
    if (!attachments || attachments.length === 0) return '';
    var html = '';
    attachments.forEach(function(a) {
        var ext = a.name.split('.').pop().toLowerCase();
        var iconClass = ext === 'pdf' ? 'bi-file-earmark-pdf text-danger'
            : (ext === 'doc' || ext === 'docx') ? 'bi-file-earmark-word text-primary'
            : 'bi-file-earmark text-secondary';
        var sizeText = a.size ? ' <small class="text-muted">(' + a.size + ')</small>' : '';
        var safeFileName = escapeHtml(a.name).replace(/'/g, "\\'");
        var safeMime = escapeHtml(a.mime_type || '').replace(/'/g, "\\'");

        html += '<div class="tl-attachment d-flex align-items-center flex-wrap gap-1 mt-1">' +
            '<i class="bi ' + iconClass + '"></i>' +
            '<span class="fw-semibold" style="font-size:0.78rem;">' + escapeHtml(a.name) + '</span>' +
            sizeText +
            '<span class="att-actions">' +
                '<button type="button" class="btn btn-outline-primary" ' +
                    'onclick="previewDocument(' + a.id + ',\'' + safeFileName + '\',\'' + safeMime + '\')" ' +
                    'title="Önizlə"><i class="bi bi-eye"></i></button>' +
                '<a href="/executor/attachments/' + a.id + '/download" class="btn btn-outline-secondary" title="Endir">' +
                    '<i class="bi bi-download"></i></a>' +
            '</span>' +
        '</div>';
    });
    return html;
}
