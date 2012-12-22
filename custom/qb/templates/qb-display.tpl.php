<?php
/**
 * $display = Объект содержащий весь дисплей
 */
?><div class="qb-display">
    <table class="table">
        <? if ($display->header): ?>
            <thead>
                <? foreach ($display->header as $th): ?>
                <th><?= $th; ?></th>
            <? endforeach; ?>
            </thead>
        <? endif; ?>
        <tbody>
            <? foreach ($display->row as $row): ?>
            <tr class="qb-display-row">
                <? foreach ($row as $cell): ?>
                    <td class="qb-display-cell"><?= $cell; ?></td>
                <? endforeach; ?>
            </tr>
        <? endforeach; ?>
        </tbody>
    </table>
</div>