<?php
/** @var array $data */

$tabImageClasses = [
    'tab-marker',
    "tab-marker-{$data['style']}",
    "tab-marker-size_{$data['size']}",
    "tab-marker-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<?php if ($data['link']): ?>
    <a class="<?= implode(' ', $tabImageClasses) ?>"
       href="<?= $data['link'] ?>"
       <?= buildAttrs($data['attributes'] ?? []) ?>
    >
        <?php if ($data['marker']): ?>
            <div class="tab-marker__marker" <?= buildAttrs($data['attributes'] ?? []) ?>>
                <?= renderIcon($data['marker'], 'tab-marker__marker-icon') ?>
            </div>
        <?php endif; ?>

        <div class="tab-marker__content">
            <div class="tab-marker__text-wrap">
                <span class="tab-marker__text"><?= $data['text'] ?></span>
    
                <?php if ($data['description']): ?>
                    <span class="tab-marker__desc"><?= $data['description'] ?></span>
                <?php endif; ?>
            </div>
    
            <?php if ($data['badge']):
                \App\Layout\Components\UI\Core\Badges\BadgeMain\Layout::drawBadgeMain(
                    text: $data['badge'],
                    style: \App\Layout\Components\UI\Core\Badges\BadgeMain\BadgeMainStyle::Error
                );
            endif; ?>
    
            <?php if ($data['iconRight']): ?>
                <?= renderIcon($data['iconRight'], 'tab-marker__icon-right') ?>
            <?php endif; ?>
        </div>
    </a>
<?php else: ?>
    <button class="<?= implode(' ', $tabImageClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php if ($data['marker']): ?>
            <div class="tab-marker__marker" <?= buildAttrs($data['attributes'] ?? []) ?>>
                <?= renderIcon($data['marker'], 'tab-marker__marker-icon') ?>
            </div>
        <?php endif; ?>

        <div class="tab-marker__content">
            <div class="tab-marker__text-wrap">
                <span class="tab-marker__text"><?= $data['text'] ?></span>

                <?php if ($data['description']): ?>
                    <span class="tab-marker__desc"><?= $data['description'] ?></span>
                <?php endif; ?>
            </div>

            <?php if ($data['badge']):
                \App\Layout\Components\UI\Core\Badges\BadgeMain\Layout::drawBadgeMain(
                    className: 'tab-marker__badge',
                    text: $data['badge'],
                    style: \App\Layout\Components\UI\Core\Badges\BadgeMain\BadgeMainStyle::Error
                );
            endif; ?>

            <?php if ($data['iconRight']): ?>
                <?= renderIcon($data['iconRight'], 'tab-marker__icon-right') ?>
            <?php endif; ?>
        </div>
    </button>
<?php endif; ?>