<?php


namespace App\Extensions\Upgrade\Hooks;


use App\Extensions\Upgrade\UpEntity;

class Notifier extends Hook
{

    public function before($what)
    {
        $this->up->replace('Notifier::echoAll','Notifier::output');
        $contents =& $this->up->newData[$this->up instanceof UpEntity ? 'classContents' : 'content'];
//        $contents = "aa Notifier::success('doc', 'Договор успешно сохранен', \$this->root1());
//        Notifier::error('doc', 'Жопа сохранен'); bb";
        if (strpos($contents, 'Notifier')) {
            $contents = str_replace('$this->root1()', '#root1#', $contents);
            $contents = preg_replace_callback('@(Notifier::)(success|error|info|notify|warning)\(([^\,\)]+)\s?\,\s([^\,\)]+)(\s?\,\s([^\,\)]+))?\)@is', function ($matches) {
//                print_r($matches);
                $start = $matches[1];
                $method = $matches[2];
                $message = $matches[4];
                $page = $matches[3];
                $goto = $matches[6] ?? 'false';
                return "$start$method($message, $goto, $page)";
            }, $contents);
            $contents = str_replace('#root1#', "\$this->root().SFCore::uri(0).'/'", $contents);
        }
//        echo $contents;
//        die;
    }

}