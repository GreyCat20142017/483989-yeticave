<form class="lot-item__form" action="lot.php?id=<?= strip_tags($lot_id); ?>" method="post">
    <p class="lot-item__form-item form__item <?= get_field_validation_classname($errors, 'cost') ?>">
        <label for="cost">Ваша ставка</label>
        <input id="cost" type="text" name="cost"
               placeholder="<?= get_rubles($min_bid, true, true) ?>"
               value="<?= get_pure_data($bid, 'cost'); ?>">
        <span class=" form__error"> <?= get_field_validation_message($errors, 'cost') ?></span>
    </p>
    <button type="submit" class="button">Сделать ставку</button>
</form>