<?php
/*
 * Simplex Framework core functions
 * Loaded automatically from the bootstrap
 */

/**
 * @param string $name
 * @param mixed|null $defaultValue
 * @return mixed|null
 */
function env(string $name, mixed $defaultValue = null): mixed
{
    return array_key_exists($name, $_ENV) ? $_ENV[$name] : $defaultValue;
}

function removeRoot(string $path): string
{
    return str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
}

function asset(string $path, bool $siteOnly = false): string
{
    if (!$siteOnly && SF_LOCATION == SF_LOCATION_ADMIN) {
        return removeRoot(SF_CORE_ROOT_PATH) . '/Admin/theme/new/' . $path;
    }

    return '/theme/default/' . $path;
}

function url(string $path, array $get = []): string
{
    $q = http_build_query($get);
    return ('http' . ($_SERVER['HTTPS'] ? 's' : '')) . '://' . $_SERVER['HTTP_HOST'] . $path . ($q ? ('?' . $q) : '');
}

#[\JetBrains\PhpStorm\Deprecated]
function debug($var)
{
    $onHttp = SF_LOCATION == SF_LOCATION_SITE || SF_LOCATION == SF_LOCATION_API;
    if (is_array($var)) {
        if ($onHttp) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        } else {
            print_r($var);
        }
    } else {
        if ($onHttp) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        } else {
            var_dump($var);
        }
    }
    exit;
}

/**
 * Admin action logger
 * @param string $msg Message
 * @return void
 */
#[\JetBrains\PhpStorm\Deprecated]
function actlog(string $msg): void
{
    \Simflex\Core\Log::info('[ {ip} ] {method} {uri} - {msg}', [
        'ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'],
        'method' => $_SERVER['REQUEST_METHOD'],
        'uri' => $_SERVER['REQUEST_URI'],
        'msg' => $msg
    ]);
}

#[\JetBrains\PhpStorm\Deprecated]
function imDev(): bool
{
    return false;
}

