<?php
/** @var array $data */

$markerClasses = array_filter([
    'marker',
    "marker-size_{$data['size']}",
    $data['className'] ?? ''
]);

$attributes = $data['attributes'] ?? [];
$attributeString = implode(' ', array_map(
    fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value)),
    array_keys($attributes),
    $attributes
));
?>

<div class="<?= implode(' ', $markerClasses) ?>" <?= $attributeString ?>>
    <?= renderIcon($data['icon'], 'marker__icon') ?>
</div>

