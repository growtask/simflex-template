<?php

namespace App\Layout;

use ScssPhp\ScssPhp\Compiler;
use Simflex\Core\Container;
use Simflex\Core\Profiler;
use tubalmartin\CssMin\Minifier;

class LayoutManager
{
    /**
     * @var LayoutBase[]
     */
    protected static $usedLayouts = [];
    protected static $usedStyles = [];

    public static $usedScripts = [];

    // if true, will update all acp fields
    public static $shouldGenerateFields = false;

    // internal use only, ordering template fields
    public static $templateOrdering = [];

    public static function useLayout(string $class)
    {
        static::$usedLayouts[$class] = $class;
    }

    public static function useStyle(string $path)
    {
        static::$usedStyles[] = $path;
    }

    public static function useScript(string $path)
    {
        static::$usedScripts[] = $path;
    }

    public static function init()
    {
        // ???
        if (SF_LOCATION == SF_LOCATION_ADMIN) {
            return;
        }

        Profiler::traceStart(__CLASS__, __FUNCTION__);
        Container::getPage()::js('/Layout/assetsGlobal/script.js');
        foreach (static::$usedLayouts as $class) {
            $class::initAssets();
        }

        static::$usedStyles = array_unique(static::$usedStyles);

        // compose cache path.
        if (!\Config::$devMode) {
            $cachePath = '';
            foreach (static::$usedStyles as $style) {
                $cachePath .= md5($style);
            }

            $cachePath = '/cache/css/' . md5($cachePath) . '.css';
        } else {
            $cachePath = '/cache/css/style.'.md5(microtime()).'.css';
        }

        $imports = [SF_ROOT_PATH . '/Layout/assetsGlobal'];

        // check if cache file exists.
        $compiler = new Compiler();
        if (\Config::$devMode || !is_file(SF_ROOT_PATH . $cachePath)) {
            // force add root style implicitly.
            $implicitPath = SF_ROOT_PATH . '/Layout/assetsGlobal/style.scss'; $implicit = '';
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
            if (!\Config::$devMode) {
                $min = new Minifier();
                $styleString = $min->run($styleString);
            }

            // write new style.
            file_put_contents(SF_ROOT_PATH . $cachePath, $styleString);
        }

        // use cached style.
        Container::getPage()::css($cachePath);
        Profiler::traceEnd(__CLASS__, __FUNCTION__);
        return $cachePath;
    }
}