<?php

namespace App\Layout;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use Simflex\Core\Assets;
use Simflex\Core\Container;
use Simflex\Core\Profiler;
use tubalmartin\CssMin\Minifier;

/**
 * Менеджер макетов
 */
class LayoutManager
{
    /**
     * @var LayoutBase[] Все используемые макеты
     */
    protected static array $usedLayouts = [];

    /**
     * @var array Все используемые стили
     */
    protected static array $usedStyles = [];

    /**
     * @var array Все используемые скрипты
     */
    public static array $usedScripts = [];

    /**
     * @var bool Если true, отрисованные макеты собирают поля своих схем, см. ./sf layout/generate
     */
    public static bool $shouldGenerateFields = false;

    /**
     * @var array<string, list<array{name: string, label: string, class: string, default: string, params: array}>>
     *      Поля, собранные из схем макетов: название блока => поля
     */
    public static array $generatedFields = [];

    /**
     * Подключает макет
     * @param string $class Класс макета
     */
    public static function useLayout(string $class): void
    {
        static::$usedLayouts[$class] = $class;
    }

    /**
     * Подключает стиль
     * @param string $path Путь к css/scss
     */
    public static function useStyle(string $path): void
    {
        static::$usedStyles[] = $path;
    }

    /**
     * Подключает JS-скрипт
     * @param string $path Путь к js
     */
    public static function useScript(string $path): void
    {
        static::$usedScripts[] = $path;
    }

    /**
     * Инициализирует систему макетов
     * @return string Путь к сгенерированному CSS-файлу
     * @throws SassException
     */
    public static function init(): string
    {
        // ???
        if (SF_LOCATION == SF_LOCATION_ADMIN) {
            return '';
        }

        Profiler::traceStart(__CLASS__, __FUNCTION__);
        Container::getPage()::js(Assets::publish(SF_ROOT_PATH . '/Layout/assetsGlobal/script.js'));
        foreach (static::$usedLayouts as $class) {
            $class::initAssets();
        }

        static::$usedStyles = array_unique(static::$usedStyles);
        $config = Container::getConfig();

        // путь к кэшу.
        if (!$config->devMode) {
            $cachePath = '';
            foreach (static::$usedStyles as $style) {
                $cachePath .= md5($style);
            }

            $cachePath = '/cache/css/' . md5($cachePath) . '.css';
        } else {
            $cachePath = '/cache/css/style.css';
        }

        $imports = [SF_ROOT_PATH . '/Layout/assetsGlobal'];

        // проверяем, есть ли файл кэша.
        $compiler = new Compiler();
        if ($config->devMode || !is_file(SF_PUBLIC_PATH . $cachePath)) {
            // неявно добавляем корневой стиль.
            $implicitPath = SF_ROOT_PATH . '/Layout/assetsGlobal/style.scss';
            $implicit = '';
            if (is_file($implicitPath)) {
                $implicit = file_get_contents($implicitPath);
            }

            // кэшируем стили.
            $styleString = '';

            $compileString = '';
            if ($implicit) {
                $compileString .= $implicit;
            }

            foreach (static::$usedStyles as $style) {
                if (!is_file($style)) {
                    continue;
                }

                $cssData = file_get_contents($style);
                if (str_ends_with($style, '.scss')) {
                    $imports[] = dirname($style);
                    $compileString .= $cssData;
                } else {
                    $styleString .= $cssData;
                }
            }

            // компилируем то, что нужно
            $compiler->setImportPaths($imports);
            $compileString = $compiler->compileString($compileString)->getCss();
            $styleString = $compileString . $styleString;

            // минифицируем итоговый css.
            if (!$config->devMode) {
                $min = new Minifier();
                $styleString = $min->run($styleString);
            }

            // записываем новый стиль.
            if (!is_dir(dirname(SF_PUBLIC_PATH . $cachePath))) {
                mkdir(dirname(SF_PUBLIC_PATH . $cachePath), 0777, true);
            }
            file_put_contents(SF_PUBLIC_PATH . $cachePath, $styleString);
        }

        // используем стиль из кэша.
        Container::getPage()::css($cachePath . ($config->devMode ? ('?v=' . md5(microtime())) : ''));
        Profiler::traceEnd(__CLASS__, __FUNCTION__);
        return $cachePath;
    }
}
