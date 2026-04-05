<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3"
                 style="width:52px;height:52px;background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                <i class="bi bi-person-plus-fill text-white fs-4"></i>
            </div>
            <h3 class="fw-bold mb-1"><?= lang('App.register_title') ?></h3>
            <p style="color:var(--muted);font-size:.9rem"><?= lang('App.register_subtitle') ?></p>
        </div>

        <div class="card p-4">
            <?php if ($errors = session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach ((array) $errors as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('register') ?>" method="post">
                <?= csrf_field() ?>
                <div class="row g-3 mb-3">
                    <div class="col">
                        <label class="form-label"><?= lang('App.field_first_name') ?></label>
                        <input type="text" name="first_name" class="form-control"
                               value="<?= esc(old('first_name')) ?>" required>
                    </div>
                    <div class="col">
                        <label class="form-label"><?= lang('App.field_last_name') ?></label>
                        <input type="text" name="last_name" class="form-control"
                               value="<?= esc(old('last_name')) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?= lang('App.field_email') ?></label>
                    <input type="email" name="email" class="form-control"
                           value="<?= esc(old('email')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        <?= lang('App.field_password') ?>
                        <small style="color:var(--muted)"><?= lang('App.field_password_min') ?></small>
                    </label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?= lang('App.field_confirm_password') ?></label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= lang('App.field_role') ?></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="role_seeker"
                                   value="job_seeker" <?= old('role', 'job_seeker') === 'job_seeker' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="role_seeker">
                                <i class="bi bi-person-badge me-1 text-primary"></i><?= lang('App.role_seeker') ?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="role_recruiter"
                                   value="recruiter" <?= old('role') === 'recruiter' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="role_recruiter">
                                <i class="bi bi-building me-1 text-success"></i><?= lang('App.role_recruiter') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-person-plus me-2"></i><?= lang('App.register_btn') ?>
                </button>
            </form>
            <hr>
            <p class="text-center mb-0" style="font-size:.875rem">
                <?= lang('App.have_account') ?>
                <a href="<?= base_url('login') ?>" class="fw-semibold"><?= lang('App.login_btn') ?></a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
