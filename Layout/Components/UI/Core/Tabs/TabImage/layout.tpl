<?php
/** @var array $data */

$tabImageClasses = [
    'tab-image',
    "tab-image-{$data['style']}",
    "tab-image-size_{$data['size']}",
    "tab-image-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<?php if ($data['link']): ?>
    <a class="<?= implode(' ', $tabImageClasses) ?>"
       href="<?= $data['link'] ?>"
       <?= buildAttrs($data['attributes'] ?? []) ?>
    >
        <?php App\Layout\Components\Common\Core\Main\MainImg\Layout::drawImg(
            className: 'tab-image__image',
            src: $data['image'],
            alt: $data['text'],
            attributes: ['loading' => 'lazy']
        ); ?>

        <div class="tab-image__content">
            <div class="tab-image__text-wrap">
                <span class="tab-image__text"><?= $data['text'] ?></span>

                <?php if ($data['description']): ?>
                    <span class="tab-image__desc"><?= $data['description'] ?></span>
                <?php endif; ?>
            </div>

            <?php if ($data['badge']):
                \App\Layout\Components\UI\Core\Badges\BadgeMain\Layout::drawBadgeMain(
                    text: $data['badge'],
                    style: \App\Layout\Components\UI\Core\Badges\BadgeMain\BadgeMainStyle::Error
                );
            endif; ?>

            <?php if ($data['iconRight']): ?>
                <?= renderIcon($data['iconRight'], 'tab-image__icon-right') ?>
            <?php endif; ?>
        </div>
    </a>
<?php else: ?>
    <button class="<?= implode(' ', $tabImageClasses) ?>" <?= buildAttrs($data['attributes'] ?? []) ?>>
        <?php App\Layout\Components\Common\Core\Main\MainImg\Layout::drawImg(
            className: 'tab-image__image',
            src: $data['image'],
            alt: $data['text'],
            attributes: ['loading' => 'lazy']
        ); ?>

        <div class="tab-image__content">
            <div class="tab-image__text-wrap">
                <span class="tab-image__text"><?= $data['text'] ?></span>

                <?php if ($data['description']): ?>
                    <span class="tab-image__desc"><?= $data['description'] ?></span>
                <?php endif; ?>
            </div>

            <?php if ($data['badge']):
                \App\Layout\Components\UI\Core\Badges\BadgeMain\Layout::drawBadgeMain(
                    className: 'tab-image__badge',
                    text: $data['badge'],
                    style: \App\Layout\Components\UI\Core\Badges\BadgeMain\BadgeMainStyle::Error
                );
            endif; ?>

            <?php if ($data['iconRight']): ?>
                <?= renderIcon($data['iconRight'], 'tab-image__icon-right') ?>
            <?php endif; ?>
        </div>
    </button>
<?php endif; ?>