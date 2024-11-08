<?php

namespace App\Layout;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use Simflex\Core\Container;
use Simflex\Core\Profiler;
use tubalmartin\CssMin\Minifier;

/**
 * Layout manager
 */
class LayoutManager
{
    /**
     * @var LayoutBase[] All used layouts
     */
    protected static array $usedLayouts = [];

    /**
     * @var array All used styles
     */
    protected static array $usedStyles = [];

    /**
     * @var array All used scripts
     */
    public static array $usedScripts = [];

    /**
     * @var bool If set to true, will dry run to update ACP fields
     */
    public static bool $shouldGenerateFields = false;

    /**
     * @var array Used internally for template ordering
     */
    public static array $templateOrdering = [];

    /**
     * Use layout
     * @param string $class Layout class
     * @return void
     */
    public static function useLayout(string $class): void
    {
        static::$usedLayouts[$class] = $class;
    }

    /**
     * Use style
     * @param string $path Path to css/scss
     * @return void
     */
    public static function useStyle(string $path): void
    {
        static::$usedStyles[] = $path;
    }

    /**
     * Use JS script
     * @param string $path Path to js
     * @return void
     */
    public static function useScript(string $path): void
    {
        static::$usedScripts[] = $path;
    }

    /**
     * Initializes layout system
     * @return string Path to generated CSS file
     * @throws SassException
     */
    public static function init(): string
    {
        // ???
        if (SF_LOCATION == SF_LOCATION_ADMIN) {
            return '';
        }

        Profiler::traceStart(__CLASS__, __FUNCTION__);
        Container::getPage()::js('/Layout/assetsGlobal/script.js');
        foreach (static::$usedLayouts as $class) {
            $class::initAssets();
        }

        static::$usedStyles = array_unique(static::$usedStyles);
        $config = Container::getConfig();

        // compose cache path.
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

        // check if cache file exists.
        $compiler = new Compiler();
        if ($config->devMode || !is_file(SF_ROOT_PATH . $cachePath)) {
            // force add root style implicitly.
            $implicitPath = SF_ROOT_PATH . '/Layout/assetsGlobal/style.scss';
            $implicit = '';
            if (is_file($implicitPath)) {
                $implicit = file_get_contents($implicitPath);
            }

            // cache styles.
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

            // compile what should be compiled
            $compiler->setImportPaths($imports);
            $compileString = $compiler->compileString($compileString)->getCss();
            $styleString = $compileString . $styleString;

            // minify resulting css.
            if (!$config->devMode) {
                $min = new Minifier();
                $styleString = $min->run($styleString);
            }

            // write new style.
            file_put_contents(SF_ROOT_PATH . $cachePath, $styleString);
        }

        // use cached style.
        Container::getPage()::css($cachePath . ($config->devMode ? ('?v=' . md5(microtime())) : ''));
        Profiler::traceEnd(__CLASS__, __FUNCTION__);
        return $cachePath;
    }
}