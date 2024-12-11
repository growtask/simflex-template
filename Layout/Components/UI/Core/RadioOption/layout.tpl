<?php
/** @var array $data */

$radioClasses = array_filter([
    'radio-option',
    "radio-option-size_{$data['size']}",
    "radio-option-position_{$data['position']}",
    "radio-option-theme_{$data['theme']}",
    $data['className'] ?? ''
]);

$attributes = $data['attributes'] ?? [];
$attributeString = implode(' ', array_map(
    fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value)),
    array_keys($attributes),
    $attributes
));
?>

<div class="<?= implode(' ', $radioClasses) ?>">
    <input type="radio" class="radio-option__input" id="<?= $data['id'] ?>" <?= $attributeString ?>>
    <label for="<?= $data['id'] ?>" class="radio-option__label">
        <span class="radio-option__indicator"></span>
        <?php if ($data['text']): ?>
            <span class="radio-option__text"><?= $data['text'] ?></span>
        <?php endif; ?>
    </label>
</div>