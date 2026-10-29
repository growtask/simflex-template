<?php

namespace App\Extensions\Form;

use App\Extensions\Form\Notifiers\MailNotifier;
use App\Extensions\Form\Notifiers\Notifier;
use App\Extensions\Form\Notifiers\TelegramNotifier;
use Simflex\Core\Extensions\Form\FormGuard;
use Simflex\Core\Http\Request;
use Simflex\Core\Logger\Log;

/**
 * Обработчик формы «Связаться с нами» (компонент Common/Form/Callback ядра): проверка запроса на сервере,
 * защита от ботов (FormGuard) и отправка заявки по настроенным каналам (почта form_email, Telegram
 * form_tg_token + form_tg_chat_id). Каналы - Notifier, в тестах подменяются.
 *
 * Ответ - массив {success: bool, errors: {поле: текст}, message: текст}.
 */
class CallbackHandler
{
    public const MAX_NAME = 100;
    public const MAX_COMMENT = 300;

    /** @var list<Notifier> */
    protected array $notifiers;

    /**
     * @param list<Notifier>|null $notifiers Каналы уведомлений (по умолчанию - почта и Telegram)
     * @param FormGuard|null $guard Защита от ботов
     */
    public function __construct(?array $notifiers = null, protected ?FormGuard $guard = null)
    {
        $this->notifiers = $notifiers ?? [new MailNotifier(), new TelegramNotifier()];
        $this->guard ??= new FormGuard();
    }

    /**
     * Обрабатывает запрос с формой
     * @return array{success: bool, errors: array<string,string>, message: string}
     */
    public function handle(Request $request): array
    {
        if (!$request->isPost()) {
            return $this->fail('Форма отправляется методом POST');
        }
        if (!$this->guard->check($request)) {
            // бота не подсказываем: общая ошибка без полей
            Log::warning('Заявка отклонена защитой от ботов, IP {ip}', ['ip' => $request->getIp()]);
            return $this->fail('Не удалось отправить, обновите страницу и попробуйте ещё раз');
        }

        $data = [
            'form' => mb_substr(trim((string)$request->post('form', 'callback')), 0, 50),
            'name' => trim((string)$request->post('name', '')),
            'phone' => trim((string)$request->post('phone', '')),
            'email' => trim((string)$request->post('email', '')),
            'comment' => trim((string)$request->post('comment', '')),
            'page' => mb_substr(trim((string)$request->post('page', '')), 0, 500),
            'date' => date('Y-m-d H:i:s'),
        ];

        $errors = $this->validate($data, (string)$request->post('consent', '') === '1');
        if ($errors) {
            return ['success' => false, 'errors' => $errors, 'message' => 'Проверьте заполнение полей'];
        }

        return $this->deliver($data);
    }

    /**
     * Проверяет поля заявки
     * @param array<string,string> $data Данные заявки
     * @return array<string,string> Ошибки: поле => текст
     */
    public function validate(array $data, bool $consent): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Заполните поле';
        } elseif (mb_strlen($data['name']) > static::MAX_NAME) {
            $errors['name'] = 'Не больше ' . static::MAX_NAME . ' символов';
        }

        $digits = preg_replace('/\D/', '', $data['phone']) ?? '';
        if ($data['phone'] === '') {
            $errors['phone'] = 'Заполните поле';
        } elseif (!preg_match('/^[\d\s+\-().]+$/', $data['phone']) || strlen($digits) < 10 || strlen($digits) > 15) {
            $errors['phone'] = 'Введите номер телефона';
        }

        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите почту в формате name@example.com';
        }
        if (mb_strlen($data['comment']) > static::MAX_COMMENT) {
            $errors['comment'] = 'Не больше ' . static::MAX_COMMENT . ' символов';
        }
        if (!$consent) {
            $errors['consent'] = 'Нужно согласие на обработку данных';
        }

        return $errors;
    }

    /**
     * Отправляет заявку по настроенным каналам: успех, если ушла хотя бы по одному. Без настроенных каналов
     * заявка пишется в журнал (чтобы не потерять) и считается принятой
     * @param array<string,string> $data Проверенные данные
     */
    protected function deliver(array $data): array
    {
        $configured = array_filter($this->notifiers, fn(Notifier $n) => $n->isConfigured());
        if (!$configured) {
            Log::info('Заявка принята, но каналы уведомлений не настроены (form_email, form_tg_token): {data}', [
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);
            return ['success' => true, 'errors' => [], 'message' => ''];
        }

        $sent = false;
        // запускаем все каналы, даже если один упал
        foreach ($configured as $notifier) {
            $sent = $notifier->send($data) || $sent;
        }

        return $sent ? ['success' => true, 'errors' => [], 'message' => ''] : $this->fail('Не удалось отправить заявку');
    }

    /**
     * @return array{success: bool, errors: array<string,string>, message: string}
     */
    protected function fail(string $message): array
    {
        return ['success' => false, 'errors' => [], 'message' => $message];
    }
}
