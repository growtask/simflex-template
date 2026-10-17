<?php

declare(strict_types=1);

namespace Tests\Support;

use Simflex\Admin\Plugins\Alert\Alert;

/**
 * The admin panel messages (Alert) without a session: read and replaced directly
 */
class AlertState
{
    public static function get(): array
    {
        $p = new \ReflectionProperty(Alert::class, 's');
        return $p->isInitialized() ? $p->getValue() : [];
    }

    public static function set(array $s): void
    {
        (new \ReflectionProperty(Alert::class, 's'))->setValue(null, $s);
        (new \ReflectionProperty(Alert::class, 'isInited'))->setValue(null, true);
    }
}
