<?php
/** @var array $data */

$buttonClasses = [
    'btn',
    "btn-{$data['style']}",
    "btn-size_{$data['size']}",
    "btn-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<?php if ($data['link']): ?>
    <a href="<?= $data['link'] ?>" class="<?= implode(' ', $buttonClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php if ($data['icon'] && $data['iconPos'] === 'left' && $data['text']): ?>
            <span class="btn__icon btn__icon_left">
                <?= renderIcon($data['icon']) ?>
            </span>
        <?php endif; ?>

        <?php if ($data['text']): ?>
            <span class="btn__text"><?= $data['text'] ?></span>
        <?php endif; ?>

        <?php if ($data['icon'] && $data['iconPos'] && !$data['text']): ?>
            <span class="btn__icon btn__icon_center">
                <?= renderIcon($data['icon']) ?>
            </span>
        <?php endif; ?>

        <?php if ($data['icon'] && $data['iconPos'] === 'right' && $data['text']): ?>
            <span class="btn__icon btn__icon_right">
                <?= renderIcon($data['icon']) ?>
            </span>
        <?php endif; ?>
    </a>
<?php else: ?>
    <button type="<?= $data['attributes']['type'] ?? 'button' ?>" class="<?= implode(' ', $buttonClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php if ($data['icon'] && $data['iconPos'] === 'left' && $data['text']): ?>
            <span class="btn__icon btn__icon_left">
                <?= renderIcon($data['icon']) ?>
            </span>
        <?php endif; ?>

        <?php if ($data['text']): ?>
            <span class="btn__text"><?= $data['text'] ?></span>
        <?php endif; ?>

        <?php if ($data['icon'] && $data['iconPos'] && !$data['text']): ?>
            <span class="btn__icon btn__icon_center">
                <?= renderIcon($data['icon']) ?>
            </span>
        <?php endif; ?>

        <?php if ($data['icon'] && $data['iconPos'] === 'right' && $data['text']): ?>
            <span class="btn__icon btn__icon_right">
                <?= renderIcon($data['icon']) ?>
            </span>
        <?php endif; ?>

        <?php if ($data['loader']): ?>
            <span class="btn__loader">
                <?php App\Layout\Components\UI\Other\LoaderIcon\Layout::draw(); ?>
            </span>
        <?php endif; ?>
    </button>
<?php endif; ?>
