<?php

namespace App\Extensions\Form\Notifiers;

use Curl\Curl;
use Simflex\Core\Logger\Log;
use Simflex\Core\System\Settings;
use Simflex\Core\Telegram\TelegramApi;

/**
 * Заявка в Telegram: бот form_tg_token, чат form_tg_chat_id
 */
class TelegramNotifier implements Notifier
{
    public function isConfigured(): bool
    {
        return (string)Settings::get('form_tg_token') !== '' && (string)Settings::get('form_tg_chat_id') !== '';
    }

    public function send(array $data): bool
    {
        $e = fn(string $key): string => TelegramApi::sanitize($data[$key] ?? '');
        $text = "*НОВАЯ ЗАЯВКА*\n\n"
            . "*Форма:* {$e('form')}\n"
            . "*Страница:* {$e('page')}\n\n"
            . "*Имя:* {$e('name')}\n"
            . "*Телефон:* {$e('phone')}\n"
            . "*Почта:* {$e('email')}\n\n"
            . "*Комментарий:* {$e('comment')}";

        try {
            $curl = new Curl();
            $curl->post('https://api.telegram.org/bot' . Settings::get('form_tg_token') . '/sendMessage', [
                'chat_id' => Settings::get('form_tg_chat_id'),
                'parse_mode' => 'MarkdownV2',
                'text' => $text,
            ]);
            $answer = json_decode((string)$curl->getResponse(), true);
            return (bool)($answer['ok'] ?? false);
        } catch (\Throwable $ex) {
            Log::error('Заявка, Telegram: {error}', ['error' => $ex->getMessage()]);
            return false;
        }
    }
}
