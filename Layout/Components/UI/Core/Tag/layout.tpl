<?php
/** @var array $data */

$tagClasses = array_filter([
    'tag',
    "tag-{$data['style']}",
    "tag-size_{$data['size']}",
    "tag-theme_{$data['theme']}",
    $data['className'] ?? ''
]);

$attributes = $data['attributes'] ?? [];
$attributeString = implode(' ', array_map(
    fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value)),
    array_keys($attributes),
    $attributes
));
?>

<button type="button" class="<?= implode(' ', $tagClasses) ?>" <?= $attributeString ?>>
    <span class="tag__text"><?= $data['text'] ?></span>
    <?= renderIcon('close', 'tag__icon') ?>
</button>
