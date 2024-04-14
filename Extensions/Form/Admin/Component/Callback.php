<?php

namespace App\Extensions\Form\Admin\Component;

use Simflex\Admin\Base;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\Time;

class Callback extends Base
{
    public function save()
    {
        $id = parent::save();
        if ($id) {
            DB::query('update callback set edited_by = ?, edited_on = ? where callback_id = ?', [Container::getUser()->user_id, Time::mysql(time())]);
        }

        return $id;
    }
}