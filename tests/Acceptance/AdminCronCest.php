<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Cron jobs are declared in code (provider/cron.php), the DB keeps only their activity and schedule
 */
class AdminCronCest
{
    private const JOB = 'cron_log_clean';

    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsDev();
    }

    public function tryToSeeJobsFromCode(AcceptanceTester $I): void
    {
        // a row of a job removed from the code is deleted, a missing row of a declared job is created
        $I->runSql('DELETE FROM cron');
        $I->runSql("INSERT INTO cron SET job = 'removed_job', active = 1, timing = '* * * * *'");

        $I->amOnPage('/admin/cron');
        $I->dontSee('Fatal');
        $I->dontSee('Warning');
        $I->see('Очистка журнала cron');
        $I->see('30 0 * * *');
        $I->dontSee('removed_job');
        $I->dontSee('Добавить');
        $I->assertSame(
            [['job' => self::JOB, 'active' => 1, 'timing' => '30 0 * * *']],
            $I->grabRowsFromSql('SELECT job, active, timing FROM cron')
        );
        $I->dontSeeLogProblems();
    }

    public function tryToEditSchedule(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/cron');
        $id = $I->grabRowsFromSql('SELECT id FROM cron WHERE job = ?', [self::JOB])[0]['id'];
        try {
            $I->amOnPage("/admin/cron/?action=form&id=$id");
            $I->seeInField('job', 'Очистка журнала cron');
            $I->fillField('timing', '15 3 * * *');
            $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
            $I->dontSee('Fatal');

            $I->assertSame(
                [['job' => self::JOB, 'timing' => '15 3 * * *']],
                $I->grabRowsFromSql('SELECT job, timing FROM cron')
            );
        } finally {
            $I->runSql('UPDATE cron SET active = 1, timing = ? WHERE job = ?', ['30 0 * * *', self::JOB]);
        }
        $I->dontSeeLogProblems();
    }

    public function tryToForbidAdding(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/cron/?action=form');
        $I->see('Недостаточно прав для добавления');
        $I->assertCount(1, $I->grabRowsFromSql('SELECT id FROM cron'));
    }

    public function tryToRunJobFromConsole(AcceptanceTester $I): void
    {
        $I->runSql('DELETE FROM cron_log WHERE job = ?', [self::JOB]);
        exec('php cron.php --job=' . self::JOB, $output, $code);
        $I->assertSame(0, $code);
        $I->assertSame(
            [['result' => 'ok']],
            $I->grabRowsFromSql('SELECT result FROM cron_log WHERE job = ?', [self::JOB])
        );
        $I->dontSeeLogProblems();
    }
}
