<main>
    <nav class="nav">
        <?= $categories_content ?>
    </nav>
    <section class="lot-item container">
        <h2><?= get_assoc_element($lot, 'name'); ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img src="<?= $images . get_assoc_element($lot, 'image'); ?>" width="730" height="548"
                         alt="<?= get_assoc_element($lot, 'name'); ?>">
                </div>
                <p class="lot-item__category">Категория: <span><?= get_assoc_element($lot, 'category'); ?></span></p>
                <p class="lot-item__description"><?= get_assoc_element($lot, 'description'); ?></p>
            </div>
            <div class="lot-item__right">
                <?php if (!$bid_hidden_status): ?>
                    <div class="lot-item__state">
                        <div class="lot-item__timer timer">
                            <?= get_assoc_element($lot, 'time_left'); ?>
                        </div>
                        <div class="lot-item__cost-state">
                            <div class="lot-item__rate">
                                <span class="lot-item__amount">Текущая цена</span>
                                <span class="lot-item__cost"><?= get_rubles(get_assoc_element($lot, 'price')); ?></span>
                            </div>
                            <div class="lot-item__min-cost">
                                Мин. ставка <span><?= get_rubles(get_assoc_element($lot, 'min_bid'), true); ?> </span>
                            </div>
                        </div>
                        <?= $bid_content ?>
                    </div>
                <?php endif; ?>
                <?= $history_content ?>
            </div>
        </div>
    </section>
</main>

