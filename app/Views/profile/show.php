<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
/* ── LinkedIn-style Profile ───────────────────────────────────────────── */
.lp-cover {
    height: 165px;
    background: linear-gradient(135deg, #0A66C2 0%, #0d1b2a 100%);
    border-radius: 8px 8px 0 0;
    position: relative;
}
.lp-avatar-wrap {
    position: absolute;
    bottom: -46px;
    left: 24px;
}
.lp-avatar {
    width: 94px; height: 94px;
    border-radius: 50%;
    border: 4px solid #fff;
    object-fit: cover;
    flex-shrink: 0;
}
.lp-avatar-init {
    width: 94px; height: 94px;
    border-radius: 50%;
    border: 4px solid #fff;
    background: #1a3c8f;
    color: #fff;
    font-size: 34px;
    font-weight: 900;
    display: flex; align-items: center; justify-content: center;
    box-sizing: border-box;
}
.lp-header-card {
    background: #fff;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.09);
    padding: 58px 24px 20px;
    margin-bottom: 12px;
}
.lp-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.09);
    padding: 20px 24px;
    margin-bottom: 12px;
}
.lp-name { font-size: 22px; font-weight: 700; color: #000; line-height: 1.2; }
.lp-headline { font-size: 14px; color: #555; margin-top: 4px; }
.lp-meta { font-size: 13px; color: #666; margin-top: 8px; display: flex; flex-wrap: wrap; gap: 4px 16px; }
.lp-meta a { color: #0A66C2; text-decoration: none; }
.lp-meta a:hover { text-decoration: underline; }
.lp-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
.lp-sec-title { font-size: 18px; font-weight: 600; color: #000; margin-bottom: 14px; }
.lp-item { padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
.lp-item:last-child { border-bottom: none; padding-bottom: 0; }
.lp-item:first-child { padding-top: 0; }
.lp-tl { display: flex; gap: 14px; }
.lp-tl-icon {
    width: 44px; height: 44px; min-width: 44px;
    border-radius: 4px; background: #f3f2f0;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #666; flex-shrink: 0;
}
.lp-tl-content { flex: 1; min-width: 0; }
.lp-tl-title { font-size: 15px; font-weight: 600; color: #000; }
.lp-tl-sub { font-size: 13px; color: #444; margin-top: 2px; }
.lp-tl-dates { font-size: 12px; color: #777; margin-top: 3px; }
.lp-desc {
    font-size: 13px; color: #333; margin-top: 6px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}
.lp-desc.expanded { display: block; -webkit-line-clamp: unset; }
.lp-expand-btn {
    background: none; border: none; padding: 0;
    font-size: 13px; color: #0A66C2; cursor: pointer;
    display: inline-block; margin-top: 4px; font-weight: 500;
}
.lp-expand-btn:hover { text-decoration: underline; }
.lp-skill-badge {
    display: inline-block;
    border: 1px solid #ccc; border-radius: 20px;
    padding: 3px 12px; font-size: 12.5px; color: #333;
    background: #f8f9fa; margin: 3px 4px 3px 0;
}
.lp-lang-badge {
    display: inline-flex; align-items: center; gap: 7px;
    background: #eef3fb; border: 1px solid #c5d8f8;
    border-radius: 6px; padding: 5px 11px;
    font-size: 13px; color: #1a3c8f; margin: 4px 6px 4px 0;
}
.lp-lang-level {
    font-size: 11px; background: #0A66C2; color: #fff;
    border-radius: 4px; padding: 1px 6px;
}
.lp-cert-logo {
    width: 44px; height: 44px; min-width: 44px;
    object-fit: contain; border-radius: 4px;
    border: 1px solid #ddd; padding: 3px; background: #fff;
}
.lp-contact-row {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; color: #333; padding: 7px 0;
    border-bottom: 1px solid #f0f0f0; text-decoration: none;
}
.lp-contact-row:last-child { border-bottom: none; }
.lp-contact-row i { color: #0A66C2; font-size: 16px; min-width: 20px; }
.lp-contact-row span { color: #333; }
@media (max-width: 767px) {
    .lp-cover { height: 108px; }
    .lp-avatar-wrap { bottom: -38px; left: 16px; }
    .lp-avatar, .lp-avatar-init { width: 78px; height: 78px; font-size: 28px; }
    .lp-header-card { padding-top: 48px; }
    .lp-card, .lp-header-card { padding: 16px; }
}
@media print {
    .lp-actions, nav, header, .navbar, footer { display: none !important; }
    .lp-card, .lp-header-card { box-shadow: none !important; border: 1px solid #eee; }
    .lp-desc { display: block !important; -webkit-line-clamp: unset !important; }
}
</style>

<div class="container py-4" style="max-width:1060px;">

    <!-- Cover + Header card -->
    <div class="lp-cover">
        <div class="lp-avatar-wrap">
            <?php if (!empty($profile?->avatar)): ?>
                <img src="<?= base_url('uploads/' . esc($profile->avatar)) ?>" class="lp-avatar" alt="">
            <?php else: ?>
                <div class="lp-avatar-init"><?= strtoupper(substr($user?->first_name ?? 'U', 0, 1)) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="lp-header-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <div class="lp-name">
                    <?= esc(trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? ''))) ?>
                </div>
                <?php if (!empty($profile?->headline)): ?>
                <div class="lp-headline"><?= esc($profile->headline) ?></div>
                <?php endif; ?>
                <div class="lp-meta">
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
                    <a href="<?= esc($profile->github) ?>" target="_blank" rel="noopener" style="color:#333;">
                        <i class="bi bi-github me-1"></i>GitHub
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($profile?->portfolio)): ?>
                    <a href="<?= esc($profile->portfolio) ?>" target="_blank" rel="noopener" style="color:#333;">
                        <i class="bi bi-globe me-1"></i>Portfolio
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="lp-actions">
            <?php if (session()->get('user_id') == $user?->id): ?>
            <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i><?= lang('App.edit_profile') ?>
            </a>
            <?php endif; ?>
            <?php if (!empty($profile?->cv_file)): ?>
            <a href="<?= base_url('profile/cv/download') ?>" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download me-1"></i><?= lang('App.download_cv') ?>
            </a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer me-1"></i>PDF
            </button>
        </div>
    </div>

    <div class="row g-3">

        <!-- ── Main column ──────────────────────────────────────── -->
        <div class="col-lg-8">

            <!-- À propos -->
            <?php if (!empty($profile?->summary)): ?>
            <div class="lp-card">
                <div class="lp-sec-title"><?= lang('App.section_about') ?></div>
                <div class="lp-desc" id="lp-summary"><?= nl2br(esc($profile->summary)) ?></div>
                <?php if (mb_strlen($profile->summary) > 300): ?>
                <button class="lp-expand-btn" data-target="lp-summary" data-more="Voir plus &#8594;" data-less="R&#233;duire &#8593;">Voir plus &#8594;</button>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Compétences -->
            <?php if (!empty($skills)): ?>
            <div class="lp-card">
                <div class="lp-sec-title"><?= lang('App.section_skills') ?></div>
                <?php foreach ($skills as $skill): ?>
                    <span class="lp-skill-badge"><?= esc($skill->skill_name) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Expériences -->
            <?php if (!empty($experiences)): ?>
            <div class="lp-card">
                <div class="lp-sec-title"><?= lang('App.section_experience') ?></div>
                <?php foreach ($experiences as $i => $exp): ?>
                <div class="lp-item">
                    <div class="lp-tl">
                        <div class="lp-tl-icon"><i class="bi bi-briefcase"></i></div>
                        <div class="lp-tl-content">
                            <div class="lp-tl-title">
                                <?= esc($exp->title ?? '') ?>
                                <?php if (!empty($exp->level)): ?>
                                <span class="badge bg-primary ms-1 fw-normal" style="font-size:11px;"><?= esc($exp->level) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($exp->contract)): ?>
                                <span class="badge bg-secondary ms-1 fw-normal" style="font-size:11px;"><?= esc($exp->contract) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="lp-tl-sub">
                                <strong><?= esc($exp->company) ?></strong>
                                <?php if (!empty($exp->department)): ?> &middot; <?= esc($exp->department) ?><?php endif; ?>
                                <?php if (!empty($exp->location)): ?> &middot; <?= esc($exp->location) ?><?php endif; ?>
                            </div>
                            <div class="lp-tl-dates">
                                <?= $exp->start_date ? date('M Y', strtotime($exp->start_date)) : '' ?>
                                &ndash;
                                <?= $exp->is_current ? lang('App.present') : ($exp->end_date ? date('M Y', strtotime($exp->end_date)) : lang('App.present')) ?>
                                <?php if (!empty($exp->manager_name)): ?>
                                &middot; <i class="bi bi-person-badge"></i> <?= esc($exp->manager_name) ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($exp->description)): ?>
                            <div class="lp-desc mt-2" id="lp-exp-<?= (int)$i ?>"><?= nl2br(esc($exp->description)) ?></div>
                            <?php if (mb_strlen($exp->description) > 250): ?>
                            <button class="lp-expand-btn" data-target="lp-exp-<?= (int)$i ?>" data-more="Voir plus &#8594;" data-less="R&#233;duire &#8593;">Voir plus &#8594;</button>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php if (!empty($exp->skills_gained)): ?>
                            <div class="mt-2">
                                <?php foreach (array_filter(array_map('trim', explode(',', $exp->skills_gained))) as $sg): ?>
                                    <span class="lp-skill-badge" style="background:#eaf3fc;border-color:#91bfee;color:#1a3c8f;"><?= esc($sg) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Formations -->
            <?php if (!empty($education)): ?>
            <div class="lp-card">
                <div class="lp-sec-title"><?= lang('App.section_education') ?></div>
                <?php foreach ($education as $edu): ?>
                <div class="lp-item">
                    <div class="lp-tl">
                        <div class="lp-tl-icon"><i class="bi bi-mortarboard"></i></div>
                        <div class="lp-tl-content">
                            <div class="lp-tl-title"><?= esc($edu->institution) ?></div>
                            <div class="lp-tl-sub">
                                <?= esc($edu->degree) ?>
                                <?php if (!empty($edu->niveau ?? '')): ?>
                                <span class="badge bg-primary ms-1 fw-normal" style="font-size:11px;"><?= esc($edu->niveau) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($edu->field)): ?> &mdash; <?= esc($edu->field) ?><?php endif; ?>
                            </div>
                            <?php if (!empty($edu->start_year) || !empty($edu->end_year)): ?>
                            <div class="lp-tl-dates">
                                <?= !empty($edu->start_year) ? esc($edu->start_year) : '' ?>
                                <?= !empty($edu->end_year) ? ' &ndash; ' . esc($edu->end_year) : '' ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Certifications -->
            <?php if (!empty($certifications)): ?>
            <div class="lp-card">
                <div class="lp-sec-title"><?= lang('App.section_certifications') ?></div>
                <?php foreach ($certifications as $cert): ?>
                <div class="lp-item">
                    <div class="lp-tl">
                        <?php if (!empty($cert->logo_file)): ?>
                        <img src="<?= base_url('uploads/cert_logos/' . esc($cert->logo_file)) ?>" class="lp-cert-logo" alt="">
                        <?php else: ?>
                        <div class="lp-tl-icon"><i class="bi bi-award"></i></div>
                        <?php endif; ?>
                        <div class="lp-tl-content">
                            <div class="lp-tl-title"><?= esc($cert->name) ?></div>
                            <?php if (!empty($cert->organization)): ?>
                            <div class="lp-tl-sub"><?= esc($cert->organization) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($cert->issue_date)): ?>
                            <div class="lp-tl-dates">
                                <?= date('M Y', strtotime($cert->issue_date)) ?>
                                <?= !empty($cert->expiry_date) ? ' &rarr; ' . date('M Y', strtotime($cert->expiry_date)) : '' ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($cert->credential_url)): ?>
                            <a href="<?= esc($cert->credential_url) ?>" target="_blank" rel="noopener" class="lp-expand-btn">
                                <i class="bi bi-link-45deg me-1"></i>Voir la certification
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Projets -->
            <?php if (!empty($projects)): ?>
            <div class="lp-card">
                <div class="lp-sec-title"><?= lang('App.section_projects') ?></div>
                <?php foreach ($projects as $i => $proj):
                    $memberObjs  = model(\App\Models\ProjectMemberModel::class)->getMembersByProject($proj->id);
                    $memberNames = [];
                    foreach ($memberObjs as $m) {
                        $mu = model(\App\Models\UserModel::class)->find($m->user_id);
                        if ($mu) { $memberNames[] = esc(trim($mu->first_name . ' ' . $mu->last_name)); }
                    }
                ?>
                <div class="lp-item">
                    <div class="lp-tl">
                        <div class="lp-tl-icon"><i class="bi bi-kanban"></i></div>
                        <div class="lp-tl-content">
                            <div class="lp-tl-title"><?= esc($proj->name) ?></div>
                            <?php if (!empty($proj->start_date)): ?>
                            <div class="lp-tl-dates">
                                <?= date('M Y', strtotime($proj->start_date)) ?>
                                &ndash;
                                <?= $proj->is_current ? lang('App.present') : (!empty($proj->end_date) ? date('M Y', strtotime($proj->end_date)) : lang('App.present')) ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($memberNames)): ?>
                            <div class="lp-tl-dates mt-1"><i class="bi bi-people me-1"></i><?= implode(', ', $memberNames) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($proj->description)): ?>
                            <div class="lp-desc mt-2" id="lp-proj-<?= (int)$i ?>"><?= nl2br(esc($proj->description)) ?></div>
                            <?php if (mb_strlen($proj->description) > 250): ?>
                            <button class="lp-expand-btn" data-target="lp-proj-<?= (int)$i ?>" data-more="Voir plus &#8594;" data-less="R&#233;duire &#8593;">Voir plus &#8594;</button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Bénévolat -->
            <?php if (!empty($volunteering)): ?>
            <div class="lp-card">
                <div class="lp-sec-title"><?= lang('App.section_volunteering') ?></div>
                <?php foreach ($volunteering as $i => $vol): ?>
                <div class="lp-item">
                    <div class="lp-tl">
                        <div class="lp-tl-icon"><i class="bi bi-heart"></i></div>
                        <div class="lp-tl-content">
                            <div class="lp-tl-title"><?= esc($vol->organization) ?></div>
                            <?php if (!empty($vol->position)): ?>
                            <div class="lp-tl-sub"><?= esc($vol->position) ?></div>
                            <?php endif; ?>
                            <div class="lp-tl-dates">
                                <?= !empty($vol->start_date) ? date('M Y', strtotime($vol->start_date)) : '' ?>
                                &ndash;
                                <?= $vol->is_current ? lang('App.present') : (!empty($vol->end_date) ? date('M Y', strtotime($vol->end_date)) : lang('App.present')) ?>
                            </div>
                            <?php if (!empty($vol->description)): ?>
                            <div class="lp-desc mt-2" id="lp-vol-<?= (int)$i ?>"><?= nl2br(esc($vol->description)) ?></div>
                            <?php if (mb_strlen($vol->description) > 250): ?>
                            <button class="lp-expand-btn" data-target="lp-vol-<?= (int)$i ?>" data-more="Voir plus &#8594;" data-less="R&#233;duire &#8593;">Voir plus &#8594;</button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div><!-- /col-lg-8 -->

        <!-- ── Sidebar ──────────────────────────────────────────── -->
        <div class="col-lg-4">

            <!-- Informations de contact -->
            <div class="lp-card">
                <div class="lp-sec-title" style="font-size:15px;"><?= lang('App.contact_info') ?></div>
                <?php if (!empty($user?->email)): ?>
                <a class="lp-contact-row" href="mailto:<?= esc($user->email) ?>">
                    <i class="bi bi-envelope"></i>
                    <span><?= esc($user->email) ?></span>
                </a>
                <?php endif; ?>
                <?php $phone = trim(($profile?->phone_code ?? '') . ' ' . ($profile?->phone ?? '')); ?>
                <?php if (!empty(trim($profile?->phone ?? ''))): ?>
                <div class="lp-contact-row">
                    <i class="bi bi-telephone"></i>
                    <span><?= esc($phone) ?></span>
                </div>
                <?php endif; ?>
                <?php $locSb = implode(', ', array_filter([$profile?->city ?? '', $profile?->country ?? ''])); ?>
                <?php if (!empty($locSb)): ?>
                <div class="lp-contact-row">
                    <i class="bi bi-geo-alt"></i>
                    <span><?= esc($locSb) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile?->linkedin)): ?>
                <a class="lp-contact-row" href="<?= esc($profile->linkedin) ?>" target="_blank" rel="noopener">
                    <i class="bi bi-linkedin"></i>
                    <span>LinkedIn</span>
                </a>
                <?php endif; ?>
                <?php if (!empty($profile?->github)): ?>
                <a class="lp-contact-row" href="<?= esc($profile->github) ?>" target="_blank" rel="noopener">
                    <i class="bi bi-github"></i>
                    <span>GitHub</span>
                </a>
                <?php endif; ?>
                <?php if (!empty($profile?->portfolio)): ?>
                <a class="lp-contact-row" href="<?= esc($profile->portfolio) ?>" target="_blank" rel="noopener">
                    <i class="bi bi-globe"></i>
                    <span>Portfolio</span>
                </a>
                <?php endif; ?>
            </div>

            <!-- Langues -->
            <?php if (!empty($languages)): ?>
            <div class="lp-card">
                <div class="lp-sec-title" style="font-size:15px;"><?= lang('App.section_languages') ?></div>
                <?php foreach ($languages as $lang): ?>
                <span class="lp-lang-badge">
                    <?= esc($lang->name) ?>
                    <span class="lp-lang-level"><?= esc($lang->level) ?></span>
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div><!-- /col-lg-4 -->

    </div><!-- /row -->

</div><!-- /container -->

<script>
document.querySelectorAll('.lp-expand-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var el = document.getElementById(this.dataset.target);
        if (!el) return;
        var expanded = el.classList.toggle('expanded');
        this.textContent = expanded ? (this.dataset.less || 'R\u00e9duire \u2191') : (this.dataset.more || 'Voir plus \u2192');
    });
});
</script>

<?= $this->endSection() ?>
