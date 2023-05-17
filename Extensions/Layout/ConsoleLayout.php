<?php
namespace App\Extensions\Layout;

use App\Extensions\Content\Content;
use App\Layout\LayoutManager;
use Simflex\Core\ConsoleBase;

class ConsoleLayout extends ConsoleBase
{
    public function generate($path)
    {
        LayoutManager::$shouldGenerateFields = true;
        Container::getRequest()->setPath($path);

        $content = new Content();
        $content->execute();

        LayoutManager::$shouldGenerateFields = false;
    }
}