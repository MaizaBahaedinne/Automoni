<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$statusColors = ['pending'=>'warning','reviewed'=>'info','shortlisted'=>'success','rejected'=>'danger','hired'=>'primary'];
$statusLabels = ['pending'=>'En attente','reviewed'=>'En cours','shortlisted'=>'Shortlisté','rejected'=>'Refusé','hired'=>'Recruté'];
$multiOrg     = count($orgs) > 1;
$f            = $filters ?? [];
?>

<style>
.fil-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;margin-bottom:1.5rem;}
.app-row:hover{background:#f8fafc;}
.app-row td{vertical-align:middle;}
.badge-status{font-size:.75rem;padding:.3em .7em;border-radius:50px;}
</style>

<!-- Header -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex-grow-1">
        <h2 class="fw-bold mb-0" style="font-size:1.3rem;">
            <i class="bi bi-people-fill me-2" style="color:var(--brand-dark);"></i>Toutes les candidatures
        </h2>
        <p class="text-muted mb-0" style="font-size:.8rem;">
            <?= count($applications) ?> candidature<?= count($applications) !== 1 ? 's' : '' ?> au total
        </p>
    </div>
    <?php if (session()->get('user_role') === 'admin'): ?>
    <button type="button" class="btn btn-sm btn-outline-danger"
            data-bs-toggle="modal" data-bs-target="#purgeInterviewsModal">
        <i class="bi bi-trash3 me-1"></i>Purger les entretiens
    </button>
    <?php endif; ?>
</div>

<!-- Purge confirmation modal (admin only) -->
<?php if (session()->get('user_role') === 'admin'): ?>
<div class="modal fade" id="purgeInterviewsModal" tabindex="-1" aria-labelledby="purgeInterviewsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius);">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger" id="purgeInterviewsLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmer la purge
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="mb-0">
                    Cette action va <strong>supprimer définitivement tous les entretiens</strong>
                    (planifiés, terminés et annulés) de la base de données.<br>
                    <span class="text-danger fw-semibold">Cette opération est irréversible.</span>
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <form action="<?= base_url('admin/interviews/purge') ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash3 me-1"></i>Oui, tout supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Flash -->
<?php if ($error = session()->getFlashdata('error')): ?>
<div class="alert alert-danger d-flex gap-2 align-items-center mb-3"><i class="bi bi-exclamation-triangle-fill"></i><?= esc($error) ?></div>
<?php endif; ?>
<?php if ($success = session()->getFlashdata('success')): ?>
<div class="alert alert-success d-flex gap-2 align-items-center mb-3"><i class="bi bi-check-circle-fill"></i><?= esc($success) ?></div>
<?php endif; ?>

<!-- Filters -->
<div class="fil-card">
    <form method="get" action="<?= base_url('applications') ?>" class="row g-2 align-items-end">

        <!-- Search -->
        <div class="col-sm-6 col-lg-3">
            <label class="form-label fw-semibold mb-1" style="font-size:.8rem;"><i class="bi bi-search me-1"></i>Candidat / email</label>
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="Nom ou e-mail…" value="<?= esc($f['q'] ?? '') ?>">
        </div>

        <!-- Org filter (only if recruiter manages >1 org) -->
        <?php if ($multiOrg): ?>
        <div class="col-sm-6 col-lg-3">
            <label class="form-label fw-semibold mb-1" style="font-size:.8rem;"><i class="bi bi-buildings me-1"></i>Organisation</label>
            <select name="org_id" class="form-select form-select-sm">
                <option value="">Toutes les orgs</option>
                <?php foreach ($orgs as $org): ?>
                <option value="<?= $org->id ?>"<?= (int)($f['org_id'] ?? 0) === (int)$org->id ? ' selected' : '' ?>>
                    <?= esc($org->name) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- Job filter -->
        <div class="col-sm-6 col-lg-3">
            <label class="form-label fw-semibold mb-1" style="font-size:.8rem;"><i class="bi bi-briefcase me-1"></i>Poste</label>
            <select name="job_id" class="form-select form-select-sm">
                <option value="">Tous les postes</option>
                <?php foreach ($jobs as $job): ?>
                <option value="<?= $job->id ?>"<?= (int)($f['job_id'] ?? 0) === (int)$job->id ? ' selected' : '' ?>>
                    <?= esc($job->title) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Status filter -->
        <div class="col-sm-6 col-lg-2">
            <label class="form-label fw-semibold mb-1" style="font-size:.8rem;"><i class="bi bi-funnel me-1"></i>Statut</label>
            <select name="status" class="form-select form-select-sm">
                <option value="all"<?= empty($f['status']) || $f['status'] === 'all' ? ' selected' : '' ?>>Tous</option>
                <?php foreach ($statusLabels as $val => $lbl): ?>
                <option value="<?= $val ?>"<?= ($f['status'] ?? '') === $val ? ' selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-auto d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-filter me-1"></i>Filtrer
            </button>
            <?php if (!empty($f['q']) || !empty($f['org_id']) || !empty($f['job_id']) || (!empty($f['status']) && $f['status'] !== 'all')): ?>
            <a href="<?= base_url('applications') ?>" class="btn btn-outline-secondary btn-sm" title="Effacer les filtres">
                <i class="bi bi-x-lg"></i>
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Results table -->
<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
    <?php if (empty($applications)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size:2rem;opacity:.4;"></i>
            <p class="mt-2 mb-0">Aucune candidature trouvée.</p>
        </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;">
                <tr>
                    <th style="width:1%;"></th>
                    <th>Candidat</th>
                    <th>Poste</th>
                    <?php if ($multiOrg): ?><th>Organisation</th><?php endif; ?>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($applications as $app): ?>
            <tr class="app-row" style="cursor:pointer;" onclick="window.location='<?= base_url('applications/' . $app->id) ?>'">
                <!-- Avatar -->
                <td class="ps-3">
                    <?php if (!empty($app->avatar)): ?>
                        <img src="<?= base_url('uploads/' . esc($app->avatar)) ?>"
                             style="width:36px;height:36px;object-fit:cover;border-radius:50%;border:2px solid var(--border);" alt="">
                    <?php else: ?>
                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--brand),var(--brand-dark));
                                    color:#fff;font-size:.85rem;font-weight:800;display:flex;align-items:center;justify-content:center;">
                            <?= strtoupper(substr($app->first_name ?? '?', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </td>
                <!-- Candidate -->
                <td>
                    <div class="fw-semibold" style="font-size:.875rem;">
                        <?= esc(trim(($app->first_name ?? '') . ' ' . ($app->last_name ?? ''))) ?: 'Candidat #' . $app->user_id ?>
                    </div>
                    <div class="text-muted" style="font-size:.75rem;"><?= esc($app->email ?? '') ?></div>
                </td>
                <!-- Job -->
                <td>
                    <span style="font-size:.85rem;"><?= esc($app->job_title ?? '—') ?></span>
                </td>
                <?php if ($multiOrg): ?>
                <!-- Org -->
                <td onclick="event.stopPropagation()">
                    <?php if (!empty($app->org_name)): ?>
                        <a href="<?= base_url('organizations/' . (int)$app->org_id) ?>"
                           class="text-decoration-none d-flex align-items-center gap-1" style="font-size:.82rem;">
                            <i class="bi bi-buildings text-primary"></i><?= esc($app->org_name) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted" style="font-size:.82rem;">—</span>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
                <!-- Date -->
                <td>
                    <small class="text-muted">
                        <?= !empty($app->applied_at) ? date('d/m/Y', strtotime($app->applied_at)) : '—' ?>
                    </small>
                </td>
                <!-- Status badge -->
                <td>
                    <span class="badge badge-status bg-<?= $statusColors[$app->status] ?? 'secondary' ?>">
                        <?= $statusLabels[$app->status] ?? ucfirst(esc($app->status)) ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
