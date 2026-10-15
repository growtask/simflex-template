<?php

namespace App\Extensions\Form\Component;

use App\Extensions\Form\Model\ModelCallback;
use App\Extensions\Form\Model\ModelCallbackEmail;
use App\Extensions\Form\Model\ModelCatalogProduct;
use Simflex\Core\Api\JsonResponse;
use Simflex\Core\ComponentBase;
use Simflex\Core\Container;
use Simflex\Core\Core;
use Simflex\Core\Mail\Mailer;
use Simflex\Core\Mail\Transport\PhpMailer;
use Simflex\Core\Time;

/**
 * @todo Incomplete: there are no migrations for the callback, callback_email tables, so this code fails on SQL
 *       in a real environment. To be moved to a separate composer package.
 */
class AjaxForm extends ComponentBase
{
    protected array $errors = [];
    protected array $data = [];

    /**
     * The JSON response is sent directly via JsonResponse (like Api\Json does),
     * bypassing the page layout.
     */
    protected function content(): string
    {
        $this->sendJson($this->process());
    }

    /**
     * Handles the form request
     * @return array Response data
     */
    protected function process(): array
    {
        $type = $_REQUEST['type'] ?? '';
        $name = $_REQUEST['name'] ?? '';
        $email = strtolower($_REQUEST['email'] ?? '');
        $phone = $_REQUEST['phone'] ?? '';
        $message = $_REQUEST['message'] ?? '';
        $from = $_REQUEST['from'] ?? '';
        $fromTitle = $_REQUEST['from_title'] ?? '';
        $date = Time::create()->asMySQL();

        // fix type
        if ($type == 'available') {
            $type = 'Сообщить о поступлении';
            $message = ModelCatalogProduct::findOne(['product_id' => $message])?->name;
        } elseif ($type == 'callback') {
            $type = 'Остались вопросы';
        }

        $this->data = compact('type', 'name', 'email', 'phone', 'message', 'date', 'from', 'fromTitle');

        // insert to db
        if (!$email) {
            ModelCallback::insertStatic(['name' => $name, 'phone' => $phone, 'message' => $message, 'date' => $date]);
        } elseif (!ModelCallbackEmail::findOne(['email' => $email])) {
            ModelCallbackEmail::insertStatic(['email' => $email, 'is_subscribed' => 1]);
        }

        // run both channels, even if the first one fails
        $mailSent = $this->sendMail();
        $telegramSent = $this->sendTelegram();
        return ['success' => $mailSent && $telegramSent, 'errors' => $this->errors];
    }

    /**
     * Sends JSON response and stops execution
     */
    protected function sendJson(array $data): never
    {
        (new JsonResponse())->setData($data)->output();
        exit;
    }

    protected function getMailer(): Mailer
    {
        if (Container::isSet('mailer')) {
            return Container::get('mailer');
        }

        $mailer = new Mailer();
        $mailer->transport = new PhpMailer();
        return $mailer;
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
        $to = (string)Core::siteParam('form_email');
        if (!$to) {
            // mail notifications are not configured
            return true;
        }

        $html = $this->buildMailBody();
        try {
            $sent = $this->getMailer()->sendText('Новая заявка с сайта', $to, $html, strip_tags($html));
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

        $token = (string)Core::siteParam('form_tg_token');
        if (!$token) {
            // telegram notifications are not configured
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
            'chat_id' => Core::siteParam('form_tg_chat_id'),
            'parse_mode' => 'MarkdownV2',
            'text' => $md,
        ]));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        return true;
    }
}
