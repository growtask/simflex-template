<?php
/** @var array $data */

$checkboxClasses = [
    'selector-tab',
    "selector-tab-theme_{$data['theme']}",
    $data['isActive'] ? 'active' : '',
    $data['className'] ?? ''
];
?>

<button class="<?= implode(' ', $checkboxClasses) ?>" type="button">
    <?php if ($data['iconLeft']): ?>
        <div class="selector-tab__icon"><?= renderIcon('true') ?></div>
    <?php endif; ?>

    <?php if ($data['text']): ?>
        <div class="selector-tab__text"><?= $data['text'] ?></div>
    <?php endif; ?>
</button>