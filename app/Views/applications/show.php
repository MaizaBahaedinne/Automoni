<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$statusColors  = ['pending'=>'warning','reviewed'=>'info','shortlisted'=>'success','rejected'=>'danger','hired'=>'primary'];
$statusIcons   = ['pending'=>'bi-hourglass-split','reviewed'=>'bi-eye','shortlisted'=>'bi-star-fill','rejected'=>'bi-x-circle-fill','hired'=>'bi-check-circle-fill'];
$statusLabel   = ['pending'=>'En attente','reviewed'=>'En cours d\'examen','shortlisted'=>'Shortlisté','rejected'=>'Refusé','hired'=>'Recruté'];
$_syms  = ['EUR'=>'€','USD'=>'$','GBP'=>'£'];
$_sym   = $_syms[$app->salary_currency ?? ''] ?? ($app->salary_currency ?? '');
$_pmap  = ['annual'=>'/an','monthly'=>'/mois','daily'=>'/jour','hourly'=>'/h'];
$_plbl  = $_pmap[$app->salary_period ?? 'annual'] ?? '/an';
$scoreColor = $matchScore >= 75 ? '#22c55e' : ($matchScore >= 50 ? '#f59e0b' : ($matchScore >= 25 ? '#f97316' : '#ef4444'));
$scoreLabel = $matchScore >= 75 ? 'Excellent' : ($matchScore >= 50 ? 'Bon' : ($matchScore >= 25 ? 'Moyen' : 'Faible'));
$curStatus  = $app->status ?? 'pending';
?>

<style>
.app-status-btn{
    display:inline-flex;align-items:center;gap:.4rem;
    padding:.45rem 1rem;border-radius:50px;font-size:.8rem;font-weight:600;
    border:2px solid transparent;cursor:pointer;transition:all .15s;white-space:nowrap;
}
.app-status-btn:not(.active){background:#f1f5f9;color:#64748b;border-color:#e2e8f0;}
.app-status-btn:not(.active):hover{background:#e2e8f0;color:#334155;}
.app-status-btn.active-warning  {background:#fef9c3;color:#854d0e;border-color:#fde68a;}
.app-status-btn.active-info     {background:#e0f2fe;color:#075985;border-color:#bae6fd;}
.app-status-btn.active-success  {background:#d1fae5;color:#065f46;border-color:#6ee7b7;}
.app-status-btn.active-danger   {background:#fee2e2;color:#991b1b;border-color:#fca5a5;}
.app-status-btn.active-primary  {background:var(--brand-light);color:var(--brand-dark);border-color:var(--brand);}
.section-divider{
    display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;
    font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);
}
.section-divider::before,.section-divider::after{content:'';flex:1;height:1px;background:var(--border);}
</style>

<!-- ── Page header ─────────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('dashboard') ?>" class="btn btn-sm btn-outline-secondary" title="Retour au dashboard">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex-grow-1">
        <h2 class="fw-bold mb-0" style="font-size:1.3rem;">
            <i class="bi bi-person-lines-fill me-2" style="color:var(--brand-dark);"></i>
            <?= esc($app->first_name) ?> <?= esc($app->last_name) ?>
        </h2>
        <p class="text-muted mb-0" style="font-size:.8rem;">
            Candidature pour <strong><?= esc($app->job_title) ?></strong>
            · <?= esc($app->company_name) ?>
            · Postulée le <?= !empty($app->applied_at) ? date('d/m/Y', strtotime($app->applied_at)) : '—' ?>
        </p>
    </div>
</div>

<!-- Flash messages -->
<?php if ($error = session()->getFlashdata('error')): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill"></i><?= esc($error) ?>
</div>
<?php endif; ?>
<?php if ($success = session()->getFlashdata('success')): ?>
<div class="alert alert-success d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-check-circle-fill"></i><?= esc($success) ?>
</div>
<?php endif; ?>

<!-- ── Status update panel ──────────────────────────────────────────────────── -->
<div class="card border-0 shadow-sm mb-4" style="border-top:3px solid var(--brand) !important;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-sliders2" style="color:var(--brand-dark);font-size:1rem;"></i>
            <span class="fw-bold" style="font-size:.9rem;">Décision du recruteur</span>
            <span class="badge bg-<?= $statusColors[$curStatus] ?? 'secondary' ?> ms-auto px-3">
                <i class="bi <?= $statusIcons[$curStatus] ?? 'bi-circle' ?> me-1"></i>
                <?= $statusLabel[$curStatus] ?? ucfirst($curStatus) ?>
            </span>
        </div>

        <form action="<?= base_url('applications/' . $app->id . '/status') ?>" method="post" id="statusForm">
            <?= csrf_field() ?>
            <input type="hidden" name="status" id="statusInput" value="<?= esc($curStatus) ?>">

            <!-- Visual status picker -->
            <div class="d-flex flex-wrap gap-2 mb-3">
                <?php
                $btnColorMap = ['pending'=>'warning','reviewed'=>'info','shortlisted'=>'success','rejected'=>'danger','hired'=>'primary'];
                foreach (['pending','reviewed','shortlisted','rejected','hired'] as $s):
                    $isActive = $curStatus === $s;
                    $cls = $isActive ? 'active active-' . $btnColorMap[$s] : '';
                ?>
                <button type="button"
                        class="app-status-btn <?= $cls ?>"
                        data-status="<?= $s ?>"
                        onclick="pickStatus('<?= $s ?>')">
                    <i class="bi <?= $statusIcons[$s] ?>"></i>
                    <?= $statusLabel[$s] ?>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Rejection reason (shown only when "rejected" is picked) -->
            <div id="rejectionBlock" style="<?= $curStatus === 'rejected' ? '' : 'display:none;' ?>">
                <label class="form-label fw-semibold text-danger mb-1" style="font-size:.82rem;">
                    <i class="bi bi-chat-square-text me-1"></i>Motif de refus
                </label>
                <p class="text-muted mb-2 d-flex align-items-center gap-1" style="font-size:.78rem;">
                    <i class="bi bi-eye-fill"></i>
                    Le candidat pourra voir ce motif dans son tableau de bord.
                </p>
                <textarea name="rejection_reason" id="rejectionReason" class="form-control" rows="2"
                          style="font-size:.875rem;"
                          placeholder="Ex : Profil ne correspondant pas aux exigences requises pour ce poste…"><?= esc($app->rejection_reason ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-primary">
                    <i class="bi bi-check2-circle me-1"></i>Enregistrer la décision
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const statusColors = {
    pending:     'warning',
    reviewed:    'info',
    shortlisted: 'success',
    rejected:    'danger',
    hired:       'primary',
};
function pickStatus(val) {
    document.getElementById('statusInput').value = val;
    // Toggle rejection block
    const block = document.getElementById('rejectionBlock');
    const field = document.getElementById('rejectionReason');
    const show  = val === 'rejected';
    block.style.display = show ? '' : 'none';
    field.required = show;
    // Update button styles
    document.querySelectorAll('.app-status-btn').forEach(btn => {
        const s  = btn.dataset.status;
        const col = statusColors[s];
        btn.classList.remove('active', 'active-' + col);
        if (s === val) btn.classList.add('active', 'active-' + col);
    });
}
pickStatus(document.getElementById('statusInput').value);
</script>

<!-- Split layout -->
<div class="row g-4">

    <!-- ─── LEFT: Candidate ──────────────────────────────────────────────── -->
    <div class="col-lg-7">

        <div class="section-divider"><i class="bi bi-person-fill"></i>Profil du candidat</div>

        <!-- Candidate header card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <?php if (!empty($app->avatar)): ?>
                        <img src="<?= base_url('uploads/' . esc($app->avatar)) ?>"
                             style="width:68px;height:68px;object-fit:cover;border-radius:50%;border:2px solid var(--border);" alt="">
                    <?php else: ?>
                        <div style="width:68px;height:68px;border-radius:50%;background:linear-gradient(135deg,var(--brand),var(--brand-dark));
                                    color:#fff;font-size:1.5rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <?= strtoupper(substr($app->first_name, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-0"><?= esc($app->first_name) ?> <?= esc($app->last_name) ?></h4>
                        <?php if (!empty($profile->headline)): ?>
                            <div class="text-muted" style="font-size:.9rem;"><?= esc($profile->headline) ?></div>
                        <?php endif; ?>
                        <div class="d-flex flex-wrap gap-3 mt-1" style="font-size:.82rem;">
                            <span class="text-muted"><i class="bi bi-envelope me-1"></i><?= esc($app->email) ?></span>
                            <?php if (!empty($profile->phone)): ?>
                                <span class="text-muted"><i class="bi bi-telephone me-1"></i><?= esc($profile->phone) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($profile->city)): ?>
                                <span class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= esc($profile->city) ?><?= !empty($profile->country) ? ', ' . esc($profile->country) : '' ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- External links -->
                <?php $hasLinks = !empty($profile->linkedin) || !empty($profile->github) || !empty($profile->portfolio); ?>
                <?php if ($hasLinks): ?>
                <div class="d-flex gap-2 mt-3">
                    <?php if (!empty($profile->linkedin)): ?>
                        <a href="<?= esc($profile->linkedin) ?>" target="_blank" rel="noopener"
                           class="btn btn-outline-primary btn-sm"><i class="bi bi-linkedin me-1"></i>LinkedIn</a>
                    <?php endif; ?>
                    <?php if (!empty($profile->github)): ?>
                        <a href="<?= esc($profile->github) ?>" target="_blank" rel="noopener"
                           class="btn btn-outline-dark btn-sm"><i class="bi bi-github me-1"></i>GitHub</a>
                    <?php endif; ?>
                    <?php if (!empty($profile->portfolio)): ?>
                        <a href="<?= esc($profile->portfolio) ?>" target="_blank" rel="noopener"
                           class="btn btn-outline-secondary btn-sm"><i class="bi bi-globe me-1"></i>Portfolio</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Match score -->
        <div class="card border-0 shadow-sm mb-3" style="border-left:4px solid <?= $scoreColor ?> !important;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-stars" style="color:<?= $scoreColor ?>;"></i>
                    Correspondance avec le poste
                </h6>
                <div class="d-flex align-items-center gap-4">
                    <!-- Circular score -->
                    <div style="position:relative;width:80px;height:80px;flex-shrink:0;">
                        <svg width="80" height="80" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="33" fill="none" stroke="#e2e8f0" stroke-width="9"/>
                            <circle cx="40" cy="40" r="33" fill="none"
                                    stroke="<?= $scoreColor ?>" stroke-width="9"
                                    stroke-dasharray="<?= round(2 * M_PI * 33 * $matchScore / 100, 1) ?> 207.3"
                                    stroke-linecap="round"
                                    transform="rotate(-90 40 40)"/>
                        </svg>
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                                    font-weight:900;font-size:1.1rem;color:<?= $scoreColor ?>;">
                            <?= $matchScore ?>%
                        </div>
                    </div>
                    <!-- Breakdown -->
                    <div class="flex-grow-1" style="font-size:.8rem;">
                        <div class="fw-bold mb-2" style="color:<?= $scoreColor ?>;font-size:.95rem;"><?= $scoreLabel ?></div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="bi bi-tools me-1 text-muted"></i>Compétences</span>
                                <span><?= $matchDetails['skills']['matched'] ?>/<?= $matchDetails['skills']['total'] ?: '—' ?></span>
                            </div>
                            <div class="progress" style="height:5px;"><div class="progress-bar" style="width:<?= $matchDetails['skills']['max'] > 0 ? round($matchDetails['skills']['score'] / $matchDetails['skills']['max'] * 100) : 50 ?>%;background:<?= $scoreColor ?>;"></div></div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="bi bi-briefcase me-1 text-muted"></i>Expérience</span>
                                <span><?= $matchDetails['exp']['years'] ?> an<?= $matchDetails['exp']['years'] != 1 ? 's' : '' ?><?= $matchDetails['exp']['required'] > 0 ? ' / ' . $matchDetails['exp']['required'] . ' requis' : '' ?></span>
                            </div>
                            <div class="progress" style="height:5px;"><div class="progress-bar" style="width:<?= round($matchDetails['exp']['score'] / $matchDetails['exp']['max'] * 100) ?>%;background:<?= $scoreColor ?>;"></div></div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span><i class="bi bi-translate me-1 text-muted"></i>Langues</span>
                                <span><?= $matchDetails['langs']['matched'] ?>/<?= $matchDetails['langs']['total'] ?: '—' ?></span>
                            </div>
                            <div class="progress" style="height:5px;"><div class="progress-bar" style="width:<?= $matchDetails['langs']['max'] > 0 ? round($matchDetails['langs']['score'] / $matchDetails['langs']['max'] * 100) : 100 ?>%;background:<?= $scoreColor ?>;"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CV -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-person me-2 text-primary"></i>CV</h6>
                <?php if (!empty($app->cv_file)): ?>
                    <?php
                    // Application-specific upload path
                    $cvPath = WRITEPATH . 'uploads/applications/' . $app->cv_file;
                    $cvUrl  = file_exists($cvPath)
                        ? base_url('uploads/applications/' . esc($app->cv_file))
                        : base_url('uploads/' . esc($app->cv_file));
                    $ext = strtolower(pathinfo($app->cv_file, PATHINFO_EXTENSION));
                    $icon = $ext === 'pdf' ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-word text-primary';
                    ?>
                    <div class="d-flex align-items-center gap-3 p-3"
                         style="background:var(--bg);border:1px solid var(--border);border-radius:10px;">
                        <i class="bi <?= $icon ?>" style="font-size:2rem;"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:.875rem;"><?= esc($app->cv_file) ?></div>
                            <small class="text-muted text-uppercase"><?= $ext ?></small>
                        </div>
                        <a href="<?= $cvUrl ?>" target="_blank" rel="noopener"
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-download me-1"></i>Télécharger
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Aucun CV joint à cette candidature.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cover letter -->
        <?php if (!empty($app->cover_letter)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-chat-quote me-2 text-primary"></i>Lettre de motivation</h6>
                <div style="background:var(--bg);border-left:3px solid var(--brand);padding:1rem 1.25rem;border-radius:0 8px 8px 0;
                            white-space:pre-wrap;font-size:.875rem;line-height:1.7;color:var(--text);">
                    <?= esc($app->cover_letter) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Prescreening answers -->
        <?php if (!empty($questions)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-patch-question-fill me-2" style="color:var(--brand-dark);"></i>Questions de présélection
                </h6>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($questions as $qi => $q): ?>
                        <?php
                        $answer    = $answerMap[(int) $q->id] ?? null;
                        $expected  = !empty($q->expected_answer) ? strtolower(trim($q->expected_answer)) : null;
                        $answerLow = $answer !== null ? strtolower(trim($answer)) : null;
                        $match     = $expected !== null && $answerLow !== null && $answerLow === $expected;
                        $noMatch   = $expected !== null && $answerLow !== null && $answerLow !== $expected;
                        ?>
                        <div style="border:1px solid var(--border);border-radius:10px;padding:.875rem 1rem;">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="fw-semibold" style="font-size:.875rem;">
                                    <?= $qi + 1 ?>. <?= esc($q->question_text) ?>
                                    <?php if (!empty($q->is_eliminatory)): ?>
                                        <span class="badge bg-danger ms-1" style="font-size:.65rem;">Éliminatoire</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($match): ?>
                                    <span class="badge bg-success text-nowrap"><i class="bi bi-check2-circle me-1"></i>Correct</span>
                                <?php elseif ($noMatch): ?>
                                    <span class="badge bg-danger text-nowrap"><i class="bi bi-x-circle me-1"></i>Incorrect</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($answer !== null): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted" style="font-size:.78rem;">Réponse :</span>
                                    <?php if ($q->question_type === 'yes_no'): ?>
                                        <span class="badge <?= strtolower($answer) === 'yes' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= strtolower($answer) === 'yes' ? 'Oui' : 'Non' ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="font-size:.875rem;"><?= esc($answer) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($expected !== null): ?>
                                    <div class="text-muted mt-1" style="font-size:.75rem;">
                                        Réponse attendue : <strong><?= $q->question_type === 'yes_no' ? ($expected === 'yes' ? 'Oui' : 'Non') : esc($q->expected_answer) ?></strong>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:.8rem;"><i class="bi bi-dash me-1"></i>Sans réponse</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Skills -->
        <?php if (!empty($skills)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-tools me-2 text-muted"></i>Compétences</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($skills as $sk): ?>
                        <?php
                        $isMatch = in_array(strtolower(trim($sk->skill_name)),
                                            array_map(fn($s) => strtolower(trim($s['skill_name'])), $jobSkills), true);
                        ?>
                        <span class="badge <?= $isMatch ? 'bg-primary' : 'bg-light text-dark border' ?>" style="font-size:.8rem;">
                            <?= esc($sk->skill_name) ?>
                            <?= $isMatch ? ' <i class="bi bi-check2"></i>' : '' ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($jobSkills)): ?>
                    <p class="text-muted mt-2 mb-0" style="font-size:.75rem;"><i class="bi bi-circle-fill text-primary me-1" style="font-size:6px;"></i>= correspond aux compétences requises</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Experiences -->
        <?php if (!empty($experiences)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-briefcase me-2 text-muted"></i>Expériences</h6>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($experiences as $exp): ?>
                    <div class="d-flex gap-3">
                        <?php if (!empty($exp->org_logo)): ?>
                            <img src="<?= base_url('uploads/' . esc($exp->org_logo)) ?>"
                                 style="width:36px;height:36px;object-fit:cover;border-radius:6px;flex-shrink:0;border:1px solid var(--border);" alt="">
                        <?php else: ?>
                            <div style="width:36px;height:36px;border-radius:6px;background:var(--brand-light);
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-building" style="color:var(--brand);font-size:.9rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="fw-semibold" style="font-size:.875rem;"><?= esc($exp->title) ?></div>
                            <div class="text-muted" style="font-size:.8rem;"><?= esc($exp->company) ?></div>
                            <div class="text-muted" style="font-size:.75rem;">
                                <?= esc($exp->start_date) ?> — <?= !empty($exp->is_current) ? 'Présent' : esc($exp->end_date ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Education -->
        <?php if (!empty($education)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-mortarboard me-2 text-muted"></i>Formation</h6>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($education as $edu): ?>
                    <div>
                        <div class="fw-semibold" style="font-size:.875rem;"><?= esc($edu->degree ?? '') ?><?= !empty($edu->field) ? ' – ' . esc($edu->field) : '' ?></div>
                        <div class="text-muted" style="font-size:.8rem;"><?= esc($edu->institution ?? $edu->org_name ?? '') ?></div>
                        <?php if (!empty($edu->start_year)): ?>
                            <div class="text-muted" style="font-size:.75rem;"><?= esc($edu->start_year) ?><?= !empty($edu->end_year) ? ' — ' . esc($edu->end_year) : (!empty($edu->is_current) ? ' — Présent' : '') ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Languages -->
        <?php if (!empty($userLangs)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-translate me-2 text-muted"></i>Langues</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($userLangs as $ul): ?>
                        <span class="badge bg-light text-dark border" style="font-size:.8rem;">
                            <?= esc($ul->name) ?><?= !empty($ul->level) ? ' — ' . esc($ul->level) : '' ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recruiter note -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2 text-muted"></i>Note recruteur</h6>
                <form action="<?= base_url('applications/' . $app->id . '/note') ?>" method="post">
                    <?= csrf_field() ?>
                    <textarea name="note" class="form-control mb-2" rows="3"
                              placeholder="Ajoutez une note interne..."><?= esc($app->recruiter_note ?? '') ?></textarea>
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-save me-1"></i>Enregistrer</button>
                </form>
            </div>
        </div>

    </div>

    <!-- ─── RIGHT: Job Offer ─────────────────────────────────────────────── -->
    <div class="col-lg-5">
        <div style="position:sticky;top:1.5rem;">

            <div class="section-divider"><i class="bi bi-briefcase-fill"></i>Offre d'emploi</div>

            <!-- Job header card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <?php if (!empty($app->company_logo)): ?>
                            <img src="<?= base_url('uploads/logos/' . esc($app->company_logo)) ?>"
                                 style="width:52px;height:52px;object-fit:cover;border-radius:10px;" alt="">
                        <?php else: ?>
                            <div style="width:52px;height:52px;border-radius:10px;background:linear-gradient(135deg,var(--brand-dark),#7c3aed);
                                        color:#fff;font-size:1.2rem;font-weight:800;display:flex;align-items:center;justify-content:center;">
                                <?= strtoupper(substr($app->company_name ?? 'J', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h5 class="fw-bold mb-0"><?= esc($app->job_title) ?></h5>
                            <div class="text-muted" style="font-size:.875rem;"><?= esc($app->company_name) ?></div>
                        </div>
                    </div>

                    <!-- Badges -->
                    <?php
                    $contractLabels = ['CDI'=>'CDI','CDD'=>'CDD','Freelance'=>'Freelance','Internship'=>'Stage','PartTime'=>'Temps partiel'];
                    $remoteLabels   = ['hybrid'=>'Hybride','remote'=>'100% Remote'];
                    $expLabels      = ['any'=>'Tous niveaux','junior'=>'Junior','mid'=>'Confirmé','senior'=>'Senior','lead'=>'Lead / Expert'];
                    ?>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-primary"><?= esc($contractLabels[$app->contract_type] ?? $app->contract_type) ?></span>
                        <?php if (!empty($app->job_location)): ?>
                            <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt me-1"></i><?= esc($app->job_location) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($remoteLabels[$app->remote ?? ''])): ?>
                            <span class="badge bg-success"><?= $remoteLabels[$app->remote] ?></span>
                        <?php endif; ?>
                        <?php if (!empty($app->experience_level) && $app->experience_level !== 'any'): ?>
                            <span class="badge bg-light text-dark border"><?= esc($expLabels[$app->experience_level] ?? $app->experience_level) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($app->min_experience_years) && $app->min_experience_years > 0): ?>
                            <span class="badge bg-light text-dark border"><i class="bi bi-briefcase me-1"></i><?= (int)$app->min_experience_years ?>+ ans</span>
                        <?php endif; ?>
                        <?php if (!empty($app->education_level)): ?>
                            <span class="badge bg-light text-dark border"><i class="bi bi-mortarboard me-1"></i><?= esc($app->education_level) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Salary -->
                    <?php if (!empty($app->salary_min) || !empty($app->salary_max)): ?>
                        <p class="text-success fw-semibold mb-3">
                            <?= number_format($app->salary_min ?? 0) ?><?= !empty($app->salary_max) ? '–' . number_format($app->salary_max) : '+' ?>
                            <strong><?= esc($_sym) ?></strong>
                            <span class="text-muted fw-normal" style="font-size:.85em;"><?= $_plbl ?></span>
                        </p>
                    <?php endif; ?>

                    <a href="<?= base_url('jobs/' . esc($app->job_slug)) ?>" target="_blank"
                       class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-arrow-up-right-square me-1"></i>Voir l'offre complète
                    </a>
                </div>
            </div>

            <!-- Job description -->
            <?php if (!empty($app->job_description)): ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-2">Description du poste</h6>
                    <div class="text-muted" style="font-size:.875rem;line-height:1.7;max-height:220px;overflow-y:auto;">
                        <?= nl2br(esc($app->job_description)) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Requirements -->
            <?php if (!empty($app->job_requirements)): ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-2">Prérequis</h6>
                    <div class="text-muted" style="font-size:.875rem;line-height:1.7;max-height:180px;overflow-y:auto;">
                        <?= nl2br(esc($app->job_requirements)) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Required job skills -->
            <?php if (!empty($jobSkills)): ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-2">Compétences requises</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($jobSkills as $jsk): ?>
                            <span class="badge bg-secondary" style="font-size:.8rem;"><?= esc($jsk['skill_name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Required job languages -->
            <?php if (!empty($jobLangs)): ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-2">Langues requises</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($jobLangs as $jl): ?>
                            <span class="badge <?= !empty($jl->is_required) ? 'bg-primary' : 'bg-light text-dark border' ?>" style="font-size:.8rem;">
                                <?= esc($jl->language) ?> — <?= esc($jl->level_code) ?>
                                <?= empty($jl->is_required) ? ' <small>(souhaité)</small>' : '' ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- sticky -->
    </div>

</div>

<?= $this->endSection() ?>
