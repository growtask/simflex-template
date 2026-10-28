<?php

namespace App\Extensions\Install;

use Composer\Installer\PackageEvent;

class PackageInstaller
{
    public static function onPackageEvent(PackageEvent $event): void
    {
        // убеждаемся, что ядро установлено, и тогда запускаем саму установку
        $path = $event->getComposer()->getConfig()->get(
            'vendor-dir'
        ) . '/growtask/simflex-core/src/Extensions/Install/PackageInstaller.php';
        if (is_file($path)) {
            require_once $path;
            \Simflex\Core\Extensions\Install\PackageInstaller::onPackageEvent($event);
        } else {
            echo "[!] PackageInstaller not found, skipping (path: $path).\n";
        }
    }
}
