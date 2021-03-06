<section class="lots">
    <?php if (was_error($lots)): ?>
        <h4><?= get_error_description($lots); ?></h4>
    <?php elseif (count($lots) === 0): ?>
        <h2><?= strip_tags($title) ?></h2>
        <h4><?= "Ничего не найдено по Вашему запросу" ?></h4>
    <?php else: ?>
        <h2><?= strip_tags($title) ?></h2>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="<?= $images . get_pure_data($lot, 'image'); ?>" width="350" height="260"
                             alt="<?= get_pure_data($lot, 'name'); ?>">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= get_pure_data($lot, 'category'); ?></span>
                        <h3 class="lot__title">
                            <a class="text-link" href="lot.php?id=<?= get_pure_data($lot, 'id'); ?>">
                                <?= get_pure_data($lot, 'name'); ?>
                            </a>
                        </h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                    <span
                                        class="lot__amount"><?= get_bids_info(get_pure_data($lot, 'bids_count')); ?></span>
                                <span class="lot__cost"><?= get_rubles(get_pure_data($lot, 'current_price')); ?></span>
                            </div>
                            <div
                                class="lot__timer timer <?= get_time_left_classname(get_pure_data($lot, 'time_left')); ?>">
                                <?= get_formatted_time_from_seconds(get_pure_data($lot, 'time_left')); ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>