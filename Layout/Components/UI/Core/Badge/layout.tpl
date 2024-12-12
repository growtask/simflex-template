<?php
/** @var array $data */

$badgeClasses = [
    'badge',
    "badge-{$data['style']}",
    "badge-size_{$data['size']}",
    $data['className'] ?? ''
];
?>

<div class="<?= implode(' ', $badgeClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
    <?php if ($data['icon'] && !$data['dot']): ?>
        <?= renderIcon($data['icon'], 'badge__icon') ?>
    <?php endif; ?>

    <?php if ($data['dot']): ?>
        <svg xmlns="http://www.w3.org/2000/svg" class="badge__icon" fill="none" stroke="#0E8C0E" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="8" d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/>
        </svg>
    <?php endif; ?>

    <?php if ($data['text']): ?>
        <span class="badge__text"><?= $data['text'] ?></span>
    <?php endif; ?>
</div>
