<?php

namespace App\Core;

use App\Extensions\Catalog\Price\PriceManager;
use App\Extensions\Catalog\Price\SaleProxy;
use Config;
use Dotenv;
use Simflex\Core\Container;

class Init
{

    /**
     * @return \Simflex\Core\Core
     */
    public static function _()
    {
        // make sure session is closed after execution finishes.
        register_shutdown_function(function () {
            if (session_status() == PHP_SESSION_ACTIVE) {
                session_write_close();
            }
        });

        // top kek
        $shit = explode('.', $_SERVER['REQUEST_URI']);
        if (count($shit) > 1 && strlen(
                $shit[count($shit) - 1]
            ) < 5 && !isset($_REQUEST['filter']) && SF_LOCATION != SF_LOCATION_CLI) {
            http_response_code(404);
            exit;
        }

        if (SF_LOCATION == SF_LOCATION_SITE && $_SERVER['REQUEST_METHOD'] == 'GET') {
            $qs = strpos($_SERVER['REQUEST_URI'], '?');
            if ($qs !== false && $qs == strlen($_SERVER['REQUEST_URI']) - 1 && !$_GET) {
                header('Location: ' . explode('?', $_SERVER['REQUEST_URI'])[0]);
                exit;
            }
        }

        static::setTimezone();
        require_once SF_ROOT_PATH . '/vendor/autoload.php';
        static::loadEnv();
        require_once SF_ROOT_PATH . '/functions.php';
        static::setSessionParams();
        static::startDebug();

        // never start session for website, as it's using lazy load now
        if (SF_LOCATION != SF_LOCATION_SITE) {
            static::startSession();
        }

        static::setCharset();
        static::loadConfig();

        if (SF_LOCATION_ADMIN == SF_LOCATION) {
            \Simflex\Core\Container::set('page', new \Simflex\Admin\Page());
            \Simflex\Core\Container::set('core', \Simflex\Admin\Core::class);
        } elseif (SF_LOCATION_API == SF_LOCATION || SF_LOCATION_SITE == SF_LOCATION) {
            \Simflex\Core\Container::set('page', new \Simflex\Core\Page());
            \Simflex\Core\Container::set('core', \Simflex\Core\Core::class);
        }

        static::setAuthHandler();

        if (SF_LOCATION != SF_LOCATION_CLI) {
            \Simflex\Core\Container::getCore()::init();
            \Simflex\Core\Container::getPage()::init();
            \App\Layout\LayoutManager::init();
        }

        return \Simflex\Core\Container::getCore();
    }

    protected static function loadConfig()
    {
        require_once SF_ROOT_PATH . '/config.php';
        Config::load();
        \Simflex\Core\Container::set('config', new Config());
    }

    protected static function loadEnv()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(SF_ROOT_PATH);
        $dotenv->safeLoad();
    }

    protected static function setAuthHandler()
    {
        $chain = [];
        if (SF_LOCATION == SF_LOCATION_ADMIN) {
            $chain[] = new \Simflex\Auth\Auth\SessionMiddleware();
        }

        $chain[] = new \Simflex\Auth\Auth\CookieMiddleware();
        $chain[] = new \Simflex\Auth\Auth\BasicAuthMiddleware();

        \Simflex\Core\Container::setAuthHandler(function () use ($chain) {
            \Simflex\Auth\Bootstrap::authByMiddlewareChain(
                (new \Simflex\Auth\Auth\Chain($chain))
            // You can change base user model for auth
//            ->setUserModelClass(YourUser::class)
            );
        });
    }

    public static function loadConstants()
    {
        require_once __DIR__ . '/../vendor/glushkovds/simflex/src/constants.php';
        require_once __DIR__ . '/../constants.php';
    }

    protected static function setTimezone()
    {
        date_default_timezone_set('Asia/Yekaterinburg');
    }

    /**
     * One session for all subdomains
     */
    protected static function setSessionParams()
    {
        if (\Simflex\Core\Config::$subdomainOneSession) {
            $baseDomain = implode('.', array_slice(explode('.', $_SERVER['HTTP_HOST']), 1));
            session_set_cookie_params(0, '/', ".$baseDomain");
        }
    }

    protected static function startDebug()
    {
        \Simflex\Core\Profiler::start();
        $_ENV['start'] = ['time' => microtime(true), 'memory' => memory_get_usage()];
    }

    protected static function startSession()
    {
        session_start();
    }

    protected static function setCharset()
    {
        ini_set('default_charset', 'UTF-8');
    }

}