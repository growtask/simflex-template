<?php
/*
 * Simplex Framework core functions
 * Loads from Init.php - no need dedicated include
 *
 */

/**
 * @param string $name
 * @param mixed|null $defaultValue
 * @return mixed|null
 */
function env(string $name, $defaultValue = null)
{
    return array_key_exists($name, $_ENV) ? $_ENV[$name] : $defaultValue;
}

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

if (!function_exists('imDev')) {
    function imDev()
    {
        return !empty($_COOKIE['imdev']);
    }
}