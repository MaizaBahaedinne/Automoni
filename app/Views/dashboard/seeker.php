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
        <div class="d-flex align-items-center gap-2">
            <?php
            // Try to find interview linked to this notification — parse app_id from link
            $_notifAppId = null;
            if (!empty($_n->link) && preg_match('/interview-(\d+)/', $_n->link, $_m)) {
                $_notifAppId = (int) $_m[1];
            } elseif (!empty($interviewMap)) {
                // fallback: first available interview for this user
                $_notifAppId = array_key_first($interviewMap);
            }
            ?>
            <?php if ($_notifAppId && isset($interviewMap[$_notifAppId])): ?>
            <button type="button" class="btn btn-sm btn-primary px-3" style="font-size:.78rem;"
                    data-bs-toggle="modal" data-bs-target="#ivModal<?= $_notifAppId ?>">
                <i class="bi bi-calendar-event me-1"></i>Voir l'entretien
            </button>
            <?php endif; ?>
            <form action="<?= base_url('notifications/' . $_n->id . '/read') ?>" method="post" class="d-flex align-items-center">
                <?= csrf_field() ?>
                <button type="submit" class="btn-close" style="font-size:.65rem;" title="Marquer comme lu"></button>
            </form>
        </div>
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
                            <th></th>
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
                            <td>
                                <?php if (isset($interviewMap[$app->id])): ?>
                                <button type="button" class="btn btn-sm btn-outline-success px-2"
                                        data-bs-toggle="modal" data-bs-target="#ivModal<?= $app->id ?>"
                                        title="Voir l'entretien" style="font-size:.75rem;">
                                    <i class="bi bi-calendar-check me-1"></i>Entretien
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $interviewMap = $interviewMap ?? []; ?>
<?php foreach ($interviewMap as $_appId => $_iv): ?>
<?php
$_ivType  = $_iv->type === 'remote' ? 'Visioconférence' : 'Présentiel';
$_ivIcon  = $_iv->type === 'remote' ? 'camera-video' : 'building';
$_ivDate  = date('l d F Y', strtotime($_iv->scheduled_at));
$_ivTime  = date('H:i', strtotime($_iv->scheduled_at));
$_ivDur   = (int) $_iv->duration_min;
$_ivLoc   = $_iv->location ?? null;
$_ivNotes = $_iv->notes ?? null;
$_ivLocLbl = $_iv->type === 'remote' ? 'Lien de connexion' : 'Lieu';
?>
<div class="modal fade" id="ivModal<?= $_appId ?>" tabindex="-1"
     aria-labelledby="ivLabel<?= $_appId ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="ivLabel<?= $_appId ?>">
                    <i class="bi bi-calendar-check-fill me-2" style="color:var(--brand-dark);"></i>Détails de l'entretien
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <div style="background:#f0f4ff;border:1px solid #c7d2fe;border-radius:10px;padding:1rem 1.25rem;">
                    <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
                        <tr>
                            <td style="padding:6px 0;color:var(--muted);width:38%;">
                                <i class="bi bi-<?= $_ivIcon ?> me-2"></i>Type
                            </td>
                            <td style="padding:6px 0;font-weight:600;"><?= $_ivType ?></td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0;color:var(--muted);">
                                <i class="bi bi-calendar3 me-2"></i>Date
                            </td>
                            <td style="padding:6px 0;font-weight:600;"><?= esc($_ivDate) ?></td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0;color:var(--muted);">
                                <i class="bi bi-clock me-2"></i>Heure
                            </td>
                            <td style="padding:6px 0;font-weight:600;"><?= esc($_ivTime) ?></td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0;color:var(--muted);">
                                <i class="bi bi-hourglass-split me-2"></i>Durée
                            </td>
                            <td style="padding:6px 0;font-weight:600;"><?= $_ivDur ?> minutes</td>
                        </tr>
                        <?php if ($_ivLoc): ?>
                        <tr>
                            <td style="padding:6px 0;color:var(--muted);">
                                <i class="bi bi-<?= $_iv->type === 'remote' ? 'link-45deg' : 'geo-alt' ?> me-2"></i><?= esc($_ivLocLbl) ?>
                            </td>
                            <td style="padding:6px 0;font-weight:600;word-break:break-all;">
                                <?php if ($_iv->type === 'remote' && str_starts_with($_ivLoc, 'http')): ?>
                                    <a href="<?= esc($_ivLoc) ?>" target="_blank" rel="noopener" class="text-decoration-none">
                                        <?= esc($_ivLoc) ?> <i class="bi bi-box-arrow-up-right ms-1" style="font-size:.7rem;"></i>
                                    </a>
                                <?php else: ?>
                                    <?= esc($_ivLoc) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    <?php if ($_ivNotes): ?>
                    <hr style="border-color:#c7d2fe;margin:.75rem 0;">
                    <div style="font-size:.8rem;">
                        <span style="color:var(--muted);"><i class="bi bi-chat-left-text me-1"></i>Notes :</span>
                        <span class="ms-1"><?= esc($_ivNotes) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

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
