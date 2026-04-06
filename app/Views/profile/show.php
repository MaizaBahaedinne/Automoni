<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
/* â”€â”€ ATS CV shell â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
#cv-sheet {
    font-family: 'Calibri', 'Segoe UI', Arial, sans-serif;
    font-size: 14px;
    line-height: 1.6;
    color: #1a1a1a;
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 40px 48px 48px;
}
.cv-name    { font-size: 26px; font-weight: 900; text-transform: uppercase; letter-spacing: 1.5px; color: #0d1b2a; }
.cv-tagline { font-size: 13.5px; color: #555; font-style: italic; margin-top: 2px; }
.cv-contacts{ display: flex; flex-wrap: wrap; gap: 6px 20px; font-size: 12px; color: #444; margin-top: 8px; }
.cv-contacts a { color: #444; text-decoration: none; }
.cv-contacts a:hover { color: #0A66C2; }
.cv-sec-head {
    font-size: 10.5px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 1.4px; color: #0d1b2a;
    border-bottom: 2px solid #0d1b2a;
    padding-bottom: 3px; margin: 22px 0 10px;
}
.cv-item { margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
.cv-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.cv-desc {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.cv-desc.expanded { display: block; -webkit-line-clamp: unset; }
.cv-expand-btn {
    background: none; border: none; padding: 0;
    font-size: 11.5px; color: #0A66C2; cursor: pointer;
    display: inline-block; margin-top: 2px;
}
.cv-expand-btn:hover { text-decoration: underline; }
.cv-skill {
    display: inline-block; border: 1px solid #ccc;
    border-radius: 3px; padding: 1px 9px; font-size: 12px;
    margin: 2px 3px 2px 0; background: #f8f9fa; color: #333;
}
.cv-toolbar {
    max-width: 900px; margin: 0 auto 14px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;
}
@media print {
    .cv-toolbar, nav, header, .navbar, footer { display: none !important; }
    #cv-sheet { border: none; padding: 0; box-shadow: none; }
    .cv-desc { display: block !important; -webkit-line-clamp: unset !important; }
}
@media (max-width: 768px) { #cv-sheet { padding: 24px 18px 32px; } }
</style>

<!-- Toolbar -->
<div class="cv-toolbar">
    <div class="d-flex align-items-center gap-2">
        <h6 class="fw-bold mb-0 text-muted"><?= lang('App.profile_title') ?></h6>
    </div>
    <div class="d-flex gap-2">
        <?php if (session()->get('user_id') == $user?->id): ?>
        <a href="<?= base_url('profile/edit') ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil me-1"></i><?= lang('App.edit_profile') ?>
        </a>
        <?php endif; ?>
        <?php if (!empty($profile?->cv_file)): ?>
        <a href="<?= base_url('profile/cv/download') ?>" class="btn btn-sm btn-outline-success">
            <i class="bi bi-download me-1"></i><?= lang('App.download_cv') ?>
        </a>
        <?php endif; ?>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>PDF
        </button>
    </div>
</div>

<!-- CV Sheet -->
<div id="cv-sheet">

    <!-- â‘  Header â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <div style="display:flex;align-items:flex-start;gap:20px;">
        <?php if (!empty(isset($profile->avatar) ? $profile->avatar : null)): ?>
            <img src="<?= base_url('uploads/' . esc($profile->avatar)) ?>"
                 style="width:72px;height:72px;border-radius:50%;object-fit:cover;flex-shrink:0;" alt="">
        <?php else: ?>
            <div style="width:72px;height:72px;border-radius:50%;background:#1a3c8f;color:#fff;
                        display:flex;align-items:center;justify-content:center;font-size:28px;
                        font-weight:900;flex-shrink:0;">
                <?= strtoupper(substr($user?->first_name ?? 'U', 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div style="flex:1;min-width:0;">
            <div class="cv-name"><?= esc(strtoupper(trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? '')))) ?></div>
            <?php if (!empty($profile?->headline)): ?>
            <div class="cv-tagline"><?= esc($profile->headline) ?></div>
            <?php endif; ?>
            <div class="cv-contacts">
                <?php if (!empty($user?->email)): ?>
                <span><i class="bi bi-envelope me-1"></i><?= esc($user->email) ?></span>
                <?php endif; ?>
                <?php $phone = trim(($profile?->phone_code ?? '') . ' ' . ($profile?->phone ?? '')); ?>
                <?php if (!empty(trim($profile?->phone ?? ''))): ?>
                <span><i class="bi bi-telephone me-1"></i><?= esc($phone) ?></span>
                <?php endif; ?>
                <?php $loc = implode(', ', array_filter([$profile?->city ?? '', $profile?->country ?? ''])); ?>
                <?php if (!empty($loc)): ?>
                <span><i class="bi bi-geo-alt me-1"></i><?= esc($loc) ?></span>
                <?php endif; ?>
                <?php if (!empty($profile?->linkedin)): ?>
                <a href="<?= esc($profile->linkedin) ?>" target="_blank" rel="noopener">
                    <i class="bi bi-linkedin me-1"></i>LinkedIn
                </a>
                <?php endif; ?>
                <?php if (!empty($profile?->github)): ?>
                <a href="<?= esc($profile->github) ?>" target="_blank" rel="noopener">
                    <i class="bi bi-github me-1"></i>GitHub
                </a>
                <?php endif; ?>
                <?php if (!empty($profile?->portfolio)): ?>
                <a href="<?= esc($profile->portfolio) ?>" target="_blank" rel="noopener">
                    <i class="bi bi-globe me-1"></i>Portfolio
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- â‘¡ Profil â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <?php if (!empty($profile?->summary)): ?>
    <div class="cv-sec-head"><?= lang('App.section_about') ?></div>
    <div class="cv-item" style="border:none;padding:0;margin:0;">
        <div class="cv-desc" id="desc-summary"><?= nl2br(esc($profile->summary)) ?></div>
        <?php if (mb_strlen($profile->summary) > 200): ?>
        <button class="cv-expand-btn" data-target="desc-summary" data-more="Lire plus â†“" data-less="RÃ©duire â†‘">Lire plus â†“</button>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- â‘¢ CompÃ©tences â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <?php if (!empty($skills)): ?>
    <div class="cv-sec-head"><?= lang('App.section_skills') ?></div>
    <div style="margin-bottom:4px;">
        <?php foreach ($skills as $skill): ?>
            <span class="cv-skill"><?= esc($skill->skill_name) ?></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- â‘£ ExpÃ©riences â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <?php if (!empty($experiences)): ?>
    <div class="cv-sec-head"><?= lang('App.section_experience') ?></div>
    <?php foreach ($experiences as $i => $exp): ?>
    <div class="cv-item">
        <div style="display:flex;justify-content:space-between;align-items:baseline;flex-wrap:wrap;gap:4px;">
            <div>
                <strong style="font-size:14.5px;"><?= esc($exp->title ?? '') ?></strong>
                <?php if (!empty($exp->level)): ?>
                    <span class="badge bg-primary ms-1 fw-normal" style="font-size:11px;vertical-align:middle;"><?= esc($exp->level) ?></span>
                <?php endif; ?>
                <?php if (!empty($exp->contract)): ?>
                    <span class="badge bg-secondary ms-1 fw-normal" style="font-size:11px;vertical-align:middle;"><?= esc($exp->contract) ?></span>
                <?php endif; ?>
            </div>
            <span style="font-size:12px;color:#666;white-space:nowrap;">
                <?= $exp->start_date ? date('M Y', strtotime($exp->start_date)) : '' ?>
                &ndash;
                <?= $exp->is_current ? lang('App.present') : ($exp->end_date ? date('M Y', strtotime($exp->end_date)) : lang('App.present')) ?>
            </span>
        </div>
        <div style="font-size:13px;color:#444;margin-top:1px;">
            <strong><?= esc($exp->company) ?></strong>
            <?php if (!empty($exp->department)): ?> &bull; <?= esc($exp->department) ?><?php endif; ?>
            <?php if (!empty($exp->location)): ?> &bull; <i class="bi bi-geo-alt" style="font-size:11px;"></i> <?= esc($exp->location) ?><?php endif; ?>
        </div>
        <?php if (!empty($exp->manager_name)): ?>
        <div style="font-size:12px;color:#666;margin-top:2px;">
            <i class="bi bi-person-badge me-1"></i><?= lang('App.exp_manager') ?> : <?= esc($exp->manager_name) ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($exp->description)): ?>
        <div class="cv-desc mt-2" id="desc-exp-<?= (int)$i ?>"><?= nl2br(esc($exp->description)) ?></div>
        <?php if (mb_strlen($exp->description) > 200): ?>
        <button class="cv-expand-btn" data-target="desc-exp-<?= (int)$i ?>" data-more="Lire plus â†“" data-less="RÃ©duire â†‘">Lire plus â†“</button>
        <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($exp->skills_gained)): ?>
        <div style="margin-top:6px;">
            <?php foreach (array_filter(array_map('trim', explode(',', $exp->skills_gained))) as $sg): ?>
                <span class="cv-skill" style="border-color:#91bfee;background:#eaf3fc;color:#1a3c8f;"><?= esc($sg) ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- â‘¤ Formations â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <?php if (!empty($education)): ?>
    <div class="cv-sec-head"><?= lang('App.section_education') ?></div>
    <?php foreach ($education as $edu): ?>
    <div class="cv-item">
        <div style="display:flex;justify-content:space-between;align-items:baseline;flex-wrap:wrap;gap:4px;">
            <div>
                <strong style="font-size:14px;"><?= esc($edu->degree) ?></strong>
                <?php if (!empty($edu->niveau ?? '')): ?>
                    <span class="badge bg-primary ms-1 fw-normal" style="font-size:11px;vertical-align:middle;"><?= esc($edu->niveau) ?></span>
                <?php endif; ?>
                <?php if (!empty($edu->field)): ?>
                    <span style="font-weight:400;font-size:13px;color:#555;"> &mdash; <?= esc($edu->field) ?></span>
                <?php endif; ?>
            </div>
            <span style="font-size:12px;color:#666;white-space:nowrap;">
                <?= !empty($edu->start_year) ? esc($edu->start_year) : '' ?>
                <?= !empty($edu->end_year) ? ' &ndash; ' . esc($edu->end_year) : '' ?>
            </span>
        </div>
        <div style="font-size:13px;color:#444;font-style:italic;"><?= esc($edu->institution) ?></div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

</div><!-- #cv-sheet -->

<script>
document.querySelectorAll('.cv-expand-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var el = document.getElementById(this.dataset.target);
        var expanded = el.classList.toggle('expanded');
        this.textContent = expanded ? this.dataset.less : this.dataset.more;
    });
});
</script>

<?= $this->endSection() ?>

