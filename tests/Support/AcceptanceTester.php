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
        $I = $this;
        $I->amOnPage('/admin/');
        $I->fillField('login[login]', 'admin');
        $I->fillField('login[password]', '12345');
        $I->click('Войти');
        $I->waitForElement('.sidebar', 10);
    }
}
