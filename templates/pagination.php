<?php if ($need_pagination): ?>
    <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev">
            <a <?= get_prev_href($pagination_context, $active, $pre_page_string); ?>>Назад</a>
        </li>
        <?php foreach ($pages as $page): ?>
            <li class="pagination-item <?= get_active_page_classname($page, $active); ?>">
                <a <?= get_page_href ($pagination_context, $page, $pre_page_string); ?>><?= $page ?></a>
            </li>
        <?php endforeach; ?>
        <li class="pagination-item pagination-item-next">
            <a <?= get_next_href($pagination_context, $active, count($pages), $pre_page_string); ?>>Вперед</a>
        </li>
    </ul>
<?php endif; ?>