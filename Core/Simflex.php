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
        // константы загрузки
        require_once __DIR__ . '/../vendor/growtask/simflex/src/constants.php';
        require_once __DIR__ . '/../constants.php';

        // загрузка composer
        require_once SF_ROOT_PATH . '/vendor/autoload.php';
        require_once SF_ROOT_PATH . '/functions.php';
    }

    /**
     * Относится ли путь запроса к админке (её URL-префикс задан в конфиге: adminPath)
     */
    public function isAdminPath(string $path): bool
    {
        $this->initConfig();
        return \Simflex\Admin\Core::isAdminPath($path);
    }

    /**
     * Инициализирует приложение
     * @throws SassException
     */
    public function init(): void
    {
        // базовая инициализация
        $this->initConfig();
        $this->initPhp();

        // инициализация, специфичная для CLI
        if (SF_LOCATION == SF_LOCATION_CLI) {
            $this->initPhpCli();
        }

        $this->initExceptionHandler();
        $this->initServices();
        $this->initEvents();
        $this->initLogger();

        Log::debug('Simflex Framework v' . getSimflexVersion());

        // инициализация подсистем ядра
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
     * Выполняет приложение
     * @param bool $asString Если задан, возвращает вывод строкой. Иначе отправляет в браузер
     * @return string|null Содержимое страницы или NULL, если `$asString` равен false.
     */
    public function execute(bool $asString): ?string
    {
        // начинаем запись в буфер, если запрошено
        if ($asString) {
            ob_start();
        }

        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreExecute, static::class));

        // выполняем приложение
        Container::getCore()::execute();

        $ev->dispatch(new Event(Events::PostExecute, static::class));
        return $asString ? ob_get_clean() : null;
    }

    /**
     * Обрабатывает запрос к API файлового менеджера админки. Доступно только пользователям админки
     */
    public function executeFileManager(): void
    {
        $mgr = new \Simflex\FileManager\Manager(SF_PUBLIC_PATH . '/uf', function () {
            return (bool)Container::getUser()?->ican(\Simflex\Core\Privilege::ADMIN);
        });

        $mgr->handleRequest();
    }

    /**
     * Готовит данные к выводу пользователю
     */
    public function prepareOutput(string $data): string
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PrePrepareOutput, static::class, $data));

        // подставляем параметры
        // параметры сайта всегда берутся из ядра сайта: Admin\Core::siteParam() требует ключ
        // и не может вернуть весь список
        $params = \Simflex\Core\Core::siteParam();
        $params = is_array($params) ? $params : [];
        $params['year'] = date('Y');

        $data = str_replace(
            array_map(fn(int|string $key) => '{' . $key . '}', array_keys($params)),
            array_map('strval', array_values($params)),
            $data
        );

        // убираем лишнее
        if (!$this->cfg->devMode) {
            $data = preg_replace([
                '/>[^\S ]+/', // убираем пробельные символы после тегов, кроме пробела
                '/[^\S ]+</', // убираем пробельные символы перед тегами, кроме пробела
                '/(\s)+/', // сокращаем повторяющиеся пробельные символы
                '/<!--(.|\s)*?-->/', // убираем HTML-комментарии
            ], [
                '>',
                '<',
                '\\1',
                '',
            ], $data);
        }

        $ev->dispatch(new Event(Events::PostPrepareOutput, static::class, $data));
        return $data;
    }

    /**
     * Инициализирует механизмы авторизации
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
     * Инициализирует ядро приложения и страницу
     *
     * Если location — CLI, ни то, ни другое не инициализируется
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
     * Инициализирует конфиг
     */
    protected function initConfig(): void
    {
        if (isset($this->cfg)) {
            return;
        }

        Dotenv::createImmutable(SF_ROOT_PATH)->safeLoad();

        require_once SF_ROOT_PATH . '/config.php';
        $this->cfg = new Config();
        $this->cfg->load();

        Container::set('config', $this->cfg);
    }

    protected function initExceptionHandler(): void
    {
        // ошибки ловит whoops, нам это делать не нужно
        if ($this->cfg->devMode) {
            return;
        }

        set_exception_handler(function (\Throwable $ex) {
            Log::emergency("Exception: {ex}\n\nStack trace: {st}", [
                'ex' => $ex->getMessage(),
                'st' => $ex->getTraceAsString(),
            ]);
        });
    }

    /**
     * Инициализирует настройки PHP
     */
    protected function initPhp(): void
    {
        date_default_timezone_set($this->cfg->timezone);
        ini_set('default_charset', 'UTF-8');
    }

    /**
     * Инициализирует окружение PHP CLI
     */
    protected function initPhpCli(): void
    {
        ini_set('display_errors', 1);
        ini_set('max_execution_time', 600);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    }

    /**
     * Инициализирует сервисы
     */
    protected function initServices(): void
    {
        $services = $this->cfg->getServices();
        $toLoad = $services['all'] ?? [];

        // загружаем сервисы только для web
        if (in_array(SF_LOCATION, [SF_LOCATION_ADMIN, SF_LOCATION_SITE])) {
            $toLoad = array_merge($toLoad, $services['web'] ?? []);
        }

        // загружаем сервисы только для api
        if (SF_LOCATION == SF_LOCATION_API) {
            $toLoad = array_merge($toLoad, $services['api'] ?? []);
        }

        // загружаем сервисы только для cli
        if (SF_LOCATION == SF_LOCATION_CLI) {
            $toLoad = array_merge($toLoad, $services['cli'] ?? []);
        }

        // загружаем сервисы только для dev
        if ($this->cfg->devMode) {
            $toLoad = array_merge($toLoad, $services['dev'] ?? []);
        }

        // загружаем запрошенные сервисы
        foreach ($toLoad as $service) {
            Container::set($service::getServiceName(), new $service());
        }
    }

    /**
     * Инициализирует события
     */
    protected function initEvents(): void
    {
        $events = $this->cfg->getEvents();

        $eventManager = Container::getEvents();
        $eventManager->subscribeAll($events);
    }

    /**
     * Инициализирует базовый логгер
     */
    protected function initLogger(): void
    {
        $ev = Container::getEvents();
        $ev->dispatch(new Event(Events::PreLoggerInit, static::class));

        // логирование отключено
        if (!$this->cfg->devMode && !$this->cfg->enableLogging) {
            return;
        }

        // логгер debug bar
        if ($this->cfg->devMode) {
            Log::addLogger(Container::get('debugbar')->getMessages());
        }

        Log::addLogger(new StdLogger());

        // файловый логгер, по желанию (IS_LOG_TO_FILE=1); по умолчанию лог пишется только в stdout
        if ($this->cfg->logToFile) {
            Log::addLogger(new FileLogger());
        }

        // событие инициализации логирования
        $ev->dispatch(new Event(Events::PostLoggerInit, static::class));
    }
}
