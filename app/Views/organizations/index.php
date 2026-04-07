<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.org-card {
    border: 1px solid var(--border);
    border-radius: var(--radius);
    background: #fff;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: transform .15s, box-shadow .15s;
}
.org-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
.org-logo-wrap {
    height: 90px;
    background: linear-gradient(135deg, var(--brand-light) 0%, #e0e7ff 100%);
    display: flex; align-items: center; justify-content: center; overflow: hidden;
}
.org-logo-wrap img { max-width: 100%; max-height: 100%; object-fit: contain; padding: 12px; }
.org-logo-init {
    width: 56px; height: 56px; border-radius: 12px;
    background: linear-gradient(135deg, var(--brand-dark), #7c3aed);
    color: #fff; font-size: 22px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}
.org-card-body  { padding: 14px 16px; flex: 1; display: flex; flex-direction: column; }
.org-card-footer { padding: 10px 16px; border-top: 1px solid var(--border); background: var(--bg); }
.org-badge-type     { background: var(--brand-light); color: var(--brand-dark); font-size: .7rem; font-weight: 600; padding: 2px 8px; border-radius: 20px; }
.org-badge-verified { background: #d1fae5; color: #065f46; font-size: .7rem; font-weight: 600; padding: 2px 8px; border-radius: 20px; }
.org-meta { font-size: .8rem; color: var(--muted); display: flex; align-items: center; gap: 5px; margin-bottom: 4px; }
.filter-bar { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 14px 18px; margin-bottom: 20px; }
</style>

<!-- Page header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="fw-bold mb-0" style="font-size:1.5rem;">
            <i class="bi bi-buildings me-2" style="color:var(--brand)"></i>Organisations
        </h1>
        <p class="mb-0 mt-1" style="color:var(--muted);font-size:.875rem;">Découvrez les entreprises, institutions et réseaux professionnels</p>
    </div>
    <?php if (session()->get('logged_in')): ?>
        <a href="<?= base_url('organizations/create') ?>" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i>Nouvelle
        </a>
    <?php endif; ?>
</div>

<!-- Filter bar -->
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <!-- Recherche -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="position-relative">
                <i class="bi bi-search position-absolute" style="left:10px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.8rem;pointer-events:none;"></i>
                <input type="text" name="keyword" class="form-control form-control-sm"
                       style="padding-left:30px;border-radius:20px;"
                       placeholder="Rechercher..."
                       value="<?= esc($filters['keyword'] ?? '') ?>">
            </div>
        </div>

        <!-- Type -->
        <div class="col-lg-2 col-md-6 col-sm-6">
            <select name="type_id" class="form-select form-select-sm" style="border-radius:20px;">
                <option value="">Tous types</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?= $type->id ?>" <?= ($filters['type_id'] ?? null) == $type->id ? 'selected' : '' ?>>
                        <?= esc($type->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Secteur d'activité -->
        <div class="col-lg-2 col-md-6 col-sm-6">
            <select name="industry" class="form-select form-select-sm" style="border-radius:20px;">
                <option value="">Tous secteurs</option>
                <?php foreach ($sectors as $sector): ?>
                    <option value="<?= esc($sector) ?>" <?= ($filters['industry'] ?? null) === $sector ? 'selected' : '' ?>>
                        <?= esc($sector) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Pays -->
        <div class="col-lg-2 col-md-6 col-sm-6">
            <select name="country_code" class="form-select form-select-sm" style="border-radius:20px;">
                <option value="">🌍 Tous pays</option>
                <?php foreach ($countriesWithFlags as $code => $data): ?>
                    <option value="<?= $code ?>" <?= ($filters['country_code'] ?? null) === $code ? 'selected' : '' ?>>
                        <?= $data['flag'] ?> <?= esc($data['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Bouton Filtrer -->
        <div class="col-lg-auto col-md-6 col-sm-6">
            <button type="submit" class="btn btn-primary btn-sm w-100" style="border-radius:20px;">
                <i class="bi bi-funnel me-1"></i>Filtrer
            </button>
        </div>
    </form>
</div>

<!-- Organization grid -->
<div class="row g-3">
    <?php if (!empty($organizations)): ?>
        <?php foreach ($organizations as $org): ?>
            <div class="col-lg-4 col-md-6">
                <div class="org-card">
                    <div class="org-logo-wrap">
                        <?php if (!empty($org->logo)): ?>
                            <img src="<?= base_url('uploads/organizations/' . esc($org->logo)) ?>" alt="<?= esc($org->name) ?>">
                        <?php else: ?>
                            <div class="org-logo-init"><?= strtoupper(substr($org->name, 0, 1)) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="org-card-body">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                            <h6 class="fw-bold mb-0 lh-sm" style="font-size:.925rem;"><?= esc($org->name) ?></h6>
                            <?php if ($org->is_verified): ?>
                                <span class="org-badge-verified flex-shrink-0"><i class="bi bi-patch-check-fill me-1"></i>Vérifié</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($org->type_name)): ?>
                            <div class="mb-2"><span class="org-badge-type"><?= esc($org->type_name) ?></span></div>
                        <?php endif; ?>
                        <?php if (!empty($org->industry)): ?>
                            <div class="org-meta"><i class="bi bi-briefcase"></i><?= esc($org->industry) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($org->country_code)): ?>
                            <?php $countryInfo = $countriesWithFlags[strtoupper($org->country_code)] ?? ['name' => $org->country_code, 'flag' => '🌍']; ?>
                            <div class="org-meta"><span style="font-size:1.1rem;margin-right:4px;"><?= $countryInfo['flag'] ?></span><?= esc($countryInfo['name']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($org->employee_count)): ?>
                            <div class="org-meta"><i class="bi bi-people"></i><?= number_format($org->employee_count) ?> employés</div>
                        <?php endif; ?>
                        <?php if (!empty($org->address)): ?>
                            <div class="org-meta"><i class="bi bi-geo-alt"></i><?= esc(mb_substr($org->address, 0, 45)) ?><?= mb_strlen((string)$org->address) > 45 ? '…' : '' ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="org-card-footer d-flex gap-2 align-items-center">
                        <a href="<?= base_url('organizations/' . $org->id) ?>" class="btn btn-sm btn-outline-primary flex-grow-1" style="border-radius:20px;font-size:12px;">
                            <i class="bi bi-eye me-1"></i>Voir
                        </a>
                        <?php if (!empty($org->website)): ?>
                            <a href="<?= esc($org->website) ?>" target="_blank" rel="noopener noreferrer"
                               class="btn btn-sm btn-outline-secondary" style="border-radius:20px;font-size:12px;" title="Site web">
                                <i class="bi bi-globe2"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="text-center py-5" style="color:var(--muted);">
                <i class="bi bi-buildings" style="font-size:3rem;opacity:.25;display:block;margin-bottom:12px;"></i>
                <p class="fw-semibold mb-1">Aucune organisation trouvée</p>
                <p style="font-size:.875rem;">Modifiez vos filtres ou créez la première organisation.</p>
                <?php if (session()->get('logged_in')): ?>
                    <a href="<?= base_url('organizations/create') ?>" class="btn btn-primary btn-sm mt-1 px-4">
                        <i class="bi bi-plus-lg me-1"></i>Créer une organisation
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($pager): ?>
    <div class="d-flex justify-content-center mt-4">
        <?= $pager->links() ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
