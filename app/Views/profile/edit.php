<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.cv-sec-head {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #1a1a2e;
    border-bottom: 2px solid #1a1a2e;
    padding-bottom: 2px;
    margin: 14px 0 5px;
}
#cv-preview {
    font-family: 'Calibri', 'Arial', sans-serif;
    font-size: 11.5px;
    line-height: 1.55;
    color: #111;
}
@media print {
    #forms-col, nav, header, .navbar, footer { display: none !important; }
    #cv-col { width: 100% !important; max-height: none !important; display: block !important; }
    #cv-preview { box-shadow: none !important; }
}
</style>

<div class="row g-0">
<!-- ═════════════════ LEFT : Formulaires ═════════════════ -->
<div class="col-12 col-lg-7 border-end" id="forms-col" style="padding:1.25rem 1.75rem;">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= base_url('profile') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i><?= lang('App.btn_back') ?>
            </a>
            <h3 class="fw-bold mb-0"><?= lang('App.edit_profile') ?></h3>
        </div>

        <!-- ── LinkedIn Import Card ──────────────────────────────────────── -->
        <div class="card mb-4" style="border: 2px solid #0A66C2 !important; border-radius: 12px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:48px;height:48px;background:#0A66C2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-linkedin text-white" style="font-size:1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0"><?= lang('App.linkedin_connect_title') ?></h5>
                        <p class="text-muted mb-0 small"><?= lang('App.linkedin_import_info') ?></p>
                    </div>
                </div>

                <p class="small fw-semibold mb-2"><?= lang('App.linkedin_what_imported') ?></p>
                <div class="row g-2 mb-3">
                    <?php foreach (['linkedin_field_name', 'linkedin_field_photo', 'linkedin_field_headline', 'linkedin_field_url'] as $key): ?>
                    <div class="col-auto">
                        <span class="badge rounded-pill" style="background:#e8f0fe;color:#0A66C2;">
                            <i class="bi bi-check-circle me-1"></i><?= lang('App.' . $key) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <a href="<?= base_url('linkedin/connect') ?>"
                       class="btn btn-sm fw-semibold text-white"
                       style="background:#0A66C2;border-color:#0A66C2;">
                        <i class="bi bi-linkedin me-1"></i><?= lang('App.linkedin_connect_btn') ?>
                    </a>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i><?= lang('App.linkedin_note') ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Basic Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-person me-2 text-primary"></i><?= lang('App.section_about') ?></h5>
            </div>
            <div class="card-body">
                <form id="basicInfoForm" action="<?= base_url('profile/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <?php
                        // Safe access for columns that may not yet exist on production DB
                        $profilePosition   = isset($profile->position)   ? $profile->position   : null;
                        $profileDepartment = isset($profile->department)  ? $profile->department : null;
                        $profilePhoneCode  = isset($profile->phone_code)  ? $profile->phone_code : null;
                        ?>

                        <!-- Position (niveau hiérarchique) -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_position') ?></label>
                            <select name="position" class="form-select">
                                <option value=""><?= lang('App.select_position') ?></option>
                                <?php
                                $positions = [
                                    'Directeur','Manager','Team Lead','Tech Lead',
                                    'Chef de projet','Chef d\'équipe','Responsable',
                                    'Collaborateur',
                                ];
                                $currentPos = old('position', $profilePosition ?? '');
                                foreach ($positions as $pos):
                                ?>
                                <option value="<?= esc($pos) ?>" <?= $currentPos === $pos ? 'selected' : '' ?>>
                                    <?= esc($pos) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Job Title -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_job_title') ?></label>
                            <input type="text" name="headline" class="form-control"
                                   id="jobTitleInput"
                                   value="<?= esc(old('headline', $profile?->headline)) ?>"
                                   placeholder="<?= lang('App.placeholder_job_title') ?>"
                                   list="jobTitleSuggestions">
                            <datalist id="jobTitleSuggestions">
                                <?php foreach ([
                                    'Développeur PHP','Développeur Full Stack','Développeur Front-end',
                                    'Développeur Back-end','DevOps Engineer','Data Scientist',
                                    'Product Manager','UX Designer','QA Engineer',
                                    'Architecte logiciel','CTO','DRH','Comptable','Commercial',
                                    'Ingénieur réseaux','Administrateur système',
                                ] as $jt): ?>
                                <option value="<?= esc($jt) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>

                        <!-- Département -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_department') ?></label>
                            <input type="text" name="department" class="form-control"
                                   value="<?= esc(old('department', $profileDepartment)) ?>"
                                   placeholder="<?= lang('App.placeholder_department') ?>"
                                   list="departmentSuggestions">
                            <datalist id="departmentSuggestions">
                                <?php foreach ([
                                    'Informatique','Développement','Ingénierie','Finance',
                                    'Comptabilité','Ressources Humaines','Marketing',
                                    'Commercial','Juridique','Direction','Logistique',
                                    'Production','R&D','Support','Communication',
                                ] as $dept): ?>
                                <option value="<?= esc($dept) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>

                        <!-- Summary -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Summary</label>
                            <textarea name="summary" class="form-control" rows="4"
                                      placeholder="Tell employers about yourself..."><?= esc(old('summary', $profile?->summary)) ?></textarea>
                        </div>

                        <!-- Phone with country code -->
                        <div class="col-md-5">
                            <label class="form-label fw-semibold"><?= lang('App.field_phone') ?></label>
                            <div class="input-group">
                                <select name="phone_code" id="phoneCodeSelect" class="form-select" style="max-width:140px;">
                                    <?php
                                    $phoneCodes = [
                                        'DZ' => ['+213', '🇩🇿 DZ'],
                                        'FR' => ['+33',  '🇫🇷 FR'],
                                        'MA' => ['+212', '🇲🇦 MA'],
                                        'TN' => ['+216', '🇹🇳 TN'],
                                        'LY' => ['+218', '🇱🇾 LY'],
                                        'EG' => ['+20',  '🇪🇬 EG'],
                                        'SA' => ['+966', '🇸🇦 SA'],
                                        'AE' => ['+971', '🇦🇪 AE'],
                                        'QA' => ['+974', '🇶🇦 QA'],
                                        'KW' => ['+965', '🇰🇼 KW'],
                                        'BH' => ['+973', '🇧🇭 BH'],
                                        'OM' => ['+968', '🇴🇲 OM'],
                                        'JO' => ['+962', '🇯🇴 JO'],
                                        'LB' => ['+961', '🇱🇧 LB'],
                                        'SY' => ['+963', '🇸🇾 SY'],
                                        'IQ' => ['+964', '🇮🇶 IQ'],
                                        'GB' => ['+44',  '🇬🇧 UK'],
                                        'DE' => ['+49',  '🇩🇪 DE'],
                                        'ES' => ['+34',  '🇪🇸 ES'],
                                        'IT' => ['+39',  '🇮🇹 IT'],
                                        'US' => ['+1',   '🇺🇸 US'],
                                        'CA' => ['+1',   '🇨🇦 CA'],
                                        'BE' => ['+32',  '🇧🇪 BE'],
                                        'CH' => ['+41',  '🇨🇭 CH'],
                                        'PT' => ['+351', '🇵🇹 PT'],
                                        'NL' => ['+31',  '🇳🇱 NL'],
                                        'SE' => ['+46',  '🇸🇪 SE'],
                                        'TR' => ['+90',  '🇹🇷 TR'],
                                        'BR' => ['+55',  '🇧🇷 BR'],
                                        'SN' => ['+221', '🇸🇳 SN'],
                                        'CI' => ['+225', '🇨🇮 CI'],
                                        'CM' => ['+237', '🇨🇲 CM'],
                                    ];
                                    $savedCode = old('phone_code', $profilePhoneCode ?? '+213');
                                    foreach ($phoneCodes as $iso => [$code, $label]):
                                    ?>
                                    <option value="<?= $code ?>" <?= $savedCode === $code ? 'selected' : '' ?>>
                                        <?= $label ?> <?= $code ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="tel" name="phone" id="phoneNumber" class="form-control"
                                       value="<?= esc(old('phone', $profile?->phone)) ?>"
                                       placeholder="6 12 34 56 78"
                                       pattern="[0-9\s\-]{6,15}">
                            </div>
                            <div class="form-text text-muted" id="phonePreview"></div>
                        </div>

                        <!-- City with datalist -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_city') ?></label>
                            <input type="text" name="city" id="cityInput" class="form-control"
                                   value="<?= esc(old('city', $profile?->city)) ?>"
                                   placeholder="<?= lang('App.placeholder_city') ?>"
                                   list="citySuggestions" autocomplete="off">
                            <datalist id="citySuggestions"></datalist>
                        </div>

                        <!-- Country select -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold"><?= lang('App.field_country') ?></label>
                            <select name="country" id="countrySelect" class="form-select">
                                <option value=""><?= lang('App.select_country') ?></option>
                                <?php
                                $countries = [
                                    'Algeria'=>'Algérie','France'=>'France','Morocco'=>'Maroc',
                                    'Tunisia'=>'Tunisie','Libya'=>'Libye','Egypt'=>'Égypte',
                                    'Saudi Arabia'=>'Arabie Saoudite','United Arab Emirates'=>'Émirats Arabes Unis',
                                    'Qatar'=>'Qatar','Kuwait'=>'Koweït','Bahrain'=>'Bahreïn',
                                    'Oman'=>'Oman','Jordan'=>'Jordanie','Lebanon'=>'Liban',
                                    'Syria'=>'Syrie','Iraq'=>'Irak','Germany'=>'Allemagne',
                                    'Belgium'=>'Belgique','Switzerland'=>'Suisse',
                                    'Canada'=>'Canada','United States'=>'États-Unis',
                                    'United Kingdom'=>'Royaume-Uni','Spain'=>'Espagne',
                                    'Italy'=>'Italie','Portugal'=>'Portugal',
                                    'Netherlands'=>'Pays-Bas','Sweden'=>'Suède','Turkey'=>'Turquie',
                                    'Senegal'=>'Sénégal','Ivory Coast'=>'Côte d\'Ivoire',
                                    'Cameroon'=>'Cameroun','Brazil'=>'Brésil',
                                ];
                                $savedCountry = old('country', $profile?->country ?? '');
                                foreach ($countries as $en => $fr):
                                ?>
                                <option value="<?= esc($en) ?>" <?= $savedCountry === $en ? 'selected' : '' ?>>
                                    <?= esc($fr) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_linkedin') ?></label>
                            <input type="url" name="linkedin" class="form-control"
                                   value="<?= esc(old('linkedin', $profile?->linkedin)) ?>" placeholder="https://linkedin.com/in/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_github') ?></label>
                            <input type="url" name="github" class="form-control"
                                   value="<?= esc(old('github', $profile?->github)) ?>" placeholder="https://github.com/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_portfolio') ?></label>
                            <input type="url" name="portfolio" class="form-control"
                                   value="<?= esc(old('portfolio', $profile?->portfolio)) ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold"><?= lang('App.field_skills') ?> <span class="text-muted small">(<?= lang('App.skills_hint') ?>)</span></label>
                            <input type="text" name="skills" class="form-control"
                                   value="<?= esc(implode(', ', array_column((array) $skills, 'skill_name'))) ?>"
                                   placeholder="PHP, JavaScript, MySQL...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_desired_salary') ?></label>
                            <input type="text" name="desired_salary" class="form-control"
                                   value="<?= esc(old('desired_salary', $profile?->desired_salary)) ?>" placeholder="45000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_desired_contract') ?></label>
                            <select name="desired_contract" class="form-select">
                                <option value="">Any</option>
                                <?php foreach (['CDI','CDD','Freelance','Internship','PartTime'] as $ct): ?>
                                    <option value="<?= $ct ?>" <?= old('desired_contract', $profile?->desired_contract) === $ct ? 'selected' : '' ?>><?= $ct ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_availability') ?></label>
                            <input type="text" name="availability" class="form-control"
                                   value="<?= esc(old('availability', $profile?->availability)) ?>" placeholder="Immediately / 1 month">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="bi bi-save me-1"></i><?= lang('App.btn_save_profile') ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- CV Upload -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-person me-2 text-success"></i><?= lang('App.section_cv') ?></h5>
            </div>
            <div class="card-body">
                <?php if (!empty($profile?->cv_file)): ?>
                    <div class="alert alert-success d-flex justify-content-between align-items-center py-2">
                        <span><i class="bi bi-file-earmark-check me-2"></i><?= esc($profile->cv_original_name ?? $profile->cv_file) ?></span>
                        <a href="<?= base_url('profile/cv/download') ?>" class="btn btn-sm btn-success">
                            <i class="bi bi-download me-1"></i>Download
                        </a>
                    </div>
                <?php endif; ?>
                <form action="<?= base_url('profile/cv/upload') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="input-group">
                        <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx" required>
                        <button type="submit" class="btn btn-outline-success">
                            <i class="bi bi-upload me-1"></i><?= lang('App.btn_upload_cv') ?>
                        </button>
                    </div>
                    <div class="form-text"><?= lang('App.cv_hint_size') ?></div>
                </form>

                <?php if (!empty($profile?->cv_file)): ?>
                <!-- Smart Profile Fill Section -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-stars me-1" style="color:#6366f1;"></i>Analyse Intelligente du CV
                    </h6>
                    <p class="text-muted small mb-3">
                        Remplissez automatiquement votre profil en analysant votre CV avec l'IA.
                    </p>
                    <a href="/profile/cv-analyze" class="btn btn-primary" style="background:#6366f1;border-color:#6366f1;">
                        <i class="bi bi-brain me-1"></i>🧠 Analyser mon CV
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Experiences -->
        <div class="card border-0 shadow-sm mb-4" id="experience">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-briefcase me-2 text-primary"></i><?= lang('App.section_experience') ?></h5>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#expAddForm" aria-expanded="false">
                    <i class="bi bi-plus me-1"></i><?= lang('App.add_experience') ?>
                </button>
            </div>
            <div class="card-body">

                <!-- Existing experiences list -->
                <?php if (!empty($experiences)): ?>
                    <?php foreach ($experiences as $exp): ?>
                    <div class="border rounded p-3 mb-3 bg-light position-relative"
                         data-exp-id="<?= $exp->id ?>"
                         data-exp-title="<?= esc($exp->title ?? '') ?>"
                         data-exp-company="<?= esc($exp->company ?? '') ?>"
                         data-exp-org-id="<?= (int)($exp->org_id ?? 0) ?>"
                         data-exp-contract="<?= esc($exp->contract ?? '') ?>"
                         data-exp-level="<?= esc($exp->level ?? '') ?>"
                         data-exp-department="<?= esc($exp->department ?? '') ?>"
                         data-exp-location="<?= esc($exp->location ?? '') ?>"
                         data-exp-start="<?= esc($exp->start_date ?? '') ?>"
                         data-exp-end="<?= esc($exp->end_date ?? '') ?>"
                         data-exp-current="<?= $exp->is_current ? '1' : '0' ?>"
                         data-exp-manager-id="<?= (int)($exp->manager_user_id ?? 0) ?>"
                         data-exp-manager-name="<?= esc($exp->manager_name ?? '') ?>"
                         data-exp-skills="<?= esc($exp->skills_gained ?? '') ?>"
                         data-exp-description="<?= esc($exp->description ?? '') ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex gap-3 align-items-start flex-grow-1 me-2">
                                <?php if (!empty($exp->org_logo)): ?>
                                <img src="<?= base_url('uploads/organizations/' . esc($exp->org_logo)) ?>"
                                     style="width:40px;height:40px;object-fit:contain;border-radius:6px;background:#fff;border:1px solid #dee2e6;padding:3px;flex-shrink:0;" alt="">
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                <div class="fw-semibold fs-6">
                                    <?= esc($exp->title ?? '') ?>
                                    <?php if (!empty($exp->level)): ?>
                                        <span class="badge bg-primary ms-1 fw-normal small"><?= esc($exp->level) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($exp->contract)): ?>
                                        <span class="badge bg-secondary ms-1 fw-normal small"><?= esc($exp->contract) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-building me-1"></i><?= esc($exp->company) ?>
                                    <?php if (!empty($exp->department)): ?>
                                        &bull; <?= esc($exp->department) ?>
                                    <?php endif; ?>
                                    <?php if (!empty($exp->location)): ?>
                                        &bull; <i class="bi bi-geo-alt me-1"></i><?= esc($exp->location) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= $exp->start_date ? date('M Y', strtotime($exp->start_date)) : '' ?> –
                                    <?= $exp->is_current ? lang('App.present') : ($exp->end_date ? date('M Y', strtotime($exp->end_date)) : lang('App.present')) ?>
                                </div>
                                <?php if (!empty($exp->manager_name)): ?>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-person-badge me-1"></i><?= lang('App.exp_manager') ?> : <?= esc($exp->manager_name) ?>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($exp->skills_gained)): ?>
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    <?php foreach (array_filter(array_map('trim', explode(',', $exp->skills_gained))) as $sg): ?>
                                        <span class="badge bg-info text-dark fw-normal small"><?= esc($sg) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                </div><!-- /inner content -->
                            </div><!-- /d-flex logo+content -->
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-exp-edit"
                                        data-bs-toggle="modal" data-bs-target="#expEditModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="<?= base_url('profile/experience/delete/' . $exp->id) ?>" method="post"
                                      onsubmit="return confirm('<?= lang('App.confirm_delete') ?>')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Add experience form (collapsible) -->
                <div class="collapse" id="expAddForm">
                    <hr>
                    <form action="<?= base_url('profile/experience/add') ?>" method="post" class="row g-3 mt-1">
                        <?= csrf_field() ?>

                        <!-- Row 1: Title + Company -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small"><?= lang('App.field_job_title') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-sm" placeholder="<?= lang('App.placeholder_job_title') ?>" required>
                        </div>
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold small"><?= lang('App.field_company_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="company" id="exp_add_company"
                                   class="form-control form-control-sm org-ac"
                                   data-hidden-id="exp_add_org_id"
                                   placeholder="ex. Google, Total, SNCF…" required autocomplete="off">
                            <input type="hidden" name="org_id" id="exp_add_org_id">
                            <ul class="org-sug list-group position-absolute w-100 shadow-sm"
                                style="z-index:1050;display:none;max-height:180px;overflow-y:auto;top:100%;left:0;"></ul>
                        </div>

                        <!-- Row 2: Contract + Level -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.col_contract') ?></label>
                            <select name="contract" class="form-select form-select-sm">
                                <option value=""><?= lang('App.exp_select_contract') ?></option>
                                <option value="CDI">CDI</option>
                                <option value="CDD">CDD</option>
                                <option value="Freelance">Freelance</option>
                                <option value="Internship">Stage / Internship</option>
                                <option value="PartTime">Temps partiel</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.exp_level') ?></label>
                            <select name="level" class="form-select form-select-sm">
                                <option value=""><?= lang('App.exp_select_level') ?></option>
                                <option value="<?= lang('App.exp_level_junior') ?>"><?= lang('App.exp_level_junior') ?></option>
                                <option value="<?= lang('App.exp_level_mid') ?>"><?= lang('App.exp_level_mid') ?></option>
                                <option value="<?= lang('App.exp_level_senior') ?>"><?= lang('App.exp_level_senior') ?></option>
                                <option value="<?= lang('App.exp_level_lead') ?>"><?= lang('App.exp_level_lead') ?></option>
                                <option value="<?= lang('App.exp_level_expert') ?>"><?= lang('App.exp_level_expert') ?></option>
                                <option value="<?= lang('App.exp_level_manager') ?>"><?= lang('App.exp_level_manager') ?></option>
                                <option value="<?= lang('App.exp_level_director') ?>"><?= lang('App.exp_level_director') ?></option>
                                <option value="<?= lang('App.exp_level_executive') ?>"><?= lang('App.exp_level_executive') ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.field_department') ?></label>
                            <input type="text" name="department" class="form-control form-control-sm" placeholder="<?= lang('App.placeholder_department') ?>">
                        </div>

                        <!-- Row 3: Location -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small"><?= lang('App.field_city') ?></label>
                            <input type="text" name="location" class="form-control form-control-sm" placeholder="<?= lang('App.exp_location_ph') ?>">
                        </div>

                        <!-- Row 4: Period -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small"><?= lang('App.field_start_date') ?> <span class="text-danger">*</span></label>
                            <input type="month" name="start_date" id="exp_start_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small"><?= lang('App.field_end_date') ?></label>
                            <input type="month" name="end_date" id="exp_end_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2 d-flex align-items-end pb-1">
                            <div class="form-check">
                                <input type="checkbox" name="is_current" value="1" id="exp_is_current" class="form-check-input">
                                <label class="form-check-label small" for="exp_is_current"><?= lang('App.exp_is_current') ?></label>
                            </div>
                        </div>

                        <!-- Row 5: Manager autocomplete -->
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold small"><?= lang('App.exp_manager') ?></label>
                            <input type="text" id="exp_manager_search" class="form-control form-control-sm"
                                   placeholder="<?= lang('App.exp_manager_ph') ?>" autocomplete="off">
                            <input type="hidden" name="manager_user_id" id="exp_manager_user_id">
                            <input type="hidden" name="manager_name"    id="exp_manager_name_val">
                            <ul class="list-group position-absolute w-100 shadow-sm"
                                id="exp_manager_suggestions"
                                style="z-index:1050;display:none;max-height:180px;overflow-y:auto;top:100%;left:0;"></ul>
                        </div>

                        <!-- Row 6: Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small"><?= lang('App.field_description') ?></label>
                            <textarea name="description" class="form-control form-control-sm" rows="3"
                                      placeholder="Décrivez vos responsabilités, réalisations…"></textarea>
                        </div>

                        <!-- Row 7: Skills gained -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small"><?= lang('App.exp_skills_gained') ?></label>
                            <input type="text" name="skills_gained" class="form-control form-control-sm"
                                   placeholder="<?= lang('App.exp_skills_gained_ph') ?>">
                            <div class="form-text"><?= lang('App.skills_hint') ?></div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus me-1"></i><?= lang('App.add_experience') ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="collapse" data-bs-target="#expAddForm">
                                <?= lang('App.btn_cancel') ?>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

<!-- ── Experience Edit Modal ───────────────────────────────────────────────── -->
<div class="modal fade" id="expEditModal" tabindex="-1" aria-labelledby="expEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="expEditModalLabel"><i class="bi bi-pencil me-2 text-primary"></i><?= lang('App.btn_edit') ?> — <?= lang('App.section_experience') ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="expEditForm" method="post" action="">
        <?= csrf_field() ?>
        <div class="modal-body row g-3">

          <!-- Title + Company -->
          <div class="col-md-6">
            <label class="form-label fw-semibold small"><?= lang('App.field_job_title') ?> <span class="text-danger">*</span></label>
            <input type="text" name="title" id="eem_title" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-6 position-relative">
            <label class="form-label fw-semibold small"><?= lang('App.field_company_name') ?> <span class="text-danger">*</span></label>
            <input type="text" name="company" id="eem_company"
                   class="form-control form-control-sm org-ac"
                   data-hidden-id="eem_org_id"
                   required autocomplete="off">
            <input type="hidden" name="org_id" id="eem_org_id">
            <ul class="org-sug list-group position-absolute w-100 shadow-sm"
                style="z-index:1060;display:none;max-height:180px;overflow-y:auto;top:100%;left:0;"></ul>
          </div>

          <!-- Contract + Level + Department -->
          <div class="col-md-4">
            <label class="form-label fw-semibold small"><?= lang('App.col_contract') ?></label>
            <select name="contract" id="eem_contract" class="form-select form-select-sm">
              <option value=""><?= lang('App.exp_select_contract') ?></option>
              <option value="CDI">CDI</option>
              <option value="CDD">CDD</option>
              <option value="Freelance">Freelance</option>
              <option value="Internship">Stage / Internship</option>
              <option value="PartTime">Temps partiel</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small"><?= lang('App.exp_level') ?></label>
            <select name="level" id="eem_level" class="form-select form-select-sm">
              <option value=""><?= lang('App.exp_select_level') ?></option>
              <option value="<?= lang('App.exp_level_junior') ?>"><?= lang('App.exp_level_junior') ?></option>
              <option value="<?= lang('App.exp_level_mid') ?>"><?= lang('App.exp_level_mid') ?></option>
              <option value="<?= lang('App.exp_level_senior') ?>"><?= lang('App.exp_level_senior') ?></option>
              <option value="<?= lang('App.exp_level_lead') ?>"><?= lang('App.exp_level_lead') ?></option>
              <option value="<?= lang('App.exp_level_expert') ?>"><?= lang('App.exp_level_expert') ?></option>
              <option value="<?= lang('App.exp_level_manager') ?>"><?= lang('App.exp_level_manager') ?></option>
              <option value="<?= lang('App.exp_level_director') ?>"><?= lang('App.exp_level_director') ?></option>
              <option value="<?= lang('App.exp_level_executive') ?>"><?= lang('App.exp_level_executive') ?></option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small"><?= lang('App.field_department') ?></label>
            <input type="text" name="department" id="eem_department" class="form-control form-control-sm" placeholder="<?= lang('App.placeholder_department') ?>">
          </div>

          <!-- Location -->
          <div class="col-md-6">
            <label class="form-label fw-semibold small"><?= lang('App.field_city') ?></label>
            <input type="text" name="location" id="eem_location" class="form-control form-control-sm" placeholder="<?= lang('App.exp_location_ph') ?>">
          </div>

          <!-- Period -->
          <div class="col-md-3">
            <label class="form-label fw-semibold small"><?= lang('App.field_start_date') ?> <span class="text-danger">*</span></label>
            <input type="month" name="start_date" id="eem_start_date" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold small"><?= lang('App.field_end_date') ?></label>
            <input type="month" name="end_date" id="eem_end_date" class="form-control form-control-sm">
          </div>
          <div class="col-12">
            <div class="form-check">
              <input type="checkbox" name="is_current" value="1" id="eem_is_current" class="form-check-input">
              <label class="form-check-label small" for="eem_is_current"><?= lang('App.exp_is_current') ?></label>
            </div>
          </div>

          <!-- Manager autocomplete -->
          <div class="col-md-6 position-relative">
            <label class="form-label fw-semibold small"><?= lang('App.exp_manager') ?></label>
            <input type="text" id="eem_manager_search" class="form-control form-control-sm"
                   placeholder="<?= lang('App.exp_manager_ph') ?>" autocomplete="off">
            <input type="hidden" name="manager_user_id" id="eem_manager_user_id">
            <input type="hidden" name="manager_name"    id="eem_manager_name_val">
            <ul class="list-group position-absolute w-100 shadow-sm" id="eem_manager_suggestions"
                style="z-index:1060;display:none;max-height:180px;overflow-y:auto;top:100%;left:0;"></ul>
          </div>

          <!-- Description -->
          <div class="col-12">
            <label class="form-label fw-semibold small"><?= lang('App.field_description') ?></label>
            <textarea name="description" id="eem_description" class="form-control form-control-sm" rows="4"
                      placeholder="Décrivez vos responsabilités, réalisations…"></textarea>
          </div>

          <!-- Skills gained -->
          <div class="col-12">
            <label class="form-label fw-semibold small"><?= lang('App.exp_skills_gained') ?></label>
            <input type="text" name="skills_gained" id="eem_skills_gained" class="form-control form-control-sm"
                   placeholder="<?= lang('App.exp_skills_gained_ph') ?>">
            <div class="form-text"><?= lang('App.skills_hint') ?></div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"><?= lang('App.btn_cancel') ?></button>
          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i><?= lang('App.btn_save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

        <!-- Formation / Education -->
        <div class="card border-0 shadow-sm mb-4" id="education">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-mortarboard me-2 text-primary"></i><?= lang('App.section_education') ?></h5>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#eduAddForm" aria-expanded="false">
                    <i class="bi bi-plus me-1"></i><?= lang('App.add_education') ?>
                </button>
            </div>
            <div class="card-body">

                <!-- Existing entries -->
                <?php if (!empty($education)): ?>
                    <?php foreach ($education as $edu): ?>
                    <div class="border rounded p-3 mb-2 bg-light d-flex justify-content-between align-items-start"
                         data-edu-id="<?= $edu->id ?>"
                         data-edu-institution="<?= esc($edu->institution ?? '') ?>"
                         data-edu-org-id="<?= (int)($edu->org_id ?? 0) ?>"
                         data-edu-degree="<?= esc($edu->degree ?? '') ?>"
                         data-edu-niveau="<?= esc($edu->niveau ?? '') ?>"
                         data-edu-field="<?= esc($edu->field ?? '') ?>"
                         data-edu-start="<?= esc($edu->start_year ?? '') ?>"
                         data-edu-end="<?= esc($edu->end_year ?? '') ?>"
                         data-edu-description="<?= esc($edu->description ?? '') ?>">
                        <div class="d-flex gap-3 align-items-start flex-grow-1 me-2">
                            <?php if (!empty($edu->org_logo)): ?>
                            <img src="<?= base_url('uploads/organizations/' . esc($edu->org_logo)) ?>"
                                 style="width:40px;height:40px;object-fit:contain;border-radius:6px;background:#fff;border:1px solid #dee2e6;padding:3px;flex-shrink:0;" alt="">
                            <?php endif; ?>
                            <div>
                            <div class="fw-semibold">
                                <?= esc($edu->degree) ?>
                                <?php if (isset($edu->niveau) && !empty($edu->niveau)): ?>
                                    <span class="badge bg-primary ms-1 fw-normal small"><?= esc($edu->niveau) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="bi bi-building me-1"></i><?= esc($edu->institution) ?>
                                <?php if (!empty($edu->field)): ?> &bull; <?= esc($edu->field) ?><?php endif; ?>
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?= !empty($edu->start_year) ? esc($edu->start_year) : '' ?>
                                <?= !empty($edu->end_year) ? ' – ' . esc($edu->end_year) : '' ?>
                            </div>
                            </div><!-- /inner -->
                        </div><!-- /d-flex -->
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-outline-primary btn-sm btn-edu-edit"
                                    data-bs-toggle="modal" data-bs-target="#eduEditModal">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="<?= base_url('profile/education/delete/' . $edu->id) ?>" method="post"
                                  onsubmit="return confirm('<?= lang('App.confirm_delete') ?>')">
                                <?= csrf_field() ?>
                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Add form (collapsible) -->
                <div class="collapse" id="eduAddForm">
                    <hr>
                    <form action="<?= base_url('profile/education/add') ?>" method="post" class="row g-3 mt-1">
                        <?= csrf_field() ?>

                        <!-- Institution + Titre -->
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold small"><?= lang('App.field_school') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="institution" id="edu_add_institution"
                                   class="form-control form-control-sm org-ac"
                                   data-hidden-id="edu_add_org_id"
                                   placeholder="Université, École, Institut…" required autocomplete="off">
                            <input type="hidden" name="org_id" id="edu_add_org_id">
                            <ul class="org-sug list-group position-absolute w-100 shadow-sm"
                                style="z-index:1050;display:none;max-height:180px;overflow-y:auto;top:100%;left:0;"></ul>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small"><?= lang('App.edu_titre') ?> <span class="text-danger">*</span></label>
                            <select name="degree" class="form-select form-select-sm" required>
                                <option value=""><?= lang('App.edu_select_titre') ?></option>
                                <option value="Doctorat">Doctorat</option>
                                <option value="Ingénieur">Ingénieur</option>
                                <option value="Master 2">Master 2</option>
                                <option value="Master 1">Master 1</option>
                                <option value="Licence Pro">Licence Pro</option>
                                <option value="Licence">Licence</option>
                                <option value="Bachelor">Bachelor</option>
                                <option value="BTS / DUT">BTS / DUT</option>
                                <option value="Technicien Supérieur">Technicien Supérieur</option>
                                <option value="Baccalauréat">Baccalauréat</option>
                                <option value="Certificat">Certificat / Diplôme professionnel</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>

                        <!-- Niveau + Spécialité -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.edu_niveau') ?></label>
                            <select name="niveau" class="form-select form-select-sm">
                                <option value=""><?= lang('App.edu_select_niveau') ?></option>
                                <option value="BAC">BAC</option>
                                <option value="BAC+1">BAC+1</option>
                                <option value="BAC+2">BAC+2</option>
                                <option value="BAC+3">BAC+3</option>
                                <option value="BAC+4">BAC+4</option>
                                <option value="BAC+5">BAC+5</option>
                                <option value="BAC+6">BAC+6</option>
                                <option value="BAC+7">BAC+7</option>
                                <option value="BAC+8">BAC+8</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small"><?= lang('App.field_field_of_study') ?></label>
                            <input type="text" name="field" class="form-control form-control-sm" placeholder="Ex. Informatique, Finance, Génie civil…">
                        </div>

                        <!-- Années -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small"><?= lang('App.field_start_year') ?> <span class="text-danger">*</span></label>
                            <input type="number" name="start_year" class="form-control form-control-sm" placeholder="2020" min="1950" max="2030" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small"><?= lang('App.field_end_year') ?></label>
                            <input type="number" name="end_year" class="form-control form-control-sm" placeholder="2023" min="1950" max="2030">
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus me-1"></i><?= lang('App.add_education') ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="collapse" data-bs-target="#eduAddForm">
                                <?= lang('App.btn_cancel') ?>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- ══ Certifications ═══════════════════════════════════════════════ -->
        <div class="card border-0 shadow-sm mb-4" id="certifications">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-patch-check me-2 text-primary"></i><?= lang('App.section_certifications') ?></h5>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#certAddForm">
                    <i class="bi bi-plus me-1"></i><?= lang('App.add_certification') ?>
                </button>
            </div>
            <div class="card-body">
                <?php if (!empty($certifications)): ?>
                <?php foreach ($certifications as $cert): ?>
                <div class="border rounded p-3 mb-2 bg-light d-flex justify-content-between align-items-start gap-2"
                     data-cert-id="<?= $cert->id ?>"
                     data-cert-name="<?= esc($cert->name ?? '') ?>"
                     data-cert-organization="<?= esc($cert->organization ?? '') ?>"
                     data-cert-issue="<?= esc(substr($cert->issue_date ?? '', 0, 7)) ?>"
                     data-cert-expiry="<?= esc(substr($cert->expiry_date ?? '', 0, 7)) ?>"
                     data-cert-url="<?= esc($cert->credential_url ?? '') ?>">
                    <div class="d-flex gap-3 align-items-start">
                        <?php if (!empty($cert->logo_file)): ?>
                        <img src="<?= base_url('uploads/cert_logos/' . esc($cert->logo_file)) ?>"
                             style="width:40px;height:40px;object-fit:contain;border-radius:6px;background:#fff;border:1px solid #dee2e6;padding:3px;" alt="">
                        <?php endif; ?>
                        <div>
                            <div class="fw-semibold"><?= esc($cert->name) ?></div>
                            <?php if (!empty($cert->organization)): ?>
                            <div class="text-muted small"><i class="bi bi-building me-1"></i><?= esc($cert->organization) ?></div>
                            <?php endif; ?>
                            <div class="text-muted small">
                                <?php if (!empty($cert->issue_date)): ?>
                                <i class="bi bi-calendar3 me-1"></i><?= date('M Y', strtotime($cert->issue_date)) ?>
                                <?php endif; ?>
                                <?php if (!empty($cert->expiry_date)): ?>
                                &rarr; <?= date('M Y', strtotime($cert->expiry_date)) ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($cert->credential_url)): ?>
                            <a href="<?= esc($cert->credential_url) ?>" target="_blank" rel="noopener" class="small text-primary">
                                <i class="bi bi-link-45deg me-1"></i>Voir la certification
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-cert-edit"
                                data-bs-toggle="modal" data-bs-target="#certEditModal">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="<?= base_url('profile/certification/delete/' . $cert->id) ?>" method="post"
                              onsubmit="return confirm('<?= lang('App.confirm_delete') ?>')">
                            <?= csrf_field() ?>
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
                <div class="collapse" id="certAddForm">
                    <hr>
                    <form action="<?= base_url('profile/certification/add') ?>" method="post" enctype="multipart/form-data" class="row g-3 mt-1">
                        <?= csrf_field() ?>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small"><?= lang('App.cert_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="<?= lang('App.cert_name_ph') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.cert_issue_date') ?></label>
                            <input type="month" name="issue_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small"><?= lang('App.cert_organization') ?></label>
                            <input type="text" name="organization" class="form-control form-control-sm" placeholder="<?= lang('App.cert_organization_ph') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.cert_expiry_date') ?></label>
                            <input type="month" name="expiry_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small"><?= lang('App.cert_credential_url') ?></label>
                            <input type="url" name="credential_url" class="form-control form-control-sm" placeholder="<?= lang('App.cert_credential_url_ph') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.cert_logo') ?></label>
                            <input type="file" name="logo_file" class="form-control form-control-sm" accept="image/*">
                            <div class="form-text"><?= lang('App.cert_logo_hint') ?></div>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i><?= lang('App.add_certification') ?></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#certAddForm"><?= lang('App.btn_cancel') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ══ Langues ══════════════════════════════════════════════════════ -->
        <div class="card border-0 shadow-sm mb-4" id="languages">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-translate me-2 text-primary"></i><?= lang('App.section_languages') ?></h5>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#langAddForm">
                    <i class="bi bi-plus me-1"></i><?= lang('App.add_language') ?>
                </button>
            </div>
            <div class="card-body">
                <?php if (!empty($languages)): ?>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php foreach ($languages as $lang): ?>
                    <div class="d-flex align-items-center gap-1 border rounded px-3 py-1 bg-light"
                         data-lang-id="<?= $lang->id ?>"
                         data-lang-name="<?= esc($lang->name ?? '') ?>"
                         data-lang-level="<?= esc($lang->level ?? '') ?>">
                        <span class="fw-semibold small"><?= esc($lang->name) ?></span>
                        <span class="badge bg-primary fw-normal ms-1"><?= esc($lang->level) ?></span>
                        <button type="button" class="btn btn-link btn-sm p-0 text-primary ms-1 btn-lang-edit"
                                data-bs-toggle="modal" data-bs-target="#langEditModal">
                            <i class="bi bi-pencil" style="font-size:.75rem;"></i>
                        </button>
                        <form action="<?= base_url('profile/language/delete/' . $lang->id) ?>" method="post"
                              onsubmit="return confirm('<?= lang('App.confirm_delete') ?>')" class="ms-1">
                            <?= csrf_field() ?>
                            <button class="btn btn-link btn-sm p-0 text-danger"><i class="bi bi-x"></i></button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="collapse" id="langAddForm">
                    <hr>
                    <form action="<?= base_url('profile/language/add') ?>" method="post" class="row g-3 mt-1">
                        <?= csrf_field() ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small"><?= lang('App.lang_name') ?> <span class="text-danger">*</span></label>
                            <select name="name" class="form-select form-select-sm" required>
                                <option value=""><?= lang('App.lang_select_level') ?></option>
                                <optgroup label="Maghreb / Moyen-Orient">
                                    <option value="Arabe">🌍 Arabe</option>
                                    <option value="Arabe (Algérien)">🇩🇿 Arabe (Algérien)</option>
                                    <option value="Arabe (Marocain)">🇲🇦 Arabe (Marocain)</option>
                                    <option value="Arabe (Tunisien)">🇹🇳 Arabe (Tunisien)</option>
                                    <option value="Tamazight / Berbère">🏔 Tamazight / Berbère</option>
                                    <option value="Hébreu">🇮🇱 Hébreu</option>
                                    <option value="Persan / Farsi">🇮🇷 Persan / Farsi</option>
                                    <option value="Turc">🇹🇷 Turc</option>
                                </optgroup>
                                <optgroup label="Europe">
                                    <option value="Français">🇫🇷 Français</option>
                                    <option value="Anglais">🇬🇧 Anglais</option>
                                    <option value="Espagnol">🇪🇸 Espagnol</option>
                                    <option value="Allemand">🇩🇪 Allemand</option>
                                    <option value="Italien">🇮🇹 Italien</option>
                                    <option value="Portugais">🇵🇹 Portugais</option>
                                    <option value="Néerlandais">🇳🇱 Néerlandais</option>
                                    <option value="Russe">🇷🇺 Russe</option>
                                    <option value="Polonais">🇵🇱 Polonais</option>
                                    <option value="Suédois">🇸🇪 Suédois</option>
                                    <option value="Danois">🇩🇰 Danois</option>
                                    <option value="Norvégien">🇳🇴 Norvégien</option>
                                    <option value="Finnois">🇫🇮 Finnois</option>
                                    <option value="Grec">🇬🇷 Grec</option>
                                    <option value="Roumain">🇷🇴 Roumain</option>
                                    <option value="Hongrois">🇭🇺 Hongrois</option>
                                    <option value="Tchèque">🇨🇿 Tchèque</option>
                                    <option value="Ukrainien">🇺🇦 Ukrainien</option>
                                </optgroup>
                                <optgroup label="Asie / Afrique">
                                    <option value="Chinois (Mandarin)">🇨🇳 Chinois (Mandarin)</option>
                                    <option value="Japonais">🇯🇵 Japonais</option>
                                    <option value="Coréen">🇰🇷 Coréen</option>
                                    <option value="Hindi">🇮🇳 Hindi</option>
                                    <option value="Bengali">🇧🇩 Bengali</option>
                                    <option value="Indonésien">🇮🇩 Indonésien</option>
                                    <option value="Swahili">🌍 Swahili</option>
                                    <option value="Hausa">🌍 Hausa</option>
                                </optgroup>
                                <optgroup label="Amérique">
                                    <option value="Anglais (américain)">🇺🇸 Anglais (américain)</option>
                                    <option value="Espagnol (latino)">🌎 Espagnol (latino)</option>
                                    <option value="Portugais (brésilien)">🇧🇷 Portugais (brésilien)</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small"><?= lang('App.lang_level') ?> <span class="text-danger">*</span></label>
                            <select name="level" class="form-select form-select-sm" required>
                                <option value=""><?= lang('App.lang_select_level') ?></option>
                                <option value="Natif"><?= lang('App.lang_native') ?></option>
                                <option value="C2"><?= lang('App.lang_c2') ?></option>
                                <option value="C1"><?= lang('App.lang_c1') ?></option>
                                <option value="B2"><?= lang('App.lang_b2') ?></option>
                                <option value="B1"><?= lang('App.lang_b1') ?></option>
                                <option value="A2"><?= lang('App.lang_a2') ?></option>
                                <option value="A1"><?= lang('App.lang_a1') ?></option>
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i><?= lang('App.add_language') ?></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#langAddForm"><?= lang('App.btn_cancel') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ══ Projets ══════════════════════════════════════════════════════ -->
        <div class="card border-0 shadow-sm mb-4" id="projects">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-kanban me-2 text-primary"></i><?= lang('App.section_projects') ?></h5>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#projAddForm">
                    <i class="bi bi-plus me-1"></i><?= lang('App.add_project') ?>
                </button>
            </div>
            <div class="card-body">
                <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $proj): ?>
                <?php
                    $memberObjs  = model(\App\Models\ProjectMemberModel::class)->getMembersByProject($proj->id);
                    $memberNames = [];
                    foreach ($memberObjs as $m) {
                        $mu = model(\App\Models\UserModel::class)->find($m->user_id);
                        if ($mu) { $memberNames[] = esc(trim($mu->first_name . ' ' . $mu->last_name)); }
                    }
                ?>
                <div class="border rounded p-3 mb-2 bg-light d-flex justify-content-between align-items-start"
                     data-proj-id="<?= $proj->id ?>"
                     data-proj-name="<?= esc($proj->name ?? '') ?>"
                     data-proj-start="<?= esc(substr($proj->start_date ?? '', 0, 7)) ?>"
                     data-proj-end="<?= esc(substr($proj->end_date ?? '', 0, 7)) ?>"
                     data-proj-current="<?= $proj->is_current ? '1' : '0' ?>"
                     data-proj-description="<?= esc($proj->description ?? '') ?>">
                    <div>
                        <div class="fw-semibold"><?= esc($proj->name) ?></div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?= !empty($proj->start_date) ? date('M Y', strtotime($proj->start_date)) : '' ?>
                            <?php if ($proj->is_current): ?>
                                — <?= lang('App.present') ?>
                            <?php elseif (!empty($proj->end_date)): ?>
                                — <?= date('M Y', strtotime($proj->end_date)) ?>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($memberNames)): ?>
                        <div class="text-muted small"><i class="bi bi-people me-1"></i><?= implode(', ', $memberNames) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($proj->description)): ?>
                        <p class="small mb-0 mt-1"><?= esc($proj->description) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-proj-edit"
                                data-bs-toggle="modal" data-bs-target="#projEditModal">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="<?= base_url('profile/project/delete/' . $proj->id) ?>" method="post"
                              onsubmit="return confirm('<?= lang('App.confirm_delete') ?>')">
                            <?= csrf_field() ?>
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
                <div class="collapse" id="projAddForm">
                    <hr>
                    <form action="<?= base_url('profile/project/add') ?>" method="post" class="row g-3 mt-1">
                        <?= csrf_field() ?>
                        <div class="col-12">
                            <label class="form-label fw-semibold small"><?= lang('App.project_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="<?= lang('App.project_name_ph') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.field_start_date') ?></label>
                            <input type="month" name="start_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.field_end_date') ?></label>
                            <input type="month" name="end_date" class="form-control form-control-sm" id="proj_end_date">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="is_current" value="1" id="proj_is_current">
                                <label class="form-check-label small" for="proj_is_current"><?= lang('App.project_is_current') ?></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small"><?= lang('App.project_team') ?></label>
                            <div class="position-relative">
                                <input type="text" id="proj_member_search" class="form-control form-control-sm"
                                       placeholder="<?= lang('App.project_team_ph') ?>" autocomplete="off">
                                <div id="proj_member_suggestions" class="list-group position-absolute w-100 shadow-sm"
                                     style="z-index:500;display:none;max-height:160px;overflow-y:auto;"></div>
                            </div>
                            <div id="proj_members_selected" class="d-flex flex-wrap gap-1 mt-2"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small"><?= lang('App.field_description') ?></label>
                            <textarea name="description" class="form-control form-control-sm" rows="3"></textarea>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i><?= lang('App.add_project') ?></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#projAddForm"><?= lang('App.btn_cancel') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ══ Bénévolat ════════════════════════════════════════════════════ -->
        <div class="card border-0 shadow-sm mb-4" id="volunteering">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-heart me-2 text-primary"></i><?= lang('App.section_volunteering') ?></h5>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#volAddForm">
                    <i class="bi bi-plus me-1"></i><?= lang('App.add_volunteering') ?>
                </button>
            </div>
            <div class="card-body">
                <?php if (!empty($volunteering)): ?>
                <?php foreach ($volunteering as $vol): ?>
                <div class="border rounded p-3 mb-2 bg-light d-flex justify-content-between align-items-start"
                     data-vol-id="<?= $vol->id ?>"
                     data-vol-organization="<?= esc($vol->organization ?? '') ?>"
                     data-vol-position="<?= esc($vol->position ?? '') ?>"
                     data-vol-start="<?= esc(substr($vol->start_date ?? '', 0, 7)) ?>"
                     data-vol-end="<?= esc(substr($vol->end_date ?? '', 0, 7)) ?>"
                     data-vol-current="<?= $vol->is_current ? '1' : '0' ?>"
                     data-vol-description="<?= esc($vol->description ?? '') ?>">
                    <div>
                        <div class="fw-semibold"><?= esc($vol->organization) ?></div>
                        <?php if (!empty($vol->position)): ?>
                        <div class="text-muted small fw-medium"><?= esc($vol->position) ?></div>
                        <?php endif; ?>
                        <div class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?= !empty($vol->start_date) ? date('M Y', strtotime($vol->start_date)) : '' ?>
                            <?php if ($vol->is_current): ?>
                                — <?= lang('App.present') ?>
                            <?php elseif (!empty($vol->end_date)): ?>
                                — <?= date('M Y', strtotime($vol->end_date)) ?>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($vol->description)): ?>
                        <p class="small mb-0 mt-1"><?= esc($vol->description) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-vol-edit"
                                data-bs-toggle="modal" data-bs-target="#volEditModal">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="<?= base_url('profile/volunteering/delete/' . $vol->id) ?>" method="post"
                              onsubmit="return confirm('<?= lang('App.confirm_delete') ?>')">
                            <?= csrf_field() ?>
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
                <div class="collapse" id="volAddForm">
                    <hr>
                    <form action="<?= base_url('profile/volunteering/add') ?>" method="post" class="row g-3 mt-1">
                        <?= csrf_field() ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small"><?= lang('App.vol_organization') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="organization" class="form-control form-control-sm" placeholder="<?= lang('App.vol_organization_ph') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small"><?= lang('App.vol_position') ?></label>
                            <input type="text" name="position" class="form-control form-control-sm" placeholder="<?= lang('App.vol_position_ph') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.field_start_date') ?></label>
                            <input type="month" name="start_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><?= lang('App.field_end_date') ?></label>
                            <input type="month" name="end_date" class="form-control form-control-sm" id="vol_end_date">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="is_current" value="1" id="vol_is_current">
                                <label class="form-check-label small" for="vol_is_current"><?= lang('App.vol_is_current') ?></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small"><?= lang('App.field_description') ?></label>
                            <textarea name="description" class="form-control form-control-sm" rows="3"></textarea>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i><?= lang('App.add_volunteering') ?></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#volAddForm"><?= lang('App.btn_cancel') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

</div><!-- end #forms-col -->

<!-- ══ Education Edit Modal ════════════════════════════════════════════════ -->
<div class="modal fade" id="eduEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil me-2 text-primary"></i>Modifier la formation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="eduEditForm" method="post" action="">
        <?= csrf_field() ?>
        <div class="modal-body row g-3">
          <div class="col-md-6 position-relative">
            <label class="form-label fw-semibold small"><?= lang('App.field_school') ?> <span class="text-danger">*</span></label>
            <input type="text" name="institution" id="eem_institution"
                   class="form-control form-control-sm org-ac"
                   data-hidden-id="eem_edu_org_id"
                   required autocomplete="off">
            <input type="hidden" name="org_id" id="eem_edu_org_id">
            <ul class="org-sug list-group position-absolute w-100 shadow-sm"
                style="z-index:1060;display:none;max-height:180px;overflow-y:auto;top:100%;left:0;"></ul>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small"><?= lang('App.edu_titre') ?> <span class="text-danger">*</span></label>
            <select name="degree" id="eem_degree" class="form-select form-select-sm" required>
              <option value="">-- Sélectionner --</option>
              <option value="Doctorat">Doctorat</option>
              <option value="Ingénieur">Ingénieur</option>
              <option value="Master 2">Master 2</option>
              <option value="Master 1">Master 1</option>
              <option value="Licence Pro">Licence Pro</option>
              <option value="Licence">Licence</option>
              <option value="Bachelor">Bachelor</option>
              <option value="BTS / DUT">BTS / DUT</option>
              <option value="Technicien Supérieur">Technicien Supérieur</option>
              <option value="Baccalauréat">Baccalauréat</option>
              <option value="Certificat">Certificat / Diplôme professionnel</option>
              <option value="Autre">Autre</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small"><?= lang('App.edu_niveau') ?></label>
            <select name="niveau" id="eem_niveau" class="form-select form-select-sm">
              <option value="">--</option>
              <option value="BAC">BAC</option>
              <option value="BAC+1">BAC+1</option>
              <option value="BAC+2">BAC+2</option>
              <option value="BAC+3">BAC+3</option>
              <option value="BAC+4">BAC+4</option>
              <option value="BAC+5">BAC+5</option>
              <option value="BAC+6">BAC+6</option>
              <option value="BAC+7">BAC+7</option>
              <option value="BAC+8">BAC+8</option>
            </select>
          </div>
          <div class="col-md-8">
            <label class="form-label fw-semibold small"><?= lang('App.field_field_of_study') ?></label>
            <input type="text" name="field" id="eem_field" class="form-control form-control-sm" placeholder="ex. Informatique, Finance…">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold small"><?= lang('App.field_start_year') ?> <span class="text-danger">*</span></label>
            <input type="number" name="start_year" id="eem_start_year" class="form-control form-control-sm" min="1950" max="2030" required>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold small"><?= lang('App.field_end_year') ?></label>
            <input type="number" name="end_year" id="eem_end_year" class="form-control form-control-sm" min="1950" max="2030">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold small"><?= lang('App.field_description') ?></label>
            <textarea name="description" id="eem_edu_description" class="form-control form-control-sm" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"><?= lang('App.btn_cancel') ?></button>
          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i><?= lang('App.btn_save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ Certification Edit Modal ════════════════════════════════════════════ -->
<div class="modal fade" id="certEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil me-2 text-primary"></i>Modifier la certification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="certEditForm" method="post" action="">
        <?= csrf_field() ?>
        <div class="modal-body row g-3">
          <div class="col-md-8">
            <label class="form-label fw-semibold small"><?= lang('App.cert_name') ?> <span class="text-danger">*</span></label>
            <input type="text" name="name" id="cem_name" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small"><?= lang('App.cert_issue_date') ?></label>
            <input type="month" name="issue_date" id="cem_issue" class="form-control form-control-sm">
          </div>
          <div class="col-md-8">
            <label class="form-label fw-semibold small"><?= lang('App.cert_organization') ?></label>
            <input type="text" name="organization" id="cem_organization" class="form-control form-control-sm" placeholder="Organisme émetteur">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small"><?= lang('App.cert_expiry_date') ?></label>
            <input type="month" name="expiry_date" id="cem_expiry" class="form-control form-control-sm">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold small"><?= lang('App.cert_credential_url') ?></label>
            <input type="url" name="credential_url" id="cem_url" class="form-control form-control-sm" placeholder="https://…">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"><?= lang('App.btn_cancel') ?></button>
          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i><?= lang('App.btn_save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ Language Edit Modal ══════════════════════════════════════════════════ -->
<div class="modal fade" id="langEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil me-2 text-primary"></i>Modifier la langue</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="langEditForm" method="post" action="">
        <?= csrf_field() ?>
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold small"><?= lang('App.lang_name') ?> <span class="text-danger">*</span></label>
            <select name="name" id="lem_name" class="form-select form-select-sm" required>
              <option value="">--</option>
              <optgroup label="Maghreb / Moyen-Orient">
                <option value="Arabe">🌍 Arabe</option>
                <option value="Arabe (Algérien)">🇩🇿 Arabe (Algérien)</option>
                <option value="Arabe (Marocain)">🇲🇦 Arabe (Marocain)</option>
                <option value="Arabe (Tunisien)">🇹🇳 Arabe (Tunisien)</option>
                <option value="Tamazight / Berbère">🏔 Tamazight / Berbère</option>
                <option value="Hébreu">🇮🇱 Hébreu</option>
                <option value="Persan / Farsi">🇮🇷 Persan / Farsi</option>
                <option value="Turc">🇹🇷 Turc</option>
              </optgroup>
              <optgroup label="Europe">
                <option value="Français">🇫🇷 Français</option>
                <option value="Anglais">🇬🇧 Anglais</option>
                <option value="Espagnol">🇪🇸 Espagnol</option>
                <option value="Allemand">🇩🇪 Allemand</option>
                <option value="Italien">🇮🇹 Italien</option>
                <option value="Portugais">🇵🇹 Portugais</option>
                <option value="Néerlandais">🇳🇱 Néerlandais</option>
                <option value="Russe">🇷🇺 Russe</option>
                <option value="Polonais">🇵🇱 Polonais</option>
                <option value="Suédois">🇸🇪 Suédois</option>
                <option value="Danois">🇩🇰 Danois</option>
                <option value="Norvégien">🇳🇴 Norvégien</option>
                <option value="Finnois">🇫🇮 Finnois</option>
                <option value="Grec">🇬🇷 Grec</option>
                <option value="Roumain">🇷🇴 Roumain</option>
                <option value="Hongrois">🇭🇺 Hongrois</option>
                <option value="Tchèque">🇨🇿 Tchèque</option>
                <option value="Ukrainien">🇺🇦 Ukrainien</option>
              </optgroup>
              <optgroup label="Asie / Afrique">
                <option value="Chinois (Mandarin)">🇨🇳 Chinois (Mandarin)</option>
                <option value="Japonais">🇯🇵 Japonais</option>
                <option value="Coréen">🇰🇷 Coréen</option>
                <option value="Hindi">🇮🇳 Hindi</option>
                <option value="Bengali">🇧🇩 Bengali</option>
                <option value="Indonésien">🇮🇩 Indonésien</option>
                <option value="Swahili">🌍 Swahili</option>
                <option value="Hausa">🌍 Hausa</option>
              </optgroup>
              <optgroup label="Amérique">
                <option value="Anglais (américain)">🇺🇸 Anglais (américain)</option>
                <option value="Espagnol (latino)">🌎 Espagnol (latino)</option>
                <option value="Portugais (brésilien)">🇧🇷 Portugais (brésilien)</option>
              </optgroup>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small"><?= lang('App.lang_level') ?> <span class="text-danger">*</span></label>
            <select name="level" id="lem_level" class="form-select form-select-sm" required>
              <option value="">--</option>
              <option value="Natif"><?= lang('App.lang_native') ?></option>
              <option value="C2"><?= lang('App.lang_c2') ?></option>
              <option value="C1"><?= lang('App.lang_c1') ?></option>
              <option value="B2"><?= lang('App.lang_b2') ?></option>
              <option value="B1"><?= lang('App.lang_b1') ?></option>
              <option value="A2"><?= lang('App.lang_a2') ?></option>
              <option value="A1"><?= lang('App.lang_a1') ?></option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"><?= lang('App.btn_cancel') ?></button>
          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i><?= lang('App.btn_save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ Project Edit Modal ═══════════════════════════════════════════════════ -->
<div class="modal fade" id="projEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil me-2 text-primary"></i>Modifier le projet</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="projEditForm" method="post" action="">
        <?= csrf_field() ?>
        <div class="modal-body row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold small"><?= lang('App.project_name') ?> <span class="text-danger">*</span></label>
            <input type="text" name="name" id="pem_name" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small"><?= lang('App.field_start_date') ?></label>
            <input type="month" name="start_date" id="pem_start" class="form-control form-control-sm">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small"><?= lang('App.field_end_date') ?></label>
            <input type="month" name="end_date" id="pem_end" class="form-control form-control-sm">
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check mb-1">
              <input class="form-check-input" type="checkbox" name="is_current" value="1" id="pem_current">
              <label class="form-check-label small" for="pem_current"><?= lang('App.project_is_current') ?></label>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold small"><?= lang('App.field_description') ?></label>
            <textarea name="description" id="pem_description" class="form-control form-control-sm" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"><?= lang('App.btn_cancel') ?></button>
          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i><?= lang('App.btn_save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ Volunteering Edit Modal ══════════════════════════════════════════════ -->
<div class="modal fade" id="volEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil me-2 text-primary"></i>Modifier le bénévolat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="volEditForm" method="post" action="">
        <?= csrf_field() ?>
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold small"><?= lang('App.vol_organization') ?> <span class="text-danger">*</span></label>
            <input type="text" name="organization" id="vem_organization" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold small"><?= lang('App.vol_position') ?></label>
            <input type="text" name="position" id="vem_position" class="form-control form-control-sm">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small"><?= lang('App.field_start_date') ?></label>
            <input type="month" name="start_date" id="vem_start" class="form-control form-control-sm">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold small"><?= lang('App.field_end_date') ?></label>
            <input type="month" name="end_date" id="vem_end" class="form-control form-control-sm">
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check mb-1">
              <input class="form-check-input" type="checkbox" name="is_current" value="1" id="vem_current">
              <label class="form-check-label small" for="vem_current"><?= lang('App.vol_is_current') ?></label>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold small"><?= lang('App.field_description') ?></label>
            <textarea name="description" id="vem_description" class="form-control form-control-sm" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"><?= lang('App.btn_cancel') ?></button>
          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i><?= lang('App.btn_save') ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ═════════════════ RIGHT : Aperçu CV ATS ═════════════════ -->
<div class="col-lg-5 d-none d-lg-block" id="cv-col" style="background:#e9ecef;">
  <div style="position:sticky;top:68px;max-height:calc(100vh - 68px);overflow-y:auto;padding:1.25rem;">

    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="fw-bold small text-secondary"><i class="bi bi-eye me-1"></i>Aperçu CV &mdash; ATS</span>
      <button onclick="window.print()" class="btn btn-sm btn-outline-secondary py-0 px-2">
        <i class="bi bi-printer me-1"></i>PDF
      </button>
    </div>

    <!-- A4 simulation -->
    <div id="cv-preview" class="bg-white shadow-sm mx-auto" style="padding:32px 40px;max-width:210mm;min-height:200px;">

      <!-- ① En-tête -->
      <div style="border-bottom:3px solid #1a1a2e;padding-bottom:10px;margin-bottom:12px;">
        <div id="cv-name" style="font-size:20px;font-weight:900;text-transform:uppercase;color:#1a1a2e;letter-spacing:1px;line-height:1.2;">
          <?= esc(strtoupper(trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? '')))) ?>
        </div>
        <div id="cv-headline" style="font-size:12px;color:#555;font-style:italic;margin-top:3px;">
          <?= esc($profile?->headline ?? '') ?>
        </div>
      </div>

      <!-- ② Contacts -->
      <div id="cv-contacts" style="display:flex;flex-wrap:wrap;gap:7px 18px;font-size:10px;color:#444;padding-bottom:9px;margin-bottom:12px;border-bottom:1px solid #e4e4e4;">
        <?php if (!empty($user?->email)): ?>
        <span id="cv-c-email">✉ <?= esc($user->email) ?></span>
        <?php endif; ?>
        <span id="cv-c-phone"<?= empty($profile?->phone) ? ' style="display:none"' : '' ?>>
          ☎ <?= esc(trim(($profile?->phone_code ?? '') . ' ' . ($profile?->phone ?? ''))) ?>
        </span>
        <span id="cv-c-loc"<?= (empty($profile?->city) && empty($profile?->country)) ? ' style="display:none"' : '' ?>>
          ⦿ <?= esc(implode(', ', array_filter([$profile?->city ?? '', $profile?->country ?? '']))) ?>
        </span>
        <?php if (!empty($profile?->linkedin)): ?>
        <span id="cv-c-li">🔗 LinkedIn</span>
        <?php endif; ?>
      </div>

      <!-- ③ Profil -->
      <div id="cv-sec-about"<?= empty($profile?->summary) ? ' style="display:none"' : '' ?>>
        <div class="cv-sec-head">PROFIL PROFESSIONNEL</div>
        <p id="cv-summary" style="margin:5px 0 0;font-size:10.5px;"><?= esc($profile?->summary ?? '') ?></p>
      </div>

      <!-- ④ Compétences -->
      <?php $cvSkills = implode(' · ', array_filter(array_map('trim', array_column((array)$skills, 'skill_name')))); ?>
      <div id="cv-sec-skills"<?= empty($cvSkills) ? ' style="display:none"' : '' ?>>
        <div class="cv-sec-head">COMPÉTENCES</div>
        <p id="cv-skills-text" style="margin:5px 0 0;font-size:10.5px;"><?= esc($cvSkills) ?></p>
      </div>

      <!-- ⑤ Expériences -->
      <?php if (!empty($experiences)): ?>
      <div>
        <div class="cv-sec-head">EXPÉRIENCES PROFESSIONNELLES</div>
        <?php foreach ($experiences as $exp): ?>
        <div style="margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #f2f2f2;">
          <div style="display:flex;justify-content:space-between;align-items:baseline;">
            <strong style="font-size:12px;"><?= esc($exp->title ?? '') ?></strong>
            <span style="font-size:10px;color:#666;white-space:nowrap;">
              <?= $exp->start_date ? date('M Y', strtotime($exp->start_date)) : '' ?>
              — <?= $exp->is_current ? 'Présent' : ($exp->end_date ? date('M Y', strtotime($exp->end_date)) : 'Présent') ?>
            </span>
          </div>
          <div style="font-size:11px;color:#444;font-style:italic;">
            <?= esc($exp->company) ?>
            <?php if (!empty($exp->contract)): ?> &middot; <?= esc($exp->contract) ?><?php endif; ?>
            <?php if (!empty($exp->location)): ?> &middot; <?= esc($exp->location) ?><?php endif; ?>
          </div>
          <?php if (!empty($exp->description)): ?>
          <p style="margin:4px 0 0;font-size:10px;color:#222;"><?= nl2br(esc($exp->description)) ?></p>
          <?php endif; ?>
          <?php if (!empty($exp->skills_gained)): ?>
          <div style="margin-top:3px;font-size:10px;color:#666;"><em>Compétences :</em> <?= esc($exp->skills_gained) ?></div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- ⑥ Formations -->
      <?php if (!empty($education)): ?>
      <div>
        <div class="cv-sec-head">FORMATIONS</div>
        <?php foreach ($education as $edu): ?>
        <div style="margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #f2f2f2;">
          <div style="display:flex;justify-content:space-between;align-items:baseline;">
            <strong style="font-size:12px;">
              <?= esc($edu->degree) ?>
              <?php if (isset($edu->niveau) && !empty($edu->niveau)): ?>
                <span style="font-weight:400;font-size:10px;color:#666;">(<?= esc($edu->niveau) ?>)</span>
              <?php endif; ?>
              <?php if (!empty($edu->field)): ?> &mdash; <span style="font-weight:400;"><?= esc($edu->field) ?></span><?php endif; ?>
            </strong>
            <span style="font-size:10px;color:#666;white-space:nowrap;">
              <?= !empty($edu->start_year) ? $edu->start_year : '' ?>
              <?= !empty($edu->end_year) ? ' – ' . $edu->end_year : '' ?>
            </span>
          </div>
          <div style="font-size:11px;color:#444;font-style:italic;"><?= esc($edu->institution) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div><!-- end #cv-preview -->
  </div>
</div><!-- end #cv-col -->

</div><!-- end row g-0 -->

<?php
// ── LinkedIn Import Preview Modal ─────────────────────────────────────────────
$liPreview = session()->get('linkedin_import_preview');
?>
<?php if (!empty($liPreview)): ?>
<div class="modal fade" id="linkedinImportModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="liImportTitle" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= base_url('linkedin/import/confirm') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Header -->
                <div class="modal-header text-white py-3" style="background:#0A66C2;border-radius:.375rem .375rem 0 0;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-linkedin fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0" id="liImportTitle">Confirmer l'import LinkedIn</h6>
                            <small class="opacity-75">Choisissez les données à importer</small>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="modal-body p-0">
                    <div class="px-4 pt-3 pb-2">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle me-1 text-primary"></i>
                            Les données suivantes ont été trouvées sur votre profil LinkedIn.
                            Décochez les champs que vous ne souhaitez <em>pas</em> importer.
                        </p>
                    </div>

                    <div class="list-group list-group-flush">
                        <?php foreach ($liPreview as $key => $item): ?>
                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 px-4 py-3" style="cursor:pointer;">
                            <input class="form-check-input flex-shrink-0 mt-0"
                                   type="checkbox"
                                   name="import_fields[]"
                                   value="<?= esc($key) ?>"
                                   id="li_field_<?= esc($key) ?>"
                                   checked>
                            <div class="flex-grow-1 overflow-hidden">
                                <span class="badge rounded-pill mb-1" style="background:#e8f0fe;color:#0A66C2;font-size:.7rem;">
                                    <?= esc($item['label']) ?>
                                </span>
                                <?php if (!empty($item['is_image'])): ?>
                                    <div class="mt-1">
                                        <img src="<?= esc($item['new']) ?>"
                                             class="rounded-circle border"
                                             style="width:56px;height:56px;object-fit:cover;"
                                             alt="Photo LinkedIn"
                                             onerror="this.closest('label').style.display='none'">
                                    </div>
                                <?php else: ?>
                                    <div class="fw-semibold text-truncate small mt-1"><?= esc($item['new']) ?></div>
                                <?php endif; ?>
                            </div>
                            <i class="bi bi-check-circle-fill text-success flex-shrink-0" style="font-size:1.1rem;"></i>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 pt-0 pb-3 px-4 gap-2">
                    <a href="<?= base_url('linkedin/import/cancel') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-sm fw-semibold text-white ms-auto" style="background:#0A66C2;min-width:130px;">
                        <i class="bi bi-cloud-arrow-down me-1"></i>Importer
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = new bootstrap.Modal(document.getElementById('linkedinImportModal'), { backdrop: 'static' });
    modal.show();

    // Toggle checkmark icon when checkbox changes
    document.querySelectorAll('#linkedinImportModal .form-check-input').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var icon = this.closest('label').querySelector('.bi-check-circle-fill, .bi-circle');
            if (icon) {
                icon.classList.toggle('bi-check-circle-fill', this.checked);
                icon.classList.toggle('bi-circle', !this.checked);
                icon.classList.toggle('text-success', this.checked);
                icon.classList.toggle('text-muted', !this.checked);
            }
        });
    });
});
</script>
<?php endif; ?>

<script>
// Phone preview
const phoneCode = document.getElementById('phoneCodeSelect');
const phoneNum  = document.getElementById('phoneNumber');
const preview   = document.getElementById('phonePreview');
function updatePhonePreview() {
    const num = phoneNum.value.trim();
    preview.textContent = num ? (phoneCode.value + ' ' + num) : '';
}
phoneCode.addEventListener('change', updatePhonePreview);
phoneNum.addEventListener('input', updatePhonePreview);
updatePhonePreview();

// City suggestions per country
const citiesByCountry = {
    'Algeria':         ['Alger','Oran','Constantine','Annaba','Blida','Sétif','Tlemcen','Batna','Béjaïa'],
    'France':          ['Paris','Lyon','Marseille','Toulouse','Bordeaux','Nantes','Lille','Strasbourg','Nice','Rennes'],
    'Morocco':         ['Casablanca','Rabat','Fès','Marrakech','Agadir','Tanger','Meknès','Oujda'],
    'Tunisia':         ['Tunis','Sfax','Sousse','Kairouan','Bizerte','Gabès'],
    'Egypt':           ['Cairo','Alexandria','Giza','Shubra El-Kheima','Port Said'],
    'Saudi Arabia':    ['Riyadh','Jeddah','Mecca','Medina','Dammam'],
    'United Arab Emirates': ['Dubai','Abu Dhabi','Sharjah','Ajman','Ras Al Khaimah'],
    'Germany':         ['Berlin','Hamburg','Munich','Cologne','Frankfurt','Stuttgart','Düsseldorf'],
    'Belgium':         ['Brussels','Antwerp','Ghent','Liège','Bruges'],
    'Switzerland':     ['Zurich','Geneva','Basel','Bern','Lausanne'],
    'Canada':          ['Toronto','Montreal','Vancouver','Calgary','Ottawa','Quebec City'],
    'United States':   ['New York','Los Angeles','Chicago','Houston','Phoenix','San Francisco','Seattle'],
    'United Kingdom':  ['London','Manchester','Birmingham','Leeds','Glasgow','Edinburgh'],
    'Spain':           ['Madrid','Barcelona','Valencia','Seville','Zaragoza'],
    'Italy':           ['Rome','Milan','Naples','Turin','Florence'],
};

const countrySelect = document.getElementById('countrySelect');
const cityInput     = document.getElementById('cityInput');
const cityList      = document.getElementById('citySuggestions');

function loadCitySuggestions(country) {
    cityList.innerHTML = '';
    (citiesByCountry[country] || []).forEach(c => {
        const opt = document.createElement('option');
        opt.value = c;
        cityList.appendChild(opt);
    });
}

countrySelect.addEventListener('change', () => loadCitySuggestions(countrySelect.value));
// Init on page load for pre-filled country
loadCitySuggestions(countrySelect.value);

// ── Experience: is_current toggle ─────────────────────────────────────────
const expIsCurrent = document.getElementById('exp_is_current');
const expEndDate   = document.getElementById('exp_end_date');
if (expIsCurrent) {
    expIsCurrent.addEventListener('change', function () {
        expEndDate.disabled = this.checked;
        if (this.checked) expEndDate.value = '';
    });
}

// ── Experience: Manager autocomplete ──────────────────────────────────────
(function () {
    const input       = document.getElementById('exp_manager_search');
    const hiddenId    = document.getElementById('exp_manager_user_id');
    const hiddenName  = document.getElementById('exp_manager_name_val');
    const suggestions = document.getElementById('exp_manager_suggestions');
    if (!input) return;

    let timer;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        hiddenId.value   = '';
        hiddenName.value = this.value.trim();
        const q = this.value.trim();
        if (q.length < 2) { suggestions.style.display = 'none'; return; }

        timer = setTimeout(async () => {
            try {
                const res  = await fetch(`<?= base_url('profile/users/search') ?>?q=` + encodeURIComponent(q));
                const data = await res.json();
                suggestions.innerHTML = '';
                if (!data.length) { suggestions.style.display = 'none'; return; }
                data.forEach(u => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item list-group-item-action py-1 small cursor-pointer';
                    li.textContent = u.name;
                    li.addEventListener('mousedown', e => {
                        e.preventDefault();
                        input.value      = u.name;
                        hiddenId.value   = u.id;
                        hiddenName.value = u.name;
                        suggestions.style.display = 'none';
                    });
                    suggestions.appendChild(li);
                });
                suggestions.style.display = 'block';
            } catch (e) { suggestions.style.display = 'none'; }
        }, 300);
    });

    document.addEventListener('click', e => {
        if (!input.contains(e.target)) suggestions.style.display = 'none';
    });
})();

// ── Experience Edit Modal ──────────────────────────────────────────────────
(function () {
    const updateUrl = '<?= base_url('profile/experience/update/') ?>';
    const searchUrl = '<?= base_url('profile/users/search') ?>';

    // Populate modal when edit button clicked
    document.querySelectorAll('.btn-exp-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('[data-exp-id]');
            const d    = card.dataset;

            document.getElementById('expEditForm').action = updateUrl + d.expId;
            document.getElementById('eem_title').value        = d.expTitle;
            document.getElementById('eem_company').value      = d.expCompany;
            document.getElementById('eem_org_id').value       = d.expOrgId && d.expOrgId !== '0' ? d.expOrgId : '';
            document.getElementById('eem_department').value   = d.expDepartment;
            document.getElementById('eem_location').value     = d.expLocation;
            document.getElementById('eem_start_date').value   = (d.expStart || '').substring(0, 7);
            document.getElementById('eem_end_date').value     = (d.expEnd   || '').substring(0, 7);
            document.getElementById('eem_description').value  = d.expDescription;
            document.getElementById('eem_skills_gained').value= d.expSkills;

            // Contract select
            const contractSel = document.getElementById('eem_contract');
            contractSel.value = d.expContract;

            // Level select
            const levelSel = document.getElementById('eem_level');
            levelSel.value = d.expLevel;

            // is_current
            const isCurrent = document.getElementById('eem_is_current');
            isCurrent.checked = d.expCurrent === '1';
            document.getElementById('eem_end_date').disabled = isCurrent.checked;

            // Manager
            document.getElementById('eem_manager_search').value = d.expManagerName;
            document.getElementById('eem_manager_user_id').value = d.expManagerId !== '0' ? d.expManagerId : '';
            document.getElementById('eem_manager_name_val').value = d.expManagerName;
        });
    });

    // is_current toggle inside modal
    document.getElementById('eem_is_current').addEventListener('change', function () {
        const endDate = document.getElementById('eem_end_date');
        endDate.disabled = this.checked;
        if (this.checked) endDate.value = '';
    });

    // Manager autocomplete inside modal
    (function () {
        const input       = document.getElementById('eem_manager_search');
        const hiddenId    = document.getElementById('eem_manager_user_id');
        const hiddenName  = document.getElementById('eem_manager_name_val');
        const suggestions = document.getElementById('eem_manager_suggestions');
        if (!input) return;

        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            hiddenId.value   = '';
            hiddenName.value = this.value.trim();
            const q = this.value.trim();
            if (q.length < 2) { suggestions.style.display = 'none'; return; }

            timer = setTimeout(async () => {
                try {
                    const res  = await fetch(searchUrl + '?q=' + encodeURIComponent(q));
                    const data = await res.json();
                    suggestions.innerHTML = '';
                    if (!data.length) { suggestions.style.display = 'none'; return; }
                    data.forEach(u => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item list-group-item-action py-1 small';
                        li.textContent = u.name;
                        li.addEventListener('mousedown', e => {
                            e.preventDefault();
                            input.value      = u.name;
                            hiddenId.value   = u.id;
                            hiddenName.value = u.name;
                            suggestions.style.display = 'none';
                        });
                        suggestions.appendChild(li);
                    });
                    suggestions.style.display = 'block';
                } catch (e) { suggestions.style.display = 'none'; }
            }, 300);
        });

        document.addEventListener('click', e => {
            if (!input.contains(e.target)) suggestions.style.display = 'none';
        });
    })();
})();

// ── Live ATS CV Preview ────────────────────────────────────────────────────
(function () {
    function val(sel) {
        const el = document.querySelector(sel);
        return el ? el.value.trim() : '';
    }

    function cvUpdate() {
        // Headline
        const headline = val('[name=headline]');
        const cvHeadline = document.getElementById('cv-headline');
        if (cvHeadline) cvHeadline.textContent = headline;

        // Summary
        const summary = val('[name=summary]');
        const cvSummary  = document.getElementById('cv-summary');
        const cvSecAbout = document.getElementById('cv-sec-about');
        if (cvSummary)  cvSummary.textContent     = summary;
        if (cvSecAbout) cvSecAbout.style.display   = summary ? '' : 'none';

        // Skills
        const skills = val('[name=skills]');
        const cvSkillsText = document.getElementById('cv-skills-text');
        const cvSecSkills  = document.getElementById('cv-sec-skills');
        if (cvSkillsText) cvSkillsText.textContent = skills.split(',').map(s => s.trim()).filter(Boolean).join(' · ');
        if (cvSecSkills)  cvSecSkills.style.display = skills ? '' : 'none';

        // Phone
        const pc = val('#phoneCodeSelect');
        const pn = val('#phoneNumber');
        const cvPhone = document.getElementById('cv-c-phone');
        if (cvPhone) {
            cvPhone.style.display = pn ? '' : 'none';
            cvPhone.textContent   = '✆ ' + (pc + ' ' + pn).trim();
        }

        // Location
        const city    = val('[name=city]');
        const country = document.querySelector('[name=country]');
        const countryVal = country ? country.options[country.selectedIndex]?.text : '';
        const cvLoc   = document.getElementById('cv-c-loc');
        if (cvLoc) {
            const loc = [city, countryVal].filter(Boolean).join(', ');
            cvLoc.style.display = loc ? '' : 'none';
            cvLoc.textContent   = '⊿ ' + loc;
        }

        // LinkedIn
        const linkedin = val('[name=linkedin]');
        const cvLi = document.getElementById('cv-c-li');
        if (cvLi) cvLi.style.display = linkedin ? '' : 'none';
    }

    // Attach to all basic-info form elements
    document.querySelectorAll('#basicInfoForm input, #basicInfoForm select, #basicInfoForm textarea').forEach(el => {
        el.addEventListener('input',  cvUpdate);
        el.addEventListener('change', cvUpdate);
    });

    cvUpdate(); // initial render
})();
</script>

<script>
// ── Project team member autocomplete ──────────────────────────────────────
(function () {
    var input       = document.getElementById('proj_member_search');
    var suggestions = document.getElementById('proj_member_suggestions');
    var selected    = document.getElementById('proj_members_selected');
    if (!input) return;

    var members = {};  // id → name
    var timer;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        var q = this.value.trim();
        if (q.length < 2) { suggestions.style.display = 'none'; return; }
        timer = setTimeout(function () {
            fetch('<?= base_url('profile/users/search') ?>?q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    suggestions.innerHTML = '';
                    if (!data.length) { suggestions.style.display = 'none'; return; }
                    data.forEach(function (u) {
                        var a = document.createElement('button');
                        a.type = 'button';
                        a.className = 'list-group-item list-group-item-action py-1 small';
                        a.textContent = u.name;
                        a.addEventListener('click', function () {
                            if (members[u.id]) return;
                            members[u.id] = u.name;
                            addChip(u.id, u.name);
                            suggestions.style.display = 'none';
                            input.value = '';
                        });
                        suggestions.appendChild(a);
                    });
                    suggestions.style.display = 'block';
                });
        }, 250);
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.style.display = 'none';
        }
    });

    function addChip(id, name) {
        var chip = document.createElement('span');
        chip.className = 'badge bg-primary d-flex align-items-center gap-1';
        chip.innerHTML = name + ' <button type="button" class="btn-close btn-close-white btn-sm" style="font-size:9px;"></button>';
        chip.querySelector('button').addEventListener('click', function () {
            delete members[id];
            selected.removeChild(chip);
            selected.querySelectorAll('[name="member_ids[]"]').forEach(function (inp) {
                if (inp.value == id) selected.removeChild(inp);
            });
        });
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'member_ids[]';
        hidden.value = id;
        selected.appendChild(chip);
        selected.appendChild(hidden);
    }

    // project is_current toggle
    var projCurrent = document.getElementById('proj_is_current');
    var projEnd     = document.getElementById('proj_end_date');
    if (projCurrent && projEnd) {
        projCurrent.addEventListener('change', function () {
            projEnd.disabled = this.checked;
            if (this.checked) projEnd.value = '';
        });
    }

    // volunteering is_current toggle
    var volCurrent = document.getElementById('vol_is_current');
    var volEnd     = document.getElementById('vol_end_date');
    if (volCurrent && volEnd) {
        volCurrent.addEventListener('change', function () {
            volEnd.disabled = this.checked;
            if (this.checked) volEnd.value = '';
        });
    }
})();
</script>


<script>
// ── Education Edit Modal ───────────────────────────────────────────────────
(function () {
    const updateUrl = '<?= base_url('profile/education/update/') ?>';
    document.querySelectorAll('.btn-edu-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('[data-edu-id]');
            const d = card.dataset;
            document.getElementById('eduEditForm').action = updateUrl + d.eduId;
            document.getElementById('eem_institution').value   = d.eduInstitution;
            document.getElementById('eem_edu_org_id').value    = d.eduOrgId && d.eduOrgId !== '0' ? d.eduOrgId : '';
            document.getElementById('eem_degree').value        = d.eduDegree;
            document.getElementById('eem_niveau').value        = d.eduNiveau;
            document.getElementById('eem_field').value         = d.eduField;
            document.getElementById('eem_start_year').value    = d.eduStart;
            document.getElementById('eem_end_year').value      = d.eduEnd;
            document.getElementById('eem_edu_description').value = d.eduDescription;
        });
    });
})();

// ── Certification Edit Modal ───────────────────────────────────────────────
(function () {
    const updateUrl = '<?= base_url('profile/certification/update/') ?>';
    document.querySelectorAll('.btn-cert-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('[data-cert-id]');
            const d = card.dataset;
            document.getElementById('certEditForm').action    = updateUrl + d.certId;
            document.getElementById('cem_name').value         = d.certName;
            document.getElementById('cem_organization').value = d.certOrganization;
            document.getElementById('cem_issue').value        = d.certIssue;
            document.getElementById('cem_expiry').value       = d.certExpiry;
            document.getElementById('cem_url').value          = d.certUrl;
        });
    });
})();

// ── Language Edit Modal ────────────────────────────────────────────────────
(function () {
    const updateUrl = '<?= base_url('profile/language/update/') ?>';
    document.querySelectorAll('.btn-lang-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('[data-lang-id]');
            const d = card.dataset;
            document.getElementById('langEditForm').action = updateUrl + d.langId;
            document.getElementById('lem_name').value  = d.langName;
            document.getElementById('lem_level').value = d.langLevel;
        });
    });
})();

// ── Project Edit Modal ─────────────────────────────────────────────────────
(function () {
    const updateUrl = '<?= base_url('profile/project/update/') ?>';
    document.querySelectorAll('.btn-proj-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('[data-proj-id]');
            const d = card.dataset;
            document.getElementById('projEditForm').action = updateUrl + d.projId;
            document.getElementById('pem_name').value        = d.projName;
            document.getElementById('pem_start').value       = d.projStart;
            document.getElementById('pem_end').value         = d.projEnd;
            document.getElementById('pem_description').value = d.projDescription;
            const isCurrent = document.getElementById('pem_current');
            isCurrent.checked = d.projCurrent === '1';
            document.getElementById('pem_end').disabled = isCurrent.checked;
        });
    });
    document.getElementById('pem_current')?.addEventListener('change', function () {
        const endEl = document.getElementById('pem_end');
        endEl.disabled = this.checked;
        if (this.checked) endEl.value = '';
    });
})();

// ── Volunteering Edit Modal ────────────────────────────────────────────────
(function () {
    const updateUrl = '<?= base_url('profile/volunteering/update/') ?>';
    document.querySelectorAll('.btn-vol-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('[data-vol-id]');
            const d = card.dataset;
            document.getElementById('volEditForm').action    = updateUrl + d.volId;
            document.getElementById('vem_organization').value = d.volOrganization;
            document.getElementById('vem_position').value     = d.volPosition;
            document.getElementById('vem_start').value        = d.volStart;
            document.getElementById('vem_end').value          = d.volEnd;
            document.getElementById('vem_description').value  = d.volDescription;
            const isCurrent = document.getElementById('vem_current');
            isCurrent.checked = d.volCurrent === '1';
            document.getElementById('vem_end').disabled = isCurrent.checked;
        });
    });
    document.getElementById('vem_current')?.addEventListener('change', function () {
        const endEl = document.getElementById('vem_end');
        endEl.disabled = this.checked;
        if (this.checked) endEl.value = '';
    });
})();

// ── Organisation Autocomplete ──────────────────────────────────────────────
(function () {
    const ORG_SEARCH = '<?= base_url('api/organizations/search') ?>';
    const BASE_UPLOAD = '<?= base_url('uploads/organizations/') ?>';

    function initOrgAc(input) {
        const hiddenId  = document.getElementById(input.dataset.hiddenId);
        const sug       = input.nextElementSibling?.tagName === 'INPUT'
                          ? input.nextElementSibling.nextElementSibling
                          : input.nextElementSibling;
        if (!hiddenId || !sug) return;

        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            hiddenId.value = '';
            const q = this.value.trim();
            if (q.length < 2) { sug.style.display = 'none'; return; }
            timer = setTimeout(async () => {
                try {
                    const res  = await fetch(ORG_SEARCH + '?q=' + encodeURIComponent(q));
                    const data = await res.json();
                    sug.innerHTML = '';
                    if (!data.length) { sug.style.display = 'none'; return; }
                    data.forEach(org => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item list-group-item-action py-1 d-flex align-items-center gap-2';
                        li.style.cursor = 'pointer';

                        if (org.logo) {
                            const img = document.createElement('img');
                            img.src = BASE_UPLOAD + org.logo;
                            img.style.cssText = 'width:24px;height:24px;object-fit:contain;border-radius:4px;background:#f8f9fa;border:1px solid #dee2e6;padding:2px;flex-shrink:0;';
                            li.appendChild(img);
                        } else {
                            const ic = document.createElement('span');
                            ic.innerHTML = '<i class="bi bi-buildings" style="font-size:1rem;color:var(--brand);flex-shrink:0;"></i>';
                            li.appendChild(ic);
                        }

                        const txt = document.createElement('span');
                        txt.className = 'small';
                        txt.innerHTML = '<strong>' + org.name + '</strong>'
                            + (org.type_name ? ' <span class="text-muted">\u00b7 ' + org.type_name + '</span>' : '');
                        li.appendChild(txt);

                        li.addEventListener('mousedown', e => {
                            e.preventDefault();
                            input.value    = org.name;
                            hiddenId.value = org.id;
                            sug.style.display = 'none';
                        });
                        sug.appendChild(li);
                    });
                    sug.style.display = 'block';
                } catch(e) { sug.style.display = 'none'; }
            }, 300);
        });

        document.addEventListener('click', e => {
            if (!input.contains(e.target) && !sug.contains(e.target)) sug.style.display = 'none';
        });
    }

    document.querySelectorAll('.org-ac').forEach(initOrgAc);
})();
</script>

<?= $this->endSection() ?>
