<?php

namespace App;

use App\Extensions\Content\Content;

/**
 * @property string timezone System-wide timezone
 */
class Config extends \Simflex\Core\ConfigBase
{
    public function load(): void
    {
        $this->defaultComponent = Content::class;

        $this->db['host'] = env('DB_HOST', '127.0.0.1');
        $this->db['user'] = env('DB_USER', 'simflex');
        $this->db['password'] = env('DB_PASS', 'simflex');
        $this->db['name'] = env('DB_NAME', 'simflex');

        $this->devMode = env('DEVELOPER', true);
        $this->enableLogging = env('LOGGING', false);

        $this->files['routes'] = SF_ROOT_PATH . '/provider/routes.php';
        $this->files['events'] = SF_ROOT_PATH . '/provider/events.php';
        $this->files['services'] = SF_ROOT_PATH . '/provider/services.php';
        $this->files['commands'] = SF_ROOT_PATH . '/provider/commands.php';

        $this->extra['timezone'] = env('TIMEZONE', 'Asia/Yekaterinburg');
    }
}
