<?php
/** @var array $data */

$toggleClasses = array_filter([
    'toggle',
    "toggle-size_{$data['size']}",
    "toggle-position_{$data['position']}",
    "toggle-theme_{$data['theme']}",
    $data['className'] ?? ''
]);

$attributes = $data['attributes'] ?? [];
$attributeString = implode(' ', array_map(
    fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value)),
    array_keys($attributes),
    $attributes
));
?>

<div class="<?= implode(' ', $toggleClasses) ?>">
    <label class="toggle__wrapper">
        <input type="checkbox" class="toggle__input" role="switch" <?= $attributeString ?>>
        <div class="toggle__background">
            <div class="toggle__thumb">
                <?php if ($data['icon']): ?>
                    <svg class="toggle__check" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M5 13l4 4L19 7"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                        />
                    </svg>
                <?php endif; ?>
            </div>
        </div>
    </label>
    <?php if ($data['text']): ?>
        <span class="toggle__text"><?= $data['text'] ?></span>
    <?php endif; ?>
</div>