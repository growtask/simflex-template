<?php

namespace App\Extensions\Form\Model;

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
