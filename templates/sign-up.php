<main>
    <nav class="nav">
        <?= $categories_content ?>
    </nav>
    <form class="form container <?= get_form_validation_classname($errors, $status) ?>" action="sign-up.php"
          method="post" enctype="multipart/form-data">
        <h2>Регистрация нового аккаунта</h2>
        <div class="form__item  <?= get_field_validation_classname($errors, 'email') ?>">
            <label for="email">E-mail*</label>
            <input id="email" type="text" name="email" placeholder="Введите e-mail"
                   value="<?= get_pure_data($user, 'email'); ?>" required>
            <span class="form__error"><?= get_field_validation_message($errors, 'email') ?></span>
        </div>
        <div class="form__item  <?= get_field_validation_classname($errors, 'password') ?>">
            <label for="password">Пароль*</label>
            <input id="password" type="password" name="password" placeholder="Введите пароль"
                   value="<?= get_pure_data($user, 'password'); ?>" required>
            <span class="form__error"><?= get_field_validation_message($errors, 'password') ?></span>
        </div>
        <div class="form__item  <?= get_field_validation_classname($errors, 'name') ?>">
            <label for="name">Имя*</label>
            <input id="name" type="text" name="name" placeholder="Введите имя"
                   value="<?= get_pure_data($user, 'name'); ?>" required>
            <span class="form__error"><?= get_field_validation_message($errors, 'name') ?></span>
        </div>
        <div class="form__item  <?= get_field_validation_classname($errors, 'message') ?>">
            <label for="message">Контактные данные*</label>
            <textarea id="message" name="message" placeholder="Напишите как с вами связаться"
                      required><?= get_pure_data($user, 'message'); ?></textarea>
            <span class="form__error"><?= get_field_validation_message($errors, 'message') ?></span>
        </div>
        <div
            class="form__item form__item--file form__item--last <?= get_field_validation_classname($errors, 'avatar') ?>">
            <label>Аватар</label>
            <div class="preview">
                <button class="preview__remove" type="button">x</button>
                <div class="preview__img">
                    <img src="img/avatar.jpg" width="113" height="113" alt="Ваш аватар">
                </div>
            </div>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" id="photo2" value="" name="avatar">
                <label for="photo2">
                    <span>+ Добавить</span>
                </label>
            </div>
            <span class="form__error"><?= get_field_validation_message($errors, 'avatar') ?></span>
        </div>
        <span class="form__error form__error--bottom"><?= get_form_validation_message($errors) . $status ?></span>
        <button type="submit" class="button">Зарегистрироваться</button>
        <a class="text-link" href="login.php">Уже есть аккаунт</a>
    </form>
</main>