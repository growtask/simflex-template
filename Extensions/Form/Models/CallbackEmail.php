<?php

namespace App\Extensions\Form\Models;

use Simflex\Core\System\ModelBase;

/**
 * @property int $id
 * @property string $email
 * @property bool $is_subscribed
 */
class CallbackEmail extends ModelBase
{
    protected static string $table = 'callback_email';
    protected static string $primaryKeyName = 'id';
}
