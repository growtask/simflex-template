<?php
/** @var array $data */

$tabClasses = [
    'tab',
    "tab-{$data['style']}",
    "tab-size_{$data['size']}",
    "tab-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<?php if ($data['link']): ?>
    <a href="<?= $data['link'] ?>" class="<?= implode(' ', $tabClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php if ($data['icon']): ?>
            <?= renderIcon($data['icon'], 'tab__icon') ?>
        <?php endif; ?>

        <span class="tab__text"><?= $data['text'] ?></span>

        <?php if ($data['badge']): ?>
            <span class="tab__badge"><?= $data['badge'] ?></span>
        <?php endif; ?>

        <?php if ($data['chevron']): ?>
            <?= renderIcon('chevron-' . $data['chevron'] . '-sm', 'tab__chevron') ?>
        <?php endif; ?>
    </a>
<?php else: ?>
    <button class="<?= implode(' ', $tabClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php if ($data['icon']): ?>
            <?= renderIcon($data['icon'], 'tab__icon') ?>
        <?php endif; ?>

        <span class="tab__text"><?= $data['text'] ?></span>

        <?php if ($data['badge']): ?>
            <span class="tab__badge"><?= $data['badge'] ?></span>
        <?php endif; ?>

        <?php if ($data['chevron']): ?>
            <?= renderIcon('chevron-' . $data['chevron'] . '-sm', 'tab__chevron') ?>
        <?php endif; ?>
    </button>
<?php endif; ?>
