<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Cache;
use Simflex\Core\Cookie\CookieItem;
use Simflex\Core\Helpers\DatetimeHelper;
use Simflex\Core\Helpers\Str;
use Simflex\Core\Request;
use Simflex\Core\Time;
use Simflex\Core\TimeDiff;

/**
 * Strings, dates, the request, the cookies, the cache
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
        $ret = Time::form_dates('2020-01-01', '2020-06-30', 2);
        $this->assertSame('1 квартал 2020', $ret[0]['period']);
        $this->assertSame('2020-01-01 00:00:00', $ret[0]['beg']);
        $this->assertSame('2020-03-31 23:59:59', $ret[0]['end']);
        $this->assertSame('2 квартал 2020', $ret[1]['period']);
        $this->assertSame('2020-04-01 00:00:00', $ret[1]['beg']);

        $ret = Time::form_dates('2020-11-01', '2021-01-15', 1);
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
