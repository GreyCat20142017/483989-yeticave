<?php if (was_error($categories)): ?>
    <h4 <?= get_classname($ul_classname) ?>><?= get_error_description($categories); ?></h4>
<?php else: ?>
    <ul <?= get_classname($ul_classname) ?>>
        <?php foreach ($categories as $category): ?>
            <li <?= get_classname($li_classname) ?>>
                <a <?= get_classname($a_classname) ?>
                    href="pages/all-lots.html"><?= get_assoc_element($category, 'name'); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
