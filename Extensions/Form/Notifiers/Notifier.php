<?php

namespace App\Extensions\Form\Notifiers;

/**
 * Канал уведомления о заявке с сайта (почта, Telegram). Для тестов подменяется своей реализацией
 */
interface Notifier
{
    /**
     * Настроен ли канал (есть адрес, токен и т. п.); ненастроенный канал не вызывается
     */
    public function isConfigured(): bool;

    /**
     * Отправляет заявку
     * @param array<string,string> $data Данные заявки: form, name, phone, email, comment, page, date
     * @return bool true, если отправлено
     */
    public function send(array $data): bool;
}
