<?php

namespace App\Extensions\Form\Components;

use Simflex\Core\System\Container;
use App\Extensions\Form\Models\Callback;
use App\Extensions\Form\Models\CallbackEmail;
use App\Extensions\Form\Models\CatalogProduct;
use Simflex\Core\Http\Api\JsonResponse;
use Simflex\Core\Http\ComponentBase;
use Simflex\Core\Mail\Mailer;
use Simflex\Core\Helpers\Time;
use Simflex\Core\System\Settings;

/**
 * @todo Incomplete: для таблиц callback, callback_email нет миграций, поэтому в реальном окружении этот код
 *       падает на SQL. Будет вынесено в отдельный composer-пакет.
 */
class AjaxForm extends ComponentBase
{
    protected array $errors = [];
    protected array $data = [];

    /**
     * JSON-ответ отправляется напрямую через JsonResponse (как делает Api\Json),
     * в обход макета страницы.
     */
    protected function content(): string
    {
        $this->sendJson($this->process());
    }

    /**
     * Обрабатывает заявку из формы
     * @return array Данные ответа
     */
    protected function process(): array
    {
        $request = Container::getRequest();
        $type = (string)$request->request('type', '');
        $name = (string)$request->request('name', '');
        $email = strtolower((string)$request->request('email', ''));
        $phone = (string)$request->request('phone', '');
        $message = (string)$request->request('message', '');
        $from = (string)$request->request('from', '');
        $fromTitle = (string)$request->request('from_title', '');
        $date = Time::create()->asMySQL();

        // приводим тип
        if ($type === 'available') {
            $type = 'Сообщить о поступлении';
            $message = CatalogProduct::findOne(['product_id' => $message])?->name;
        } elseif ($type === 'callback') {
            $type = 'Остались вопросы';
        }

        $this->data = compact('type', 'name', 'email', 'phone', 'message', 'date', 'from', 'fromTitle');

        // пишем в БД
        if (!$email) {
            Callback::insert(['name' => $name, 'phone' => $phone, 'message' => $message, 'date' => $date]);
        } elseif (!CallbackEmail::findOne(['email' => $email])) {
            CallbackEmail::insert(['email' => $email, 'is_subscribed' => 1]);
        }

        // запускаем оба канала, даже если первый упал
        $mailSent = $this->sendMail();
        $telegramSent = $this->sendTelegram();
        return ['success' => $mailSent && $telegramSent, 'errors' => $this->errors];
    }

    /**
     * Отправляет JSON-ответ и останавливает выполнение
     */
    protected function sendJson(array $data): never
    {
        (new JsonResponse())->setData($data)->output();
        exit;
    }

    protected function buildMailBody(): string
    {
        $d = array_map(fn($v) => htmlspecialchars((string)$v, ENT_QUOTES), $this->data);

        return <<<HTML
<p><b>Форма: </b> {$d['type']}</p>
<p><b>Страница: </b> <a href="{$d['from']}">{$d['fromTitle']}</a></p>
<p></p>
<p><b>Имя: </b> {$d['name']}</p>
<p><b>E-mail: </b> {$d['email']}</p>
<p><b>Телефон: </b> {$d['phone']}</p>
<p></p>
<p><b>Сообщение: </b> {$d['message']}</p>
HTML;
    }

    protected function sendMail(): bool
    {
        $to = (string)Settings::get('form_email');
        if (!$to) {
            // уведомления на почту не настроены
            return true;
        }

        $html = $this->buildMailBody();
        try {
            $sent = Mailer::get()->sendText('Новая заявка с сайта', $to, $html, strip_tags($html));
        } catch (\Throwable $ex) {
            $this->errors[] = 'mail: ' . $ex->getMessage();
            return false;
        }

        if (!$sent) {
            $this->errors[] = 'mail: not sent';
        }
        return $sent;
    }

    protected function sendTelegram(): bool
    {
        $patch = function ($i) {
            return str_replace(
                ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
                [
                    '\\_',
                    '\\*',
                    '\\[',
                    '\\]',
                    '\\(',
                    '\\)',
                    '\\~',
                    '\\`',
                    '\\>',
                    '\\#',
                    '\\+',
                    '\\-',
                    '\\=',
                    '\\|',
                    '\\{',
                    '\\}',
                    '\\.',
                    '\\!',
                ],
                $i
            );
        };

        $md = <<<MD
**НОВАЯ ЗАЯВКА**

**Форма: ** {$patch($this->data['type'])}
**Страница: ** [{$patch($this->data['fromTitle'])}]({$patch($this->data['from'])})

**Имя: ** {$patch($this->data['name'])}
**E\-mail: ** {$patch($this->data['email'])}
**Телефон: ** {$patch($this->data['phone'])}

**Сообщение: ** {$patch($this->data['message'])}
MD;

        $token = (string)Settings::get('form_tg_token');
        if (!$token) {
            // уведомления в Telegram не настроены
            return true;
        }

        $ch = curl_init();

        curl_setopt(
            $ch,
            CURLOPT_URL,
            'https://api.telegram.org/bot' . $token . '/sendMessage'
        );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'chat_id' => Settings::get('form_tg_chat_id'),
            'parse_mode' => 'MarkdownV2',
            'text' => $md,
        ]));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        return true;
    }
}
