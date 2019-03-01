<?php if ($need_pagination): ?>
    <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev">
            <a <?= get_prev_href($pagination_context, $id, $active); ?>>Назад</a>
        </li>
        <?php foreach ($pages as $page): ?>
            <li class="pagination-item <?= get_active_page_classname($page, $active); ?>">
                <a href="<?= $pagination_context ?>.php?id=<?= $id ?>&page=<?= $page ?>"><?= $page ?></a>
            </li>
        <?php endforeach; ?>
        <li class="pagination-item pagination-item-next">
            <a <?= get_next_href ($pagination_context, $id, $active, count($pages)); ?>>Вперед</a>
        </li>
    </ul>
<?php endif; ?>