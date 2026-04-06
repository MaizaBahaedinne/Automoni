<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.sp-header {
    margin-bottom: 1.5rem;
}
.sp-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: .75rem;
}
.sp-search-form {
    display: flex;
    gap: .5rem;
    max-width: 500px;
}
.sp-search-form input {
    flex: 1;
    padding: .5rem 1rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    font-size: .9rem;
    background: var(--bg);
    color: var(--text);
}
.sp-search-form input:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(99,102,241,.15);
}
.sp-results-title {
    font-size: .9rem;
    color: var(--muted);
    margin-bottom: 1rem;
}
.sp-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
}
.sp-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.25rem 1rem;
    text-align: center;
    transition: box-shadow .2s;
}
.sp-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.1); }
.sp-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--border);
    margin: 0 auto .7rem;
    display: block;
    background: var(--brand-light);
}
.sp-avatar-placeholder {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: var(--brand-light);
    color: var(--brand);
    font-size: 1.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto .7rem;
    border: 3px solid var(--border);
}
.sp-name {
    font-weight: 600;
    font-size: .92rem;
    color: var(--text);
    text-decoration: none;
    display: block;
    margin-bottom: .2rem;
}
.sp-name:hover { color: var(--brand); }
.sp-headline {
    font-size: .78rem;
    color: var(--muted);
    margin-bottom: .2rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.sp-location {
    font-size: .75rem;
    color: var(--muted);
    margin-bottom: .85rem;
}
.sp-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--muted);
}
.sp-empty i { font-size: 2.5rem; display: block; margin-bottom: 1rem; color: var(--border); }
</style>

<div class="sp-header">
    <div class="d-flex align-items-center gap-2 mb-2">
        <a href="<?= base_url('connections') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Mes relations
        </a>
    </div>
    <h1 class="sp-title">Trouver des personnes</h1>
    <form class="sp-search-form" method="get" action="<?= base_url('connections/search') ?>">
        <input type="search" name="q" value="<?= esc($keyword) ?>" placeholder="Rechercher par nom, poste…" autofocus>
        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

<?php if ($keyword !== ''): ?>
<p class="sp-results-title">
    <?= count($results) ?> résultat<?= count($results) !== 1 ? 's' : '' ?> pour
    "<strong><?= esc($keyword) ?></strong>"
</p>
<?php endif; ?>

<?php if ($keyword !== '' && empty($results)): ?>
<div class="sp-empty">
    <i class="bi bi-person-x"></i>
    <p>Aucun utilisateur trouvé pour "<?= esc($keyword) ?>".</p>
</div>
<?php elseif (!empty($results)): ?>
<div class="sp-grid">
    <?php foreach ($results as $p):
        // Determine connection status from joined data
        $myId = session()->get('user_id');
        if ($p->conn_status === null) {
            $connStatus = 'none';
        } elseif ($p->conn_status === 'accepted') {
            $connStatus = 'accepted';
        } elseif ($p->conn_status === 'pending') {
            $connStatus = ((int)$p->conn_requester === (int)$myId) ? 'pending_sent' : 'pending_received';
        } else {
            $connStatus = 'none';
        }
    ?>
    <div class="sp-card" id="sp-card-<?= $p->id ?>">
        <?php if (!empty($p->avatar)): ?>
        <img src="<?= base_url('uploads/' . esc($p->avatar)) ?>" alt="" class="sp-avatar">
        <?php else: ?>
        <div class="sp-avatar-placeholder"><?= strtoupper(substr($p->first_name, 0, 1)) ?></div>
        <?php endif; ?>

        <a class="sp-name" href="<?= base_url('profile/view/' . $p->id) ?>">
            <?= esc($p->first_name . ' ' . $p->last_name) ?>
        </a>

        <?php if (!empty($p->headline)): ?>
        <p class="sp-headline"><?= esc($p->headline) ?></p>
        <?php endif; ?>

        <?php if (!empty($p->city) || !empty($p->country)): ?>
        <p class="sp-location">
            <i class="bi bi-geo-alt"></i>
            <?= esc(implode(', ', array_filter([$p->city, $p->country]))) ?>
        </p>
        <?php endif; ?>

        <div class="cn-action-wrap" data-id="<?= $p->id ?>">
            <?php if ($connStatus === 'accepted'): ?>
            <span class="badge bg-success-subtle text-success mb-1">
                <i class="bi bi-check2 me-1"></i>Connecté
            </span><br>
            <button class="btn btn-outline-danger btn-sm btn-sp-remove" data-id="<?= $p->id ?>">
                <i class="bi bi-person-dash me-1"></i>Retirer
            </button>
            <?php elseif ($connStatus === 'pending_sent'): ?>
            <button class="btn btn-outline-secondary btn-sm btn-sp-withdraw" data-id="<?= $p->id ?>">
                <i class="bi bi-clock me-1"></i>En attente…
            </button>
            <?php elseif ($connStatus === 'pending_received'): ?>
            <button class="btn btn-primary btn-sm btn-sp-accept" data-id="<?= $p->id ?>">
                <i class="bi bi-check-lg me-1"></i>Accepter
            </button>
            <button class="btn btn-outline-secondary btn-sm btn-sp-reject" data-id="<?= $p->id ?>">
                Ignorer
            </button>
            <?php else: ?>
            <button class="btn btn-outline-primary btn-sm btn-sp-send" data-id="<?= $p->id ?>">
                <i class="bi bi-person-plus me-1"></i>Se connecter
            </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="sp-empty">
    <i class="bi bi-search"></i>
    <p>Entrez un nom ou un poste pour trouver des personnes.</p>
</div>
<?php endif; ?>

<script>
(function () {
    const BASE      = '<?= base_url() ?>';
    const CSRF_NAME = '<?= csrf_token() ?>';
    const CSRF_HASH = '<?= csrf_hash() ?>';

    function post(url, cb) {
        const body = new URLSearchParams({ [CSRF_NAME]: CSRF_HASH });
        fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body })
            .then(r => r.json())
            .then(data => { if (data.success) cb(data); })
            .catch(() => {});
    }

    function wrap(id) { return document.querySelector('#sp-card-' + id + ' .cn-action-wrap'); }

    function setAccepted(id) {
        const w = wrap(id);
        if (!w) return;
        w.innerHTML =
            '<span class="badge bg-success-subtle text-success mb-1"><i class="bi bi-check2 me-1"></i>Connecté</span><br>' +
            '<button class="btn btn-outline-danger btn-sm btn-sp-remove" data-id="'+id+'"><i class="bi bi-person-dash me-1"></i>Retirer</button>';
    }

    function setPendingSent(id) {
        const w = wrap(id);
        if (!w) return;
        w.innerHTML = '<button class="btn btn-outline-secondary btn-sm btn-sp-withdraw" data-id="'+id+'"><i class="bi bi-clock me-1"></i>En attente…</button>';
    }

    function setNone(id) {
        const w = wrap(id);
        if (!w) return;
        w.innerHTML = '<button class="btn btn-outline-primary btn-sm btn-sp-send" data-id="'+id+'"><i class="bi bi-person-plus me-1"></i>Se connecter</button>';
    }

    document.addEventListener('click', e => {
        const btn = e.target.closest('[class*="btn-sp-"]');
        if (!btn) return;
        const id = btn.dataset.id;

        if (btn.classList.contains('btn-sp-send')) {
            post(BASE + 'connections/send/' + id, () => setPendingSent(id));
        } else if (btn.classList.contains('btn-sp-withdraw')) {
            post(BASE + 'connections/withdraw/' + id, () => setNone(id));
        } else if (btn.classList.contains('btn-sp-remove')) {
            post(BASE + 'connections/remove/' + id, () => setNone(id));
        } else if (btn.classList.contains('btn-sp-accept')) {
            post(BASE + 'connections/accept/' + id, () => setAccepted(id));
        } else if (btn.classList.contains('btn-sp-reject')) {
            post(BASE + 'connections/reject/' + id, () => setNone(id));
        }
    });
})();
</script>

<?= $this->endSection() ?>
