<?php if (was_error($categories)): ?>
    <label for="category">Категория</label>
    <select id="category" name="category">
        <option><?= get_error_description($categories); ?></option>
    </select>
    <span class="form__error">Ошибка при получении данных</span>
<?php else: ?>
    <label for="category">Категория</label>
    <select id="category" name="category" required>
        <option value="0"><?= $empty_category; ?></option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= get_assoc_element($category, 'id'); ?>"
            <?= get_selected_state(get_assoc_element($category, 'id'), $current); ?>>
            <?= get_assoc_element($category, 'name'); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <span class=" form__error
    "><?= get_field_validation_message($errors, 'category') ?></span>
<?php endif; ?>

