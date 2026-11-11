<?php

/*
 * Маршруты сайта: путь => класс компонента или контроллера (наследник ControllerBase).
 * Маршрут сайта главнее маршрута расширения с тем же путём; пути без маршрута разрешаются по пунктам меню сайта
 */

return [
    '/' => \App\Extensions\Content\Content::class,
    // приём формы «Связаться с нами»
    '/form/callback' => \App\Extensions\Form\Components\CallbackForm::class,
];
