<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-4" style="max-width:800px;">

    <div class="mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-arrow-down-circle-fill fs-3" style="color:var(--brand);"></i>
        <div class="flex-grow-1">
            <h4 class="mb-0 fw-bold" style="color:var(--text);">Déploiement — Git Pull</h4>
            <small style="color:var(--muted);">Disponible uniquement pour les administrateurs.</small>
        </div>
        <a href="<?= base_url('admin/logs') ?>" class="btn btn-sm" style="border-radius:20px; border:1px solid var(--border); font-size:.8rem;">
            <i class="bi bi-journal-text me-1"></i>Voir les logs
        </a>
    </div>

    <?php if (isset($ran)): ?>

    <?php /* ── Git pull result ─────────────────────────────────────────── */ ?>
    <div class="mb-3" style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:1.25rem;">
        <div class="d-flex align-items-center gap-2 mb-3">
            <?php if ($pullExit === 0): ?>
                <span class="badge" style="background:#22c55e; font-size:.85rem; padding:.5em .85em;">
                    <i class="bi bi-check-circle me-1"></i>git pull — Succès
                </span>
            <?php else: ?>
                <span class="badge bg-danger" style="font-size:.85rem; padding:.5em .85em;">
                    <i class="bi bi-x-circle me-1"></i>git pull — Échec (exit <?= (int)$pullExit ?>)
                </span>
            <?php endif; ?>
            <small style="color:var(--muted);"><?= date('d/m/Y H:i:s') ?></small>
        </div>
        <pre style="background:#0f172a;color:#e2e8f0;border-radius:8px;padding:1rem;font-size:.8rem;line-height:1.6;white-space:pre-wrap;word-break:break-word;margin:0;max-height:300px;overflow-y:auto;"><?= htmlspecialchars($pullOutput ?? '') ?></pre>

        <?php if (!empty($permissionError)): ?>
        <div class="mt-3 p-3" style="background:#fef9c3; border:1px solid #fde68a; border-radius:8px;">
            <p class="mb-2 fw-semibold" style="color:#92400e;">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Cause : le processus PHP n'a pas les droits d'écriture sur <code>.git/</code>.
            </p>
            <p class="mb-1" style="color:#78350f; font-size:.9rem;">Connectez-vous en <strong>SSH</strong> et exécutez <strong>une seule fois</strong> :</p>
            <pre style="background:#1e1b4b;color:#c7d2fe;border-radius:6px;padding:.75rem 1rem;font-size:.8rem;margin:0 0 .5rem;">sudo chown -R $(whoami):$(whoami) <?= htmlspecialchars($projectRoot ?? '/home/persomy.com/public_html') ?>/.git</pre>
            <p class="mb-0" style="color:#78350f; font-size:.85rem;">Relancez ensuite le git pull depuis cette page.</p>
        </div>
        <?php endif; ?>
    </div>

    <?php /* ── Migration result (shown only when pull succeeded) ──────── */ ?>
    <?php if ($migrateExit !== null): ?>
    <div class="mb-3" style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:1.25rem;">
        <div class="d-flex align-items-center gap-2 mb-3">
            <?php if ($migrateExit === 0): ?>
                <span class="badge" style="background:#22c55e; font-size:.85rem; padding:.5em .85em;">
                    <i class="bi bi-database-check me-1"></i>spark migrate — Succès
                </span>
            <?php else: ?>
                <span class="badge bg-danger" style="font-size:.85rem; padding:.5em .85em;">
                    <i class="bi bi-database-x me-1"></i>spark migrate — Échec (exit <?= (int)$migrateExit ?>)
                </span>
            <?php endif; ?>
        </div>
        <pre style="background:#0f172a;color:#e2e8f0;border-radius:8px;padding:1rem;font-size:.8rem;line-height:1.6;white-space:pre-wrap;word-break:break-word;margin:0;max-height:200px;overflow-y:auto;"><?= htmlspecialchars($migrateOutput ?? '') ?></pre>
    </div>
    <?php endif; ?>

    <?php endif; /* end $ran */ ?>

    <?php /* ── Action form ──────────────────────────────────────────────── */ ?>
    <div style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:1.5rem; margin-bottom:1rem;">
        <p style="color:var(--text); margin-bottom:1.25rem;">
            Exécute <code>git pull</code> puis <code>php spark migrate</code> dans le répertoire racine du projet.
        </p>
        <form method="post" action="<?= base_url('admin/deploy/pull') ?>" id="deployForm">
            <?= csrf_field() ?>
            <button type="submit" class="btn px-4" id="pullBtn"
                style="background:var(--brand); color:#fff; border:none; border-radius:8px; font-weight:600; padding:.65rem 1.5rem;">
                <i class="bi bi-arrow-down-circle me-1"></i>
                Lancer git pull + migrate
            </button>
        </form>
    </div>

    <?php /* ── Error log tail ──────────────────────────────────────────── */ ?>
    <?php if (!empty($logTail)): ?>
    <div style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:1.25rem;">
        <h6 class="fw-semibold mb-2" style="color:var(--text);">
            <i class="bi bi-journal-text me-1" style="color:var(--brand);"></i>
            Journal d'erreurs (aujourd'hui — 60 dernières lignes)
        </h6>
        <pre style="background:#0f172a;color:#fca5a5;border-radius:8px;padding:1rem;font-size:.75rem;line-height:1.5;white-space:pre-wrap;word-break:break-word;margin:0;max-height:350px;overflow-y:auto;"><?= htmlspecialchars($logTail) ?></pre>
    </div>
    <?php else: ?>
    <div style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:1.25rem;">
        <p class="mb-0" style="color:var(--muted); font-size:.875rem;">
            <i class="bi bi-check2-circle me-1" style="color:#22c55e;"></i>
            Aucune entrée dans le journal d'erreurs d'aujourd'hui.
        </p>
    </div>
    <?php endif; ?>

</div>

<script>
document.getElementById('deployForm').addEventListener('submit', function () {
    var btn = document.getElementById('pullBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>En cours…';
});
</script>

<?= $this->endSection() ?>

