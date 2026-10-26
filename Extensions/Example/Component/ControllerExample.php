<?php

namespace App\Extensions\Example\Component;

use Simflex\Core\Controller\Action;
use Simflex\Core\ControllerBase;

class ControllerExample extends ControllerBase
{
    // корень по умолчанию, вызывается, только если baseUri == path
    #[Action('/')]
    protected function exampleRoot(): string
    {
        return 'This is the exampleRoot!';
    }

    // обратите внимание: этот путь стоит ДО первого пути с извлечением параметра,
    // потому что однозначно их не различить, а сортировка на бэкенде не делается ради более быстрого
    // разрешения
    #[Action('/hello')]
    protected function exampleStrict(): string
    {
        return 'This is the exampleStrict!';
    }

    // пример пути с извлечением параметра
    // всё, что идёт после /, извлекается и передаётся в аргумент
    #[Action('/:item')]
    protected function exampleExtract(string $item): string
    {
        return 'This is the exampleExtract! Extracted item: ' . $item;
    }

    // это действие вызывается, только если HTTP-метод "post"
    #[Action('/:item/post', 'post')]
    protected function exampleExtractPost(string $item): string
    {
        return 'This is the exampleExtractPost! Extracted item: ' . $item;
    }

    // запасной метод — выполняется, если не подошло ни одно другое действие
    // по умолчанию просто перенаправляет на /404, но сюда можно добавить свою логику
    protected function fallback(): string
    {
        return 'This is fallback!';
    }
}
