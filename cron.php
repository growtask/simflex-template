<?php


/**
 * Usage from CLI: php cron.php --cron_id={id}
 */

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/';
}
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'www.' . basename(__FILE__);
}
if (!isset($_SERVER['REMOTE_ADDR'])) {
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}
if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = __DIR__;
}

require_once 'Core/Init.php';

Init::loadConstants();
const SF_LOCATION = SF_LOCATION_CLI;

Init::_();

cliParamsToGET();

$cronId = (int)@$_GET['cron_id'];
$q = "
    SELECT * 
    FROM cron 
    WHERE active = 1" . ($cronId ? " AND id = $cronId" : '') . "
";
$jobs = \Simflex\Core\DB::assoc($q);
foreach ($jobs as $job) {
    if (!$cronId && !timingPassed($job['timing'])) {
        continue;
    }
    $action = $job['action'];
    $params = $job['cparams'];
    ob_start();
    if (
        ($class = $job['class_fqn'])
        && class_exists($class)
        && method_exists($class, $action)
    ) {
        $class::$action($params);
    }
    $result = str_replace("'", "\'", ob_get_clean());
    $q = "insert into cron_log set cron_id = {$job['id']}, datetime = now(), result = '$result'";
    \Simflex\Core\DB::query($q);
}

if (timingPassed('30 0 * * *')) {
    $q = "delete from cron_log where adddate(datetime,interval 2 month) < now()";
    \Simflex\Core\DB::query($q);
}

function cliParamsToGET()
{
    global $argc, $argv;
    if ($argc == 1) {
        return;
    }
    foreach (array_slice($argv, 1) as $arg) {
        if (strpos($arg, '--') !== 0) {
            echo 'error reading args';
            die;
        }
        $tmp = explode('=', trim($arg, '-'));
        $key = $tmp[0];
        $value = $tmp[1];
        $_GET[$key] = $value;
    }
}

function timingPassed($timing)
{
    # m h dom mon dow
    #$timing = "* 11-18 * * 1";
    $now = (int)date("i") . ' ' . (int)date("H") . ' ' . (int)date("d") . ' ' . (int)date("m") . ' ' . (int)date("w");
    $cronTime = explode(' ', $timing);

    $parts = array('i', 'H', 'd', 'm', 'w');
    foreach ($parts as $index => $dateParam) {
        $val = str_replace('*', (int)date($dateParam), $cronTime[$index]);
        $del = 0;
        if (strpos($val, '/') !== false) {
            $tmp = explode('/', $val);
            $val = $tmp[0];
            $del = $tmp[1];
        }

        $vals = explode(',', $val);
        $passed = false;
        $cur = (int)date($dateParam);
        foreach ($vals as $val) {
            if (strpos($val, '-') !== false) {
                $tmp = explode('-', $val);
                if ($tmp[0] <= $cur && $cur <= $tmp[1]) {
                    $val = $cur;
                }
            }

            $passed = (int)$val === $cur && (!$del || $del && $val % $del === 0);
            if ($passed) {
                break;
            }
        }
        $cronTime[$index] = $passed ? $cur : 'false';
    }

    $cronTimeStr = implode(' ', $cronTime);
    return $now == $cronTimeStr;
}
