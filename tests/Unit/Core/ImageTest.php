<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Image;

class ImageTest extends Unit
{
    private array $serverBackup = [];
    private string $tmpRoot = '';

    protected function _before(): void
    {
        $this->serverBackup = $_SERVER;
        $this->tmpRoot = sys_get_temp_dir() . '/image_test_' . uniqid();
        mkdir($this->tmpRoot, 0777, true);
        $_SERVER['DOCUMENT_ROOT'] = $this->tmpRoot;
    }

    protected function _after(): void
    {
        $_SERVER = $this->serverBackup;
        exec('rm -rf ' . escapeshellarg($this->tmpRoot));
    }

    public function testPreviewSizeIsAdded(): void
    {
        $img = new Image('test/', ['small' => '200x200']);
        $this->assertSame('90x90', $img->sizes['preview']);
        $this->assertSame('200x200', $img->sizes['small']);

        $img = new Image('test/', ['preview' => '120x120']);
        $this->assertSame('120x120', $img->sizes['preview']);
    }

    public function testSvgIsCopiedToEverySize(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="1" height="1"/>';
        $src = $this->tmpRoot . '/upload.svg';
        file_put_contents($src, $svg);

        $img = new Image('svg/', ['small' => '200x200']);
        $img->type = 'svg';
        (new \ReflectionProperty(Image::class, 'img'))->setValue($img, $svg);
        (new \ReflectionProperty(Image::class, 'uploadedFilePath'))->setValue($img, $src);
        $img->save();

        foreach (['raw', 'small', 'preview', 'source'] as $subdir) {
            $file = $img->path . $subdir . '/' . $img->name . '.svg';
            $this->assertFileExists($file);
            $this->assertSame($svg, file_get_contents($file));
        }
    }
}
