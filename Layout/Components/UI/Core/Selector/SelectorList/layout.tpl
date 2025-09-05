<?php
/** @var array $data */

$checkboxClasses = [
    'selector-list',
    "selector-list-theme_{$data['theme']}",
    $data['className'] ?? ''
];
?>

<?php if ($data['tabsArr']): ?>
    <div class="<?= implode(' ', $checkboxClasses) ?>">
        <?php foreach ($data['tabsArr'] as $props) {
            App\Layout\Components\UI\Core\Selector\SelectorTab\Layout::drawSelectorTab(
                className: $props['className'] ?? '',
                text: $props['text'] ?? '',
                isActive: $props['isActive'] ?? '',
                theme: $data['theme'] == 'dark'
                    ? \App\Layout\Components\UI\Core\Selector\SelectorTab\SelectorTabTheme::Dark
                    : \App\Layout\Components\UI\Core\Selector\SelectorTab\SelectorTabTheme::Light,
                attributes: $props['attributes'] ?? []
            );
        } ?>
    </div>
<?php endif; ?>