<?php

namespace Tests\Unit\Extensions;

use Codeception\Test\Unit;
use Simflex\Core\Extensions\Install\PackageInstaller;

/**
 * Установщик пакетов-расширений: миграции пакета попадают в сайт следующими номерами сайта
 */
class PackageInstallerTest extends Unit
{
    private string $dir;

    protected function _before(): void
    {
        $this->dir = sys_get_temp_dir() . '/sf-installer-' . uniqid();
        mkdir($this->dir . '/package', 0777, true);
        mkdir($this->dir . '/site', 0777, true);
        foreach (['00_migration.php', '27_seo.php', '28_settings.php'] as $file) {
            touch($this->dir . '/site/' . $file);
        }
        foreach (['01_catalog_category.php', '02_catalog_product.php'] as $file) {
            file_put_contents($this->dir . '/package/' . $file, $file);
        }
    }

    protected function _after(): void
    {
        foreach (['package', 'site'] as $sub) {
            array_map('unlink', glob("$this->dir/$sub/*"));
            rmdir("$this->dir/$sub");
        }
        rmdir($this->dir);
    }

    public function testMigrationsGetNextNumbersOfSite(): void
    {
        $copied = PackageInstaller::copyMigrations($this->dir . '/package', $this->dir . '/site');

        $this->assertSame(
            ['01_catalog_category.php' => '29_catalog_category.php', '02_catalog_product.php' => '30_catalog_product.php'],
            $copied
        );
        $this->assertSame('02_catalog_product.php', file_get_contents($this->dir . '/site/30_catalog_product.php'));
    }

    public function testInstalledMigrationsAreNotCopiedAgain(): void
    {
        PackageInstaller::copyMigrations($this->dir . '/package', $this->dir . '/site');
        // обновление пакета: новая миграция получает следующий номер, старые не дублируются
        file_put_contents($this->dir . '/package/03_catalog_brand.php', '');

        $this->assertSame(
            ['03_catalog_brand.php' => '31_catalog_brand.php'],
            PackageInstaller::copyMigrations($this->dir . '/package', $this->dir . '/site')
        );
        $this->assertCount(6, glob($this->dir . '/site/*.php'));
    }
}
