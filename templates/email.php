<h1>Поздравляем с победой</h1>
<p>Здравствуйте, <?= get_pure_data($winner, 'username'); ?></p>
<p>Ваша ставка для лота
    <a href="lot.php?id=<?= get_pure_data($winner, 'lot_id'); ?>">
        <?= get_pure_data($winner, 'name'); ?>
    </a> победила
</p>
<p>
    Перейдите по ссылке <a href="my-lots.php">мои ставки</a> чтобы связаться с автором объявления
</p>
<small>Интернет-аукцион "YetiCave"</small>
