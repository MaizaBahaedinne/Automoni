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
                                <option value="1" <?= old('type_id') == '1' ? 'selected' : '' ?>>Société</option>
                                <option value="2" <?= old('type_id') == '2' ? 'selected' : '' ?>>ONG</option>
                                <option value="3" <?= old('type_id') == '3' ? 'selected' : '' ?>>Association</option>
                                <option value="4" <?= old('type_id') == '4' ? 'selected' : '' ?>>Organisme gouvernemental</option>
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
                        <div class="col-md-12">
                            <label for="parent_id" class="form-label fw-semibold">Organisation parente</label>
                            <select name="parent_id" id="parent_id" class="form-select">
                                <option value="">-- Aucune (organisation principale) --</option>
                                <?php foreach ($organizations as $org): ?>
                                    <option value="<?= $org->id ?>" <?= old('parent_id') == $org->id ? 'selected' : '' ?>><?= esc($org->name) ?></option>
                                <?php endforeach; ?>
                            </select>
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
                            <select name="phone_country_code" id="phone_country_code" class="form-select">
                                <option value="">-- Sélectionner --</option>
                                <option value="+213" <?= old('phone_country_code') == '+213' ? 'selected' : '' ?>>🇩🇿 Algérie (+213)</option>
                                <option value="+216" <?= old('phone_country_code') == '+216' ? 'selected' : '' ?>>🇹🇳 Tunisie (+216)</option>
                                <option value="+212" <?= old('phone_country_code') == '+212' ? 'selected' : '' ?>>🇲🇦 Maroc (+212)</option>
                                <option value="+33" <?= old('phone_country_code') == '+33' ? 'selected' : '' ?>>🇫🇷 France (+33)</option>
                                <option value="+1" <?= old('phone_country_code') == '+1' ? 'selected' : '' ?>>🇺🇸 États-Unis (+1)</option>
                                <option value="+44" <?= old('phone_country_code') == '+44' ? 'selected' : '' ?>>🇬🇧 Royaume-Uni (+44)</option>
                                <option value="+34" <?= old('phone_country_code') == '+34' ? 'selected' : '' ?>>🇪🇸 Espagne (+34)</option>
                                <option value="+39" <?= old('phone_country_code') == '+39' ? 'selected' : '' ?>>🇮🇹 Italie (+39)</option>
                                <option value="+41" <?= old('phone_country_code') == '+41' ? 'selected' : '' ?>>🇨🇭 Suisse (+41)</option>
                                <option value="+1" <?= old('phone_country_code') == '+1' ? 'selected' : '' ?>>🇨🇦 Canada (+1)</option>
                            </select>
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
                        <div class="col-md-6">
                            <label for="city" class="form-label fw-semibold">Ville <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control" required
                                   value="<?= old('city') ?>" placeholder="Alger">
                            <div class="invalid-feedback">La ville est requise.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="postal_code" class="form-label fw-semibold">Code postal <span class="text-danger">*</span></label>
                            <input type="text" name="postal_code" id="postal_code" class="form-control" required
                                   value="<?= old('postal_code') ?>" placeholder="16000">
                            <div class="invalid-feedback">Code postal requis.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="country_code" class="form-label fw-semibold">Pays <span class="text-danger">*</span></label>
                        <select name="country_code" id="country_code" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="DZ" <?= old('country_code') == 'DZ' ? 'selected' : '' ?>>Algérie</option>
                            <option value="TN" <?= old('country_code') == 'TN' ? 'selected' : '' ?>>Tunisie</option>
                            <option value="MA" <?= old('country_code') == 'MA' ? 'selected' : '' ?>>Maroc</option>
                            <option value="FR" <?= old('country_code') == 'FR' ? 'selected' : '' ?>>France</option>
                            <option value="US" <?= old('country_code') == 'US' ? 'selected' : '' ?>>États-Unis</option>
                            <option value="GB" <?= old('country_code') == 'GB' ? 'selected' : '' ?>>Royaume-Uni</option>
                            <option value="ES" <?= old('country_code') == 'ES' ? 'selected' : '' ?>>Espagne</option>
                            <option value="IT" <?= old('country_code') == 'IT' ? 'selected' : '' ?>>Italie</option>
                            <option value="CH" <?= old('country_code') == 'CH' ? 'selected' : '' ?>>Suisse</option>
                            <option value="CA" <?= old('country_code') == 'CA' ? 'selected' : '' ?>>Canada</option>
                            <option value="BE" <?= old('country_code') == 'BE' ? 'selected' : '' ?>>Belgique</option>
                            <option value="DE" <?= old('country_code') == 'DE' ? 'selected' : '' ?>>Allemagne</option>
                            <option value="NL" <?= old('country_code') == 'NL' ? 'selected' : '' ?>>Pays-Bas</option>
                            <option value="SE" <?= old('country_code') == 'SE' ? 'selected' : '' ?>>Suède</option>
                            <option value="NO" <?= old('country_code') == 'NO' ? 'selected' : '' ?>>Norvège</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- ── Informations complémentaires ─────────────────────────── -->
            <div class="cr-card">
                <div class="cr-section-header"><i class="bi bi-gear"></i>Détails de l'organisation</div>
                <div class="cr-card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="employee_count" class="form-label fw-semibold">Nombre d'employés</label>
                            <input type="number" name="employee_count" id="employee_count" class="form-control" min="0"
                                   value="<?= old('employee_count') ?>" placeholder="ex : 500">
                        </div>
                        <div class="col-md-4">
                            <label for="founded_at" class="form-label fw-semibold">Date de fondation <span class="text-danger">*</span></label>
                            <input type="date" name="founded_at" id="founded_at" class="form-control" required
                                   value="<?= old('founded_at') ?>">
                            <div class="invalid-feedback">La date de fondation est requise.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="tax_id" class="form-label fw-semibold">Numéro fiscal</label>
                            <input type="text" name="tax_id" id="tax_id" class="form-control"
                                   value="<?= old('tax_id') ?>" placeholder="NIF / SIRET / RC…">
                        </div>
                    </div>

                    <!-- Taille de l'organisation -->
                    <div class="mb-3">
                        <label for="size" class="form-label fw-semibold">Taille de l'organisation</label>
                        <select name="size" id="size" class="form-select">
                            <option value="">-- Non défini --</option>
                            <option value="startup" <?= old('size') == 'startup' ? 'selected' : '' ?>>Startup (< 10 employés)</option>
                            <option value="pme" <?= old('size') == 'pme' ? 'selected' : '' ?>>PME (10-250 employés)</option>
                            <option value="grande_entreprise" <?= old('size') == 'grande_entreprise' ? 'selected' : '' ?>>Grande entreprise (> 250 employés)</option>
                        </select>
                    </div>

                    <!-- Secteur d'activité (select simple) -->
                    <div class="mb-3">
                        <label for="industry" class="form-label fw-semibold">Secteur d'activité <span class="text-danger">*</span></label>
                        <select name="industry" id="industry" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="technology" <?= old('industry') == 'technology' ? 'selected' : '' ?>>Technologie</option>
                            <option value="finance" <?= old('industry') == 'finance' ? 'selected' : '' ?>>Finance</option>
                            <option value="healthcare" <?= old('industry') == 'healthcare' ? 'selected' : '' ?>>Santé</option>
                            <option value="manufacturing" <?= old('industry') == 'manufacturing' ? 'selected' : '' ?>>Industrie</option>
                            <option value="retail" <?= old('industry') == 'retail' ? 'selected' : '' ?>>Commerce de détail</option>
                            <option value="real-estate" <?= old('industry') == 'real-estate' ? 'selected' : '' ?>>Immobilier</option>
                            <option value="energy" <?= old('industry') == 'energy' ? 'selected' : '' ?>>Énergie</option>
                            <option value="transportation" <?= old('industry') == 'transportation' ? 'selected' : '' ?>>Transport</option>
                            <option value="education" <?= old('industry') == 'education' ? 'selected' : '' ?>>Éducation</option>
                            <option value="media" <?= old('industry') == 'media' ? 'selected' : '' ?>>Médias</option>
                            <option value="hospitality" <?= old('industry') == 'hospitality' ? 'selected' : '' ?>>Hôtellerie</option>
                            <option value="non-profit" <?= old('industry') == 'non-profit' ? 'selected' : '' ?>>Non-profit</option>
                            <option value="government" <?= old('industry') == 'government' ? 'selected' : '' ?>>Gouvernement</option>
                            <option value="professional-services" <?= old('industry') == 'professional-services' ? 'selected' : '' ?>>Services professionnels</option>
                            <option value="agriculture" <?= old('industry') == 'agriculture' ? 'selected' : '' ?>>Agriculture</option>
                            <option value="telecommunications" <?= old('industry') == 'telecommunications' ? 'selected' : '' ?>>Télécommunications</option>
                            <option value="utilities" <?= old('industry') == 'utilities' ? 'selected' : '' ?>>Utilitaires</option>
                            <option value="consulting" <?= old('industry') == 'consulting' ? 'selected' : '' ?>>Conseil</option>
                        </select>
                    </div>

                    <!-- Marchés ciblés -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Marchés ciblés</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="markets_targeted[]" id="market_local" value="local"
                                       <?= in_array('local', (old('markets_targeted') ?? [])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="market_local">
                                    <i class="bi bi-geo-alt me-1"></i>Local (dans le pays)
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="markets_targeted[]" id="market_international" value="international"
                                       <?= in_array('international', (old('markets_targeted') ?? [])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="market_international">
                                    <i class="bi bi-globe2 me-1"></i>International
                                </label>
                            </div>
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
document.addEventListener('DOMContentLoaded', function () {
    // ── Autocomplete: Recherche d'organisation parente ───────────────────
    let parentOrgTimeout;
    const parentIdSelect = document.getElementById('parent_id');
    
    if (parentIdSelect) {
        parentIdSelect.addEventListener('focus', function () {
            // Créer le dropdown si nécessaire
            if (!document.getElementById('parentOrgDropdown')) {
                const dropdown = document.createElement('div');
                dropdown.id = 'parentOrgDropdown';
                dropdown.style.cssText = `
                    position: absolute;
                    background: #fff;
                    border: 1px solid var(--border);
                    border-radius: var(--radius);
                    box-shadow: var(--shadow);
                    max-height: 250px;
                    overflow-y: auto;
                    z-index: 1000;
                    width: 100%;
                    display: none;
                `;
                this.parentElement.style.position = 'relative';
                this.parentElement.appendChild(dropdown);
            }
        });

        parentIdSelect.addEventListener('keyup', function (e) {
            clearTimeout(parentOrgTimeout);
            const query = this.value.toLowerCase();
            
            if (query.length < 1) {
                if (document.getElementById('parentOrgDropdown')) {
                    document.getElementById('parentOrgDropdown').style.display = 'none';
                }
                return;
            }

            parentOrgTimeout = setTimeout(function () {
                fetch('<?= base_url('api/organizations/search') ?>?q=' + encodeURIComponent(query))
                    .then(r => r.json())
                    .then(data => {
                        const dropdown = document.getElementById('parentOrgDropdown');
                        if (!dropdown) return;
                        
                        dropdown.innerHTML = '';
                        
                        if (!data.length) {
                            dropdown.innerHTML = '<div style="padding: 8px; color: var(--muted);">Aucun résultat</div>';
                        } else {
                            data.forEach(org => {
                                const item = document.createElement('div');
                                item.style.cssText = 'padding: 10px 12px; cursor: pointer; border-bottom: 1px solid var(--border); transition: background .2s;';
                                item.innerHTML = `<strong>${org.name}</strong><br><small style="color: var(--muted);">${org.type_name}</small>`;
                                item.addEventListener('mouseover', () => item.style.background = 'var(--brand-light)');
                                item.addEventListener('mouseout', () => item.style.background = 'transparent');
                                item.addEventListener('click', function () {
                                    parentIdSelect.value = org.id;
                                    document.getElementById('parentOrgDropdown').style.display = 'none';
                                    // Mettre à jour le texte affiché
                                    parentIdSelect.innerHTML += `<option selected value="${org.id}">${org.name}</option>`;
                                    parentIdSelect.value = org.id;
                                });
                                dropdown.appendChild(item);
                            });
                        }
                        dropdown.style.display = 'block';
                    })
                    .catch(() => {
                        const dropdown = document.getElementById('parentOrgDropdown');
                        if (dropdown) dropdown.style.display = 'none';
                    });
            }, 300);
        });
    }

    // ── Logo Preview ────────────────────────────────────────────────────
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function (e) {
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
    }

    // ── Ajouter liens sociaux ──────────────────────────────────────────
    let socialLinkCount = 0;
    const addSocialLinkButton = document.getElementById('addSocialLinkButton');
    if (addSocialLinkButton) {
        addSocialLinkButton.addEventListener('click', function () {
            const template = document.getElementById('socialLinkTemplate');
            const clone = template.cloneNode(true);
            clone.id = '';
            clone.classList.remove('d-none');
            clone.querySelector('.platformSelect').name = `social_platform_${socialLinkCount}`;
            clone.querySelector('.urlInput').name = `social_url_${socialLinkCount}`;
            document.getElementById('socialLinksContainer').appendChild(clone);
            socialLinkCount++;
        });
    }

    // ── Supprimer liens sociaux ────────────────────────────────────────
    const socialLinksContainer = document.getElementById('socialLinksContainer');
    if (socialLinksContainer) {
        socialLinksContainer.addEventListener('click', function (e) {
            if (e.target.closest('.removeSocialLink')) {
                e.target.closest('.socialLink').remove();
            }
        });
    }

    // ── Validation Bootstrap ───────────────────────────────────────────
    const crForm = document.getElementById('crForm');
    if (crForm) {
        crForm.addEventListener('submit', function (e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    }
});
</script>

<?= $this->endSection() ?>
