<?php
// app/Views/organizations/show.php
?>

<div class="container-fluid py-5">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-md-3">
            <?php if ($logo_url): ?>
                <img src="<?= $logo_url ?>" alt="<?= esc($organization->name) ?>" 
                     class="img-fluid rounded shadow" style="max-width: 100%; height: auto;">
            <?php else: ?>
                <div class="bg-light rounded p-5 text-center">
                    <i class="fas fa-building fa-5x text-muted"></i>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h1><?= esc($organization->name) ?></h1>
                    <p class="text-muted">
                        <span class="badge bg-secondary"><?= esc($organization->type_name ?? 'Organization') ?></span>
                        <?php if ($organization->is_verified): ?>
                            <span class="badge bg-success ms-2">Verified</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <?php if ($can_edit): ?>
                    <a href="/organizations/<?= $organization->id ?>/edit" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                <?php endif; ?>
            </div>

            <!-- Breadcrumbs hiérarchie -->
            <?php if (count($breadcrumbs) > 1): ?>
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <?php foreach ($breadcrumbs as $index => $crumb): ?>
                            <li class="breadcrumb-item <?= ($index === count($breadcrumbs) - 1) ? 'active' : '' ?>">
                                <?php if ($index < count($breadcrumbs) - 1): ?>
                                    <a href="/organizations/<?= $crumb->id ?>">
                                        <?= esc($crumb->name) ?>
                                    </a>
                                <?php else: ?>
                                    <?= esc($crumb->name) ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            <?php endif; ?>

            <!-- Info básica -->
            <div class="row g-3 text-sm">
                <?php if ($organization->founded_at): ?>
                    <div class="col-md-6">
                        <strong>Founded:</strong> <?= date('F d, Y', strtotime($organization->founded_at)) ?>
                    </div>
                <?php endif; ?>
                <?php if ($organization->employee_count): ?>
                    <div class="col-md-6">
                        <strong>Employees:</strong> <?= number_format($organization->employee_count) ?>
                    </div>
                <?php endif; ?>
                <?php if ($organization->industry): ?>
                    <div class="col-md-6">
                        <strong>Industry:</strong> <?= esc($organization->industry) ?>
                    </div>
                <?php endif; ?>
                <?php if ($organization->website): ?>
                    <div class="col-md-6">
                        <strong>Website:</strong> 
                        <a href="<?= esc($organization->website) ?>" target="_blank">
                            <?= esc(parse_url($organization->website, PHP_URL_HOST)) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="row g-4">
        <!-- Description -->
        <div class="col-lg-8">
            <?php if ($organization->description): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">About</h5>
                        <p><?= nl2br(esc($organization->description)) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contact & Localisation -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Contact & Location</h5>
                    
                    <div class="row g-3">
                        <?php if ($organization->address): ?>
                            <div class="col-md-6">
                                <strong>Address:</strong>
                                <p><?= esc($organization->address) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($organization->email): ?>
                            <div class="col-md-6">
                                <strong>Email:</strong>
                                <p><a href="mailto:<?= esc($organization->email) ?>">
                                    <?= esc($organization->email) ?>
                                </a></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($organization->phone): ?>
                            <div class="col-md-6">
                                <strong>Phone:</strong>
                                <p><a href="tel:<?= esc($organization->phone) ?>">
                                    <?= esc($organization->phone) ?>
                                </a></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Map (optional) -->
                    <?php if ($organization->latitude && $organization->longitude): ?>
                        <div class="mt-3">
                            <iframe width="100%" height="300" style="border:0; border-radius: 0.25rem;" 
                                    src="https://www.openstreetmap.org/export/embed.html?bbox=<?= $organization->longitude - 0.05 ?>,<?= $organization->latitude - 0.05 ?>,<?= $organization->longitude + 0.05 ?>,<?= $organization->latitude + 0.05 ?>&layer=mapnik" 
                                    style="border:1px solid #ccc;"></iframe>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Réseaux sociaux -->
            <?php if (!empty($social_links)): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Social Links</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php foreach ($social_links as $link): ?>
                                <a href="<?= esc($link->url) ?>" target="_blank" 
                                   class="btn btn-sm btn-outline-secondary" title="<?= esc($link->platform) ?>">
                                    <i class="fab fa-<?= esc($link->platform) ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Certifications -->
            <?php if (!empty($certifications)): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Certifications</h5>
                        <div class="list-group">
                            <?php foreach ($certifications as $cert): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= esc($cert->name) ?></h6>
                                        <?php if ($cert->issued_at): ?>
                                            <small><?= date('M Y', strtotime($cert->issued_at)) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($cert->issuer): ?>
                                        <p class="mb-1 text-muted small">Issuer: <?= esc($cert->issuer) ?></p>
                                    <?php endif; ?>
                                    <?php if ($cert->url): ?>
                                        <a href="<?= esc($cert->url) ?>" target="_blank" class="small">
                                            View credential →
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Stats -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Statistics</h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Members</span>
                            <strong><?= $stats['members_count'] ?? 0 ?></strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Direct Subsidiaries</span>
                            <strong><?= $stats['subsidiaries_count'] ?? 0 ?></strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>All Descendants</span>
                            <strong><?= $stats['descendants_count'] ?? 0 ?></strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Partners</span>
                            <strong><?= $stats['partners_count'] ?? 0 ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filiales -->
            <?php if (!empty($subsidiaries)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Subsidiaries</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($subsidiaries as $sub): ?>
                            <a href="/organizations/<?= $sub->id ?>" class="list-group-item list-group-item-action">
                                <h6 class="mb-0"><?= esc($sub->name) ?></h6>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Partenaires -->
            <?php if (!empty($partners)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Partners</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($partners as $partner): ?>
                            <a href="/organizations/<?= $partner->partner_id ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <?php if ($partner->logo): ?>
                                        <img src="<?= base_url('uploads/organizations/' . $partner->logo) ?>" 
                                             alt="logo" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;" 
                                             class="me-2">
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-0"><?= esc($partner->partner_name) ?></h6>
                                        <?php if ($partner->partnership_type): ?>
                                            <small class="text-muted"><?= esc($partner->partnership_type) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Membres -->
            <?php if (!empty($members)): ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Team Members</h5>
                        <?php if ($can_manage): ?>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                +
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($members as $member): ?>
                            <div class="list-group-item">
                                <div class="d-flex">
                                    <?php if ($member->avatar): ?>
                                        <img src="<?= base_url($member->avatar) ?>" alt="avatar" 
                                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" class="me-2">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded-circle me-2" 
                                             style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: white;">
                                            <?= strtoupper(substr($member->first_name, 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0"><?= esc($member->first_name . ' ' . $member->last_name) ?></h6>
                                        <small class="text-muted d-block"><?= esc($member->email) ?></small>
                                    </div>
                                    <span class="badge bg-info"><?= esc($member->role) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .text-sm { font-size: 0.875rem; }
</style>
