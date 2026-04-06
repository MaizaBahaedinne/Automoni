<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-4">
    <!-- Filters sidebar -->
    <div class="col-lg-3">
        <div class="card p-3">
            <h5 class="fw-bold mb-3"><i class="bi bi-funnel me-1"></i><?= lang('App.filters_title') ?></h5>
            <form action="<?= base_url('jobs') ?>" method="get">
                <div class="mb-3">
                    <label class="form-label small"><?= lang('App.filter_keyword') ?></label>
                    <input type="text" name="keyword" class="form-control form-control-sm"
                           placeholder="PHP, Laravel…" value="<?= esc($filters['keyword'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small"><?= lang('App.filter_location') ?></label>
                    <input type="text" name="location" class="form-control form-control-sm"
                           placeholder="Paris, Remote…" value="<?= esc($filters['location'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small"><?= lang('App.filter_contract') ?></label>
                    <select name="contract_type" class="form-select form-select-sm">
                        <option value=""><?= lang('App.any') ?></option>
                        <?php foreach (['CDI','CDD','Freelance','Internship','PartTime'] as $ct): ?>
                            <option value="<?= $ct ?>" <?= ($filters['contract_type'] ?? '') === $ct ? 'selected' : '' ?>><?= $ct ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small"><?= lang('App.filter_remote') ?></label>
                    <select name="remote" class="form-select form-select-sm">
                        <option value=""><?= lang('App.any') ?></option>
                        <?php foreach (['onsite','remote','hybrid'] as $r): ?>
                            <option value="<?= $r ?>" <?= ($filters['remote'] ?? '') === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small"><?= lang('App.filter_experience') ?></label>
                    <select name="experience_level" class="form-select form-select-sm">
                        <option value=""><?= lang('App.any') ?></option>
                        <?php foreach (['junior','mid','senior','lead'] as $e): ?>
                            <option value="<?= $e ?>" <?= ($filters['experience_level'] ?? '') === $e ? 'selected' : '' ?>><?= ucfirst($e) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary btn-sm w-100"><?= lang('App.btn_apply_filters') ?></button>
                <?php if (array_filter($filters ?? [])): ?>
                    <a href="<?= base_url('jobs') ?>" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                        <?= lang('App.btn_clear_filters') ?>
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Job list -->
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><?= lang('App.jobs_found', [count($jobs)]) ?></h5>
            <?php if (session()->get('user_role') === 'recruiter'): ?>
                <a href="<?= base_url('jobs/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i><?= lang('App.btn_post_job') ?>
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($jobs)): ?>
            <div class="text-center py-5" style="color:var(--muted)">
                <i class="bi bi-briefcase display-4 d-block mb-2"></i>
                <?= lang('App.no_jobs_match') ?>
            </div>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
            <div class="card mb-3 job-card">
                <div class="card-body d-flex gap-3 align-items-start p-3">
                    <?php if (!empty($job->company_logo)): ?>
                        <img src="<?= base_url('uploads/logos/' . esc($job->company_logo)) ?>"
                             alt="logo" class="rounded" style="width:48px;height:48px;object-fit:cover;flex-shrink:0;">
                    <?php else: ?>
                        <div class="rounded d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:48px;height:48px;flex-shrink:0;background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                            <?= strtoupper(substr($job->company_name ?? 'C', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-0">
                                    <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="text-decoration-none text-dark">
                                        <?= esc($job->title) ?>
                                    </a>
                                </h6>
                                <small style="color:var(--muted)"><?= esc($job->company_name) ?></small>
                            </div>
                            <small style="color:var(--muted)"><?= date('d M Y', strtotime($job->created_at)) ?></small>
                        </div>
                        <div class="mt-2 d-flex flex-wrap gap-1">
                            <span class="badge bg-primary"><?= esc($job->contract_type) ?></span>
                            <?php if (!empty($job->location)): ?>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-geo-alt me-1"></i><?= esc($job->location) ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($job->remote !== 'onsite'): ?>
                                <span class="badge bg-success"><?= ucfirst(esc($job->remote)) ?></span>
                            <?php endif; ?>
                            <span class="badge bg-light text-dark border"><?= ucfirst(esc($job->experience_level)) ?></span>
                        </div>
                        <?php if (!empty($job->salary_min)): ?>
                            <p class="text-success small mb-0 mt-1">
                                <i class="bi bi-currency-euro"></i>
                                <?= number_format($job->salary_min) ?><?= !empty($job->salary_max) ? ' – ' . number_format($job->salary_max) : '+' ?><?= lang('App.salary_per_year') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if ($pager): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

