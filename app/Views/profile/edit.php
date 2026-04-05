<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-9">
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
                <form action="<?= base_url('profile/update') ?>" method="post">
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
                                   value="<?= esc(implode(', ', array_column((array) $skills, 'name'))) ?>"
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
            </div>
        </div>

        <!-- Experiences -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-briefcase me-2 text-primary"></i><?= lang('App.section_experience') ?></h5>
            </div>
            <div class="card-body">
                <?php if (!empty($experiences)): ?>
                    <?php foreach ($experiences as $exp): ?>
                    <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2">
                        <div>
                            <span class="fw-semibold"><?= esc($exp->title ?? '') ?></span> @ <?= esc($exp->company) ?>
                            <small class="text-muted d-block">
                                <?= $exp->start_date ? date('M Y', strtotime($exp->start_date)) : '' ?> –
                                <?= $exp->is_current ? lang('App.present') : ($exp->end_date ? date('M Y', strtotime($exp->end_date)) : lang('App.present')) ?>
                            </small>
                        </div>
                        <form action="<?= base_url('profile/experience/delete/' . $exp->id) ?>" method="post"
                              onsubmit="return confirm('Delete?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <form action="<?= base_url('profile/experience/add') ?>" method="post" class="row g-2 mt-2">
                    <?= csrf_field() ?>
                    <div class="col-md-4">
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="Job Title" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="company" class="form-control form-control-sm" placeholder="Company" required>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control form-control-sm" placeholder="End date">
                    </div>
                    <div class="col-12">
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Description (optional)"></textarea>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-plus me-1"></i><?= lang('App.add_experience') ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Education -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-mortarboard me-2 text-primary"></i><?= lang('App.section_education') ?></h5>
            </div>
            <div class="card-body">
                <?php if (!empty($education)): ?>
                    <?php foreach ($education as $edu): ?>
                    <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2">
                        <div>
                            <span class="fw-semibold"><?= esc($edu->degree) ?></span> — <?= esc($edu->school) ?>
                            <small class="text-muted d-block"><?= esc($edu->field_of_study ?? '') ?> <?= esc($edu->start_year ?? '') ?> – <?= esc($edu->end_year ?? '') ?></small>
                        </div>
                        <form action="<?= base_url('profile/education/delete/' . $edu->id) ?>" method="post"
                              onsubmit="return confirm('Delete?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <form action="<?= base_url('profile/education/add') ?>" method="post" class="row g-2 mt-2">
                    <?= csrf_field() ?>
                    <div class="col-md-4">
                        <input type="text" name="school" class="form-control form-control-sm" placeholder="School / University" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="degree" class="form-control form-control-sm" placeholder="Degree" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="field_of_study" class="form-control form-control-sm" placeholder="Field of Study">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="start_year" class="form-control form-control-sm" placeholder="Start Year" min="1950" max="2030">
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="end_year" class="form-control form-control-sm" placeholder="End Year" min="1950" max="2030">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-plus me-1"></i><?= lang('App.add_education') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
</script>

<?= $this->endSection() ?>
