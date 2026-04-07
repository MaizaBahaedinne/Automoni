<?php $this->extend('layouts/main') ?>
<?php $this->section('content') ?>

<?php
// Build current query string helper (preserve filters when paginating / linking)
function nfl_qs(array $override = []): string {
    $params = array_filter([
        'from'   => $override['from']   ?? ($_GET['from']   ?? ''),
        'to'     => $override['to']     ?? ($_GET['to']     ?? ''),
        'search' => $override['search'] ?? ($_GET['search'] ?? ''),
        'page'   => $override['page']   ?? ($_GET['page']   ?? ''),
    ]);
    return $params ? '?' . http_build_query($params) : '';
}
?>

<style>
.nf-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius); }
.nf-stat { text-align:center; padding:1.25rem; }
.nf-stat .num { font-size:2rem; font-weight:900; line-height:1; color:var(--brand-dark); }
.nf-stat .lbl { font-size:.75rem; color:var(--muted); margin-top:4px; }
.nf-url-cell { max-width:420px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-family:monospace; font-size:.78rem; }
.nf-badge-anon { background:#f1f5f9; color:var(--muted); font-size:.7rem; padding:1px 7px; border-radius:20px; }
.nf-badge-auth { background:var(--brand-light); color:var(--brand-dark); font-size:.7rem; padding:1px 7px; border-radius:20px; }
.top-url-bar { height:8px; background:var(--brand); border-radius:4px; display:block; min-width:4px; }
.day-bar { width:100%; background:var(--brand-light); border-radius:3px 3px 0 0; position:relative; overflow:hidden; }
.day-bar-fill { background:var(--brand); border-radius:3px 3px 0 0; position:absolute; bottom:0; left:0; right:0; }
</style>

<!-- ── Header ──────────────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h2 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-signpost-split" style="color:var(--brand-dark);"></i>
            Rapport des erreurs 404
        </h2>
        <p class="text-muted mb-0" style="font-size:.85rem;">
            Pages introuvables rencontrées par vos utilisateurs
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/logs') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-file-earmark-text me-1"></i>Logs CI4
        </a>
        <?php if ($total > 0): ?>
        <form method="post" action="<?= base_url('admin/404-logs/clear') ?>"
              onsubmit="return confirm('Vider TOUS les logs 404 ? Action irréversible.')">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash3 me-1"></i>Tout effacer
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- ── Stats row ───────────────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="nf-card nf-stat">
            <div class="num"><?= number_format($total) ?></div>
            <div class="lbl"><i class="bi bi-collection me-1"></i>Total (filtrés)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <?php
        $todayCount = 0;
        foreach ($dailyCounts as $d) {
            if ($d->day === date('Y-m-d')) { $todayCount = (int)$d->hits; break; }
        }
        ?>
        <div class="nf-card nf-stat">
            <div class="num"><?= number_format($todayCount) ?></div>
            <div class="lbl"><i class="bi bi-calendar-day me-1"></i>Aujourd'hui</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <?php $uniqueUrls = count(array_unique(array_column($topUrls, 'url'))); ?>
        <div class="nf-card nf-stat">
            <div class="num"><?= number_format(count($topUrls)) ?></div>
            <div class="lbl"><i class="bi bi-link-45deg me-1"></i>URLs distinctes (top)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <?php
        $authHits = 0;
        foreach ($rows as $r) { if (!empty($r->user_id)) $authHits++; }
        ?>
        <div class="nf-card nf-stat">
            <div class="num" style="color:#f59e0b;"><?= count($rows) > 0 ? round($authHits / count($rows) * 100) : 0 ?>%</div>
            <div class="lbl"><i class="bi bi-person-check me-1"></i>Hits authentifiés (pgr actuelle)</div>
        </div>
    </div>
</div>

<!-- ── Sparkline + Top URLs ────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">

    <!-- Daily bar chart (30 days) -->
    <div class="col-12 col-lg-7">
        <div class="nf-card p-3" style="height:220px;">
            <div class="fw-semibold mb-2" style="font-size:.85rem;">
                <i class="bi bi-bar-chart me-1" style="color:var(--brand);"></i>
                Fréquence — 30 derniers jours
            </div>
            <?php if (count($dailyCounts) === 0): ?>
                <p class="text-muted text-center mt-4">Aucune donnée</p>
            <?php else:
                $maxHits = max(array_column($dailyCounts, 'hits'));
                // Index by day for fast lookup
                $byDay = [];
                foreach ($dailyCounts as $d) $byDay[$d->day] = (int)$d->hits;
                // Build 30-day range
                $days30 = [];
                for ($i = 29; $i >= 0; $i--) {
                    $days30[] = date('Y-m-d', strtotime("-{$i} days"));
                }
            ?>
            <div class="d-flex align-items-end gap-px mt-2" style="height:140px;gap:2px !important;">
                <?php foreach ($days30 as $day):
                    $h = $byDay[$day] ?? 0;
                    $pct = $maxHits > 0 ? round($h / $maxHits * 100) : 0;
                    $label = date('d/m', strtotime($day));
                    $isToday = $day === date('Y-m-d');
                ?>
                <div class="day-bar flex-fill" style="height:140px;"
                     title="<?= $label ?> : <?= $h ?> erreurs">
                    <div class="day-bar-fill" style="height:<?= $pct ?>%;<?= $isToday ? 'background:var(--brand-dark);' : '' ?>"></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="d-flex justify-content-between mt-1" style="font-size:.65rem;color:var(--muted);">
                <span><?= date('d/m', strtotime('-29 days')) ?></span>
                <span>Aujourd'hui</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top URLs -->
    <div class="col-12 col-lg-5">
        <div class="nf-card p-3" style="height:220px;overflow-y:auto;">
            <div class="fw-semibold mb-2" style="font-size:.85rem;">
                <i class="bi bi-fire me-1" style="color:#f59e0b;"></i>
                URLs les plus fréquentes
            </div>
            <?php if (empty($topUrls)): ?>
                <p class="text-muted text-center mt-4">Aucune donnée</p>
            <?php else:
                $maxTop = max(array_column($topUrls, 'hits'));
            ?>
            <?php foreach ($topUrls as $t): ?>
            <div class="mb-1">
                <div class="d-flex justify-content-between mb-1" style="font-size:.75rem;">
                    <span class="text-truncate" style="max-width:75%;font-family:monospace;color:var(--text);"
                          title="<?= esc($t->url) ?>"><?= esc($t->url) ?></span>
                    <span class="fw-bold" style="color:var(--brand-dark);"><?= $t->hits ?></span>
                </div>
                <span class="top-url-bar" style="width:<?= round($t->hits/$maxTop*100) ?>%;max-width:100%;display:block;margin-bottom:6px;"></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── Filters ─────────────────────────────────────────────────────────────── -->
<div class="nf-card p-3 mb-3">
    <form method="get" action="<?= base_url('admin/404-logs') ?>"
          class="row g-2 align-items-end">
        <div class="col-12 col-sm-auto">
            <label class="form-label mb-1" style="font-size:.78rem;font-weight:600;">Du</label>
            <input type="date" name="from" class="form-control form-control-sm"
                   value="<?= esc($from) ?>" max="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-12 col-sm-auto">
            <label class="form-label mb-1" style="font-size:.78rem;font-weight:600;">Au</label>
            <input type="date" name="to" class="form-control form-control-sm"
                   value="<?= esc($to) ?>" max="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-12 col-sm">
            <label class="form-label mb-1" style="font-size:.78rem;font-weight:600;">URL contient</label>
            <input type="search" name="search" class="form-control form-control-sm"
                   placeholder="ex: /jobs/999" value="<?= esc($search) ?>">
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-primary px-3">
                <i class="bi bi-funnel me-1"></i>Filtrer
            </button>
        </div>
        <?php if ($from || $to || $search): ?>
        <div class="col-auto">
            <a href="<?= base_url('admin/404-logs') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Réinitialiser
            </a>
        </div>
        <?php endif; ?>
    </form>
</div>

<!-- ── Log table ───────────────────────────────────────────────────────────── -->
<div class="nf-card mb-4">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <span style="font-size:.85rem;font-weight:600;">
            <i class="bi bi-table me-1" style="color:var(--brand);"></i>
            <?= number_format($total) ?> résultat<?= $total > 1 ? 's' : '' ?>
            <?php if ($from || $to || $search): ?>
            <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem;">Filtré</span>
            <?php endif; ?>
        </span>
        <span class="text-muted" style="font-size:.75rem;">Page <?= $page ?> / <?= max(1,$pages) ?></span>
    </div>

    <?php if (empty($rows)): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-check-circle" style="font-size:2.5rem;color:#22c55e;"></i>
        <p class="mt-2 mb-0">Aucune erreur 404 enregistrée pour ces critères.</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0" style="font-size:.8rem;">
            <thead style="background:var(--bg);">
                <tr>
                    <th class="ps-3" style="width:160px;">Date</th>
                    <th>URL demandée</th>
                    <th style="width:60px;">Méth.</th>
                    <th style="width:100px;">Utilisateur</th>
                    <th style="width:110px;">IP</th>
                    <th style="width:50px;"></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
            <tr>
                <td class="ps-3 text-muted" style="white-space:nowrap;">
                    <?= date('d/m/Y H:i', strtotime($row->created_at)) ?>
                </td>
                <td class="nf-url-cell" title="<?= esc($row->url) ?>">
                    <?= esc($row->url) ?>
                </td>
                <td>
                    <span class="badge" style="background:<?= $row->method === 'GET' ? 'var(--brand-light);color:var(--brand-dark)' : '#fef9c3;color:#92400e' ?>;">
                        <?= esc($row->method) ?>
                    </span>
                </td>
                <td>
                    <?php if (!empty($row->user_id)): ?>
                    <span class="nf-badge-auth">
                        <i class="bi bi-person-fill me-1"></i>#<?= (int)$row->user_id ?>
                    </span>
                    <?php else: ?>
                    <span class="nf-badge-anon">Anonyme</span>
                    <?php endif; ?>
                </td>
                <td class="text-muted font-monospace" style="font-size:.72rem;">
                    <?= esc($row->ip) ?>
                </td>
                <td>
                    <form method="post" action="<?= base_url('admin/404-logs/delete/' . (int)$row->id) ?>"
                          onsubmit="return confirm('Supprimer cette entrée ?')">
                        <?= csrf_field() ?>
                        <button class="btn btn-link btn-sm p-0 text-danger" title="Supprimer">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php if (!empty($row->referer) || !empty($row->user_agent)): ?>
            <tr style="background:var(--bg);">
                <td colspan="6" class="ps-4 pb-2 pe-3" style="font-size:.72rem;color:var(--muted);">
                    <?php if (!empty($row->referer)): ?>
                    <i class="bi bi-link me-1"></i>Referer: <span class="font-monospace"><?= esc(mb_substr($row->referer, 0, 120)) ?></span><br>
                    <?php endif; ?>
                    <?php if (!empty($row->user_agent)): ?>
                    <i class="bi bi-laptop me-1"></i><?= esc(mb_substr($row->user_agent, 0, 120)) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <div class="d-flex justify-content-center gap-1 p-3 flex-wrap">
        <?php if ($page > 1): ?>
        <a href="<?= base_url('admin/404-logs') . nfl_qs(['page' => $page - 1]) ?>"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-chevron-left"></i>
        </a>
        <?php endif; ?>

        <?php
        $pStart = max(1, $page - 3);
        $pEnd   = min($pages, $page + 3);
        for ($p = $pStart; $p <= $pEnd; $p++):
        ?>
        <a href="<?= base_url('admin/404-logs') . nfl_qs(['page' => $p]) ?>"
           class="btn btn-sm <?= $p === $page ? 'btn-primary' : 'btn-outline-secondary' ?>">
            <?= $p ?>
        </a>
        <?php endfor; ?>

        <?php if ($page < $pages): ?>
        <a href="<?= base_url('admin/404-logs') . nfl_qs(['page' => $page + 1]) ?>"
           class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php $this->endSection() ?>
