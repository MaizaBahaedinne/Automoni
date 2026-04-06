<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.form-section-header {
    padding: 12px 16px; border-bottom: 1px solid var(--border);
    font-weight: 700; font-size: .9rem; color: var(--text);
    display: flex; align-items: center; gap: 8px;
}
.form-section-header i { color: var(--brand); }
.form-card { border: 1px solid var(--border); border-radius: var(--radius); background: #fff; margin-bottom: 20px; overflow: hidden; }
.form-card-body { padding: 20px; }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <h1 class="fw-bold mb-4" style="font-size:1.4rem;">
            <i class="bi bi-buildings me-2" style="color:var(--brand)"></i><?= esc($title) ?>
        </h1>

        <form method="POST" action="<?= base_url('organizations/' . ($organization->id ?? '')) ?>" enctype="multipart/form-data" id="organizationForm" class="needs-validation" novalidate>
            <?= csrf_field() ?>

            <!-- Basic Information -->
            <div class="form-card">
                <div class="form-section-header"><i class="bi bi-info-circle"></i>Informations de base</div>
                <div class="form-card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="type_id" class="form-label fw-semibold">Type d'organisation <span class="text-danger">*</span></label>
                            <select name="type_id" id="type_id" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= $type->id ?>"
                                            <?= ($organization->type_id ?? null) == $type->id ? 'selected' : '' ?>>
                                        <?= esc($type->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un type</div>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required
                                   value="<?= esc($organization->name ?? '') ?>"
                                   placeholder="Nom de l'organisation">
                            <div class="invalid-feedback">Le nom est requis</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4"
                                  placeholder="Décrivez l'organisation..."><?= esc($organization->description ?? '') ?></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="parent_id" class="form-label fw-semibold">Organisation parente</label>
                            <select name="parent_id" id="parent_id" class="form-select">
                                <option value="">-- Aucune (organisation principale) --</option>
                                <?php foreach ($organizations as $org): ?>
                                    <?php if (empty($organization) || empty($organization->id) || $org->id !== $organization->id): ?>
                                        <option value="<?= $org->id ?>"
                                                <?= ($organization->parent_id ?? null) == $org->id ? 'selected' : '' ?>>
                                            <?= esc($org->name) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="industry" class="form-label fw-semibold">Secteur d'activité</label>
                            <input type="text" name="industry" id="industry" class="form-control"
                                   value="<?= esc($organization->industry ?? '') ?>"
                                   placeholder="ex : Technologies de l'information">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact & Location -->
            <div class="form-card">
                <div class="form-section-header"><i class="bi bi-telephone"></i>Contact & Localisation</div>
                <div class="form-card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   value="<?= esc($organization->email ?? '') ?>"
                                   placeholder="contact@organisation.com">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Téléphone</label>
                            <input type="tel" name="phone" id="phone" class="form-control"
                                   value="<?= esc($organization->phone ?? '') ?>"
                                   placeholder="+213 555 00 00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="website" class="form-label fw-semibold">Site web</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-globe2"></i></span>
                            <input type="url" name="website" id="website" class="form-control"
                                   value="<?= esc($organization->website ?? '') ?>"
                                   placeholder="https://exemple.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">Adresse</label>
                        <textarea name="address" id="address" class="form-control" rows="2"
                                  placeholder="Adresse complète..."><?= esc($organization->address ?? '') ?></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="latitude" class="form-label fw-semibold">Latitude</label>
                            <input type="number" name="latitude" id="latitude" class="form-control" step="0.000001"
                                   value="<?= esc($organization->latitude ?? '') ?>"
                                   placeholder="ex : 36.7538">
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label fw-semibold">Longitude</label>
                            <input type="number" name="longitude" id="longitude" class="form-control" step="0.000001"
                                   value="<?= esc($organization->longitude ?? '') ?>"
                                   placeholder="ex : 3.0588">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organization Details -->
            <div class="form-card">
                <div class="form-section-header"><i class="bi bi-gear"></i>Détails de l'organisation</div>
                <div class="form-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="employee_count" class="form-label fw-semibold">Nombre d'employés</label>
                            <input type="number" name="employee_count" id="employee_count" class="form-control" min="0"
                                   value="<?= esc($organization->employee_count ?? '') ?>"
                                   placeholder="ex : 500">
                        </div>
                        <div class="col-md-6">
                            <label for="founded_at" class="form-label fw-semibold">Date de fondation</label>
                            <input type="date" name="founded_at" id="founded_at" class="form-control"
                                   value="<?= esc($organization->founded_at ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo -->
            <div class="form-card">
                <div class="form-section-header"><i class="bi bi-image"></i>Logo</div>
                <div class="form-card-body">
                    <?php if (!empty($organization) && !empty($logo_url)): ?>
                        <div class="mb-3 d-flex align-items-center gap-3">
                            <img src="<?= esc($logo_url) ?>" alt="Logo actuel"
                                 style="max-width:120px;max-height:80px;border-radius:8px;border:1px solid var(--border);padding:6px;background:#fff;">
                            <span style="font-size:.85rem;color:var(--muted);">Logo actuel</span>
                        </div>
                    <?php endif; ?>
                    <label for="logo" class="form-label fw-semibold">
                        <?= (!empty($organization) && !empty($logo_url)) ? 'Remplacer le logo' : 'Télécharger un logo' ?>
                        <span class="fw-normal" style="color:var(--muted);">(max. 5 Mo)</span>
                    </label>
                    <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                    <div class="form-text">Formats acceptés : JPEG, PNG, WebP, SVG</div>
                    <div id="logoPreview" class="d-none mt-3">
                        <p style="font-size:.85rem;color:var(--muted);" class="mb-1">Aperçu :</p>
                        <img id="logoImg" src="" alt="Aperçu"
                             style="max-width:150px;max-height:90px;border-radius:8px;border:1px solid var(--border);padding:6px;background:#fff;">
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div class="form-card">
                <div class="form-section-header"><i class="bi bi-share"></i>Réseaux sociaux</div>
                <div class="form-card-body">
                    <div id="socialLinksContainer">
                        <?php if (!empty($social_links)): ?>
                            <?php $i = 0; foreach ($social_links as $link): ?>
                                <div class="row g-2 mb-2 socialLink align-items-center">
                                    <div class="col-md-4">
                                        <select name="social_platform_<?= $i ?>" class="form-select form-select-sm">
                                            <option value="">-- Plateforme --</option>
                                            <option value="facebook"  <?= $link->platform === 'facebook'  ? 'selected' : '' ?>>Facebook</option>
                                            <option value="twitter"   <?= $link->platform === 'twitter'   ? 'selected' : '' ?>>Twitter/X</option>
                                            <option value="linkedin"  <?= $link->platform === 'linkedin'  ? 'selected' : '' ?>>LinkedIn</option>
                                            <option value="instagram" <?= $link->platform === 'instagram' ? 'selected' : '' ?>>Instagram</option>
                                            <option value="youtube"   <?= $link->platform === 'youtube'   ? 'selected' : '' ?>>YouTube</option>
                                            <option value="github"    <?= $link->platform === 'github'    ? 'selected' : '' ?>>GitHub</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <input type="url" name="social_url_<?= $i ?>" class="form-control form-control-sm"
                                               placeholder="https://..." value="<?= esc($link->url) ?>">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-outline-danger btn-sm removeSocialLink">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php $i++; endforeach; ?>
                        <?php endif; ?>

                        <!-- Template (hidden) -->
                        <div class="row g-2 mb-2 socialLink d-none" id="socialLinkTemplate">
                            <div class="col-md-4">
                                <select name="social_platform_" class="form-select form-select-sm platformSelect">
                                    <option value="">-- Plateforme --</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="twitter">Twitter/X</option>
                                    <option value="linkedin">LinkedIn</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="github">GitHub</option>
                                </select>
                            </div>
                            <div class="col">
                                <input type="url" name="social_url_" class="form-control form-control-sm urlInput"
                                       placeholder="https://...">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-danger btn-sm removeSocialLink">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addSocialLinkButton" class="btn btn-outline-secondary btn-sm mt-1">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter un réseau
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex gap-2 mt-2 mb-5">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-save me-1"></i>
                    <?= !empty($organization) ? 'Mettre à jour' : "Créer l'organisation" ?>
                </button>
                <a href="<?= !empty($organization) ? base_url('organizations/' . $organization->id) : base_url('organizations') ?>"
                   class="btn btn-outline-secondary px-4">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
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

let socialLinkCount = <?= count($social_links ?? []) ?>;

document.getElementById('addSocialLinkButton')?.addEventListener('click', function() {
    const template = document.getElementById('socialLinkTemplate');
    const clone = template.cloneNode(true);

    clone.id = '';
    clone.classList.remove('d-none');
    clone.querySelector('.platformSelect').name = `social_platform_${socialLinkCount}`;
    clone.querySelector('.urlInput').name = `social_url_${socialLinkCount}`;

    document.getElementById('socialLinksContainer').appendChild(clone);
    socialLinkCount++;
    addRemoveListeners();
});

function addRemoveListeners() {
    document.querySelectorAll('.removeSocialLink').forEach(btn => {
        btn.onclick = function(e) {
            e.preventDefault();
            this.closest('.socialLink').remove();
        };
    });
}

addRemoveListeners();

document.getElementById('organizationForm')?.addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    this.classList.add('was-validated');
});
</script>

<?= $this->endSection() ?>
