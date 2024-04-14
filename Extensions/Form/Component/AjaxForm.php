<?php

namespace App\Extensions\Form\Component;

use App\Extensions\Catalog\MailAssist;
use App\Plugins\ReCaptcha\ReCaptcha;
use Simflex\Core\ComponentBase;
use Simflex\Core\Container;
use Simflex\Core\Core;
use Simflex\Core\DB;

use Simflex\Core\Time;

use function Sodium\compare;

class AjaxForm extends ComponentBase
{
    protected $errors = [];
    protected $data = [];

    protected function content()
    {
        if (!ReCaptcha::checkResponse()) {
            return json_encode(['success' => false]);
        }

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
            $message = DB::result('select name from catalog_product where product_id = ?', 'name', [$message]);
        } elseif ($type == 'callback') {
            $type = 'Остались вопросы';
        }

        $this->data = compact('type', 'name', 'email', 'phone', 'message', 'date', 'from', 'fromTitle');

        // insert to db
        $type = 'normal';
        if (!$email) {
            DB::query(
                'INSERT INTO callback (name, phone, message, date) VALUES (?, ?, ?, ?)',
                [$name, $phone, $message, $date]
            );
        } else {
            DB::query(
                'insert into callback_email (email, is_subscribed) select ?, 1 where not exists(select 1 from callback_email where email = ?)',
                [$email, $email]
            );
        }
        return json_encode(['success' => $this->sendMail() && $this->sendTelegram(), 'errors' => $this->errors]);
    }

    protected function sendMail()
    {
        $m = new MailAssist(Core::siteParam('form_email'), 'Новая заявка с сайта');

        $html = <<<HTML
<p><b>Форма: </b> {$this->data['type']}</p>
<p><b>Страница: </b> <a href="{$this->data['from']}">{$this->data['fromTitle']}</a></p>
<p></p>
<p><b>Имя: </b> {$this->data['name']}</p>
<p><b>E-mail: </b> {$this->data['email']}</p>
<p><b>Телефон: </b> {$this->data['phone']}</p>
<p></p>
<p><b>Сообщение: </b> {$this->data['message']}</p>
HTML;

        $m->content($html);
        return $m->send();
    }

    protected function sendTelegram()
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
                    '\\!'
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

        $ch = curl_init();

        curl_setopt(
            $ch,
            CURLOPT_URL,
            'https://api.telegram.org/bot' . Core::siteParam('form_tg_token') . '/sendMessage'
        );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'chat_id' => Core::siteParam('form_tg_chat_id'),
            'parse_mode' => 'MarkdownV2',
            'text' => $md
        ]));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        return true;
    }
}