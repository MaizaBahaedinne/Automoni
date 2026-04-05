<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-4">
    <!-- Job Detail -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?php if (!empty($job->company_logo)): ?>
                        <img src="<?= base_url('writable/uploads/logos/' . esc($job->company_logo)) ?>"
                             alt="logo" class="rounded" style="width:56px;height:56px;object-fit:cover;">
                    <?php else: ?>
                        <div class="rounded bg-secondary d-flex align-items-center justify-content-center text-white fw-bold fs-4"
                             style="width:56px;height:56px;">
                            <?= strtoupper(substr($job->company_name ?? 'C', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h2 class="fw-bold mb-0"><?= esc($job->title) ?></h2>
                        <a href="<?= base_url('companies/' . esc($job->slug ?? '')) ?>" class="text-muted text-decoration-none">
                            <?= esc($job->company_name) ?>
                        </a>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="badge bg-primary fs-6"><?= esc($job->contract_type) ?></span>
                    <?php if (!empty($job->location)): ?>
                        <span class="badge bg-light text-dark border fs-6"><i class="bi bi-geo-alt me-1"></i><?= esc($job->location) ?></span>
                    <?php endif; ?>
                    <?php if ($job->remote !== 'onsite'): ?>
                        <span class="badge bg-success fs-6"><?= ucfirst(esc($job->remote)) ?></span>
                    <?php endif; ?>
                    <span class="badge bg-light text-dark border fs-6"><?= ucfirst(esc($job->experience_level)) ?></span>
                </div>

                <?php if (!empty($job->salary_min)): ?>
                    <p class="text-success fw-semibold">
                        <i class="bi bi-currency-euro"></i>
                        <?= number_format($job->salary_min) ?>
                        <?= !empty($job->salary_max) ? ' – ' . number_format($job->salary_max) : '+' ?>
                        <?= esc($job->salary_currency ?? 'EUR') ?><?= lang('App.salary_per_year') ?>
                    </p>
                <?php endif; ?>

                <hr>
                <h5 class="fw-bold"><?= lang('App.job_description') ?></h5>
                <div class="text-muted"><?= nl2br(esc($job->description)) ?></div>

                <?php if (!empty($job->requirements)): ?>
                    <h5 class="fw-bold mt-4"><?= lang('App.job_requirements') ?></h5>
                    <div class="text-muted"><?= nl2br(esc($job->requirements)) ?></div>
                <?php endif; ?>

                <?php if (!empty($job->benefits)): ?>
                    <h5 class="fw-bold mt-4"><?= lang('App.job_benefits') ?></h5>
                    <div class="text-muted"><?= nl2br(esc($job->benefits)) ?></div>
                <?php endif; ?>

                <?php if (!empty($jobSkills)): ?>
                    <h5 class="fw-bold mt-4"><?= lang('App.job_skills') ?></h5>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($jobSkills as $skill): ?>
                            <span class="badge bg-secondary"><?= esc($skill['skill_name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Apply Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <?php if (!session()->get('logged_in')): ?>
                    <p class="text-muted small"><?= lang('App.login_to_apply') ?></p>
                    <a href="<?= base_url('login') ?>" class="btn btn-primary w-100 fw-semibold"><?= lang('App.login_to_apply') ?></a>
                <?php elseif (session()->get('user_role') !== 'job_seeker'): ?>
                    <p class="text-muted small"><?= lang('App.seekers_only') ?></p>
                <?php elseif ($hasApplied): ?>
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle me-2"></i><?= lang('App.already_applied') ?>
                    </div>
                <?php else: ?>
                    <h6 class="fw-bold mb-3"><?= lang('App.apply_job_title') ?></h6>
                    <form action="<?= base_url('jobs/' . $job->id . '/apply') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small"><?= lang('App.cv_optional') ?></label>
                            <textarea name="cover_letter" class="form-control" rows="4"
                                      placeholder="<?= lang('App.cover_letter_ph') ?>"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small"><?= lang('App.cv_optional') ?></label>
                            <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx">
                            <div class="form-text"><?= lang('App.cv_hint') ?></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-send me-1"></i><?= lang('App.btn_submit_apply') ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Company Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><?= lang('App.about_company') ?> <?= esc($job->company_name) ?></h6>
                <?php if (!empty($job->company_description)): ?>
                    <p class="text-muted small"><?= esc(substr($job->company_description, 0, 200)) ?>…</p>
                <?php endif; ?>
                <?php if (!empty($job->company_city)): ?>
                    <p class="small mb-1"><i class="bi bi-geo-alt text-muted me-1"></i><?= esc($job->company_city) ?>, <?= esc($job->company_country) ?></p>
                <?php endif; ?>
                <?php if (!empty($job->company_website)): ?>
                    <a href="<?= esc($job->company_website) ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                        <i class="bi bi-globe me-1"></i><?= lang('App.visit_website') ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meta -->
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body p-3">
                <small class="text-muted d-block"><i class="bi bi-eye me-1"></i><?= (int) $job->views ?> <?= lang('App.job_views') ?></small>
                <small class="text-muted d-block"><i class="bi bi-calendar me-1"></i><?= lang('App.job_posted') ?> <?= date('d M Y', strtotime($job->created_at)) ?></small>
                <?php if (!empty($job->expires_at)): ?>
                    <small class="text-muted d-block"><i class="bi bi-alarm me-1"></i><?= lang('App.job_expires') ?> <?= date('d M Y', strtotime($job->expires_at)) ?></small>
                <?php endif; ?>
            </div>
        </div>

        <?php if (session()->get('logged_in') && (int) session()->get('user_id') === (int) $job->user_id): ?>
            <div class="d-flex gap-2 mt-3">
                <a href="<?= base_url('jobs/edit/' . $job->id) ?>" class="btn btn-outline-secondary btn-sm flex-fill">
                    <i class="bi bi-pencil me-1"></i><?= lang('App.btn_edit') ?>
                </a>
                <form action="<?= base_url('jobs/delete/' . $job->id) ?>" method="post" class="flex-fill"
                      onsubmit="return confirm('<?= lang('App.confirm_delete') ?>')">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-trash me-1"></i><?= lang('App.btn_delete') ?></button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
