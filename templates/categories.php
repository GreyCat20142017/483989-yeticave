<?php /** @noinspection ALL */
    if (was_error($categories)): ?>
    <h4 <?= get_classname(get_assoc_element($style, 'ul_classname')) ?>><?= get_error_description($categories); ?></h4>
<?php else: ?>
    <ul <?= get_classname(get_assoc_element($style, 'ul_classname')) ?>>
        <?php foreach ($categories as $category): ?>
            <li <?= get_classname(get_assoc_element($style, 'li_classname')) ?>>
                <a <?= get_classname(get_assoc_element($style, 'a_classname')) ?>
                    href="all-lots.php?id=<?= get_pure_data($category, 'id') . "&page=1"; ?>">
                    <?= get_assoc_element($category, 'name'); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
