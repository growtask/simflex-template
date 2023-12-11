<?php
/*
 * Simplex Framework core functions
 * Loads from Init.php - no need dedicated include
 *
 */

/**
 * @param string $name
 * @param mixed|null $defaultValue
 * @return mixed|null
 */
function env(string $name, $defaultValue = null)
{
    return array_key_exists($name, $_ENV) ? $_ENV[$name] : $defaultValue;
}

function remove_root(string $path)
{
    return str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
}

function asset(string $path, bool $siteOnly = false)
{
    if (!$siteOnly && SF_LOCATION == SF_LOCATION_ADMIN) {
        return remove_root(SF_CORE_ROOT_PATH) . '/Admin/theme/new/' . $path;
    }

    return '/theme/default/' . $path;
}

function url(string $path, array $get = [])
{
    $q = http_build_query($get);
    return ('http' . ($_SERVER['HTTPS'] ? 's' : '')) . '://' . $_SERVER['HTTP_HOST'] . $path . ($q ? ('?' . $q) : '');
}

function debug($var)
{
    $onHttp = SF_LOCATION == SF_LOCATION_SITE || SF_LOCATION == SF_LOCATION_API;
    if (is_array($var)) {
        if ($onHttp) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        } else {
            print_r($var);
        }
    } else {
        if ($onHttp) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        } else {
            var_dump($var);
        }
    }
    exit;
}

function actlog(string $msg)
{
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $time = date('H:i:s d.m.Y');
    $log = "$time [ $ip ] $_SERVER[REQUEST_METHOD] $_SERVER[REQUEST_URI] - $msg\n";
    file_put_contents(SF_ROOT_PATH . '/uf/_act.log', $log, FILE_APPEND);
}

function imDev()
{
    if (isset($_GET['setimdev'])) {
        setcookie('imdev', 1, time() + 60 * 60 * 24 * 30, '/');
        header("location: ./");
        exit;
    }
    $ret = !empty($_COOKIE['imdev']);
    $ret |= $_SERVER['REMOTE_ADDR'] == '127.0.0.1';
    $ret |= strpos($_SERVER['REMOTE_ADDR'], '192.168') === 0;
    $ret |= explode(':', $_SERVER['HTTP_HOST'])[0] == 'localhost';
    return $ret;
}

function barf($f, $l, $debug = false, $printTrace = false)
{
    if (imDev()) {
        $d = &$_ENV['debug_barf'];
        $s = &$_ENV['debug_start'];
        empty($d) && $d = [];
        $di = [];
        $di['line'] = $l;
        $di['file'] = $f;
        $di['time'] = microtime(true) - $s['time'];
        $di['m0'] = round((memory_get_usage() - $s['m0']) / 1024);
        $di['m1'] = round((memory_get_usage(true) - $s['m1']) / 1024);
        $trace = '';
        if ($printTrace) {
            ob_start();
            debug_print_backtrace();
            $trace = nl2br(ob_get_clean());
        }
        $di['debug'] = $debug . $trace;
        $d[] = $di;
    }
}

function barfOut()
{
    if (imDev()) {
        echo '<table cellpadding=3 border=1>';
        foreach ($_ENV['debug_barf'] as $row) {
            echo '<tr>';
            echo '<td>' . $row['file'] . '</td>';
            echo '<td>' . $row['line'] . '</td>';
            echo '<td>' . round($row['time'], 3) . '</td>';
            echo '<td>' . $row['m0'] . '</td>';
            echo '<td>' . $row['m1'] . '</td>';
            echo '<td>' . var_export($row['debug'], true) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        die;
    }
}

function sanitizeOutput($buffer)
{
    if (imDev()) {
        return $buffer;
    }
    $search = array(
        '/\>[^\S ]+/s', // strip whitespaces after tags, except space
        '/[^\S ]+\</s', // strip whitespaces before tags, except space
        '/(\s)+/s', // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    );
    $replace = array(
        '>',
        '<',
        '\\1',
        ''
    );
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}