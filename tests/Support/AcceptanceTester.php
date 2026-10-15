<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */

    /**
     * Logs in to the admin panel with the default seeded admin account
     */
    public function loginAsAdmin(): void
    {
        $this->loginAs('admin');
    }

    /**
     * Logs in to the admin panel with the default seeded dev account (full "dev" privilege)
     */
    public function loginAsDev(): void
    {
        $this->loginAs('dev');
    }

    /**
     * Clicks a form submit button and waits until the reloaded page is ready,
     * so that later checks don't hit elements of the page being unloaded
     */
    public function submitAndWaitForReload(string $button, string $waitFor): void
    {
        $I = $this;
        $I->executeJS('window.__beforeSubmit = true;');
        $I->click($button);
        $I->waitForJS('return !window.__beforeSubmit && document.readyState === "complete";', 10);
        $I->waitForElement($waitFor, 10);
    }

    private function loginAs(string $login): void
    {
        $I = $this;
        $I->amOnPage('/admin/');
        $I->fillField('login[login]', $login);
        $I->fillField('login[password]', '12345');
        $I->click('Войти');
        $I->waitForElement('.sidebar', 10);
    }

    /**
     * Direct DB access for test fixture setup/teardown (e.g. resetting a value that's
     * unreliable to type via WebDriver, such as non-Latin text after a page navigation).
     */
    public function runSql(string $sql, array $params = []): void
    {
        $pdo = new \PDO(
            'mysql:host=' . (getenv('DB_HOST') ?: 'db') . ';dbname=' . (getenv('DB_NAME') ?: 'simflex') . ';charset=utf8mb4',
            getenv('DB_USER') ?: 'simflex',
            getenv('DB_PASS') ?: 'simflex'
        );
        $pdo->prepare($sql)->execute($params);
    }
}
