<ul class="nav nav-pills flex-column mb-auto">
    <?php foreach($items as $item) : ?>
        <?php if ($item['type'] == 'menu') : ?>
            <li class="nav-item">
                <a class="nav-link rounded-3 shadow-sm  mb-2 <?php if (isset($item['is_active'])) : ?>active bg-primary border-primary<?php else: ?>bg-white text-dark border<?php endif ?>" aria-current="page" href="<?= $item['link'] ?>">
                    <i class="bi bi-<?= $item['icon'] ?> me-2"></i> 
                    <?= $item['title'] ?>
                </a>
            </li>
        <?php endif ?>
        <?php if ($item['type'] == 'separator') : ?>
            <hr>
        <?php endif ?>
    <?php endforeach ?> 
</ul>