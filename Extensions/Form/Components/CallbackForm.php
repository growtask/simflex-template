<?php

namespace App\Extensions\Form\Components;

use App\Extensions\Form\CallbackHandler;
use Simflex\Core\Http\Api\ApiController;
use Simflex\Core\Http\Api\JsonRequest;
use Simflex\Core\Http\Controller\Action;
use Simflex\Core\Http\Request;

/**
 * Приём формы «Связаться с нами» (Common/Form/Callback ядра): POST /form/callback, ответ - конверт JSON API.
 * Форма отправляется телом JSON (ApiController не принимает изменяющий запрос формой без CSRF-токена).
 * Заявка принята - пустой конверт, ошибки полей - 422 с error.errors. Проверка и отправка - CallbackHandler
 */
class CallbackForm extends ApiController
{
    /**
     * @return array<string, mixed>
     */
    #[Action('/', 'post')]
    protected function submit(JsonRequest $request): array
    {
        // поля формы из тела JSON - как POST обычной формы; адрес клиента остаётся прежним
        $form = Request::create('/form/callback', [], $request->json(), [], [], [
            'REQUEST_METHOD' => 'POST',
            'REMOTE_ADDR' => $request->server('REMOTE_ADDR', ''),
            'HTTP_X_FORWARDED_FOR' => $request->header('X-Forwarded-For') ?? '',
        ]);

        return (new CallbackHandler())->handle($form);
    }
}
