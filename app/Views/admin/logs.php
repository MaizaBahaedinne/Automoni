<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.log-entry          { border-left: 3px solid var(--border); padding: .6rem .9rem .6rem 1rem; margin-bottom: .5rem; border-radius: 0 8px 8px 0; background:#fff; }
.log-entry.CRITICAL { border-color: #7f1d1d; background: #fff5f5; }
.log-entry.ERROR    { border-color: #dc2626; background: #fff8f8; }
.log-entry.WARNING  { border-color: #d97706; background: #fffbf0; }
.log-entry.INFO     { border-color: #2563eb; background: #f0f7ff; }
.log-entry.DEBUG    { border-color: #6b7280; background: #f9fafb; }
.log-badge          { display:inline-block; font-size:.68rem; font-weight:700; padding:.2em .6em; border-radius:4px; letter-spacing:.05em; }
.badge-CRITICAL     { background:#7f1d1d; color:#fff; }
.badge-ERROR        { background:#dc2626; color:#fff; }
.badge-WARNING      { background:#d97706; color:#fff; }
.badge-INFO         { background:#2563eb; color:#fff; }
.badge-DEBUG        { background:#6b7280; color:#fff; }
.log-msg            { font-family: 'Courier New', monospace; font-size: .82rem; color: var(--text); word-break: break-word; margin:.2rem 0 0; }
.log-trace          { font-family: 'Courier New', monospace; font-size: .74rem; color: #64748b; white-space: pre-wrap; word-break: break-word; margin-top:.4rem; max-height: 200px; overflow-y: auto; }
.log-dt             { font-size: .74rem; color: var(--muted); }
.level-btn          { border-radius: 20px; font-size: .78rem; font-weight: 600; padding: .3rem .85rem; border: 1px solid var(--border); background: #fff; color: var(--text); cursor: pointer; text-decoration: none; transition: .12s; }
.level-btn:hover, .level-btn.active { color:#fff; }
.level-btn.active-CRITICAL { background:#7f1d1d; border-color:#7f1d1d; }
.level-btn.active-ERROR    { background:#dc2626; border-color:#dc2626; }
.level-btn.active-WARNING  { background:#d97706; border-color:#d97706; }
.level-btn.active-INFO     { background:#2563eb; border-color:#2563eb; }
.level-btn.active-DEBUG    { background:#6b7280; border-color:#6b7280; }
.level-btn.active-ALL      { background:var(--brand); border-color:var(--brand); }
.trace-toggle       { font-size:.72rem; color:var(--brand); cursor:pointer; user-select:none; }
.trace-toggle:hover { text-decoration:underline; }
</style>

<div class="container-fluid py-4" style="max-width:1100px;">

    <!-- Header -->
    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <i class="bi bi-journal-text fs-2" style="color:var(--brand);"></i>
        <div class="flex-grow-1">
            <h4 class="mb-0 fw-bold" style="color:var(--text);">Journal d'erreurs</h4>
            <small style="color:var(--muted);">Logs CI4 — <?= esc($date) ?> — <?= $totalEntries ?> entrée<?= $totalEntries > 1 ? 's' : '' ?> au total</small>
        </div>
        <a href="<?= base_url('admin/deploy') ?>" class="btn btn-sm" style="border-radius:20px; border:1px solid var(--border); font-size:.8rem;">
            <i class="bi bi-arrow-down-circle me-1"></i>Déploiement
        </a>
    </div>

    <!-- Controls bar -->
    <div style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:1rem 1.25rem; margin-bottom:1rem;">
        <form method="get" action="" class="d-flex flex-wrap gap-3 align-items-center">
            <!-- Date picker -->
            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0 fw-semibold" style="font-size:.82rem; white-space:nowrap;">
                    <i class="bi bi-calendar3 me-1"></i>Date
                </label>
                <select name="date" class="form-select form-select-sm" style="border-radius:20px; font-size:.82rem; width:auto;" onchange="this.form.submit()">
                    <?php foreach ($availableDates as $d): ?>
                        <option value="<?= $d ?>" <?= $d === $date ? 'selected' : '' ?>><?= $d ?></option>
                    <?php endforeach; ?>
                    <?php if (!in_array($date, $availableDates, true)): ?>
                        <option value="<?= esc($date) ?>" selected><?= esc($date) ?> (aucun fichier)</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Level filter -->
            <input type="hidden" name="level" id="levelInput" value="<?= esc($activeLevel) ?>">
            <div class="d-flex flex-wrap gap-1 align-items-center">
                <span style="font-size:.82rem; font-weight:600; color:var(--muted);">Niveau :</span>
                <a href="?date=<?= $date ?>&level=" class="level-btn <?= $activeLevel === '' ? 'active active-ALL' : '' ?>">
                    Tous <span class="ms-1" style="opacity:.75;"><?= $totalEntries ?></span>
                </a>
                <?php foreach ($levels as $lvl): ?>
                    <a href="?date=<?= $date ?>&level=<?= $lvl ?>"
                       class="level-btn <?= $activeLevel === $lvl ? 'active active-' . $lvl : '' ?>">
                        <?= $lvl ?>
                        <?php if (($counts[$lvl] ?? 0) > 0): ?>
                            <span class="ms-1" style="opacity:.75;"><?= $counts[$lvl] ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </form>
    </div>

    <!-- Stats strip -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?php foreach ($levels as $lvl): ?>
            <?php if (($counts[$lvl] ?? 0) > 0): ?>
            <div class="d-flex align-items-center gap-1 px-3 py-1" style="background:#fff; border:1px solid var(--border); border-radius:20px; font-size:.8rem;">
                <span class="log-badge badge-<?= $lvl ?>"><?= $lvl ?></span>
                <span class="fw-semibold" style="color:var(--text);"><?= $counts[$lvl] ?></span>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Log entries -->
    <?php if (empty($entries)): ?>
        <div style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:2.5rem; text-align:center;">
            <i class="bi bi-check2-circle fs-1" style="color:#22c55e; display:block; margin-bottom:12px;"></i>
            <p class="fw-semibold mb-1" style="color:var(--text);">Aucune entrée<?= $activeLevel ? ' de niveau ' . esc($activeLevel) : '' ?></p>
            <p style="color:var(--muted); font-size:.875rem; margin:0;">Le fichier de log est vide ou aucun fichier n'existe pour cette date.</p>
        </div>
    <?php else: ?>
        <div id="logContainer">
            <?php foreach ($entries as $i => $entry): ?>
            <div class="log-entry <?= esc($entry['level']) ?>">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="log-badge badge-<?= esc($entry['level']) ?>"><?= esc($entry['level']) ?></span>
                    <span class="log-dt"><i class="bi bi-clock me-1"></i><?= esc($entry['datetime']) ?></span>
                    <?php if (!empty($entry['trace'])): ?>
                        <span class="trace-toggle ms-auto" onclick="toggleTrace(<?= $i ?>)">
                            <i class="bi bi-code-slash me-1"></i>Stack trace
                        </span>
                    <?php endif; ?>
                </div>
                <div class="log-msg"><?= esc($entry['message']) ?></div>
                <?php if (!empty($entry['trace'])): ?>
                    <div class="log-trace" id="trace-<?= $i ?>" style="display:none;"><?= esc($entry['trace']) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($entries) >= 200): ?>
        <p class="text-center mt-3" style="color:var(--muted); font-size:.82rem;">
            <i class="bi bi-info-circle me-1"></i>Affichage limité aux 200 entrées les plus récentes.
        </p>
        <?php endif; ?>
    <?php endif; ?>

</div>

<script>
function toggleTrace(id) {
    var el = document.getElementById('trace-' + id);
    if (!el) return;
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

// Expand all CRITICAL + ERROR traces by default
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.log-entry.CRITICAL .log-trace, .log-entry.ERROR .log-trace').forEach(function(el) {
        el.style.display = 'block';
    });
});
</script>

<?= $this->endSection() ?>
