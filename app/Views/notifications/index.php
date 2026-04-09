<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h2 class="fw-bold mb-0" style="font-size:1.3rem;">
        <i class="bi bi-bell-fill me-2" style="color:var(--brand-dark);"></i>Notifications
    </h2>
</div>

<?php if (empty($notifications)): ?>
<div class="text-center py-5 text-muted"
     style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
    <i class="bi bi-bell-slash" style="font-size:2.5rem;opacity:.3;"></i>
    <p class="mt-2 mb-0">Aucune notification pour le moment.</p>
</div>
<?php else: ?>
<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
    <?php foreach ($notifications as $n): ?>
    <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom"
         style="<?= $n->is_read ? '' : 'background:#eef2ff;' ?>">

        <div style="width:38px;height:38px;border-radius:50%;
                    background:<?= $n->is_read ? 'var(--bg)' : 'var(--brand)' ?>;
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
            <i class="bi bi-<?= $n->type === 'interview' ? 'calendar-check' : 'bell' ?>"
               style="font-size:.85rem;color:<?= $n->is_read ? 'var(--muted)' : '#fff' ?>;"></i>
        </div>

        <div class="flex-grow-1">
            <div class="fw-semibold" style="font-size:.875rem;<?= $n->is_read ? 'color:var(--muted)' : '' ?>">
                <?= esc($n->title) ?>
            </div>
            <?php if (!empty($n->body)): ?>
            <div class="text-muted" style="font-size:.8rem;"><?= esc($n->body) ?></div>
            <?php endif; ?>
            <div class="text-muted mt-1" style="font-size:.72rem;">
                <i class="bi bi-clock me-1"></i><?= date('d/m/Y à H:i', strtotime($n->created_at)) ?>
            </div>
        </div>

        <?php if (!$n->is_read): ?>
        <span class="badge bg-primary rounded-pill align-self-center" style="font-size:.65rem;">Nouveau</span>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
