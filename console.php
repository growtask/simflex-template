<?php
/**
 * Usage:
 * ./sf extName/actionName param1 paramN - params will passed as method params
 * Or ./sf extName/actionName --param1=value --paramN=value - params will passed as GET params.
 *    Also if method accepts params in long writings such as --param1=value will be passed to method params by their names.
 *    Example: ./sd test/testme --value=2 --key=1 will launch class ConsoleTest from /ext/test/consoletest.class.php
 *             with method actionTestme($key, $value) this way: (new ConsoleTest())->actionTestme(1,2); // not 2,1
 */
ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

error_reporting(E_ALL & ~E_DEPRECATED);

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

$sfArgv = cliParamsToGET();

$job = explode('/', @$argv[1]);
if (count($job) !== 2) {
    echo "Error!! example usage: php console.php ext/action\nOR: ./sf ext/action\n";
    exit;
}

if (strtolower($job[0]) == 'migrate') {
    $class = 'Simflex\Core\DB\Migrator';
} else {
    $class = 'App\Extensions\\' . ucfirst($job[0]) . '\Console' . ucfirst($job[0]);
}

$action = $job[1];
if (!class_exists($class)) {
    sfCliExit(1, "class $class not found");
}
if ($class instanceof \Simflex\Core\ConsoleBase) {
    sfCliExit(1, "class $class is not console controller");
}
$handler = new $class();
if (!method_exists($handler, $action)) {
    sfCliExit(1, "method $action not found");
}
$paramValues = $sfArgv;
$reflection = new ReflectionMethod($handler, $action);
if ($actionParams = $reflection->getParameters()) {
    if (count($paramValues) < count($actionParams)) {
        foreach ($actionParams as $actionParam) {
            $paramName = $actionParam->getName();
            if (array_key_exists($paramName, $_GET)) {
                $paramValues[$actionParam->getPosition()] = $_GET[$paramName];
            } else {
                try {
                    $default = $actionParam->getDefaultValue();
                    $paramValues[$actionParam->getPosition()] = $default;
                } catch (\Throwable $ex) {}
            }
        }
    }
    if (count($paramValues) < $reflection->getNumberOfRequiredParameters()) {
        sfCliExit(1, "not enough params");
    }
}
$handler->$action(...$paramValues);


function cliParamsToGET(): array
{
    global $argc, $argv;
    if ($argc == 1) {
        return [];
    }
    $sfArgv = array_slice($argv, 2);
    foreach ($sfArgv as $index => $arg) {
        if (strpos($arg, '--') !== 0) {
            continue;
        }
        $tmp = explode('=', trim($arg, '-'));
        $key = $tmp[0];
        $value = $tmp[1];
        $_GET[$key] = $value;
        unset($sfArgv[$index]);
    }
    return $sfArgv;
}

function sfCliExit($code, $message = '')
{
    if ($message) {
        echo "$message\n";
    }
    exit($code);
}