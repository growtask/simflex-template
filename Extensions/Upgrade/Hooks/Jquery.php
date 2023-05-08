<?php


namespace App\Extensions\Upgrade\Hooks;


class Jquery extends Hook
{

    public function after($what)
    {
        if ($what == 'class' && $this->up->newData['class'] == 'Jquery') {
            $this->up->newData['classContents'] = "\n\npublic static function webPath()
    {
        return str_replace(SF_ROOT_PATH, '', __DIR__);
    }\n" . $this->up->newData['classContents'];
            $this->up->replace("'/plug/jquery/","static::webPath() . '/");
        }
        $this->up->save();
//        $this->up->replace('Jquery::jquery', 'Jquery::core');
    }

}