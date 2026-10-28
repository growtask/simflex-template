<?php

namespace App\Extensions\Form\Models;

use Simflex\Core\ModelBase;

/**
 * @property int $id
 * @property string $email
 * @property bool $is_subscribed
 */
class ModelCallbackEmail extends ModelBase
{
    protected static string $table = 'callback_email';
    protected static string $primaryKeyName = 'id';
}
