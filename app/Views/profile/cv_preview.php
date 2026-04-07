<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .confidence-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
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
    .preview-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .preview-card.editable {
        position: relative;
    }
    .preview-card .edit-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: none;
    }
    .preview-card:hover .edit-btn {
        display: block;
    }
    .preview-field {
        margin-bottom: 1rem;
    }
    .preview-field label {
        font-weight: 600;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }
    .preview-value {
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 4px;
        color: #333;
    }
    .preview-list {
        list-style: none;
        padding: 0;
    }
    .preview-list li {
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: start;
    }
    .preview-list strong {
        display: block;
        margin-bottom: 0.25rem;
    }
    .skill-badge {
        display: inline-block;
        background: var(--brand-light);
        color: var(--brand-dark);
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.875rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
</style>

<div class="container mt-4">
    <!-- Header -->
    <div style="background: var(--brand-light); border-left: 4px solid var(--brand); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem;">
        <h1 style="margin: 0 0 0.5rem 0; color: var(--brand)">CV Preview & Auto-Fill</h1>
        <p style="margin: 0; color: var(--text)">Review and edit the data extracted from your CV before saving to your profile.</p>
    </div>

    <!-- Overall Confidence Score -->
    <div class="preview-card" style="background: linear-gradient(135deg, var(--brand-light), #f0f4ff); border-color: var(--brand);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h5 style="margin: 0 0 0.5rem 0; color: var(--brand)">Parsing Confidence Score</h5>
                <p style="margin: 0; color: #666; font-size: 0.9rem;">Overall accuracy of data extraction from your CV</p>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2rem; color: var(--brand); font-weight: bold;">
                    <?= round(($preview['metadata']['overall_confidence'] ?? 0.5) * 100) ?>%
                </div>
            </div>
        </div>
    </div>

    <form id="cvPreviewForm" method="post" action="/profile/cv/apply-preview">
        <?= csrf_field() ?>

        <!-- 1. Profile Section -->
        <div class="preview-card editable">
            <h3 style="margin-top: 0; color: var(--brand);">
                <i class="bi bi-person"></i> Profile Information
            </h3>

            <div class="preview-field">
                <label>
                    Headline
                    <span class="confidence-badge confidence-<?= ($preview['profile']['headline_confidence'] ?? 0) >= 0.9 ? 'high' : (($preview['profile']['headline_confidence'] ?? 0) >= 0.75 ? 'medium' : 'low') ?>">
                        <?= round(($preview['profile']['headline_confidence'] ?? 0) * 100) ?>%
                    </span>
                </label>
                <input type="text" class="form-control" name="profile[headline]" 
                       value="<?= esc($preview['profile']['headline'] ?? '') ?>"
                       placeholder="e.g., Senior PHP Developer">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="preview-field">
                        <label>Email</label>
                        <input type="email" class="form-control" name="profile[email]" 
                               value="<?= esc($preview['profile']['email'] ?? '') ?>"
                               placeholder="your.email@example.com">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="preview-field">
                        <label>Phone</label>
                        <input type="tel" class="form-control" name="profile[phone]" 
                               value="<?= esc($preview['profile']['phone'] ?? '') ?>"
                               placeholder="+33 6 12 34 56 78">
                    </div>
                </div>
            </div>

            <div class="preview-field">
                <label>
                    Professional Summary
                    <span class="confidence-badge confidence-<?= ($preview['profile']['summary_confidence'] ?? 0) >= 0.9 ? 'high' : (($preview['profile']['summary_confidence'] ?? 0) >= 0.75 ? 'medium' : 'low') ?>">
                        <?= round(($preview['profile']['summary_confidence'] ?? 0) * 100) ?>%
                    </span>
                </label>
                <textarea class="form-control" name="profile[summary]" rows="4" placeholder="Tell us about yourself..."><?= esc($preview['profile']['summary'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- 2. Skills Section -->
        <?php if (!empty($preview['skills'])): ?>
            <div class="preview-card editable">
                <h3 style="margin-top: 0; color: var(--brand);">
                    <i class="bi bi-star"></i> Skills
                </h3>
                <div class="preview-field">
                    <small class="text-muted">The following skills were detected in your CV:</small>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem;">
                        <?php foreach ($preview['skills'] as $idx => $skill): ?>
                            <div style="background: var(--brand-light); padding: 0.6rem 1rem;border-radius: 20px; display: flex; align-items: center; gap: 0.5rem;">
                                <span><?= esc($skill['name'] ?? $skill) ?></span>
                                <span class="confidence-badge confidence-<?= ($skill['confidence'] ?? 0.75) >= 0.9 ? 'high' : (($skill['confidence'] ?? 0.75) >= 0.75 ? 'medium' : 'low') ?>" 
                                      style="margin: 0;">
                                    <?= round(($skill['confidence'] ?? 0.75) * 100) ?>%
                                </span>
                                <input type="hidden" name="skills[]" value="<?= esc($skill['name'] ?? $skill) ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- 3. Experiences Section -->
        <?php if (!empty($preview['experiences'])): ?>
            <div class="preview-card editable">
                <h3 style="margin-top: 0; color: var(--brand);">
                    <i class="bi bi-briefcase"></i> Experiences (<?= count($preview['experiences']) ?>)
                </h3>
                <?php foreach ($preview['experiences'] as $idx => $exp): ?>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; border-left: 3px solid var(--brand);">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="preview-field" style="margin-bottom: 0.75rem;">
                                    <label style="font-size: 0.85rem;">Job Title</label>
                                    <input type="text" class="form-control form-control-sm" name="experiences[<?= $idx ?>][title]"
                                           value="<?= esc($exp['title'] ?? '') ?>"
                                           placeholder="e.g., Senior Developer">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="preview-field" style="margin-bottom: 0.75rem;">
                                    <label style="font-size: 0.85rem;">Company</label>
                                    <input type="text" class="form-control form-control-sm" name="experiences[<?= $idx ?>][organization]"
                                           value="<?= esc($exp['organization'] ?? '') ?>"
                                           placeholder="Company name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="preview-field" style="margin-bottom: 0;">
                                    <label style="font-size: 0.85rem;">Start Year</label>
                                    <input type="number" class="form-control form-control-sm" name="experiences[<?= $idx ?>][start_year]" min="1950" max="2099"
                                           value="<?= $exp['start_year'] ?? '' ?>" placeholder="2020">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="preview-field" style="margin-bottom: 0;">
                                    <label style="font-size: 0.85rem;">End Year</label>
                                    <input type="number" class="form-control form-control-sm" name="experiences[<?= $idx ?>][end_year]" min="1950" max="2099"
                                           value="<?= $exp['end_year'] ?? '' ?>" placeholder="2023">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="preview-field" style="margin-bottom: 0;">
                                    <label style="font-size: 0.85rem;">Confidence</label>
                                    <span class="confidence-badge confidence-<?= ($exp['confidence'] ?? 0) >= 0.85 ? 'high' : 'medium' ?>" style="display: block; width: 100%; text-align: center;">
                                        <?= round(($exp['confidence'] ?? 0) * 100) ?>%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- 4. Education Section -->
        <?php if (!empty($preview['education'])): ?>
            <div class="preview-card editable">
                <h3 style="margin-top: 0; color: var(--brand);">
                    <i class="bi bi-mortarboard"></i> Education (<?= count($preview['education']) ?>)
                </h3>
                <?php foreach ($preview['education'] as $idx => $edu): ?>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; border-left: 3px solid var(--brand);">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="preview-field" style="margin-bottom: 0.75rem;">
                                    <label style="font-size: 0.85rem;">Degree</label>
                                    <input type="text" class="form-control form-control-sm" name="education[<?= $idx ?>][degree]"
                                           value="<?= esc($edu['degree'] ?? '') ?>"
                                           placeholder="e.g., Bachelor, Master, PhD">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="preview-field" style="margin-bottom: 0.75rem;">
                                    <label style="font-size: 0.85rem;">Field of Study</label>
                                    <input type="text" class="form-control form-control-sm" name="education[<?= $idx ?>][field]"
                                           value="<?= esc($edu['field'] ?? '') ?>"
                                           placeholder="e.g., Computer Science">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="preview-field" style="margin-bottom: 0.75rem;">
                                    <label style="font-size: 0.85rem;">Institution</label>
                                    <input type="text" class="form-control form-control-sm" name="education[<?= $idx ?>][institution]"
                                           value="<?= esc($edu['institution'] ?? '') ?>"
                                           placeholder="University name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="preview-field" style="margin-bottom: 0.75rem;">
                                    <label style="font-size: 0.85rem;">Year Graduated</label>
                                    <input type="number" class="form-control form-control-sm" name="education[<?= $idx ?>][year]" min="1950" max="2099"
                                           value="<?= $edu['year'] ?? '' ?>" placeholder="2020">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    Confidence: <span class="confidence-badge confidence-<?= ($edu['confidence'] ?? 0) >= 0.85 ? 'high' : 'medium' ?>" style="margin: 0;">
                                        <?= round(($edu['confidence'] ?? 0) * 100) ?>%
                                    </span>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- 5. Languages Section -->
        <?php if (!empty($preview['languages'])): ?>
            <div class="preview-card editable">
                <h3 style="margin-top: 0; color: var(--brand);">
                    <i class="bi bi-translate"></i> Languages
                </h3>
                <div class="preview-field">
                    <ul class="preview-list" style="margin: 0;">
                        <?php foreach ($preview['languages'] as $idx => $lang): ?>
                            <li style="margin-bottom: 0;">
                                <div>
                                    <strong><?= esc($lang['name']) ?></strong>
                                    <span style="color: #666; font-size: 0.9rem;">
                                        (<?= ucfirst(esc($lang['level'])) ?>)
                                    </span>
                                </div>
                                <span class="confidence-badge confidence-<?= $lang['confidence'] >= 0.85 ? 'high' : 'medium' ?>" 
                                      style="margin-left: 1rem;">
                                    <?= round($lang['confidence'] * 100) ?>%
                                </span>
                                <input type="hidden" name="languages[<?= $idx ?>][name]" value="<?= esc($lang['name']) ?>">
                                <input type="hidden" name="languages[<?= $idx ?>][level]" value="<?= esc($lang['level']) ?>">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Metadata (hidden) -->
        <input type="hidden" name="metadata[parsed_at]" value="<?= esc($preview['metadata']['parsed_at'] ?? '') ?>">

        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem; margin-bottom: 2rem;">
            <button type="submit" class="btn btn-primary" style="background: var(--brand); border-color: var(--brand);">
                <i class="bi bi-check-circle"></i> Apply Changes To Profile
            </button>
            <a href="/profile/edit" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </form>

    <!-- Info Box -->
    <div style="background: #f8f9fa; border: 1px solid var(--border); border-radius: var(--radius); padding: 1rem; margin-top: 2rem;">
        <h6 style="margin-top: 0; color: var(--brand);">
            <i class="bi bi-info-circle"></i> How it works
        </h6>
        <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.9rem; color: #666;">
            <li>The percentages show how confident the system is about each extracted field.</li>
            <li>You can edit any field before applying to your profile.</li>
            <li>Once applied, this data will update your profile permanently.</li>
            <li>Cancel anytime to discard the preview without saving.</li>
        </ul>
    </div>
</div>

<script>
// CSRF Setup
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

// Form submission with better UX
document.getElementById('cvPreviewForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Disable submit button during request
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Applying...';
    
    fetch('/profile/cv/apply-preview', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            ...data,
            [CSRF_NAME]: CSRF_HASH,
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            // Show success message
            alert('✓ Profile updated successfully!');
            // Redirect to profile
            if (res.redirect) {
                window.location.href = res.redirect;
            }
        } else {
            alert('✗ Error: ' + (res.message || 'Unknown error'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Apply Changes To Profile';
        }
    })
    .catch(err => {
        console.error(err);
        alert('✗ Request failed: ' + err.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Apply Changes To Profile';
    });
});
</script>

<?= $this->endSection() ?>
