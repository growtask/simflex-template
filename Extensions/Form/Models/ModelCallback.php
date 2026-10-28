<?php

namespace App\Extensions\Form\Models;

use Simflex\Core\ModelBase;

/**
 * @property int $callback_id
 * @property string $name
 * @property string $phone
 * @property string $message
 * @property string $date
 * @property int|null $edited_by
 * @property string|null $edited_on
 */
class ModelCallback extends ModelBase
{
    protected static string $table = 'callback';
    protected static string $primaryKeyName = 'callback_id';
}
