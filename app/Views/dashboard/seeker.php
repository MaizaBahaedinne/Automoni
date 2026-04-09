<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2 text-primary"></i><?= lang('App.dash_seeker_title') ?></h3>
    <a href="<?= base_url('jobs') ?>" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-search me-1"></i><?= lang('App.nav_jobs') ?>
    </a>
</div>

<!-- Profile completeness -->
<?php if ($profile): ?>
    <?php $completeness = (int) ($profile->completeness ?? 0); ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold"><?= lang('App.profile_completeness') ?></span>
                <span class="badge bg-<?= $completeness >= 80 ? 'success' : ($completeness >= 50 ? 'warning' : 'danger') ?>">
                    <?= $completeness ?>%
                </span>
            </div>
            <div class="progress" style="height:8px;">
                <div class="progress-bar bg-<?= $completeness >= 80 ? 'success' : ($completeness >= 50 ? 'warning' : 'danger') ?>"
                     style="width:<?= $completeness ?>%"></div>
            </div>
            <?php if ($completeness < 80): ?>
                <a href="<?= base_url('profile/edit') ?>" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="bi bi-pencil me-1"></i><?= lang('App.complete_profile_btn') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <h2 class="fw-bold text-primary"><?= count($applications) ?></h2>
            <p class="text-muted mb-0 small"><?= lang('App.stats_sent') ?></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <h2 class="fw-bold text-success">
                <?= count(array_filter($applications, fn($a) => $a->status === 'shortlisted')) ?>
            </h2>
            <p class="text-muted mb-0 small"><?= lang('App.stats_shortlisted') ?></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <h2 class="fw-bold text-warning">
                <?= count(array_filter($applications, fn($a) => $a->status === 'pending')) ?>
            </h2>
            <p class="text-muted mb-0 small"><?= lang('App.stats_pending') ?></p>
        </div>
    </div>
</div>

<!-- My Applications -->
<?php
// Unread interview notifications
$_seekerNotifs = [];
try {
    $_seekerNotifs = array_filter(
        model(\App\Models\NotificationModel::class)->getRecent((int) session()->get('user_id'), 10),
        fn($n) => !$n->is_read
    );
} catch (\Throwable $_e) {}
?>
<?php if (!empty($_seekerNotifs)): ?>
<div class="mb-4">
    <?php foreach ($_seekerNotifs as $_n): ?>
    <div class="d-flex align-items-start gap-3 p-3 mb-2"
         style="background:#eef2ff;border:1px solid #c7d2fe;border-radius:var(--radius);">
        <div style="width:38px;height:38px;border-radius:50%;background:var(--brand);
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-calendar-check-fill text-white" style="font-size:.9rem;"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-semibold" style="font-size:.875rem;"><?= esc($_n->title) ?></div>
            <?php if (!empty($_n->body)): ?>
            <div class="text-muted" style="font-size:.8rem;"><?= esc($_n->body) ?></div>
            <?php endif; ?>
            <div class="text-muted" style="font-size:.72rem;"><?= date('d/m/Y à H:i', strtotime($_n->created_at)) ?></div>
        </div>
        <form action="<?= base_url('notifications/' . $_n->id . '/read') ?>" method="post" class="d-flex align-items-start">
            <?= csrf_field() ?>
            <button type="submit" class="btn-close" style="font-size:.65rem;" title="Marquer comme lu"></button>
        </form>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><?= lang('App.my_applications') ?></h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($applications)): ?>
            <p class="text-muted text-center py-4"><?= lang('App.no_applications_yet') ?> <a href="<?= base_url('jobs') ?>"><?= lang('App.nav_jobs') ?></a></p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><?= lang('App.col_job') ?></th>
                            <th><?= lang('App.col_company') ?></th>
                            <th>Postulé le</th>
                            <th>Expiration</th>
                            <th><?= lang('App.col_status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('jobs/' . esc($app->slug ?? $app->job_id)) ?>" class="text-decoration-none fw-semibold">
                                    <?= esc($app->job_title ?? 'Job #' . $app->job_id) ?>
                                </a>
                            </td>
                            <td class="text-muted"><?= esc($app->company_name ?? '—') ?></td>
                            <td class="text-muted small">
                                <?php if (!empty($app->applied_at)): ?>
                                    <?= date('d', strtotime($app->applied_at)) . ' ' . lang('App.months.' . date('n', strtotime($app->applied_at))) . ' ' . date('Y', strtotime($app->applied_at)) ?>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                            <td class="small">
                                <?php if (!empty($app->expires_at)): ?>
                                    <?php
                                    $expTs  = strtotime($app->expires_at);
                                    $daysLeft = (int) ceil(($expTs - time()) / 86400);
                                    $expColor = $daysLeft <= 0 ? 'text-danger' : ($daysLeft <= 7 ? 'text-warning fw-semibold' : 'text-muted');
                                    ?>
                                    <span class="<?= $expColor ?>">
                                        <?= date('d', $expTs) . ' ' . lang('App.months.' . date('n', $expTs)) . ' ' . date('Y', $expTs) ?>
                                        <?php if ($daysLeft <= 0): ?><small>(expirée)</small>
                                        <?php elseif ($daysLeft <= 7): ?><small>(<?= $daysLeft ?>j)</small>
                                        <?php endif; ?>
                                    </span>
                                <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusColors = [
                                    'pending'     => 'warning',
                                    'reviewed'    => 'info',
                                    'shortlisted' => 'success',
                                    'rejected'    => 'danger',
                                    'hired'       => 'primary',
                                ];
                                $statusLabels = [
                                    'pending'     => 'En attente',
                                    'reviewed'    => 'En cours',
                                    'shortlisted' => 'Shortlisté',
                                    'rejected'    => 'Refusé',
                                    'hired'       => 'Recruté',
                                ];
                                $color = $statusColors[$app->status] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= $statusLabels[$app->status] ?? ucfirst(esc($app->status)) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recommended Jobs -->
<?php if (!empty($recommended)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="fw-bold mb-0"><i class="bi bi-stars text-warning me-1"></i><?= lang('App.recommended_jobs') ?></h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php foreach ($recommended as $job): ?>
            <div class="col-md-4">
                <div class="card border shadow-sm h-100">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-1">
                            <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="text-decoration-none text-dark">
                                <?= esc($job->title) ?>
                            </a>
                        </h6>
                        <small class="text-muted"><?= esc($job->company_name ?? '') ?></small>
                        <div class="mt-2">
                            <span class="badge bg-primary"><?= esc($job->contract_type) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
