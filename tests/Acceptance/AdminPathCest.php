<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Адрес админки задаётся в конфиге (ADMIN_PATH в .env)
 */
class AdminPathCest
{
    private const PATH = '/cp-codecept';

    private ?string $env = null;

    public function _before(AcceptanceTester $I): void
    {
        $this->env = file_get_contents('.env');
        file_put_contents('.env', preg_replace('/^ADMIN_PATH=.*$/m', '', $this->env) . "\nADMIN_PATH=" . self::PATH . "\n");
    }

    public function _after(AcceptanceTester $I): void
    {
        $this->restore($I);
    }

    public function _failed(AcceptanceTester $I): void
    {
        $this->restore($I);
    }

    public function adminWorksOnConfiguredPath(AcceptanceTester $I): void
    {
        $I->loginAsAdmin(self::PATH);

        // меню и шапка строятся от настроенного адреса
        $I->seeElementInDOM('a[href="' . self::PATH . '/content"]');
        $I->seeElementInDOM('a[href="' . self::PATH . '/logout"]');
        $I->dontSeeElementInDOM('a[href^="/admin"]');

        $I->amOnPage(self::PATH . '/content');
        $I->seeElement('.sidebar');
        $I->seeInTitle('Редактор страниц');

        $fm = $I->executeAsyncJS(
            'const done = arguments[arguments.length - 1];
            fetch(arguments[0]).then(r => r.json()).then(done).catch(e => done({error: String(e)}));',
            [self::PATH . '/fm?method=getTree']
        );
        $I->assertTrue($fm['success'] ?? false, json_encode($fm));

        // адрес со слэшем на конце редиректит на адрес без него
        $I->amOnPage(self::PATH . '/content/');
        $I->seeCurrentUrlEquals(self::PATH . '/content');

        // старый адрес больше не ведёт в админку
        $I->amOnPage('/admin');
        $I->dontSeeElement('input[name="login[login]"]');
        $I->dontSeeElementInDOM('.sidebar');

        $I->amOnPage(self::PATH . '/logout');
        $I->seeInCurrentUrl(self::PATH);
        $I->seeElement('input[name="login[login]"]');
    }

    private function restore(AcceptanceTester $I): void
    {
        if ($this->env !== null) {
            file_put_contents('.env', $this->env);
            $this->env = null;
        }
    }
}
