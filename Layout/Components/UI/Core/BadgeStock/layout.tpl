<?php
/** @var array $data */

$badgeStockClasses = array_filter([
    'badge-stock',
    "badge-stock-{$data['style']}",
    "badge-stock-size_{$data['size']}",
    $data['className'] ?? ''
]);

$attributes = $data['attributes'] ?? [];
$attributeString = implode(' ', array_map(
    fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value)),
    array_keys($attributes),
    $attributes
));
?>

<div class="<?= implode(' ', $badgeStockClasses) ?>" <?= $attributeString ?>>
    <span class="badge__text"><?= $data['text'] ?></span>
</div>
