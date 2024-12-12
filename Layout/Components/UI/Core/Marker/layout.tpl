<?php
/** @var array $data */

$markerClasses = [
    'marker',
    "marker-size_{$data['size']}",
    $data['className'] ?? ''
];
?>

<div class="<?= implode(' ', $markerClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
    <?= renderIcon($data['icon'], 'marker__icon') ?>
</div>

