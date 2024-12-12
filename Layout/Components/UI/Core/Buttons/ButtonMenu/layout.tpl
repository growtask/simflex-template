<?php
/** @var array $data */

$buttonMenuClasses = [
    'btn-menu',
    "btn-menu-{$data['style']}",
    "btn-menu-size_{$data['size']}",
    "btn-menu-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<?php if ($data['link']): ?>
    <a href="<?= $data['link'] ?>" class="<?= implode(' ', $buttonMenuClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php if ($data['badge']): ?>
            <span class="btn-menu__badge"><?= $data['badge'] ?></span>
        <?php endif; ?>

        <?php if ($data['icon']): ?>
            <?= renderIcon($data['icon'], 'btn-menu__icon') ?>
        <?php endif; ?>

        <?php if ($data['text']): ?>
            <span class="btn-menu__text"><?= $data['text'] ?></span>
        <?php endif; ?>
    </a>
<?php else: ?>
    <button class="<?= implode(' ', $buttonMenuClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php if ($data['badge']): ?>
            <span class="btn-menu__badge"><?= $data['badge'] ?></span>
        <?php endif; ?>

        <?php if ($data['icon']): ?>
            <?= renderIcon($data['icon'], 'btn-menu__icon') ?>
        <?php endif; ?>

        <?php if ($data['text']): ?>
            <span class="btn-menu__text"><?= $data['text'] ?></span>
        <?php endif; ?>
    </button>
<?php endif; ?>