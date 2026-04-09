<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.org-hero-cover {
    height: 80px;
    background: linear-gradient(135deg, var(--brand-dark) 0%, #7c3aed 100%);
    border-radius: var(--radius) var(--radius) 0 0;
}
.org-hero-logo {
    width: 80px; height: 80px; border-radius: 12px;
    border: 3px solid #fff; background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
    overflow: hidden; flex-shrink: 0;
}
.org-hero-logo img { width: 100%; height: 100%; object-fit: contain; padding: 6px; }
.org-hero-logo-init {
    width: 100%; height: 100%;
    background: linear-gradient(135deg, var(--brand-dark), #7c3aed);
    color: #fff; font-size: 28px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}
.org-badge-type     { background: var(--brand-light); color: var(--brand-dark); font-size: .75rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
.org-badge-verified { background: #d1fae5; color: #065f46; font-size: .75rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
.info-card { border: 1px solid var(--border); border-radius: var(--radius); background: #fff; margin-bottom: 16px; overflow: hidden; }
.info-card-header { padding: 12px 16px; border-bottom: 1px solid var(--border); font-weight: 700; font-size: .9rem; color: var(--text); display: flex; align-items: center; gap: 8px; }
.info-card-body  { padding: 16px; }
.stat-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border); }
.stat-item:last-child { border-bottom: none; }
.stat-label { font-size: .85rem; color: var(--muted); display: flex; align-items: center; gap: 6px; }
.stat-value { font-weight: 700; font-size: .95rem; color: var(--text); }
.org-meta-item { display: flex; align-items: center; gap: 8px; font-size: .875rem; color: var(--muted); margin-bottom: 6px; }
.social-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border: 1px solid var(--border); border-radius: 20px; font-size: .8rem; color: var(--text); text-decoration: none; transition: background .15s,border-color .15s; }
.social-btn:hover { background: var(--brand-light); border-color: var(--brand); color: var(--brand-dark); }
.member-avatar      { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
.member-avatar-init { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--brand-dark), #7c3aed); color: #fff; font-size: .9rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.sidebar-link { display: flex; align-items: center; gap: 10px; padding: 10px 14px; text-decoration: none; color: var(--text); border-bottom: 1px solid var(--border); transition: background .12s; }
.sidebar-link:hover { background: var(--bg); }
.sidebar-link:last-child { border-bottom: none; }
</style>

<?php
$platformIcons = [
    'facebook'  => 'bi-facebook',
    'twitter'   => 'bi-twitter-x',
    'linkedin'  => 'bi-linkedin',
    'instagram' => 'bi-instagram',
    'youtube'   => 'bi-youtube',
    'github'    => 'bi-github',
];
$platformLabels = [
    'facebook'  => 'Facebook',
    'twitter'   => 'Twitter/X',
    'linkedin'  => 'LinkedIn',
    'instagram' => 'Instagram',
    'youtube'   => 'YouTube',
    'github'    => 'GitHub',
];
?>

<!-- Breadcrumbs -->
<?php if (count($breadcrumbs) > 1): ?>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('organizations') ?>">Organisations</a></li>
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                <li class="breadcrumb-item <?= ($index === count($breadcrumbs) - 1) ? 'active' : '' ?>">
                    <?php if ($index < count($breadcrumbs) - 1): ?>
                        <a href="<?= base_url('organizations/' . $crumb->id) ?>"><?= esc($crumb->name) ?></a>
                    <?php else: ?>
                        <?= esc($crumb->name) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
<?php endif; ?>

<!-- Hero card -->
<div class="info-card mb-4">
    <div class="org-hero-cover"></div>
    <div class="info-card-body">
        <div class="d-flex align-items-start gap-3" style="margin-top:-40px;">
            <div class="org-hero-logo">
                <?php if ($logo_url): ?>
                    <img src="<?= esc($logo_url) ?>" alt="<?= esc($organization->name) ?>">
                <?php else: ?>
                    <div class="org-hero-logo-init"><?= strtoupper(substr($organization->name, 0, 1)) ?></div>
                <?php endif; ?>
            </div>
            <div class="flex-grow-1 pt-4">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                    <div>
                        <h1 class="fw-bold mb-1" style="font-size:1.4rem;"><?= esc($organization->name) ?></h1>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if (!empty($organization->type_name)): ?>
                                <span class="org-badge-type"><?= esc($organization->type_name) ?></span>
                            <?php endif; ?>
                            <?php if ($organization->is_verified): ?>
                                <span class="org-badge-verified"><i class="bi bi-patch-check-fill me-1"></i>Vérifié</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($can_edit): ?>
                        <a href="<?= base_url('organizations/' . $organization->id . '/edit') ?>" class="btn btn-outline-primary btn-sm px-3">
                            <i class="bi bi-pencil me-1"></i>Modifier
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Meta strip -->
        <div class="d-flex flex-wrap gap-3 mt-3 pt-2" style="border-top:1px solid var(--border);">
            <?php if ($organization->industry): ?>
                <div class="org-meta-item mb-0"><i class="bi bi-briefcase"></i><?= esc($organization->industry) ?></div>
            <?php endif; ?>
            <?php if ($organization->employee_count): ?>
                <div class="org-meta-item mb-0"><i class="bi bi-people"></i><?= number_format($organization->employee_count) ?> employés</div>
            <?php endif; ?>
            <?php if ($organization->founded_at): ?>
                <div class="org-meta-item mb-0"><i class="bi bi-calendar3"></i>Fondée en <?= date('Y', strtotime($organization->founded_at)) ?></div>
            <?php endif; ?>
            <?php if ($organization->website): ?>
                <a href="<?= esc($organization->website) ?>" target="_blank" rel="noopener noreferrer"
                   class="org-meta-item mb-0 text-decoration-none" style="color:var(--brand);">
                    <i class="bi bi-globe2"></i><?= esc(parse_url($organization->website, PHP_URL_HOST) ?: $organization->website) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="row g-3">
    <!-- Left column -->
    <div class="col-lg-8">

        <!-- Description -->
        <?php if ($organization->description): ?>
            <div class="info-card">
                <div class="info-card-header"><i class="bi bi-info-circle" style="color:var(--brand)"></i>À propos</div>
                <div class="info-card-body">
                    <p class="mb-0" style="line-height:1.75;white-space:pre-wrap;"><?= esc($organization->description) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Contact & Location -->
        <?php if ($organization->email || $organization->phone || $organization->address): ?>
            <div class="info-card">
                <div class="info-card-header"><i class="bi bi-telephone" style="color:var(--brand)"></i>Contact & Localisation</div>
                <div class="info-card-body">
                    <div class="row g-3">
                        <?php if ($organization->email): ?>
                            <div class="col-md-6">
                                <div class="org-meta-item mb-1"><i class="bi bi-envelope"></i><strong>Email</strong></div>
                                <a href="mailto:<?= esc($organization->email) ?>" class="text-decoration-none"><?= esc($organization->email) ?></a>
                            </div>
                        <?php endif; ?>
                        <?php if ($organization->phone): ?>
                            <div class="col-md-6">
                                <div class="org-meta-item mb-1"><i class="bi bi-telephone"></i><strong>Téléphone</strong></div>
                                <a href="tel:<?= esc($organization->phone) ?>" class="text-decoration-none"><?= esc($organization->phone) ?></a>
                            </div>
                        <?php endif; ?>
                        <?php if ($organization->address): ?>
                            <div class="col-12">
                                <div class="org-meta-item mb-1"><i class="bi bi-geo-alt"></i><strong>Adresse</strong></div>
                                <p class="mb-0" style="color:var(--muted);"><?= nl2br(esc($organization->address)) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($organization->latitude && $organization->longitude): ?>
                        <div class="mt-3">
                            <iframe title="Localisation" width="100%" height="250"
                                    style="border:0;border-radius:8px;" loading="lazy"
                                    src="https://www.openstreetmap.org/export/embed.html?bbox=<?= ($organization->longitude - 0.05) ?>,<?= ($organization->latitude - 0.05) ?>,<?= ($organization->longitude + 0.05) ?>,<?= ($organization->latitude + 0.05) ?>&layer=mapnik&marker=<?= $organization->latitude ?>,<?= $organization->longitude ?>">
                            </iframe>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Social links -->
        <?php if (!empty($social_links)): ?>
            <div class="info-card">
                <div class="info-card-header"><i class="bi bi-share" style="color:var(--brand)"></i>Réseaux sociaux</div>
                <div class="info-card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($social_links as $link): ?>
                            <?php
                            $icon  = $platformIcons[$link->platform]  ?? 'bi-link-45deg';
                            $label = $platformLabels[$link->platform] ?? ucfirst($link->platform);
                            ?>
                            <a href="<?= esc($link->url) ?>" target="_blank" rel="noopener noreferrer" class="social-btn">
                                <i class="bi <?= $icon ?>"></i><?= $label ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Certifications -->
        <?php if (!empty($certifications)): ?>
            <div class="info-card">
                <div class="info-card-header"><i class="bi bi-award" style="color:var(--brand)"></i>Certifications</div>
                <div class="p-0">
                    <?php foreach ($certifications as $cert): ?>
                        <div class="d-flex justify-content-between align-items-start px-3 py-3" style="border-bottom:1px solid var(--border);">
                            <div>
                                <div class="fw-semibold" style="font-size:.9rem;"><?= esc($cert->name) ?></div>
                                <?php if ($cert->issuer): ?>
                                    <div style="font-size:.8rem;color:var(--muted);"><?= esc($cert->issuer) ?></div>
                                <?php endif; ?>
                                <?php if ($cert->url): ?>
                                    <a href="<?= esc($cert->url) ?>" target="_blank" rel="noopener noreferrer"
                                       class="text-decoration-none" style="font-size:.8rem;color:var(--brand);">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Voir le certificat
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php if ($cert->issued_at): ?>
                                <span class="flex-shrink-0" style="font-size:.8rem;color:var(--muted);"><?= date('M Y', strtotime($cert->issued_at)) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Job offers -->
        <?php $org_jobs = $org_jobs ?? []; ?>
        <div class="info-card">
            <div class="info-card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-briefcase" style="color:var(--brand)"></i>&nbsp;Offres d'emploi
                    <?php if (!empty($org_jobs)): ?>
                        <span class="badge ms-1" style="background:var(--brand-light);color:var(--brand-dark);font-size:.7rem;"><?= count($org_jobs) ?></span>
                    <?php endif; ?>
                </span>
                <?php if ($can_edit): ?>
                    <a href="<?= base_url('jobs/create') ?>" class="btn btn-outline-primary btn-sm px-2 py-0" style="font-size:.75rem;">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php if (empty($org_jobs)): ?>
                <div class="info-card-body text-muted" style="font-size:.875rem;">
                    Aucune offre active pour le moment.
                </div>
            <?php else: ?>
                <div class="p-0">
                    <?php
                    $contractLabels = ['CDI'=>'CDI','CDD'=>'CDD','Freelance'=>'Freelance','Internship'=>'Stage','PartTime'=>'Temps partiel'];
                    $remoteLabels   = ['onsite'=>'Sur site','remote'=>'Télétravail','hybrid'=>'Hybride'];
                    ?>
                    <?php foreach ($org_jobs as $jb): ?>
                        <a href="<?= base_url('jobs/' . esc($jb->slug)) ?>"
                           class="d-flex align-items-start gap-3 px-3 py-3 text-decoration-none text-dark sidebar-link"
                           style="border-bottom:1px solid var(--border);">
                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="fw-semibold lh-sm mb-1" style="font-size:.9rem;"><?= esc($jb->title) ?></div>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge" style="background:var(--brand-light);color:var(--brand-dark);font-size:.65rem;">
                                        <?= esc($contractLabels[$jb->contract_type] ?? $jb->contract_type) ?>
                                    </span>
                                    <?php if (!empty($jb->remote)): ?>
                                    <span class="badge bg-light text-muted border" style="font-size:.65rem;">
                                        <?= esc($remoteLabels[$jb->remote] ?? $jb->remote) ?>
                                    </span>
                                    <?php endif; ?>
                                    <?php if (!empty($jb->location)): ?>
                                    <span class="text-muted" style="font-size:.72rem;">
                                        <i class="bi bi-geo-alt me-1"></i><?= esc($jb->location) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-muted" style="font-size:.72rem;white-space:nowrap;">
                                <?= !empty($jb->created_at) ? date('d M Y', strtotime($jb->created_at)) : '' ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Right sidebar -->
    <div class="col-lg-4">

        <!-- Stats -->
        <div class="info-card">
            <div class="info-card-header"><i class="bi bi-bar-chart" style="color:var(--brand)"></i>Statistiques</div>
            <div class="info-card-body">
                <div class="stat-item">
                    <span class="stat-label"><i class="bi bi-people"></i>Membres</span>
                    <span class="stat-value"><?= number_format($stats['members_count'] ?? 0) ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label"><i class="bi bi-diagram-3"></i>Filiales directes</span>
                    <span class="stat-value"><?= number_format($stats['subsidiaries_count'] ?? 0) ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label"><i class="bi bi-diagram-2"></i>Toutes filiales</span>
                    <span class="stat-value"><?= number_format($stats['descendants_count'] ?? 0) ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label"><i class="bi bi-handshake"></i>Partenaires</span>
                    <span class="stat-value"><?= number_format($stats['partners_count'] ?? 0) ?></span>
                </div>
            </div>
        </div>

        <!-- Subsidiaries -->
        <?php if (!empty($subsidiaries)): ?>
            <div class="info-card">
                <div class="info-card-header"><i class="bi bi-diagram-3" style="color:var(--brand)"></i>Filiales</div>
                <div class="p-0">
                    <?php foreach ($subsidiaries as $sub): ?>
                        <a href="<?= base_url('organizations/' . $sub->id) ?>" class="sidebar-link">
                            <div style="width:32px;height:32px;border-radius:8px;background:var(--brand-light);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;color:var(--brand-dark);flex-shrink:0;">
                                <?= strtoupper(substr($sub->name, 0, 1)) ?>
                            </div>
                            <span style="font-size:.875rem;"><?= esc($sub->name) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Partners -->
        <?php if (!empty($partners)): ?>
            <div class="info-card">
                <div class="info-card-header"><i class="bi bi-handshake" style="color:var(--brand)"></i>Partenaires</div>
                <div class="p-0">
                    <?php foreach ($partners as $partner): ?>
                        <a href="<?= base_url('organizations/' . $partner->partner_id) ?>" class="sidebar-link">
                            <?php if (!empty($partner->logo)): ?>
                                <img src="<?= base_url('uploads/organizations/' . esc($partner->logo)) ?>" alt=""
                                     style="width:32px;height:32px;border-radius:8px;object-fit:contain;border:1px solid var(--border);flex-shrink:0;">
                            <?php else: ?>
                                <div style="width:32px;height:32px;border-radius:8px;background:var(--brand-light);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;color:var(--brand-dark);flex-shrink:0;">
                                    <?= strtoupper(substr($partner->partner_name, 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div style="font-size:.875rem;font-weight:600;"><?= esc($partner->partner_name) ?></div>
                                <?php if (!empty($partner->partnership_type)): ?>
                                    <div style="font-size:.75rem;color:var(--muted);"><?= esc($partner->partnership_type) ?></div>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Members (visible to organization members only) -->
        <?php if ($is_member && (!empty($members) || $can_manage)): ?>
            <div class="info-card">
                <div class="info-card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people" style="color:var(--brand)"></i>&nbsp;Équipe</span>
                    <?php if ($can_manage): ?>
                        <button type="button" class="btn btn-primary btn-sm px-2 py-0" style="font-size:.75rem;"
                                data-bs-toggle="modal" data-bs-target="#addMemberModal">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    <?php endif; ?>
                </div>
                <?php if (!empty($members)): ?>
                    <div class="p-0">
                        <?php foreach ($members as $member): ?>
                            <?php $inactive = isset($member->is_active) && !$member->is_active; ?>
                            <div class="d-flex align-items-center gap-2 px-3 py-2 org-member-row<?= $inactive ? ' opacity-50' : '' ?>"
                                 id="member-row-<?= (int)$member->user_id ?>"
                                 style="border-bottom:1px solid var(--border);">
                                <?php if (!empty($member->avatar)): ?>
                                    <img src="<?= base_url(esc($member->avatar)) ?>" alt="" class="member-avatar">
                                <?php else: ?>
                                    <div class="member-avatar-init"><?= strtoupper(substr($member->first_name, 0, 1)) ?></div>
                                <?php endif; ?>
                                <div class="flex-grow-1" style="min-width:0;">
                                    <div class="fw-semibold lh-sm" style="font-size:.875rem;"><?= esc($member->first_name . ' ' . $member->last_name) ?></div>
                                    <div style="font-size:.75rem;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= esc($member->email) ?></div>
                                </div>
                                <?php if (!empty($member->role)): ?>
                                    <span class="badge flex-shrink-0" style="background:var(--brand-light);color:var(--brand-dark);font-size:.65rem;"><?= esc($member->role) ?></span>
                                <?php endif; ?>
                                <?php if ($inactive): ?>
                                    <span class="badge bg-secondary flex-shrink-0" style="font-size:.65rem;">inactif</span>
                                <?php endif; ?>
                                <?php if ($can_manage && $member->user_id !== session()->get('user_id')): ?>
                                <div class="d-flex gap-1 flex-shrink-0 ms-1">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-<?= $inactive ? 'success' : 'warning' ?> py-0 px-1"
                                            style="font-size:.7rem;"
                                            title="<?= $inactive ? 'Réactiver' : 'Désactiver' ?>"
                                            onclick="toggleMember(<?= (int)$member->user_id ?>, this)">
                                        <i class="bi bi-<?= $inactive ? 'person-check' : 'person-dash' ?>"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger py-0 px-1"
                                            style="font-size:.7rem;"
                                            title="Retirer de l'organisation"
                                            onclick="removeMember(<?= (int)$member->user_id ?>, '<?= esc($member->first_name . ' ' . $member->last_name) ?>')">
                                        <i class="bi bi-person-x"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php if ($can_manage): ?>
<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="border-radius:var(--radius);">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold" id="addMemberModalLabel">Ajouter un membre</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <!-- Step 1: search -->
                <div id="amStep1">
                    <div class="mb-2">
                        <input type="text" id="amSearchInput" class="form-control form-control-sm"
                               placeholder="Nom ou email…" autocomplete="off">
                    </div>
                    <div id="amResults" class="list-group list-group-flush mb-1" style="max-height:200px;overflow-y:auto;"></div>
                </div>
                <!-- Step 2: confirm + role -->
                <div id="amStep2" class="d-none">
                    <div class="d-flex align-items-center gap-2 p-2 mb-2" style="background:var(--brand-light);border-radius:8px;">
                        <div id="amUserAvatar"></div>
                        <div>
                            <div class="fw-semibold" id="amUserName" style="font-size:.875rem;"></div>
                            <div id="amUserEmail" style="font-size:.75rem;color:var(--muted);"></div>
                        </div>
                    </div>
                    <select id="amRoleSelect" class="form-select form-select-sm mb-3">
                        <option value="viewer">Membre</option>
                        <option value="manager">Gestionnaire</option>
                        <option value="owner">Propriétaire</option>
                    </select>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1" id="amBackBtn">Retour</button>
                        <button type="button" class="btn btn-sm btn-primary flex-grow-1" id="amConfirmBtn">Ajouter</button>
                    </div>
                    <div id="amError" class="text-danger mt-2" style="font-size:.8rem;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const ORG_ID   = <?= (int) $organization->id ?>;
    const BASE     = '<?= base_url() ?>';
    const CSRF_NAME = '<?= csrf_token() ?>';
    let   CSRF_HASH = '<?= csrf_hash() ?>';
    let   selectedUserId = null;
    let   searchTimer = null;

    const step1   = document.getElementById('amStep1');
    const step2   = document.getElementById('amStep2');
    const results = document.getElementById('amResults');
    const input   = document.getElementById('amSearchInput');

    // Search users as user types
    input.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const q = this.value.trim();
        if (q.length < 2) { results.innerHTML = ''; return; }
        searchTimer = setTimeout(() => fetchUsers(q), 300);
    });

    function fetchUsers(q) {
        fetch(BASE + 'organizations/' + ORG_ID + '/members/search-users?q=' + encodeURIComponent(q), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            results.innerHTML = '';
            if (!data.data || data.data.length === 0) {
                results.innerHTML = '<div class="list-group-item py-1 text-muted" style="font-size:.8rem;">Aucun résultat</div>';
                return;
            }
            data.data.forEach(u => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action py-1 px-2';
                item.style.fontSize = '.8rem';
                item.innerHTML = `<strong>${escHtml(u.first_name + ' ' + u.last_name)}</strong> <span class="text-muted">${escHtml(u.email)}</span>`;
                item.addEventListener('click', () => selectUser(u));
                results.appendChild(item);
            });
        })
        .catch(() => {});
    }

    function selectUser(u) {
        selectedUserId = u.id;
        document.getElementById('amUserName').textContent  = u.first_name + ' ' + u.last_name;
        document.getElementById('amUserEmail').textContent = u.email;
        const avatarEl = document.getElementById('amUserAvatar');
        if (u.avatar) {
            avatarEl.innerHTML = `<img src="${BASE}uploads/${escHtml(u.avatar)}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;" alt="">`;
        } else {
            avatarEl.innerHTML = `<div style="width:32px;height:32px;border-radius:50%;background:var(--brand);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;">${escHtml(u.first_name.charAt(0).toUpperCase())}</div>`;
        }
        step1.classList.add('d-none');
        step2.classList.remove('d-none');
        document.getElementById('amError').textContent = '';
    }

    document.getElementById('amBackBtn').addEventListener('click', () => {
        step2.classList.add('d-none');
        step1.classList.remove('d-none');
        selectedUserId = null;
    });

    document.getElementById('amConfirmBtn').addEventListener('click', () => {
        if (!selectedUserId) return;
        const role = document.getElementById('amRoleSelect').value;
        const body = new URLSearchParams({ [CSRF_NAME]: CSRF_HASH, user_id: selectedUserId, role });

        fetch(BASE + 'organizations/' + ORG_ID + '/members', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body
        })
        .then(r => r.json().then(d => ({ status: r.status, data: d })))
        .then(({ status, data }) => {
            if (data._token_name) { CSRF_HASH = data._token_hash; }
            if (status === 201 || data.status === 'success') {
                location.reload();
            } else {
                document.getElementById('amError').textContent = data.message || 'Erreur lors de l\'ajout.';
            }
        })
        .catch(() => {
            document.getElementById('amError').textContent = 'Erreur réseau.';
        });
    });

    // Reset modal state on close
    document.getElementById('addMemberModal').addEventListener('hidden.bs.modal', () => {
        step1.classList.remove('d-none');
        step2.classList.add('d-none');
        input.value = '';
        results.innerHTML = '';
        selectedUserId = null;
        document.getElementById('amError').textContent = '';
    });

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
<?php endif; ?>

<?php if ($can_manage): ?>
<script>
const ORG_ID   = <?= (int) $organization->id ?>;
const CSRF_N   = '<?= csrf_token() ?>';
const CSRF_H   = '<?= csrf_hash() ?>';
const BASE_URL = '<?= base_url() ?>';

function orgFetch(url, method, onSuccess) {
    fetch(url, {
        method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: CSRF_N + '=' + encodeURIComponent(CSRF_H),
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') { onSuccess(data); }
        else { alert(data.message || 'Erreur'); }
    })
    .catch(() => alert('Erreur réseau.'));
}

function toggleMember(userId, btn) {
    orgFetch(BASE_URL + 'organizations/' + ORG_ID + '/members/' + userId + '/toggle', 'POST', function(data) {
        const row = document.getElementById('member-row-' + userId);
        if (!row) return;
        const active = data.is_active;
        row.classList.toggle('opacity-50', !active);
        // Update badge
        let badge = row.querySelector('.badge.bg-secondary');
        if (!active) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'badge bg-secondary flex-shrink-0';
                badge.style.fontSize = '.65rem';
                badge.textContent = 'inactif';
                row.querySelector('.d-flex.gap-1').before(badge);
            }
        } else {
            if (badge) badge.remove();
        }
        // Swap button
        btn.className = 'btn btn-sm btn-outline-' + (active ? 'warning' : 'success') + ' py-0 px-1';
        btn.title     = active ? 'Désactiver' : 'Réactiver';
        btn.querySelector('i').className = 'bi bi-' + (active ? 'person-dash' : 'person-check');
    });
}

function removeMember(userId, name) {
    if (!confirm('Retirer ' + name + ' de l\'organisation ?')) return;
    orgFetch(BASE_URL + 'organizations/' + ORG_ID + '/members/' + userId, 'DELETE', function() {
        const row = document.getElementById('member-row-' + userId);
        if (row) row.remove();
    });
}
</script>
<?php endif; ?>

<?= $this->endSection() ?>
