<?php

use App\Extensions\Example\Component\ControllerExample;

return [
    '/' => \App\Extensions\Content\Content::class,
    '/example' => ControllerExample::class,
];