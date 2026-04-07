<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .cv-integrate-container {
        background: linear-gradient(135deg, var(--brand-light), #f0f4ff);
        border-left: 4px solid var(--brand);
        padding: 2rem;
        border-radius: var(--radius);
        margin-bottom: 2rem;
    }

    .cv-integrate-title {
        color: var(--brand);
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
    }

    .cv-integrate-subtitle {
        color: var(--text);
        font-size: 1.05rem;
        margin: 0;
    }

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
        transform: scale(1.02);
    }

    .upload-zone-icon {
        font-size: 3rem;
        color: var(--brand);
        margin-bottom: 1rem;
    }

    .preview-section {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 2rem;
        margin-top: 2rem;
        display: none;
    }

    .preview-section.active {
        display: block;
    }

    .preview-card {
        background: #f8f9fa;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .preview-card-title {
        color: var(--brand);
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .preview-field {
        margin-bottom: 1rem;
    }

    .preview-field label {
        font-weight: 600;
        color: var(--text);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .preview-field input,
    .preview-field textarea {
        width: 100%;
    }

    .confidence-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }

    .confidence-high {
        background-color: #d4edda;
        color: #155724;
    }

    .confidence-medium {
        background-color: #fff3cd;
        color: #856404;
    }

    .confidence-low {
        background-color: #f8d7da;
        color: #721c24;
    }

    .skill-tag {
        display: inline-block;
        background: var(--brand-light);
        color: var(--brand-dark);
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.875rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .loading-spinner {
        display: none;
        text-align: center;
        padding: 2rem;
    }

    .loading-spinner.active {
        display: block;
    }

    .spinner-border-sm {
        width: 1.5rem;
        height: 1.5rem;
        border-width: 0.25rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        justify-content: flex-end;
    }

    .alert-message {
        display: none;
        margin-bottom: 1rem;
    }

    .alert-message.active {
        display: block;
    }

    .list-item-edit {
        background: #f8f9fa;
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .list-item-edit .btn-remove {
        position: absolute;
        right: 1rem;
        top: 1rem;
    }
</style>

<div class="container mt-4 mb-8">
    <!-- Header -->
    <div class="cv-integrate-container">
        <h1 class="cv-integrate-title">
            <i class="bi bi-file-pdf me-2"></i>CV Integration
        </h1>
        <p class="cv-integrate-subtitle">
            Upload your CV for intelligent auto-fill of your profile
        </p>
    </div>

    <!-- Messages -->
    <div id="messageAlert" class="alert-message"></div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Step 1: Upload -->
            <div id="uploadSection" style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:2rem;">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-cloud-upload me-2" style="color:var(--brand)"></i>Step 1: Upload Your CV
                </h5>

                <form id="cvUploadForm" enctype="multipart/form-data">
                    <div class="upload-zone" id="uploadZone">
                        <div class="upload-zone-icon">
                            <i class="bi bi-file-pdf"></i>
                        </div>
                        <h5 style="margin: 1rem 0 0.5rem 0;">Drag and drop your CV here</h5>
                        <p style="margin: 0.5rem 0 1.5rem 0; color: var(--muted); font-size: 0.9rem;">
                            or click to browse (PDF, DOC, DOCX, JPG, PNG - max 10MB)
                        </p>
                        <input type="file" id="cvFile" name="cv_file" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                               style="display:none;" required>
                        <button type="button" class="btn btn-primary" 
                                style="background: var(--brand); border-color: var(--brand);">
                            <i class="bi bi-folder-open me-1"></i>Select File
                        </button>
                    </div>

                    <small id="fileName" class="text-muted d-block mt-2" style="display:none;">
                        <i class="bi bi-check-circle me-1"></i><span id="fileNameText"></span>
                    </small>

                    <div class="mt-4">
                        <button type="submit" id="parseBtn" class="btn btn-primary" 
                                style="background: var(--brand); border-color: var(--brand); display:none;">
                            <i class="bi bi-lightning-fill me-1"></i>Parse CV
                        </button>
                        <a href="/profile" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Loading -->
            <div id="loadingSection" class="loading-spinner">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Analyzing your CV...</p>
            </div>

            <!-- Step 2: Preview & Edit -->
            <div id="previewSection" class="preview-section">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-eye me-2" style="color:var(--brand)"></i>Step 2: Review & Edit
                </h5>

                <form id="previewForm">
                    <?= csrf_field() ?>

                    <!-- Profile Info -->
                    <div class="preview-card">
                        <div class="preview-card-title">
                            <i class="bi bi-person"></i>Personal Information
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="preview-field">
                                    <label>
                                        First Name
                                        <span id="c_fname" class="confidence-badge"></span>
                                    </label>
                                    <input type="text" class="form-control" name="profile[first_name]" 
                                           id="first_name" placeholder="First name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="preview-field">
                                    <label>
                                        Last Name
                                        <span id="c_lname" class="confidence-badge"></span>
                                    </label>
                                    <input type="text" class="form-control" name="profile[last_name]" 
                                           id="last_name" placeholder="Last name">
                                </div>
                            </div>
                        </div>

                        <div class="preview-field">
                            <label>
                                Headline
                                <span id="c_headline" class="confidence-badge"></span>
                            </label>
                            <input type="text" class="form-control" name="profile[headline]" 
                                   id="headline" placeholder="e.g., Senior PHP Developer">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="preview-field">
                                    <label>
                                        Email
                                        <span id="c_email" class="confidence-badge"></span>
                                    </label>
                                    <input type="email" class="form-control" name="profile[email]" 
                                           id="email" placeholder="your@email.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="preview-field">
                                    <label>
                                        Phone
                                        <span id="c_phone" class="confidence-badge"></span>
                                    </label>
                                    <input type="tel" class="form-control" name="profile[phone]" 
                                           id="phone" placeholder="+33 12 34 56 78">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="preview-field">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="profile[city]" 
                                           id="city" placeholder="City">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="preview-field">
                                    <label>Country</label>
                                    <input type="text" class="form-control" name="profile[country]" 
                                           id="country" placeholder="Country">
                                </div>
                            </div>
                        </div>

                        <div class="preview-field">
                            <label>
                                Summary
                                <span id="c_summary" class="confidence-badge"></span>
                            </label>
                            <textarea class="form-control" name="profile[summary]" id="summary" 
                                      rows="4" placeholder="Professional summary..."></textarea>
                        </div>
                    </div>

                    <!-- Skills -->
                    <div id="skillsCard" class="preview-card" style="display:none;">
                        <div class="preview-card-title">
                            <i class="bi bi-star"></i>Skills
                            <span class="badge bg-primary" id="skillsCount">0</span>
                        </div>
                        <div id="skillsList"></div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Uncheck skills you want to remove
                        </small>
                    </div>

                    <!-- Languages -->
                    <div id="languagesCard" class="preview-card" style="display:none;">
                        <div class="preview-card-title">
                            <i class="bi bi-translate"></i>Languages
                            <span class="badge bg-primary" id="languagesCount">0</span>
                        </div>
                        <div id="languagesList"></div>
                    </div>

                    <!-- Experiences -->
                    <div id="experiencesCard" class="preview-card" style="display:none;">
                        <div class="preview-card-title">
                            <i class="bi bi-briefcase"></i>Work Experience
                            <span class="badge bg-primary" id="experiencesCount">0</span>
                        </div>
                        <div id="experiencesList"></div>
                    </div>

                    <!-- Education -->
                    <div id="educationCard" class="preview-card" style="display:none;">
                        <div class="preview-card-title">
                            <i class="bi bi-mortarboard"></i>Education
                            <span class="badge bg-primary" id="educationCount">0</span>
                        </div>
                        <div id="educationList"></div>
                    </div>

                    <!-- Certifications -->
                    <div id="certificationsCard" class="preview-card" style="display:none;">
                        <div class="preview-card-title">
                            <i class="bi bi-award"></i>Certifications
                            <span class="badge bg-primary" id="certificationsCount">0</span>
                        </div>
                        <div id="certificationsList"></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="button" id="cancelBtn" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="submit" id="saveBtn" class="btn btn-success" 
                                style="background: #198754; border-color: #198754;">
                            <i class="bi bi-check-circle me-1"></i>Save & Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const BASE = '<?= base_url() ?>';
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

let cvParseResult = {};

// ─── File Upload Handling ──────────────────────────────────────────────

const uploadZone = document.getElementById('uploadZone');
const cvFile = document.getElementById('cvFile');
const cvForm = document.getElementById('cvUploadForm');
const parseBtn = document.getElementById('parseBtn');
const fileName = document.getElementById('fileName');
const fileNameText = document.getElementById('fileNameText');

uploadZone.addEventListener('click', () => cvFile.click());

cvFile.addEventListener('change', () => {
    if (cvFile.files.length > 0) {
        const file = cvFile.files[0];
        const sizeMB = (file.size / 1024 / 1024).toFixed(2);
        fileNameText.textContent = `${file.name} (${sizeMB} MB)`;
        fileName.style.display = 'block';
        parseBtn.style.display = 'inline-block';
    }
});

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
        cvFile.dispatchEvent(new Event('change'));
    }
});

// ─── Parse CV ──────────────────────────────────────────────────────────

cvForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append('cv_file', cvFile.files[0]);
    formData.append(CSRF_NAME, CSRF_HASH);

    parseBtn.disabled = true;
    parseBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Parsing...';

    document.getElementById('loadingSection').classList.add('active');
    document.getElementById('uploadSection').style.opacity = '0.5';
    document.getElementById('uploadSection').style.pointerEvents = 'none';

    try {
        const resp = await fetch(BASE + 'profile/cv-parse', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await resp.json();

        document.getElementById('loadingSection').classList.remove('active');

        if (data.success) {
            cvParseResult = data.data;
            renderPreview(cvParseResult);
            document.getElementById('previewSection').classList.add('active');
            showMessage('✓ CV parsed successfully!', 'success');
        } else {
            showMessage('✗ ' + (data.message || 'Parsing failed'), 'danger');
            document.getElementById('uploadSection').style.opacity = '1';
            document.getElementById('uploadSection').style.pointerEvents = 'auto';
            parseBtn.disabled = false;
            parseBtn.innerHTML = '<i class="bi bi-lightning-fill me-1"></i>Parse CV';
        }
    } catch (err) {
        console.error(err);
        showMessage('✗ Error: ' + err.message, 'danger');
        document.getElementById('loadingSection').classList.remove('active');
        document.getElementById('uploadSection').style.opacity = '1';
        document.getElementById('uploadSection').style.pointerEvents = 'auto';
        parseBtn.disabled = false;
        parseBtn.innerHTML = '<i class="bi bi-lightning-fill me-1"></i>Parse CV';
    }
});

// ─── Render Preview ────────────────────────────────────────────────────

function renderPreview(data) {
    const profile = data.profile || {};
    const confidences = profile.confidences || {};

    // Profile fields
    document.getElementById('first_name').value = profile.first_name || '';
    document.getElementById('last_name').value = profile.last_name || '';
    document.getElementById('headline').value = profile.headline || '';
    document.getElementById('email').value = profile.email || '';
    document.getElementById('phone').value = profile.phone || '';
    document.getElementById('city').value = profile.city || '';
    document.getElementById('country').value = profile.country || '';
    document.getElementById('summary').value = profile.summary || '';

    // Confidence badges
    setConfidenceBadge('c_fname', confidences.first_name?.score);
    setConfidenceBadge('c_lname', confidences.last_name?.score);
    setConfidenceBadge('c_headline', confidences.headline?.score);
    setConfidenceBadge('c_email', confidences.email?.score);
    setConfidenceBadge('c_phone', confidences.phone?.score);
    setConfidenceBadge('c_summary', confidences.summary?.score);

    // Skills
    if (data.skills && data.skills.length > 0) {
        renderSkills(data.skills);
    }

    // Languages
    if (data.languages && data.languages.length > 0) {
        renderLanguages(data.languages);
    }

    // Experiences
    if (data.experiences && data.experiences.length > 0) {
        renderExperiences(data.experiences);
    }

    // Education
    if (data.education && data.education.length > 0) {
        renderEducation(data.education);
    }

    // Certifications
    if (data.certifications && data.certifications.length > 0) {
        renderCertifications(data.certifications);
    }
}

function setConfidenceBadge(id, score) {
    const el = document.getElementById(id);
    if (!score) return;
    
    const percent = Math.round(score * 100);
    let cls = 'confidence-low';
    if (percent >= 90) cls = 'confidence-high';
    else if (percent >= 75) cls = 'confidence-medium';
    
    el.textContent = percent + '%';
    el.classList.add(cls);
}

function renderSkills(skills) {
    const container = document.getElementById('skillsList');
    const card = document.getElementById('skillsCard');
    
    container.innerHTML = skills.map((skill, idx) => `
        <div class="skill-tag">
            <input type="checkbox" name="skills[${idx}][name]" value="${skill.name || skill}" 
                   checked style="margin-right: 0.5rem;">
            <label style="margin:0; display:inline;">
                ${skill.name || skill}
                <span class="confidence-badge confidence-${skill.confidence >= 0.9 ? 'high' : skill.confidence >= 0.75 ? 'medium' : 'low'}">
                    ${Math.round((skill.confidence || 0.75) * 100)}%
                </span>
            </label>
        </div>
    `).join('');
    
    document.getElementById('skillsCount').textContent = skills.length;
    card.style.display = 'block';
}

function renderLanguages(languages) {
    const container = document.getElementById('languagesList');
    const card = document.getElementById('languagesCard');
    
    container.innerHTML = languages.map((lang, idx) => `
        <div class="list-item-edit">
            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="languages[${idx}][name]" 
                           value="${lang.name || ''}" placeholder="Language">
                </div>
                <div class="col-md-6">
                    <select class="form-control" name="languages[${idx}][level]">
                        <option value="A1" ${lang.level === 'A1' ? 'selected' : ''}>A1 - Beginner</option>
                        <option value="A2" ${lang.level === 'A2' ? 'selected' : ''}>A2 - Elementary</option>
                        <option value="B1" ${lang.level === 'B1' ? 'selected' : ''}>B1 - Intermediate</option>
                        <option value="B2" ${lang.level === 'B2' ? 'selected' : ''}>B2 - Upper-Intermediate</option>
                        <option value="C1" ${lang.level === 'C1' ? 'selected' : ''}>C1 - Advanced</option>
                        <option value="C2" ${lang.level === 'C2' ? 'selected' : ''}>C2 - Fluent</option>
                    </select>
                </div>
            </div>
        </div>
    `).join('');
    
    document.getElementById('languagesCount').textContent = languages.length;
    card.style.display = 'block';
}

function renderExperiences(experiences) {
    const container = document.getElementById('experiencesList');
    const card = document.getElementById('experiencesCard');
    
    container.innerHTML = experiences.map((exp, idx) => `
        <div class="list-item-edit">
            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="experiences[${idx}][title]" 
                           value="${exp.title || ''}" placeholder="Job Title">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="experiences[${idx}][organization]" 
                           value="${exp.organization || ''}" placeholder="Company">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">
                    <input type="number" class="form-control" name="experiences[${idx}][start_year]" 
                           value="${exp.start_year || ''}" placeholder="Start Year">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="experiences[${idx}][end_year]" 
                           value="${exp.end_year || ''}" placeholder="End Year">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="experiences[${idx}][location]" 
                           value="${exp.location || ''}" placeholder="Location">
                </div>
            </div>
        </div>
    `).join('');
    
    document.getElementById('experiencesCount').textContent = experiences.length;
    card.style.display = 'block';
}

function renderEducation(education) {
    const container = document.getElementById('educationList');
    const card = document.getElementById('educationCard');
    
    container.innerHTML = education.map((edu, idx) => `
        <div class="list-item-edit">
            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="education[${idx}][degree]" 
                           value="${edu.degree || ''}" placeholder="Degree">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="education[${idx}][field]" 
                           value="${edu.field || ''}" placeholder="Field of Study">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="education[${idx}][institution]" 
                           value="${edu.institution || ''}" placeholder="Institution">
                </div>
                <div class="col-md-6">
                    <input type="number" class="form-control" name="education[${idx}][year_graduated]" 
                           value="${edu.year_graduated || ''}" placeholder="Year Graduated">
                </div>
            </div>
        </div>
    `).join('');
    
    document.getElementById('educationCount').textContent = education.length;
    card.style.display = 'block';
}

function renderCertifications(certifications) {
    const container = document.getElementById('certificationsList');
    const card = document.getElementById('certificationsCard');
    
    container.innerHTML = certifications.map((cert, idx) => `
        <div class="list-item-edit">
            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="certifications[${idx}][name]" 
                           value="${cert.name || ''}" placeholder="Certification Name">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="certifications[${idx}][organization]" 
                           value="${cert.organization || ''}" placeholder="Issuer">
                </div>
            </div>
        </div>
    `).join('');
    
    document.getElementById('certificationsCount').textContent = certifications.length;
    card.style.display = 'block';
}

// ─── Save Profile ──────────────────────────────────────────────────────

document.getElementById('previewForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = new FormData(e.target);
    const data = Object.fromEntries(form);

    document.getElementById('saveBtn').disabled = true;
    document.getElementById('saveBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

    try {
        const resp = await fetch(BASE + 'profile/cv-save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                ...data,
                [CSRF_NAME]: CSRF_HASH,
            })
        });

        const result = await resp.json();

        if (result.success) {
            showMessage('✓ Profile updated successfully!', 'success');
            setTimeout(() => {
                window.location.href = result.redirect || '/profile';
            }, 1000);
        } else {
            showMessage('✗ ' + (result.message || 'Save failed'), 'danger');
            document.getElementById('saveBtn').disabled = false;
            document.getElementById('saveBtn').innerHTML = '<i class="bi bi-check-circle me-1"></i>Save & Update Profile';
        }
    } catch (err) {
        console.error(err);
        showMessage('✗ Error: ' + err.message, 'danger');
        document.getElementById('saveBtn').disabled = false;
        document.getElementById('saveBtn').innerHTML = '<i class="bi bi-check-circle me-1"></i>Save & Update Profile';
    }
});

// ─── Cancel Button ─────────────────────────────────────────────────────

document.getElementById('cancelBtn').addEventListener('click', () => {
    if (confirm('Are you sure? All data will be lost.')) {
        window.location.href = '/profile';
    }
});

// ─── Messages ──────────────────────────────────────────────────────────

function showMessage(message, type) {
    const alert = document.getElementById('messageAlert');
    alert.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    alert.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?= $this->endSection() ?>
