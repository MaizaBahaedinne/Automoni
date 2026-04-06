<?php
// app/Views/organizations/form.php
?>

<div class="container-fluid py-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <h1 class="mb-4"><?= $title ?></h1>

            <form method="POST" enctype="multipart/form-data" id="organizationForm" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <!-- Info Basique -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="type_id" class="form-label">Organization Type *</label>
                                <select name="type_id" id="type_id" class="form-select" required>
                                    <option value="">-- Select Type --</option>
                                    <?php foreach ($types as $type): ?>
                                        <option value="<?= $type->id ?>" 
                                                <?= ($organization->type_id ?? null) == $type->id ? 'selected' : '' ?>>
                                            <?= esc($type->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a type</div>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">Organization Name *</label>
                                <input type="text" name="name" id="name" class="form-control" required
                                       value="<?= esc($organization->name ?? '') ?>" 
                                       placeholder="Company name">
                                <div class="invalid-feedback">Name is required</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" 
                                      placeholder="Organization description...">
                                <?= esc($organization->description ?? '') ?>
                            </textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="parent_id" class="form-label">Parent Organization (if subsidiary)</label>
                                <select name="parent_id" id="parent_id" class="form-select">
                                    <option value="">-- No parent (main organization) --</option>
                                    <?php foreach ($organizations as $org): ?>
                                        <option value="<?= $org->id ?>" 
                                                <?= ($organization->parent_id ?? null) == $org->id ? 'selected' : '' ?>>
                                            <?= esc($org->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="industry" class="form-label">Industry</label>
                                <input type="text" name="industry" id="industry" class="form-control"
                                       value="<?= esc($organization->industry ?? '') ?>" 
                                       placeholder="e.g., Information Technology">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact & Location -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Contact & Location</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                       value="<?= esc($organization->email ?? '') ?>" 
                                       placeholder="contact@organization.com">
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" name="phone" id="phone" class="form-control"
                                       value="<?= esc($organization->phone ?? '') ?>" 
                                       placeholder="+1-555-0100">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" name="website" id="website" class="form-control"
                                   value="<?= esc($organization->website ?? '') ?>" 
                                   placeholder="https://example.com">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2"
                                      placeholder="Full address...">
                                <?= esc($organization->address ?? '') ?>
                            </textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" name="latitude" id="latitude" class="form-control" step="0.0001"
                                       value="<?= esc($organization->latitude ?? '') ?>" 
                                       placeholder="e.g., 37.7749">
                            </div>

                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" name="longitude" id="longitude" class="form-control" step="0.0001"
                                       value="<?= esc($organization->longitude ?? '') ?>" 
                                       placeholder="e.g., -122.4194">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailsorganacionales -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Organization Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="employee_count" class="form-label">Number of Employees</label>
                                <input type="number" name="employee_count" id="employee_count" class="form-control" min="0"
                                       value="<?= esc($organization->employee_count ?? '') ?>" 
                                       placeholder="e.g., 500">
                            </div>

                            <div class="col-md-6">
                                <label for="founded_at" class="form-label">Founded Date</label>
                                <input type="date" name="founded_at" id="founded_at" class="form-control"
                                       value="<?= esc($organization->founded_at ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logo -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Organization Logo</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($organization && $logo_url ?? null): ?>
                            <div class="mb-3">
                                <p class="small text-muted">Current Logo:</p>
                                <img src="<?= $logo_url ?>" alt="Logo" style="max-width: 200px; max-height: 100px;">
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Upload New Logo (Max 5MB)</label>
                            <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                            <small class="text-muted d-block mt-1">
                                Supported: JPEG, PNG, WebP, SVG
                            </small>
                        </div>

                        <div id="logoPreview" class="d-none mt-3">
                            <p class="small text-muted">Preview:</p>
                            <img id="logoImg" src="" alt="Preview" style="max-width: 200px;max-height: 100px;">
                        </div>
                    </div>
                </div>

                <!-- Réseaux Sociaux -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Social Media & Links</h5>
                    </div>
                    <div class="card-body">
                        <div id="socialLinksContainer">
                            <?php if (!empty($social_links)): ?>
                                <?php $i = 0; foreach ($social_links as $link): ?>
                                    <div class="row mb-3 socialLink">
                                        <div class="col-md-5">
                                            <select name="social_platform_<?= $i ?>" class="form-select">
                                                <option value="">-- Select Platform --</option>
                                                <option value="facebook" <?= $link->platform === 'facebook' ? 'selected' : '' ?>>Facebook</option>
                                                <option value="twitter" <?= $link->platform === 'twitter' ? 'selected' : '' ?>>Twitter/X</option>
                                                <option value="linkedin" <?= $link->platform === 'linkedin' ? 'selected' : '' ?>>LinkedIn</option>
                                                <option value="instagram" <?= $link->platform === 'instagram' ? 'selected' : '' ?>>Instagram</option>
                                                <option value="youtube" <?= $link->platform === 'youtube' ? 'selected' : '' ?>>YouTube</option>
                                                <option value="github" <?= $link->platform === 'github' ? 'selected' : '' ?>>GitHub</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="url" name="social_url_<?= $i ?>" class="form-control" 
                                                   placeholder="https://..." value="<?= esc($link->url) ?>">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-sm btn-danger removeSocialLink">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            
                            <!-- Template for new social links -->
                            <div class="row mb-3 socialLink d-none" id="socialLinkTemplate">
                                <div class="col-md-5">
                                    <select name="social_platform_" class="form-select platformSelect">
                                        <option value="">-- Select Platform --</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="twitter">Twitter/X</option>
                                        <option value="linkedin">LinkedIn</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="youtube">YouTube</option>
                                        <option value="github">GitHub</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="url" name="social_url_" class="form-control urlInput" 
                                           placeholder="https://...">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-danger removeSocialLink">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="addSocialLinkButton" class="btn btn-sm btn-secondary mt-2">
                            <i class="fas fa-plus"></i> Add Social Link
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                        <i class="fas fa-save"></i> 
                        <?= $organization ? 'Update Organization' : 'Create Organization' ?>
                    </button>
                    <a href="<?= $organization ? "/organizations/{$organization->id}" : '/organizations' ?>" 
                       class="btn btn-secondary btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Logo preview
    document.getElementById('logo')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('logoPreview').classList.remove('d-none');
                document.getElementById('logoImg').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Social links management
    let socialLinkCount = <?= count($social_links ?? []) ?>;

    document.getElementById('addSocialLinkButton')?.addEventListener('click', function() {
        const template = document.getElementById('socialLinkTemplate');
        const clone = template.cloneElement(true);
        
        clone.id = '';
        clone.classList.remove('d-none');
        
        // Update names
        clone.querySelector('.platformSelect').name = `social_platform_${socialLinkCount}`;
        clone.querySelector('.urlInput').name = `social_url_${socialLinkCount}`;
        
        document.getElementById('socialLinksContainer').appendChild(clone);
        socialLinkCount++;
        
        addRemoveListeners();
    });

    function addRemoveListeners() {
        document.querySelectorAll('.removeSocialLink').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                this.closest('.socialLink').remove();
            });
        });
    }

    // Initialize remove listeners
    addRemoveListeners();

    // Form validation
    document.getElementById('organizationForm')?.addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
</script>

<style>
    .needs-validation .form-control:invalid,
    .needs-validation .form-select:invalid {
        border-color: #dc3545;
    }
    
    .needs-validation .form-control:valid,
    .needs-validation .form-select:valid {
        border-color: #198754;
    }
</style>
