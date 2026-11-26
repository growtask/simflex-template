<?php

/**
 * Витрина UI-компонентов: страница на компонент (демо из demo.tpl в папках компонентов ядра
 * и сайта, см. Simflex\Core\Layout\LayoutShowcase). Только в режиме разработки.
 * Страница создаётся в админке с шаблоном «Витрина UI»; компонент выбирается параметром ?component=UI/Badge.
 */

use Simflex\Core\Http\PageAssets;
use Simflex\Core\Layout\LayoutFoundations;
use Simflex\Core\Layout\LayoutShowcase;
use Simflex\Core\System\Container;

if (!Container::getConfig()->devMode) {
    echo '<p>Витрина доступна только в режиме разработки.</p>';
    return;
}

// тема сайта (theme/default/index.tpl) не выводит на этой странице шапку, подвал и окно заявки
Container::set('themeBare', true);

PageAssets::css(asset('ui-showcase/ui-showcase.css'), 90);
// ui-showcase.js должен подключиться раньше Alpine: он регистрирует данные в событии alpine:init
PageAssets::js(asset('ui-showcase/ui-showcase.js'), 90);
PageAssets::js(asset('ui-showcase/alpine.min.js'), 91);

$h = static fn(string $text): string => htmlspecialchars($text);
$ucfirst = static fn(string $text): string => mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
$components = LayoutShowcase::getComponents();
// страницы вне компонентов: «Начало» (intro, coverage) и «Основы» (LayoutFoundations::PAGES)
$startPages = ['intro' => 'Введение', 'coverage' => 'Полнота по Figma'];
$pages = $startPages + LayoutFoundations::PAGES;
$page = (string)Container::getRequest()?->get('page', '');
$key = (string)Container::getRequest()?->get('component', '');
// блок компонентов (папка UI/Button): страница со списком его компонентов и наборов Figma
$blockKey = (string)Container::getRequest()?->get('block', '');
$blocks = [];
foreach ($components as $item) {
    if ($item['group'] !== '') {
        $blocks[$item['group']][$item['key']] = $item;
    }
}
if (!isset($pages[$page]) && isset($blocks[$blockKey])) {
    $page = 'block';
}
if ($page === 'block') {
    $key = '';
} elseif (isset($pages[$page])) {
    $key = '';
} elseif (!isset($components[$key])) {
    $key = (string)array_key_first($components);
    $page = '';
}
$component = $components[$key] ?? null;
$baseUrl = Container::getRequest()?->getPath() ?? '/';
$link = static fn(string $componentKey): string => $baseUrl . '?component=' . urlencode($componentKey);
$pageLink = static fn(string $pageKey): string => $baseUrl . '?page=' . urlencode($pageKey);
$blockLink = static fn(string $block): string => $baseUrl . '?block=' . urlencode($block);
$blockTitle = static fn(string $group): string => LayoutShowcase::GROUP_TITLES[$group] ?? $group;
$figmaDocs = $component ? LayoutShowcase::getFigmaDocs($component['key']) : [];

// меню: раздел => группа => компоненты
$menu = [];
foreach ($components as $item) {
    $menu[$item['section']][$item['group']][$item['key']] = $item;
}
$keys = array_keys($components);
$position = array_search($key, $keys, true);
$previous = $position !== false && $position > 0 ? $components[$keys[$position - 1]] : null;
$next = $position !== false && isset($keys[$position + 1]) ? $components[$keys[$position + 1]] : null;

/** Панели темы: demo в нужных темах (переключаются вкладками) */
$panes = static function () use ($component, $h): string {
    $html = '';
    foreach (LayoutShowcase::THEMES as $theme => $title) {
        $body = LayoutShowcase::renderDemo($component['key'], $theme);
        $html .= '<div class="docs-pane docs-pane_' . $theme . ' docs-pane_demo"'
            . ' x-show="mode !== \'' . ($theme === 'light' ? 'dark' : 'light') . '\'">'
            . '<span class="docs-pane__label">' . $h($theme) . '</span><div class="docs-pane__body">' . $body
            . '</div></div>';
    }

    return $html;
};
$modeTabs = static function () use ($h): string {
    $html = '<div class="docs-preview__bar" role="group" aria-label="Темы">';
    foreach (['both' => 'Обе темы'] + LayoutShowcase::THEMES as $mode => $title) {
        $html .= '<button type="button" class="docs-chip-btn" :aria-pressed="mode === \'' . $mode . '\'"'
            . ' @click="setMode(\'' . $mode . '\')">' . $h($title) . '</button>';
    }

    return $html . '</div>';
};
$statusTitles = ['core' => 'ядро', 'site' => 'сайт', 'replaced' => 'сайт заменяет ядро'];

$toc = [];
if ($component) {
    if ($figmaDocs) {
        $toc['figma'] = 'Описание из Figma';
    }
    $toc['variants'] = 'Все варианты';
    if ($component['overrides']) {
        $toc['override'] = 'Переопределение';
    }
}

/** Образец цвета: градиенты и прозрачные значения рисуются поверх шахматки */
$swatch = static fn(string $value): string => '<span class="docs-swatch" style="--swatch:' . htmlspecialchars($value) . '"></span>';

$figmaAll = LayoutShowcase::getAllFigmaDocs();
$figmaTotal = array_sum(array_map('count', $figmaAll));
?>
<div class="docs" x-data="uiShowcase" :data-theme="theme">
    <aside class="docs-side" aria-label="Компоненты">
        <div class="docs-tools">
            <input type="search" class="docs-search" x-model="query" placeholder="Поиск компонента"
                   aria-label="Поиск компонента">
            <div class="docs-theme-switch" role="group" aria-label="Тема страницы">
                <?php foreach (LayoutShowcase::THEMES as $theme => $title): ?>
                    <button type="button" class="docs-chip-btn" :aria-pressed="theme === '<?= $theme ?>'"
                            @click="setTheme('<?= $theme ?>')"><?= $h($title) ?></button>
                <?php endforeach; ?>
            </div>
        </div>
        <nav class="docs-nav">
            <?php foreach (['Начало' => $startPages, 'Основы' => LayoutFoundations::PAGES] as $groupTitle => $groupPages): ?>
                <div class="docs-nav__group" x-show='anyMatch(<?= $h(json_encode(array_merge([$groupTitle], array_values($groupPages)), JSON_UNESCAPED_UNICODE)) ?>)'>
                    <div class="docs-nav__title"><?= $h($groupTitle) ?></div>
                    <ul class="docs-nav__list">
                        <?php foreach ($groupPages as $pageKey => $pageTitle): ?>
                            <li>
                                <a class="docs-nav__link<?= $pageKey === $page ? ' docs-nav__link_active' : '' ?>"
                                   href="<?= $h($pageLink($pageKey)) ?>"
                                   <?= $pageKey === $page ? 'aria-current="page"' : '' ?>><?= $h($pageTitle) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
            <?php foreach ($menu as $section => $groups): ?>
                <?php foreach ($groups as $group => $items): ?>
                    <?php $texts = array_map(static fn(array $item): string => $item['name'] . ' ' . $group, $items); ?>
                    <div class="docs-nav__group" x-show='anyMatch(<?= $h(json_encode(array_values($texts), JSON_UNESCAPED_UNICODE)) ?>)'>
                        <div class="docs-nav__title">
                            <?php if ($group !== ''): ?>
                                <a class="docs-nav__title-link" href="<?= $h($blockLink($group)) ?>"><?= $h(LayoutShowcase::SECTIONS[$section] . ' · ' . $blockTitle($group)) ?></a>
                            <?php else: ?>
                                <?= $h(LayoutShowcase::SECTIONS[$section]) ?>
                            <?php endif; ?>
                        </div>
                        <ul class="docs-nav__list">
                            <?php foreach ($items as $item): ?>
                                <li x-show="matches('<?= $h(addslashes($item['name'] . ' ' . $group)) ?>')">
                                    <a class="docs-nav__link<?= $item['key'] === $key ? ' docs-nav__link_active' : '' ?>"
                                       href="<?= $h($link($item['key'])) ?>"
                                       <?= $item['key'] === $key ? 'aria-current="page"' : '' ?>>
                                        <?= $h($item['name']) ?>
                                        <?php if ($item['inSite']): ?>
                                            <span class="docs-nav__tag"><?= $item['overrides'] ? 'App ≠ ядро' : 'App' ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php if (!$components): ?>
                <p class="docs-nav__empty">Нет компонентов с demo.tpl.</p>
            <?php endif; ?>
        </nav>
    </aside>

    <div class="docs-body">
        <main class="docs-main">
            <?php if ($page !== ''): ?>
                <article>
                    <p class="docs-eyebrow"><?= $page === 'block' ? 'UI-компоненты' : (isset($startPages[$page]) ? 'Начало' : 'Основы') ?></p>
                    <h1 class="docs-h1"><?= $h($page === 'block' ? $blockTitle($blockKey) : $pages[$page]) ?><?php if ($page === 'block'): ?> <code class="docs-h1__code">UI/<?= $h($blockKey) ?></code><?php endif; ?></h1>

                    <?php if ($page === 'block'): ?>
                        <p class="docs-p">Компоненты блока (<?= count($blocks[$blockKey]) ?>) и наборы Figma, которые они реализуют.</p>
                        <table class="docs-table">
                            <thead><tr><th>Компонент</th><th>Набор Figma</th><th>Что это</th></tr></thead>
                            <tbody>
                            <?php foreach ($blocks[$blockKey] as $item): ?>
                                <?php $sets = $figmaAll[$item['key']] ?? [['set' => '', 'what' => '']]; ?>
                                <?php foreach ($sets as $index => $set): ?>
                                    <tr>
                                        <td><?php if ($index === 0): ?><a href="<?= $h($link($item['key'])) ?>"><?= $h($item['name']) ?></a><?php endif; ?></td>
                                        <td><?= $set['set'] !== '' ? '<code class="docs-inline">' . $h($set['set']) . '</code>' : '<span class="docs-muted">нет в Figma</span>' ?></td>
                                        <td><?= $h($set['what']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if ($page === 'intro'): ?>
                        <p class="docs-p">Витрина показывает UI-компоненты ядра и сайта в светлой и тёмной темах и собрана по образцу
                            страницы документации UI-кита. Компоненты рисуются настоящим кодом Layout, а не макетами.</p>
                        <h2 class="docs-h2">Как устроена витрина</h2>
                        <ul class="docs-list">
                            <li><b>Начало</b>: введение и полнота покрытия набора Figma.</li>
                            <li><b>Основы</b>: токены дизайн-системы (цвета, типографика, тени, скругления, движение, иконки);
                                значения читаются из SCSS ядра и не расходятся с кодом.</li>
                            <li><b>UI-компоненты</b>: страница на компонент с краткой инструкцией из Figma
                                («Зачем», «Как работать», «Ограничения») и всеми вариантами в обеих темах.</li>
                        </ul>
                        <h2 class="docs-h2">Как добавить компонент</h2>
                        <p class="docs-p">Положите в папку компонента файл <code class="docs-inline">demo.tpl</code> со всеми
                            вариантами (стили, размеры, состояния) и, если у компонента есть набор в Figma, добавьте его описание
                            в <code class="docs-inline">Layout/figma-docs.php</code> ядра. Страница появится в меню сама.</p>
                        <p class="docs-muted">Витрина работает только в режиме разработки.</p>

                    <?php elseif ($page === 'coverage'): ?>
                        <p class="docs-p">Наборы страницы UI в Figma (<?= $figmaTotal ?>) и компоненты, которые их реализуют.
                            Строка ведёт на страницу компонента.</p>
                        <p class="docs-muted">Наборы <code class="docs-inline">focus</code> (кольцо фокуса с клавиатуры) — поведение:
                            оно реализовано в стилях каждого компонента, а образец показан у компонента, к которому относится набор.
                            Наборы <code class="docs-inline">hint</code> (подсказки кнопки, таба, поля) — это TooltipMain.</p>
                        <table class="docs-table">
                            <thead><tr><th>Набор Figma</th><th>Что это</th><th>Блок</th><th>Компонент</th></tr></thead>
                            <tbody>
                            <?php foreach ($figmaAll as $componentKey => $sets): ?>
                                <?php foreach ($sets as $set): ?>
                                    <tr>
                                        <td><code class="docs-inline"><?= $h($set['set']) ?></code></td>
                                        <td><?= $h($set['what']) ?></td>
                                        <td><?= $h(isset($components[$componentKey]) && $components[$componentKey]['group'] !== '' ? $blockTitle($components[$componentKey]['group']) : '—') ?></td>
                                        <td>
                                            <?php if (isset($components[$componentKey])): ?>
                                                <a href="<?= $h($link($componentKey)) ?>"><?= $h($components[$componentKey]['name']) ?></a>
                                            <?php else: ?>
                                                <?= $h($componentKey) ?> <span class="docs-muted">(нет демо)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php
                        $withoutFigma = array_diff_key($components, $figmaAll);
                        ?>
                        <?php if ($withoutFigma): ?>
                            <h2 class="docs-h2">Нет в Figma</h2>
                            <p class="docs-p">Компоненты без набора на странице UI:
                                <?php foreach ($withoutFigma as $item): ?>
                                    <a class="docs-inline" href="<?= $h($link($item['key'])) ?>"><?= $h($item['name']) ?></a>
                                <?php endforeach; ?>
                            </p>
                        <?php endif; ?>

                    <?php elseif ($page === 'colors'): ?>
                        <p class="docs-p">Цвета заданы переменными <code class="docs-inline">--color-*</code>.
                            Имя стиля Figma: «/» заменяется на «-» (main/gray/black → <code class="docs-inline">--color-main-gray-black</code>).</p>
                        <?php foreach (LayoutFoundations::getColors() as $groupTitle => $colors): ?>
                            <h2 class="docs-h2"><?= $h($groupTitle) ?></h2>
                            <div class="docs-swatches">
                                <?php foreach ($colors as $name => $value): ?>
                                    <div class="docs-swatch-card">
                                        <?= $swatch('var(' . $name . ')') ?>
                                        <code class="docs-swatch-card__name"><?= $h($name) ?></code>
                                        <span class="docs-swatch-card__value"><?= $h(strlen($value) > 24 ? 'градиент' : $value) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>

                    <?php elseif ($page === 'typography'): ?>
                        <p class="docs-p">Стили текста подключаются миксинами из <code class="docs-inline">tokens/_typography.scss</code>
                            (<code class="docs-inline">@include h1</code>, <code class="docs-inline">t2</code>,
                            <code class="docs-inline">ui-m</code>). Размеры даны для ширины 1920; на меньших экранах
                            пересчитываются в vw.</p>
                        <table class="docs-table">
                            <thead><tr><th>Миксин</th><th>Размер</th><th>Вес</th><th>Интервал</th><th>Образец</th></tr></thead>
                            <tbody>
                            <?php foreach (LayoutFoundations::getTypography() as $style): ?>
                                <tr>
                                    <td><code class="docs-inline"><?= $h($style['name']) ?></code></td>
                                    <td><?= $h($style['size']) ?></td>
                                    <td><?= $h($style['weight']) ?></td>
                                    <td><?= $h($style['lineHeight']) ?></td>
                                    <td><span style="font-size:<?= min(40, (int)$style['size']) ?>px;font-weight:<?= (int)$style['weight'] ?>;line-height:<?= $h($style['lineHeight']) ?>">Съешь ещё этих мягких булок</span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    <?php elseif ($page === 'shadows'): ?>
                        <p class="docs-p">Тени заданы переменными <code class="docs-inline">--shadow-*</code>:
                            нейтральные (gray) и фирменные (primary), от xs до xl.</p>
                        <div class="docs-swatches">
                            <?php foreach (LayoutFoundations::getShadows() as $name => $value): ?>
                                <div class="docs-swatch-card">
                                    <span class="docs-shadow" style="box-shadow:var(<?= $h($name) ?>)"></span>
                                    <code class="docs-swatch-card__name"><?= $h($name) ?></code>
                                    <span class="docs-swatch-card__value"><?= $h($value) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php elseif ($page === 'radius'): ?>
                        <p class="docs-p">Скругления заданы переменными <code class="docs-inline">--radius-*</code>
                            (коллекция corner radius в Figma): шесть групп, у каждой пять режимов. Режим меняется
                            атрибутом <code class="docs-inline">data-radius</code> на корне
                            (<code class="docs-inline">none</code>, <code class="docs-inline">s</code>,
                            <code class="docs-inline">m</code>, <code class="docs-inline">l</code>,
                            <code class="docs-inline">full</code>); по умолчанию действует <code class="docs-inline">m</code>.
                            В стилях: <code class="docs-inline">border-radius: var(--radius-ui-medium)</code>
                            или <code class="docs-inline">$radiusMedium</code>; для круга — <code class="docs-inline">50%</code>,
                            для полной таблетки полос и тумблеров — <code class="docs-inline">var(--radius-pill)</code>.</p>
                        <?php foreach (LayoutFoundations::getRadiusScale() as $group): ?>
                            <h3 class="docs-h3"><?= $h($group['title']) ?> <code class="docs-inline"><?= $h($group['variable']) ?></code></h3>
                            <div class="docs-radii">
                                <?php foreach ($group['modes'] as $mode => $value): ?>
                                    <?php $isPill = $value === '9999px'; ?>
                                    <div class="docs-radius-card<?= $mode === 'm' ? ' docs-radius-card_default' : '' ?>">
                                        <span class="docs-radius" style="border-radius:<?= $h($value) ?>"></span>
                                        <b class="docs-radius-card__mode"><?= $mode === 'none' ? 'none' : $h($mode) ?></b>
                                        <span class="docs-swatch-card__value"><?= $isPill ? 'pill' : ($value === '0' ? '0 px' : $h(str_replace('px', ' px', $value))) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>

                    <?php elseif ($page === 'motion'): ?>
                        <p class="docs-p">Переходы подключаются миксином <code class="docs-inline">transition</code>:
                            по умолчанию 0.2s и кривая <code class="docs-inline">cubic-bezier(0.14, 0.18, 0.16, 1.02)</code>.
                            Для анимаций в ядре есть готовые <code class="docs-inline">@keyframes</code>.</p>
                        <div class="docs-swatches">
                            <?php foreach (LayoutFoundations::getAnimations() as $name): ?>
                                <div class="docs-swatch-card">
                                    <span class="docs-motion" style="animation:<?= $h($name) ?> 1.6s infinite"></span>
                                    <code class="docs-swatch-card__name"><?= $h($name) ?></code>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php elseif ($page === 'icons'): ?>
                        <?php
                        $iconWeight = (string)Container::getRequest()?->get('weight', '') === 'slim' ? 'slim' : 'regular';
                        $iconSections = array_values(array_filter(
                            LayoutFoundations::getIconGroups(),
                            static fn(array $section): bool => $section['slim'] === ($iconWeight === 'slim') || ($iconWeight === 'regular' && $section['id'] === 'social')
                        ));
                        $iconCount = array_sum(array_map(
                            static fn(array $section): int => count($section['icons']) + array_sum(array_map(static fn(array $group): int => count($group['icons']), $section['groups'])),
                            $iconSections
                        ));
                        $iconTitle = static fn(array $section): string => preg_replace('/-slim$/', '', $section['title']) ?? $section['title'];
                        $iconDraw = static function (array $icons) use ($h): void {
                            echo '<div class="docs-icons">';
                            foreach ($icons as $icon) {
                                echo '<div class="docs-icon" title="' . $h($icon) . '">' . renderIcon($icon, 'docs-icon__svg')
                                    . '<span class="docs-icon__name">' . $h($icon) . '</span></div>';
                            }
                            echo '</div>';
                        };
                        ?>
                        <p class="docs-p">Иконки спрайта темы сайта собраны в группы, как на странице Icons в Figma: раздел
                            (<code class="docs-inline">action</code>, <code class="docs-inline">direction</code>…)
                            и подгруппы внутри него (<code class="docs-inline">rotate</code>, <code class="docs-inline">arrow</code>…).
                            В спрайте это вложенные <code class="docs-inline">&lt;g id="group-…" data-title="…"&gt;</code>.
                            Вывод: <code class="docs-inline">renderIcon('имя', 'класс')</code>.</p>
                        <div class="docs-icon-tabs" role="tablist">
                            <a class="docs-icon-tab<?= $iconWeight === 'regular' ? ' docs-icon-tab_active' : '' ?>" href="<?= $h($pageLink('icons')) ?>">Обычные</a>
                            <a class="docs-icon-tab<?= $iconWeight === 'slim' ? ' docs-icon-tab_active' : '' ?>" href="<?= $h($pageLink('icons') . '&weight=slim') ?>">Тонкие (slim)</a>
                            <span class="docs-icon-tabs__count"><?= $iconCount ?> шт., разделов: <?= count($iconSections) ?></span>
                        </div>
                        <nav class="docs-icon-nav" aria-label="Разделы иконок">
                            <?php foreach ($iconSections as $section): ?>
                                <a class="docs-chip" href="#icons-<?= $h($section['id']) ?>"><?= $h($iconTitle($section)) ?></a>
                            <?php endforeach; ?>
                        </nav>
                        <?php foreach ($iconSections as $section): ?>
                            <?php $sectionCount = count($section['icons']) + array_sum(array_map(static fn(array $group): int => count($group['icons']), $section['groups'])); ?>
                            <h2 class="docs-h2" id="icons-<?= $h($section['id']) ?>"><?= $h($iconTitle($section)) ?> <span class="docs-h2__count"><?= $sectionCount ?></span></h2>
                            <?php if ($section['icons'] !== []) { $iconDraw($section['icons']); } ?>
                            <?php foreach ($section['groups'] as $group): ?>
                                <h3 class="docs-h3"><?= $h($group['title']) ?> <span class="docs-h2__count"><?= count($group['icons']) ?></span></h3>
                                <?php $iconDraw($group['icons']); ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (isset($pages[$page])): ?>
                    <nav class="docs-pager" aria-label="Страницы">
                        <?php
                        $pageKeys = array_keys($pages);
                        $pagePosition = (int)array_search($page, $pageKeys, true);
                        ?>
                        <span><?php if ($pagePosition > 0): ?><a href="<?= $h($pageLink($pageKeys[$pagePosition - 1])) ?>">← <?= $h($pages[$pageKeys[$pagePosition - 1]]) ?></a><?php endif; ?></span>
                        <span><?php if (isset($pageKeys[$pagePosition + 1])): ?><a href="<?= $h($pageLink($pageKeys[$pagePosition + 1])) ?>"><?= $h($pages[$pageKeys[$pagePosition + 1]]) ?> →</a><?php endif; ?></span>
                    </nav>
                    <?php endif; ?>
                </article>
            <?php elseif (!$component): ?>
                <h1 class="docs-h1">Витрина UI</h1>
                <p class="docs-p">Добавьте компоненту файл demo.tpl (все варианты).</p>
            <?php else: ?>
                <article>
                    <p class="docs-eyebrow">
                        <?= $h(LayoutShowcase::SECTIONS[$component['section']]) ?>
                        <?= $component['group'] !== '' ? ' · ' . $h(LayoutShowcase::GROUP_TITLES[$component['group']] ?? $component['group']) : '' ?>
                    </p>
                    <h1 class="docs-h1">
                        <?= $h($component['name']) ?>
                        <code class="docs-h1__code"><?= $h($component['key']) ?></code>
                    </h1>
                    <div class="docs-chips">
                        <?php if ($component['inSite']): ?>
                            <span class="docs-chip docs-chip_site">App</span>
                        <?php endif; ?>
                        <?php if ($component['overrides']): ?>
                            <span class="docs-chip">переопределяет ядро</span>
                        <?php endif; ?>
                        <?php if (!$component['inCore']): ?>
                            <span class="docs-chip">нет в ядре</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($figmaDocs): ?>
                        <h2 id="figma" class="docs-h2" data-docs-section><a href="#figma">Описание из Figma</a></h2>
                        <?php foreach ($figmaDocs as $set): ?>
                            <section class="docs-figma">
                                <h3 class="docs-figma__title"><code class="docs-inline"><?= $h($set['set']) ?></code></h3>
                                <p class="docs-p"><?= $h($ucfirst($set['what'])) ?></p>
                                <dl class="docs-figma__list">
                                    <dt>Зачем</dt><dd><?= $h($ucfirst($set['why'])) ?></dd>
                                    <dt>Как работать</dt><dd><?= $h($ucfirst($set['how'])) ?></dd>
                                    <dt>Ограничения</dt><dd><?= $h($ucfirst($set['limits'])) ?></dd>
                                </dl>
                            </section>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <h2 id="variants" class="docs-h2" data-docs-section><a href="#variants">Все варианты</a></h2>
                    <p class="docs-p">demo.tpl: стили, размеры, состояния в светлой и тёмной темах
                        (<?= $component['demoSource'] === LayoutShowcase::SOURCE_SITE ? 'демо сайта' : 'демо ядра' ?>).</p>
                    <div class="docs-preview">
                        <?= $modeTabs() ?>
                        <div class="docs-panes"><?= $panes() ?></div>
                    </div>

                    <?php if ($component['overrides']): ?>
                        <h2 id="override" class="docs-h2" data-docs-section><a href="#override">Переопределение</a></h2>
                        <p class="docs-p">Компонент есть и в ядре, и в сайте: сайт заменяет файлы ядра с тем же именем.
                            Демо без своего файла в сайте берётся из ядра и рисует класс ядра.</p>
                        <table class="docs-table">
                            <thead><tr><th>Файл</th><th>Ядро</th><th>Сайт</th><th>Итог</th></tr></thead>
                            <tbody>
                            <?php foreach (LayoutShowcase::getFiles($key) as $file): ?>
                                <tr>
                                    <td><code class="docs-inline"><?= $h($file['name']) ?></code></td>
                                    <td><?= $file['core'] !== null ? 'есть' : '—' ?></td>
                                    <td><?= $file['site'] !== null ? 'есть' : '—' ?></td>
                                    <td><?= $h($statusTitles[$file['status']]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <nav class="docs-pager" aria-label="Соседние компоненты">
                        <span><?php if ($previous): ?><a href="<?= $h($link($previous['key'])) ?>">← <?= $h($previous['name']) ?></a><?php endif; ?></span>
                        <span><?php if ($next): ?><a href="<?= $h($link($next['key'])) ?>"><?= $h($next['name']) ?> →</a><?php endif; ?></span>
                    </nav>
                </article>
            <?php endif; ?>
        </main>
    </div>

    <aside class="docs-toc" aria-label="На странице">
        <?php if ($toc): ?>
            <div class="docs-toc__title">На странице</div>
            <ul class="docs-toc__list">
                <?php foreach ($toc as $id => $title): ?>
                    <li><a class="docs-toc__link" :class="{'docs-toc__link_active': active === '<?= $id ?>'}"
                           href="#<?= $id ?>"><?= $h($title) ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </aside>
</div>
