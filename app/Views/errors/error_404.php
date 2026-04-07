<?php $this->extend('layouts/main') ?>
<?php $this->section('content') ?>

<div class="row justify-content-center" style="min-height:55vh;align-items:center;">
    <div class="col-12 col-md-7 col-lg-5 text-center py-5">

        <!-- Illustration -->
        <div style="font-size:7rem;line-height:1;margin-bottom:.5rem;
                    background:linear-gradient(135deg,var(--brand-dark),#7c3aed);
                    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
                    background-clip:text;font-weight:900;letter-spacing:-.04em;">
            404
        </div>

        <div style="font-size:3rem;margin-bottom:1rem;">
            <i class="bi bi-compass" style="color:var(--muted);opacity:.5;"></i>
        </div>

        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text);margin-bottom:.75rem;">
            Page introuvable
        </h1>
        <p class="text-muted mb-1" style="font-size:.95rem;">
            La page que vous cherchez n'existe pas ou a été déplacée.
        </p>
        <?php if (!empty($requestedUrl)): ?>
        <p class="mb-4" style="font-size:.78rem;color:var(--muted);">
            <code style="background:var(--bg);padding:2px 8px;border-radius:6px;word-break:break-all;">
                <?= $requestedUrl ?>
            </code>
        </p>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="<?= base_url('/') ?>" class="btn btn-primary px-4">
                <i class="bi bi-house-fill me-2"></i>Retour à l'accueil
            </a>
            <a href="<?= base_url('jobs') ?>" class="btn btn-outline-primary px-4">
                <i class="bi bi-briefcase me-2"></i>Voir les offres
            </a>
            <?php if (session()->get('logged_in')): ?>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary px-4">
                <i class="bi bi-grid me-2"></i>Mon tableau de bord
            </a>
            <?php endif; ?>
        </div>

        <!-- Quick search shortcut -->
        <div class="mt-5" style="background:#fff;border:1px solid var(--border);
                                  border-radius:var(--radius);padding:1.25rem;">
            <p class="text-muted mb-2" style="font-size:.82rem;">
                Vous cherchez un emploi ou un profil ?
            </p>
            <form action="<?= base_url('jobs') ?>" method="get" class="d-flex gap-2">
                <input type="search" name="keyword" placeholder="Mot-clé, intitulé de poste…"
                       class="form-control form-control-sm"
                       style="border-radius:8px;flex:1;">
                <button class="btn btn-sm btn-primary px-3" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>

    </div>
</div>

<?php $this->endSection() ?>
