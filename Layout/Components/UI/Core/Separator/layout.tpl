<?php
/** @var array $data */

$separatorClasses = [
    'separator',
    "separator-orientation_{$data['orientation']}",
    "separator-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<div class="<?= implode(' ', $separatorClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>></div>