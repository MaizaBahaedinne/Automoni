<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
/* ── Connections page ───────────────────────────────────────────────────── */
.cn-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.cn-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text);
    margin: 0;
}
.cn-count {
    font-size: .95rem;
    color: var(--muted);
    font-weight: 400;
}
.cn-tabs {
    display: flex;
    gap: .25rem;
    border-bottom: 2px solid var(--border);
    margin-bottom: 1.5rem;
}
.cn-tab {
    padding: .55rem 1.1rem;
    border: none;
    background: none;
    cursor: pointer;
    font-size: .9rem;
    font-weight: 500;
    color: var(--muted);
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: color .2s, border-color .2s;
    display: flex;
    align-items: center;
    gap: .4rem;
}
.cn-tab.active {
    color: var(--brand);
    border-bottom-color: var(--brand);
}
.cn-tab .badge {
    font-size: .7rem;
    padding: .2em .45em;
}
.cn-search {
    position: relative;
    max-width: 380px;
    margin-bottom: 1.25rem;
}
.cn-search input {
    width: 100%;
    padding: .5rem .85rem .5rem 2.4rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    font-size: .9rem;
    background: var(--bg);
    color: var(--text);
}
.cn-search input:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(99,102,241,.15);
}
.cn-search .cn-search-icon {
    position: absolute;
    left: .75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: .9rem;
    pointer-events: none;
}
.cn-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
}
.cn-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.25rem 1rem;
    text-align: center;
    transition: box-shadow .2s;
    position: relative;
}
.cn-card:hover {
    box-shadow: var(--shadow-lg);
}
.cn-avatar {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--border);
    margin: 0 auto .75rem;
    display: block;
    background: var(--brand-light);
}
.cn-avatar-placeholder {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: var(--brand-light);
    color: var(--brand);
    font-size: 1.6rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto .75rem;
    border: 3px solid var(--border);
}
.cn-name {
    font-weight: 600;
    font-size: .95rem;
    color: var(--text);
    text-decoration: none;
    display: block;
    margin-bottom: .2rem;
}
.cn-name:hover { color: var(--brand); }
.cn-headline {
    font-size: .8rem;
    color: var(--muted);
    margin-bottom: .25rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.cn-location {
    font-size: .78rem;
    color: var(--muted);
    margin-bottom: .85rem;
}
.cn-actions {
    display: flex;
    justify-content: center;
    gap: .5rem;
    flex-wrap: wrap;
}
.cn-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--muted);
}
.cn-empty i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--border);
    display: block;
}
.cn-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: .75rem;
    margin-bottom: 1.75rem;
}
.cn-stat-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem .75rem;
    text-align: center;
    transition: box-shadow .2s, transform .15s;
}
.cn-stat-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
.cn-stat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto .6rem;
    font-size: 1.1rem;
}
.cn-stat-num {
    font-size: 1.7rem;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
}
.cn-stat-label {
    font-size: .72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--muted);
    margin-top: .3rem;
}
.cn-received-list {
    display: flex;
    flex-direction: column;
    gap: .75rem;
    max-width: 600px;
}
.cn-list-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: .85rem 1rem;
    transition: box-shadow .2s;
}
.cn-list-card:hover { box-shadow: var(--shadow-lg); }
.cn-list-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 2px solid var(--border);
    background: var(--brand-light);
}
.cn-list-avatar-placeholder {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: var(--brand-light);
    color: var(--brand);
    font-size: 1.2rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid var(--border);
}
.cn-list-body { flex: 1; min-width: 0; }
.cn-list-name {
    font-weight: 600;
    font-size: .9rem;
    color: var(--text);
    text-decoration: none;
    display: block;
}
.cn-list-name:hover { color: var(--brand); }
.cn-list-sub {
    font-size: .78rem;
    color: var(--muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cn-list-actions { display: flex; gap: .4rem; flex-shrink: 0; }
</style>

<!-- Header -->
<div class="cn-header">
    <h1 class="cn-title">
        Mes relations
        <span class="cn-count"><?= $connectionsCount ?> relation<?= $connectionsCount !== 1 ? 's' : '' ?></span>
    </h1>
    <a href="<?= base_url('connections/search') ?>" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-search me-1"></i>Trouver des personnes
    </a>
</div>

<!-- Stats -->
<div class="cn-stats">
    <div class="cn-stat-card">
        <div class="cn-stat-icon" style="background:var(--brand-light); color:var(--brand);">
            <i class="bi bi-people-fill"></i>
        </div>
        <div class="cn-stat-num"><?= $connectionsCount ?></div>
        <div class="cn-stat-label">Relations</div>
    </div>
    <div class="cn-stat-card" style="cursor:pointer;" onclick="document.querySelector('[data-pane=pane-received]').click()">
        <div class="cn-stat-icon" style="background:#eff6ff; color:#3b82f6;">
            <i class="bi bi-person-plus-fill"></i>
        </div>
        <div class="cn-stat-num"><?= count($pendingReceived) ?></div>
        <div class="cn-stat-label">Invitations reçues</div>
    </div>
    <div class="cn-stat-card" style="cursor:pointer;" onclick="document.querySelector('[data-pane=pane-sent]').click()">
        <div class="cn-stat-icon" style="background:#f0fdf4; color:#22c55e;">
            <i class="bi bi-send-fill"></i>
        </div>
        <div class="cn-stat-num"><?= count($sentPending) ?></div>
        <div class="cn-stat-label">Invitations envoyées</div>
    </div>
    <?php if ($connectionsCount > 0): ?>
    <?php
    $cities = array_unique(array_filter(array_column((array) $connections, 'city')));
    $nbCities = count($cities);
    ?>
    <div class="cn-stat-card">
        <div class="cn-stat-icon" style="background:#fdf4ff; color:#a855f7;">
            <i class="bi bi-geo-alt-fill"></i>
        </div>
        <div class="cn-stat-num"><?= $nbCities ?></div>
        <div class="cn-stat-label">Villes représentées</div>
    </div>
    <?php endif; ?>
</div>

<!-- Tabs -->
<div class="cn-tabs" role="tablist">
    <button class="cn-tab active" data-pane="pane-connections">
        <i class="bi bi-people"></i> Relations
        <?php if ($connectionsCount): ?>
        <span class="badge bg-secondary"><?= $connectionsCount ?></span>
        <?php endif; ?>
    </button>
    <button class="cn-tab" data-pane="pane-received">
        <i class="bi bi-person-plus"></i> Invitations reçues
        <?php if (count($pendingReceived)): ?>
        <span class="badge bg-primary"><?= count($pendingReceived) ?></span>
        <?php endif; ?>
    </button>
    <button class="cn-tab" data-pane="pane-sent">
        <i class="bi bi-send"></i> Invitations envoyées
        <?php if (count($sentPending)): ?>
        <span class="badge bg-secondary"><?= count($sentPending) ?></span>
        <?php endif; ?>
    </button>
</div>

<!-- ── Pane: My Connections ───────────────────────────────────────────────── -->
<div class="cn-pane active" id="pane-connections">
    <div class="cn-search">
        <i class="bi bi-search cn-search-icon"></i>
        <input type="search" id="cnFilter" placeholder="Filtrer mes relations…" autocomplete="off">
    </div>

    <?php if (empty($connections)): ?>
    <div class="cn-empty">
        <i class="bi bi-people"></i>
        <p class="mb-0">Vous n'avez pas encore de relations.<br>
        <a href="<?= base_url('connections/search') ?>">Trouvez des personnes</a> à connecter.</p>
    </div>
    <?php else: ?>
    <div class="cn-grid" id="cnGrid">
        <?php foreach ($connections as $p): ?>
        <div class="cn-card" data-name="<?= esc(strtolower($p->first_name . ' ' . $p->last_name)) ?>">
            <?php if (!empty($p->avatar)): ?>
            <img src="<?= base_url('uploads/' . esc($p->avatar)) ?>" alt="" class="cn-avatar">
            <?php else: ?>
            <div class="cn-avatar-placeholder"><?= strtoupper(substr($p->first_name, 0, 1)) ?></div>
            <?php endif; ?>
            <a class="cn-name" href="<?= base_url('profile/view/' . $p->id) ?>">
                <?= esc($p->first_name . ' ' . $p->last_name) ?>
            </a>
            <?php if (!empty($p->headline)): ?>
            <p class="cn-headline"><?= esc($p->headline) ?></p>
            <?php endif; ?>
            <?php if (!empty($p->city) || !empty($p->country)): ?>
            <p class="cn-location">
                <i class="bi bi-geo-alt text-muted"></i>
                <?= esc(implode(', ', array_filter([$p->city, $p->country]))) ?>
            </p>
            <?php endif; ?>
            <div class="cn-actions">
                <a href="<?= base_url('profile/view/' . $p->id) ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-person me-1"></i>Voir
                </a>
                <button class="btn btn-outline-danger btn-sm btn-cn-remove" data-id="<?= $p->id ?>" title="Retirer la relation">
                    <i class="bi bi-person-dash"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ── Pane: Received ─────────────────────────────────────────────────────── -->
<div class="cn-pane" id="pane-received">
    <?php if (empty($pendingReceived)): ?>
    <div class="cn-empty">
        <i class="bi bi-inbox"></i>
        <p class="mb-0">Aucune invitation en attente.</p>
    </div>
    <?php else: ?>
    <div class="cn-received-list">
        <?php foreach ($pendingReceived as $p): ?>
        <div class="cn-list-card" id="received-<?= $p->id ?>">
            <?php if (!empty($p->avatar)): ?>
            <img src="<?= base_url('uploads/' . esc($p->avatar)) ?>" alt="" class="cn-list-avatar">
            <?php else: ?>
            <div class="cn-list-avatar-placeholder"><?= strtoupper(substr($p->first_name, 0, 1)) ?></div>
            <?php endif; ?>
            <div class="cn-list-body">
                <a class="cn-list-name" href="<?= base_url('profile/view/' . $p->id) ?>">
                    <?= esc($p->first_name . ' ' . $p->last_name) ?>
                </a>
                <span class="cn-list-sub"><?= esc($p->headline ?? ($p->city ?? '')) ?></span>
            </div>
            <div class="cn-list-actions">
                <button class="btn btn-primary btn-sm btn-cn-accept" data-id="<?= $p->id ?>">
                    <i class="bi bi-check-lg me-1"></i>Accepter
                </button>
                <button class="btn btn-outline-secondary btn-sm btn-cn-reject" data-id="<?= $p->id ?>">
                    Ignorer
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ── Pane: Sent ─────────────────────────────────────────────────────────── -->
<div class="cn-pane" id="pane-sent">
    <?php if (empty($sentPending)): ?>
    <div class="cn-empty">
        <i class="bi bi-send"></i>
        <p class="mb-0">Aucune invitation envoyée en attente.</p>
    </div>
    <?php else: ?>
    <div class="cn-received-list">
        <?php foreach ($sentPending as $p): ?>
        <div class="cn-list-card" id="sent-<?= $p->id ?>">
            <?php if (!empty($p->avatar)): ?>
            <img src="<?= base_url('uploads/' . esc($p->avatar)) ?>" alt="" class="cn-list-avatar">
            <?php else: ?>
            <div class="cn-list-avatar-placeholder"><?= strtoupper(substr($p->first_name, 0, 1)) ?></div>
            <?php endif; ?>
            <div class="cn-list-body">
                <a class="cn-list-name" href="<?= base_url('profile/view/' . $p->id) ?>">
                    <?= esc($p->first_name . ' ' . $p->last_name) ?>
                </a>
                <span class="cn-list-sub"><?= esc($p->headline ?? ($p->city ?? '')) ?></span>
            </div>
            <div class="cn-list-actions">
                <button class="btn btn-outline-secondary btn-sm btn-cn-withdraw" data-id="<?= $p->id ?>">
                    <i class="bi bi-x-lg me-1"></i>Annuler
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
(function () {
    const BASE = '<?= base_url() ?>';
    const CSRF_NAME  = '<?= csrf_token() ?>';
    const CSRF_HASH  = '<?= csrf_hash() ?>';

    // ── Tab switching ──────────────────────────────────────────────────────
    document.querySelectorAll('.cn-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.cn-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.cn-pane').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(btn.dataset.pane).classList.add('active');
        });
    });

    // ── Filter connections ─────────────────────────────────────────────────
    const filterInput = document.getElementById('cnFilter');
    if (filterInput) {
        filterInput.addEventListener('input', () => {
            const q = filterInput.value.toLowerCase();
            document.querySelectorAll('#cnGrid .cn-card').forEach(card => {
                card.style.display = card.dataset.name.includes(q) ? '' : 'none';
            });
        });
    }

    // ── Generic AJAX helper ────────────────────────────────────────────────
    function postAction(url, onSuccess) {
        const body = new URLSearchParams({ [CSRF_NAME]: CSRF_HASH });
        fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body })
            .then(r => r.json())
            .then(data => { if (data.success) onSuccess(data); })
            .catch(() => {});
    }

    // ── Remove connection ─────────────────────────────────────────────────
    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-cn-remove');
        if (!btn) return;
        const id   = btn.dataset.id;
        const card = btn.closest('.cn-card');
        postAction(BASE + 'connections/remove/' + id, () => {
            card.remove();
            updateCount('pane-connections', '#cnGrid .cn-card');
        });
    });

    // ── Accept ───────────────────────────────────────────────────────────
    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-cn-accept');
        if (!btn) return;
        const id = btn.dataset.id;
        btn.disabled = true;
        postAction(BASE + 'connections/accept/' + id, () => location.reload());
    });

    // ── Reject ────────────────────────────────────────────────────────────
    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-cn-reject');
        if (!btn) return;
        const id = btn.dataset.id;
        postAction(BASE + 'connections/reject/' + id, () => {
            document.getElementById('received-' + id)?.remove();
            updateBadge('[data-pane="pane-received"]');
        });
    });

    // ── Withdraw ─────────────────────────────────────────────────────────
    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-cn-withdraw');
        if (!btn) return;
        const id = btn.dataset.id;
        postAction(BASE + 'connections/withdraw/' + id, () => {
            document.getElementById('sent-' + id)?.remove();
            updateBadge('[data-pane="pane-sent"]');
        });
    });

    function updateCount(paneId, selector) {
        const count = document.querySelectorAll('#' + paneId + ' ' + selector).length;
        const tab = document.querySelector('[data-pane="' + paneId + '"]');
        if (tab) {
            const badge = tab.querySelector('.badge');
            if (badge) { if (count) badge.textContent = count; else badge.remove(); }
        }
    }

    function updateBadge(tabSelector) {
        const tab   = document.querySelector(tabSelector);
        const pane  = document.getElementById(tab?.dataset.pane);
        if (!pane) return;
        const count = pane.querySelectorAll('.cn-list-card').length;
        const badge = tab.querySelector('.badge');
        if (badge) { if (count) badge.textContent = count; else badge.remove(); }
    }
})();
</script>

<?= $this->endSection() ?>
