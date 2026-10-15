<?php

namespace App\Extensions\Example\Component;

use Simflex\Core\Controller\Action;
use Simflex\Core\ControllerBase;

class ControllerExample extends ControllerBase
{
    // default root, this one is invoked only if baseUri == path
    #[Action('/')]
    protected function exampleRoot(): string
    {
        return 'This is the exampleRoot!';
    }

    // note how this one is BEFORE the first extractable path
    // this is done this way because we can't tell for sure, and the sorting isn't done in the backend for quicker
    // resolve
    #[Action('/hello')]
    protected function exampleStrict(): string
    {
        return 'This is the exampleStrict!';
    }

    // extractable path example
    // whatever comes after /, will be extracted and put into the argument
    #[Action('/:item')]
    protected function exampleExtract(string $item): string
    {
        return 'This is the exampleExtract! Extracted item: ' . $item;
    }

    // this action will be invoked only if HTTP method was "post"
    #[Action('/:item/post', 'post')]
    protected function exampleExtractPost(string $item): string
    {
        return 'This is the exampleExtractPost! Extracted item: ' . $item;
    }

    // fallback method - this one will be executed if no other action was matched
    // by default, it simply redirects to /404, but you might want some extra logic here
    protected function fallback(): string
    {
        return 'This is fallback!';
    }
}
