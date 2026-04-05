<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i><?= lang('App.btn_back') ?>
            </a>
            <h3 class="fw-bold mb-0"><?= lang('App.btn_post_job') ?></h3>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?= base_url('jobs/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.field_job_title') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               value="<?= esc(old('title')) ?>" placeholder="e.g. Senior PHP Developer" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.filter_contract') ?> <span class="text-danger">*</span></label>
                            <select name="contract_type" class="form-select" required>
                                <?php foreach (['CDI','CDD','Freelance','Internship','PartTime'] as $ct): ?>
                                    <option value="<?= $ct ?>" <?= old('contract_type') === $ct ? 'selected' : '' ?>><?= $ct ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.filter_experience') ?></label>
                            <select name="experience_level" class="form-select">
                                <?php foreach (['any','junior','mid','senior','lead'] as $e): ?>
                                    <option value="<?= $e ?>" <?= old('experience_level', 'any') === $e ? 'selected' : '' ?>><?= ucfirst($e) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.filter_remote') ?></label>
                            <select name="remote" class="form-select">
                                <?php foreach (['onsite','hybrid','remote'] as $r): ?>
                                    <option value="<?= $r ?>" <?= old('remote', 'onsite') === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.filter_location') ?></label>
                        <input type="text" name="location" class="form-control"
                               value="<?= esc(old('location')) ?>" placeholder="Paris, France">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Salary Min (€/yr)</label>
                            <input type="number" name="salary_min" class="form-control"
                                   value="<?= esc(old('salary_min')) ?>" placeholder="35000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Salary Max (€/yr)</label>
                            <input type="number" name="salary_max" class="form-control"
                                   value="<?= esc(old('salary_max')) ?>" placeholder="55000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Expires At</label>
                            <input type="date" name="expires_at" class="form-control"
                                   value="<?= esc(old('expires_at')) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.job_description') ?> <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="6" required
                                  placeholder="Describe the role, team, responsibilities..."><?= esc(old('description')) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.job_requirements') ?></label>
                        <textarea name="requirements" class="form-control" rows="4"
                                  placeholder="Required experience, diplomas..."><?= esc(old('requirements')) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('App.job_benefits') ?></label>
                        <textarea name="benefits" class="form-control" rows="3"
                                  placeholder="Health insurance, stock options, flexible hours..."><?= esc(old('benefits')) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold"><?= lang('App.job_skills') ?></label>
                        <input type="text" name="skills" class="form-control"
                               value="<?= esc(old('skills')) ?>" placeholder="PHP, Laravel, MySQL (comma separated)">
                        <div class="form-text">Enter skills separated by commas.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="bi bi-send me-1"></i><?= lang('App.btn_publish') ?>
                        </button>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary"><?= lang('App.btn_cancel') ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
