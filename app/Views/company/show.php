<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <?php if (!empty($company->logo)): ?>
                <img src="<?= base_url('uploads/logos/' . esc($company->logo)) ?>"
                     alt="logo" class="mx-auto mb-3 rounded" style="width:80px;height:80px;object-fit:cover;">
            <?php else: ?>
                <div class="rounded bg-secondary text-white d-flex align-items-center fw-bold justify-content-center mx-auto mb-3 fs-2"
                     style="width:80px;height:80px;">
                    <?= strtoupper(substr($company->name, 0, 1)) ?>
                </div>
            <?php endif; ?>
            <h4 class="fw-bold"><?= esc($company->name) ?></h4>
            <?php if (!empty($company->industry)): ?>
                <p class="text-muted small mb-1"><i class="bi bi-building me-1"></i><?= esc($company->industry) ?></p>
            <?php endif; ?>
            <?php if (!empty($company->size)): ?>
                <p class="text-muted small mb-1"><i class="bi bi-people me-1"></i><?= esc($company->size) ?> <?= lang('App.employees') ?></p>
            <?php endif; ?>
            <?php if (!empty($company->city)): ?>
                <p class="text-muted small mb-1"><i class="bi bi-geo-alt me-1"></i><?= esc($company->city) ?><?= !empty($company->country) ? ', ' . esc($company->country) : '' ?></p>
            <?php endif; ?>
            <?php if (!empty($company->website)): ?>
                <a href="<?= esc($company->website) ?>" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm mt-2 w-100">
                    <i class="bi bi-globe me-1"></i><?= lang('App.visit_website') ?>
                </a>
            <?php endif; ?>
            <?php if (!empty($company->linkedin)): ?>
                <a href="<?= esc($company->linkedin) ?>" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm mt-1 w-100">
                    <i class="bi bi-linkedin me-1"></i><?= lang('App.linkedin_lbl') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <?php if (!empty($company->description)): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i><?= lang('App.section_about') ?></h5>
                <p class="text-muted mb-0"><?= nl2br(esc($company->description)) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between">
                <h5 class="fw-bold mb-0"><?= lang('App.open_positions') ?> <span class="badge bg-primary ms-1"><?= count($jobs) ?></span></h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($jobs)): ?>
                    <p class="text-muted text-center py-4"><?= lang('App.no_open_positions') ?></p>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                        <div>
                            <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="fw-semibold text-decoration-none text-dark">
                                <?= esc($job->title) ?>
                            </a>
                            <div class="mt-1">
                                <span class="badge bg-primary"><?= esc($job->contract_type) ?></span>
                                <?php if (!empty($job->location)): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt me-1"></i><?= esc($job->location) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="btn btn-outline-primary btn-sm">
                            <?= lang('App.btn_view') ?> <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
