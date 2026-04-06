<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.cr-section-header {
    padding: 10px 16px; border-bottom: 1px solid var(--border);
    font-weight: 700; font-size: .88rem; color: var(--text);
    display: flex; align-items: center; gap: 8px;
}
.cr-section-header i { color: var(--brand); font-size: 1rem; }
.cr-card { border: 1px solid var(--border); border-radius: var(--radius); background: #fff; margin-bottom: 20px; overflow: hidden; box-shadow: var(--shadow); }
.cr-card-body { padding: 20px; }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <h1 class="fw-bold mb-4" style="font-size:1.4rem;">
            <i class="bi bi-buildings me-2" style="color:var(--brand)"></i><?= esc($title) ?>
        </h1>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger mb-3">
                <ul class="mb-0 ps-3">
                    <?php foreach ((array) session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('organizations') ?>" enctype="multipart/form-data" id="crForm" class="needs-validation" novalidate>
            <?= csrf_field() ?>

            <!-- ── Informations de base ──────────────────────────────────── -->
            <div class="cr-card">
                <div class="cr-section-header"><i class="bi bi-info-circle"></i>Informations de base</div>
                <div class="cr-card-body">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="type_id" class="form-label fw-semibold">Type d'organisation <span class="text-danger">*</span></label>
                            <select name="type_id" id="type_id" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= $type->id ?>" <?= old('type_id') == $type->id ? 'selected' : '' ?>><?= esc($type->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un type.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required
                                   value="<?= old('name') ?>" placeholder="Nom de l'organisation">
                            <div class="invalid-feedback">Le nom est requis (3 caractères min.).</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="legal_name" class="form-label fw-semibold">Raison sociale</label>
                        <input type="text" name="legal_name" id="legal_name" class="form-control"
                               value="<?= old('legal_name') ?>" placeholder="Dénomination légale officielle">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4"
                                  placeholder="Décrivez l'organisation..."><?= old('description') ?></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="parent_id" class="form-label fw-semibold">Organisation parente</label>
                            <select name="parent_id" id="parent_id" class="form-select">
                                <option value="">-- Aucune (organisation principale) --</option>
                                <?php foreach ($organizations as $org): ?>
                                    <option value="<?= $org->id ?>" <?= old('parent_id') == $org->id ? 'selected' : '' ?>><?= esc($org->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="industry" class="form-label fw-semibold">Secteur d'activité</label>
                            <input type="text" name="industry" id="industry" class="form-control"
                                   value="<?= old('industry') ?>" placeholder="ex : Technologies de l'information">
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── Contact ──────────────────────────────────────────────── -->
            <div class="cr-card">
                <div class="cr-section-header"><i class="bi bi-envelope"></i>Contact</div>
                <div class="cr-card-body">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control" required
                                       value="<?= old('email') ?>" placeholder="contact@organisation.com">
                            </div>
                            <div class="invalid-feedback">Adresse email valide requise.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="website" class="form-label fw-semibold">Site web <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-globe2"></i></span>
                                <input type="url" name="website" id="website" class="form-control" required
                                       value="<?= old('website') ?>" placeholder="https://exemple.com">
                            </div>
                            <div class="invalid-feedback">URL valide requise (avec https://).</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="phone_country_code" class="form-label fw-semibold">Indicatif pays</label>
                            <input type="text" name="phone_country_code" id="phone_country_code" class="form-control"
                                   value="<?= old('phone_country_code') ?>" placeholder="+213">
                        </div>
                        <div class="col-md-8">
                            <label for="phone_number" class="form-label fw-semibold">Numéro de téléphone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone_number" id="phone_number" class="form-control" required
                                   value="<?= old('phone_number') ?>" placeholder="555 12 34 56">
                            <div class="invalid-feedback">Numéro de téléphone requis.</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── Adresse ──────────────────────────────────────────────── -->
            <div class="cr-card">
                <div class="cr-section-header"><i class="bi bi-geo-alt"></i>Adresse</div>
                <div class="cr-card-body">

                    <div class="mb-3">
                        <label for="street_address" class="form-label fw-semibold">Rue / Adresse <span class="text-danger">*</span></label>
                        <input type="text" name="street_address" id="street_address" class="form-control" required
                               value="<?= old('street_address') ?>" placeholder="N° et nom de la rue">
                        <div class="invalid-feedback">L'adresse est requise.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label for="city" class="form-label fw-semibold">Ville <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control" required
                                   value="<?= old('city') ?>" placeholder="Alger">
                            <div class="invalid-feedback">La ville est requise.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="postal_code" class="form-label fw-semibold">Code postal <span class="text-danger">*</span></label>
                            <input type="text" name="postal_code" id="postal_code" class="form-control" required
                                   value="<?= old('postal_code') ?>" placeholder="16000">
                            <div class="invalid-feedback">Code postal requis.</div>
                        </div>
                        <div class="col-md-3">
                            <label for="country_code" class="form-label fw-semibold">Code pays <span class="text-danger">*</span></label>
                            <input type="text" name="country_code" id="country_code" class="form-control" required
                                   maxlength="2" style="text-transform:uppercase"
                                   value="<?= old('country_code') ?>" placeholder="DZ">
                            <div class="invalid-feedback">Code ISO 2 lettres (ex : DZ).</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="country" class="form-label fw-semibold">Pays</label>
                        <input type="text" name="country" id="country" class="form-control"
                               value="<?= old('country') ?>" placeholder="Algérie">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="latitude" class="form-label fw-semibold">Latitude</label>
                            <input type="number" name="latitude" id="latitude" class="form-control" step="0.000001"
                                   value="<?= old('latitude') ?>" placeholder="36.7538">
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label fw-semibold">Longitude</label>
                            <input type="number" name="longitude" id="longitude" class="form-control" step="0.000001"
                                   value="<?= old('longitude') ?>" placeholder="3.0588">
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── Informations complémentaires ─────────────────────────── -->
            <div class="cr-card">
                <div class="cr-section-header"><i class="bi bi-gear"></i>Détails de l'organisation</div>
                <div class="cr-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="employee_count" class="form-label fw-semibold">Nombre d'employés</label>
                            <input type="number" name="employee_count" id="employee_count" class="form-control" min="0"
                                   value="<?= old('employee_count') ?>" placeholder="ex : 500">
                        </div>
                        <div class="col-md-4">
                            <label for="founded_at" class="form-label fw-semibold">Date de fondation</label>
                            <input type="date" name="founded_at" id="founded_at" class="form-control"
                                   value="<?= old('founded_at') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="tax_id" class="form-label fw-semibold">Numéro fiscal</label>
                            <input type="text" name="tax_id" id="tax_id" class="form-control"
                                   value="<?= old('tax_id') ?>" placeholder="NIF / SIRET / RC…">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Logo ─────────────────────────────────────────────────── -->
            <div class="cr-card">
                <div class="cr-section-header"><i class="bi bi-image"></i>Logo</div>
                <div class="cr-card-body">
                    <label for="logo" class="form-label fw-semibold">
                        Télécharger un logo <span class="fw-normal" style="color:var(--muted);">(max. 5 Mo)</span>
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

            <!-- ── Réseaux sociaux ───────────────────────────────────────── -->
            <div class="cr-card">
                <div class="cr-section-header"><i class="bi bi-share"></i>Réseaux sociaux</div>
                <div class="cr-card-body">
                    <div id="socialLinksContainer">
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

            <!-- ── Actions ──────────────────────────────────────────────── -->
            <div class="d-flex gap-2 mt-2 mb-5">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-save me-1"></i>Créer l'organisation
                </button>
                <a href="<?= base_url('organizations') ?>" class="btn btn-outline-secondary px-4">Annuler</a>
            </div>

        </form>
    </div>
</div>

<script>
document.getElementById('logo')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('logoPreview').classList.remove('d-none');
            document.getElementById('logoImg').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});

let socialLinkCount = 0;

document.getElementById('addSocialLinkButton')?.addEventListener('click', function () {
    const template = document.getElementById('socialLinkTemplate');
    const clone = template.cloneNode(true);
    clone.id = '';
    clone.classList.remove('d-none');
    clone.querySelector('.platformSelect').name = `social_platform_${socialLinkCount}`;
    clone.querySelector('.urlInput').name = `social_url_${socialLinkCount}`;
    document.getElementById('socialLinksContainer').appendChild(clone);
    socialLinkCount++;
});

document.getElementById('socialLinksContainer')?.addEventListener('click', function (e) {
    if (e.target.closest('.removeSocialLink')) {
        e.target.closest('.socialLink').remove();
    }
});

// Force country_code to uppercase
document.getElementById('country_code')?.addEventListener('input', function () {
    this.value = this.value.toUpperCase();
});

// Bootstrap validation
(function () {
    'use strict';
    document.getElementById('crForm')?.addEventListener('submit', function (e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
})();
</script>

<?= $this->endSection() ?>
