<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i><?= lang('App.btn_back') ?>
            </a>
            <h3 class="fw-bold mb-0"><?= esc($title) ?></h3>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="<?= base_url($company ? 'company/update' : 'company/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold"><?= lang('App.field_company_name_f') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= esc(old('name', $company?->name)) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Logo</label>
                            <?php if (!empty($company?->logo)): ?>
                                <div class="mb-2">
                                    <img src="<?= base_url('writable/uploads/logos/' . esc($company->logo)) ?>"
                                         alt="logo" class="rounded" style="height:48px;object-fit:cover;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <div class="form-text"><?= lang('App.logo_hint') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('App.field_website') ?></label>
                            <input type="url" name="website" class="form-control"
                                   value="<?= esc(old('website', $company?->website)) ?>" placeholder="https://...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('App.field_linkedin_page') ?></label>
                            <input type="url" name="linkedin" class="form-control"
                                   value="<?= esc(old('linkedin', $company?->linkedin)) ?>" placeholder="https://linkedin.com/company/...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_industry') ?></label>
                            <input type="text" name="industry" class="form-control"
                                   value="<?= esc(old('industry', $company?->industry)) ?>" placeholder="Technology, Finance...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_size') ?></label>
                            <select name="size" class="form-select">
                                <option value="">Unknown</option>
                                <?php foreach (['1-10','11-50','51-200','201-500','501-1000','1000+'] as $s): ?>
                                    <option value="<?= $s ?>" <?= old('size', $company?->size) === $s ? 'selected' : '' ?>><?= $s ?> <?= lang('App.employees') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><?= lang('App.field_country') ?></label>
                            <input type="text" name="country" class="form-control"
                                   value="<?= esc(old('country', $company?->country)) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('App.field_city') ?></label>
                            <input type="text" name="city" class="form-control"
                                   value="<?= esc(old('city', $company?->city)) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold"><?= lang('App.field_description') ?></label>
                            <textarea name="description" class="form-control" rows="5"
                                      placeholder="Tell candidates about your company culture, mission..."><?= esc(old('description', $company?->description)) ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="bi bi-save me-1"></i><?= $company ? lang('App.btn_save_changes') : lang('App.btn_create_company') ?>
                        </button>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary"><?= lang('App.btn_cancel') ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
