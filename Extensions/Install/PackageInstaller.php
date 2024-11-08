<?php

namespace App\Extensions\Install;

use Composer\Installer\PackageEvent;

class PackageInstaller
{
    public static function onPackageEvent(PackageEvent $event): void
    {
        // get package
        $operation = $event->getOperation();
        if (method_exists($operation, 'getPackage')) {
            $package = $operation->getPackage();
        } elseif (method_exists($operation, 'getTargetPackage')) {
            $package = $operation->getTargetPackage();
        }

        if (!isset($package) || !$package) {
            return;
        }

        // check if it's a Simflex package
        $path = $event->getComposer()->getInstallationManager()->getInstallPath($package);
        if (!is_dir($path . '/provider/extension')) {
            return;
        }

        $root = dirname($event->getComposer()->getConfig()->get('vendor-dir'));

        // copy files
        static::copyContents($path . '/database/migrations', $root . '/database/migrations');
        static::copyContents($path . '/database/seeders', $root . '/database/seeders');
        static::copyContents($path . '/provider/extension', $root . '/provider/extension');
    }

    protected static function copyContents(string $from, string $to): void
    {
        if (!is_dir($from) || !is_dir($to)) {
            return;
        }

        foreach (scandir($from) as $file) {
            copy($from . '/' . $file, $to . '/' . $file);
        }
    }
}