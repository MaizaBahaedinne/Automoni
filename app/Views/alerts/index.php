<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-primary"></i><?= lang('App.alerts_title') ?></h3>
</div>

<!-- Create Alert -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i><?= lang('App.create_alert') ?></h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('alerts/store') ?>" method="post" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold"><?= lang('App.field_keywords') ?></label>
                <input type="text" name="keywords" class="form-control"
                       placeholder="PHP, developer..." value="<?= esc(old('keywords')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold"><?= lang('App.filter_location') ?></label>
                <input type="text" name="location" class="form-control"
                       placeholder="Paris, Remote..." value="<?= esc(old('location')) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold"><?= lang('App.filter_contract') ?></label>
                <select name="contract_type" class="form-select">
                    <option value="">Any</option>
                    <?php foreach (['CDI','CDD','Freelance','Internship','PartTime'] as $ct): ?>
                        <option value="<?= $ct ?>" <?= old('contract_type') === $ct ? 'selected' : '' ?>><?= $ct ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold"><?= lang('App.field_frequency') ?> <span class="text-danger">*</span></label>
                <select name="frequency" class="form-select" required>
                    <option value="instant"><?= lang('App.freq_instant') ?></option>
                    <option value="daily"><?= lang('App.freq_daily') ?></option>
                    <option value="weekly"><?= lang('App.freq_weekly') ?></option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Alert List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="fw-bold mb-0"><?= lang('App.my_alerts') ?></h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($alerts)): ?>
            <p class="text-muted text-center py-4">
                <i class="bi bi-bell-slash d-block display-5 mb-2"></i>
                <?= lang('App.no_alerts_yet') ?>
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Keywords</th>
                            <th>Location</th>
                            <th>Contract</th>
                            <th>Frequency</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alerts as $alert): ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($alert->keywords ?: '— Any —') ?></td>
                            <td class="text-muted"><?= esc($alert->location ?: '—') ?></td>
                            <td><?= $alert->contract_type ? '<span class="badge bg-primary">' . esc($alert->contract_type) . '</span>' : '<span class="text-muted">Any</span>' ?></td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= ucfirst(esc($alert->frequency)) ?></span>
                            </td>
                            <td>
                                <?php if ($alert->is_active): ?>
                                    <span class="badge bg-success"><i class="bi bi-bell me-1"></i><?= lang('App.alert_active') ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="bi bi-bell-slash me-1"></i><?= lang('App.alert_paused') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="<?= base_url('alerts/toggle/' . $alert->id) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-<?= $alert->is_active ? 'warning' : 'success' ?>" title="<?= $alert->is_active ? lang('App.btn_pause') : lang('App.btn_resume') ?>">
                                        <i class="bi bi-<?= $alert->is_active ? 'pause' : 'play' ?>"></i>
                                    </button>
                                </form>
                                <form action="<?= base_url('alerts/delete/' . $alert->id) ?>" method="post" class="d-inline"
                                      onsubmit="return confirm('Delete this alert?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
