<?php if ($search_enable): ?>
    <form class="main-header__search" method="get" action="search-result.php">
        <input type="search" name="search" placeholder="Поиск лота" value="<?= strip_tags($search_string); ?>">
        <input class="main-header__search-btn" type="submit" name="find" value="Найти">
    </form>
<?php else: ?>
    <form class="main-header__search" method="get" action="none">
        <input type="search" name="search" placeholder="Поиск лота" value="<?= strip_tags($search_string); ?>" disabled>
        <input class="main-header__search-btn" type="submit" name="find" value="Найти" disabled>
    </form>
<?php endif; ?>
