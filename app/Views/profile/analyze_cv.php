<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .upload-zone {
        border: 3px dashed var(--border);
        border-radius: var(--radius);
        padding: 3rem;
        text-align: center;
        background: var(--bg);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .upload-zone:hover {
        border-color: var(--brand);
        background: var(--brand-light);
    }
    .upload-zone.dragover {
        border-color: var(--brand);
        background: var(--brand-light);
    }
    .file-icon {
        font-size: 3rem;
        color: var(--brand);
        margin-bottom: 1rem;
    }
</style>

<div class="container mt-4 mb-8">
    <!-- Header -->
    <div style="background: linear-gradient(135deg, var(--brand-light), #f0f4ff); border-left: 4px solid var(--brand); padding: 2.5rem; border-radius: var(--radius); margin-bottom: 2rem;">
        <h1 style="margin: 0 0 0.75rem 0; color: var(--brand); font-size: 2rem; font-weight: 700;">
            <i class="bi bi-brain" style="font-size: 2.5rem;"></i> <?= lang('App.cv_preview_title') ?>
        </h1>
        <p style="margin: 0; color: var(--text); font-size: 1.05rem;">
            <?= lang('App.cv_preview_subtitle') ?>
        </p>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Upload Card -->
            <div style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:2rem; margin-bottom:2rem;">
                <?php if (!empty($profile?->cv_file)): ?>
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong><?= lang('App.section_cv') ?></strong>: 
                        <em><?= esc($profile->cv_original_name ?? 'CV') ?></em>
                        <a href="/profile/cv/download" class="ms-2" target="_blank">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                <?php endif; ?>

                <form id="cvUploadForm" enctype="multipart/form-data" method="post" action="/profile/cv/upload">
                    <?= csrf_field() ?>

                    <div class="upload-zone" id="uploadZone">
                        <div class="file-icon">
                            <i class="bi bi-file-pdf"></i>
                        </div>
                        <h5 style="margin-bottom: 0.5rem; color: var(--text);">
                            <?= lang('App.cv_hint_size') ?>
                        </h5>
                        <p style="margin: 0.5rem 0 1.5rem 0; color: var(--muted); font-size: 0.9rem;">
                            PDF, DOC ou DOCX (max 5 MB)
                        </p>
                        <input type="file" id="cvFile" name="cv_file" accept=".pdf,.doc,.docx" style="display:none;" required>
                        <button type="button" class="btn btn-primary" style="background: var(--brand); border-color: var(--brand);">
                            <i class="bi bi-cloud-upload me-1"></i>Sélectionner un fichier
                        </button>
                        <p style="margin: 1rem 0 0 0; color: var(--muted); font-size: 0.85rem;">
                            ou glisser-déposer un fichier
                        </p>
                    </div>

                    <small id="fileName" class="text-muted d-block mt-2" style="display:none;">
                        <i class="bi bi-file-check me-1"></i><span id="fileNameText"></span>
                    </small>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" id="analyzeBtn" class="btn btn-primary flex-grow-1" style="background: var(--brand); border-color: var(--brand); display:none;">
                            <i class="bi bi-play-circle me-1"></i>Analyser mon CV
                        </button>
                        <a href="/profile/edit" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i><?= lang('App.btn_cancel') ?>
                        </a>
                    </div>

                    <div id="uploadMessage" class="mt-3"></div>
                </form>
            </div>

            <!-- Info Box -->
            <div style="background: #f8f9fa; border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem;">
                <h6 style="margin-top: 0; margin-bottom: 1rem; color: var(--brand);">
                    <i class="bi bi-info-circle me-1"></i>Comment ça fonctionne ?
                </h6>
                <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.9rem; color: #666; line-height: 1.6;">
                    <li>📤 Uploadez votre CV (PDF, DOC ou DOCX)</li>
                    <li>🤖 Notre système d'IA analyse automatiquement votre document</li>
                    <li>✏️ Vous pouvez modifier chaque champ avant de l'appliquer</li>
                    <li>💾 Cliquez sur "Appliquer" pour mettre à jour votre profil</li>
                    <li>🎯 Les scores de confiance indiquent la fiabilité de chaque extraction</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const uploadZone = document.getElementById('uploadZone');
    const cvFile = document.getElementById('cvFile');
    const cvForm = document.getElementById('cvUploadForm');
    const analyzeBtn = document.getElementById('analyzeBtn');
    const fileName = document.getElementById('fileName');
    const fileNameText = document.getElementById('fileNameText');
    const uploadMessage = document.getElementById('uploadMessage');

    // Click to upload
    uploadZone.addEventListener('click', () => cvFile.click());

    // File selection
    cvFile.addEventListener('change', () => {
        if (cvFile.files.length > 0) {
            const file = cvFile.files[0];
            fileNameText.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
            fileName.style.display = 'block';
            analyzeBtn.style.display = 'block';
            uploadMessage.innerHTML = '';
        }
    });

    // Drag & Drop
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        
        if (e.dataTransfer.files.length > 0) {
            cvFile.files = e.dataTransfer.files;
            const event = new Event('change', { bubbles: true });
            cvFile.dispatchEvent(event);
        }
    });

    // Form submission
    cvForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(cvForm);
        analyzeBtn.disabled = true;
        analyzeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

        try {
            const resp = await fetch('<?= base_url('profile/cv/upload') ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const text = await resp.text();
            
            if (resp.ok && text.includes('successfully')) {
                uploadMessage.innerHTML = '<div class="alert alert-success mb-0"><i class="bi bi-check-circle me-1"></i>CV uploadé avec succès! Redirection vers l\'analyse...</div>';
                setTimeout(() => {
                    window.location.href = '<?= base_url('profile/cv-preview') ?>';
                }, 1000);
            } else {
                uploadMessage.innerHTML = '<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-circle me-1"></i>' + (text.substring(0, 150) || 'Erreur lors de l\'upload') + '</div>';
                analyzeBtn.disabled = false;
                analyzeBtn.innerHTML = '<i class="bi bi-play-circle me-1"></i>Analyser mon CV';
            }
        } catch (err) {
            uploadMessage.innerHTML = '<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-circle me-1"></i>Erreur: ' + err.message + '</div>';
            analyzeBtn.disabled = false;
            analyzeBtn.innerHTML = '<i class="bi bi-play-circle me-1"></i>Analyser mon CV';
        }
    });
});
</script>

<?= $this->endSection() ?>
