<?php
/** @var array $data */

$inputClasses = [
    'text-input',
    "text-input-pos_{$data['labelPos']}",
    "text-input-style_{$data['style']}",
    "text-input-size_{$data['size']}",
    "text-input-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<div class="<?= implode(' ', $inputClasses) ?>">
    <div class="text-input__wrapper">
        <?php if ($data['labelPos'] === 'standard'): ?>
            <label class="text-input__label" for="<?= $data['id'] ?>"><?= $data['labelText'] ?></label>
        <?php endif; ?>

        <div class="text-input__input-container">
            <input type="text" id="<?= $data['id'] ?>" class="text-input__field" <?= buildAttrs($data['attributes'] ?? []) ?>>

            <?php if ($data['icon']): ?>
                <?= renderIcon($data['icon'], 'text-input__icon') ?>
            <?php endif; ?>

            <?php if ($data['reset']): ?>
                <button class="text-input__reset reset-button" type="button" aria-hidden="true">
                    <?= renderIcon('close', 'text-input__reset-icon') ?>
                </button>
            <?php endif; ?>
        </div>
        <span class="text-input__error-message hidden"></span>
    </div>
</div>
