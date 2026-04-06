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
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 6px <?= $isRtl ? '14px' : '32px' ?> 6px <?= $isRtl ? '32px' : '14px' ?>;
            font-size: .8rem;
            background: var(--bg);
            color: var(--text);
            width: 180px;
            transition: width .2s, border-color .15s;
            outline: none;
        }
        .nav-search-input:focus {
            border-color: var(--brand);
            width: 230px;
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
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
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--brand-dark), #7c3aed);
            border-radius: 8px;
            color: #fff;
            font-size: .75rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }

        /* Hamburger mobile */
        .nav-toggler {
            display: none;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 1.1rem;
            cursor: pointer;
            color: var(--text);
            margin-<?= $isRtl ? 'right' : 'left' ?>: auto;
        }

        /* ── Dropdown ─────────────────────────────────────────────── */
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

        /* ── Mobile ───────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .nav-links, .nav-actions { display: none; }
            .nav-toggler { display: block; }
            .nav-links.open, .nav-actions.open {
                display: flex;
                flex-direction: column;
                position: fixed;
                top: var(--nav-h);
                left: 0; right: 0;
                background: var(--surface);
                border-bottom: 1px solid var(--border);
                padding: 12px 16px;
                gap: 6px;
                z-index: 1029;
            }
        }

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
            <li><a href="<?= base_url('/') ?>"><i class="bi bi-house-fill"></i><span><?= lang('App.nav_home') ?></span></a></li>
            <li><a href="<?= base_url('jobs') ?>"><i class="bi bi-briefcase"></i><span><?= lang('App.nav_jobs') ?></span></a></li>
            <?php if (session()->get('logged_in')): ?>
            <li><a href="<?= base_url('connections') ?>"><i class="bi bi-people"></i><span>Relations</span></a></li>
            <li><a href="<?= base_url('dashboard') ?>"><i class="bi bi-grid"></i><span><?= lang('App.nav_dashboard') ?></span></a></li>
            <?php endif; ?>
            <li><a href="<?= base_url('coaching') ?>"><i class="bi bi-lightbulb"></i><span><?= lang('App.nav_coaching') ?></span></a></li>
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
                        <span class="user-avatar"><?= strtoupper(substr(session()->get('user_name') ?? 'U', 0, 1)) ?></span>
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

        <button class="nav-toggler" id="navToggler" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </button>
    </div>
</nav>

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
    // Mobile menu
    document.getElementById('navToggler')?.addEventListener('click', () => {
        document.getElementById('navLinks').classList.toggle('open');
        document.getElementById('navActions').classList.toggle('open');
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
