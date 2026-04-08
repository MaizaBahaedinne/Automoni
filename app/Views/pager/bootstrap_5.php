<?php

/**
 * Bootstrap 5 pager template for CodeIgniter 4
 */

$pager->setSurroundCount(2);

?>
<?php if ($pager->hasNextPage() || $pager->hasPreviousPage()) : ?>
<nav aria-label="Page navigation">
    <ul class="pagination pagination-sm mb-0">

        <?php if ($pager->hasPreviousPage()) : ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager->getFirstPageURL() ?>" aria-label="First">
                <span aria-hidden="true">&laquo;&laquo;</span>
            </a>
        </li>
        <li class="page-item">
            <a class="page-link" href="<?= $pager->getPreviousPageURL() ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <?php else : ?>
        <li class="page-item disabled">
            <span class="page-link">&laquo;&laquo;</span>
        </li>
        <li class="page-item disabled">
            <span class="page-link">&laquo;</span>
        </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
        <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
            <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
        </li>
        <?php endforeach ?>

        <?php if ($pager->hasNextPage()) : ?>
        <li class="page-item">
            <a class="page-link" href="<?= $pager->getNextPageURL() ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
        <li class="page-item">
            <a class="page-link" href="<?= $pager->getLastPageURL() ?>" aria-label="Last">
                <span aria-hidden="true">&raquo;&raquo;</span>
            </a>
        </li>
        <?php else : ?>
        <li class="page-item disabled">
            <span class="page-link">&raquo;</span>
        </li>
        <li class="page-item disabled">
            <span class="page-link">&raquo;&raquo;</span>
        </li>
        <?php endif ?>

    </ul>
</nav>
<?php endif ?>
