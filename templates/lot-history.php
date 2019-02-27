<div class="history">
    <h3>История ставок (<span><?= count($bids) ?></span>)</h3>
    <table class="history__list">
        <?php foreach ($bids as $bid): ?>
            <tr class="history__item">
                <td class="history__name"><?= get_assoc_element($bid, 'name'); ?></td>
                <td class="history__price"><?= get_rubles(get_assoc_element($bid, 'declared_price'), true); ?></td>
                <td class="history__time"><?= get_assoc_element($bid, 'time_ago'); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>