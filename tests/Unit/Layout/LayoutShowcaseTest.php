<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\LayoutFoundations;
use Simflex\Core\Layout\LayoutManager;
use Simflex\Core\Layout\LayoutShowcase;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Витрина компонентов Layout: поиск компонентов с demo.tpl в ядре и сайте, вывод в темах,
 * переопределение компонентов сайтом
 */
class LayoutShowcaseTest extends Unit
{
    private mixed $configBackup;

    /** @var list<string> Созданные тестом файлы и папки сайта */
    private array $created = [];

    protected function _before(): void
    {
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;
        Container::set('config', (object)['theme' => 'default', 'devMode' => true]);
        Container::set('location', Location::Site);
    }

    protected function _after(): void
    {
        foreach (array_reverse($this->created) as $path) {
            is_dir($path) ? @rmdir($path) : @unlink($path);
        }
        $this->created = [];
        Container::set('config', $this->configBackup);
    }

    /**
     * Создаёт файл в Layout/Components сайта (и недостающие папки); убирается в _after()
     */
    private function createSiteFile(string $relative, string $content): void
    {
        $file = Container::getPaths()->getRoot('Layout/Components/' . $relative);
        $directory = dirname($file);
        $missing = [];
        for ($path = $directory; !is_dir($path); $path = dirname($path)) {
            $missing[] = $path;
        }
        foreach (array_reverse($missing) as $path) {
            mkdir($path);
            $this->created[] = $path;
        }
        file_put_contents($file, $content);
        $this->created[] = $file;
    }

    public function testFindsCoreComponentsWithGroupAndName(): void
    {
        $components = LayoutShowcase::getComponents();

        $badge = $components['UI/Badge'];
        $this->assertSame('Badge', $badge['name']);
        $this->assertSame('UI', $badge['section']);
        $this->assertSame('', $badge['group']);
        $this->assertTrue($badge['inCore']);
        $this->assertFalse($badge['inSite']);
        $this->assertFalse($badge['overrides']);
        $this->assertSame(LayoutShowcase::SOURCE_CORE, $badge['demoSource']);
        // компонент во вложенной папке
        $this->assertSame('Button', $components['UI/Button/ButtonMain']['group']);
        foreach ($components as $component) {
            $this->assertFileExists($component['demo']);
        }
        // порядок страницы UI в Figma (figma-docs.php), компоненты без набора Figma (UI/Other) - в конце
        $keys = array_keys($components);
        $figmaKeys = array_keys(LayoutShowcase::getAllFigmaDocs());
        $this->assertSame(array_values(array_intersect($figmaKeys, $keys)), array_slice($keys, 0, count($figmaKeys)));
        $this->assertSame('UI/Other/BadgeStock', $keys[count($figmaKeys)]);
        $this->assertSame('Other', $components['UI/Other/Logo']['group']);
        // раздел фильтрует список
        $this->assertSame(
            array_filter($components, static fn(array $component): bool => $component['section'] === 'UI'),
            LayoutShowcase::getComponents('UI')
        );
        // раздел Parts: блок States (Figma), без группы
        $states = $components['Parts/States'];
        $this->assertSame('Parts', $states['section']);
        $this->assertSame('', $states['group']);
        $this->assertSame(['Parts/States'], array_keys(LayoutShowcase::getComponents('Parts')));
        $this->assertSame([], LayoutShowcase::getComponents('Nope'));
    }

    public function testRendersEveryDemoInLightAndDarkTheme(): void
    {
        foreach (array_keys(LayoutShowcase::getComponents()) as $key) {
            foreach (array_keys(LayoutShowcase::THEMES) as $theme) {
                $html = LayoutShowcase::renderDemo($key, $theme);
                // тосты не зависят от темы: демо выводит их только в светлой
                if ($key === 'UI/Feedback/FeedbackToast' && $theme === 'dark') {
                    $this->assertSame('', trim($html));
                    continue;
                }
                $this->assertNotSame('', trim($html), "$key, $theme: демо пустое");
                $this->assertStringNotContainsString('ui-showcase__error', $html, "$key, $theme: демо упало");
            }
        }
    }

    public function testDemoCoversEveryEnumOfComponent(): void
    {
        $root = dirname((string)(new \ReflectionClass(LayoutShowcase::class))->getFileName()) . '/Components/';
        foreach (LayoutShowcase::getComponents() as $key => $component) {
            $demo = (string)file_get_contents($component['demo']);
            foreach (glob($root . $key . '/*.php') ?: [] as $file) {
                $name = basename($file, '.php');
                $source = (string)file_get_contents($file);
                // темы задаёт тема витрины; остальные enum демо перебирает или называет все значения
                if ($name === 'Layout' || !str_contains($source, 'enum ') || str_ends_with($name, 'Theme')) {
                    continue;
                }
                preg_match_all('/^\s*case (\w+)/m', $source, $cases);
                if (str_contains($demo, $name . '::cases()')) {
                    continue;
                }
                foreach ($cases[1] as $case) {
                    $this->assertStringContainsString("$name::$case", $demo, "$key: демо не показывает $name::$case");
                }
            }
        }
    }

    /**
     * Сбрасывает подключённые макеты и стили LayoutManager (защищённые свойства), возвращает прежние значения
     *
     * @return array{0: array, 1: array}
     */
    private function resetLayoutManager(): array
    {
        $backup = [];
        foreach (['usedLayouts', 'usedStyles'] as $name) {
            $property = new \ReflectionProperty(LayoutManager::class, $name);
            $backup[] = $property->getValue();
            $property->setValue(null, []);
        }

        return $backup;
    }

    private function usedLayouts(): array
    {
        return array_values((new \ReflectionProperty(LayoutManager::class, 'usedLayouts'))->getValue());
    }

    /**
     * Демо компонента со style.scss подключает ассеты своего Layout (draw* вызывает draw() или useLayout()):
     * иначе стили не попадут на страницу и компонент станет невидимым, как было у LoaderSkeleton::drawSkeleton()
     */
    public function testEveryComponentDemoRegistersItsAssets(): void
    {
        $root = dirname((string)(new \ReflectionClass(LayoutShowcase::class))->getFileName()) . '/Components/';
        $checked = 0;
        $backup = $this->resetLayoutManager();
        try {
            foreach (array_keys(LayoutShowcase::getComponents()) as $key) {
                if (!is_file($root . $key . '/style.scss') || !is_file($root . $key . '/Layout.php')) {
                    continue;
                }
                $this->resetLayoutManager();
                LayoutShowcase::renderDemo($key, 'light');
                $class = 'Simflex\\Core\\Layout\\Components\\' . str_replace('/', '\\', $key) . '\\Layout';
                $this->assertContains($class, $this->usedLayouts(), "$key: демо не подключило ассеты (draw*() не вызывает draw())");
                $checked++;
            }
        } finally {
            (new \ReflectionProperty(LayoutManager::class, 'usedLayouts'))->setValue(null, $backup[0]);
            (new \ReflectionProperty(LayoutManager::class, 'usedStyles'))->setValue(null, $backup[1]);
        }
        $this->assertGreaterThan(30, $checked);
    }

    /**
     * Собранный CSS компонента содержит правила его корневого класса (первый класс style.scss, который есть в демо; сам класс или его элементы и модификаторы)
     */
    public function testCompiledCssHasRulesOfRootClass(): void
    {
        // без devMode: профайлеру не нужна панель отладки, CSS пишется в файл по хэшу стилей (тест его удаляет)
        Container::set('config', (object)['theme' => 'default', 'devMode' => false]);
        $root = dirname((string)(new \ReflectionClass(LayoutShowcase::class))->getFileName()) . '/Components/';
        $checked = 0;
        $backup = $this->resetLayoutManager();
        try {
            foreach (array_keys(LayoutShowcase::getComponents()) as $key) {
                if (!is_file($root . $key . '/style.scss')) {
                    continue;
                }
                $source = (string)file_get_contents($root . $key . '/style.scss');
                if (!preg_match('/^\.([\w-]+)/m', $source, $match)) {
                    continue;
                }
                $this->resetLayoutManager();
                $html = LayoutShowcase::renderDemo($key, 'light');
                if (!preg_match('/class="[^"]*(?<![\w-])' . preg_quote($match[1], '/') . '(?![\w-])/', $html)) {
                    continue;
                }
                $file = Container::getPaths()->getPublic() . LayoutManager::init();
                $css = (string)file_get_contents($file);
                unlink($file);
                $this->assertMatchesRegularExpression(
                    '/\.' . preg_quote($match[1], '/') . '(?![a-zA-Z0-9])/',
                    $css,
                    "$key: в собранном CSS нет правил для .{$match[1]} (ассеты не подключены)"
                );
                $checked++;
            }
        } finally {
            (new \ReflectionProperty(LayoutManager::class, 'usedLayouts'))->setValue(null, $backup[0]);
            (new \ReflectionProperty(LayoutManager::class, 'usedStyles'))->setValue(null, $backup[1]);
        }
        $this->assertGreaterThan(20, $checked);
    }

    public function testDemoShowsComponentInThemeOfShowcase(): void
    {
        $this->assertStringContainsString('btn-theme_dark', LayoutShowcase::renderDemo('UI/Button/ButtonMain', 'dark'));
        $this->assertStringContainsString('btn-theme_light', LayoutShowcase::renderDemo('UI/Button/ButtonMain', 'light'));
    }

    public function testUnknownComponentOrThemeGivesEmptyString(): void
    {
        $this->assertSame('', LayoutShowcase::renderDemo('UI/Nope', 'light'));
        $this->assertSame('', LayoutShowcase::renderDemo('UI/Badge', 'sepia'));
        // путь выше каталога компонентов не обходит список
        $this->assertSame('', LayoutShowcase::renderDemo('../../../composer', 'light'));
        $this->assertNull(LayoutShowcase::getComponent('../../../composer'));
    }

    public function testSiteComponentOverridesCoreFiles(): void
    {
        $this->createSiteFile('UI/Badge/demo.tpl', '<p>демо сайта <?= $theme ?></p>');

        $components = LayoutShowcase::getComponents();
        $badge = $components['UI/Badge'];

        $this->assertTrue($badge['inCore']);
        $this->assertTrue($badge['inSite']);
        $this->assertTrue($badge['overrides']);
        $this->assertSame(LayoutShowcase::SOURCE_SITE, $badge['demoSource']);
        $this->assertStringContainsString('демо сайта dark', LayoutShowcase::renderDemo('UI/Badge', 'dark'));
        // остальные компоненты ядра не затронуты
        $this->assertFalse($components['UI/Button/ButtonMain']['overrides']);

        $files = LayoutShowcase::getFiles('UI/Badge');
        $this->assertSame('replaced', $files['demo.tpl']['status']);
        $this->assertSame('core', $files['Layout.php']['status']);
        $this->assertNotNull($files['demo.tpl']['core']);
        $this->assertNotNull($files['demo.tpl']['site']);
    }

    public function testSiteOnlyComponentAndSiteFiles(): void
    {
        $this->createSiteFile('UI/MyCard/demo.tpl', '<p>карточка</p>');

        $card = LayoutShowcase::getComponent('UI/MyCard');

        $this->assertNotNull($card);
        $this->assertFalse($card['inCore']);
        $this->assertTrue($card['inSite']);
        $this->assertFalse($card['overrides']);
        $this->assertStringContainsString('карточка', LayoutShowcase::renderDemo('UI/MyCard', 'light'));
        $this->assertSame('site', LayoutShowcase::getFiles('UI/MyCard')['demo.tpl']['status']);
    }

    public function testBrokenDemoShowsErrorInsteadOfFailing(): void
    {
        $this->createSiteFile('UI/Badge/demo.tpl', '<?php throw new \RuntimeException("нет параметра"); ?>');
        $level = ob_get_level();

        $html = LayoutShowcase::renderDemo('UI/Badge', 'light');

        $this->assertStringContainsString('ui-showcase__error', $html);
        $this->assertStringContainsString('нет параметра', $html);
        $this->assertStringContainsString('demo.tpl', $html);
        $this->assertSame($level, ob_get_level());
    }

    public function testComponentWithoutDemoIsNotListed(): void
    {
        // Layout.php без demo.tpl: компонента нет в списке
        $this->createSiteFile('UI/NoDemo/Layout.php', '<?php');

        $this->assertNull(LayoutShowcase::getComponent('UI/NoDemo'));
    }

    public function testFigmaDocsBelongToExistingComponents(): void
    {
        $components = LayoutShowcase::getComponents();
        $all = LayoutShowcase::getAllFigmaDocs();

        $this->assertNotEmpty($all);
        foreach ($all as $key => $sets) {
            $this->assertArrayHasKey($key, $components, "Описание Figma для несуществующего компонента $key");
            foreach ($sets as $set) {
                $this->assertNotSame('', $set['why'], $set['set']);
                $this->assertNotSame('', $set['how'], $set['set']);
            }
        }
        $this->assertSame('Buttons/main', LayoutShowcase::getFigmaDocs('UI/Button/ButtonMain')[0]['set']);
        $this->assertSame([], LayoutShowcase::getFigmaDocs('UI/Nope'));
    }

    public function testFoundationsReadTokensFromScss(): void
    {
        $colors = LayoutFoundations::getColors();
        $this->assertSame('#0D0D0D', $colors['Основные: серые']['--color-main-gray-black']);
        $this->assertSame('8px', LayoutFoundations::getRadius()['--radius-ui-small']);
        $this->assertArrayHasKey('--shadow-gray-s', LayoutFoundations::getShadows());
        $this->assertContains('fadeIn', LayoutFoundations::getAnimations());
        $names = array_column(LayoutFoundations::getTypography(), 'name');
        $this->assertContains('h1', $names);
        $this->assertContains('ui-m-light', $names);
    }

    public function testRadiusScaleMatchesFigmaModes(): void
    {
        $scale = array_column(LayoutFoundations::getRadiusScale(), 'modes', 'variable');
        $this->assertCount(6, $scale);
        $this->assertSame(['none', 's', 'm', 'l', 'full'], array_keys($scale['--radius-ui-medium']));
        $this->assertSame(['0', '2px', '4px', '6px', '9999px'], array_values($scale['--radius-ui-xsmall']));
        $this->assertSame(['0', '12px', '16px', '20px', '24px'], array_values($scale['--radius-ui-large']));
        $this->assertSame(['0', '28px', '32px', '36px', '40px'], array_values($scale['--radius-blocks']));
        $this->assertArrayNotHasKey('--radius-pill', $scale);
    }

    public function testIconGroupsFollowFigmaIconsPage(): void
    {
        $sections = array_column(LayoutFoundations::getIconGroups(), null, 'id');
        $this->assertArrayHasKey('action', $sections);
        $this->assertArrayHasKey('action-slim', $sections);
        $this->assertTrue($sections['action-slim']['slim']);
        $this->assertFalse($sections['action']['slim']);
        // подгруппы раздела — рамки внутри раздела в Figma
        $this->assertSame(['rotate', 'save'], array_column($sections['action']['groups'], 'title'));
        $save = array_column($sections['action']['groups'], 'icons', 'title')['save'];
        $this->assertSame(['save-plus', 'save-pen', 'save-off', 'save-check', 'save-all', 'save'], $save);
        $this->assertContains('check', $sections['action']['icons']);
        $this->assertContains('arrow-line-from-down', array_column($sections['direction']['groups'], 'icons', 'title')['arrow-line']);
        $this->assertSame(['social', 'social-monochrome'], array_column($sections['social']['groups'], 'title'));
        // все иконки спрайта попали в группы, без потерь и дублей
        $grouped = [];
        foreach ($sections as $section) {
            $grouped = array_merge($grouped, $section['icons'], ...array_column($section['groups'], 'icons'));
        }
        $this->assertSame(count($grouped), count(array_unique($grouped)));
        $this->assertEqualsCanonicalizing(LayoutFoundations::getIcons(), $grouped);
    }

    public function testComponentsUseRadiusVariables(): void
    {
        $root = dirname((string)(new \ReflectionClass(LayoutFoundations::class))->getFileName()) . '/Components';
        $hard = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root)) as $file) {
            // исключения: радиусы, нарисованные в иконке Figma или вне шкалы (логотип, чекбокс размера m)
            $exception = str_contains($file->getPathname(), 'Other' . DIRECTORY_SEPARATOR . 'Logo')
                || str_contains($file->getPathname(), 'Switch' . DIRECTORY_SEPARATOR . 'SwitchCheckbox');
            if ($file->getExtension() !== 'scss' || $exception) {
                continue;
            }
            if (preg_match('/border-r\(|border-radius:\s*\d*[1-9]\d*px/', (string)file_get_contents($file->getPathname()))) {
                $hard[] = substr($file->getPathname(), strlen($root) + 1);
            }
        }
        $this->assertSame([], $hard, 'Скругления компонентов задаются переменными --radius-*');
    }
}
