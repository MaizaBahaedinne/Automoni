<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i><?= lang('App.dash_recruiter_title') ?></h3>
    <a href="<?= base_url('jobs/create') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i><?= lang('App.btn_post_job') ?>
    </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h2 class="fw-bold text-primary"><?= count($jobs) ?></h2>
            <p class="text-muted mb-0 small"><?= lang('App.stats_active_jobs') ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h2 class="fw-bold text-success"><?= count($applications) ?></h2>
            <p class="text-muted mb-0 small"><?= lang('App.stats_total_apps') ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h2 class="fw-bold text-warning">
                <?= count(array_filter($applications, fn($a) => $a->status === 'pending')) ?>
            </h2>
            <p class="text-muted mb-0 small"><?= lang('App.stats_awaiting') ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h2 class="fw-bold text-info">
                <?= count(array_filter($applications, fn($a) => $a->status === 'shortlisted')) ?>
            </h2>
            <p class="text-muted mb-0 small"><?= lang('App.stats_shortlisted') ?></p>
        </div>
    </div>
</div>

<!-- My Organizations -->
<?php if (!empty($orgs)): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="bi bi-buildings me-2 text-primary"></i>Mes organisations <span class="badge bg-primary ms-1"><?= count($orgs) ?></span></h5>
        <a href="<?= base_url('organizations/create') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus me-1"></i>Nouvelle
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3">
        <?php foreach ($orgs as $org): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="d-flex align-items-center gap-3 p-3"
                     style="background:var(--bg);border:1px solid var(--border);border-radius:10px;">
                    <?php if (!empty($org->logo)): ?>
                        <img src="<?= base_url('uploads/' . esc($org->logo)) ?>"
                             style="width:44px;height:44px;object-fit:cover;border-radius:8px;flex-shrink:0;" alt="">
                    <?php else: ?>
                        <div style="width:44px;height:44px;border-radius:8px;background:linear-gradient(135deg,var(--brand-dark),#7c3aed);
                                    color:#fff;font-size:1.1rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <?= strtoupper(substr($org->name, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex-grow-1 min-width-0">
                        <div class="fw-semibold text-truncate" style="font-size:.875rem;"><?= esc($org->name) ?></div>
                        <?php if (!empty($org->city)): ?>
                        <div class="text-muted" style="font-size:.75rem;"><i class="bi bi-geo-alt me-1"></i><?= esc($org->city) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        <a href="<?= base_url('organizations/' . $org->id) ?>"
                           class="btn btn-outline-secondary btn-sm px-2" title="Voir" style="font-size:.7rem;">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= base_url('organizations/' . $org->id . '/edit') ?>"
                           class="btn btn-outline-primary btn-sm px-2" title="Modifier" style="font-size:.7rem;">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- My Jobs -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><?= lang('App.my_jobs') ?></h5>
        <a href="<?= base_url('jobs/create') ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus me-1"></i><?= lang('App.btn_new') ?>
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($jobs)): ?>
            <p class="text-muted text-center py-4"><?= lang('App.no_jobs_posted') ?></p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th><?= lang('App.col_job') ?></th><th><?= lang('App.filter_contract') ?></th><th><?= lang('App.col_status') ?></th><th><?= lang('App.col_views') ?></th><th><?= lang('App.col_date') ?></th><th><?= lang('App.col_actions') ?></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="text-decoration-none fw-semibold">
                                    <?= esc($job->title) ?>
                                </a>
                            </td>
                            <td><span class="badge bg-primary"><?= esc($job->contract_type) ?></span></td>
                            <td>
                                <span class="badge bg-<?= $job->status === 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst(esc($job->status)) ?>
                                </span>
                            </td>
                            <td><small class="text-muted"><?= (int) $job->views ?></small></td>
                            <td><small class="text-muted"><?= date('d M Y', strtotime($job->created_at)) ?></small></td>
                            <td>
                                <a href="<?= base_url('jobs/edit/' . $job->id) ?>" class="btn btn-outline-secondary btn-sm me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?= base_url('jobs/delete/' . $job->id) ?>" method="post" class="d-inline"
                                      onsubmit="return confirm('Delete this job?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Applications -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="fw-bold mb-0"><?= lang('App.recent_applications') ?></h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($applications)): ?>
            <p class="text-muted text-center py-4"><?= lang('App.no_applications_yet') ?></p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th><?= lang('App.col_candidate') ?></th><th><?= lang('App.col_job') ?></th><th><?= lang('App.col_date') ?></th><th><?= lang('App.col_status') ?></th><th><?= lang('App.col_actions') ?></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($applications, 0, 20) as $app): ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($app->candidate_name ?? 'Candidate #' . $app->user_id) ?></td>
                            <td class="text-muted small"><?= esc($app->job_title ?? '—') ?></td>
                            <td><small class="text-muted"><?= !empty($app->created_at) ? date('d M Y', strtotime($app->created_at)) : '—' ?></small></td>
                            <td>
                                <?php
                                $sc = ['pending'=>'warning','reviewed'=>'info','shortlisted'=>'success','rejected'=>'danger','hired'=>'primary'];
                                ?>
                                <span class="badge bg-<?= $sc[$app->status] ?? 'secondary' ?>"><?= ucfirst(esc($app->status)) ?></span>
                            </td>
                            <td>
                                <form action="<?= base_url('applications/' . $app->id . '/status') ?>" method="post" class="d-flex gap-1">
                                    <?= csrf_field() ?>
                                    <select name="status" class="form-select form-select-sm" style="width:130px;">
                                        <?php foreach (['pending','reviewed','shortlisted','rejected','hired'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $app->status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
