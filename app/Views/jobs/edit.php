<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i><?= lang('App.btn_back') ?>
            </a>
            <h3 class="fw-bold mb-0"><?= lang('App.btn_edit') ?> Job</h3>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?= base_url('jobs/update/' . $job->id) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.field_job_title') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               value="<?= esc(old('title', $job->title)) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.filter_contract') ?> <span class="text-danger">*</span></label>
                            <select name="contract_type" class="form-select" required>
                                <?php foreach (['CDI','CDD','Freelance','Internship','PartTime'] as $ct): ?>
                                    <option value="<?= $ct ?>" <?= old('contract_type', $job->contract_type) === $ct ? 'selected' : '' ?>><?= $ct ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.filter_experience') ?></label>
                            <select name="experience_level" class="form-select">
                                <?php foreach (['any','junior','mid','senior','lead'] as $e): ?>
                                    <option value="<?= $e ?>" <?= old('experience_level', $job->experience_level) === $e ? 'selected' : '' ?>><?= ucfirst($e) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.filter_remote') ?></label>
                            <select name="remote" class="form-select">
                                <?php foreach (['onsite','hybrid','remote'] as $r): ?>
                                    <option value="<?= $r ?>" <?= old('remote', $job->remote) === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <?php foreach (['active','paused','closed','draft'] as $s): ?>
                                <option value="<?= $s ?>" <?= old('status', $job->status) === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.filter_location') ?></label>
                        <input type="text" name="location" class="form-control"
                               value="<?= esc(old('location', $job->location)) ?>">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Salary Min</label>
                            <input type="number" name="salary_min" class="form-control"
                                   value="<?= esc(old('salary_min', $job->salary_min)) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Salary Max</label>
                            <input type="number" name="salary_max" class="form-control"
                                   value="<?= esc(old('salary_max', $job->salary_max)) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Expires At</label>
                            <input type="date" name="expires_at" class="form-control"
                                   value="<?= esc(old('expires_at', $job->expires_at)) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.job_description') ?> <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="6" required><?= esc(old('description', $job->description)) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.job_requirements') ?></label>
                        <textarea name="requirements" class="form-control" rows="4"><?= esc(old('requirements', $job->requirements)) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.job_benefits') ?></label>
                        <textarea name="benefits" class="form-control" rows="3"><?= esc(old('benefits', $job->benefits)) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold"><?= lang('App.job_skills') ?></label>
                        <input type="text" name="skills" class="form-control"
                               value="<?= esc(old('skills', $skillsList ?? '')) ?>"
                               placeholder="PHP, Laravel, MySQL (comma separated)">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="bi bi-save me-1"></i><?= lang('App.btn_save_changes') ?>
                        </button>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary"><?= lang('App.btn_cancel') ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
