<?php
/** @var array $data */

$buttonClasses = array_filter([
    'btn',
    "btn-{$data['style']}",
    "btn-size_{$data['size']}",
    "btn-theme_{$data['theme']}",
    $data['cn'] ?? ''
]);

$attributes = $data['attributes'] ?? [];

if (!isset($attributes['type'])) {
    $attributes['type'] = 'button';
}

$attributeString = implode(' ', array_map(
    fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value)),
    array_keys($attributes),
    $attributes
));
?>

<button class="<?= implode(' ', $buttonClasses) ?>" <?= $attributeString ?>>
    <?php if ($data['icon'] && $data['iconPos'] === 'left' && $data['text']): ?>
        <span class="btn__icon btn__icon_left">
            <?= renderIcon($data['icon'] ) ?>
        </span>
    <?php endif; ?>

    <?php if ($data['text']): ?>
        <span class="btn__text"><?= $data['text'] ?></span>
    <?php endif; ?>

    <?php if ($data['icon'] && $data['iconPos'] && !$data['text']): ?>
        <span class="btn__icon btn__icon_center">
            <?= renderIcon($data['icon'] ) ?>
        </span>
    <?php endif; ?>

    <?php if ($data['icon'] && $data['iconPos'] === 'right' && $data['text']): ?>
        <span class="btn__icon btn__icon_right">
            <?= renderIcon($data['icon']) ?>
        </span>
    <?php endif; ?>

    <span class="btn__loader">
        <?php App\Layout\Components\UI\Other\LoaderIcon\Layout::draw(); ?>
    </span>
</button>