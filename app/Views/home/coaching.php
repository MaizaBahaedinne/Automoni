<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="fw-bold mb-1"><i class="bi bi-lightbulb-fill text-warning me-2"></i><?= lang('App.coaching_title') ?></h1>
        <p style="color:var(--muted)" class="mb-4"><?= lang('App.coaching_subtitle') ?></p>

        <div class="card mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold"><i class="bi bi-file-earmark-text text-primary me-2"></i><?= lang('App.coaching_cv_title') ?></h4>
                <p class="mb-0"><?= lang('App.coaching_cv_body') ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold"><i class="bi bi-chat-quote text-success me-2"></i><?= lang('App.coaching_interview_title') ?></h4>
                <p class="mb-0"><?= lang('App.coaching_interview_body') ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold"><i class="bi bi-linkedin text-primary me-2"></i><?= lang('App.coaching_network_title') ?></h4>
                <p class="mb-0"><?= lang('App.coaching_network_body') ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold"><i class="bi bi-graph-up-arrow text-warning me-2"></i><?= lang('App.coaching_salary_title') ?></h4>
                <p class="mb-0"><?= lang('App.coaching_salary_body') ?></p>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="<?= base_url('jobs') ?>" class="btn btn-primary btn-lg">
                <i class="bi bi-search me-1"></i><?= lang('App.browse_positions_btn') ?>
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

