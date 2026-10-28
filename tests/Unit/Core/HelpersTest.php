<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Cache;
use Simflex\Http\Cookie\CookieItem;
use Simflex\Helpers\DatetimeHelper;
use Simflex\Helpers\Str;
use Simflex\Http\Request;
use Simflex\Helpers\Time;
use Simflex\Helpers\TimeDiff;

/**
 * Строки, даты, запрос, cookies, кэш
 */
class HelpersTest extends Unit
{
    public function testTransliteMakesUrlSlug(): void
    {
        $this->assertSame('vtoraya-zapis', Str::translite('Вторая запись'));
        $this->assertSame('zhuk-chaschi-shum-yula-tsaplya-hleb', Str::translite('Жук чащи шум юла цапля хлеб'));
        $this->assertSame('obem-dene-yozh-elka-eho', Str::translite('Объём деньё йож ёлка эхо'));
        $this->assertSame('privet-mir-2', Str::translite('  Привет,  мир — 2! '));
        $this->assertSame('cafe-strasse', Str::translite('Café Straße'));
    }

    public function testPluralizeFeminine(): void
    {
        $this->assertSame('2 ночи', Str::pluralize(2, 'ноч', false));
        $this->assertSame('3 минуты', Str::pluralize(3, 'минут', false));
        $this->assertSame('1 минута', Str::pluralize(1, 'минут', false));
    }

    public function testFormDatesByQuarters(): void
    {
        $ret = Time::formDates('2020-01-01', '2020-06-30', 2);
        $this->assertSame('1 квартал 2020', $ret[0]['period']);
        $this->assertSame('2020-01-01 00:00:00', $ret[0]['beg']);
        $this->assertSame('2020-03-31 23:59:59', $ret[0]['end']);
        $this->assertSame('2 квартал 2020', $ret[1]['period']);
        $this->assertSame('2020-04-01 00:00:00', $ret[1]['beg']);

        $ret = Time::formDates('2020-11-01', '2021-01-15', 1);
        $this->assertSame('2020-11-01 00:00:00', $ret[0]['beg']);
        $this->assertSame('2020-12-01 00:00:00', $ret[1]['beg']);
        $this->assertSame('2021-01-01 00:00:00', $ret[2]['beg']);
    }

    public function testAddIntervalKeepsFormat(): void
    {
        $this->assertSame('2020-01-02 00:00:00', Time::addInterval('2020-01-01 00:00:00', '+1 day'));
        $this->assertSame('02.01.2020 00:00:00', Time::addInterval('01.01.2020 00:00:00', 86400));
        $gmt = '2020-01-01T10:00:00+00:00';
        $this->assertSame(date(DATE_ATOM, strtotime($gmt) + 3600), Time::addInterval($gmt, 3600));
        $this->assertSame(strtotime($gmt) + 3600, Time::addInterval($gmt, 3600, true));
        $this->assertSame(1000 + 60, Time::addInterval(1000, 60));
    }

    public function testUnixFromExcel(): void
    {
        $this->assertSame(mktime(10, 20, 30, 3, 15, 2021), Time::unix('15/03/2021 10:20:30'));
        $this->assertSame(mktime(0, 0, 0, 3, 15, 2021), Time::unix('15/03/2021'));
    }

    public function testDetectFormat(): void
    {
        $this->assertSame(Time::FORMAT_MYSQL, Time::detectFormat('1999-12-31 10:00:00'));
        $this->assertSame(Time::FORMAT_NORMAL, Time::detectFormat('31.12.1999 10:00:00'));
        $this->assertSame(Time::FORMAT_EXCEL, Time::detectFormat('31/12/1999'));
        $this->assertSame(Time::FORMAT_GMT, Time::detectFormat('2020-01-01T10:00:00+00:00'));
        $this->assertSame(Time::FORMAT_TIME, Time::detectFormat('12:30'));
        $this->assertSame(Time::FORMAT_UNIX, Time::detectFormat(1600000000));
        $this->assertSame(Time::FORMAT_UNIX, Time::detectFormat('1600000000'));
    }

    public function testUnixFromAllFormats(): void
    {
        $this->assertSame(mktime(10, 20, 30, 3, 5, 2020), Time::unix('2020-03-05 10:20:30'));
        $this->assertSame(mktime(10, 20, 0, 3, 5, 2020), Time::unix('2020-03-05 10:20'));
        $this->assertSame(mktime(0, 0, 0, 3, 5, 2020), Time::unix('2020-03-05'));
        $this->assertSame(mktime(0, 0, 0, 3, 1, 2020), Time::unix('2020-03'));
        $this->assertSame(mktime(10, 20, 30, 3, 5, 2020), Time::unix('05.03.2020 10:20:30'));
        $this->assertSame(mktime(10, 20, 0, 3, 5, 2020), Time::unix('05.03.2020 10:20'));
        $this->assertSame(mktime(0, 0, 0, 3, 5, 2020), Time::unix('05.03.2020'));
        $this->assertSame(12 * 3600 + 30 * 60, Time::unix('12:30'));
        $this->assertSame(12 * 3600 + 30 * 60 + 45, Time::unix('12:30:45'));
        $this->assertSame(1577872800, Time::unix('2020-01-01T10:00:00+00:00'));
        $this->assertSame(1600000000, Time::unix(1600000000));
        $this->assertSame(1600000000, Time::unix('1600000000'));
    }

    public function testConvertCustomLetters(): void
    {
        $date = '2020-03-05 10:20:30';
        $this->assertSame('05 мар 2020', Time::convert($date, 'd K Y', false));
        $this->assertSame('05 марта 2020', Time::convert($date, 'd Q Y', false));
        $this->assertSame('март 2020', Time::convert($date, 'X Y', false));
        $this->assertSame('Март', Time::convert($date, 'E', false));
        $this->assertSame('ЧТ', Time::convert($date, 'R', false));
        $this->assertSame('четверг', Time::convert($date, 'J', false));
        $this->assertSame('ЧТ, четверг 05 мар март марта Март 2020', Time::convert($date, 'R, J d K X Q E Y', false));
        $this->assertSame('мартмартаМартчетвергЧТмар', Time::convert($date, 'XQEJRK', false));
        $this->assertSame('2020-03-05 10:20:30', Time::convert($date, 'Y-m-d'));
        $this->assertSame('2020-03-05', Time::convert('2020-03-05', 'Y-m-d'));
        $this->assertSame('1970-01-01', Time::convert('12:30', 'Y-m-d', false));
        $this->assertSame('12:30', Time::convert('12:30', 'H:i', false));
    }

    public function testConvertKeepsEmptyValues(): void
    {
        foreach (['-', '', ' - '] as $value) {
            $this->assertSame($value, Time::convert($value, 'Y-m-d'));
            $this->assertSame($value, Time::mysql($value));
            $this->assertSame($value, Time::normal($value));
        }
        $this->assertSame('-', Time::user('-'));
    }

    public function testUserAndUserFull(): void
    {
        $this->assertSame('05 мар 2020', Time::user('2020-03-05', false));
        $this->assertSame('05 мар 2020 в 00:00', Time::user('2020-03-05', true));
        $this->assertSame('05 мар 2020 в 10:20', Time::user('2020-03-05 10:20:30'));
        $this->assertSame('05 мар 2020 в 10:20:30', Time::user('2020-03-05 10:20:30', true, true));
        $this->assertSame('05 марта 2020 года', Time::userFull('2020-03-05', false));
        $this->assertSame('05 марта 2020 года', Time::userFull('2020-03-05'));
        $this->assertSame('05 марта 2020 в 00:00:00', Time::userFull('2020-03-05', true, true));
        $this->assertSame('15 марта 2021 года', Time::userFull('15/03/2021'));

        $unix = mktime(12, 26, 40, 9, 13, 2020);
        $this->assertSame('13 сентября 2020 в 12:26', Time::userFull($unix));
        $this->assertSame('13 сентября 2020 года', Time::userFull($unix, false));
    }

    public function testMysqlAndNormalAutoHourMin(): void
    {
        $this->assertSame('2020-03-05', Time::mysql('2020-03-05'));
        $this->assertSame('2020-03-05 10:20', Time::mysql('05.03.2020 10:20'));
        $this->assertSame('2020-03-05 10:20:30', Time::mysql('05.03.2020 10:20:30'));
        $this->assertSame('2020-03-05 10:20', Time::mysql('2020-03-05 10:20:30', 'auto', false));
        $this->assertSame('2020-03-05', Time::mysql('2020-03-05 10:20:30', false));
        $this->assertSame('2021-03-15', Time::mysql('15/03/2021'));
        $this->assertSame('2020-03-05 00:00:00', Time::mysql('2020-03-05', true, true));

        $this->assertSame('05.03.2020', Time::normal('2020-03-05'));
        $this->assertSame('05.03.2020 10:20:30', Time::normal('2020-03-05 10:20:30'));
        $this->assertSame('05.03.2020 00:00', Time::normal('2020-03-05', true, false));

        $unix = mktime(12, 26, 40, 9, 13, 2020);
        $this->assertSame('2020-09-13 12:26:40', Time::mysql($unix));
        $this->assertSame('13.09.2020 12:26:40', Time::normal($unix));
        $this->assertSame('2020-09-13', Time::mysql($unix, false));
    }

    public function testSubTimes(): void
    {
        $this->assertSame(86400, Time::subTimes('2020-01-02', '2020-01-01'));
        $this->assertSame(-86400, Time::subTimes('2020-01-01', '2020-01-02'));
        $this->assertSame('24:00:00', Time::subTimes('2020-01-02', '2020-01-01', 'normal'));
        $this->assertSame('01:29', Time::subTimes('2020-01-01 10:00:00', '2020-01-01 08:30:15', 'normal', false));
        $this->assertSame('-', Time::subTimes('-', '2020-01-01'));
        $this->assertSame('-', Time::subTimes('2020-01-01', '-', 'normal'));
    }

    public function testSecondsToTime(): void
    {
        $this->assertSame('00:00:00', Time::secondsToTime(0));
        $this->assertSame('00:02:09', Time::secondsToTime(129));
        $this->assertSame('00:02', Time::secondsToTime(129, false));
        $this->assertSame('-00:02:09', Time::secondsToTime(-129));
        $this->assertSame('-00:02', Time::secondsToTime(-129, false));
        $this->assertSame('23:59:59', Time::secondsToTime(86399));
        $this->assertSame('100:00:00', Time::secondsToTime(360000));
        $this->assertSame('00:01:00', Time::secondsToTime(59.6));
    }

    public function testUnixtimeFromTime(): void
    {
        $this->assertSame(129, Time::unixtimeFromTime('02:09'));
        $this->assertSame(3729, Time::unixtimeFromTime('1:02:09'));
        $this->assertSame(0, Time::unixtimeFromTime(''));
    }

    public function testAddIntervalEdgeCases(): void
    {
        $this->assertSame('2020-02-01 00:00:00', Time::addInterval('2020-01-01', '+1 month'));
        $this->assertSame('01.01.2020 01:00:00', Time::addInterval('01.01.2020', '3600'));
        $this->assertSame('2020-01-01', Time::addInterval('2020-01-01', 'abc'));
        $this->assertSame(1000, Time::addInterval(1000, 0));
        $this->assertSame(4600, Time::addInterval(1000, '+1 hour'));
    }

    public function testTimeObject(): void
    {
        $time = Time::create('2020-03-05 10:20:30');
        $this->assertSame(mktime(10, 20, 30, 3, 5, 2020), $time->asUnix());
        $this->assertSame('2020-03-05 10:20:30', $time->asMySQL());
        $this->assertSame('05.03.2020 10:20:30', $time->asNormal());
        $this->assertSame('05 мар 2020 10:20:30', $time->asUser());
        $this->assertSame('05 марта 2020 10:20:30', $time->asUserFull());
        $this->assertSame('2020-03', $time->asYearMonth());
        $this->assertSame('ЧТ 05 мар 2020', $time->format('R d K Y'));
        $this->assertSame('2020-03-05 10:20:30', (string)$time);
        $this->assertSame(date(DATE_ATOM, $time->asUnix()), $time->asGMT());
        $this->assertSame('2020-03-02 00:00:00', Time::create('2020-01-31')->add('+1 month')->asMySQL());
        $this->assertSame('2020-01-31 01:00:00', Time::create('2020-01-31')->add(3600)->asMySQL());
        $this->assertEqualsWithDelta(time(), Time::create()->asUnix(), 2);
    }

    public function testWdayAndExtractTime(): void
    {
        $this->assertSame('ЧТ', Time::wday('2020-03-05'));
        $this->assertSame('Четверг', Time::wdayFull('05.03.2020'));
        $this->assertSame('10:20:30', Time::extractTime('2020-03-05 10:20:30'));
        $this->assertSame('10:20', Time::extractTime('2020-03-05 10:20:30', false));
    }

    public function testDiffSignificant(): void
    {
        $diff = Time::create('2020-01-01 00:00:00')->diff('2020-01-03 03:04:05');
        $this->assertSame('2 дня 3 часа 4 минуты', $diff->significant());
        $this->assertSame('2 дня 3 часа', $diff->significant(2));
        $this->assertSame('2 дня 3 часа 4 минуты 5 секунд', $diff->significant(6));
        $this->assertSame('2', (string)$diff);

        $diff = Time::create('2020-01-01')->diff('2021-03-04 10:00:00');
        $this->assertSame('1 год 2 месяца 3 дня', $diff->significant());
        $this->assertSame('428', (string)$diff);
        $this->assertSame('1 год 2 месяца 3 дня', Time::create('2020-01-01')->diff(Time::create('2021-03-04 10:00:00'), true)->significant());

        $this->assertSame('2 дня', Time::create('2020-01-03')->diff('2020-01-01')->significant());
        $this->assertSame('-2', Time::create('2020-01-03')->diff('2020-01-01')->format('%R%a'));
    }

    public function testWeekAndMonthBounds(): void
    {
        $this->assertSame('2020-03-02', Time::weekStart('2020-03-05'));
        $this->assertSame('2020-03-08', Time::weekEnd('2020-03-05'));
        $this->assertSame('2020-03-31', Time::monthEnd('2020-03-05'));
        $this->assertSame('2020-12-28', Time::weekStart('2021-01-01'));
        $this->assertSame('2021-01-03', Time::weekEnd('2021-01-01'));
        $this->assertSame('2020-12-31', Time::monthEnd('2020-12-31'));
        $this->assertSame('2020-02-29', Time::monthEnd('2020-02-10'));
    }

    public function testFormDatesByYears(): void
    {
        $ret = Time::formDates('2019-05-10', '2021-07-15', 3);
        $this->assertCount(3, $ret);
        $this->assertSame(2019, $ret[0]['period']);
        $this->assertSame('2021-12-31 23:59:59', $ret[2]['end']);
    }

    public function testToDateTime(): void
    {
        $ts = 1600000000;
        $this->assertSame($ts, DatetimeHelper::toDateTime($ts)->getTimestamp());
        $this->assertSame('2020-01-02 03:04:05', DatetimeHelper::toDateTime('2020-01-02 03:04:05')->format('Y-m-d H:i:s'));
        $this->assertSame('2020-01-02', DatetimeHelper::toDateTime('2020-01-02')->format('Y-m-d'));
        $this->assertSame(gmdate('Y-m-d H:i:s', $ts), DatetimeHelper::toSqlFormat($ts));
        $this->expectException(\Exception::class);
        DatetimeHelper::toDateTime('not a date');
    }

    public function testTimeDiff(): void
    {
        $interval = new \DateInterval('P2DT3H');
        $diff = new TimeDiff($interval);
        $this->assertSame($interval, $diff->getRawDateInterval());
        $this->assertSame('2 03', $diff->format('%d %H'));
    }

    public function testRequestWithoutHttpHeaders(): void
    {
        $server = $_SERVER;
        try {
            foreach (array_keys($_SERVER) as $key) {
                if (str_starts_with($key, 'HTTP_')) {
                    unset($_SERVER[$key]);
                }
            }
            $request = new Request();
            $this->assertSame([], $request->header());
            $this->assertNull($request->header('accept'));
        } finally {
            $_SERVER = $server;
        }
    }

    public function testCookieItem(): void
    {
        $this->assertSame('value', (new CookieItem('typing_test_cookie', 'value'))->getValue());
        $this->assertNull((new CookieItem('typing_test_absent_cookie'))->getValue());
    }

    public function testCacheStoresObjects(): void
    {
        $dirProp = new \ReflectionProperty(Cache::class, 'dir');
        $oldDir = $dirProp->isInitialized() ? $dirProp->getValue() : '';
        $dir = sys_get_temp_dir() . '/sf_cache_test_' . uniqid();
        mkdir($dir);
        $dirProp->setValue(null, $dir);
        try {
            $obj = new \ArrayObject(['x' => 1]);
            Cache::set('bugfix_obj', $obj);
            $this->assertEquals($obj, Cache::get('bugfix_obj'));
        } finally {
            array_map('unlink', glob("$dir/*"));
            rmdir($dir);
            $dirProp->setValue(null, $oldDir);
        }
    }
}
