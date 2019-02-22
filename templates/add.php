<main>
    <nav class="nav">
        <?= $categories_content ?>
    </nav>
    <form class="form form--add-lot container <?= get_form_validation_classname($errors) ?>" action="add.php"
          method="post" enctype="multipart/form-data">
        <h2>Добавление лота</h2>
        <div class="form__container-two">
            <div class="form__item <?= get_field_validation_classname($errors, 'lot-name') ?>">
                <label for="lot-name">Наименование</label>
                <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота"
                       value="<?= get_pure_data($lot, 'lot-name'); ?>" required>
                <span class="form__error"><?= get_field_validation_message($errors, 'lot-name') ?></span>
            </div>
            <div class="form__item  <?= get_field_validation_classname($errors, 'category') ?>">
                <?= $categories_dropdown ?>
            </div>
        </div>
        <div class="form__item form__item--wide <?= get_field_validation_classname($errors, 'message') ?>">
            <label for="message">Описание</label>
            <textarea id="message" name="message" placeholder="Напишите описание лота"
                      required><?= get_pure_data($lot, 'message'); ?></textarea>
            <span class="form__error"><?= get_field_validation_message($errors, 'message') ?></span>
        </div>
        <div
            class="form__item form__item--file <?= get_field_validation_classname($errors, 'lot-image', 'form__item--uploaded') ?>">
            <label>Изображение</label>
            <div class="preview">
                <button class="preview__remove" type="button">x</button>
                <div class="preview__img">
                    <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
                </div>
            </div>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" id="photo2" name="lot-image"
                       value="<?= get_pure_data($lot, 'lot-image'); ?>">
                <label for="photo2">
                    <span>+ Добавить</span>
                </label>
            </div>
            <span class="form__error"><?= get_field_validation_message($errors, 'lot-image') ?></span>
        </div>
        <div class="form__container-three">
            <div class="form__item form__item--small <?= get_field_validation_classname($errors, 'lot-rate') ?>">
                <label for="lot-rate">Начальная цена</label>
                <input id="lot-rate" type="number" name="lot-rate" placeholder="0"
                       value="<?= get_pure_data($lot, 'lot-rate'); ?>" required>
                <span class="form__error"><?= get_field_validation_message($errors, 'lot-rate') ?></span>
            </div>
            <div class="form__item form__item--small <?= get_field_validation_classname($errors, 'lot-step') ?>">
                <label for="lot-step">Шаг ставки</label>
                <input id="lot-step" type="number" name="lot-step" placeholder="0"
                       value="<?= get_pure_data($lot, 'lot-step'); ?>" required>
                <span class="form__error"><?= get_field_validation_message($errors, 'lot-step') ?></span>
            </div>
            <div class="form__item <?= get_field_validation_classname($errors, 'lot-date') ?>">
                <label for="lot-date">Дата окончания торгов</label>
                <input class="form__input-date" id="lot-date" type="date" name="lot-date"
                       value="<?= get_pure_data($lot, 'lot-date'); ?>" required>
                <span class="form__error"><?= get_field_validation_message($errors, 'lot-date') ?></span>
            </div>
        </div>
        <span class="form__error form__error--bottom"><?= get_form_validation_message($errors) ?></span>
        <button type="submit" class="button">Добавить лот</button>
    </form>
</main>