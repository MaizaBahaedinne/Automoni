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
                    <?php
                    $_curr  = $job->salary_currency ?? '';
                    $_syms  = ['EUR'=>'€','USD'=>'$','GBP'=>'£'];
                    $_sym   = $_syms[$_curr] ?? $_curr;
                    $_pmap  = ['annual'=>'/ an','monthly'=>'/ mois','daily'=>'/ jour','hourly'=>'/ heure'];
                    $_plbl  = $_pmap[$job->salary_period ?? 'annual'] ?? '/ an';
                    ?>
                    <p class="text-success fw-semibold mb-3">
                        <?php if (!empty($job->salary_min)): ?>
                            <?= number_format($job->salary_min) ?>
                            <?= !empty($job->salary_max) ? ' – ' . number_format($job->salary_max) : '+' ?>
                        <?php elseif (!empty($job->salary_max)): ?>
                            jusqu'à <?= number_format($job->salary_max) ?>
                        <?php endif; ?>
                        <span class="fw-bold"><?= esc($_sym) ?></span>
                        <span class="text-muted fw-normal" style="font-size:.85em;"><?= $_plbl ?></span>
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
                    <p class="text-muted small mb-3">Rejoignez <strong><?= esc($job->company_name) ?></strong> en tant que <strong><?= esc($job->title) ?></strong>.</p>
                    <button type="button" class="btn btn-primary w-100 fw-semibold"
                            data-bs-toggle="modal" data-bs-target="#applyModal">
                        <i class="bi bi-send me-2"></i>Postuler à cette offre
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Match Score (job seekers only) -->
        <?php if ($matchScore !== null): ?>
        <?php
            $scoreColor = $matchScore >= 75 ? '#22c55e' : ($matchScore >= 50 ? '#f59e0b' : ($matchScore >= 25 ? '#f97316' : '#ef4444'));
            $scoreLabel = $matchScore >= 75 ? 'Excellent' : ($matchScore >= 50 ? 'Bon' : ($matchScore >= 25 ? 'Moyen' : 'Faible'));
        ?>
        <div class="card border-0 shadow-sm mb-3" style="border-left:4px solid <?= $scoreColor ?> !important;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-stars" style="color:<?= $scoreColor ?>;"></i>
                    Correspondance profil
                </h6>
                <!-- Circular score -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="position:relative;width:72px;height:72px;flex-shrink:0;">
                        <svg width="72" height="72" viewBox="0 0 72 72">
                            <circle cx="36" cy="36" r="30" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                            <circle cx="36" cy="36" r="30" fill="none"
                                    stroke="<?= $scoreColor ?>" stroke-width="8"
                                    stroke-dasharray="<?= round(2 * M_PI * 30 * $matchScore / 100, 1) ?> 188.5"
                                    stroke-linecap="round"
                                    transform="rotate(-90 36 36)"/>
                        </svg>
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                                    font-weight:900;font-size:1rem;color:<?= $scoreColor ?>;">
                            <?= $matchScore ?>%
                        </div>
                    </div>
                    <div>
                        <div class="fw-bold" style="color:<?= $scoreColor ?>;font-size:1rem;"><?= $scoreLabel ?></div>
                        <div class="text-muted" style="font-size:.75rem;">Score de votre profil<br>par rapport à ce poste</div>
                    </div>
                </div>
                <!-- Breakdown -->
                <div class="d-flex flex-column gap-2" style="font-size:.78rem;">
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold"><i class="bi bi-tools me-1 text-muted"></i>Compétences</span>
                            <span><?= $matchDetails['skills']['matched'] ?>/<?= $matchDetails['skills']['total'] ?: '—' ?></span>
                        </div>
                        <div class="progress" style="height:5px;border-radius:3px;">
                            <div class="progress-bar" style="width:<?= $matchDetails['skills']['max'] > 0 ? round($matchDetails['skills']['score'] / $matchDetails['skills']['max'] * 100) : 50 ?>%;background:<?= $scoreColor ?>;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold"><i class="bi bi-briefcase me-1 text-muted"></i>Expérience</span>
                            <span><?= $matchDetails['exp']['years'] ?> an<?= $matchDetails['exp']['years'] != 1 ? 's' : '' ?><?= $matchDetails['exp']['required'] > 0 ? ' / ' . $matchDetails['exp']['required'] . ' requis' : '' ?></span>
                        </div>
                        <div class="progress" style="height:5px;border-radius:3px;">
                            <div class="progress-bar" style="width:<?= round($matchDetails['exp']['score'] / $matchDetails['exp']['max'] * 100) ?>%;background:<?= $scoreColor ?>;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold"><i class="bi bi-translate me-1 text-muted"></i>Langues</span>
                            <span><?= $matchDetails['langs']['matched'] ?>/<?= $matchDetails['langs']['total'] ?: '—' ?></span>
                        </div>
                        <div class="progress" style="height:5px;border-radius:3px;">
                            <div class="progress-bar" style="width:<?= $matchDetails['langs']['max'] > 0 ? round($matchDetails['langs']['score'] / $matchDetails['langs']['max'] * 100) : 100 ?>%;background:<?= $scoreColor ?>;"></div>
                        </div>
                    </div>
                </div>
                <?php if ($matchScore < 60): ?>
                <a href="<?= base_url('profile/edit') ?>" class="btn btn-sm btn-outline-primary w-100 mt-3" style="font-size:.78rem;">
                    <i class="bi bi-pencil-square me-1"></i>Améliorer mon profil
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

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
                <small class="text-muted d-block"><i class="bi bi-calendar me-1"></i><?= lang('App.job_posted') ?> <?= !empty($job->created_at) ? date('d', strtotime($job->created_at)) . ' ' . lang('App.months.' . date('n', strtotime($job->created_at))) . ' ' . date('Y', strtotime($job->created_at)) : '—' ?></small>
                <?php if (!empty($job->expires_at)): ?>
                    <small class="text-muted d-block"><i class="bi bi-alarm me-1"></i><?= lang('App.job_expires') ?> <?= date('d', strtotime($job->expires_at)) . ' ' . lang('App.months.' . date('n', strtotime($job->expires_at))) . ' ' . date('Y', strtotime($job->expires_at)) ?></small>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recruiter actions -->
        <?php if (session()->get('logged_in') && session()->get('user_role') !== 'job_seeker' && (int) session()->get('user_id') === (int) $job->user_id): ?>
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

<?php if (session()->get('logged_in') && session()->get('user_role') === 'job_seeker' && !($hasApplied ?? false)): ?>
<!-- ── Apply Modal ───────────────────────────────────────────────────────── -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--brand-light);border-bottom:1px solid var(--border);">
        <div>
          <h5 class="modal-title fw-bold mb-0" id="applyModalLabel">
            <i class="bi bi-briefcase-fill me-2" style="color:var(--brand-dark);"></i>Postuler — <?= esc($job->title) ?>
          </h5>
          <small class="text-muted"><?= esc($job->company_name) ?></small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('jobs/' . $job->id . '/apply') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="modal-body">

          <!-- CV -->
          <div class="mb-4">
            <label class="form-label fw-semibold d-flex align-items-center gap-2">
              <i class="bi bi-file-earmark-person-fill" style="color:var(--brand-dark);"></i>
              CV
              <?php if (!empty($job->require_cv)): ?>
                <span class="badge text-bg-danger" style="font-size:.65rem;">Obligatoire</span>
              <?php else: ?>
                <span class="badge text-bg-secondary" style="font-size:.65rem;">Optionnel</span>
              <?php endif; ?>
            </label>
            <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx">
            <div class="form-text"><i class="bi bi-info-circle me-1"></i>PDF, DOC, DOCX — max 5 Mo. Si vide, votre CV de profil sera utilisé.</div>
          </div>

          <!-- Cover letter -->
          <div class="mb-4">
            <label class="form-label fw-semibold d-flex align-items-center gap-2">
              <i class="bi bi-envelope-paper-fill" style="color:#6366f1;"></i>
              Lettre de motivation
              <?php if (!empty($job->require_cover_letter)): ?>
                <span class="badge text-bg-danger" style="font-size:.65rem;">Obligatoire</span>
              <?php else: ?>
                <span class="badge text-bg-secondary" style="font-size:.65rem;">Optionnelle</span>
              <?php endif; ?>
            </label>
            <textarea name="cover_letter" class="form-control" rows="5"
                      placeholder="<?= lang('App.cover_letter_ph') ?>"></textarea>
          </div>

          <?php if (!empty($questions)): ?>
          <!-- Prescreening questions -->
          <hr>
          <h6 class="fw-bold mb-3"><i class="bi bi-patch-question-fill me-2" style="color:var(--brand-dark);"></i>Questions de présélection</h6>
          <?php foreach ($questions as $qi => $q): ?>
            <div class="mb-3 p-3" style="background:var(--brand-light);border-radius:8px;border:1px solid var(--border);">
              <label class="form-label fw-semibold" style="font-size:.88rem;">
                <?= $qi + 1 ?>. <?= esc($q->question_text) ?>
                <?php if (!empty($q->is_eliminatory)): ?>
                  <span class="badge text-bg-danger ms-1" style="font-size:.6rem;">Éliminatoire</span>
                <?php endif; ?>
              </label>
              <?php if (($q->question_type ?? 'text') === 'yes_no'): ?>
                <div class="d-flex gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio"
                           name="answers[<?= $qi ?>][answer]" value="yes"
                           id="ans_<?= $qi ?>_yes"
                           required>
                    <label class="form-check-label" for="ans_<?= $qi ?>_yes">Oui</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio"
                           name="answers[<?= $qi ?>][answer]" value="no"
                           id="ans_<?= $qi ?>_no">
                    <label class="form-check-label" for="ans_<?= $qi ?>_no">Non</label>
                  </div>
                </div>
              <?php else: ?>
                <textarea name="answers[<?= $qi ?>][answer]" class="form-control form-control-sm" rows="2"
                          placeholder="Votre réponse…"
                          required></textarea>
              <?php endif; ?>
              <input type="hidden" name="answers[<?= $qi ?>][question_id]" value="<?= (int)$q->id ?>">
            </div>
          <?php endforeach; ?>
          <?php endif; ?>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-send me-2"></i>Envoyer ma candidature
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
