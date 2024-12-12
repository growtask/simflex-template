<?php
/** @var array $data */

$badgeStockClasses = [
    'badge-stock',
    "badge-stock-{$data['style']}",
    "badge-stock-size_{$data['size']}",
    $data['className'] ?? ''
];
?>

<div class="<?= implode(' ', $badgeStockClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
    <span class="badge__text"><?= $data['text'] ?></span>
</div>
