<?php
namespace App\Extensions\Layout;

use App\Core\Console\Alert;
use App\Extensions\Content\Content;
use App\Layout\LayoutManager;
use Simflex\Core\ConsoleBase;
use Simflex\Core\Container;
use Simflex\Core\Html\HtmlRequest;

class ConsoleLayout extends ConsoleBase
{
    public function generate($path)
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
            Alert::error($ex->getMessage());
        }

        LayoutManager::$shouldGenerateFields = false;
    }
}