<?php
/** @var array $data */

$tabHeadlingClasses = [
    'tab-headling',
    $data['className'] ?? ''
];
?>

<?php if ($data['link']): ?>
    <a class="<?= implode(' ', $tabHeadlingClasses) ?>"
       href="<?= $data['link'] ?>"
        <?= buildAttrs($data['attributes'] ?? []) ?>
    >
        <span class="tab-headling__text"><?= $data['text'] ?></span>

        <?php if ($data['badge']):
            \App\Layout\Components\UI\Core\Badges\BadgeMain\Layout::drawBadgeMain(
                text: $data['badge'],
                style: \App\Layout\Components\UI\Core\Badges\BadgeMain\BadgeMainStyle::Gray,
                size: \App\Layout\Components\UI\Core\Badges\BadgeMain\BadgeMainSize::Small
            );
        endif; ?>
    </a>
<?php else: ?>
    <button class="<?= implode(' ', $tabHeadlingClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <span class="tab-headling__text"><?= $data['text'] ?></span>

        <?php if ($data['badge']):
            \App\Layout\Components\UI\Core\Badges\BadgeMain\Layout::drawBadgeMain(
                text: $data['badge'],
                style: \App\Layout\Components\UI\Core\Badges\BadgeMain\BadgeMainStyle::Gray,
                size: \App\Layout\Components\UI\Core\Badges\BadgeMain\BadgeMainSize::Small
            );
        endif; ?>
    </button>
<?php endif; ?>