<?php

namespace App\Extensions\Install;

use Composer\Installer\PackageEvent;

class PackageInstaller
{
    public static function onPackageEvent(PackageEvent $event): void
    {
        // make sure core is installed, if so - run the actual thing
        $path = $event->getComposer()->getConfig()->get(
                'vendor-dir'
            ) . '/growtask/simflex/Extensions/Install/PackageInstaller.php';
        if (is_file($path)) {
            require_once $path;
            \Simflex\Extensions\Install\PackageInstaller::onPackageEvent($event);
        }
    }
}