<main>
    <nav class="nav">
        <?= $categories_content ?>
    </nav>
    <form class="form container <?= get_form_validation_classname($errors) ?>" action="login.php" method="post">
        <span class="form__error form__error--bottom"><?= $status ?></span>
        <h2>Вход</h2>
        <div class="form__item <?= get_field_validation_classname($errors, 'email') ?>">
            <label for="email">E-mail*</label>
            <input id="email" type="text" name="email" placeholder="Введите e-mail"
                   value="<?= get_pure_data($user, 'email'); ?>" required>
            <span class="form__error"><?= get_field_validation_message($errors, 'email') ?></span>
        </div>
        <div class="form__item form__item--last <?= get_field_validation_classname($errors, 'password') ?>">
            <label for="password">Пароль*</label>
            <input id="password" type="password" name="password" placeholder="Введите пароль"
                   value="<?= get_pure_data($user, 'password'); ?>" required>
            <span class="form__error"> <?= get_field_validation_message($errors, 'password') ?></span>
        </div>
        <button type="submit" class="button">Войти</button>
    </form>
</main>