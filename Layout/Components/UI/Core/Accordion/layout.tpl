<?php
/** @var array $data */

$accordionClasses = array_filter([
    'accordion',
    "accordion-{$data['style']}",
    "accordion-size_{$data['size']}",
    "accordion-theme_{$data['theme']}",
    $data['className'] ?? ''
]);

$attributes = $data['attributes'] ?? [];
$attributeString = implode(' ', array_map(
    fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value)),
    array_keys($attributes),
    $attributes
));
?>

<li class="<?= implode(' ', $accordionClasses) ?>" <?= $attributeString ?>>
    <div class="accordion__header" role="tab">
        <div class="accordion__header-wrapper">
            <?php if ($data['icon']): ?>
                <?= renderIcon($data['icon'], 'accordion__icon') ?>
            <?php endif; ?>

            <div class="accordion__text-wrapper">
                <h3 class="accordion__title"><?= $data['title'] ?></h3>
                <?php if ($data['subtitle']): ?>
                    <span class="accordion__subtitle"><?= $data['subtitle'] ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="accordion__controls-wrapper">
            <?php if ($data['comment']): ?>
                <span class="accordion__comment"><?= $data['comment'] ?></span>
            <?php endif; ?>

            <?php if ($data['badge']): ?>
                <span class="accordion__badge"><?= $data['badge'] ?></span>
            <?php endif; ?>

            <button class="accordion__button">
                <?= renderIcon('chevron-down', 'accordion__chevron') ?>
            </button>
        </div>
    </div>
    <div class="accordion__content" role="tabpanel">
        <div class="accordion__description content">
            <?= $data['description'] ?>
        </div>
    </div>
</li>