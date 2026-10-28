<?php

namespace App;

use App\Extensions\Content\Content;

/**
 * Конфиг сайта. Маршруты, события, сервисы и команды — provider/*.php сайта; ConfigBase объединяет их с объявлениями
 * расширений (provider/extensions/*.php)
 *
 * @property string timezone Часовой пояс системы
 */
class Config extends \Simflex\Core\ConfigBase
{
    public function load(): void
    {
        $this->defaultComponent = Content::class;
        $this->privilegeClass = \App\Core\Privilege::class;
        $this->adminMenuClass = \App\Admin\Menu::class;

        // настройки из окружения. Значения по умолчанию объявлены только в ConfigBase
        $this->db['host'] = env('DB_HOST', $this->db['host']);
        $this->db['user'] = env('DB_USER', $this->db['user']);
        $this->db['password'] = env('DB_PASS', $this->db['password']);
        $this->db['name'] = env('DB_NAME', $this->db['name']);
        $this->adminPath = env('ADMIN_PATH', $this->adminPath);
        $this->devMode = env('DEVELOPER', $this->devMode);
        $this->enableLogging = env('LOGGING', $this->enableLogging);
        $this->logToFile = env('IS_LOG_TO_FILE', $this->logToFile);

        // дополнительные настройки
        $this->extra['timezone'] = env('TIMEZONE', 'Asia/Yekaterinburg');
    }
}
