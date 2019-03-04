<main>
    <nav class="nav">
        <?= $categories_content ?>
    </nav>
    <section class="rates container">
        <h2><?= strip_tags($title) ?></h2>
        <table class="rates__list">
            <?php foreach ($bids as $bid): ?>
                <tr class="rates__item <?= get_rates__item_classname(get_pure_data($bid, 'result')) ?>">
                    <td class="rates__info">
                        <div class="rates__img">
                            <img src="<?= $images . get_pure_data($bid, 'image'); ?>" width="54" height="40"
                                 alt="Фото">
                        </div>
                        <div>
                            <h3 class="rates__title">
                                <a href="lot.php?id=<?= get_pure_data($bid, 'lot_id'); ?>">
                                    <?= get_pure_data($bid, 'name'); ?>
                                </a>
                            </h3>
                            <p><?= get_pure_data($bid, 'contacts'); ?></p>
                        </div>
                    </td>
                    <td class="rates__category">
                        <?= get_pure_data($bid, 'category'); ?>
                    </td>
                    <td class="rates__timer">
                        <div
                            class="timer <?= get_timer_classname(get_pure_data($bid, 'result'), get_pure_data($bid, 'expired')) ?>"
                            title="<?= get_pure_data($bid, 'result'); ?>">
                            <?= get_timer_info(get_pure_data($bid, 'result'), gmdate('H:i:s', get_pure_data($bid, 'time_left'))); ?>
                        </div>
                    </td>
                    <td class="rates__price">
                        <?= get_rubles(get_pure_data($bid, 'declared_price'), true); ?>
                    </td>
                    <td class="rates__time">
                        <?= get_pure_data($bid, 'placement_date'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
</main>