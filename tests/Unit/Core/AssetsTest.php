<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Http\Assets;
use Simflex\Core\System\Container;

/**
 * Публикация статики пакета в рабочем режиме: каталог копируется в public/cache/assets/<hash>, а адрес копии
 * меняется, когда меняются файлы внутри (даже если mtime самого каталога прежний — так бывает после деплоя)
 */
class AssetsTest extends Unit
{
    private mixed $configBackup;
    private string $source;
    /** @var string[] Опубликованные тестом каталоги в public */
    private array $published = [];

    protected function _before(): void
    {
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;
        Container::set('config', (object)['devMode' => false]);
        $this->source = sys_get_temp_dir() . '/codecept-assets-' . uniqid();
        mkdir($this->source . '/js', 0777, true);
        file_put_contents($this->source . '/js/app.js', 'console.log(1);');
        file_put_contents($this->source . '/js/secret.php', '<?php echo 1;');
        $this->resetPublished();
    }

    protected function _after(): void
    {
        Container::set('config', $this->configBackup);
        $this->resetPublished();
        foreach ([...$this->published, $this->source] as $dir) {
            if (is_dir($dir)) {
                $this->remove($dir);
            }
        }
    }

    public function testChangedFileInsideDirGetsNewCopy(): void
    {
        $dirTime = filemtime($this->source);
        $first = $this->publish();
        $this->assertSame('console.log(1);', file_get_contents($this->getPublicPath($first) . '/js/app.js'));
        $this->assertFileDoesNotExist($this->getPublicPath($first) . '/js/secret.php', 'код не публикуется');

        // тот же каталог без изменений — та же копия
        $this->assertSame($first, $this->publish());

        // файл внутри обновили (как при деплое), mtime самих каталогов остался прежним
        file_put_contents($this->source . '/js/app.js', 'console.log(2);');
        touch($this->source . '/js/app.js', time() + 60);
        touch($this->source . '/js', $dirTime);
        touch($this->source, $dirTime);

        $second = $this->publish();
        $this->assertNotSame($first, $second, 'новые файлы — новая копия');
        $this->assertSame('console.log(2);', file_get_contents($this->getPublicPath($second) . '/js/app.js'));
    }

    public function testAddedFileGetsNewCopy(): void
    {
        $first = $this->publish();
        file_put_contents($this->source . '/js/extra.css', 'a{}');

        $second = $this->publish();
        $this->assertNotSame($first, $second);
        $this->assertFileExists($this->getPublicPath($second) . '/js/extra.css');
    }

    /** Публикует каталог как в новом запросе (кэш адресов текущего запроса сброшен) */
    private function publish(): string
    {
        $this->resetPublished();
        $url = Assets::publish($this->source);
        $this->published[] = $this->getPublicPath($url);

        return $url;
    }

    private function getPublicPath(string $url): string
    {
        return Container::getPaths()->getPublic() . $url;
    }

    private function resetPublished(): void
    {
        new \ReflectionProperty(Assets::class, 'published')->setValue(null, []);
    }

    private function remove(string $dir): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }
}
