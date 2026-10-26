<?php

namespace App\Extensions\Form\Admin\Component;

use App\Extensions\Form\Model\ModelCallback;
use Simflex\Admin\Table\Table;
use Simflex\Core\Container;
use Simflex\Core\Time;

/**
 * @todo Incomplete: для таблицы callback нет миграций, поэтому в реальном окружении этот код падает
 *       на SQL. Будет вынесено в отдельный composer-пакет.
 */
class Callback extends Table
{
    protected static string $model = ModelCallback::class;

    public static function getFields(): array
    {
        return [];
    }

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
