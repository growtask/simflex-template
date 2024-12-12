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

/**
 * Renders an SVG icon reference from a sprite file
 * @param string $iconName Name of the icon to reference
 * @param string $className Optional CSS classes to apply
 * @return string The SVG HTML with sprite reference
 */
function renderIcon(string $iconName, string $className = ''): string {
    return sprintf(
        '<svg class="%s"><use href="/assets/icons/icons.svg#icon-%s"></use></svg>',
        htmlspecialchars($className),
        htmlspecialchars($iconName)
    );
}

/**
 * Generates HTML attribute string from array of key-value pairs
 * @param array<string,mixed> $attributes Key-value pairs of HTML attributes
 * @return string Generated HTML attribute string with escaped values
 */
function buildAttrs(array $attributes = []): string
{
    if (empty($attributes)) {
        return '';
    }

    return implode(' ', array_map(
        fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_QUOTES, 'UTF-8')),
        array_keys($attributes),
        $attributes
    ));
}