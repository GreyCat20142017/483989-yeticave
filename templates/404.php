<main>
    <nav class="nav">
        <?= $categories_content ?>
    </nav>
    <section class="lot-item container">
        <h2>404 Страница не найдена</h2>
        <p><?= get_error_info($_GET); ?></p>
    </section>
</main>

