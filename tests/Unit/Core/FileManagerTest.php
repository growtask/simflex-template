<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\FileManager\Manager;
use Simflex\FileManager\Request;

/**
 * Пакет simflex/file-manager сам по себе, без ядра: свой запрос (Request из массивов), ответ — process()
 * {"error": {"code", "message"}, ...}, изменения — только POST со своего сайта и с CSRF-токеном, системные папки
 * (setProtectedDirs) не удаляются и не перемещаются
 */
class FileManagerTest extends Unit
{
    private const TOKEN = 'codecept-token';

    private string $root;

    protected function _before(): void
    {
        $this->root = sys_get_temp_dir() . '/codecept-fm-' . uniqid();
        mkdir($this->root . '/images', 0777, true);
        file_put_contents($this->root . '/readme.txt', 'text');
    }

    protected function _after(): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->root, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($this->root);
    }

    public function testReadsTreeAndListWithEnvelope(): void
    {
        [$status, $body] = $this->process(['method' => 'getTree', 'depth' => 1]);
        $this->assertSame(200, $status);
        $this->assertSame(['code' => 0, 'message' => ''], $body['error']);
        $this->assertSame('/images', $body['items'][0]['path']);

        [, $body] = $this->process(['method' => 'getFileList', 'path' => '', 'include_dirs' => 1]);
        $paths = array_column($body['items'], 'path');
        sort($paths);
        $this->assertSame(['/images', '/readme.txt'], $paths);
    }

    public function testModifiesOnlyWithPostFromSameSiteAndToken(): void
    {
        $this->assertSame(405, $this->process(['method' => 'createDir', 'path' => '', 'name' => 'a'])[0]);
        $this->assertSame(403, $this->process(['method' => 'createDir', 'path' => ''], ['name' => 'a'], 'wrong')[0]);
        // запрос с чужого сайта — даже с верным токеном
        $foreign = ['REQUEST_METHOD' => 'POST', 'HTTP_SEC_FETCH_SITE' => 'cross-site',
            'HTTP_X_CSRF_TOKEN' => self::TOKEN];
        $createA = ['method' => 'createDir', 'path' => ''];
        $this->assertSame(403, $this->process($createA, ['name' => 'a'], null, $foreign)[0]);
        $this->assertDirectoryDoesNotExist($this->root . '/a');

        [$status, $body] = $this->process(['method' => 'createDir', 'path' => ''], ['name' => 'docs'], self::TOKEN);
        $this->assertSame(200, $status, json_encode($body));
        $this->assertDirectoryExists($this->root . '/docs');

        $move = ['method' => 'move', 'path' => '/images'];
        [$status, $body] = $this->process($move, ['items' => ['/docs']], self::TOKEN);
        $this->assertSame([200, ['/images/docs']], [$status, $body['items']]);
    }

    public function testKeepsRootAndProtectedDirs(): void
    {
        $delete = fn(string $path) => $this->process(['method' => 'delete', 'path' => $path], [], self::TOKEN);
        $this->assertSame(400, $delete('')[0]);
        [$status, $body] = $delete('/images');
        $this->assertSame([403, 403], [$status, $body['error']['code']]);
        $this->assertStringContainsString('images', $body['error']['message']);
        $this->assertSame(400, $this->process(['method' => 'getFileList', 'path' => '/../'])[0]);
        $this->assertDirectoryExists($this->root . '/images');

        $this->assertSame(200, $delete('/readme.txt')[0]);
        $this->assertFileDoesNotExist($this->root . '/readme.txt');
    }

    public function testRefusesWithoutAccessAndHidesInternalErrors(): void
    {
        $denied = new Manager($this->root, fn() => false, null, new Request(['method' => 'getTree']));
        $this->assertSame(401, $denied->process()[0]);

        $logged = [];
        $broken = (new Manager('/nonexistent-' . uniqid(), fn() => true, null, new Request(['method' => 'getTree'])))
            ->setErrorLogger(function (string $message) use (&$logged) {
                $logged[] = $message;
            });
        [$status, $body] = $broken->process();
        // каталога нет — ошибка API с текстом, а не внутренняя
        $this->assertSame(500, $status);
        $this->assertSame('Каталог файлов не найден', $body['error']['message']);
        $this->assertSame([], $logged);
    }

    /**
     * Запрос к API без ядра: GET или (с токеном) POST со страницы этого же сайта
     * @return array{0: int, 1: array<string, mixed>}
     */
    private function process(array $get, array $post = [], ?string $token = null, ?array $server = null): array
    {
        $server ??= $token === null
            ? ['REQUEST_METHOD' => 'GET']
            : ['REQUEST_METHOD' => 'POST', 'HTTP_SEC_FETCH_SITE' => 'same-origin', 'HTTP_X_CSRF_TOKEN' => $token];
        $request = new Request($get, $post, [], $server);
        $manager = new Manager(
            $this->root,
            fn() => true,
            fn() => ($server['HTTP_X_CSRF_TOKEN'] ?? '') === self::TOKEN,
            $request
        );

        return $manager->setProtectedDirs(['/images'])->process();
    }
}
