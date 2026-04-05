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
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Headline</label>
                            <input type="text" name="headline" class="form-control"
                                   value="<?= esc(old('headline', $profile?->headline)) ?>"
                                   placeholder="e.g. Senior PHP Developer | Open to opportunities">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Summary</label>
                            <textarea name="summary" class="form-control" rows="4"
                                      placeholder="Tell employers about yourself..."><?= esc(old('summary', $profile?->summary)) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('App.field_phone') ?></label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?= esc(old('phone', $profile?->phone)) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold"><?= lang('App.field_city') ?></label>
                            <input type="text" name="city" class="form-control"
                                   value="<?= esc(old('city', $profile?->city)) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold"><?= lang('App.field_country') ?></label>
                            <input type="text" name="country" class="form-control"
                                   value="<?= esc(old('country', $profile?->country)) ?>">
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
                            <span class="fw-semibold"><?= esc($exp->job_title) ?></span> @ <?= esc($exp->company) ?>
                            <small class="text-muted d-block">
                                <?= date('M Y', strtotime($exp->start_date)) ?> –
                                <?= $exp->is_current ? lang('App.present') : date('M Y', strtotime($exp->end_date)) ?>
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
                        <input type="text" name="job_title" class="form-control form-control-sm" placeholder="Job Title" required>
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

<?= $this->endSection() ?>
