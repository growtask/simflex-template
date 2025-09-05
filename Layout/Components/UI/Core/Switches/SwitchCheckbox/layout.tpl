<?php
/** @var array $data */

$checkboxClasses = [
    'checkbox',
    "checkbox-{$data['style']}",
    "checkbox-size-icon_{$data['iconSize']}",
    "checkbox-size_{$data['size']}",
    "checkbox-position_{$data['iconPosition']}",
    "checkbox-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<label class="<?= implode(' ', $checkboxClasses) ?>">
    <input type="checkbox" class="checkbox__input" <?= buildAttrs($data['attributes'] ?? []) ?>>

    <div class="checkbox__icon">
        <?php if ($data['style'] == 'line'): ?>
            <svg class="checkbox__icon-line" width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="1" y="1" width="23.9999" height="24" rx="7" stroke="currentColor" stroke-width="2"/>
                <path d="M18.2802 8.30562C18.6636 7.90822 19.2968 7.89692 19.6943 8.28023C20.0917 8.66361 20.103 9.29679 19.7197 9.69429L12.0058 17.6943L11.9316 17.7636C11.752 17.9155 11.5234 17.9999 11.2861 18C11.0145 18 10.7538 17.8897 10.5654 17.6943L6.28021 13.25C5.89691 12.8525 5.90825 12.2193 6.3056 11.8359C6.70312 11.4526 7.33629 11.4638 7.71966 11.8613L11.2851 15.5586L18.2802 8.30562Z" fill="currentColor"/>
            </svg>
        <?php else: ?>
            <?= renderIcon('checkbox', 'checkbox__icon-default') ?>
            <?= renderIcon('checkbox-hover', 'checkbox__icon-hover') ?>
            <?= renderIcon('checkbox-active', 'checkbox__icon-active') ?>
        <?php endif; ?>
    </div>

    <?php if ($data['text']): ?>
        <span class="checkbox__text"><?= $data['text'] ?></span>
    <?php endif; ?>
</label>