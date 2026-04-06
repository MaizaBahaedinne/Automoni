<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
/* ──────────────── HOME PAGE ──────────────────────────────────────────────── */

/* ── Feed layout (logged-in) ────────────────────────────────────────────── */
.feed-aside-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.09);
    overflow: hidden;
    margin-bottom: 12px;
}
.feed-aside-cover {
    height: 58px;
    background: linear-gradient(135deg, #0A66C2 0%, #0d1b2a 100%);
}
.feed-aside-avatar-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: -30px;
    padding: 0 16px 14px;
}
.feed-aside-avatar {
    width: 60px; height: 60px;
    border-radius: 50%;
    border: 3px solid #fff;
    object-fit: cover;
}
.feed-aside-init {
    width: 60px; height: 60px;
    border-radius: 50%;
    border: 3px solid #fff;
    background: #4f46e5;
    color: #fff;
    font-size: 22px;
    font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}
.feed-aside-name { font-size: 15px; font-weight: 700; margin-top: 8px; color: #000; text-align: center; }
.feed-aside-head { font-size: 12px; color: #666; text-align: center; line-height: 1.4; }
.feed-quick-link {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 16px;
    font-size: 13px; font-weight: 500; color: #333;
    text-decoration: none;
    border-top: 1px solid #f0f0f0;
    transition: background .12s;
}
.feed-quick-link:hover { background: #f3f2f0; color: #0A66C2; }
.feed-quick-link i { font-size: 16px; color: #0A66C2; min-width: 20px; }
.feed-completeness {
    padding: 10px 16px 14px;
    border-top: 1px solid #f0f0f0;
}
.feed-completeness small { font-size: 11.5px; color: #666; }
.progress-slim { height: 5px; border-radius: 10px; background: #e5e7eb; }
.progress-slim .bar { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #0A66C2, #4f46e5); transition: width .4s; }

/* ── Feed cards (job posts) ─────────────────────────────────────────────── */
.feed-post {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.09);
    margin-bottom: 10px;
    transition: box-shadow .15s;
}
.feed-post:hover { box-shadow: 0 2px 12px rgba(0,0,0,.13); }
.feed-post-head {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 16px 16px 10px;
}
.feed-co-icon {
    width: 48px; height: 48px; min-width: 48px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid #e5e7eb;
    background: #f8f9fa;
}
.feed-co-init {
    width: 48px; height: 48px; min-width: 48px;
    border-radius: 6px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff; font-size: 18px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}
.feed-post-title { font-size: 15px; font-weight: 700; color: #000; line-height: 1.3; }
.feed-post-title a { color: inherit; text-decoration: none; }
.feed-post-title a:hover { color: #0A66C2; }
.feed-post-sub { font-size: 13px; color: #555; margin-top: 1px; }
.feed-post-time { font-size: 11.5px; color: #999; margin-top: 3px; }
.feed-post-body { padding: 0 16px 10px; font-size: 13px; color: #444; line-height: 1.6; }
.feed-post-footer {
    padding: 10px 16px 14px;
    border-top: 1px solid #f0f0f0;
    display: flex; align-items: center; flex-wrap: wrap; gap: 8px;
}
.feed-badge {
    font-size: 11.5px; font-weight: 600;
    padding: 2px 9px; border-radius: 20px;
    border: 1px solid #ccc;
    background: #f8f9fa; color: #444;
}
.feed-badge.cdi   { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
.feed-badge.remote{ background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
.feed-badge.salary{ background: #fefce8; border-color: #fde68a; color: #92400e; }
.feed-search-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.09);
    padding: 16px;
    margin-bottom: 10px;
}

/* ── Right sidebar ──────────────────────────────────────────────────────── */
.sidebar-r-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.09);
    padding: 16px;
    margin-bottom: 12px;
}
.sidebar-r-title { font-size: 15px; font-weight: 700; color: #000; margin-bottom: 12px; }
.co-row {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
    text-decoration: none;
}
.co-row:last-child { border-bottom: none; }
.co-row-logo {
    width: 36px; height: 36px; min-width: 36px;
    border-radius: 5px; object-fit: cover;
    border: 1px solid #e5e7eb;
}
.co-row-init {
    width: 36px; height: 36px; min-width: 36px;
    border-radius: 5px;
    background: linear-gradient(135deg, #6366f1, #7c3aed);
    color: #fff; font-size: 14px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}
.co-row-name { font-size: 13px; font-weight: 600; color: #000; }
.co-row-jobs { font-size: 11.5px; color: #0A66C2; margin-top: 1px; }

/* ── Landing (guest) ────────────────────────────────────────────────────── */
.landing-hero {
    background: linear-gradient(135deg, #0A66C2 0%, #0d1b2a 100%);
    border-radius: 16px;
    padding: 64px 40px;
    color: #fff;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}
.landing-hero h1 { font-size: clamp(1.8rem, 4vw, 2.8rem); font-weight: 800; line-height: 1.15; }
.landing-hero .lead { font-size: 1.1rem; opacity: .85; }
.hero-search-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 28px; }
.hero-input {
    flex: 1; min-width: 140px; max-width: 280px;
    border: none; border-radius: 8px;
    padding: 12px 16px; font-size: .95rem; color: #111;
}
.hero-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(255,255,255,.3); }
.hero-btn {
    background: #fff; color: #0A66C2;
    border: none; border-radius: 8px;
    padding: 12px 24px; font-size: .95rem; font-weight: 700;
    cursor: pointer; white-space: nowrap;
    transition: transform .15s;
}
.hero-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.2); }
.hero-blob {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    pointer-events: none;
}

/* ── Stats row ──────────────────────────────────────────────────────────── */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 40px; }
.stat-box {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.08);
    padding: 24px 16px;
    text-align: center;
}
.stat-box .stat-num { font-size: 1.9rem; font-weight: 800; color: #0A66C2; line-height: 1; }
.stat-box .stat-lbl { font-size: .8rem; color: #666; margin-top: 6px; font-weight: 500; }

/* ── Landing job grid ───────────────────────────────────────────────────── */
.land-job-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.09);
    padding: 18px;
    text-decoration: none;
    display: block;
    transition: transform .15s, box-shadow .15s;
}
.land-job-card:hover { transform: translateY(-3px); box-shadow: 0 4px 20px rgba(0,0,0,.12); }
.land-job-co    { font-size: 12.5px; font-weight: 600; color: #555; margin: 6px 0 2px; }
.land-job-title { font-size: 15px; font-weight: 700; color: #111; margin-bottom: 8px; line-height: 1.3; }

/* ── CTA cards ──────────────────────────────────────────────────────────── */
.cta-card { border-radius: 16px; padding: 36px 32px; color: #fff; }
.cta-seeker    { background: linear-gradient(135deg, #0A66C2 0%, #4f46e5 100%); }
.cta-recruiter { background: linear-gradient(135deg, #db2777 0%, #9333ea 100%); }
.cta-card h4 { font-size: 1.25rem; font-weight: 700; margin-bottom: 8px; }
.cta-card p  { opacity: .88; font-size: .95rem; margin-bottom: 20px; }

@media (max-width: 991px) {
    .feed-left-col, .feed-right-col { display: none; }
}
@media (max-width: 767px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .landing-hero { padding: 40px 20px; }
}
</style>

<?php if (session()->get('logged_in')): ?>
<!-- ====================================================================
     LOGGED-IN VIEW — LinkedIn-style feed
===================================================================== -->
<?php
    $fullName  = esc(session()->get('user_name') ?? '');
    $pct       = (int) ($myProfile?->completeness ?? 0);
    $avatarSrc = !empty($myProfile?->avatar) ? base_url('uploads/' . esc($myProfile->avatar)) : null;
?>
<div class="row g-3" style="margin-top:-8px;">

    <!-- ── Left sidebar ──────────────────────────────────────────── -->
    <div class="col-lg-3 feed-left-col">

        <div class="feed-aside-card">
            <div class="feed-aside-cover"></div>
            <div class="feed-aside-avatar-wrap">
                <?php if ($avatarSrc): ?>
                    <img src="<?= $avatarSrc ?>" class="feed-aside-avatar" alt="">
                <?php else: ?>
                    <div class="feed-aside-init"><?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?></div>
                <?php endif; ?>
                <div class="feed-aside-name"><?= $fullName ?></div>
                <?php if (!empty($myProfile?->headline)): ?>
                <div class="feed-aside-head"><?= esc($myProfile->headline) ?></div>
                <?php endif; ?>
                <?php
                    $loc = implode(', ', array_filter([$myProfile?->city ?? '', $myProfile?->country ?? '']));
                    if (!empty($loc)):
                ?>
                <div class="feed-aside-head mt-1">
                    <i class="bi bi-geo-alt" style="font-size:11px;"></i> <?= esc($loc) ?>
                </div>
                <?php endif; ?>
            </div>

            <a href="<?= base_url('profile') ?>" class="feed-quick-link">
                <i class="bi bi-person-circle"></i> Mon profil
            </a>
            <?php if (session()->get('user_role') === 'job_seeker'): ?>
            <a href="<?= base_url('alerts') ?>" class="feed-quick-link">
                <i class="bi bi-bell"></i> Mes alertes
            </a>
            <?php endif; ?>
            <a href="<?= base_url('jobs') ?>" class="feed-quick-link">
                <i class="bi bi-briefcase"></i> Parcourir les offres
            </a>
            <?php if (in_array(session()->get('user_role'), ['recruiter','admin'])): ?>
            <a href="<?= base_url('jobs/create') ?>" class="feed-quick-link">
                <i class="bi bi-plus-circle"></i> Publier une offre
            </a>
            <?php endif; ?>

            <?php if ($pct < 100): ?>
            <div class="feed-completeness">
                <div class="d-flex justify-content-between mb-1">
                    <small class="fw-semibold" style="color:#0A66C2;">Profil complété</small>
                    <small><?= $pct ?>%</small>
                </div>
                <div class="progress-slim"><div class="bar" style="width:<?= $pct ?>%;"></div></div>
                <a href="<?= base_url('profile/edit') ?>" class="d-block text-center mt-2" style="font-size:12px;color:#0A66C2;text-decoration:none;font-weight:500;">
                    Compléter mon profil &#8594;
                </a>
            </div>
            <?php endif; ?>
        </div>

        <div class="feed-aside-card py-2">
            <a href="<?= base_url('dashboard') ?>" class="feed-quick-link" style="border-top:none;">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            <a href="<?= base_url('profile/edit') ?>" class="feed-quick-link">
                <i class="bi bi-pencil-square"></i> Modifier le profil
            </a>
        </div>

    </div>

    <!-- ── Main feed ─────────────────────────────────────────────── -->
    <div class="col-lg-6">

        <div class="feed-search-card">
            <form action="<?= base_url('jobs') ?>" method="get" class="d-flex gap-2">
                <div class="d-flex align-items-center gap-2 flex-grow-1"
                     style="background:#f3f2f0;border-radius:24px;padding:8px 16px;">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" name="keyword" class="border-0 bg-transparent flex-grow-1"
                           placeholder="Rechercher des offres, entreprises&#x2026;"
                           style="outline:none;font-size:14px;color:#333;">
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-3">Chercher</button>
            </form>
        </div>

        <?php if (empty($latestJobs)): ?>
        <div class="feed-post" style="padding:40px;text-align:center;color:#999;">
            <i class="bi bi-briefcase display-4 d-block mb-3 opacity-25"></i>
            <p class="mb-0">Aucune offre disponible.</p>
        </div>
        <?php else: ?>
        <?php foreach ($latestJobs as $job):
            $posted    = !empty($job->created_at) ? (new DateTime($job->created_at))->diff(new DateTime()) : null;
            $postedAgo = '';
            if ($posted) {
                $postedAgo = $posted->days > 0 ? $posted->days . 'j' : ($posted->h . 'h');
                $postedAgo .= ' ago';
            }
        ?>
        <div class="feed-post">
            <div class="feed-post-head">
                <?php if (!empty($job->company_logo)): ?>
                    <img src="<?= base_url('uploads/logos/' . esc($job->company_logo)) ?>"
                         alt="" class="feed-co-icon">
                <?php else: ?>
                    <div class="feed-co-init"><?= strtoupper(substr($job->company_name ?? 'C', 0, 1)) ?></div>
                <?php endif; ?>
                <div class="flex-grow-1">
                    <div class="feed-post-title">
                        <a href="<?= base_url('jobs/' . esc($job->slug)) ?>"><?= esc($job->title) ?></a>
                    </div>
                    <div class="feed-post-sub">
                        <?php if (!empty($job->company_slug)): ?>
                        <a href="<?= base_url('companies/' . esc($job->company_slug)) ?>"
                           style="color:inherit;text-decoration:none;font-weight:600;"><?= esc($job->company_name) ?></a>
                        <?php else: ?>
                        <strong><?= esc($job->company_name) ?></strong>
                        <?php endif; ?>
                        <?php if (!empty($job->location)): ?> &middot; <?= esc($job->location) ?><?php endif; ?>
                    </div>
                    <?php if ($postedAgo): ?>
                    <div class="feed-post-time"><i class="bi bi-clock me-1"></i><?= $postedAgo ?></div>
                    <?php endif; ?>
                </div>
                <a href="<?= base_url('jobs/' . esc($job->slug)) ?>"
                   class="btn btn-sm btn-outline-primary align-self-start flex-shrink-0"
                   style="border-radius:20px!important;font-size:12px;padding:4px 14px;">
                    Postuler
                </a>
            </div>

            <?php if (!empty($job->description)): ?>
            <div class="feed-post-body">
                <?= esc(mb_substr(strip_tags($job->description), 0, 180)) ?><?= mb_strlen(strip_tags((string)$job->description)) > 180 ? '&hellip;' : '' ?>
            </div>
            <?php endif; ?>

            <div class="feed-post-footer">
                <span class="feed-badge cdi"><?= esc($job->contract_type) ?></span>
                <?php if (!empty($job->remote) && $job->remote !== 'onsite'): ?>
                <span class="feed-badge remote"><i class="bi bi-laptop me-1"></i><?= ucfirst(esc($job->remote)) ?></span>
                <?php endif; ?>
                <?php if (!empty($job->experience_level)): ?>
                <span class="feed-badge"><?= esc($job->experience_level) ?></span>
                <?php endif; ?>
                <?php if (!empty($job->salary_min)): ?>
                <span class="feed-badge salary">
                    <i class="bi bi-cash me-1"></i><?= number_format($job->salary_min) ?><?= !empty($job->salary_max) ? '&ndash;'.number_format($job->salary_max) : '+' ?>&nbsp;<?= esc($job->salary_currency ?? 'MAD') ?>/an
                </span>
                <?php endif; ?>
                <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="ms-auto"
                   style="font-size:12.5px;color:#0A66C2;text-decoration:none;font-weight:500;">
                    Voir l&rsquo;offre &#8594;
                </a>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="text-center py-2">
            <a href="<?= base_url('jobs') ?>" class="btn btn-outline-primary px-5"
               style="border-radius:20px!important;">
                Voir toutes les offres <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <?php endif; ?>

    </div>

    <!-- ── Right sidebar ─────────────────────────────────────────── -->
    <div class="col-lg-3 feed-right-col">

        <?php if (!empty($topCompanies)): ?>
        <div class="sidebar-r-card">
            <div class="sidebar-r-title">Entreprises qui recrutent</div>
            <?php foreach ($topCompanies as $co): ?>
            <a href="<?= base_url('companies/' . esc($co->slug)) ?>" class="co-row">
                <?php if (!empty($co->logo)): ?>
                <img src="<?= base_url('uploads/logos/' . esc($co->logo)) ?>" class="co-row-logo" alt="">
                <?php else: ?>
                <div class="co-row-init"><?= strtoupper(substr($co->name, 0, 1)) ?></div>
                <?php endif; ?>
                <div>
                    <div class="co-row-name"><?= esc($co->name) ?></div>
                    <div class="co-row-jobs"><?= (int)$co->job_count ?> offre<?= $co->job_count > 1 ? 's' : '' ?> active<?= $co->job_count > 1 ? 's' : '' ?></div>
                </div>
            </a>
            <?php endforeach; ?>
            <a href="<?= base_url('jobs') ?>" class="d-block mt-2 text-center"
               style="font-size:12.5px;color:#0A66C2;text-decoration:none;font-weight:500;">
                Voir toutes les entreprises &#8594;
            </a>
        </div>
        <?php endif; ?>

        <?php if ($pct < 100): ?>
        <div class="sidebar-r-card">
            <div class="sidebar-r-title" style="font-size:14px;">
                <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Boostez votre visibilit&#233;
            </div>
            <p style="font-size:12.5px;color:#555;line-height:1.6;margin-bottom:12px;">
                Un profil complet est <strong>5&times; plus visible</strong> par les recruteurs.
            </p>
            <div class="d-flex justify-content-between mb-1" style="font-size:12px;">
                <span>Compl&#233;t&#233;</span><strong><?= $pct ?>%</strong>
            </div>
            <div class="progress-slim mb-3"><div class="bar" style="width:<?= $pct ?>%;"></div></div>
            <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary btn-sm w-100"
               style="border-radius:20px!important;">
                Compl&#233;ter maintenant
            </a>
        </div>
        <?php endif; ?>

        <div class="sidebar-r-card" style="padding:14px 16px;">
            <div style="font-size:11px;color:#999;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;">
                Stats Persomy
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid #f0f0f0;">
                <span style="color:#555;">Offres affich&#233;es</span>
                <strong style="color:#0A66C2;"><?= count($latestJobs) ?>+</strong>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;">
                <span style="color:#555;">Entreprises</span>
                <strong style="color:#0A66C2;"><?= count($topCompanies) ?>+</strong>
            </div>
        </div>

    </div>

</div>

<?php else: ?>
<!-- ====================================================================
     GUEST VIEW — Landing page
===================================================================== -->

<!-- Hero -->
<div class="landing-hero">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <div style="font-size:13px;font-weight:600;letter-spacing:.06em;opacity:.7;text-transform:uppercase;margin-bottom:12px;">
                <i class="bi bi-briefcase-fill me-2"></i>Votre r&#233;seau professionnel
            </div>
            <h1>Connectez-vous.<br>Postulez.<br><span style="color:#7dd3fc;">D&#233;crochez le job.</span></h1>
            <p class="lead mt-3">
                Rejoignez des milliers de professionnels et d&rsquo;entreprises sur Persomy &mdash;
                la plateforme qui met en relation les talents et les recruteurs.
            </p>
            <div class="hero-search-row">
                <input type="text" id="hs-kw"  class="hero-input" placeholder="Titre, comp&#233;tences, mots-cl&#233;s&#x2026;">
                <input type="text" id="hs-loc" class="hero-input" placeholder="Ville, pays&#x2026;">
                <button class="hero-btn"
                    onclick="location.href='<?= base_url('jobs') ?>?keyword='+encodeURIComponent(document.getElementById('hs-kw').value)+'&location='+encodeURIComponent(document.getElementById('hs-loc').value)">
                    <i class="bi bi-search me-1"></i>Rechercher
                </button>
            </div>
            <div class="mt-3" style="font-size:13px;opacity:.7;">
                <i class="bi bi-shield-check me-1"></i>Gratuit pour les chercheurs d&rsquo;emploi
                &nbsp;&middot;&nbsp;
                <i class="bi bi-people me-1"></i>10&nbsp;000+ professionnels inscrits
            </div>
        </div>
        <div class="col-lg-5 d-none d-lg-flex justify-content-center" style="position:relative;min-height:180px;">
            <div style="width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.08);position:absolute;top:-20px;right:0;"></div>
            <div style="width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.05);position:absolute;bottom:0;right:60px;"></div>
            <div style="display:flex;flex-direction:column;align-items:center;gap:12px;z-index:1;padding-top:10px;">
                <div style="background:rgba(255,255,255,.15);border-radius:16px;padding:20px 28px;backdrop-filter:blur(8px);text-align:center;">
                    <i class="bi bi-person-check-fill" style="font-size:2.5rem;"></i>
                    <div style="font-size:.85rem;font-weight:600;margin-top:6px;">Profil optimis&#233;</div>
                </div>
                <div style="display:flex;gap:12px;">
                    <div style="background:rgba(255,255,255,.12);border-radius:12px;padding:14px 18px;backdrop-filter:blur(8px);text-align:center;">
                        <i class="bi bi-building" style="font-size:1.6rem;"></i>
                        <div style="font-size:.75rem;font-weight:600;margin-top:4px;">Entreprises</div>
                    </div>
                    <div style="background:rgba(255,255,255,.12);border-radius:12px;padding:14px 18px;backdrop-filter:blur(8px);text-align:center;">
                        <i class="bi bi-bell-fill" style="font-size:1.6rem;"></i>
                        <div style="font-size:.75rem;font-weight:600;margin-top:4px;">Alertes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-blob" style="width:300px;height:300px;top:-80px;right:-80px;"></div>
    <div class="hero-blob" style="width:180px;height:180px;bottom:-50px;left:20%;"></div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-num">10K+</div>
        <div class="stat-lbl"><i class="bi bi-people me-1"></i>Professionnels</div>
    </div>
    <div class="stat-box">
        <div class="stat-num">500+</div>
        <div class="stat-lbl"><i class="bi bi-briefcase me-1"></i>Offres actives</div>
    </div>
    <div class="stat-box">
        <div class="stat-num">200+</div>
        <div class="stat-lbl"><i class="bi bi-building me-1"></i>Entreprises</div>
    </div>
    <div class="stat-box">
        <div class="stat-num">98%</div>
        <div class="stat-lbl"><i class="bi bi-star-fill me-1"></i>Satisfaction</div>
    </div>
</div>

<!-- How it works -->
<div class="text-center mb-5">
    <h4 class="fw-bold mb-1">Comment &#231;a marche</h4>
    <p class="text-muted" style="font-size:.95rem;">3 &#233;tapes simples pour trouver votre prochain emploi</p>
    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="d-flex flex-column align-items-center p-4">
                <div style="width:60px;height:60px;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <i class="bi bi-person-plus-fill" style="font-size:1.5rem;color:#0A66C2;"></i>
                </div>
                <h6 class="fw-bold">1. Cr&#233;ez votre profil</h6>
                <p class="text-muted text-center" style="font-size:.875rem;">Importez votre CV, ajoutez vos exp&#233;riences et comp&#233;tences en quelques minutes.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex flex-column align-items-center p-4">
                <div style="width:60px;height:60px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <i class="bi bi-search" style="font-size:1.5rem;color:#15803d;"></i>
                </div>
                <h6 class="fw-bold">2. Explorez les offres</h6>
                <p class="text-muted text-center" style="font-size:.875rem;">Parcourez des centaines d&rsquo;offres filtr&#233;es selon vos crit&#232;res et vos alertes.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex flex-column align-items-center p-4">
                <div style="width:60px;height:60px;background:#fefce8;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <i class="bi bi-send-fill" style="font-size:1.5rem;color:#d97706;"></i>
                </div>
                <h6 class="fw-bold">3. Postulez en 1 clic</h6>
                <p class="text-muted text-center" style="font-size:.875rem;">Envoyez votre candidature directement via la plateforme et suivez vos r&#233;ponses.</p>
            </div>
        </div>
    </div>
</div>

<!-- Latest jobs -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Derni&#232;res opportunit&#233;s</h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">Des offres fra&#238;chement publi&#233;es pour vous</p>
    </div>
    <a href="<?= base_url('jobs') ?>" class="btn btn-outline-primary btn-sm" style="border-radius:20px!important;">
        Tout voir <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>

<?php if (empty($latestJobs)): ?>
<div class="text-center py-5" style="color:#999;">
    <i class="bi bi-briefcase display-3 d-block mb-3 opacity-25"></i>
    <p>Aucune offre disponible pour le moment.</p>
    <a href="<?= base_url('jobs') ?>" class="btn btn-primary px-4">Parcourir les offres</a>
</div>
<?php else: ?>
<div class="row g-3 mb-5">
    <?php foreach ($latestJobs as $job): ?>
    <div class="col-md-6 col-lg-4">
        <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="land-job-card">
            <div class="d-flex align-items-center gap-3">
                <?php if (!empty($job->company_logo)): ?>
                    <img src="<?= base_url('uploads/logos/' . esc($job->company_logo)) ?>"
                         alt="" style="width:44px;height:44px;border-radius:8px;object-fit:cover;border:1px solid #e5e7eb;flex-shrink:0;">
                <?php else: ?>
                    <div style="width:44px;height:44px;border-radius:8px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-size:17px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <?= strtoupper(substr($job->company_name ?? 'C', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <div class="land-job-co"><?= esc($job->company_name) ?></div>
                    <div class="land-job-title"><?= esc($job->title) ?></div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-1 mt-3">
                <span class="feed-badge cdi"><?= esc($job->contract_type) ?></span>
                <?php if (!empty($job->location)): ?>
                <span class="feed-badge"><i class="bi bi-geo-alt me-1"></i><?= esc($job->location) ?></span>
                <?php endif; ?>
                <?php if (!empty($job->remote) && $job->remote !== 'onsite'): ?>
                <span class="feed-badge remote"><?= ucfirst(esc($job->remote)) ?></span>
                <?php endif; ?>
            </div>
            <?php if (!empty($job->salary_min)): ?>
            <div class="mt-2 fw-semibold" style="color:#15803d;font-size:.82rem;">
                <i class="bi bi-cash me-1"></i>
                <?= number_format($job->salary_min) ?><?= !empty($job->salary_max) ? '&ndash;'.number_format($job->salary_max) : '+' ?>&nbsp;<?= esc($job->salary_currency ?? 'MAD') ?>/an
            </div>
            <?php endif; ?>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- CTA cards -->
<div class="row g-4 mb-2">
    <div class="col-md-6">
        <div class="cta-card cta-seeker">
            <i class="bi bi-person-badge-fill" style="font-size:2.2rem;opacity:.9;"></i>
            <h4 class="mt-3">Vous cherchez un emploi&nbsp;?</h4>
            <p>Cr&#233;ez votre profil professionnel, importez votre CV et acc&#233;dez &#224; des centaines d&rsquo;offres cibl&#233;es.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('register') ?>" class="btn btn-light fw-bold px-4">
                    <i class="bi bi-rocket-takeoff me-1"></i>Commencer gratuitement
                </a>
                <a href="<?= base_url('jobs') ?>" class="btn btn-outline-light px-4">Voir les offres</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="cta-card cta-recruiter">
            <i class="bi bi-building-fill-check" style="font-size:2.2rem;opacity:.9;"></i>
            <h4 class="mt-3">Vous recrutez&nbsp;?</h4>
            <p>Publiez vos offres, acc&#233;dez &#224; des profils qualifi&#233;s et trouvez les meilleurs talents en quelques jours.</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('register') ?>" class="btn btn-light fw-bold px-4">
                    <i class="bi bi-plus-circle me-1"></i>Publier une offre
                </a>
                <a href="<?= base_url('login') ?>" class="btn btn-outline-light px-4">Se connecter</a>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>