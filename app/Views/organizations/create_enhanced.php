<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
/* ── Wizard wrapper ───────────────────────── */
.wz-wrap { max-width: 700px; margin: 0 auto; }

/* ── Step indicator ───────────────────────── */
.wz-steps { display: flex; align-items: flex-start; margin-bottom: 2rem; }
.wz-step  { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
.wz-step-circle {
    width: 38px; height: 38px; border-radius: 50%;
    border: 2px solid var(--border); background: #fff; color: var(--muted);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .85rem; position: relative; z-index: 1;
    transition: background .25s, border-color .25s, color .25s;
}
.wz-step-label { font-size: .7rem; margin-top: 5px; color: var(--muted); text-align: center; transition: color .25s; }
.wz-step:not(:first-child)::before {
    content: ''; position: absolute; top: 19px;
    right: calc(50% + 19px); left: calc(-50% + 19px);
    height: 2px; background: var(--border); transition: background .25s; z-index: 0;
}
.wz-step.active .wz-step-circle { background: var(--brand); border-color: var(--brand); color: #fff; }
.wz-step.active .wz-step-label  { color: var(--brand); font-weight: 600; }
.wz-step.done   .wz-step-circle { background: var(--brand-dark); border-color: var(--brand-dark); color: #fff; }
.wz-step.done::before           { background: var(--brand-dark); }
.wz-step.done   .wz-step-label  { color: var(--brand-dark); }

/* ── Panels ───────────────────────────────── */
.wz-panel        { display: none; }
.wz-panel.active { display: block; }

/* ── Card ──────────────────────────────────── */
.wz-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; margin-bottom: 1.25rem; }
.wz-card-header { padding: 12px 16px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: .88rem; color: var(--text); }
.wz-card-header i { color: var(--brand); font-size: 1rem; }
.wz-card-body   { padding: 20px; }

/* ── Nav ────────────────────────────────────── */
.wz-nav { display: flex; gap: 10px; margin-top: 8px; margin-bottom: 2.5rem; }
.wz-nav .btn-prev   { min-width: 130px; }
.wz-nav .btn-next   { flex: 1; }
.wz-nav .btn-submit { flex: 1; }
</style>

<div class="wz-wrap">

    <h1 class="fw-bold mb-4" style="font-size:1.35rem;">
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
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger mb-3"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <!-- Step indicator -->
    <div class="wz-steps" id="wzSteps">
        <div class="wz-step active" data-step="1"><div class="wz-step-circle">1</div><div class="wz-step-label">Identite</div></div>
        <div class="wz-step"        data-step="2"><div class="wz-step-circle">2</div><div class="wz-step-label">Contact</div></div>
        <div class="wz-step"        data-step="3"><div class="wz-step-circle">3</div><div class="wz-step-label">Adresse</div></div>
        <div class="wz-step"        data-step="4"><div class="wz-step-circle">4</div><div class="wz-step-label">Details</div></div>
        <div class="wz-step"        data-step="5"><div class="wz-step-circle">5</div><div class="wz-step-label">Medias</div></div>
    </div>

    <form method="POST" action="<?= base_url('organizations') ?>" enctype="multipart/form-data" id="crForm" novalidate>
        <?= csrf_field() ?>

        <!-- ═══ ETAPE 1 – Identite ═══════════════════ -->
        <div class="wz-panel active" id="panel-1">
            <div class="wz-card">
                <div class="wz-card-header"><i class="bi bi-buildings"></i>Identite de l'organisation</div>
                <div class="wz-card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="type_id" class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type_id" id="type_id" class="form-select" required>
                                <option value="">-- Selectionner --</option>
                                <?php foreach ($types as $t): ?>
                                    <option value="<?= $t->id ?>" <?= old('type_id') == $t->id ? 'selected' : '' ?>><?= esc($t->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Veuillez selectionner un type.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required minlength="3"
                                   value="<?= old('name') ?>" placeholder="Nom de l'organisation">
                            <div class="invalid-feedback">Nom requis (3 caracteres min.).</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="legal_name" class="form-label fw-semibold">Raison sociale</label>
                        <input type="text" name="legal_name" id="legal_name" class="form-control"
                               value="<?= old('legal_name') ?>" placeholder="Denomination legale officielle">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3"
                                  placeholder="Decrivez l'organisation..."><?= old('description') ?></textarea>
                    </div>
                    <div class="mb-1">
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
            <div class="wz-nav">
                <button type="button" class="btn btn-outline-secondary btn-prev" style="visibility:hidden">
                    <i class="bi bi-arrow-left me-1"></i>Precedent
                </button>
                <button type="button" class="btn btn-primary btn-next" data-current="1">
                    Suivant<i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- ═══ ETAPE 2 – Contact ════════════════════ -->
        <div class="wz-panel" id="panel-2">
            <div class="wz-card">
                <div class="wz-card-header"><i class="bi bi-envelope"></i>Coordonnees</div>
                <div class="wz-card-body">
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
                            <label for="phone_country_code" class="form-label fw-semibold">Indicatif</label>
                            <select name="phone_country_code" id="phone_country_code" class="form-select">
                                <option value="">--</option>
                                <option value="+213" <?= old('phone_country_code')=='+213'?'selected':'' ?>>DZ +213</option>
                                <option value="+216" <?= old('phone_country_code')=='+216'?'selected':'' ?>>TN +216</option>
                                <option value="+212" <?= old('phone_country_code')=='+212'?'selected':'' ?>>MA +212</option>
                                <option value="+33"  <?= old('phone_country_code')=='+33' ?'selected':'' ?>>FR +33</option>
                                <option value="+32"  <?= old('phone_country_code')=='+32' ?'selected':'' ?>>BE +32</option>
                                <option value="+41"  <?= old('phone_country_code')=='+41' ?'selected':'' ?>>CH +41</option>
                                <option value="+44"  <?= old('phone_country_code')=='+44' ?'selected':'' ?>>GB +44</option>
                                <option value="+1"   <?= old('phone_country_code')=='+1'  ?'selected':'' ?>>US +1</option>
                                <option value="+49"  <?= old('phone_country_code')=='+49' ?'selected':'' ?>>DE +49</option>
                                <option value="+34"  <?= old('phone_country_code')=='+34' ?'selected':'' ?>>ES +34</option>
                                <option value="+39"  <?= old('phone_country_code')=='+39' ?'selected':'' ?>>IT +39</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label for="phone_number" class="form-label fw-semibold">Telephone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone_number" id="phone_number" class="form-control" required
                                   value="<?= old('phone_number') ?>" placeholder="555 12 34 56">
                            <div class="invalid-feedback">Numero de telephone requis.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wz-nav">
                <button type="button" class="btn btn-outline-secondary btn-prev" data-current="2">
                    <i class="bi bi-arrow-left me-1"></i>Precedent
                </button>
                <button type="button" class="btn btn-primary btn-next" data-current="2">
                    Suivant<i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- ═══ ETAPE 3 – Adresse ════════════════════ -->
        <div class="wz-panel" id="panel-3">
            <div class="wz-card">
                <div class="wz-card-header"><i class="bi bi-geo-alt"></i>Adresse</div>
                <div class="wz-card-body">
                    <div class="mb-3">
                        <label for="street_address" class="form-label fw-semibold">Rue <span class="text-danger">*</span></label>
                        <input type="text" name="street_address" id="street_address" class="form-control" required
                               value="<?= old('street_address') ?>" placeholder="N et nom de la rue">
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
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="country_code" class="form-label fw-semibold">Pays <span class="text-danger">*</span></label>
                            <select name="country_code" id="country_code" class="form-select" required>
                                <option value="">-- Selectionner --</option>
                                <option value="DZ" <?= old('country_code')=='DZ'?'selected':'' ?>>Algerie</option>
                                <option value="TN" <?= old('country_code')=='TN'?'selected':'' ?>>Tunisie</option>
                                <option value="MA" <?= old('country_code')=='MA'?'selected':'' ?>>Maroc</option>
                                <option value="FR" <?= old('country_code')=='FR'?'selected':'' ?>>France</option>
                                <option value="BE" <?= old('country_code')=='BE'?'selected':'' ?>>Belgique</option>
                                <option value="CH" <?= old('country_code')=='CH'?'selected':'' ?>>Suisse</option>
                                <option value="CA" <?= old('country_code')=='CA'?'selected':'' ?>>Canada</option>
                                <option value="US" <?= old('country_code')=='US'?'selected':'' ?>>Etats-Unis</option>
                                <option value="GB" <?= old('country_code')=='GB'?'selected':'' ?>>Royaume-Uni</option>
                                <option value="DE" <?= old('country_code')=='DE'?'selected':'' ?>>Allemagne</option>
                                <option value="ES" <?= old('country_code')=='ES'?'selected':'' ?>>Espagne</option>
                                <option value="IT" <?= old('country_code')=='IT'?'selected':'' ?>>Italie</option>
                                <option value="NL" <?= old('country_code')=='NL'?'selected':'' ?>>Pays-Bas</option>
                                <option value="SE" <?= old('country_code')=='SE'?'selected':'' ?>>Suede</option>
                                <option value="NO" <?= old('country_code')=='NO'?'selected':'' ?>>Norvege</option>
                            </select>
                            <div class="invalid-feedback">Veuillez selectionner un pays.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="map_link" class="form-label fw-semibold">Lien Google Maps</label>
                            <input type="url" name="map_link" id="map_link" class="form-control"
                                   value="<?= old('map_link') ?>" placeholder="https://maps.google.com/...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="wz-nav">
                <button type="button" class="btn btn-outline-secondary btn-prev" data-current="3">
                    <i class="bi bi-arrow-left me-1"></i>Precedent
                </button>
                <button type="button" class="btn btn-primary btn-next" data-current="3">
                    Suivant<i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- ═══ ETAPE 4 – Details ════════════════════ -->
        <div class="wz-panel" id="panel-4">
            <div class="wz-card">
                <div class="wz-card-header"><i class="bi bi-gear"></i>Details de l'organisation</div>
                <div class="wz-card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="founded_at" class="form-label fw-semibold">Date de fondation</label>
                            <input type="date" name="founded_at" id="founded_at" class="form-control"
                                   value="<?= old('founded_at') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="employee_count" class="form-label fw-semibold">Nombre d'employes</label>
                            <input type="number" name="employee_count" id="employee_count" class="form-control" min="0"
                                   value="<?= old('employee_count') ?>" placeholder="ex : 500">
                        </div>
                        <div class="col-md-4">
                            <label for="tax_id" class="form-label fw-semibold">N fiscal</label>
                            <input type="text" name="tax_id" id="tax_id" class="form-control"
                                   value="<?= old('tax_id') ?>" placeholder="NIF / SIRET / RC...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="industry" class="form-label fw-semibold">Secteur d'activite</label>
                        <select name="industry" id="industry" class="form-select">
                            <option value="">-- Selectionner --</option>
                            <option value="technology"            <?= old('industry')=='technology'           ?'selected':'' ?>>Technologie</option>
                            <option value="finance"               <?= old('industry')=='finance'              ?'selected':'' ?>>Finance</option>
                            <option value="healthcare"            <?= old('industry')=='healthcare'           ?'selected':'' ?>>Sante</option>
                            <option value="manufacturing"         <?= old('industry')=='manufacturing'        ?'selected':'' ?>>Industrie</option>
                            <option value="retail"                <?= old('industry')=='retail'               ?'selected':'' ?>>Commerce de detail</option>
                            <option value="real-estate"           <?= old('industry')=='real-estate'          ?'selected':'' ?>>Immobilier</option>
                            <option value="energy"                <?= old('industry')=='energy'               ?'selected':'' ?>>Energie</option>
                            <option value="transportation"        <?= old('industry')=='transportation'       ?'selected':'' ?>>Transport</option>
                            <option value="education"             <?= old('industry')=='education'            ?'selected':'' ?>>Education</option>
                            <option value="media"                 <?= old('industry')=='media'                ?'selected':'' ?>>Medias</option>
                            <option value="hospitality"           <?= old('industry')=='hospitality'          ?'selected':'' ?>>Hotellerie</option>
                            <option value="non-profit"            <?= old('industry')=='non-profit'           ?'selected':'' ?>>Non-profit</option>
                            <option value="government"            <?= old('industry')=='government'           ?'selected':'' ?>>Gouvernement</option>
                            <option value="professional-services" <?= old('industry')=='professional-services'?'selected':'' ?>>Services professionnels</option>
                            <option value="agriculture"           <?= old('industry')=='agriculture'          ?'selected':'' ?>>Agriculture</option>
                            <option value="telecommunications"    <?= old('industry')=='telecommunications'   ?'selected':'' ?>>Telecommunications</option>
                            <option value="consulting"            <?= old('industry')=='consulting'           ?'selected':'' ?>>Conseil</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="latitude" class="form-label fw-semibold">Latitude</label>
                            <input type="number" step="any" name="latitude" id="latitude" class="form-control"
                                   value="<?= old('latitude') ?>" placeholder="36.7538">
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label fw-semibold">Longitude</label>
                            <input type="number" step="any" name="longitude" id="longitude" class="form-control"
                                   value="<?= old('longitude') ?>" placeholder="3.0588">
                        </div>
                    </div>
                </div>
            </div>
            <div class="wz-nav">
                <button type="button" class="btn btn-outline-secondary btn-prev" data-current="4">
                    <i class="bi bi-arrow-left me-1"></i>Precedent
                </button>
                <button type="button" class="btn btn-primary btn-next" data-current="4">
                    Suivant<i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- ═══ ETAPE 5 – Medias ════════════════════ -->
        <div class="wz-panel" id="panel-5">
            <div class="wz-card">
                <div class="wz-card-header"><i class="bi bi-image"></i>Logo</div>
                <div class="wz-card-body">
                    <label for="logo" class="form-label fw-semibold">
                        Telecharger un logo <span class="fw-normal" style="color:var(--muted);">(max. 5 Mo)</span>
                    </label>
                    <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                    <div class="form-text">Formats acceptes : JPEG, PNG, WebP, SVG</div>
                    <div id="logoPreview" class="d-none mt-3">
                        <p style="font-size:.84rem;color:var(--muted);" class="mb-1">Apercu :</p>
                        <img id="logoImg" src="" alt="Apercu"
                             style="max-width:140px;max-height:84px;border-radius:8px;border:1px solid var(--border);padding:6px;background:#fff;">
                    </div>
                </div>
            </div>
            <div class="wz-card">
                <div class="wz-card-header"><i class="bi bi-share"></i>Reseaux sociaux</div>
                <div class="wz-card-body">
                    <div id="socialLinksContainer">
                        <div class="row g-2 mb-2 socialLink d-none" id="socialLinkTemplate">
                            <div class="col-md-4">
                                <select name="social_platform_" class="form-select form-select-sm platformSelect">
                                    <option value="">-- Plateforme --</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="twitter">Twitter / X</option>
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
                        <i class="bi bi-plus-lg me-1"></i>Ajouter un reseau
                    </button>
                </div>
            </div>
            <div class="wz-nav">
                <button type="button" class="btn btn-outline-secondary btn-prev" data-current="5">
                    <i class="bi bi-arrow-left me-1"></i>Precedent
                </button>
                <button type="submit" class="btn btn-primary btn-submit">
                    <i class="bi bi-buildings-fill me-1"></i>Creer l'organisation
                </button>
            </div>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const TOTAL = 5;
    let current = 1;

    function showStep(n) {
        document.querySelectorAll('.wz-panel').forEach(p => p.classList.remove('active'));
        document.getElementById('panel-' + n).classList.add('active');
        document.querySelectorAll('.wz-step').forEach(s => {
            const sn = +s.dataset.step;
            s.classList.remove('active', 'done');
            if (sn === n)      s.classList.add('active');
            else if (sn < n)   s.classList.add('done');
        });
        current = n;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validatePanel(n) {
        const panel = document.getElementById('panel-' + n);
        const fields = panel.querySelectorAll('[required]');
        let ok = true;
        fields.forEach(f => {
            if (!f.value.trim()) {
                f.classList.add('is-invalid');
                ok = false;
            } else {
                f.classList.remove('is-invalid');
            }
        });
        return ok;
    }

    document.getElementById('crForm').addEventListener('input', function (e) {
        if (e.target.value.trim()) e.target.classList.remove('is-invalid');
    });

    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', function () {
            if (validatePanel(+this.dataset.current)) showStep(+this.dataset.current + 1);
        });
    });

    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', function () {
            const n = +this.dataset.current;
            if (n > 1) showStep(n - 1);
        });
    });

    document.getElementById('logo').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => {
            document.getElementById('logoPreview').classList.remove('d-none');
            document.getElementById('logoImg').src = ev.target.result;
        };
        reader.readAsDataURL(file);
    });

    let slCount = 0;
    document.getElementById('addSocialLinkButton').addEventListener('click', function () {
        const tpl = document.getElementById('socialLinkTemplate');
        const clone = tpl.cloneNode(true);
        clone.id = '';
        clone.classList.remove('d-none');
        clone.querySelector('.platformSelect').name = 'social_platform_' + slCount;
        clone.querySelector('.urlInput').name       = 'social_url_' + slCount;
        document.getElementById('socialLinksContainer').appendChild(clone);
        slCount++;
    });

    document.getElementById('socialLinksContainer').addEventListener('click', function (e) {
        const btn = e.target.closest('.removeSocialLink');
        if (btn) btn.closest('.socialLink').remove();
    });

    document.getElementById('crForm').addEventListener('submit', function (e) {
        for (let i = 1; i <= TOTAL; i++) {
            if (!validatePanel(i)) {
                e.preventDefault();
                showStep(i);
                return;
            }
        }
    });

});
</script>

<?= $this->endSection() ?>
