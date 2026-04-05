<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3"
                 style="width:52px;height:52px;background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                <i class="bi bi-briefcase-fill text-white fs-4"></i>
            </div>
            <h3 class="fw-bold mb-1"><?= lang('App.login_title') ?></h3>
            <p style="color:var(--muted);font-size:.9rem"><?= lang('App.login_subtitle') ?></p>
        </div>

        <div class="card p-4">
            <?php if ($errors = session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach ((array) $errors as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label"><?= lang('App.field_email') ?></label>
                    <input type="email" name="email" class="form-control"
                           value="<?= esc(old('email')) ?>" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?= lang('App.field_password') ?></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:.875rem"><?= lang('App.remember_me') ?></label>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i><?= lang('App.login_btn') ?>
                </button>
            </form>
            <hr>
            <p class="text-center mb-0" style="font-size:.875rem">
                <?= lang('App.no_account') ?>
                <a href="<?= base_url('register') ?>" class="fw-semibold"><?= lang('App.register_btn') ?></a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

