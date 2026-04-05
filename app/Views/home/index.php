<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<div class="hero-gradient text-white p-5 mb-5 text-center position-relative overflow-hidden">
    <div class="position-relative" style="z-index:1">
        <h1 class="display-5 fw-bold mb-2"><?= lang('App.hero_title') ?></h1>
        <p class="lead mb-4 opacity-90"><?= lang('App.hero_subtitle') ?></p>
        <form action="<?= base_url('jobs') ?>" method="get" class="row g-2 justify-content-center">
            <div class="col-md-5">
                <input type="text" name="keyword" class="form-control form-control-lg border-0"
                       placeholder="<?= lang('App.hero_keyword_ph') ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="location" class="form-control form-control-lg border-0"
                       placeholder="<?= lang('App.hero_location_ph') ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-light btn-lg fw-bold px-4" style="border-radius:8px;">
                    <i class="bi bi-search me-1"></i><?= lang('App.hero_search_btn') ?>
                </button>
            </div>
        </form>
        <div class="mt-3 opacity-75 small">
            <i class="bi bi-shield-check me-1"></i>Trusted by 10,000+ professionals
        </div>
    </div>
    <!-- decorative blobs -->
    <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;background:rgba(255,255,255,.07);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-40px;left:-40px;width:160px;height:160px;background:rgba(255,255,255,.06);border-radius:50%;"></div>
</div>

<!-- Latest Jobs -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><?= lang('App.latest_jobs') ?></h4>
    <a href="<?= base_url('jobs') ?>" class="btn btn-outline-primary btn-sm">
        <?= lang('App.view_all_jobs') ?> <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>

<?php if (empty($latestJobs)): ?>
    <div class="text-center py-5" style="color:var(--muted)">
        <i class="bi bi-briefcase display-3 d-block mb-2 opacity-25"></i>
        <p><?= lang('App.no_jobs_yet') ?></p>
    </div>
<?php else: ?>
    <div class="row g-4 mb-5">
        <?php foreach ($latestJobs as $job): ?>
        <div class="col-md-4">
            <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="text-decoration-none">
            <div class="card h-100 p-3" style="transition:transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?php if (!empty($job->company_logo)): ?>
                        <img src="<?= base_url('writable/uploads/logos/' . esc($job->company_logo)) ?>"
                             alt="logo" class="rounded-3" style="width:44px;height:44px;object-fit:cover;flex-shrink:0;">
                    <?php else: ?>
                        <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold text-white"
                             style="width:44px;height:44px;flex-shrink:0;background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                            <?= strtoupper(substr($job->company_name ?? 'C', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <p class="fw-bold mb-0 text-dark small"><?= esc($job->title) ?></p>
                        <p class="text-muted mb-0" style="font-size:.8rem"><?= esc($job->company_name) ?></p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-1 mt-auto">
                    <span class="badge bg-primary"><?= esc($job->contract_type) ?></span>
                    <?php if (!empty($job->location)): ?>
                        <span class="badge" style="background:#f1f5f9;color:#64748b;"><i class="bi bi-geo-alt me-1"></i><?= esc($job->location) ?></span>
                    <?php endif; ?>
                    <?php if ($job->remote !== 'onsite'): ?>
                        <span class="badge" style="background:#f0fdf4;color:#15803d;"><?= ucfirst(esc($job->remote)) ?></span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($job->salary_min)): ?>
                    <p class="mb-0 mt-2 fw-semibold" style="color:#15803d;font-size:.85rem;">
                        <?= number_format($job->salary_min) ?><?= !empty($job->salary_max) ? '–'.number_format($job->salary_max) : '+' ?><?= lang('App.salary_per_year') ?>
                    </p>
                <?php endif; ?>
            </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- CTA -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 p-4 text-white h-100" style="background:linear-gradient(135deg,#4f46e5,#6d28d9);border-radius:16px!important;">
            <i class="bi bi-person-badge display-5 mb-3"></i>
            <h5 class="fw-bold"><?= lang('App.cta_seeker_title') ?></h5>
            <p class="opacity-85 mb-3"><?= lang('App.cta_seeker_body') ?></p>
            <a href="<?= base_url('register') ?>" class="btn btn-light fw-bold align-self-start px-4"><?= lang('App.cta_seeker_btn') ?></a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 p-4 text-white h-100" style="background:linear-gradient(135deg,#db2777,#9333ea);border-radius:16px!important;">
            <i class="bi bi-building display-5 mb-3"></i>
            <h5 class="fw-bold"><?= lang('App.cta_recruiter_title') ?></h5>
            <p class="opacity-85 mb-3"><?= lang('App.cta_recruiter_body') ?></p>
            <a href="<?= base_url('register') ?>" class="btn btn-light fw-bold align-self-start px-4"><?= lang('App.cta_recruiter_btn') ?></a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

