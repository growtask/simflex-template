<?php

namespace App\Extensions\Layout;

use App\Extensions\Content\Content;
use App\Layout\LayoutManager;
use Simflex\Core\Console\Command;
use Simflex\Core\Console\Help;
use Simflex\Core\ConsoleBase;
use Simflex\Core\Container;
use Simflex\Core\Html\HtmlRequest;
use Simflex\Core\Log;

class ConsoleLayout extends ConsoleBase
{
    #[Command('Generate layout fields')]
    public function generate(#[Help('Page path')] string $path): void
    {
        // set bogus data.
        $_SERVER['REQUEST_URI'] = $path;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        Container::set('request', new HtmlRequest());

        LayoutManager::$shouldGenerateFields = true;

        try {
            $content = new Content();
            $content->execute();
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }

        LayoutManager::$shouldGenerateFields = false;
    }
}