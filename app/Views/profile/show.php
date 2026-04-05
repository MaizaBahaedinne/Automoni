<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-4">
    <!-- Left: Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4 mb-3">
            <?php if (!empty(isset($profile->avatar) ? $profile->avatar : null)): ?>
                <img src="<?= base_url('writable/uploads/' . esc($profile->avatar)) ?>"
                     class="rounded-circle mx-auto mb-3" style="width:90px;height:90px;object-fit:cover;" alt="avatar">
            <?php else: ?>
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3 fw-bold fs-2"
                     style="width:90px;height:90px;">
                    <?= strtoupper(substr($user?->first_name ?? 'U', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <h4 class="fw-bold mb-0"><?= esc($user?->first_name . ' ' . $user?->last_name) ?></h4>
            <?php if (!empty($profile?->headline)): ?>
                <p class="text-muted small mb-1"><?= esc($profile->headline) ?></p>
            <?php endif; ?>
            <?php if (!empty($profile?->city)): ?>
                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= esc($profile->city) ?><?= !empty($profile->country) ? ', ' . esc($profile->country) : '' ?></small>
            <?php endif; ?>
            <hr>
            <?php if (!empty($profile?->linkedin)): ?>
                <a href="<?= esc($profile->linkedin) ?>" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm mb-1 w-100">
                    <i class="bi bi-linkedin me-1"></i><?= lang('App.linkedin_lbl') ?>
                </a>
            <?php endif; ?>
            <?php if (!empty($profile?->github)): ?>
                <a href="<?= esc($profile->github) ?>" target="_blank" rel="noopener" class="btn btn-outline-dark btn-sm mb-1 w-100">
                    <i class="bi bi-github me-1"></i><?= lang('App.github_lbl') ?>
                </a>
            <?php endif; ?>
            <?php if (!empty($profile?->cv_file)): ?>
                <a href="<?= base_url('profile/cv/download') ?>" class="btn btn-outline-success btn-sm w-100">
                    <i class="bi bi-download me-1"></i><?= lang('App.download_cv') ?>
                </a>
            <?php endif; ?>
        </div>

        <?php if (session()->get('user_id') == $user?->id): ?>
        <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary w-100 mb-2">
            <i class="bi bi-pencil me-1"></i><?= lang('App.edit_profile') ?>
        </a>
        <?php endif; ?>
    </div>

    <!-- Right: Details -->
    <div class="col-lg-8">
        <?php if (!empty($profile?->summary)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h5 class="fw-bold"><i class="bi bi-person me-2 text-primary"></i><?= lang('App.section_about') ?></h5>
                <p class="text-muted mb-0"><?= nl2br(esc($profile->summary)) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Skills -->
        <?php if (!empty($skills)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h5 class="fw-bold"><i class="bi bi-tools me-2 text-primary"></i><?= lang('App.section_skills') ?></h5>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($skills as $skill): ?>
                        <span class="badge bg-secondary fs-6"><?= esc($skill->name) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Experience -->
        <?php if (!empty($experiences)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h5 class="fw-bold"><i class="bi bi-briefcase me-2 text-primary"></i><?= lang('App.section_experience') ?></h5>
                <?php foreach ($experiences as $exp): ?>
                <div class="mb-3 pb-2 border-bottom">
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold"><?= esc($exp->job_title) ?></span>
                        <small class="text-muted">
                            <?= date('M Y', strtotime($exp->start_date)) ?> –
                            <?= $exp->is_current ? lang('App.present') : date('M Y', strtotime($exp->end_date)) ?>
                        </small>
                    </div>
                    <div class="text-muted small"><?= esc($exp->company) ?></div>
                    <?php if (!empty($exp->description)): ?>
                        <p class="small mt-1 mb-0"><?= nl2br(esc($exp->description)) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Education -->
        <?php if (!empty($education)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h5 class="fw-bold"><i class="bi bi-mortarboard me-2 text-primary"></i><?= lang('App.section_education') ?></h5>
                <?php foreach ($education as $edu): ?>
                <div class="mb-2 pb-2 border-bottom">
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold"><?= esc($edu->degree) ?><?= !empty($edu->field_of_study) ? ' in ' . esc($edu->field_of_study) : '' ?></span>
                        <small class="text-muted">
                            <?= !empty($edu->start_year) ? esc($edu->start_year) : '' ?>
                            <?= !empty($edu->end_year) ? ' – ' . esc($edu->end_year) : '' ?>
                        </small>
                    </div>
                    <div class="text-muted small"><?= esc($edu->school) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
