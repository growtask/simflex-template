<?php
/** @var array $data */

$radioClasses = [
    'radio-option',
    "radio-option-size_{$data['size']}",
    "radio-option-position_{$data['position']}",
    "radio-option-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<div class="<?= implode(' ', $radioClasses) ?>">
    <input type="radio" class="radio-option__input" id="<?= $data['id'] ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
    <label for="<?= $data['id'] ?>" class="radio-option__label">
        <span class="radio-option__indicator"></span>
        <?php if ($data['text']): ?>
            <span class="radio-option__text"><?= $data['text'] ?></span>
        <?php endif; ?>
    </label>
</div>