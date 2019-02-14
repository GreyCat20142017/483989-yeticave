<main class="container">
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное
            снаряжение.</p>
        <?= $categories_content ?>
    </section>
    <section class="lots">
        <?php if (was_error($lots)): ?>
            <h4><?= get_error_description($lots); ?></h4>
        <?php else: ?>
            <div class="lots__header">
                <h2>Открытые лоты</h2>
            </div>
            <ul class="lots__list">
                <?php foreach ($lots as $lot): ?>
                    <li class="lots__item lot">
                        <div class="lot__image">
                            <img src="<?= strip_tags($lot['image']); ?>" width="350" height="260"
                                 alt="<?= strip_tags($lot['name']); ?>">
                        </div>
                        <div class="lot__info">
                            <span class="lot__category"><?= strip_tags($lot['category']); ?></span>
                            <h3 class="lot__title">
                                <a class="text-link" href="pages/lot.html"><?= strip_tags($lot['name']); ?></a>
                            </h3>
                            <div class="lot__state">
                                <div class="lot__rate">
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span class="lot__cost"><?= get_rubles(strip_tags($lot['price'])); ?></span>
                                </div>
                                <div class="lot__timer timer">
                                    <?= get_lot_lifetime(); ?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>
