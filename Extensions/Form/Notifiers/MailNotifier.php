<?php

namespace App\Extensions\Form\Notifiers;

use Simflex\Core\Logger\Log;
use Simflex\Core\Mail\Mailer;
use Simflex\Core\System\Settings;

/**
 * Заявка письмом на адрес из настройки form_email
 */
class MailNotifier implements Notifier
{
    public function isConfigured(): bool
    {
        return (string)Settings::get('form_email') !== '';
    }

    public function send(array $data): bool
    {
        $e = fn(string $key): string => htmlspecialchars($data[$key] ?? '', ENT_QUOTES);
        $html = "<p><b>Форма:</b> {$e('form')}</p>"
            . "<p><b>Страница:</b> {$e('page')}</p>"
            . "<p><b>Имя:</b> {$e('name')}</p>"
            . "<p><b>Телефон:</b> {$e('phone')}</p>"
            . "<p><b>Почта:</b> {$e('email')}</p>"
            . "<p><b>Комментарий:</b> {$e('comment')}</p>";

        try {
            return Mailer::get()->sendText('Новая заявка с сайта', (string)Settings::get('form_email'), $html, strip_tags($html));
        } catch (\Throwable $ex) {
            Log::error('Заявка, почта: {error}', ['error' => $ex->getMessage()]);
            return false;
        }
    }
}
