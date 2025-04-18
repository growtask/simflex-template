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
        <?php if ($data['iconLeft']): ?>
            <?= renderIcon($data['iconLeft'], 'tab__icon-left') ?>
        <?php endif; ?>

        <span class="tab__text"><?= $data['text'] ?></span>

        <?php if ($data['badge']): ?>
            <span class="tab__badge"><?= $data['badge'] ?></span>
        <?php endif; ?>

        <?php if ($data['iconRight']): ?>
            <?= renderIcon($data['iconRight'], 'tab__icon-right') ?>
        <?php endif; ?>
    </a>
<?php else: ?>
    <button type="<?= $data['attributes']['type'] ?? 'button' ?>" class="<?= implode(' ', $tabClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php if ($data['iconLeft']): ?>
            <?= renderIcon($data['iconLeft'], 'tab__icon-left') ?>
        <?php endif; ?>

        <span class="tab__text"><?= $data['text'] ?></span>

        <?php if ($data['badge']): ?>
            <span class="tab__badge"><?= $data['badge'] ?></span>
        <?php endif; ?>

        <?php if ($data['iconRight']): ?>
            <?= renderIcon($data['iconRight'], 'tab__icon-right') ?>
        <?php endif; ?>
    </button>
<?php endif; ?>
