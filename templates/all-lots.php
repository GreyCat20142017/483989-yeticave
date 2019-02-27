<main>
    <nav class="nav">
        <?= $categories_content ?>
    </nav>
    <div class="container">
        <section class="lots">
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
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span class="lot__cost"><?= get_rubles(get_pure_data($lot, 'price')); ?></span>
                                </div>
                                <div class="lot__timer timer">
                                    <?= get_lot_lifetime(); ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?= $pagination_content ?>
    </div>
</main>