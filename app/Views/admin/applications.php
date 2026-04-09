<?php $this->extend('layouts/main') ?>
<?php $this->section('content') ?>

<?php
// Status config
$statusCfg = [
    'pending'   => ['label' => 'En attente',   'color' => '#f59e0b', 'bg' => '#fef3c7', 'icon' => 'bi-hourglass-split'],
    'reviewing' => ['label' => 'En examen',    'color' => '#6366f1', 'bg' => '#eef2ff', 'icon' => 'bi-eye'],
    'accepted'  => ['label' => 'Acceptée',     'color' => '#22c55e', 'bg' => '#f0fdf4', 'icon' => 'bi-check-circle-fill'],
    'rejected'  => ['label' => 'Refusée',      'color' => '#ef4444', 'bg' => '#fef2f2', 'icon' => 'bi-x-circle-fill'],
];

// Current filter query string helper (preserves filters across pagination / status links)
function aa_qs(array $override = []): string {
    global $status, $search, $from, $to;
    $params = array_filter([
        'status' => $override['status'] ?? $status ?? '',
        'search' => $override['search'] ?? $search ?? '',
        'from'   => $override['from']   ?? $from   ?? '',
        'to'     => $override['to']     ?? $to     ?? '',
        'page'   => $override['page']   ?? ($_GET['page'] ?? ''),
    ], fn($v) => $v !== '' && $v !== null);
    return $params ? '?' . http_build_query($params) : '';
}

$total = $counts['total'];
?>

<style>
.aa-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius); }
.aa-stat { text-align:center; padding:1rem .75rem; }
.aa-stat .num { font-size:1.7rem; font-weight:900; line-height:1; }
.aa-stat .lbl { font-size:.72rem; color:var(--muted); margin-top:3px; }
.aa-pill {
    display:inline-flex; align-items:center; gap:5px;
    font-size:.72rem; font-weight:600; padding:3px 9px;
    border-radius:20px; white-space:nowrap;
}
.aa-tab { padding:6px 14px; border-radius:8px; font-size:.8rem; font-weight:600;
          color:var(--muted); text-decoration:none; border:1px solid transparent;
          transition:all .15s; }
.aa-tab:hover { background:var(--brand-light); color:var(--brand-dark); }
.aa-tab.active { background:var(--brand-dark); color:#fff; border-color:var(--brand-dark); }
</style>

<!-- ── Header ──────────────────────────────────────────────────────────────── -->
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h2 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-briefcase-fill" style="color:var(--brand-dark);"></i>
            Gestion des candidatures
        </h2>
        <p class="text-muted mb-0" style="font-size:.85rem;">
            Toutes les candidatures déposées sur la plateforme
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/logs') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-file-earmark-text me-1"></i>Logs CI4
        </a>
        <a href="<?= base_url('admin/404-logs') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-signpost-split me-1"></i>Erreurs 404
        </a>
        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#purgeFModal">
            <i class="bi bi-trash3-fill me-1"></i>Purger les candidatures
        </button>
        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#purgeInterviewsFModal">
            <i class="bi bi-calendar-x me-1"></i>Purger les entretiens
        </button>
    </div>
</div>

<!-- ── Purge interviews modal ─────────────────────────────────────────────── -->
<div class="modal fade" id="purgeInterviewsFModal" tabindex="-1" aria-labelledby="purgeIntLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-warning fw-bold" id="purgeIntLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Purger tous les entretiens
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3">
                    <strong>⚠ Action irréversible</strong> — cette opération supprime <strong>tous</strong> les entretiens planifiés, terminés et annulés.
                </div>
                <p class="text-muted" style="font-size:.9rem;">
                    Les candidatures et offres d'emploi ne seront pas affectées.
                </p>
                <form action="<?= base_url('admin/interviews/purge') ?>" method="post" id="purgeIntForm">
                    <?= csrf_field() ?>
                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                        Tapez <code>PURGE</code> pour confirmer :
                    </label>
                    <input type="text" id="purgeIntConfirmInput" class="form-control form-control-sm mb-3"
                           placeholder="PURGE" autocomplete="off">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" id="purgeIntSubmitBtn" class="btn btn-sm btn-warning" disabled>
                            <i class="bi bi-calendar-x me-1"></i>Confirmer la purge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ── Purge confirmation modal ──────────────────────────────────────────── -->
<div class="modal fade" id="purgeFModal" tabindex="-1" aria-labelledby="purgeFLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger fw-bold" id="purgeFLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Purger toutes les candidatures
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger mb-3">
                    <strong>⚠ Action irréversible</strong> — cette opération supprime <strong>toutes</strong> les candidatures de la base de données.
                </div>
                <p class="text-muted" style="font-size:.9rem;">
                    Utilisez cette fonction <strong>uniquement pour les tests</strong>.
                    Les offres d'emploi et les profils utilisateurs ne seront pas affectés.
                </p>
                <form action="<?= base_url('admin/applications/purge') ?>" method="post" id="purgeForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="confirm_token" value="PURGE_CONFIRMED">
                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                        Tapez <code>PURGE</code> pour confirmer :
                    </label>
                    <input type="text" id="purgeConfirmInput" class="form-control form-control-sm mb-3"
                           placeholder="PURGE" autocomplete="off">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" id="purgeSubmitBtn" class="btn btn-sm btn-danger" disabled>
                            <i class="bi bi-trash3-fill me-1"></i>Confirmer la purge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('purgeConfirmInput').addEventListener('input', function () {
    document.getElementById('purgeSubmitBtn').disabled = this.value.trim() !== 'PURGE';
});
document.getElementById('purgeIntConfirmInput').addEventListener('input', function () {
    document.getElementById('purgeIntSubmitBtn').disabled = this.value.trim() !== 'PURGE';
});
</script>

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4" style="display:none"><!-- layout spacer placeholder -->
</div>

<!-- ── Stats strip ─────────────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <?php
    $statItems = [
        ['key'=>'total',     'label'=>'Total',       'color'=>'var(--brand-dark)', 'icon'=>'bi-collection'],
        ['key'=>'pending',   'label'=>'En attente',  'color'=>'#f59e0b',           'icon'=>'bi-hourglass-split'],
        ['key'=>'reviewing', 'label'=>'En examen',   'color'=>'#6366f1',           'icon'=>'bi-eye'],
        ['key'=>'accepted',  'label'=>'Acceptées',   'color'=>'#22c55e',           'icon'=>'bi-check-circle'],
        ['key'=>'rejected',  'label'=>'Refusées',    'color'=>'#ef4444',           'icon'=>'bi-x-circle'],
    ];
    foreach ($statItems as $si):
        $n = $counts[$si['key']] ?? 0;
    ?>
    <div class="col-6 col-sm-4 col-lg">
        <div class="aa-card aa-stat">
            <div class="num" style="color:<?= $si['color'] ?>"><?= number_format($n) ?></div>
            <div class="lbl"><i class="bi <?= $si['icon'] ?> me-1"></i><?= $si['label'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Status tabs ─────────────────────────────────────────────────────────── -->
<div class="d-flex flex-wrap gap-2 mb-3">
    <?php
    $tabs = ['all' => 'Toutes'] + array_map(fn($c) => $c['label'], $statusCfg);
    foreach ($tabs as $val => $label):
        $isActive = ($status === $val) || ($val === 'all' && ($status === '' || $status === 'all'));
    ?>
    <a href="<?= base_url('admin/applications') . aa_qs(['status' => $val, 'page' => '']) ?>"
       class="aa-tab <?= $isActive ? 'active' : '' ?>">
        <?= $label ?>
        <?php if ($val !== 'all' && isset($counts[$val])): ?>
        <span class="ms-1 badge rounded-pill" style="background:<?= $statusCfg[$val]['color'] ?>;font-size:.65rem;">
            <?= $counts[$val] ?>
        </span>
        <?php endif; ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- ── Filters ─────────────────────────────────────────────────────────────── -->
<div class="aa-card p-3 mb-3">
    <form method="get" action="<?= base_url('admin/applications') ?>"
          class="row g-2 align-items-end">
        <input type="hidden" name="status" value="<?= esc($status) ?>">
        <div class="col-12 col-md">
            <input type="search" name="search" class="form-control form-control-sm"
                   placeholder="Nom, email, poste, entreprise…"
                   value="<?= esc($search) ?>">
        </div>
        <div class="col-12 col-sm-auto">
            <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;">Du</label>
            <input type="date" name="from" class="form-control form-control-sm"
                   value="<?= esc($from) ?>" max="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-12 col-sm-auto">
            <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;">Au</label>
            <input type="date" name="to" class="form-control form-control-sm"
                   value="<?= esc($to) ?>" max="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-sm btn-primary px-3">
                <i class="bi bi-funnel me-1"></i>Filtrer
            </button>
            <?php if ($search !== '' || $from || $to): ?>
            <a href="<?= base_url('admin/applications') . aa_qs(['search'=>'','from'=>'','to'=>'','page'=>'']) ?>"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle"></i>
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ── Applications table ──────────────────────────────────────────────────── -->
<div class="aa-card mb-4">
    <div class="p-3 border-bottom d-flex align-items-center gap-2">
        <i class="bi bi-table" style="color:var(--brand);"></i>
        <span class="fw-semibold" style="font-size:.85rem;">
            <?= number_format($total) ?> candidature<?= $total > 1 ? 's' : '' ?> au total
            <?php if ($search !== '' || ($status !== 'all' && $status !== '')): ?>
            <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem;">Filtré</span>
            <?php endif; ?>
        </span>
    </div>

    <?php if (empty($apps)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox" style="font-size:2.5rem;color:var(--muted);opacity:.5;"></i>
        <p class="mt-2 mb-0">Aucune candidature trouvée.</p>
    </div>
    <?php else: ?>

    <!-- Desktop table -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover table-sm mb-0" style="font-size:.82rem;">
            <thead style="background:var(--bg);">
                <tr>
                    <th class="ps-3" style="width:44px;">#</th>
                    <th>Candidat</th>
                    <th>Poste / Entreprise</th>
                    <th style="width:110px;">Date</th>
                    <th style="width:130px;">Statut</th>
                    <th style="width:180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($apps as $app):
                $cfg = $statusCfg[$app->status] ?? $statusCfg['pending'];
            ?>
            <tr>
                <td class="ps-3 text-muted"><?= (int)$app->id ?></td>

                <!-- Candidat -->
                <td>
                    <div class="fw-semibold"><?= esc($app->first_name . ' ' . $app->last_name) ?></div>
                    <div class="text-muted" style="font-size:.73rem;"><?= esc($app->email) ?></div>
                </td>

                <!-- Job / Company -->
                <td>
                    <div class="fw-semibold text-truncate" style="max-width:220px;">
                        <?= esc($app->job_title) ?>
                    </div>
                    <div class="text-muted" style="font-size:.73rem;"><?= esc($app->company_name) ?></div>
                </td>

                <!-- Date -->
                <td class="text-muted" style="white-space:nowrap;">
                    <?= date('d/m/Y', strtotime($app->applied_at)) ?><br>
                    <span style="font-size:.7rem;"><?= date('H:i', strtotime($app->applied_at)) ?></span>
                </td>

                <!-- Status badge -->
                <td>
                    <span class="aa-pill" style="background:<?= $cfg['bg'] ?>;color:<?= $cfg['color'] ?>;">
                        <i class="bi <?= $cfg['icon'] ?>"></i>
                        <?= $cfg['label'] ?>
                    </span>
                </td>

                <!-- Actions -->
                <td>
                    <div class="d-flex gap-1 flex-wrap">
                        <!-- Status quick-change -->
                        <div class="dropdown">
                            <button class="btn btn-xs btn-outline-secondary dropdown-toggle"
                                    style="font-size:.73rem;padding:2px 8px;"
                                    data-bs-toggle="dropdown">
                                Changer
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width:160px;">
                                <?php foreach ($statusCfg as $sKey => $sCfg): ?>
                                <?php if ($sKey === $app->status) continue; ?>
                                <li>
                                    <form method="post"
                                          action="<?= base_url('admin/applications/' . (int)$app->id . '/status') ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="status" value="<?= esc($sKey) ?>">
                                        <button class="dropdown-item d-flex align-items-center gap-2"
                                                style="font-size:.8rem;">
                                            <i class="bi <?= $sCfg['icon'] ?>"
                                               style="color:<?= $sCfg['color'] ?>;width:16px;"></i>
                                            <?= $sCfg['label'] ?>
                                        </button>
                                    </form>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- CV download if available -->
                        <?php if (!empty($app->cv_file)): ?>
                        <a href="<?= base_url('uploads/applications/' . esc($app->cv_file)) ?>"
                           class="btn btn-xs btn-outline-primary"
                           style="font-size:.73rem;padding:2px 8px;"
                           target="_blank" title="Télécharger le CV">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                        </a>
                        <?php endif; ?>

                        <!-- View cover letter -->
                        <?php if (!empty($app->cover_letter)): ?>
                        <button class="btn btn-xs btn-outline-secondary"
                                style="font-size:.73rem;padding:2px 8px;"
                                data-bs-toggle="modal"
                                data-bs-target="#clModal<?= (int)$app->id ?>"
                                title="Lettre de motivation">
                            <i class="bi bi-envelope-open"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>

            <!-- Cover letter modal -->
            <?php if (!empty($app->cover_letter)): ?>
            <div class="modal fade" id="clModal<?= (int)$app->id ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold">
                                Lettre de motivation — <?= esc($app->first_name . ' ' . $app->last_name) ?>
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" style="white-space:pre-wrap;font-size:.85rem;">
                            <?= esc($app->cover_letter) ?>
                        </div>
                        <div class="modal-footer">
                            <!-- Quick status change from modal -->
                            <form method="post"
                                  action="<?= base_url('admin/applications/' . (int)$app->id . '/status') ?>"
                                  class="d-flex gap-2 flex-wrap">
                                <?= csrf_field() ?>
                                <select name="status" class="form-select form-select-sm" style="width:auto;">
                                    <?php foreach ($statusCfg as $sKey => $sCfg): ?>
                                    <option value="<?= $sKey ?>" <?= $app->status === $sKey ? 'selected' : '' ?>>
                                        <?= $sCfg['label'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-primary">Enregistrer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile cards -->
    <div class="d-md-none p-3">
        <?php foreach ($apps as $app):
            $cfg = $statusCfg[$app->status] ?? $statusCfg['pending'];
        ?>
        <div class="aa-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div>
                    <div class="fw-bold" style="font-size:.9rem;">
                        <?= esc($app->first_name . ' ' . $app->last_name) ?>
                    </div>
                    <div class="text-muted" style="font-size:.75rem;"><?= esc($app->email) ?></div>
                </div>
                <span class="aa-pill" style="background:<?= $cfg['bg'] ?>;color:<?= $cfg['color'] ?>;">
                    <i class="bi <?= $cfg['icon'] ?>"></i><?= $cfg['label'] ?>
                </span>
            </div>
            <div class="fw-semibold" style="font-size:.82rem;"><?= esc($app->job_title) ?></div>
            <div class="text-muted mb-2" style="font-size:.75rem;"><?= esc($app->company_name) ?></div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="text-muted" style="font-size:.72rem;">
                    <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($app->applied_at)) ?>
                </span>
                <!-- Status change form (mobile) -->
                <form method="post"
                      action="<?= base_url('admin/applications/' . (int)$app->id . '/status') ?>"
                      class="d-flex gap-1 ms-auto">
                    <?= csrf_field() ?>
                    <select name="status" class="form-select form-select-sm" style="font-size:.75rem;width:auto;">
                        <?php foreach ($statusCfg as $sKey => $sCfg): ?>
                        <option value="<?= $sKey ?>" <?= $app->status === $sKey ? 'selected' : '' ?>>
                            <?= $sCfg['label'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-primary" style="font-size:.75rem;padding:3px 10px;">
                        <i class="bi bi-check2"></i>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pager): ?>
    <div class="d-flex justify-content-center p-3 flex-wrap gap-1">
        <?= $pager->links('admin_apps', 'default_full') ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

<?php $this->endSection() ?>
