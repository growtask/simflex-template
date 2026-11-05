<?php

declare(strict_types=1);

namespace Tests\Support;

use Simflex\Core\Extensions\Alert\Alert;

/**
 * Alert с подменой признака редиректа: в CLI headers_list() всегда пуст
 */
class RedirectingAlert extends Alert
{
    public static bool $isRedirect = false;

    protected static function isRedirecting(): bool
    {
        return static::$isRedirect;
    }
}
