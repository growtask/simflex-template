<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Media\File;
use Simflex\Media\Image;

/**
 * Загруженные изображения: каталоги размеров, масштабирование, обрезка, SVG, удаление
 */
class ImageTest extends Unit
{
    protected string $root = '';

    protected function _before(): void
    {
        $this->root = sys_get_temp_dir() . '/image_test_' . uniqid();
        (new \ReflectionProperty(File::class, 'uploadRoot'))->setValue(null, $this->root);
    }

    protected function _after(): void
    {
        (new \ReflectionProperty(File::class, 'uploadRoot'))->setValue(null, null);
        exec('rm -rf ' . escapeshellarg($this->root));
    }

    public function testPreviewAndSourceSizesAreAlwaysPresent(): void
    {
        $img = new Image('test/', ['small' => '200x200']);
        $this->assertSame(['small' => '200x200', 'preview' => '90x90', 'source' => '2000x10000'], $img->sizes);
        $this->assertSame($this->root . '/images/test/', $img->path);

        $img = new Image('test/', ['preview' => '120x120']);
        $this->assertSame('120x120', $img->sizes['preview']);
    }

    public function testSizesAreFittedAndCropped(): void
    {
        $src = $this->root . '/upload.png';
        mkdir($this->root, 0777, true);
        imagepng(imagecreatetruecolor(400, 200), $src);

        $img = new Image('png/', ['small' => '100x100', 'square' => '50x50x1', 'big' => '1000x1000']);
        $this->assertTrue($img->load($src));
        $img->save();

        $size = fn(string $dir) => array_slice(getimagesize($img->path . $dir . '/' . $img->getName()), 0, 2);
        $this->assertSame('png', $img->type);
        $this->assertSame([100, 50], $size('small'));
        $this->assertSame([50, 50], $size('square'));
        $this->assertSame([400, 200], $size('big'), 'smaller image is not enlarged');
        $this->assertSame([90, 45], $size('preview'));
        $this->assertFileEquals($src, $img->path . 'raw/' . $img->getName());

        $this->assertTrue($img->delete($img->getName()));
        $this->assertFileDoesNotExist($img->path . 'small/' . $img->getName());
        $this->assertFileDoesNotExist($img->path . 'raw/' . $img->getName());
    }

    public function testSvgIsCopiedToEverySize(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"></svg>';
        mkdir($this->root, 0777, true);
        file_put_contents($src = $this->root . '/upload.svg', $svg);

        $img = new Image('svg/', ['small' => '200x200']);
        $this->assertTrue($img->load($src));
        $img->save();

        foreach (['raw', 'small', 'preview', 'source'] as $dir) {
            $this->assertStringEqualsFile($img->path . $dir . '/' . $img->name . '.svg', $svg);
        }
    }

    public function testNotImageIsRejected(): void
    {
        mkdir($this->root, 0777, true);
        file_put_contents($src = $this->root . '/upload.png', '<?php echo 1;');

        $img = new Image('bad/');
        $this->assertFalse($img->load($src));
        $this->assertSame('', $img->getName());
    }
}
