<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isEdit = $isEdit ?? false;
$job    = $job    ?? null;
$v      = fn(string $f, $def = '') => old($f) !== null ? old($f) : ($job->$f ?? $def);
$chk    = fn(string $f) => old($f) !== null ? (bool)old($f) : (bool)($job->$f ?? false);

$languages  = $languages  ?? [];
$certs      = $certs      ?? [];
$questions  = $questions  ?? [];
$steps      = $steps      ?? [];
$skillsList = $skillsList ?? '';

$cecrl = ['A1','A2','B1','B2','C1','C2'];

$defaultLangs = !empty($languages) ? $languages : [
    (object)['language'=>'Français','level_code'=>'C1','is_required'=>1],
    (object)['language'=>'Anglais', 'level_code'=>'B2','is_required'=>1],
];
$defaultSteps = !empty($steps) ? $steps : [
    (object)['step_name'=>'Tri CV + présélection', 'description'=>'', 'responsible'=>'RH', 'duration_days'=>5],
    (object)['step_name'=>'Entretien RH', 'description'=>'', 'responsible'=>'Chargé(e) RH', 'duration_days'=>3],
    (object)['step_name'=>'Entretien technique', 'description'=>'', 'responsible'=>'Manager', 'duration_days'=>5],
    (object)['step_name'=>'Décision finale', 'description'=>'', 'responsible'=>'DRH', 'duration_days'=>3],
];
?>

<style>
.jf-section{background:#fff;border:1px solid var(--border);border-radius:var(--radius);margin-bottom:1.25rem;overflow:hidden;}
.jf-section-header{background:var(--brand-light);border-bottom:1px solid var(--border);padding:.75rem 1.25rem;display:flex;align-items:center;gap:.6rem;font-weight:700;font-size:.88rem;color:var(--brand-dark);}
.jf-section-body{padding:1.25rem;}
.jf-label{display:block;font-size:.82rem;font-weight:600;color:var(--text);margin-bottom:.3rem;}
.badge-required{font-size:.65rem;background:#fee2e2;color:#dc2626;border-radius:4px;padding:1px 5px;vertical-align:middle;}
.badge-optional{font-size:.65rem;background:var(--bg,#f8fafc);color:#64748b;border-radius:4px;padding:1px 5px;vertical-align:middle;}
.vis-badge{display:inline-flex;align-items:center;gap:4px;font-size:.7rem;font-weight:600;padding:2px 8px;border-radius:12px;}
.vis-public{background:#dcfce7;color:#166534;}
.vis-private{background:#f3e8ff;color:#6b21a8;}
.vis-cond{background:#fef9c3;color:#713f12;}
.repeatable-row{background:var(--bg,#f8fafc);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem;margin-bottom:.6rem;}
.btn-add-row{font-size:.8rem;border-style:dashed;}
</style>

<!-- Page header -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url($isEdit ? 'jobs/' . ($job->slug ?? $job->id) : 'jobs') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.3rem;">
            <i class="bi bi-briefcase-fill me-2" style="color:var(--brand-dark);"></i>
            <?= $isEdit ? 'Modifier l\'offre d\'emploi' : 'Publier une nouvelle offre' ?>
        </h2>
        <p class="text-muted mb-0" style="font-size:.8rem;">
            Entreprise&nbsp;: <strong><?= esc($company->name ?? '—') ?></strong>
            <?php if ($isEdit): ?> · Réf&nbsp;: <?= esc($job->internal_ref ?? '—') ?><?php endif; ?>
        </p>
    </div>
</div>

<!-- Validation errors -->
<?php if ($errors = session()->getFlashdata('errors')): ?>
<div class="alert alert-danger mb-3">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <ul class="mb-0 ps-3"><?php foreach ((array)$errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form action="<?= $isEdit ? base_url('jobs/update/'.$job->id) : base_url('jobs/store') ?>"
      method="post" id="jobForm">
<?= csrf_field() ?>

<div class="row g-4">
<div class="col-12 col-xl-8">

<!-- ── A. Informations générales ─────────────────────────────────────────── -->
<div class="jf-section">
    <div class="jf-section-header"><i class="bi bi-info-circle-fill"></i> A — Informations générales</div>
    <div class="jf-section-body">

        <div class="mb-3">
            <label class="jf-label">Intitulé du poste <span class="badge-required">Obligatoire</span> <span class="vis-badge vis-public ms-1"><i class="bi bi-eye"></i> Public</span></label>
            <input type="text" name="title" class="form-control" value="<?= esc($v('title')) ?>"
                   placeholder="ex : SAP BASIS Administrator Senior" required>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="jf-label">Référence interne <span class="badge-optional">Optionnel</span> <span class="vis-badge vis-private ms-1"><i class="bi bi-lock"></i> Privé</span></label>
                <input type="text" name="internal_ref" class="form-control form-control-sm" value="<?= esc($v('internal_ref')) ?>" placeholder="ex : RH-2026-047">
            </div>
            <div class="col-md-4">
                <label class="jf-label">Département <span class="badge-optional">Optionnel</span></label>
                <input type="text" name="department" class="form-control form-control-sm" value="<?= esc($v('department')) ?>" placeholder="ex : DSI / Infrastructure">
            </div>
            <div class="col-md-4">
                <label class="jf-label">Nb de postes <span class="vis-badge vis-private ms-1"><i class="bi bi-lock"></i> Privé</span></label>
                <input type="number" name="num_positions" class="form-control form-control-sm" value="<?= esc($v('num_positions', 1)) ?>" min="1" max="99">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="jf-label">Type de contrat <span class="badge-required">Obligatoire</span></label>
                <select name="contract_type" class="form-select form-select-sm" required id="contractType">
                    <?php foreach (['CDI'=>'CDI','CDD'=>'CDD','Freelance'=>'Freelance','Internship'=>'Stage','PartTime'=>'Temps partiel'] as $cv => $cl): ?>
                    <option value="<?= $cv ?>" <?= $v('contract_type') === $cv ? 'selected' : '' ?>><?= $cl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4" id="durationWrap" style="display:none;">
                <label class="jf-label">Durée <span class="vis-badge vis-cond"><i class="bi bi-sliders"></i> Si ≠ CDI</span></label>
                <input type="text" name="contract_duration" class="form-control form-control-sm" value="<?= esc($v('contract_duration')) ?>" placeholder="ex : 6 mois">
            </div>
            <div class="col-md-4">
                <label class="jf-label">Niveau hiérarchique</label>
                <select name="hierarchical_level" class="form-select form-select-sm">
                    <option value="">— Choisir —</option>
                    <?php foreach (['Junior','Mid-level','Senior','Lead','Manager','Directeur'] as $hl): ?>
                    <option value="<?= $hl ?>" <?= $v('hierarchical_level') === $hl ? 'selected' : '' ?>><?= $hl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-5">
                <label class="jf-label">Localisation <span class="vis-badge vis-public ms-1"><i class="bi bi-eye"></i> Public</span></label>
                <input type="text" name="location" class="form-control form-control-sm" value="<?= esc($v('location')) ?>" placeholder="Lyon, France">
            </div>
            <div class="col-md-3">
                <label class="jf-label">Télétravail</label>
                <select name="remote" class="form-select form-select-sm">
                    <?php foreach (['onsite'=>'Présentiel','hybrid'=>'Hybride','remote'=>'100% Remote'] as $rv => $rl): ?>
                    <option value="<?= $rv ?>" <?= $v('remote','onsite') === $rv ? 'selected' : '' ?>><?= $rl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="jf-label">Responsable direct <span class="vis-badge vis-private ms-1"><i class="bi bi-lock"></i> Privé</span></label>
                <input type="text" name="direct_manager" class="form-control form-control-sm" value="<?= esc($v('direct_manager')) ?>" placeholder="ex : Directeur Infrastructure">
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="jf-label">Visibilité</label>
                <select name="visibility_level" class="form-select form-select-sm">
                    <option value="public"     <?= $v('visibility_level','public') === 'public'     ? 'selected' : '' ?>>Public — Tout le monde</option>
                    <option value="logged_in"  <?= $v('visibility_level','public') === 'logged_in'  ? 'selected' : '' ?>>Connectés uniquement</option>
                    <option value="apply_only" <?= $v('visibility_level','public') === 'apply_only' ? 'selected' : '' ?>>Visible après candidature</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="internal_only" value="1" id="internalOnly"
                           <?= $chk('internal_only') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="internalOnly" style="font-size:.82rem;">
                        <span class="vis-badge vis-private"><i class="bi bi-lock"></i> Interne uniquement</span>
                    </label>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ── B. Description ──────────────────────────────────────────────────────── -->
<div class="jf-section">
    <div class="jf-section-header"><i class="bi bi-file-text-fill"></i> B — Description du poste</div>
    <div class="jf-section-body">

        <div class="mb-3">
            <label class="jf-label">Résumé / Accroche <span class="badge-required">Obligatoire</span> <span class="vis-badge vis-public ms-1"><i class="bi bi-eye"></i> Public</span></label>
            <textarea name="description" class="form-control" rows="4" required
                      placeholder="Décrivez le poste en 3-5 lignes percutantes…"><?= esc($v('description')) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="jf-label">Contexte de la mission <span class="vis-badge vis-public ms-1"><i class="bi bi-eye"></i> Public</span></label>
            <textarea name="mission_context" class="form-control" rows="3"
                      placeholder="Contexte de l'équipe, enjeux, environnement technique…"><?= esc($v('mission_context')) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="jf-label">Missions principales <span class="vis-badge vis-public ms-1"><i class="bi bi-eye"></i> Public</span></label>
            <textarea name="requirements" class="form-control" rows="4"
                      placeholder="• Administrer les systèmes SAP&#10;• Gérer les transports&#10;• …"><?= esc($v('requirements')) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="jf-label">Avantages & Bénéfices <span class="vis-badge vis-public ms-1"><i class="bi bi-eye"></i> Public</span></label>
            <textarea name="benefits" class="form-control" rows="2"
                      placeholder="TR, mutuelle, RTT, télétravail, budget formation…"><?= esc($v('benefits')) ?></textarea>
        </div>

        <div class="mb-0">
            <label class="jf-label">Notes internes recruteur <span class="vis-badge vis-private ms-1"><i class="bi bi-lock"></i> Privé</span></label>
            <textarea name="recruitment_notes" class="form-control" rows="2"
                      placeholder="Contexte confidentiel, budget, remplacement…"><?= esc($v('recruitment_notes')) ?></textarea>
        </div>

    </div>
</div>

<!-- ── C. Profil recherché ────────────────────────────────────────────────── -->
<div class="jf-section">
    <div class="jf-section-header"><i class="bi bi-person-badge-fill"></i> C — Profil recherché</div>
    <div class="jf-section-body">

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="jf-label">Expérience min. (années)</label>
                <input type="number" name="min_experience_years" class="form-control form-control-sm"
                       value="<?= esc($v('min_experience_years', 0)) ?>" min="0" max="50">
            </div>
            <div class="col-md-3">
                <label class="jf-label">Niveau d'expérience</label>
                <select name="experience_level" class="form-select form-select-sm">
                    <?php foreach (['any'=>'Tous niveaux','junior'=>'Junior','mid'=>'Confirmé','senior'=>'Senior','lead'=>'Lead / Expert'] as $ev => $el): ?>
                    <option value="<?= $ev ?>" <?= $v('experience_level','any') === $ev ? 'selected' : '' ?>><?= $el ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="jf-label">Niveau d'études</label>
                <select name="education_level" class="form-select form-select-sm">
                    <option value="">— Non précisé —</option>
                    <?php foreach (['Bac','Bac+2','Bac+3','Bac+5 (Master / Ingénieur)','MBA / Doctorat'] as $ed): ?>
                    <option value="<?= $ed ?>" <?= $v('education_level') === $ed ? 'selected' : '' ?>><?= $ed ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="jf-label">Domaine de formation</label>
                <input type="text" name="education_field" class="form-control form-control-sm"
                       value="<?= esc($v('education_field')) ?>" placeholder="ex : Informatique">
            </div>
        </div>

        <div class="mb-0">
            <label class="jf-label">Compétences clés <span class="vis-badge vis-public ms-1"><i class="bi bi-eye"></i> Public</span></label>
            <input type="text" name="skills" class="form-control form-control-sm"
                   value="<?= esc($skillsList ?: $v('skills')) ?>"
                   placeholder="SAP BASIS, HANA DB, Linux, Python (séparées par des virgules)">
            <div class="form-text" style="font-size:.75rem;">Compétences requises et souhaitées, séparées par des virgules.</div>
        </div>

    </div>
</div>

<!-- ── D. Langues ──────────────────────────────────────────────────────────── -->
<div class="jf-section">
    <div class="jf-section-header">
        <i class="bi bi-translate"></i> D — Langues requises
        <span class="vis-badge vis-public ms-auto"><i class="bi bi-eye"></i> Public</span>
    </div>
    <div class="jf-section-body">
        <div id="langRows">
        <?php foreach ($defaultLangs as $i => $lr): ?>
        <div class="repeatable-row d-flex gap-2 align-items-center flex-wrap" data-lang-row>
            <div style="flex:2;min-width:130px;">
                <label class="jf-label">Langue</label>
                <input type="text" name="languages[<?= $i ?>][language]" class="form-control form-control-sm"
                       value="<?= esc($lr->language ?? '') ?>" placeholder="ex : Anglais">
            </div>
            <div style="flex:1;min-width:100px;">
                <label class="jf-label">Niveau CECRL</label>
                <select name="languages[<?= $i ?>][level_code]" class="form-select form-select-sm">
                    <?php foreach ($cecrl as $lv): ?>
                    <option value="<?= $lv ?>" <?= ($lr->level_code ?? 'B2') === $lv ? 'selected' : '' ?>><?= $lv ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex:1;min-width:110px;">
                <label class="jf-label">Obligatoire</label>
                <select name="languages[<?= $i ?>][is_required]" class="form-select form-select-sm">
                    <option value="1" <?= ($lr->is_required ?? 1) ? 'selected' : '' ?>>Oui — Requis</option>
                    <option value="0" <?= !($lr->is_required ?? 1) ? 'selected' : '' ?>>Souhaité</option>
                </select>
            </div>
            <div style="padding-top:20px;">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash3"></i></button>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary btn-add-row mt-2" onclick="addLangRow()">
            <i class="bi bi-plus-circle me-1"></i>Ajouter une langue
        </button>
    </div>
</div>

<!-- ── E. Certifications ──────────────────────────────────────────────────── -->
<div class="jf-section">
    <div class="jf-section-header">
        <i class="bi bi-patch-check-fill"></i> E — Certifications
        <span class="vis-badge vis-public ms-auto"><i class="bi bi-eye"></i> Public</span>
    </div>
    <div class="jf-section-body">
        <div id="certRows">
        <?php foreach ($certs as $i => $cr): ?>
        <div class="repeatable-row d-flex gap-2 align-items-center flex-wrap" data-cert-row>
            <div style="flex:3;min-width:200px;">
                <label class="jf-label">Certification</label>
                <input type="text" name="certs[<?= $i ?>][certification_name]" class="form-control form-control-sm"
                       value="<?= esc($cr->certification_name ?? '') ?>" placeholder="ex : SAP Certified Technology Associate">
            </div>
            <div style="flex:1;min-width:110px;">
                <label class="jf-label">Type</label>
                <select name="certs[<?= $i ?>][is_required]" class="form-select form-select-sm">
                    <option value="1" <?=  ($cr->is_required ?? 0) ? 'selected' : '' ?>>Requise</option>
                    <option value="0" <?= !($cr->is_required ?? 0) ? 'selected' : '' ?>>Souhaitée</option>
                </select>
            </div>
            <div style="flex:1;min-width:90px;">
                <label class="jf-label">Délai (mois)</label>
                <input type="number" name="certs[<?= $i ?>][delay_months]" class="form-control form-control-sm"
                       value="<?= esc($cr->delay_months ?? '') ?>" min="0" max="24" placeholder="—">
            </div>
            <div style="padding-top:20px;">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash3"></i></button>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary btn-add-row mt-2" onclick="addCertRow()">
            <i class="bi bi-plus-circle me-1"></i>Ajouter une certification
        </button>
    </div>
</div>

<!-- ── F. Questions de présélection ──────────────────────────────────────── -->
<div class="jf-section">
    <div class="jf-section-header">
        <i class="bi bi-patch-question-fill"></i> F — Questions de présélection
        <span class="vis-badge vis-public ms-auto"><i class="bi bi-eye"></i> Public (réponses privées)</span>
    </div>
    <div class="jf-section-body">
        <div id="qRows">
        <?php foreach ($questions as $i => $qr): ?>
        <div class="repeatable-row" data-q-row>
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="jf-label">Question</label>
                    <input type="text" name="questions[<?= $i ?>][question_text]" class="form-control form-control-sm"
                           value="<?= esc($qr->question_text ?? '') ?>" placeholder="ex : Avez-vous 3 ans d'exp. SAP BASIS ?">
                </div>
                <div class="col-6 col-md-2">
                    <label class="jf-label">Type</label>
                    <select name="questions[<?= $i ?>][question_type]" class="form-select form-select-sm" onchange="toggleExpected(this)">
                        <option value="yes_no" <?= ($qr->question_type??'yes_no')==='yes_no'?'selected':''?>>Oui / Non</option>
                        <option value="text"   <?= ($qr->question_type??'')==='text'   ?'selected':''?>>Texte libre</option>
                        <option value="number" <?= ($qr->question_type??'')==='number' ?'selected':''?>>Nombre</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 expected-wrap">
                    <label class="jf-label">Réponse attendue <span class="vis-badge vis-private"><i class="bi bi-lock"></i></span></label>
                    <input type="text" name="questions[<?= $i ?>][expected_answer]" class="form-control form-control-sm"
                           value="<?= esc($qr->expected_answer ?? '') ?>" placeholder="Oui">
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="questions[<?= $i ?>][is_eliminatory]" value="1"
                               id="elim_<?= $i ?>" <?= ($qr->is_eliminatory??0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="elim_<?= $i ?>" style="font-size:.78rem;">
                            Eliminatoire <span class="vis-badge vis-private"><i class="bi bi-lock"></i></span>
                        </label>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger mt-3" onclick="removeRow(this)"><i class="bi bi-trash3"></i></button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary btn-add-row mt-2" onclick="addQRow()">
            <i class="bi bi-plus-circle me-1"></i>Ajouter une question
        </button>
    </div>
</div>

<!-- ── G. Process de recrutement ─────────────────────────────────────────── -->
<div class="jf-section">
    <div class="jf-section-header">
        <i class="bi bi-diagram-3-fill"></i> G — Process de recrutement
        <span class="vis-badge vis-public ms-auto"><i class="bi bi-eye"></i> Public</span>
    </div>
    <div class="jf-section-body">
        <div id="stepRows">
        <?php foreach ($defaultSteps as $i => $sr): ?>
        <div class="repeatable-row" data-step-row>
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge" style="background:var(--brand-dark);font-size:.75rem;">Étape <?= $i+1 ?></span>
                <input type="text" name="steps[<?= $i ?>][step_name]" class="form-control form-control-sm fw-semibold"
                       value="<?= esc($sr->step_name ?? '') ?>" placeholder="Nom de l'étape" style="max-width:260px;">
                <input type="text" name="steps[<?= $i ?>][responsible]" class="form-control form-control-sm"
                       value="<?= esc($sr->responsible ?? '') ?>" placeholder="Responsable" style="max-width:160px;">
                <input type="number" name="steps[<?= $i ?>][duration_days]" class="form-control form-control-sm"
                       value="<?= esc($sr->duration_days ?? '') ?>" placeholder="Jours" style="max-width:80px;" min="0">
                <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="removeRow(this)"><i class="bi bi-trash3"></i></button>
            </div>
            <textarea name="steps[<?= $i ?>][description]" class="form-control form-control-sm" rows="1"
                      placeholder="Description optionnelle…"><?= esc($sr->description ?? '') ?></textarea>
        </div>
        <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary btn-add-row mt-2" onclick="addStepRow()">
            <i class="bi bi-plus-circle me-1"></i>Ajouter une étape
        </button>
    </div>
</div>

</div><!-- col-8 -->

<!-- ── Right sidebar ──────────────────────────────────────────────────────── -->
<div class="col-12 col-xl-4">

<!-- Rémunération -->
<div class="jf-section">
    <div class="jf-section-header"><i class="bi bi-cash-coin"></i> Rémunération</div>
    <div class="jf-section-body">

        <div class="row g-2 mb-2">
            <div class="col-6">
                <label class="jf-label">Minimum</label>
                <input type="number" name="salary_min" class="form-control form-control-sm"
                       value="<?= esc($v('salary_min')) ?>" placeholder="55000">
            </div>
            <div class="col-6">
                <label class="jf-label">Maximum</label>
                <input type="number" name="salary_max" class="form-control form-control-sm"
                       value="<?= esc($v('salary_max')) ?>" placeholder="70000">
            </div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="jf-label">Devise</label>
                <select name="salary_currency" class="form-select form-select-sm">
                    <?php foreach (['EUR'=>'€ EUR','USD'=>'$ USD','GBP'=>'£ GBP','MAD'=>'MAD','DZD'=>'DZD','TND'=>'TND'] as $cv => $cl): ?>
                    <option value="<?= $cv ?>" <?= $v('salary_currency','EUR') === $cv ? 'selected' : '' ?>><?= $cl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6">
                <label class="jf-label">Périodicité</label>
                <select name="salary_period" class="form-select form-select-sm">
                    <?php foreach (['annual'=>'Annuel','monthly'=>'Mensuel','daily'=>'Journalier','hourly'=>'Horaire'] as $pv => $pl): ?>
                    <option value="<?= $pv ?>" <?= $v('salary_period','annual') === $pv ? 'selected' : '' ?>><?= $pl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="salary_public" value="1"
                   id="salaryPublic" <?= $chk('salary_public') ? 'checked' : '' ?>>
            <label class="form-check-label" for="salaryPublic" style="font-size:.82rem;">
                <span class="vis-badge vis-cond"><i class="bi bi-sliders"></i></span>
                Afficher la fourchette aux candidats
            </label>
        </div>

        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="salary_variable" value="1"
                   id="salaryVariable" <?= $chk('salary_variable') ? 'checked' : '' ?>
                   onchange="document.getElementById('bonusPctWrap').style.display=this.checked?'block':'none'">
            <label class="form-check-label" for="salaryVariable" style="font-size:.82rem;">
                Part variable / bonus
            </label>
        </div>

        <div id="bonusPctWrap" style="display:<?= $chk('salary_variable') ? 'block' : 'none' ?>">
            <label class="jf-label">Variable (% du fixe)</label>
            <input type="number" name="salary_bonus_pct" class="form-control form-control-sm"
                   value="<?= esc($v('salary_bonus_pct')) ?>" min="0" max="100" placeholder="15">
        </div>

    </div>
</div>

<!-- Documents requis -->
<div class="jf-section">
    <div class="jf-section-header"><i class="bi bi-file-earmark-arrow-up-fill"></i> Documents requis</div>
    <div class="jf-section-body">
        <p class="text-muted mb-3" style="font-size:.78rem;">Indiquez les documents que le candidat doit fournir lors de sa candidature.</p>

        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="require_cv" value="1"
                   id="requireCv" <?= $chk('require_cv') || (!$isEdit && !old('require_cv')) ? 'checked' : '' ?>>
            <label class="form-check-label d-flex align-items-center gap-2" for="requireCv" style="font-size:.82rem;">
                <i class="bi bi-file-earmark-person-fill" style="color:var(--brand-dark);"></i>
                CV <span class="badge-required">Recommandé</span>
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="require_cover_letter" value="1"
                   id="requireCoverLetter" <?= $chk('require_cover_letter') ? 'checked' : '' ?>>
            <label class="form-check-label d-flex align-items-center gap-2" for="requireCoverLetter" style="font-size:.82rem;">
                <i class="bi bi-envelope-paper-fill" style="color:#6366f1;"></i>
                Lettre de motivation
            </label>
        </div>
    </div>
</div>

<!-- Publication -->
<div class="jf-section">
    <div class="jf-section-header"><i class="bi bi-send-fill"></i> Publication</div>
    <div class="jf-section-body">
        <div class="mb-3">
            <label class="jf-label">Date d'expiration de l'offre</label>
            <input type="date" name="expires_at" class="form-control form-control-sm"
                   value="<?= esc($v('expires_at')) ?>" min="<?= date('Y-m-d') ?>">
        </div>
        <?php if ($isEdit): ?>
        <div class="mb-3">
            <label class="jf-label">Statut</label>
            <select name="status" class="form-select form-select-sm">
                <option value="active" <?= ($job->status ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="paused" <?= ($job->status ?? '') === 'paused' ? 'selected' : '' ?>>Suspendue</option>
                <option value="closed" <?= ($job->status ?? '') === 'closed' ? 'selected' : '' ?>>Fermée</option>
            </select>
        </div>
        <?php endif; ?>
        <div class="d-grid gap-2 mt-3">
            <button type="submit" class="btn btn-primary fw-bold">
                <i class="bi bi-<?= $isEdit ? 'check2-circle' : 'send' ?> me-2"></i>
                <?= $isEdit ? 'Enregistrer les modifications' : 'Publier l\'offre' ?>
            </button>
            <a href="<?= base_url($isEdit ? 'jobs/' . ($job->slug ?? $job->id) : 'jobs') ?>" class="btn btn-outline-secondary btn-sm">Annuler</a>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="jf-section">
    <div class="jf-section-header"><i class="bi bi-info-circle"></i> Légende</div>
    <div class="jf-section-body" style="font-size:.78rem;">
        <div class="d-flex align-items-center gap-2 mb-1"><span class="vis-badge vis-public"><i class="bi bi-eye"></i> Public</span> Visible par tous les candidats</div>
        <div class="d-flex align-items-center gap-2 mb-1"><span class="vis-badge vis-private"><i class="bi bi-lock"></i> Privé</span> Visible recruteur uniquement</div>
        <div class="d-flex align-items-center gap-2"><span class="vis-badge vis-cond"><i class="bi bi-sliders"></i> Conditionnel</span> Affiché selon un paramètre</div>
    </div>
</div>

</div><!-- sidebar -->
</div><!-- row -->
</form>

<script>
let langIdx  = <?= count($defaultLangs) ?>;
let certIdx  = <?= count($certs) ?>;
let qIdx     = <?= count($questions) ?>;
let stepIdx  = <?= count($defaultSteps) ?>;

// Contract type → duration field visibility
(function(){
    const ct=document.getElementById('contractType');
    const dw=document.getElementById('durationWrap');
    function t(){dw.style.display=ct.value!=='CDI'?'':'none';}
    ct.addEventListener('change',t); t();
})();

function removeRow(btn){
    btn.closest('[data-lang-row],[data-cert-row],[data-q-row],[data-step-row]').remove();
}

function addLangRow(){
    const i=langIdx++;
    document.getElementById('langRows').insertAdjacentHTML('beforeend',
    `<div class="repeatable-row d-flex gap-2 align-items-center flex-wrap" data-lang-row>
        <div style="flex:2;min-width:130px;"><label class="jf-label">Langue</label>
        <input type="text" name="languages[${i}][language]" class="form-control form-control-sm" placeholder="ex : Espagnol"></div>
        <div style="flex:1;min-width:100px;"><label class="jf-label">Niveau CECRL</label>
        <select name="languages[${i}][level_code]" class="form-select form-select-sm">
        ${['A1','A2','B1','B2','C1','C2'].map(l=>`<option${l==='B2'?' selected':''}>${l}</option>`).join('')}
        </select></div>
        <div style="flex:1;min-width:110px;"><label class="jf-label">Obligatoire</label>
        <select name="languages[${i}][is_required]" class="form-select form-select-sm">
        <option value="1" selected>Oui — Requis</option><option value="0">Souhaité</option></select></div>
        <div style="padding-top:20px;"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash3"></i></button></div>
    </div>`);
}

function addCertRow(){
    const i=certIdx++;
    document.getElementById('certRows').insertAdjacentHTML('beforeend',
    `<div class="repeatable-row d-flex gap-2 align-items-center flex-wrap" data-cert-row>
        <div style="flex:3;min-width:200px;"><label class="jf-label">Certification</label>
        <input type="text" name="certs[${i}][certification_name]" class="form-control form-control-sm" placeholder="ex : ITIL v4 Foundation"></div>
        <div style="flex:1;min-width:110px;"><label class="jf-label">Type</label>
        <select name="certs[${i}][is_required]" class="form-select form-select-sm">
        <option value="1">Requise</option><option value="0" selected>Souhaitée</option></select></div>
        <div style="flex:1;min-width:90px;"><label class="jf-label">Délai (mois)</label>
        <input type="number" name="certs[${i}][delay_months]" class="form-control form-control-sm" min="0" max="24" placeholder="—"></div>
        <div style="padding-top:20px;"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash3"></i></button></div>
    </div>`);
}

function addQRow(){
    const i=qIdx++;
    document.getElementById('qRows').insertAdjacentHTML('beforeend',
    `<div class="repeatable-row" data-q-row>
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-5"><label class="jf-label">Question</label>
            <input type="text" name="questions[${i}][question_text]" class="form-control form-control-sm" placeholder="ex : Êtes-vous disponible immédiatement ?"></div>
            <div class="col-6 col-md-2"><label class="jf-label">Type</label>
            <select name="questions[${i}][question_type]" class="form-select form-select-sm" onchange="toggleExpected(this)">
            <option value="yes_no" selected>Oui / Non</option><option value="text">Texte libre</option><option value="number">Nombre</option></select></div>
            <div class="col-6 col-md-2 expected-wrap"><label class="jf-label">Réponse attendue</label>
            <input type="text" name="questions[${i}][expected_answer]" class="form-control form-control-sm" placeholder="Oui"></div>
            <div class="col-6 col-md-2"><div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="questions[${i}][is_eliminatory]" value="1" id="elim_${i}">
            <label class="form-check-label" for="elim_${i}" style="font-size:.78rem;">Eliminatoire</label></div></div>
            <div class="col-auto"><button type="button" class="btn btn-sm btn-outline-danger mt-3" onclick="removeRow(this)"><i class="bi bi-trash3"></i></button></div>
        </div>
    </div>`);
}

function addStepRow(){
    const i=stepIdx++;
    document.getElementById('stepRows').insertAdjacentHTML('beforeend',
    `<div class="repeatable-row" data-step-row>
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <span class="badge" style="background:var(--brand-dark);font-size:.75rem;">Étape ${i+1}</span>
            <input type="text" name="steps[${i}][step_name]" class="form-control form-control-sm fw-semibold" placeholder="Nom de l'étape" style="max-width:260px;">
            <input type="text" name="steps[${i}][responsible]" class="form-control form-control-sm" placeholder="Responsable" style="max-width:160px;">
            <input type="number" name="steps[${i}][duration_days]" class="form-control form-control-sm" placeholder="Jours" style="max-width:80px;" min="0">
            <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="removeRow(this)"><i class="bi bi-trash3"></i></button>
        </div>
        <textarea name="steps[${i}][description]" class="form-control form-control-sm" rows="1" placeholder="Description optionnelle…"></textarea>
    </div>`);
}

function toggleExpected(sel){
    const wrap=sel.closest('.row').querySelector('.expected-wrap');
    if(wrap) wrap.style.display=sel.value==='text'?'none':'';
}
</script>

<!-- Scroll to top button -->
<button id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})" title="Haut de page"
        style="display:none;position:fixed;bottom:1.5rem;right:1.5rem;z-index:1050;
               width:42px;height:42px;border-radius:50%;border:none;
               background:var(--brand-dark);color:#fff;font-size:1.1rem;
               box-shadow:var(--shadow);cursor:pointer;transition:opacity .2s;">
    <i class="bi bi-arrow-up"></i>
</button>
<script>
(function(){
    const btn = document.getElementById('scrollTopBtn');
    window.addEventListener('scroll', function(){
        btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
        btn.style.alignItems = 'center';
        btn.style.justifyContent = 'center';
    });
})();
</script>

<?= $this->endSection() ?>
