<?php

namespace App\Extensions\Form\Admin\Component;

use Simflex\Admin\Base;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\Time;

/**
 * @todo Incomplete: there are no migrations for the callback table, so this code fails on SQL
 *       in a real environment. To be moved to a separate composer package.
 */
class Callback extends Base
{
    public function save(): string|int|false
    {
        $id = parent::save();
        if ($id) {
            DB::query('update callback set edited_by = ?, edited_on = ? where callback_id = ?', [Container::getUser()->user_id, Time::mysql(time()), $id]);
        }

        return $id;
    }
}