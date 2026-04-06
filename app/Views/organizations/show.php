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

        <!-- Members -->
        <?php if (!empty($members) || $can_manage): ?>
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
                            <div class="d-flex align-items-center gap-2 px-3 py-2" style="border-bottom:1px solid var(--border);">
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
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>
