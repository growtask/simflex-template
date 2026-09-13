<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Корень веб-сервера - public/: снаружи доступны только index.php и статика
 */
class PublicRootCest
{
    public function codeAndConfigsAreNotServed(AcceptanceTester $I): void
    {
        $I->amOnPage('/');

        // файл => строка, которая выдала бы его содержимое
        $files = [
            '/.env' => 'DB_PASS',
            '/config.php' => 'namespace App',
            '/constants.php' => 'SF_ROOT_PATH',
            '/composer.json' => 'growtask/simflex',
            '/Core/Simflex.php' => 'namespace App',
            '/vendor/autoload.php' => 'autoload_real.php',
            '/vendor/growtask/simflex/src/Core/Core.php' => 'namespace Simflex',
            '/provider/routes.php' => '<?php',
            '/theme/default/index.tpl' => '<?php',
            '/' . $I->logFile() => 'CODECEPT_LOG_MARKER',
        ];
        // маркер, которого нет нигде, кроме файла лога (лог очищается перед каждым тестом)
        file_put_contents($I->logFile(), "CODECEPT_LOG_MARKER\n", FILE_APPEND);
        foreach ($files as $url => $marker) {
            $body = $this->fetch($I, $url)['body'];
            $I->assertStringNotContainsString($marker, $body, "$url must not be served");
        }
    }

    public function scriptsOtherThanIndexAreNotExecuted(AcceptanceTester $I): void
    {
        $file = 'public/uf/files/codecept_probe.php';
        $I->writeToFile($file, '<?php echo "PROBE_" . "EXECUTED";');
        try {
            $I->amOnPage('/');
            $res = $this->fetch($I, '/uf/files/codecept_probe.php');
            $I->assertStringNotContainsString('PROBE_EXECUTED', $res['body']);
            $I->assertStringNotContainsString('<?php', $res['body']);
        } finally {
            $I->deleteFile($file);
        }
    }

    public function siteAssetsAreServed(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $this->seeAllAssetsServed($I);
    }

    public function adminAssetsArePublished(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin');
        $I->seeElementInDOM('link[href^="/cache/assets/"]');
        $this->seeAllAssetsServed($I);

        // в публичную директорию копируется только статика
        $I->assertSame([], array_filter(
            iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(
                'public/cache/assets',
                \FilesystemIterator::SKIP_DOTS
            ))),
            fn(\SplFileInfo $f) => preg_match('/\.(php\d?|phtml|tpl|scss|json|md)$/i', $f->getFilename())
        ));
    }

    public function fileManagerIsForAdminsOnly(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->assertStringContainsString('not_authenticated', $this->fetch($I, '/admin/fm?method=getTree')['body']);

        $I->loginAsAdmin();
        $res = $this->fetch($I, '/admin/fm?method=getTree');
        $I->assertSame(200, $res['status']);
        $I->assertTrue(json_decode($res['body'], true)['success'] ?? false, $res['body']);
    }

    private function seeAllAssetsServed(AcceptanceTester $I): void
    {
        $urls = $I->executeJS(
            'return [...document.querySelectorAll("link[href], script[src]")]
                .map(e => e.getAttribute("href") || e.getAttribute("src"))
                .filter(u => u.startsWith("/") && !u.startsWith("//"))'
        );
        $I->assertNotEmpty($urls);
        foreach ($urls as $url) {
            $I->assertSame(200, $this->fetch($I, $url)['status'], "$url must be served");
        }
    }

    /**
     * @return array{status: int, body: string}
     */
    private function fetch(AcceptanceTester $I, string $url): array
    {
        return $I->executeAsyncJS(
            'const done = arguments[arguments.length - 1];
            fetch(arguments[0]).then(r => r.text().then(body => done({status: r.status, body})))
                .catch(e => done({status: 0, body: String(e)}));',
            [$url]
        );
    }
}
