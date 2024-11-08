<?php
/*
 * Simplex Framework core functions
 * Loaded automatically from the bootstrap
 */

use Composer\InstalledVersions;
use Simflex\Core\Buffer;

/**
 * Get environment variable
 * @param string $name
 * @param mixed|null $defaultValue
 * @return mixed|null
 */
function env(string $name, mixed $defaultValue = null): mixed
{
    return array_key_exists($name, $_ENV) ? $_ENV[$name] : $defaultValue;
}

/**
 * Remove DOCUMENT_ROOT from the path
 * @param string $path Path to process
 * @return string Processed path
 */
function removeRoot(string $path): string
{
    return str_replace(SF_ROOT_PATH, '', $path);
}

/**
 * Get asset path
 * @param string $path Relative path to the asset
 * @param bool $siteOnly If set to true, returns only site assets
 * @return string Full path to the asset
 */
function asset(string $path, bool $siteOnly = false): string
{
    if (!$siteOnly && SF_LOCATION == SF_LOCATION_ADMIN) {
        return removeRoot(SF_CORE_ROOT_PATH) . '/Admin/theme/new/' . $path;
    }

    return '/assets/' . $path;
}

/**
 * Get full URL
 * @param string $path Path to the resource
 * @param array $get GET parameters
 * @return string Full URL
 */
function url(string $path, array $get = []): string
{
    $q = http_build_query($get);
    return ('http' . ($_SERVER['HTTPS'] ? 's' : '')) . '://' . $_SERVER['HTTP_HOST'] . $path . ($q ? ('?' . $q) : '');
}

/**
 * Get Simflex version
 * @return string Simflex version
 */
function getSimflexVersion(): string
{
    return Buffer::getOrSet('version', function () {
        if (!InstalledVersions::isInstalled('growtask/simflex')) {
            return 'N/A';
        }

        return InstalledVersions::getPrettyVersion('growtask/simflex');
    });
}
