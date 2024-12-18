<?php

namespace App;

use App\Extensions\Content\Content;
use Simflex\Core\ExtensionConfig;
use Simflex\Core\Log;

/**
 * @property string timezone System-wide timezone
 * @property array extensions Loaded extensions
 * @property array data Loaded file data (routes, events, etc...)
 */
class Config extends \Simflex\Core\ConfigBase
{
    public function getRoutes(): array
    {
        return $this->data['routes'];
    }

    public function getEvents(): array
    {
        return $this->data['events'];
    }

    public function getServices(): array
    {
        return $this->data['services'];
    }

    public function getCommands(): array
    {
        return $this->data['commands'];
    }

    public function load(): void
    {
        $this->defaultComponent = Content::class;

        // configure database
        $this->db['host'] = env('DB_HOST', '127.0.0.1');
        $this->db['user'] = env('DB_USER', 'simflex');
        $this->db['password'] = env('DB_PASS', 'simflex');
        $this->db['name'] = env('DB_NAME', 'simflex');

        // developer options
        $this->devMode = env('DEVELOPER', true);
        $this->enableLogging = env('LOGGING', false);

        // default files
        $this->files['routes'] = SF_ROOT_PATH . '/provider/routes.php';
        $this->files['events'] = SF_ROOT_PATH . '/provider/events.php';
        $this->files['services'] = SF_ROOT_PATH . '/provider/services.php';
        $this->files['commands'] = SF_ROOT_PATH . '/provider/commands.php';

        // extra settings
        $this->extra['configDebug'] = env('CONFIG_DEBUG', false);
        $this->extra['timezone'] = env('TIMEZONE', 'Asia/Yekaterinburg');
        $this->extra['extensions'] = [];

        // dynamic loader
        $this->loadExtensions();
        $this->loadFiles();
    }

    protected function loadFiles(): void
    {
        if (!$this->devMode && is_file(SF_ROOT_PATH . '/cache/files.php')) {
            $this->extra['data'] = include SF_ROOT_PATH . '/cache/files.php';
            $this->log('Loaded files from cache');
            return;
        }

        $routes = include $this->files['routes'];
        $events = include $this->files['events'];
        $services = include $this->files['services'];
        $commands = include $this->files['commands'];

        // combine from extensions
        // note: loads extensions FIRST, so user can override those later if needed
        foreach ($this->extra['extensions'] as $name => $data) {
            $routes = array_merge($data['routes'], $routes);
            $events = array_merge($data['events'], $events);
            $commands = array_merge($data['commands'], $commands);

            foreach ($services as $key => $value) {
                $services[$key] = array_merge($data['services'][$key] ?? [], $value);
            }

            $this->log('Loaded data of extension {name}', ['name' => $name]);
        }

        $this->extra['data'] = [
            'routes' => $routes,
            'events' => $events,
            'services' => $services,
            'commands' => $commands
        ];

        if (!$this->devMode) {
            file_put_contents(
                SF_ROOT_PATH . '/cache/files.php',
                "<?php\nreturn " . var_export($this->extra['data'], true) . ';'
            );
        }
    }

    protected function loadExtensions(): void
    {
        if (!$this->devMode && is_file(SF_ROOT_PATH . '/cache/extensions.php')) {
            $this->extra['extensions'] = include SF_ROOT_PATH . '/cache/extensions.php';
            $this->log('Loaded {n} extensions from cache', ['n' => count($this->extra['extensions'])]);
            return;
        }

        $extensions = [];
        foreach (scandir(SF_ROOT_PATH . '/provider/extension') as $file) {
            $path = pathinfo($file);
            if (($path['extension'] ?? '') != 'php') {
                continue;
            }

            /** @var ExtensionConfig $class */
            $class = include SF_ROOT_PATH . '/provider/extension/' . $file;
            if (!$class || !is_object($class)) {
                $this->log('Extension {file} cannot be loaded', ['file' => $file]);
                continue;
            }

            try {
                $ref = new \ReflectionClass($class);
                if (!$ref->isSubclassOf(ExtensionConfig::class)) {
                    $this->log('Extension {file} does not extend Simflex\Core\ExtensionConfig class', ['file' => $file]
                    );
                    continue;
                }
            } catch (\ReflectionException $e) {
                $this->log('Extension {file} cannot be loaded: {ex}', ['file' => $file, 'ex' => $e->getMessage()]);
                continue;
            }

            $extensions[$class->name] = [
                'loadOrder' => $class->loadOrder,
                'routes' => $class->getRoutes(),
                'events' => $class->getEvents(),
                'services' => $class->getServices(),
                'commands' => $class->getCommands(),
            ];

            $this->log('Loaded extension {name}', ['name' => $class->name]);
        }

        // sort by load order
        uasort($extensions, function (array $a, array $b) {
            if ($a['loadOrder'] == $b['loadOrder']) {
                return 0;
            }

            return $a['loadOrder'] < $b['loadOrder'] ? -1 : 1;
        });

        $this->extra['extensions'] = $extensions;
        if (!$this->devMode) {
            file_put_contents(
                SF_ROOT_PATH . '/cache/extensions.php',
                "<?php\nreturn " . var_export($extensions, true) . ';'
            );
        }
    }

    protected function log(string $msg, array $i = []): void
    {
        if (!$this->extra['configDebug']) {
            return;
        }

        $logPath = true;
        if (!is_dir($rootDir = $this->logPath)) {
            if (!@mkdir($rootDir, 0700, true)) {
                $logPath = false;
            }
        }

        if ($logPath) {
            $logPath = $rootDir . '/' . date('Y-m-d') . '.log';
            file_put_contents(
                $logPath,
                str_replace(array_map(fn(string $k) => '{' . $k . '}', array_keys($i)), array_values($i), $msg) . "\n",
                FILE_APPEND
            );
        }
    }
}
