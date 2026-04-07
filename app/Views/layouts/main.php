<?php
$locale  = session()->get('locale') ?? 'en';
$isRtl   = ($locale === 'ar');
$dir     = $isRtl ? 'rtl' : 'ltr';
$bsCss   = $isRtl
    ? 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css'
    : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css';
$arabicFont = $isRtl ? "https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" : '';
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Persomy') ?> — Persomy</title>
    <link href="<?= $bsCss ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <?php if ($isRtl): ?>
    <link href="<?= $arabicFont ?>" rel="stylesheet">
    <?php endif; ?>

    <style>
        :root {
            --brand:        #6366f1;
            --brand-dark:   #4f46e5;
            --brand-light:  #eef2ff;
            --surface:      #ffffff;
            --bg:           #f1f5f9;
            --nav-h:        64px;
            --footer-h:     64px;
            --text:         #0f172a;
            --muted:        #64748b;
            --border:       #e2e8f0;
            --radius:       12px;
            --shadow:       0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
            --shadow-lg:    0 8px 32px rgba(99,102,241,.18);
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: <?= $isRtl ? "'Cairo'" : "'Inter'" ?>, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: var(--nav-h);
        }

        /* ── Fixed Navbar ─────────────────────────────────────────── */
        .app-nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--nav-h);
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border-bottom: 1px solid var(--border);
            z-index: 1030;
            transition: box-shadow .2s;
        }
        .app-nav.scrolled { box-shadow: 0 2px 20px rgba(0,0,0,.10); }
        .app-nav .container {
            height: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-brand {
            font-size: 1.35rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--brand-dark), #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .nav-brand .brand-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--brand-dark), #7c3aed);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: .9rem;
            flex-shrink: 0;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
            margin: 0 auto 0 24px;
            list-style: none;
            padding: 0;
        }
        .nav-links a {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            color: var(--muted);
            text-decoration: none;
            font-size: .68rem;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 8px;
            transition: all .15s;
            white-space: nowrap;
            line-height: 1.1;
        }
        .nav-links a i { font-size: 1.15rem; display: block; }
        .nav-links a:hover { background: var(--brand-light); color: var(--brand-dark); }
        .nav-links a.active { background: var(--brand-light); color: var(--brand-dark); font-weight: 700; }
        /* Notification bell */
        .nav-notif-btn {
            position: relative;
            width: 36px; height: 36px;
            border: none; background: none;
            color: var(--muted);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem;
            transition: background .15s, color .15s;
            cursor: pointer;
            flex-shrink: 0;
        }
        .nav-notif-btn:hover { background: var(--brand-light); color: var(--brand); }
        .nav-notif-badge {
            position: absolute;
            top: 3px; right: 3px;
            width: 8px; height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 1.5px solid #fff;
        }

        /* Search bar */
        .nav-search {
            position: relative;
            display: flex;
            align-items: center;
        }
        .nav-search-icon {
            position: absolute;
            <?= $isRtl ? 'right' : 'left' ?>: 10px;
            color: var(--muted);
            font-size: .85rem;
            pointer-events: none;
        }
        .nav-search-input {
            border: 1.5px solid var(--border);
            border-radius: 20px;
            padding: 7px <?= $isRtl ? '16px' : '36px' ?> 7px <?= $isRtl ? '36px' : '16px' ?>;
            font-size: .82rem;
            background: #fff;
            color: var(--text);
            width: 210px;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
            transition: width .2s, border-color .15s, box-shadow .15s;
            outline: none;
        }
        .nav-search-input:focus {
            border-color: var(--brand);
            width: 270px;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        .nav-search-input::placeholder { color: var(--muted); }
        @media (max-width: 767px) { .nav-search { display: none; } }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-<?= $isRtl ? 'right' : 'left' ?>: auto;
        }

        /* Lang switcher */
        .lang-switcher {
            display: flex;
            align-items: center;
            gap: 2px;
            background: var(--bg);
            border-radius: 8px;
            padding: 3px;
            border: 1px solid var(--border);
        }
        .lang-btn {
            font-size: .75rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            color: var(--muted);
            text-decoration: none;
            transition: all .15s;
        }
        .lang-btn:hover { background: #fff; color: var(--brand-dark); }
        .lang-btn.active { background: var(--brand-dark); color: #fff; }

        /* User avatar dropdown */
        .user-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 5px 12px 5px 6px;
            font-size: .875rem;
            font-weight: 600;
            color: var(--text);
            cursor: pointer;
            text-decoration: none;
            transition: all .15s;
        }
        .user-btn:hover { border-color: var(--brand); background: var(--brand-light); color: var(--brand-dark); }
        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--brand-dark), #7c3aed);
            border-radius: 50%;
            color: #fff;
            font-size: .78rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 50%; }

        /* Hamburger mobile */
        .nav-toggler {
            display: none;
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: var(--text);
            margin-<?= $isRtl ? 'right' : 'left' ?>: auto;
        }

        /* ── Mobile bottom nav ───────────────────────────────────── */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: 56px;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(12px);
            border-top: 1px solid var(--border);
            z-index: 1030;
            padding: 0 4px;
            justify-content: space-around;
            align-items: center;
        }
        .mobile-bottom-nav a, .mobile-bottom-nav button {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            color: var(--muted);
            text-decoration: none;
            font-size: .6rem;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 10px;
            border: none;
            background: none;
            cursor: pointer;
            line-height: 1.1;
            flex: 1;
            transition: color .15s;
        }
        .mobile-bottom-nav a i, .mobile-bottom-nav button i { font-size: 1.3rem; display: block; }
        .mobile-bottom-nav a.active, .mobile-bottom-nav a:hover,
        .mobile-bottom-nav button.active { color: var(--brand-dark); }

        /* Mobile user drawer */
        .mobile-user-drawer {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 56px;
            background: rgba(15,23,42,.45);
            z-index: 1028;
            opacity: 0;
            transition: opacity .2s;
        }
        .mobile-user-drawer.open { opacity: 1; }
        .mobile-user-sheet {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: #fff;
            border-radius: 20px 20px 0 0;
            padding: 16px 0 20px;
            transform: translateY(100%);
            transition: transform .25s cubic-bezier(.32,1,.6,1);
        }
        .mobile-user-drawer.open .mobile-user-sheet { transform: translateY(0); }
        .mobile-sheet-handle { width: 40px; height: 4px; background: var(--border); border-radius: 2px; margin: 0 auto 16px; }
        .mobile-sheet-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            color: var(--text);
            text-decoration: none;
            font-size: .9rem;
            font-weight: 500;
            transition: background .12s;
        }
        .mobile-sheet-item:hover { background: var(--brand-light); color: var(--brand-dark); }
        .mobile-sheet-item.danger { color: #dc2626; }
        .mobile-sheet-item.danger:hover { background: #fef2f2; }
        .mobile-sheet-item i { font-size: 1.1rem; width: 22px; text-align: center; }

        @media (max-width: 768px) {
            body { padding-bottom: calc(56px + env(safe-area-inset-bottom, 0px)); }
            .nav-links, .nav-actions { display: none !important; }
            .nav-toggler { display: block; }
            .mobile-bottom-nav { display: flex; }
        }
        .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 6px;
            min-width: 200px;
        }
        .dropdown-item {
            border-radius: 8px;
            font-size: .875rem;
            font-weight: 500;
            padding: 8px 12px;
            transition: all .12s;
        }
        .dropdown-item:hover { background: var(--brand-light); color: var(--brand-dark); }
        .dropdown-item.text-danger:hover { background: #fef2f2; color: #dc2626; }

        /* ── Main content ─────────────────────────────────────────── */
        main {
            flex: 1;
            padding: 32px 0 48px;
        }

        /* ── Cards ────────────────────────────────────────────────── */
        .card {
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
            box-shadow: var(--shadow) !important;
            background: var(--surface);
        }
        .card:hover { box-shadow: 0 4px 24px rgba(99,102,241,.12) !important; }

        /* ── Buttons ──────────────────────────────────────────────── */
        .btn-primary {
            background: linear-gradient(135deg, var(--brand-dark) 0%, #7c3aed 100%);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: .01em;
            transition: all .2s;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99,102,241,.4);
        }
        .btn-outline-primary {
            border-color: var(--brand);
            color: var(--brand-dark);
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-outline-primary:hover {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
            transform: translateY(-1px);
        }
        .btn { border-radius: 8px !important; font-weight: 500; }

        /* ── Badges ───────────────────────────────────────────────── */
        .badge { border-radius: 6px; font-weight: 600; letter-spacing: .02em; }
        .badge.bg-primary { background: var(--brand-light) !important; color: var(--brand-dark) !important; }

        /* ── Flash messages ───────────────────────────────────────── */
        .flash-wrap { position: fixed; top: calc(var(--nav-h) + 12px); <?= $isRtl ? 'left' : 'right' ?>: 20px; z-index: 1040; maxwidth: 380px; }
        .alert { border-radius: var(--radius); border: none; font-size: .875rem; font-weight: 500; }
        .alert-success { background: #f0fdf4; color: #15803d; box-shadow: 0 4px 16px rgba(21,128,61,.15); }
        .alert-danger  { background: #fef2f2; color: #dc2626; box-shadow: 0 4px 16px rgba(220,38,38,.15); }

        /* ── Progress bar ─────────────────────────────────────────── */
        .progress { border-radius: 20px; background: var(--border); }
        .progress-bar { border-radius: 20px; }

        /* ── Tables ───────────────────────────────────────────────── */
        .table { font-size: .875rem; }
        .table th { font-weight: 600; color: var(--muted); font-size: .8rem; letter-spacing: .05em; text-transform: uppercase; }
        .table-hover tbody tr:hover { background: var(--brand-light); }

        /* ── Forms ────────────────────────────────────────────────── */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1.5px solid var(--border);
            font-size: .875rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        .form-label { font-size: .8rem; font-weight: 600; color: var(--muted); letter-spacing: .04em; text-transform: uppercase; margin-bottom: 6px; }

        /* ── Fixed Footer ─────────────────────────────────────────── */
        .app-footer {
            background: #0f172a;
            color: #64748b;
            height: var(--footer-h);
            display: flex;
            align-items: center;
            flex-shrink: 0;
            border-top: 1px solid #1e293b;
        }
        .app-footer a { color: #475569; text-decoration: none; font-size: .8rem; transition: color .15s; }
        .app-footer a:hover { color: var(--brand); }
        .app-footer p { margin: 0; font-size: .8rem; }

        /* ── Hero gradient ────────────────────────────────────────── */
        .hero-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);
            border-radius: 20px;
        }

        /* ── Stat cards ───────────────────────────────────────────── */
        .stat-card {
            border-radius: var(--radius);
            padding: 24px;
            text-align: center;
            background: var(--surface);
            border: 1px solid var(--border);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg) !important; }
        .stat-number { font-size: 2rem; font-weight: 800; line-height: 1; }

        /* ── Mobile (legacy block — superseded by bottom nav above) ─ */

        /* RTL tweaks */
        <?php if ($isRtl): ?>
        .dropdown-menu-end { --bs-position: start; }
        .ms-auto { margin-right: auto !important; margin-left: 0 !important; }
        .me-auto { margin-left: auto !important; margin-right: 0 !important; }
        <?php endif; ?>
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>

<!-- ── Fixed Navbar ────────────────────────────────────────────────────────── -->
<nav class="app-nav" id="appNav">
    <div class="container">
        <a class="nav-brand" href="<?= base_url('/') ?>">
            <span class="brand-icon"><i class="bi bi-briefcase-fill"></i></span>
            Persomy
        </a>

        <ul class="nav-links" id="navLinks">
            <li><a href="<?= base_url('/') ?>"><i class="bi bi-house-fill"></i><span>Accueil</span></a></li>
            <li><a href="<?= base_url('jobs') ?>"><i class="bi bi-briefcase"></i><span>Emplois</span></a></li>
            <?php if (session()->get('logged_in')): ?>
            <li><a href="<?= base_url('connections') ?>"><i class="bi bi-people"></i><span>Relations</span></a></li>
            <li><a href="<?= base_url('organizations') ?>"><i class="bi bi-buildings"></i><span>Organisations</span></a></li>
            <?php endif; ?>
            <li><a href="<?= base_url('coaching') ?>"><i class="bi bi-lightbulb"></i><span>Coaching</span></a></li>
        </ul>

        <!-- Search bar -->
        <form action="<?= base_url('jobs') ?>" method="get" class="nav-search" role="search">
            <i class="bi bi-search nav-search-icon"></i>
            <input type="search" name="keyword" placeholder="<?= lang('App.nav_search_placeholder') ?>" class="nav-search-input" aria-label="Search">
        </form>

        <div class="nav-actions" id="navActions">

            <?php if (session()->get('logged_in')): ?>
                <!-- Notifications (placeholder — module à venir) -->
                <button class="nav-notif-btn" title="Notifications" disabled>
                    <i class="bi bi-bell"></i>
                    <!-- <span class="nav-notif-badge"></span> -->
                </button>
                <div class="dropdown">
                    <a class="user-btn dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-avatar">
                            <?php if (session()->get('user_avatar')): ?>
                                <img src="<?= base_url('uploads/' . esc(session()->get('user_avatar'))) ?>" alt="">
                            <?php else: ?>
                                <?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?>
                            <?php endif; ?>
                        </span>
                        <span class="d-none d-md-inline"><?= esc(explode(' ', session()->get('user_name'))[0]) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <div class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold small"><?= esc(session()->get('user_name')) ?></div>
                                <div class="text-muted" style="font-size:.75rem"><?= esc(session()->get('user_email')) ?></div>
                            </div>
                        </li>
                        <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="bi bi-person me-2"></i><?= lang('App.nav_profile') ?></a></li>
                        <li><a class="dropdown-item" href="<?= base_url('dashboard') ?>"><i class="bi bi-grid me-2"></i>Tableau de bord</a></li>
                        <?php if (session()->get('user_role') === 'job_seeker'): ?>
                        <li><a class="dropdown-item" href="<?= base_url('alerts') ?>"><i class="bi bi-bell me-2"></i><?= lang('App.nav_alerts') ?></a></li>
                        <?php endif; ?>
                        <?php if (in_array(session()->get('user_role'), ['recruiter', 'admin'])): ?>
                        <li><a class="dropdown-item" href="<?= base_url('jobs/create') ?>"><i class="bi bi-plus-circle me-2"></i><?= lang('App.nav_post_job') ?></a></li>
                        <li><a class="dropdown-item" href="<?= base_url('company/edit') ?>"><i class="bi bi-building me-2"></i><?= lang('App.nav_company') ?></a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <!-- Language switcher -->
                        <li>
                            <div class="px-3 py-1 d-flex align-items-center gap-2">
                                <small class="text-muted me-1"><i class="bi bi-translate"></i></small>
                                <a href="<?= base_url('lang/en') ?>" class="lang-btn <?= $locale === 'en' ? 'active' : '' ?>">EN</a>
                                <a href="<?= base_url('lang/fr') ?>" class="lang-btn <?= $locale === 'fr' ? 'active' : '' ?>">FR</a>
                                <a href="<?= base_url('lang/ar') ?>" class="lang-btn <?= $locale === 'ar' ? 'active' : '' ?>">ع</a>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i><?= lang('App.nav_logout') ?></a></li>
                    </ul>
                </div>
            <?php else: ?>
                <div class="lang-switcher">
                    <a href="<?= base_url('lang/en') ?>" class="lang-btn <?= $locale === 'en' ? 'active' : '' ?>">EN</a>
                    <a href="<?= base_url('lang/fr') ?>" class="lang-btn <?= $locale === 'fr' ? 'active' : '' ?>">FR</a>
                    <a href="<?= base_url('lang/ar') ?>" class="lang-btn <?= $locale === 'ar' ? 'active' : '' ?>">ع</a>
                </div>
                <a href="<?= base_url('login') ?>" class="btn btn-sm btn-outline-primary px-3"><?= lang('App.nav_login') ?></a>
                <a href="<?= base_url('register') ?>" class="btn btn-sm btn-primary px-3"><?= lang('App.nav_signup') ?></a>
            <?php endif; ?>
        </div>

        <button class="nav-toggler" id="navToggler" aria-label="Mon compte">
            <span class="user-avatar" style="width:34px;height:34px;font-size:.78rem;">
                <?php if (session()->get('user_avatar')): ?>
                    <img src="<?= base_url('uploads/' . esc(session()->get('user_avatar'))) ?>" alt="">
                <?php elseif (session()->get('logged_in')): ?>
                    <?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?>
                <?php else: ?>
                    <i class="bi bi-person" style="font-size:1rem;"></i>
                <?php endif; ?>
            </span>
        </button>
    </div>
</nav>

<!-- ── Mobile bottom navigation bar ───────────────────────────────────────── -->
<nav class="mobile-bottom-nav" id="mobileBottomNav">
    <a href="<?= base_url('/') ?>"><i class="bi bi-house-fill"></i><span>Accueil</span></a>
    <a href="<?= base_url('jobs') ?>"><i class="bi bi-briefcase"></i><span>Emplois</span></a>
    <?php if (session()->get('logged_in')): ?>
        <a href="<?= base_url('connections') ?>"><i class="bi bi-people"></i><span>Relations</span></a>
        <a href="<?= base_url('organizations') ?>"><i class="bi bi-buildings"></i><span>Orgs</span></a>
    <?php else: ?>
        <a href="<?= base_url('coaching') ?>"><i class="bi bi-lightbulb"></i><span>Coaching</span></a>
        <a href="<?= base_url('login') ?>"><i class="bi bi-box-arrow-in-right"></i><span>Connexion</span></a>
    <?php endif; ?>
    <button id="mobileUserBtn" aria-label="Mon compte">
        <span class="user-avatar" style="width:30px;height:30px;font-size:.7rem;margin-bottom:2px;">
            <?php if (session()->get('user_avatar')): ?>
                <img src="<?= base_url('uploads/' . esc(session()->get('user_avatar'))) ?>" alt="">
            <?php elseif (session()->get('logged_in')): ?>
                <?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?>
            <?php else: ?>
                <i class="bi bi-person"></i>
            <?php endif; ?>
        </span>
        <span><?= session()->get('logged_in') ? esc(explode(' ', session()->get('user_name') ?? 'U')[0]) : 'Compte' ?></span>
    </button>
</nav>

<!-- ── Mobile user drawer ─────────────────────────────────────────────────── -->
<div class="mobile-user-drawer" id="mobileUserDrawer">
    <div class="mobile-user-sheet">
        <div class="mobile-sheet-handle"></div>
        <?php if (session()->get('logged_in')): ?>
            <div class="d-flex align-items-center gap-3 px-4 pb-3 border-bottom mb-1">
                <span class="user-avatar" style="width:46px;height:46px;font-size:1rem;flex-shrink:0;">
                    <?php if (session()->get('user_avatar')): ?>
                        <img src="<?= base_url('uploads/' . esc(session()->get('user_avatar'))) ?>" alt="">
                    <?php else: ?>
                        <?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?>
                    <?php endif; ?>
                </span>
                <div>
                    <div class="fw-bold" style="font-size:.95rem;"><?= esc(session()->get('user_name')) ?></div>
                    <div class="text-muted" style="font-size:.75rem;"><?= esc(session()->get('user_email')) ?></div>
                </div>
            </div>
            <a class="mobile-sheet-item" href="<?= base_url('profile') ?>"><i class="bi bi-person"></i><?= lang('App.nav_profile') ?></a>
            <a class="mobile-sheet-item" href="<?= base_url('dashboard') ?>"><i class="bi bi-grid"></i>Tableau de bord</a>
            <?php if (session()->get('user_role') === 'job_seeker'): ?>
            <a class="mobile-sheet-item" href="<?= base_url('alerts') ?>"><i class="bi bi-bell"></i><?= lang('App.nav_alerts') ?></a>
            <?php endif; ?>
            <?php if (in_array(session()->get('user_role'), ['recruiter', 'admin'])): ?>
            <a class="mobile-sheet-item" href="<?= base_url('jobs/create') ?>"><i class="bi bi-plus-circle"></i><?= lang('App.nav_post_job') ?></a>
            <a class="mobile-sheet-item" href="<?= base_url('company/edit') ?>"><i class="bi bi-building"></i><?= lang('App.nav_company') ?></a>
            <?php endif; ?>
            <div class="px-4 py-2 d-flex align-items-center gap-2 border-top mt-1">
                <small class="text-muted"><i class="bi bi-translate me-1"></i></small>
                <a href="<?= base_url('lang/en') ?>" class="lang-btn <?= $locale === 'en' ? 'active' : '' ?>">EN</a>
                <a href="<?= base_url('lang/fr') ?>" class="lang-btn <?= $locale === 'fr' ? 'active' : '' ?>">FR</a>
                <a href="<?= base_url('lang/ar') ?>" class="lang-btn <?= $locale === 'ar' ? 'active' : '' ?>">ع</a>
            </div>
            <a class="mobile-sheet-item danger" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right"></i><?= lang('App.nav_logout') ?></a>
        <?php else: ?>
            <a class="mobile-sheet-item" href="<?= base_url('login') ?>"><i class="bi bi-box-arrow-in-right"></i><?= lang('App.nav_login') ?></a>
            <a class="mobile-sheet-item" href="<?= base_url('register') ?>"><i class="bi bi-person-plus"></i><?= lang('App.nav_signup') ?></a>
        <?php endif; ?>
    </div>
</div>

<!-- ── Flash messages ─────────────────────────────────────────────────────── -->
<div class="flash-wrap" id="flashWrap">
    <?php if ($msg = session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-2 shadow">
            <i class="bi bi-check-circle-fill"></i>
            <span><?= esc($msg) ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($msg = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 mb-2 shadow">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= esc($msg) ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($errs = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible mb-2 shadow">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0 ps-3">
                <?php foreach ((array) $errs as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>

<!-- ── Page content ───────────────────────────────────────────────────────── -->
<main>
    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>
</main>

<!-- ── Fixed Footer ───────────────────────────────────────────────────────── -->
<footer class="app-footer">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <p>&copy; <?= date('Y') ?> <strong class="text-white">Persomy</strong> — <?= lang('App.hero_subtitle') ?></p>
        <div class="d-flex gap-3">
            <a href="<?= base_url('/') ?>"><?= lang('App.nav_home') ?></a>
            <a href="<?= base_url('jobs') ?>"><?= lang('App.nav_jobs') ?></a>
            <a href="<?= base_url('coaching') ?>"><?= lang('App.nav_coaching') ?></a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar shadow on scroll
    const nav = document.getElementById('appNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 10);
    });
    // Auto-dismiss flash after 4s
    document.querySelectorAll('#flashWrap .alert').forEach(el => {
        setTimeout(() => el.classList.add('fade'), 4000);
        setTimeout(() => el.remove(), 4500);
    });
    // Mobile user drawer
    function openMobileDrawer() {
        const d = document.getElementById('mobileUserDrawer');
        d.style.display = 'block';
        requestAnimationFrame(() => d.classList.add('open'));
    }
    function closeMobileDrawer() {
        const d = document.getElementById('mobileUserDrawer');
        d.classList.remove('open');
        setTimeout(() => { d.style.display = ''; }, 250);
    }
    document.getElementById('mobileUserBtn')?.addEventListener('click', openMobileDrawer);
    document.getElementById('navToggler')?.addEventListener('click', openMobileDrawer);
    document.getElementById('mobileUserDrawer')?.addEventListener('click', function (e) {
        if (e.target === this) closeMobileDrawer();
    });
    // Active nav link
    (function () {
        const curr = window.location.pathname.replace(/\/$/, '') || '/';
        const allNavLinks = document.querySelectorAll('.nav-links a, .mobile-bottom-nav a');
        allNavLinks.forEach(a => {
            try {
                const href = new URL(a.href).pathname.replace(/\/$/, '') || '/';
                if (curr === href || (href.length > 1 && curr.startsWith(href))) {
                    a.classList.add('active');
                }
            } catch(e) {}
        });
    })();
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
