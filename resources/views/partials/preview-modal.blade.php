{{-- Document Preview Modal — place inside any page that needs preview --}}
<div class="modal fade" id="previewModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e3a5f 0%, #2a5298 100%); color: #fff; border: none; padding: 0.75rem 1.25rem;">
                <h6 class="modal-title mb-0" id="previewModalTitle" style="font-size: 0.88rem;">
                    <i class="bi bi-file-earmark-text me-2"></i>Sənəd Önizləmə
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="previewDownloadBtn" class="btn btn-sm btn-light" style="font-size: 0.75rem; padding: 3px 10px;">
                        <i class="bi bi-download me-1"></i>Endir
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="font-size: 0.65rem;"></button>
                </div>
            </div>
            <div class="modal-body" id="previewModalBody" style="padding: 0; min-height: 500px; background: #f8fafc;">
                <div class="preview-loading">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3">Sənəd yüklənir...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade" id="previewBackdrop" style="display:none; z-index:1055;"></div>

<style>
    /* Preview Modal */
    #previewModal .modal-dialog { max-width: 900px; }
    #previewFrame { width: 100%; height: 75vh; border: none; background: #fff; }
    #wordPreviewContainer { padding: 2rem 2.5rem; max-height: 75vh; overflow-y: auto; background: #fff; }
    #wordPreviewContainer img { max-width: 100%; }
    .word-render-area { font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; line-height: 1.8; color: #1e293b; font-size: 0.92rem; }
    .word-render-area table { border-collapse: collapse; width: 100%; margin: 1rem 0; }
    .word-render-area table td, .word-render-area table th { border: 1px solid #d1d5db; padding: 6px 10px; font-size: 0.85rem; }
    .word-render-area h1, .word-render-area h2, .word-render-area h3 { color: #1e3a5f; margin-top: 1.2rem; }
    .word-render-area p { margin-bottom: 0.6rem; }
    .preview-loading { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 400px; color: #64748b; }
    .preview-loading .spinner-border { width: 3rem; height: 3rem; }

    /* Attachment action buttons */
    .att-actions { display: inline-flex; gap: 4px; margin-left: 4px; }
    .att-actions .btn { padding: 2px 7px; font-size: 0.72rem; border-radius: 5px; }
</style>
