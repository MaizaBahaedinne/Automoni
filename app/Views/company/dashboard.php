<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.dash-card {
    background:#fff;
    border:1px solid var(--border);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
}
.dash-section-title {
    font-size:.7rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.08em;
    color:var(--muted);
    margin-bottom:.75rem;
}
/* Org cards */
.org-card {
    border:1px solid var(--border);
    border-radius:var(--radius);
    background:#fff;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    transition:transform .15s,box-shadow .15s;
}
.org-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.12); }
.org-logo-wrap {
    height:80px;
    background:linear-gradient(135deg,var(--brand-light) 0%,#e0e7ff 100%);
    display:flex;align-items:center;justify-content:center;overflow:hidden;
}
.org-logo-wrap img { max-width:100%;max-height:100%;object-fit:contain;padding:10px; }
.org-logo-init {
    width:48px;height:48px;border-radius:10px;
    background:linear-gradient(135deg,var(--brand-dark),#7c3aed);
    color:#fff;font-size:20px;font-weight:800;
    display:flex;align-items:center;justify-content:center;
}
.org-card-body  { padding:12px 14px;flex:1;display:flex;flex-direction:column; }
.org-card-footer{ padding:8px 14px;border-top:1px solid var(--border);background:var(--bg); }
.org-badge { background:var(--brand-light);color:var(--brand-dark);font-size:.68rem;font-weight:600;padding:2px 7px;border-radius:20px; }
</style>

<!-- Page header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-0" style="font-size:1.35rem;">
            <i class="bi bi-building-fill me-2" style="color:var(--brand-dark);"></i>Mon espace recruteur
        </h2>
        <p class="text-muted mb-0" style="font-size:.82rem;">Gérez votre entreprise et vos organisations</p>
    </div>
</div>

<div class="row g-4">

<!-- ── Left column: Company profile ──────────────────────────────────────── -->
<div class="col-lg-4">
    <p class="dash-section-title"><i class="bi bi-briefcase-fill me-1"></i>Profil entreprise</p>

    <?php if ($company): ?>
    <div class="dash-card p-4 text-center mb-3">
        <?php if (!empty($company->logo)): ?>
            <img src="<?= base_url('uploads/logos/' . esc($company->logo)) ?>"
                 alt="logo" class="mx-auto mb-3 rounded" style="width:72px;height:72px;object-fit:cover;">
        <?php else: ?>
            <div class="rounded d-flex align-items-center justify-content-center fw-bold mx-auto mb-3"
                 style="width:72px;height:72px;background:linear-gradient(135deg,var(--brand-dark),#7c3aed);color:#fff;font-size:1.6rem;border-radius:14px!important;">
                <?= strtoupper(substr($company->name, 0, 1)) ?>
            </div>
        <?php endif; ?>

        <h5 class="fw-bold mb-1"><?= esc($company->name) ?></h5>

        <?php if (!empty($company->industry)): ?>
            <p class="text-muted small mb-1"><i class="bi bi-buildings me-1"></i><?= esc($company->industry) ?></p>
        <?php endif; ?>
        <?php if (!empty($company->city)): ?>
            <p class="text-muted small mb-1">
                <i class="bi bi-geo-alt me-1"></i><?= esc($company->city) ?><?= !empty($company->country) ? ', ' . esc($company->country) : '' ?>
            </p>
        <?php endif; ?>
        <?php if (!empty($company->size)): ?>
            <p class="text-muted small mb-2"><i class="bi bi-people me-1"></i><?= esc($company->size) ?> employés</p>
        <?php endif; ?>

        <div class="d-flex gap-2 flex-wrap justify-content-center mt-2">
            <a href="<?= base_url('company/edit') ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>
            <?php if (!empty($company->slug)): ?>
            <a href="<?= base_url('companies/' . esc($company->slug)) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                <i class="bi bi-eye me-1"></i>Page publique
            </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($company->website)): ?>
        <div class="mt-3">
            <a href="<?= esc($company->website) ?>" target="_blank" rel="noopener"
               class="text-muted small text-decoration-none">
                <i class="bi bi-globe me-1"></i><?= esc($company->website) ?>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Quick actions -->
    <div class="dash-card p-3">
        <p class="dash-section-title mb-2"><i class="bi bi-lightning-fill me-1"></i>Actions rapides</p>
        <div class="d-grid gap-2">
            <a href="<?= base_url('jobs/create') ?>" class="btn btn-primary btn-sm fw-semibold">
                <i class="bi bi-plus-circle me-2"></i>Publier une offre
            </a>
            <a href="<?= base_url('organizations/create') ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-buildings me-2"></i>Créer une organisation
            </a>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-grid me-2"></i>Tableau de bord
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="dash-card p-4 text-center">
        <div class="mb-3" style="font-size:3rem;opacity:.25;"><i class="bi bi-building-add"></i></div>
        <p class="text-muted mb-1" style="font-size:.875rem;">Aucun profil entreprise lié.</p>
        <p class="text-muted small mb-3">Vos organisations servent de profil entreprise. Un profil dédié sera créé automatiquement lors de votre première publication d'offre.</p>
    </div>
    <?php endif; ?>
</div>

<!-- ── Right column: Organizations ───────────────────────────────────────── -->
<div class="col-lg-8">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <p class="dash-section-title mb-0"><i class="bi bi-buildings-fill me-1"></i>Mes organisations</p>
        <a href="<?= base_url('organizations/create') ?>" class="btn btn-sm btn-outline-primary"
           style="font-size:.78rem;border-radius:20px;">
            <i class="bi bi-plus me-1"></i>Nouvelle organisation
        </a>
    </div>

    <?php if (empty($orgs)): ?>
    <div class="dash-card p-5 text-center">
        <div class="mb-3" style="font-size:3rem;opacity:.2;"><i class="bi bi-buildings"></i></div>
        <p class="text-muted mb-1">Aucune organisation gérée pour le moment.</p>
        <p class="text-muted small mb-3">Créez votre première organisation ou faites-vous inviter en tant que propriétaire/manager.</p>
        <a href="<?= base_url('organizations/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus me-1"></i>Créer une organisation
        </a>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($orgs as $org): ?>
        <div class="col-sm-6 col-xl-4">
            <div class="org-card h-100">
                <div class="org-logo-wrap">
                    <?php if (!empty($org->logo)): ?>
                        <img src="<?= base_url('uploads/' . esc($org->logo)) ?>" alt="<?= esc($org->name) ?>">
                    <?php else: ?>
                        <div class="org-logo-init"><?= strtoupper(substr($org->name, 0, 1)) ?></div>
                    <?php endif; ?>
                </div>

                <div class="org-card-body">
                    <div class="d-flex align-items-start justify-content-between gap-1 mb-1">
                        <h6 class="fw-bold mb-0" style="font-size:.9rem;line-height:1.3;">
                            <?= esc($org->name) ?>
                        </h6>
                        <?php if (!empty($org->is_verified)): ?>
                            <i class="bi bi-patch-check-fill" style="color:#6366f1;font-size:.9rem;flex-shrink:0;" title="Vérifiée"></i>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($org->type_name ?? null)): ?>
                    <span class="org-badge mb-2"><?= esc($org->type_name) ?></span>
                    <?php endif; ?>

                    <?php if (!empty($org->industry)): ?>
                    <p class="text-muted mb-1" style="font-size:.78rem;">
                        <i class="bi bi-diagram-3 me-1"></i><?= esc($org->industry) ?>
                    </p>
                    <?php endif; ?>

                    <?php if (!empty($org->city)): ?>
                    <p class="text-muted mb-0" style="font-size:.78rem;">
                        <i class="bi bi-geo-alt me-1"></i><?= esc($org->city) ?><?= !empty($org->country) ? ', ' . esc($org->country) : '' ?>
                    </p>
                    <?php endif; ?>

                    <?php if (!empty($org->description)): ?>
                    <p class="text-muted mt-2 mb-0"
                       style="font-size:.78rem;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        <?= esc($org->description) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <div class="org-card-footer d-flex gap-2">
                    <a href="<?= base_url('organizations/' . $org->id) ?>"
                       class="btn btn-outline-secondary btn-sm flex-fill" style="font-size:.75rem;">
                        <i class="bi bi-eye me-1"></i>Voir
                    </a>
                    <a href="<?= base_url('organizations/' . $org->id . '/edit') ?>"
                       class="btn btn-outline-primary btn-sm flex-fill" style="font-size:.75rem;">
                        <i class="bi bi-pencil me-1"></i>Modifier
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

</div><!-- row -->

<?= $this->endSection() ?>
