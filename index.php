<?php

require_once 'Core/Init.php';

Init::loadConstants();
const SF_LOCATION = SF_LOCATION_SITE;

Init::_();

ob_start();
\Simflex\Core\Core::execute();
$html = ob_get_clean();
$siteParams = \Simflex\Core\Core::siteParam();
foreach ($siteParams as $paramAlias => $paramValue) {
    $html = str_replace('{' . $paramAlias . '}', $paramValue, $html);
}
$html = str_replace('{year}', date('Y'), $html);
preg_match_all("@\{position_([a-z_\-]+)\}@Ui", $html, $matches);
if (!empty($matches[1])) {
    foreach ($matches[1] as $pos) {
        ob_start();
        \Simflex\Core\Page::position($pos);
        $ppp = ob_get_clean();
        $html = str_replace("{position_$pos}", $ppp, $html);
    }
}

echo sanitizeOutput($html);
