<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-4">
    <!-- ── Job Detail ──────────────────────────────────────────────────── -->
    <div class="col-lg-8">

        <!-- Header card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?php if (!empty($job->company_logo)): ?>
                        <img src="<?= base_url('uploads/logos/' . esc($job->company_logo)) ?>"
                             alt="logo" class="rounded" style="width:56px;height:56px;object-fit:cover;">
                    <?php else: ?>
                        <div class="rounded bg-secondary d-flex align-items-center justify-content-center text-white fw-bold fs-4"
                             style="width:56px;height:56px;">
                            <?= strtoupper(substr($job->company_name ?? 'C', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h2 class="fw-bold mb-0"><?= esc($job->title) ?></h2>
                        <a href="<?= base_url('companies/' . esc($job->slug ?? '')) ?>" class="text-muted text-decoration-none">
                            <?= esc($job->company_name) ?>
                        </a>
                        <?php if (!empty($job->department)): ?>
                            <span class="text-muted"> · <?= esc($job->department) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Badges -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php
                    $contractLabels = ['CDI'=>'CDI','CDD'=>'CDD','Freelance'=>'Freelance','Internship'=>'Stage','PartTime'=>'Temps partiel'];
                    ?>
                    <span class="badge bg-primary"><?= esc($contractLabels[$job->contract_type] ?? $job->contract_type) ?></span>
                    <?php if (!empty($job->contract_duration)): ?>
                        <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i><?= esc($job->contract_duration) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($job->location)): ?>
                        <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt me-1"></i><?= esc($job->location) ?></span>
                    <?php endif; ?>
                    <?php
                    $remoteLabels = ['hybrid'=>'Hybride','remote'=>'100% Remote'];
                    if (!empty($remoteLabels[$job->remote ?? ''])): ?>
                        <span class="badge bg-success"><?= $remoteLabels[$job->remote] ?></span>
                    <?php endif; ?>
                    <?php
                    $expLabels = ['any'=>'Tous niveaux','junior'=>'Junior','mid'=>'Confirmé','senior'=>'Senior','lead'=>'Lead / Expert'];
                    if (!empty($job->experience_level) && $job->experience_level !== 'any'): ?>
                        <span class="badge bg-light text-dark border"><?= esc($expLabels[$job->experience_level] ?? $job->experience_level) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($job->hierarchical_level)): ?>
                        <span class="badge bg-light text-dark border"><i class="bi bi-diagram-2 me-1"></i><?= esc($job->hierarchical_level) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($job->min_experience_years) && $job->min_experience_years > 0): ?>
                        <span class="badge bg-light text-dark border"><i class="bi bi-briefcase me-1"></i><?= (int)$job->min_experience_years ?>+ ans exp.</span>
                    <?php endif; ?>
                    <?php if (!empty($job->education_level)): ?>
                        <span class="badge bg-light text-dark border"><i class="bi bi-mortarboard me-1"></i><?= esc($job->education_level) ?><?= !empty($job->education_field) ? ' – ' . esc($job->education_field) : '' ?></span>
                    <?php endif; ?>
                </div>

                <!-- Salary -->
                <?php if (!empty($job->salary_min) || !empty($job->salary_max)): ?>
                    <?php if (!empty($job->salary_public) || (!isset($job->salary_public))): ?>
                    <p class="text-success fw-semibold mb-3">
                        <i class="bi bi-currency-euro"></i>
                        <?php if (!empty($job->salary_min)): ?>
                            <?= number_format($job->salary_min) ?>
                            <?= !empty($job->salary_max) ? ' – ' . number_format($job->salary_max) : '+' ?>
                        <?php elseif (!empty($job->salary_max)): ?>
                            jusqu'à <?= number_format($job->salary_max) ?>
                        <?php endif; ?>
                        <?= esc($job->salary_currency ?? 'EUR') ?>
                        <?php
                        $periods = ['year'=>'/ an','month'=>'/ mois','day'=>'/ jour','hour'=>'/ heure'];
                        echo isset($periods[$job->salary_period ?? '']) ? $periods[$job->salary_period] : '/ an';
                        ?>
                        <?php if (!empty($job->salary_variable)): ?>
                            <span class="text-muted fw-normal" style="font-size:.85em;">
                                + variable<?= !empty($job->salary_bonus_pct) ? ' (' . esc($job->salary_bonus_pct) . '%)' : '' ?>
                            </span>
                        <?php endif; ?>
                    </p>
                    <?php endif; ?>
                <?php endif; ?>

                <hr>

                <!-- Description -->
                <h5 class="fw-bold"><?= lang('App.job_description') ?></h5>
                <div class="text-muted mb-4"><?= nl2br(esc($job->description)) ?></div>

                <!-- Mission context -->
                <?php if (!empty($job->mission_context)): ?>
                    <h5 class="fw-bold">Contexte de la mission</h5>
                    <div class="text-muted mb-4"><?= nl2br(esc($job->mission_context)) ?></div>
                <?php endif; ?>

                <!-- Requirements / Missions -->
                <?php if (!empty($job->requirements)): ?>
                    <h5 class="fw-bold"><?= lang('App.job_requirements') ?></h5>
                    <div class="text-muted mb-4"><?= nl2br(esc($job->requirements)) ?></div>
                <?php endif; ?>

                <!-- Benefits -->
                <?php if (!empty($job->benefits)): ?>
                    <h5 class="fw-bold"><?= lang('App.job_benefits') ?></h5>
                    <div class="text-muted mb-4"><?= nl2br(esc($job->benefits)) ?></div>
                <?php endif; ?>

                <!-- Skills -->
                <?php if (!empty($jobSkills)): ?>
                    <h5 class="fw-bold"><?= lang('App.job_skills') ?></h5>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <?php foreach ($jobSkills as $skill): ?>
                            <span class="badge bg-secondary"><?= esc($skill['skill_name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Languages -->
                <?php if (!empty($languages)): ?>
                    <h5 class="fw-bold">Langues requises</h5>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <?php foreach ($languages as $lang): ?>
                            <span class="badge <?= ($lang->is_required ?? 1) ? 'bg-primary' : 'bg-light text-dark border' ?>">
                                <?= esc($lang->language) ?> — <?= esc($lang->level_code) ?>
                                <?= empty($lang->is_required) ? ' <small>(souhaité)</small>' : '' ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Certifications -->
                <?php if (!empty($certs)): ?>
                    <h5 class="fw-bold">Certifications</h5>
                    <ul class="list-unstyled mb-4">
                        <?php foreach ($certs as $cert): ?>
                            <li class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-patch-check<?= ($cert->is_required ?? 0) ? '-fill text-primary' : ' text-muted' ?>"></i>
                                <span><?= esc($cert->certification_name) ?></span>
                                <?php if (!empty($cert->delay_months)): ?>
                                    <small class="text-muted">(délai : <?= (int)$cert->delay_months ?> mois)</small>
                                <?php endif; ?>
                                <?php if (empty($cert->is_required)): ?>
                                    <small class="text-muted">— souhaitée</small>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Recruitment process -->
                <?php if (!empty($steps)): ?>
                    <h5 class="fw-bold">Process de recrutement</h5>
                    <div class="d-flex flex-column gap-2 mb-4">
                        <?php foreach ($steps as $i => $step): ?>
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                     style="width:28px;height:28px;background:var(--brand);font-size:.78rem;">
                                    <?= $i + 1 ?>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:.9rem;"><?= esc($step->step_name) ?></div>
                                    <?php if (!empty($step->responsible)): ?>
                                        <small class="text-muted"><?= esc($step->responsible) ?><?= !empty($step->duration_days) ? ' · ' . (int)$step->duration_days . ' j' : '' ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($step->description)): ?>
                                        <div class="text-muted" style="font-size:.82rem;"><?= esc($step->description) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Prescreening questions (visible questions, not expected answers) -->
                <?php if (!empty($questions)): ?>
                    <h5 class="fw-bold">Questions de présélection</h5>
                    <ol class="mb-0 ps-3">
                        <?php foreach ($questions as $q): ?>
                            <li class="mb-1" style="font-size:.9rem;">
                                <?= esc($q->question_text) ?>
                                <?php if (($q->question_type ?? '') === 'yes_no'): ?>
                                    <small class="text-muted">(Oui / Non)</small>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- ── Sidebar ─────────────────────────────────────────────────────── -->
    <div class="col-lg-4">

        <!-- Apply Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <?php if (!session()->get('logged_in')): ?>
                    <p class="text-muted small"><?= lang('App.login_to_apply') ?></p>
                    <a href="<?= base_url('login') ?>" class="btn btn-primary w-100 fw-semibold"><?= lang('App.login_to_apply') ?></a>
                <?php elseif (session()->get('user_role') !== 'job_seeker'): ?>
                    <p class="text-muted small"><?= lang('App.seekers_only') ?></p>
                <?php elseif ($hasApplied): ?>
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle me-2"></i><?= lang('App.already_applied') ?>
                    </div>
                <?php else: ?>
                    <h6 class="fw-bold mb-3"><?= lang('App.apply_job_title') ?></h6>
                    <form action="<?= base_url('jobs/' . $job->id . '/apply') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small"><?= lang('App.cv_optional') ?></label>
                            <textarea name="cover_letter" class="form-control" rows="4"
                                      placeholder="<?= lang('App.cover_letter_ph') ?>"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small"><?= lang('App.cv_optional') ?></label>
                            <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx">
                            <div class="form-text"><?= lang('App.cv_hint') ?></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-send me-1"></i><?= lang('App.btn_submit_apply') ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Company Info -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><?= lang('App.about_company') ?> <?= esc($job->company_name) ?></h6>
                <?php if (!empty($job->company_description)): ?>
                    <p class="text-muted small"><?= esc(substr($job->company_description, 0, 200)) ?>…</p>
                <?php endif; ?>
                <?php if (!empty($job->company_city)): ?>
                    <p class="small mb-1"><i class="bi bi-geo-alt text-muted me-1"></i><?= esc($job->company_city) ?>, <?= esc($job->company_country) ?></p>
                <?php endif; ?>
                <?php if (!empty($job->company_website)): ?>
                    <a href="<?= esc($job->company_website) ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                        <i class="bi bi-globe me-1"></i><?= lang('App.visit_website') ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meta -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <?php if (!empty($job->num_positions) && $job->num_positions > 1): ?>
                    <small class="text-muted d-block"><i class="bi bi-people me-1"></i><?= (int)$job->num_positions ?> postes ouverts</small>
                <?php endif; ?>
                <small class="text-muted d-block"><i class="bi bi-eye me-1"></i><?= (int) $job->views ?> <?= lang('App.job_views') ?></small>
                <small class="text-muted d-block"><i class="bi bi-calendar me-1"></i><?= lang('App.job_posted') ?> <?= !empty($job->created_at) ? date('d M Y', strtotime($job->created_at)) : '—' ?></small>
                <?php if (!empty($job->expires_at)): ?>
                    <small class="text-muted d-block"><i class="bi bi-alarm me-1"></i><?= lang('App.job_expires') ?> <?= date('d M Y', strtotime($job->expires_at)) ?></small>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recruiter actions -->
        <?php if (session()->get('logged_in') && (int) session()->get('user_id') === (int) $job->user_id): ?>
            <div class="d-flex gap-2">
                <a href="<?= base_url('jobs/edit/' . $job->id) ?>" class="btn btn-outline-secondary btn-sm flex-fill">
                    <i class="bi bi-pencil me-1"></i><?= lang('App.btn_edit') ?>
                </a>
                <form action="<?= base_url('jobs/delete/' . $job->id) ?>" method="post" class="flex-fill"
                      onsubmit="return confirm('<?= lang('App.confirm_delete') ?>')">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-trash me-1"></i><?= lang('App.btn_delete') ?></button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
