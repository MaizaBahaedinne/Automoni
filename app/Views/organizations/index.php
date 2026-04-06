<?php
// app/Views/organizations/index.php
?>

<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $title ?></h1>
        <?php if (session()->get('user_id')): ?>
            <a href="/organizations/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Organization
            </a>
        <?php endif; ?>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control" 
                           placeholder="Search..." value="<?= esc($filters['keyword'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="type_id" class="form-select">
                        <option value="">All Types</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= $type->id ?>" 
                                    <?= ($filters['type_id'] ?? null) == $type->id ? 'selected' : '' ?>>
                                <?= esc($type->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="industry" class="form-control" 
                           placeholder="Industry" value="<?= esc($filters['industry'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Grid d'organisations -->
    <div class="row g-4">
        <?php if (!empty($organizations)): ?>
            <?php foreach ($organizations as $org): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm hover-shadow transition border-0">
                        <?php if ($org->logo): ?>
                            <img src="<?= base_url('uploads/organizations/' . $org->logo) ?>" 
                                 class="card-img-top" alt="<?= esc($org->name) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="fas fa-building text-muted fa-3x"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0"><?= esc($org->name) ?></h5>
                                <?php if ($org->is_verified): ?>
                                    <span class="badge bg-success">Verified</span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="text-muted small mb-3"><?= esc($org->type_name ?? 'Organization') ?></p>
                            
                            <?php if ($org->industry): ?>
                                <p class="small mb-2">
                                    <i class="fas fa-industry"></i> <?= esc($org->industry) ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($org->employee_count): ?>
                                <p class="small mb-2">
                                    <i class="fas fa-users"></i> <?= number_format($org->employee_count) ?> employees
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($org->website): ?>
                                <a href="<?= esc($org->website) ?>" target="_blank" class="small text-decoration-none">
                                    <i class="fas fa-globe"></i> Website
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer bg-transparent border-top">
                            <a href="/organizations/<?= $org->id ?>" class="btn btn-sm btn-outline-primary w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No organizations found. 
                    <?php if (session()->get('user_id')): ?>
                        <a href="/organizations/create">Create one!</a>
                    <?php else: ?>
                        <a href="/login">Login to create one</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pager): ?>
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <?= $pager->links() ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<style>
    .hover-shadow {
        transition: box-shadow 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>
