<?php
/** @var array $data */

$separatorClasses = array_filter([
    'separator',
    "separator-orientation_{$data['orientation']}",
    "separator-theme_{$data['theme']}",
    $data['className'] ?? ''
]);

$attributes = $data['attributes'] ?? [];
$attributeString = implode(' ', array_map(
    fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value)),
    array_keys($attributes),
    $attributes
));
?>

<div class="<?= implode(' ', $separatorClasses) ?>" <?= $attributeString ?>></div>