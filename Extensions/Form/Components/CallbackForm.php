<?php

namespace App\Extensions\Form\Components;

use App\Extensions\Form\CallbackHandler;
use Simflex\Core\Http\Api\JsonResponse;
use Simflex\Core\Http\ComponentBase;
use Simflex\Core\System\Container;

/**
 * Приём формы «Связаться с нами» (Common/Form/Callback ядра): POST /form/callback, ответ JSON.
 * Проверка и отправка - CallbackHandler
 */
class CallbackForm extends ComponentBase
{
    protected function content(): string
    {
        $answer = (new CallbackHandler())->handle(Container::getRequest());
        (new JsonResponse())->setData($answer)->output();
        exit;
    }
}
