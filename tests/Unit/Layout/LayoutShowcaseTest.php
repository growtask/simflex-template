<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\LayoutFoundations;
use Simflex\Core\Layout\LayoutManager;
use Simflex\Core\Layout\LayoutShowcase;
use Simflex\Core\System\Container;
use Simflex\Core\System\ExtensionConfig;
use Simflex\Core\System\Location;

/**
 * Витрина компонентов Layout: поиск компонентов с demo.tpl в ядре, расширениях и сайте, вывод в темах,
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
        ExtensionConfig::reset();
    }

    /**
     * Класс Layout компонента: namespace из его Layout.php (ядро, расширение или сайт)
     */
    private function layoutClass(array $component): string
    {
        $source = (string)file_get_contents($component['root'] . '/' . $component['key'] . '/Layout.php');
        preg_match('/^namespace ([\w\\\\]+);/m', $source, $match);

        return $match[1] . '\\Layout';
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
        // без набора Figma: Placeholder* (не из Figma) и UI/Other
        $this->assertSame('Parts/Placeholder/PlaceholderAvatar', $keys[count($figmaKeys)]);
        $this->assertContains('UI/Other/Separator', $keys);
        $this->assertSame('Other', $components['UI/Other/Separator']['group']);
        // раздел фильтрует список
        $this->assertSame(
            array_filter($components, static fn(array $component): bool => $component['section'] === 'UI'),
            LayoutShowcase::getComponents('UI')
        );
        // раздел Parts: блок States (Figma), без группы
        $states = $components['Parts/States'];
        $this->assertSame('Parts', $states['section']);
        $this->assertSame('', $states['group']);
        // блок System лежит в группе System
        $this->assertSame('System', $components['Parts/System/SystemCookie']['group']);
        $this->assertSame(
            [
                'Parts/States', 'Parts/System/SystemCookie', 'Parts/System/SystemConsent', 'Parts/System/SystemAgeGate',
                'Parts/System/SystemBrowserSupport', 'Parts/System/SystemPreloader', 'Parts/System/SystemMaintenance',
                'Parts/Brand/BrandLogoAnim', 'Parts/Brand/BrandLogo', 'Parts/Brand/BrandMadeIn', 'Parts/Brand/BrandMadeBy',
                'Parts/Payment/PaymentLogo', 'Parts/Payment/PaymentList', 'Parts/Delivery/DeliveryLogo', 'Parts/Delivery/DeliveryList', 'Parts/Placeholder/PlaceholderAvatar', 'Parts/Placeholder/PlaceholderImg',
            ],
            array_keys(LayoutShowcase::getComponents('Parts'))
        );
        // раздел Base (страница Base в Figma): Seo без группы, Nav в группе
        $this->assertSame('Base', $components['Base/Seo']['section']);
        $this->assertSame('', $components['Base/Seo']['group']);
        $this->assertSame('Nav', $components['Base/Nav/NavMenu']['group']);
        $this->assertSame(
            [
                'Base/Seo', 'Base/Nav/NavBreadcrumbs', 'Base/Nav/NavPagination', 'Base/Nav/NavMenu', 'Base/Nav/NavMenuList',
                'Base/Item/ItemBenefit', 'Base/Slider/SliderMain', 'Base/Slider/SliderStep', 'Base/Slider/SliderStepItem',
                'Base/Form/FormCallback', 'Base/Form/FormMailing', 'Base/Form/FormResult', 'Base/Modal/ModalMain',
                'Base/Geo/GeoModal', 'Base/Geo/GeoMain', 'Base/Geo/GeoList', 'Base/Player/PlayerAudio', 'Base/Player/PlayerWave',
            ],
            array_keys(LayoutShowcase::getComponents('Base'))
        );
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
        foreach (LayoutShowcase::getComponents() as $key => $component) {
            $demo = (string)file_get_contents($component['demo']);
            foreach (glob($component['root'] . '/' . $key . '/*.php') ?: [] as $file) {
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
     * иначе стили не попадут на страницу и компонент станет невидимым, как было у LoaderSkeleton::drawLoaderSkeleton()
     */
    public function testEveryComponentDemoRegistersItsAssets(): void
    {
        $checked = 0;
        $backup = $this->resetLayoutManager();
        try {
            foreach (LayoutShowcase::getComponents() as $key => $component) {
                $root = $component['root'] . '/';
                if (!is_file($root . $key . '/style.scss') || !is_file($root . $key . '/Layout.php')) {
                    continue;
                }
                $this->resetLayoutManager();
                LayoutShowcase::renderDemo($key, 'light');
                $class = $this->layoutClass($component);
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
        $checked = 0;
        $backup = $this->resetLayoutManager();
        try {
            foreach (LayoutShowcase::getComponents() as $key => $component) {
                $root = $component['root'] . '/';
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

    /**
     * Расширение со своим разделом: компоненты из src/Layout/Components пакета, описания Figma пакета,
     * переопределение сайтом; раздел ядра расширение не перекрывает
     */
    public function testExtensionSectionComponents(): void
    {
        $root = sys_get_temp_dir() . '/sf-showcase-ext-' . getmypid();
        $files = [
            $root . '/src/Layout/Components/Shop/Card/CardShop/demo.tpl' => '<p>карточка магазина <?= $theme ?></p>',
            $root . '/src/Layout/Components/Shop/Card/CardShop/Layout.php' => '<?php',
            $root . '/src/Layout/Components/UI/Badge/demo.tpl' => '<p>чужой бейдж</p>',
            $root . '/src/Layout/figma-docs.php' => "<?php return ['Shop/Card/CardShop' => [['Cards/shop', 'карточка', 'зачем', 'как', '—']]];",
        ];
        foreach ($files as $file => $content) {
            if (!is_dir(dirname($file))) {
                mkdir(dirname($file), 0777, true);
            }
            file_put_contents($file, $content);
        }
        $extension = new class ($root . '/src/Layout/Components') extends ExtensionConfig {
            public string $name = 'shop';
            public int $loadOrder = 10;
            protected array $layoutSections = ['Shop' => 'Магазин', 'UI' => 'Чужой UI'];

            public function __construct(private readonly string $root)
            {
            }

            public function getLayoutRoot(): ?string
            {
                return $this->root;
            }
        };
        ExtensionConfig::reset([$extension]);
        try {
            $this->assertSame('Магазин', LayoutShowcase::getSections()['Shop']);
            $this->assertSame('UI-компоненты', LayoutShowcase::getSections()['UI']);
            $this->assertSame('Shop', array_key_last(LayoutShowcase::getSections()), 'разделы расширений — после ядра');

            $card = LayoutShowcase::getComponent('Shop/Card/CardShop');
            $this->assertNotNull($card);
            $this->assertSame('shop', $card['package']);
            $this->assertSame('Card', $card['group']);
            $this->assertTrue($card['inCore']);
            $this->assertSame(LayoutShowcase::SOURCE_CORE, $card['demoSource']);
            $this->assertStringContainsString('карточка магазина dark', LayoutShowcase::renderDemo('Shop/Card/CardShop', 'dark'));
            $this->assertSame('Cards/shop', LayoutShowcase::getFigmaDocs('Shop/Card/CardShop')[0]['set']);
            $this->assertSame('core', LayoutShowcase::getFiles('Shop/Card/CardShop')['Layout.php']['status']);
            // раздел UI остаётся у ядра
            $this->assertSame(LayoutShowcase::PACKAGE_CORE, LayoutShowcase::getComponent('UI/Badge')['package']);
            $this->assertStringNotContainsString('чужой бейдж', LayoutShowcase::renderDemo('UI/Badge', 'light'));

            // сайт переопределяет компонент расширения
            $this->createSiteFile('Shop/Card/CardShop/demo.tpl', '<p>карточка сайта</p>');
            $card = LayoutShowcase::getComponent('Shop/Card/CardShop');
            $this->assertTrue($card['overrides']);
            $this->assertStringContainsString('карточка сайта', LayoutShowcase::renderDemo('Shop/Card/CardShop', 'light'));
            $this->assertSame('replaced', LayoutShowcase::getFiles('Shop/Card/CardShop')['demo.tpl']['status']);
        } finally {
            ExtensionConfig::reset();
            foreach (array_keys($files) as $file) {
                @unlink($file);
            }
            foreach (['/src/Layout/Components/Shop/Card/CardShop', '/src/Layout/Components/Shop/Card', '/src/Layout/Components/Shop',
                         '/src/Layout/Components/UI/Badge', '/src/Layout/Components/UI', '/src/Layout/Components', '/src/Layout', '/src', ''] as $dir) {
                @rmdir($root . $dir);
            }
        }
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
            $exception = str_contains($file->getPathname(), 'Parts' . DIRECTORY_SEPARATOR . 'Brand')
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
