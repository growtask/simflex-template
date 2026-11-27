<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\LayoutFoundations;
use Simflex\Core\System\ExtensionConfig;

/**
 * Правила стилей компонентов Layout (план дизайн-системы, этап 2): движение на токенах, prefers-reduced-motion,
 * цвета токенами, кольцо фокуса. Проверяются style.scss всех компонентов ядра и расширений (разделы витрины) без комментариев
 */
class LayoutStylesTest extends Unit
{
    /** Цвета числами: флаги и логотипы (нарисованы в Figma), маска чекбокса, обводка образцов цвета */
    private const COLOR_EXCEPTIONS = [
        'Parts/Brand/BrandMadeIn',
        'UI/Input/InputPhone',
        'UI/Switch/SwitchCheckbox',
        'UI/Switch/SwitchRadioColor',
    ];

    /** Наведение без своего фокуса: переопределяют цвета вложенной кнопки ButtonMain, кольцо — у неё */
    private const FOCUS_EXCEPTIONS = [
        'Common/Footer',
        'Common/HeaderBanner',
        'Common/Uploader',
    ];

    /**
     * @return array<string,string> компонент (путь под Components ядра или расширения) => стили без комментариев
     */
    private function styles(): array
    {
        $roots = [dirname((string)(new \ReflectionClass(LayoutFoundations::class))->getFileName()) . '/Components'];
        foreach (ExtensionConfig::getAll() as $extension) {
            if ($extension->getLayoutSections() !== [] && $extension->getLayoutRoot() !== null) {
                $roots[] = $extension->getLayoutRoot();
            }
        }
        $styles = [];
        foreach ($roots as $root) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root)) as $file) {
                if ($file->getFilename() !== 'style.scss') {
                    continue;
                }
                $css = (string)file_get_contents($file->getPathname());
                $css = (string)preg_replace(['~/\*.*?\*/~s', '~(?<!:)//[^\n]*~'], '', $css);
                $key = str_replace('\\', '/', substr($file->getPath(), strlen($root) + 1));
                $styles[$key] = $css;
            }
        }
        ksort($styles);
        $this->assertNotEmpty($styles);
        return $styles;
    }

    public function testTransitionDurationsAreMotionTokens(): void
    {
        $bad = [];
        foreach ($this->styles() as $component => $css) {
            // transition: свойство длительность …, свойство длительность …
            preg_match_all('/(?<![\w-])transition\s*:\s*([^;{}]+);/', $css, $matches);
            foreach ($matches[1] as $value) {
                foreach (preg_split('/,(?![^(]*\))/', $value) as $part) {
                    $words = preg_split('/\s+/', trim($part), 3);
                    $time = $words[1] ?? '';
                    if ($time !== '' && preg_match('/^\d/', $time) && !preg_match('/^0m?s$/', $time)) {
                        $bad[] = "$component: transition: $part";
                    }
                }
            }
            preg_match_all('/@include transition\(([^;]*)\);/', $css, $matches);
            foreach ($matches[1] as $args) {
                $rest = trim((string)preg_replace('/^\([^)]*\)|^[\w-]+/', '', trim($args)), ', ');
                if ($rest !== '' && !str_starts_with($rest, 'var(')) {
                    $bad[] = "$component: @include transition($args)";
                }
            }
            if (preg_match_all('/transition-duration\s*:\s*\d[^;]*;/', $css, $matches)) {
                foreach ($matches[0] as $rule) {
                    $bad[] = "$component: $rule";
                }
            }
        }
        $this->assertSame([], $bad, 'Длительности переходов — токены --motion-* (при prefers-reduced-motion они 1 мс)');
    }

    public function testAnimationsRespectReducedMotion(): void
    {
        $bad = [];
        foreach ($this->styles() as $component => $css) {
            preg_match_all('/(?<![\w-])animation\s*:\s*([^;{}]+);/', $css, $matches);
            foreach ($matches[1] as $value) {
                // длительность числом (бесконечные петли, свои скорости) — нужен свой блок prefers-reduced-motion
                if ($value !== 'none' && preg_match('/(^|\s)\d*\.?\d+m?s\b/', $value)
                    && !str_contains($css, 'prefers-reduced-motion')) {
                    $bad[] = "$component: animation: $value";
                }
            }
        }
        $this->assertSame([], $bad, 'Анимации с длительностью числом выключаются при prefers-reduced-motion');
    }

    public function testColorsAreTokens(): void
    {
        $bad = [];
        foreach ($this->styles() as $component => $css) {
            if (in_array($component, self::COLOR_EXCEPTIONS, true)) {
                continue;
            }
            if (preg_match('/#[0-9a-fA-F]{3,8}\b(?![\w-])|(?<![\w-])rgba?\(/', $css, $match)) {
                $bad[] = "$component: {$match[0]}";
            }
        }
        $this->assertSame([], $bad, 'Цвета и тени — токены --color-*, --shadow-* (исключения — в COLOR_EXCEPTIONS)');
    }

    public function testInteractiveStylesHaveFocus(): void
    {
        $bad = [];
        foreach ($this->styles() as $component => $css) {
            if (preg_match('/outline\s*:\s*(none|0)\b/', $css) && !str_contains($css, 'focus-visible')) {
                $bad[] = "$component: outline: none без :focus-visible";
            }
            if (in_array($component, self::FOCUS_EXCEPTIONS, true)) {
                continue;
            }
            if (preg_match('/:hover|cursor\s*:\s*pointer/', $css) && !str_contains($css, 'focus')) {
                $bad[] = "$component: наведение без фокуса";
            }
        }
        $this->assertSame([], $bad, 'У интерактивных элементов есть фокус с клавиатуры (@include focus-ring в :focus-visible)');
    }

    public function testMotionTokensAndReducedMotion(): void
    {
        $motion = (string)file_get_contents(dirname((string)(new \ReflectionClass(LayoutFoundations::class))->getFileName())
            . '/assetsGlobal/styles/abstract/_motion.scss');
        foreach (['--motion-fast: 120ms', '--motion-base: 180ms', '--motion-slow: 240ms', '--ease-out', '--ease-in-out',
                     '--ease-in', '--ease-spring'] as $token) {
            $this->assertStringContainsString($token, $motion);
        }
        $this->assertMatchesRegularExpression('/prefers-reduced-motion: reduce\)\s*\{\s*:root\s*\{\s*--motion-fast: 1ms/', $motion);
    }
}
