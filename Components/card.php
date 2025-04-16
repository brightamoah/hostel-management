<?php
function renderCard($title, $content, $buttonText = null, $buttonLink = null, $dropdown = false)
{
?>
    <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2"><?= $title ?></h5>
            <?php if ($dropdown): ?>
                <div class="dropdown">
                    <button class="btn text-body-secondary p-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                        <a class="dropdown-item" href="javascript:void(0);">View All</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?= $content ?>
            <?php if ($buttonText && $buttonLink): ?>
                <a href="<?= $buttonLink ?>" class="btn btn-primary mt-3 d-grid w-100"><?= $buttonText ?></a>
            <?php endif; ?>
        </div>
    </div>
<?php
}
