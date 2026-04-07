<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-4" style="max-width:760px;">

    <div class="mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-arrow-down-circle-fill fs-3" style="color:var(--brand);"></i>
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--text);">Déploiement — Git Pull</h4>
            <small style="color:var(--muted);">Disponible uniquement pour les administrateurs.</small>
        </div>
    </div>

    <?php if (isset($ran)): ?>
    <div class="mb-4" style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:1.25rem;">
        <div class="d-flex align-items-center gap-2 mb-3">
            <?php if ($exitCode === 0): ?>
                <span class="badge" style="background:#22c55e; font-size:.85rem; padding:.5em .85em;">
                    <i class="bi bi-check-circle me-1"></i>Succès (exit 0)
                </span>
            <?php else: ?>
                <span class="badge bg-danger" style="font-size:.85rem; padding:.5em .85em;">
                    <i class="bi bi-x-circle me-1"></i>Échec (exit <?= $exitCode ?>)
                </span>
            <?php endif; ?>
            <small style="color:var(--muted);"><?= date('d/m/Y H:i:s') ?></small>
        </div>
        <pre style="
            background:#0f172a;
            color:#e2e8f0;
            border-radius:8px;
            padding:1rem;
            font-size:.8rem;
            line-height:1.6;
            white-space:pre-wrap;
            word-break:break-word;
            margin:0;
            max-height:400px;
            overflow-y:auto;
        "><?= htmlspecialchars($output ?? '') ?></pre>
    </div>
    <?php endif; ?>

    <div style="background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:1.5rem;">
        <p style="color:var(--text); margin-bottom:1.25rem;">
            Exécute <code>git pull</code> dans le répertoire racine du projet sur le serveur.
            Assurez-vous d'avoir committé et pushé vos modifications avant de lancer cette action.
        </p>

        <form method="post" action="<?= base_url('admin/deploy/pull') ?>" id="deployForm">
            <?= csrf_field() ?>
            <button type="submit" class="btn px-4" id="pullBtn"
                style="background:var(--brand); color:#fff; border:none; border-radius:8px; font-weight:600; padding:.65rem 1.5rem;">
                <i class="bi bi-arrow-down-circle me-1"></i>
                Lancer git pull
            </button>
        </form>
    </div>

</div>

<script>
document.getElementById('deployForm').addEventListener('submit', function () {
    var btn = document.getElementById('pullBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>En cours…';
});
</script>

<?= $this->endSection() ?>
