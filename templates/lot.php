<main>
    <nav class="nav">
        <?= $categories_content ?>
    </nav>
    <section class="lot-item container">
        <h2><?= get_pure_data($lot, 'name'); ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img src="<?= $images . get_pure_data($lot, 'image'); ?>" width="730" height="548"
                         alt="<?= get_pure_data($lot, 'name'); ?>">
                </div>
                <p class="lot-item__category">Категория: <span><?= get_pure_data($lot, 'category'); ?></span></p>
                <p class="lot-item__description"><?= get_pure_data($lot, 'description'); ?></p>
            </div>
            <div class="lot-item__right">

                <div class="lot-item__state">
                    <div class="lot-item__timer timer" style="min-width: 100px;">
                        <?= get_formatted_time_from_seconds(get_pure_data($lot, 'time_left')) ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost"><?= get_rubles(get_pure_data($lot, 'price')); ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= get_rubles(get_pure_data($lot, 'min_bid'), true); ?> </span>
                        </div>
                    </div>
                    <?php if (!$bid_hidden_status): ?>
                        <?= $bid_content ?>
                    <?php endif; ?>
                </div>
                <?= $history_content ?>
            </div>
        </div>
    </section>
</main>

