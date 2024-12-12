<?php
/** @var array $data */

$tagClasses = [
    'tag',
    "tag-{$data['style']}",
    "tag-size_{$data['size']}",
    "tag-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<button type="button" class="<?= implode(' ', $tagClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
    <span class="tag__text"><?= $data['text'] ?></span>
    <?= renderIcon('close', 'tag__icon') ?>
</button>