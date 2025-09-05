<?php
/** @var array $data */

$radioClasses = [
    'radio',
    "radio-size-radio_{$data['radioSize']}",
    "radio-size_{$data['size']}",
    "radio-position_{$data['radioPosition']}",
    "radio-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<div class="<?= implode(' ', $radioClasses) ?>">
    <input type="radio" class="radio__input" id="<?= $data['id'] ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
    <label for="<?= $data['id'] ?>" class="radio__label">
        <span class="radio__indicator"></span>
        <?php if ($data['text']): ?>
            <span class="radio__text"><?= $data['text'] ?></span>
        <?php endif; ?>
    </label>
</div>