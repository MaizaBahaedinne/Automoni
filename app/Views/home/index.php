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

/* ── Create post card ───────────────────────────────────────────────────── */
.feed-create-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.09);
    padding: 14px 16px 12px;
    margin-bottom: 10px;
}
.feed-create-input {
    flex-grow: 1;
    background: #f3f2f0;
    border: 1px solid #e0e0e0;
    border-radius: 24px;
    padding: 9px 18px;
    font-size: 13.5px;
    color: #888;
    cursor: pointer;
    text-align: left;
    transition: background .12s, border-color .12s;
}
.feed-create-input:hover { background: #e9e8e6; border-color: #bbb; }
.feed-type-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: none; border: none;
    padding: 7px 14px;
    font-size: 13px; font-weight: 600; color: #666;
    border-radius: 6px;
    cursor: pointer;
    transition: background .12s;
}
.feed-type-btn:hover { background: #f3f2f0; color: #333; }
.feed-type-divider { height: 1px; background: #f0f0f0; margin: 10px 0 8px; }

/* ── Post card extras (on top of .feed-post base) ───────────────────────── */
.feed-post-media { line-height: 0; }
.feed-post-img   { width: 100%; max-height: 460px; object-fit: cover; }
.feed-post-video-wrap {
    position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;
    background: #000;
}
.feed-post-video-wrap iframe,
.feed-post-video-wrap video {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    border: none;
}
.feed-post-content { padding: 10px 16px 12px; font-size: 13.5px; color: #333; line-height: 1.65; white-space: pre-wrap; }

/* ── Announcement banner ────────────────────────────────────────────────── */
.announce-banner {
    display: flex; align-items: center; gap: 14px;
    margin: 0 16px 12px;
    padding: 14px 16px;
    border-radius: 10px;
    font-size: 14px; font-weight: 600;
}
.announce-banner .announce-icon { font-size: 2rem; line-height: 1; flex-shrink: 0; }
.announce-banner .announce-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; opacity: .75; margin-bottom: 2px; }
.announce-banner .announce-title { font-size: 15px; font-weight: 700; }
.announce-new_job      { background: #ecfdf5; color: #065f46; }
.announce-open_to_work { background: #eff6ff; color: #1e40af; }
.announce-certification{ background: #fefce8; color: #78350f; }
.announce-promotion    { background: #f5f3ff; color: #4c1d95; }
.announce-other        { background: #f8f9fa; color: #374151; }

/* ── Job card in feed (with "Offre" badge) ──────────────────────────────── */
.feed-job-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: #eff6ff; color: #1d4ed8;
    font-size: 11px; font-weight: 700;
    padding: 2px 8px; border-radius: 20px;
    border: 1px solid #bfdbfe;
    margin-bottom: 6px;
}

/* ── Reactions / comments bar ───────────────────────────────────────────── */
.feed-reactions-bar {
    display: flex; align-items: center; gap: 4px;
    padding: 4px 16px 10px;
    font-size: 12px; color: #999;
    border-bottom: 1px solid #f0f0f0;
}
.feed-reactions-bar i { font-size: 14px; color: #0A66C2; }
.feed-action-bar {
    display: flex; align-items: stretch;
    padding: 2px 8px 4px;
}
.feed-action-btn {
    flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px;
    background: none; border: none;
    padding: 8px;
    font-size: 13px; font-weight: 600; color: #666;
    border-radius: 6px; cursor: pointer;
    transition: background .12s, color .12s;
}
.feed-action-btn:hover { background: #f3f2f0; color: #333; }
.feed-action-btn.liked { color: #0A66C2; }
.feed-action-btn.liked i::before { font-weight: 900; }

/* ── Comments section ───────────────────────────────────────────────────── */
.feed-comments-section {
    border-top: 1px solid #f0f0f0;
    padding: 12px 16px;
    background: #fafafa;
    border-radius: 0 0 10px 10px;
}
.post-cmt-row { display: flex; gap: 8px; margin-bottom: 10px; }
.post-cmt-av  { width: 32px; height: 32px; min-width: 32px; border-radius: 50%; object-fit: cover; }
.post-cmt-av-init {
    width: 32px; height: 32px; min-width: 32px;
    border-radius: 50%;
    background: #4f46e5; color: #fff; font-size: 13px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}
.post-cmt-bubble { flex: 1; }
.post-cmt-bubble strong { font-size: 12.5px; color: #111; }
.post-cmt-time { font-size: 11px; color: #aaa; margin-left: 4px; }
.post-cmt-text { font-size: 13px; color: #444; margin-top: 2px; line-height: 1.5; }
.post-cmt-input-row { display: flex; gap: 8px; margin-top: 8px; }
.post-cmt-input {
    flex: 1; background: #fff; border: 1px solid #ddd; border-radius: 20px;
    padding: 7px 14px; font-size: 13px; color: #333; outline: none;
    transition: border-color .15s;
}
.post-cmt-input:focus { border-color: #0A66C2; }
.post-cmt-send {
    background: #0A66C2; color: #fff; border: none; border-radius: 20px;
    padding: 7px 16px; font-size: 13px; font-weight: 600; cursor: pointer;
    transition: background .12s;
}
.post-cmt-send:hover { background: #084e96; }
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

    <?php $csrfName = csrf_token(); $csrfHash = csrf_hash(); ?>
    <script>let CSRF={name:'<?= $csrfName ?>',hash:'<?= $csrfHash ?>'};</script>

        <!-- Create post quick card -->
        <div class="feed-create-card">
            <div class="d-flex align-items-center gap-3">
                <?php if ($avatarSrc): ?>
                    <img src="<?= $avatarSrc ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;" alt="">
                <?php else: ?>
                    <div style="width:40px;height:40px;border-radius:50%;background:#4f46e5;color:#fff;font-size:17px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?></div>
                <?php endif; ?>
                <button class="feed-create-input" onclick="openPostModal('text')">
                    Partagez quelque chose, <?= esc(explode(' ', $fullName)[0]) ?>&nbsp;&hellip;
                </button>
            </div>
            <div class="feed-type-divider"></div>
            <div class="d-flex gap-1 flex-wrap">
                <button class="feed-type-btn" onclick="openPostModal('image')">
                    <i class="bi bi-image" style="color:#15803d;"></i> Photo
                </button>
                <button class="feed-type-btn" onclick="openPostModal('video')">
                    <i class="bi bi-play-circle-fill" style="color:#dc2626;"></i> Vid&eacute;o
                </button>
                <button class="feed-type-btn" onclick="openPostModal('announcement')">
                    <i class="bi bi-megaphone-fill" style="color:#d97706;"></i> Annonce
                </button>
                <button class="feed-type-btn" onclick="openPostModal('text')">
                    <i class="bi bi-pencil-square" style="color:#0A66C2;"></i> Texte
                </button>
            </div>
        </div>

        <!-- Search bar -->
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

        <?php if (empty($posts) && empty($latestJobs)): ?>
        <div class="feed-post" style="padding:48px 40px;text-align:center;color:#aaa;">
            <i class="bi bi-wind" style="font-size:3rem;display:block;margin-bottom:12px;opacity:.25;"></i>
            <p class="mb-1 fw-semibold" style="color:#666;">Votre fil est vide pour l&rsquo;instant.</p>
            <p style="font-size:13px;">Soyez le premier &agrave; publier quelque chose&nbsp;!</p>
        </div>

        <?php else:
        $jobIdx   = 0;
        $jobCount = count($latestJobs);
        $announceConfig = [
            'new_job'       => ['icon' => '&#127881;', 'label' => 'Nouveau poste',    'cls' => 'announce-new_job'],
            'open_to_work'  => ['icon' => '&#128269;', 'label' => 'Disponible',       'cls' => 'announce-open_to_work'],
            'certification' => ['icon' => '&#127942;', 'label' => 'Certification',    'cls' => 'announce-certification'],
            'promotion'     => ['icon' => '&#128640;', 'label' => 'Promotion',        'cls' => 'announce-promotion'],
            'other'         => ['icon' => '&#128226;', 'label' => 'Annonce',          'cls' => 'announce-other'],
        ];
        ?>

        <?php foreach ($posts as $pIdx => $post):

            /* ---- Interleave job card every 4 user posts ---- */
            if ($pIdx > 0 && $pIdx % 4 === 0 && $jobIdx < $jobCount):
                $job = $latestJobs[$jobIdx++];
                try { $jd = (new DateTime($job->created_at))->diff(new DateTime()); $jAgo = ($jd->days > 0 ? $jd->days.'j' : ($jd->h > 0 ? $jd->h.'h' : '1h')) . ' ago'; } catch (\Exception $e) { $jAgo = ''; }
        ?>
        <div class="feed-post">
            <div style="padding:8px 16px 0;"><span class="feed-job-badge"><i class="bi bi-briefcase-fill"></i>&nbsp;Offre d&rsquo;emploi</span></div>
            <div class="feed-post-head">
                <?php if (!empty($job->company_logo)): ?><img src="<?= base_url('uploads/logos/' . esc($job->company_logo)) ?>" alt="" class="feed-co-icon"><?php else: ?><div class="feed-co-init"><?= strtoupper(substr($job->company_name ?? 'C', 0, 1)) ?></div><?php endif; ?>
                <div class="flex-grow-1">
                    <div class="feed-post-title"><a href="<?= base_url('jobs/' . esc($job->slug)) ?>"><?= esc($job->title) ?></a></div>
                    <div class="feed-post-sub"><?php if (!empty($job->company_slug)): ?><a href="<?= base_url('companies/' . esc($job->company_slug)) ?>" style="color:inherit;text-decoration:none;font-weight:600;"><?= esc($job->company_name) ?></a><?php else: ?><strong><?= esc($job->company_name) ?></strong><?php endif; ?><?php if (!empty($job->location)): ?> &middot; <?= esc($job->location) ?><?php endif; ?></div>
                    <?php if ($jAgo): ?><div class="feed-post-time"><i class="bi bi-clock me-1"></i><?= $jAgo ?></div><?php endif; ?>
                </div>
                <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="btn btn-sm btn-outline-primary align-self-start flex-shrink-0" style="border-radius:20px!important;font-size:12px;padding:4px 14px;">Postuler</a>
            </div>
            <?php if (!empty($job->description)): ?><div class="feed-post-body"><?= esc(mb_substr(strip_tags($job->description), 0, 180)) ?><?= mb_strlen(strip_tags((string)$job->description)) > 180 ? '&hellip;' : '' ?></div><?php endif; ?>
            <div class="feed-post-footer">
                <span class="feed-badge cdi"><?= esc($job->contract_type) ?></span>
                <?php if (!empty($job->remote) && $job->remote !== 'onsite'): ?><span class="feed-badge remote"><?= ucfirst(esc($job->remote)) ?></span><?php endif; ?>
                <?php if (!empty($job->salary_min)): ?><?php $_sp=['annual'=>'/an','monthly'=>'/mois','daily'=>'/jour','hourly'=>'/h']; ?><span class="feed-badge salary"><i class="bi bi-cash me-1"></i><?= number_format($job->salary_min) ?><?= !empty($job->salary_max) ? '&ndash;'.number_format($job->salary_max) : '+' ?> <?= esc($job->salary_currency ?? 'MAD') ?><?= $_sp[$job->salary_period ?? 'annual'] ?? '/an' ?></span><?php endif; ?>
                <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="ms-auto" style="font-size:12.5px;color:#0A66C2;text-decoration:none;font-weight:500;">Voir l&rsquo;offre &#8594;</a>
            </div>
        </div>
            <?php endif; /* end interleave */ ?>

        <!-- ── User post ──────────────────────────────────────────── -->
        <?php
        $isOwner  = ((int)$post->user_id === $userId);
        $postAv   = !empty($post->avatar) ? base_url('uploads/' . esc($post->avatar)) : null;
        $postName = esc(trim(($post->first_name ?? '') . ' ' . ($post->last_name ?? '')));
        $postHead = esc($post->headline ?? $post->user_position ?? '');
        try { $pd = (new DateTime($post->created_at))->diff(new DateTime()); $pAgo = $pd->days > 0 ? $pd->days.'j' : ($pd->h > 0 ? $pd->h.'h' : max(1,$pd->i).'min'); } catch (\Exception $e) { $pAgo = ''; }
        $hasReacted = isset($myReactions[$post->id]);
        $ytId = null;
        if ($post->type === 'video' && !empty($post->video_url)) {
            preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $post->video_url, $ytM);
            $ytId = $ytM[1] ?? null;
        }
        $ac = ($post->type === 'announcement') ? ($announceConfig[$post->announcement_subtype] ?? $announceConfig['other']) : null;
        ?>
        <div class="feed-post" id="post-<?= $post->id ?>">

            <div class="feed-post-head">
                <?php if ($postAv): ?><img src="<?= $postAv ?>" style="width:46px;height:46px;min-width:46px;border-radius:50%;object-fit:cover;" alt=""><?php else: ?><div class="feed-co-init" style="border-radius:50%!important;"><?= strtoupper(substr($post->first_name ?? 'U', 0, 1)) ?></div><?php endif; ?>
                <div class="flex-grow-1">
                    <div class="feed-post-title"><?= $postName ?></div>
                    <?php if ($postHead): ?><div class="feed-post-sub"><?= $postHead ?></div><?php endif; ?>
                    <?php if ($pAgo): ?><div class="feed-post-time"><i class="bi bi-clock me-1"></i><?= $pAgo ?></div><?php endif; ?>
                </div>
                <?php if ($isOwner): ?>
                <form action="<?= base_url('posts/' . $post->id . '/delete') ?>" method="post" style="display:inline;"
                      onsubmit="return confirm('Supprimer cette publication ?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-light" style="padding:3px 8px;font-size:12px;color:#999;" title="Supprimer">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <?php if ($post->type === 'announcement' && $ac): ?>
            <div class="announce-banner <?= $ac['cls'] ?>">
                <span class="announce-icon"><?= $ac['icon'] ?></span>
                <div>
                    <div class="announce-label"><?= $ac['label'] ?></div>
                    <?php if (!empty($post->content)): ?><div class="announce-title"><?= esc($post->content) ?></div><?php endif; ?>
                </div>
            </div>
            <?php elseif (!empty($post->content)): ?>
            <div class="feed-post-content"><?= nl2br(esc($post->content)) ?></div>
            <?php endif; ?>

            <?php if ($post->type === 'image' && !empty($post->media_file)): ?>
            <div class="feed-post-media">
                <img src="<?= base_url('uploads/posts/' . esc($post->media_file)) ?>" class="feed-post-img" alt="" loading="lazy">
            </div>
            <?php elseif ($post->type === 'video'): ?>
            <div class="feed-post-media">
                <div class="feed-post-video-wrap">
                    <?php if ($ytId): ?>
                    <iframe src="https://www.youtube-nocookie.com/embed/<?= esc($ytId) ?>" allowfullscreen loading="lazy" title="Video"></iframe>
                    <?php elseif (!empty($post->media_file)): ?>
                    <video controls preload="metadata">
                        <source src="<?= base_url('uploads/posts/' . esc($post->media_file)) ?>">
                    </video>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($post->reactions_count > 0 || $post->comments_count > 0): ?>
            <div class="feed-reactions-bar">
                <?php if ($post->reactions_count > 0): ?><i class="bi bi-hand-thumbs-up-fill"></i><span><?= (int)$post->reactions_count ?></span><?php endif; ?>
                <?php if ($post->comments_count > 0): ?><span class="ms-auto" style="cursor:pointer;" onclick="toggleComments(<?= $post->id ?>)"><?= (int)$post->comments_count ?> commentaire<?= $post->comments_count > 1 ? 's' : '' ?></span><?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="feed-action-bar">
                <button class="feed-action-btn <?= $hasReacted ? 'liked' : '' ?>" id="react-btn-<?= $post->id ?>" onclick="toggleReaction(<?= $post->id ?>)">
                    <i class="bi bi-hand-thumbs-up<?= $hasReacted ? '-fill' : '' ?>"></i>
                    J&rsquo;aime <span id="react-count-<?= $post->id ?>"><?= $post->reactions_count > 0 ? '('.(int)$post->reactions_count.')' : '' ?></span>
                </button>
                <button class="feed-action-btn" onclick="toggleComments(<?= $post->id ?>)">
                    <i class="bi bi-chat"></i>
                    Commenter <span id="cmt-count-<?= $post->id ?>"><?= $post->comments_count > 0 ? '('.(int)$post->comments_count.')' : '' ?></span>
                </button>
                <button class="feed-action-btn" onclick="navigator.share ? navigator.share({url:'<?= base_url('') ?>'}) : void 0">
                    <i class="bi bi-share"></i> Partager
                </button>
            </div>

            <div class="feed-comments-section" id="cmt-section-<?= $post->id ?>" style="display:none;">
                <div id="cmt-list-<?= $post->id ?>"></div>
                <div class="post-cmt-input-row">
                    <?php if ($avatarSrc): ?><img src="<?= $avatarSrc ?>" class="post-cmt-av" alt=""><?php else: ?><div class="post-cmt-av post-cmt-av-init"><?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?></div><?php endif; ?>
                    <input type="text" class="post-cmt-input" id="cmt-input-<?= $post->id ?>"
                           placeholder="Ajouter un commentaire&hellip;"
                           onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();submitComment(<?= $post->id ?>);}">
                    <button class="post-cmt-send" onclick="submitComment(<?= $post->id ?>)"><i class="bi bi-send-fill"></i></button>
                </div>
            </div>

        </div>
        <?php endforeach; ?>

        <?php while ($jobIdx < $jobCount):
            $job = $latestJobs[$jobIdx++];
            try { $jd = (new DateTime($job->created_at))->diff(new DateTime()); $jAgo = ($jd->days > 0 ? $jd->days.'j' : ($jd->h > 0 ? $jd->h.'h' : '1h')) . ' ago'; } catch (\Exception $e) { $jAgo = ''; }
        ?>
        <div class="feed-post">
            <div style="padding:8px 16px 0;"><span class="feed-job-badge"><i class="bi bi-briefcase-fill"></i>&nbsp;Offre d&rsquo;emploi</span></div>
            <div class="feed-post-head">
                <?php if (!empty($job->company_logo)): ?><img src="<?= base_url('uploads/logos/' . esc($job->company_logo)) ?>" alt="" class="feed-co-icon"><?php else: ?><div class="feed-co-init"><?= strtoupper(substr($job->company_name ?? 'C', 0, 1)) ?></div><?php endif; ?>
                <div class="flex-grow-1">
                    <div class="feed-post-title"><a href="<?= base_url('jobs/' . esc($job->slug)) ?>"><?= esc($job->title) ?></a></div>
                    <div class="feed-post-sub"><?php if (!empty($job->company_slug)): ?><a href="<?= base_url('companies/' . esc($job->company_slug)) ?>" style="color:inherit;text-decoration:none;font-weight:600;"><?= esc($job->company_name) ?></a><?php else: ?><strong><?= esc($job->company_name) ?></strong><?php endif; ?><?php if (!empty($job->location)): ?> &middot; <?= esc($job->location) ?><?php endif; ?></div>
                    <?php if ($jAgo): ?><div class="feed-post-time"><i class="bi bi-clock me-1"></i><?= $jAgo ?></div><?php endif; ?>
                </div>
                <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="btn btn-sm btn-outline-primary align-self-start flex-shrink-0" style="border-radius:20px!important;font-size:12px;padding:4px 14px;">Postuler</a>
            </div>
            <?php if (!empty($job->description)): ?><div class="feed-post-body"><?= esc(mb_substr(strip_tags($job->description), 0, 180)) ?><?= mb_strlen(strip_tags((string)$job->description)) > 180 ? '&hellip;' : '' ?></div><?php endif; ?>
            <div class="feed-post-footer">
                <span class="feed-badge cdi"><?= esc($job->contract_type) ?></span>
                <?php if (!empty($job->remote) && $job->remote !== 'onsite'): ?><span class="feed-badge remote"><?= ucfirst(esc($job->remote)) ?></span><?php endif; ?>
                <?php if (!empty($job->salary_min)): ?><?php $_sp=['annual'=>'/an','monthly'=>'/mois','daily'=>'/jour','hourly'=>'/h']; ?><span class="feed-badge salary"><i class="bi bi-cash me-1"></i><?= number_format($job->salary_min) ?><?= !empty($job->salary_max) ? '&ndash;'.number_format($job->salary_max) : '+' ?> <?= esc($job->salary_currency ?? 'MAD') ?><?= $_sp[$job->salary_period ?? 'annual'] ?? '/an' ?></span><?php endif; ?>
                <a href="<?= base_url('jobs/' . esc($job->slug)) ?>" class="ms-auto" style="font-size:12.5px;color:#0A66C2;text-decoration:none;font-weight:500;">Voir l&rsquo;offre &#8594;</a>
            </div>
        </div>
        <?php endwhile; ?>

        <div class="text-center py-3">
            <a href="<?= base_url('jobs') ?>" class="btn btn-outline-primary px-5" style="border-radius:20px!important;">
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

<!-- ====================================================================
     CREATE POST MODAL
===================================================================== -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0;padding:16px 20px;">
                <h6 class="modal-title fw-bold" id="postModalLabel">Cr&eacute;er une publication</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Type selector pills -->
            <div class="d-flex gap-2 flex-wrap px-4 pt-3 pb-2" style="border-bottom:1px solid #f0f0f0;">
                <button class="feed-type-btn post-type-pill active" data-type="text"      onclick="switchPostType('text')">      <i class="bi bi-pencil-square"   style="color:#0A66C2;"></i>&nbsp;Texte</button>
                <button class="feed-type-btn post-type-pill"        data-type="image"     onclick="switchPostType('image')">     <i class="bi bi-image"           style="color:#15803d;"></i>&nbsp;Photo</button>
                <button class="feed-type-btn post-type-pill"        data-type="video"     onclick="switchPostType('video')">     <i class="bi bi-play-circle-fill" style="color:#dc2626;"></i>&nbsp;Vid&eacute;o</button>
                <button class="feed-type-btn post-type-pill"        data-type="announcement" onclick="switchPostType('announcement')"><i class="bi bi-megaphone-fill" style="color:#d97706;"></i>&nbsp;Annonce</button>
            </div>

            <form action="<?= base_url('posts/store') ?>" method="post" enctype="multipart/form-data" id="postForm">
                <?= csrf_field() ?>
                <input type="hidden" name="type" id="postType" value="text">

                <div class="modal-body" style="padding:20px 24px;">

                    <!-- Author row -->
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <?php if ($avatarSrc): ?>
                            <img src="<?= $avatarSrc ?>" style="width:44px;height:44px;border-radius:50%;object-fit:cover;" alt="">
                        <?php else: ?>
                            <div style="width:44px;height:44px;border-radius:50%;background:#4f46e5;color:#fff;font-size:18px;font-weight:800;display:flex;align-items:center;justify-content:center;"><?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?></div>
                        <?php endif; ?>
                        <div>
                            <div style="font-size:15px;font-weight:700;"><?= esc(session()->get('user_name') ?? '') ?></div>
                            <div style="font-size:12px;color:#888;"><i class="bi bi-globe2 me-1"></i>Visible par tous</div>
                        </div>
                    </div>

                    <!-- Content textarea -->
                    <textarea name="content" id="postContent" rows="5"
                              class="form-control border-0 bg-transparent"
                              style="resize:none;font-size:15px;color:#222;padding:0;"
                              placeholder="Que souhaitez-vous partager ?"></textarea>

                    <!-- Announcement subtype selector (visible only for announcement) -->
                    <div id="announceSubtypeWrap" style="display:none;margin-top:16px;">
                        <label class="form-label small fw-semibold">Type d&rsquo;annonce</label>
                        <div class="d-flex flex-wrap gap-2">
                            <label class="announce-pill" style="cursor:pointer;">
                                <input type="radio" name="announcement_subtype" value="new_job" style="display:none;">
                                <span class="announce-pill-btn announce-new_job">&#127881; Nouveau poste</span>
                            </label>
                            <label class="announce-pill" style="cursor:pointer;">
                                <input type="radio" name="announcement_subtype" value="open_to_work" style="display:none;">
                                <span class="announce-pill-btn announce-open_to_work">&#128269; Disponible</span>
                            </label>
                            <label class="announce-pill" style="cursor:pointer;">
                                <input type="radio" name="announcement_subtype" value="certification" style="display:none;">
                                <span class="announce-pill-btn announce-certification">&#127942; Certification</span>
                            </label>
                            <label class="announce-pill" style="cursor:pointer;">
                                <input type="radio" name="announcement_subtype" value="promotion" style="display:none;">
                                <span class="announce-pill-btn announce-promotion">&#128640; Promotion</span>
                            </label>
                            <label class="announce-pill" style="cursor:pointer;">
                                <input type="radio" name="announcement_subtype" value="other" checked style="display:none;">
                                <span class="announce-pill-btn announce-other">&#128226; Autre</span>
                            </label>
                        </div>
                    </div>

                    <!-- Image upload -->
                    <div id="imageUploadWrap" style="display:none;margin-top:14px;">
                        <label class="form-label small fw-semibold">Image (JPG, PNG, GIF &bull; max 10&nbsp;Mo)</label>
                        <input type="file" name="media_file" id="imageFileInput" class="form-control form-control-sm"
                               accept="image/jpeg,image/png,image/gif,image/webp">
                        <div id="imagePreview" style="margin-top:10px;display:none;">
                            <img id="imagePreviewImg" src="" style="max-width:100%;max-height:300px;border-radius:8px;object-fit:contain;" alt="">
                        </div>
                    </div>

                    <!-- Video upload / URL -->
                    <div id="videoUploadWrap" style="display:none;margin-top:14px;">
                        <label class="form-label small fw-semibold">Lien YouTube</label>
                        <input type="url" name="video_url" class="form-control form-control-sm mb-2"
                               placeholder="https://www.youtube.com/watch?v=...">
                        <label class="form-label small fw-semibold">ou Fichier vid&eacute;o (MP4/WebM &bull; max 200&nbsp;Mo)</label>
                        <input type="file" name="media_file" id="videoFileInput" class="form-control form-control-sm"
                               accept="video/mp4,video/webm,video/ogg">
                    </div>

                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0;padding:12px 20px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius:20px!important;">
                        <i class="bi bi-send-fill me-1"></i>Publier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.post-type-pill.active { background: #eff6ff; color: #1d4ed8; }
.announce-pill-btn {
    display: inline-block; padding: 5px 12px; border-radius: 20px;
    font-size: 13px; font-weight: 600; cursor: pointer; border: 1.5px solid transparent;
    transition: all .12s;
}
input[type="radio"]:checked + .announce-pill-btn { border-color: currentColor; font-weight: 700; }
</style>

<script>
(function(){
    /* ── Post modal ─────────────────────────────────────────────── */
    window.openPostModal = function(type) {
        switchPostType(type);
        new bootstrap.Modal(document.getElementById('postModal')).show();
    };

    window.switchPostType = function(type) {
        document.getElementById('postType').value = type;
        document.getElementById('announceSubtypeWrap').style.display = type === 'announcement' ? '' : 'none';
        document.getElementById('imageUploadWrap').style.display     = type === 'image'        ? '' : 'none';
        document.getElementById('videoUploadWrap').style.display     = type === 'video'        ? '' : 'none';
        var ph = {
            text:         'Quoi de neuf ? Partagez vos r&eacute;flexions\u2026',
            image:        'D&eacute;crivez votre photo\u2026',
            video:        'D&eacute;crivez votre vid&eacute;o\u2026',
            announcement: 'Pr&eacute;cisez votre annonce\u2026'
        };
        document.getElementById('postContent').placeholder = ph[type] || ph.text;
        document.querySelectorAll('.post-type-pill').forEach(function(b){
            b.classList.toggle('active', b.dataset.type === type);
        });
    };

    // Image preview
    var imgInput = document.getElementById('imageFileInput');
    if (imgInput) {
        imgInput.addEventListener('change', function() {
            var f = this.files[0];
            if (!f) return;
            var r = new FileReader();
            r.onload = function(e) {
                document.getElementById('imagePreviewImg').src = e.target.result;
                document.getElementById('imagePreview').style.display = '';
            };
            r.readAsDataURL(f);
        });
    }

    /* ── Reactions ──────────────────────────────────────────────── */
    window.toggleReaction = function(postId) {
        fetch('<?= base_url('posts/') ?>' + postId + '/react', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: CSRF.name + '=' + encodeURIComponent(CSRF.hash) + '&type=like'
        })
        .then(function(r){ return r.json(); })
        .then(function(d) {
            var btn = document.getElementById('react-btn-' + postId);
            var cnt = document.getElementById('react-count-' + postId);
            if (!btn) return;
            if (d.reacted) {
                btn.classList.add('liked');
                btn.querySelector('i').className = 'bi bi-hand-thumbs-up-fill';
            } else {
                btn.classList.remove('liked');
                btn.querySelector('i').className = 'bi bi-hand-thumbs-up';
            }
            cnt.textContent = d.count > 0 ? '(' + d.count + ')' : '';
        })
        .catch(function(){});
    };

    /* ── Comments ───────────────────────────────────────────────── */
    var cmtOpen = {};
    window.toggleComments = function(postId) {
        var section = document.getElementById('cmt-section-' + postId);
        if (!section) return;
        if (cmtOpen[postId]) {
            section.style.display = 'none';
            cmtOpen[postId] = false;
        } else {
            section.style.display = '';
            cmtOpen[postId] = true;
            if (!section.dataset.loaded) {
                fetch('<?= base_url('posts/') ?>' + postId + '/comments', {
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                })
                .then(function(r){ return r.json(); })
                .then(function(d) {
                    document.getElementById('cmt-list-' + postId).innerHTML = d.html;
                    section.dataset.loaded = '1';
                })
                .catch(function(){});
            }
        }
    };

    window.submitComment = function(postId) {
        var input = document.getElementById('cmt-input-' + postId);
        var val = (input && input.value) ? input.value.trim() : '';
        if (!val) return;
        input.disabled = true;
        fetch('<?= base_url('posts/') ?>' + postId + '/comment', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: CSRF.name + '=' + encodeURIComponent(CSRF.hash) + '&content=' + encodeURIComponent(val)
        })
        .then(function(r){ return r.json(); })
        .then(function(d) {
            document.getElementById('cmt-list-' + postId).innerHTML = d.html;
            var section = document.getElementById('cmt-section-' + postId);
            if (section) section.dataset.loaded = '1';
            var cnt = document.getElementById('cmt-count-' + postId);
            if (cnt) cnt.textContent = d.count > 0 ? '(' + d.count + ')' : '';
            input.value = '';
            input.disabled = false;
            input.focus();
        })
        .catch(function(){ input.disabled = false; });
    };
})();
</script>

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
            <?php if (!empty($job->salary_min)): ?><?php $_sp=['annual'=>'/an','monthly'=>'/mois','daily'=>'/jour','hourly'=>'/h']; ?>
            <div class="mt-2 fw-semibold" style="color:#15803d;font-size:.82rem;">
                <i class="bi bi-cash me-1"></i>
                <?= number_format($job->salary_min) ?><?= !empty($job->salary_max) ? '&ndash;'.number_format($job->salary_max) : '+' ?>&nbsp;<?= esc($job->salary_currency ?? 'MAD') ?><?= $_sp[$job->salary_period ?? 'annual'] ?? '/an' ?>
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