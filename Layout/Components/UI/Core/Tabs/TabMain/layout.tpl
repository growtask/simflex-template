<?php
/** @var array $data */

$tabClasses = [
    'tab-main',
    "tab-main-{$data['style']}",
    "tab-main-size_{$data['size']}",
    "tab-main-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<?php if ($data['link']): ?>
    <a href="<?= $data['link'] ?>" class="<?= implode(' ', $tabClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php if ($data['iconLeft']): ?>
            <div class="tab-main__icon-left">
                <?= renderIcon($data['iconLeft'], 'tab-main__icon') ?>
            </div>
        <?php endif; ?>

        <span class="tab-main__text"><?= $data['text'] ?></span>

        <?php if ($data['badge']):
            \App\Layout\Components\UI\Core\Badges\BadgeMain\Layout::drawBadgeMain(
                text: $data['badge'],
                style: \App\Layout\Components\UI\Core\Badges\BadgeMain\BadgeMainStyle::Error
            );
        endif; ?>

        <?php if ($data['iconRight']): ?>
            <div class="tab-main__icon-right">
                <?= renderIcon($data['iconRight'], 'tab-main__icon') ?>
            </div>
        <?php endif; ?>
    </a>
<?php else: ?>
    <button type="<?= $data['attributes']['type'] ?? 'button' ?>" class="<?= implode(' ', $tabClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php if ($data['iconLeft']): ?>
            <div class="tab-main__icon-left">
                <?= renderIcon($data['iconLeft'], 'tab-main__icon') ?>
            </div>
        <?php endif; ?>

        <span class="tab-main__text"><?= $data['text'] ?></span>

        <?php if ($data['badge']):
            \App\Layout\Components\UI\Core\Badges\BadgeMain\Layout::drawBadgeMain(
                text: $data['badge'],
                style: \App\Layout\Components\UI\Core\Badges\BadgeMain\BadgeMainStyle::Error
            );
        endif; ?>

        <?php if ($data['iconRight']): ?>
            <div class="tab-main__icon-right">
                <?= renderIcon($data['iconRight'], 'tab-main__icon') ?>
            </div>
        <?php endif; ?>
    </button>
<?php endif; ?>
