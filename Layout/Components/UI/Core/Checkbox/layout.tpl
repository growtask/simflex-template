<?php
/** @var array $data */

$checkboxClasses = array_filter([
    'checkbox',
    "checkbox-{$data['style']}",
    "checkbox-size_{$data['size']}",
    "checkbox-position_{$data['position']}",
    "checkbox-theme_{$data['theme']}",
    $data['className'] ?? ''
]);

$attributes = $data['attributes'] ?? [];
$attributeString = implode(' ', array_map(
    fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value)),
    array_keys($attributes),
    $attributes
));
?>

<label class="<?= implode(' ', $checkboxClasses) ?>">
    <input type="checkbox" class="checkbox__input" <?= $attributeString ?>>
    <span class="checkbox__custom" aria-hidden="true"></span>
    <?php if ($data['text']): ?>
        <span class="checkbox__text"><?= $data['text'] ?></span>
    <?php endif; ?>
    <?php if ($data['policy'] && !$data['text']): ?>
        <span class="checkbox__policy">
            Я соглашаюсь с <a href="/policy/">политикой обработки персональных данных</a>
        </span>
    <?php endif; ?>
</label>