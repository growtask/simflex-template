<?php

namespace App\Core;

use App\Config;
use App\Layout\LayoutManager;
use Dotenv\Dotenv;
use ScssPhp\ScssPhp\Exception\SassException;
use Simflex\Auth\Auth\BasicAuthMiddleware;
use Simflex\Auth\Auth\Chain;
use Simflex\Auth\Auth\CookieMiddleware;
use Simflex\Auth\Auth\SessionMiddleware;
use Simflex\Auth\Bootstrap;
use Simflex\Core\Container;
use Simflex\Core\Events\Event;
use Simflex\Core\Log;
use Simflex\Core\Logger\FileLogger;
use Simflex\Core\Logger\StdLogger;

class Simflex
{
    protected Config $cfg;

    public function __construct()
    {
        // bootstrap constants
        require_once __DIR__ . '/../vendor/glushkovds/simflex/src/constants.php';
        require_once __DIR__ . '/../constants.php';

        // bootstrap composer
        require_once SF_ROOT_PATH . '/vendor/autoload.php';
        require_once SF_ROOT_PATH . '/functions.php';
    }

    /**
     * Initializes app
     * @return void
     * @throws SassException
     */
    public function init(): void
    {
        // base init
        $this->initConfig();
        $this->initPhp();
        $this->initServices();
        $this->initEvents();
        $this->initLogger();

        Log::debug('Simflex Framework v' . SF_VERSION);

        // init core subsystems
        $this->initAuth();
        $this->initCore();

        $ev = Container::getEvents();
        $ev->dispatch(new Event('pre_init', static::class, SF_LOCATION));

        if (SF_LOCATION != SF_LOCATION_CLI) {
            Container::getCore()::init();
            Container::getPage()::init();
            LayoutManager::init();
        }

        $ev->dispatch(new Event('post_init', static::class, SF_LOCATION));
    }

    /**
     * Execute application
     * @param bool $asString If set, returns output as string. Otherwise, sends to the browser
     * @return string|null Page content, or NULL if `$asString` is set to false.
     */
    public function execute(bool $asString): ?string
    {
        // start writing to buffer, if requested
        if ($asString) {
            ob_start();
        }

        $ev = Container::getEvents();
        $ev->dispatch(new Event('pre_execute', static::class, SF_LOCATION));

        // execute app
        Container::getCore()::execute();

        $ev->dispatch(new Event('post_execute', static::class, SF_LOCATION));
        return $asString ? ob_get_clean() : null;
    }

    /**
     * Prepares data for output to the user
     * @param string $data
     * @return string
     */
    public function prepareOutput(string $data): string
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event('pre_prepare_output', static::class, SF_LOCATION, $data));

        // replace params
        $params = Container::getCore()::siteParam();
        $params['year'] = date('Y');

        $data = str_replace(array_map(fn(string $key) => '{' . $key . '}', array_keys($params)),
            array_values($params),
            $data);

        // strip unnecessary stuff
        if (!$this->cfg->devMode) {
            $data = preg_replace([
                '/>[^\S ]+/', // strip whitespaces after tags, except space
                '/[^\S ]+</', // strip whitespaces before tags, except space
                '/(\s)+/', // shorten multiple whitespace sequences
                '/<!--(.|\s)*?-->/' // strip HTML comments
            ], [
                '>',
                '<',
                '\\1',
                ''
            ], $data);
        }

        $ev->dispatch(new Event('post_prepare_output', static::class, SF_LOCATION, $data));
        return $data;
    }

    /**
     * Initializes authorization mechanisms
     * @return void
     */
    protected function initAuth(): void
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event('pre_auth_init', static::class, SF_LOCATION));

        Container::setAuthHandler(function () {
            Bootstrap::authByMiddlewareChain(
                (new Chain([
                    new SessionMiddleware(),
                    new CookieMiddleware(),
                    new BasicAuthMiddleware(),
                ]))
            );
        });

        $ev->dispatch(new Event('post_auth_init', static::class, SF_LOCATION));
    }

    /**
     * Initializes app core and page
     *
     * Note, that if location is set to CLI - neither will initialize
     * @return void
     */
    protected function initCore(): void
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event('pre_core_init', static::class, SF_LOCATION));

        if (SF_LOCATION == SF_LOCATION_ADMIN) {
            Container::set('page', new \Simflex\Admin\Page());
            Container::set('core', \Simflex\Admin\Core::class);
        } elseif (SF_LOCATION == SF_LOCATION_SITE || SF_LOCATION == SF_LOCATION_API) {
            Container::set('page', new \Simflex\Core\Page());
            Container::set('core', \Simflex\Core\Core::class);
        }

        $ev->dispatch(new Event('post_core_init', static::class, SF_LOCATION));
    }

    /**
     * Initializes config
     * @return void
     */
    protected function initConfig(): void
    {
        Dotenv::createImmutable(SF_ROOT_PATH)->safeLoad();

        require_once SF_ROOT_PATH . '/config.php';
        $this->cfg = new Config();
        $this->cfg->load();

        Container::set('config', $this->cfg);
    }

    /**
     * Initializes php-specific options
     * @return void
     */
    protected function initPhp(): void
    {
        date_default_timezone_set($this->cfg->timezone);
        ini_set('default_charset', 'UTF-8');

        if (SF_LOCATION != SF_LOCATION_CLI) {
            return;
        }

        // additional options for CLI
        ini_set('display_errors', 1);
        ini_set('max_execution_time', 600);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['DOCUMENT_ROOT'] = SF_ROOT_PATH;
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // parse commandline options
        global $argv;
        $_REQUEST = [];

        $args = array_slice($argv, 1);
        foreach ($args as $i => $arg) {
            if (!str_starts_with($arg, '--')) {
                $_REQUEST[$i] = $arg;
            } else {
                $data = explode('=', $arg);
                $_REQUEST[trim($data[0], '-')] = $data[1];
            }
        }

        $_GET = $_POST = $_REQUEST;
    }

    /**
     * Initializes services
     * @return void
     */
    protected function initServices(): void
    {
        $services = include $this->cfg->files['services'];
        foreach ($services['prod'] ?? [] as $service) {
            Container::set($service::getServiceName(), new $service());
        }

        // load dev-only services
        if ($this->cfg->devMode) {
            foreach ($services['dev'] ?? [] as $service) {
                Container::set($service::getServiceName(), new $service());
            }
        }
    }

    /**
     * Initializes events
     * @return void
     */
    protected function initEvents(): void
    {
        $events = include $this->cfg->files['events'];

        $eventManager = Container::getEvents();
        $eventManager->subscribeAll($events);
    }

    /**
     * Initializes base logger
     * @return void
     */
    protected function initLogger(): void
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event('pre_init_logger', static::class, SF_LOCATION));

        // logging is disabled
        if (!$this->cfg->devMode && !$this->cfg->enableLogging) {
            return;
        }

        // debug bar logger
        if ($this->cfg->devMode) {
            Log::addLogger(Container::get('debugbar')->getMessages());
        }

        // cli logger
        if (SF_LOCATION == SF_LOCATION_CLI) {
            Log::addLogger(new StdLogger());
        }

        // file logger
        $this->cfg->extra['logPath'] = SF_ROOT_PATH . '/uf/log';
        Log::addLogger(new FileLogger());

        // fire logging init event
        $ev->dispatch(new Event('post_init_logger', static::class, SF_LOCATION));
    }
}