<?php

namespace App\Extensions\Form\Admin\Component;

use App\Extensions\Form\Model\ModelCallback;
use Simflex\Admin\Base;
use Simflex\Core\Container;
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
            ModelCallback::bulkUpdate(
                ['edited_by' => Container::getUser()->user_id, 'edited_on' => Time::mysql(time())],
                ['callback_id' => $id]
            );
        }

        return $id;
    }
}
