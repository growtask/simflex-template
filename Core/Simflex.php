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
use Simflex\Core\Events\Events;
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

        // init CLI-specific stuff
        if (SF_LOCATION == SF_LOCATION_CLI) {
            $this->initPhpCli();
        }

        $this->initServices();
        $this->initEvents();
        $this->initLogger();

        Log::debug('Simflex Framework v' . getSimflexVersion());

        // init core subsystems
        $this->initAuth();
        $this->initCore();

        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreInit, static::class));

        if (SF_LOCATION != SF_LOCATION_CLI) {
            Container::getCore()::init();
            Container::getPage()::init();
            LayoutManager::init();
        }

        $ev->dispatch(new Event(Events::PostInit, static::class));
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
        $ev->dispatch(new Event(Events::PreExecute, static::class));

        // execute app
        Container::getCore()::execute();

        $ev->dispatch(new Event(Events::PostExecute, static::class));
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
        $ev->dispatch(new Event(Events::PrePrepareOutput, static::class, $data));

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

        $ev->dispatch(new Event(Events::PostPrepareOutput, static::class, $data));
        return $data;
    }

    /**
     * Initializes authorization mechanisms
     * @return void
     */
    protected function initAuth(): void
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreAuthInit, static::class));

        Container::setAuthHandler(function () {
            Bootstrap::authByMiddlewareChain(
                (new Chain([
                    new SessionMiddleware(),
                    new CookieMiddleware(),
                    new BasicAuthMiddleware(),
                ]))
            );
        });

        $ev->dispatch(new Event(Events::PostAuthInit, static::class));
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
        $ev->dispatch(new Event(Events::PreCoreInit, static::class));

        if (SF_LOCATION == SF_LOCATION_ADMIN) {
            Container::set('page', new \Simflex\Admin\Page());
            Container::set('core', \Simflex\Admin\Core::class);
        } elseif (SF_LOCATION == SF_LOCATION_SITE || SF_LOCATION == SF_LOCATION_API) {
            Container::set('page', new \Simflex\Core\Page());
            Container::set('core', \Simflex\Core\Core::class);
        }

        $ev->dispatch(new Event(Events::PostCoreInit, static::class));
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
    }

    /**
     * Initializes PHP CLI environment
     * @return void
     */
    protected function initPhpCli(): void
    {
        ini_set('display_errors', 1);
        ini_set('max_execution_time', 600);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    }

    /**
     * Initializes services
     * @return void
     */
    protected function initServices(): void
    {
        $services = include $this->cfg->files['services'];
        $toLoad = $services['all'] ?? [];

        // load web-only services
        if (in_array(SF_LOCATION, [SF_LOCATION_ADMIN, SF_LOCATION_SITE])) {
            $toLoad = array_merge($toLoad, $services['web'] ?? []);
        }

        // load api-only services
        if (SF_LOCATION == SF_LOCATION_API) {
            $toLoad = array_merge($toLoad, $services['api'] ?? []);
        }

        // load cli-only services
        if (SF_LOCATION == SF_LOCATION_CLI) {
            $toLoad = array_merge($toLoad, $services['cli'] ?? []);
        }

        // load dev-only services
        if ($this->cfg->devMode) {
            $toLoad = array_merge($toLoad, $services['dev'] ?? []);
        }

        // load requested services
        foreach ($toLoad as $service) {
            Container::set($service::getServiceName(), new $service());
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
        $ev->dispatch(new Event(Events::PreLoggerInit, static::class));

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
        $ev->dispatch(new Event(Events::PostLoggerInit, static::class));
    }
}