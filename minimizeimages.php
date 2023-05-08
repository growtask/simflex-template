<?php

# Скрипт сжимает все картинки на сайте для оптимизации
# denis@internet-menu.ru / 2017-09-25

header('Content-Type: text/html; charset=utf-8');
ini_set('iconv.internal_encoding', 'UTF-8');
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set('max_execution_time', 12000);

$root = $_SERVER['DOCUMENT_ROOT'];

//exec('cp Изолятор.png Изолятор2.png');
//exec('convert images/karta.jpg -sampling-factor 4:2:0 -strip -quality 85 -interlace JPEG -colorspace RGB images/karta.jpg');
//exec('convert Изолятор.png -strip -resize 170x170 Изолятор.png');
//$images = glob('./*.{jpeg,jpg,gif,png,JPEG,JPG,GIF,PNG}', GLOB_BRACE);
$dir = new RecursiveDirectoryIterator('./');
$it = new RecursiveIteratorIterator($dir);
$regex = new RegexIterator($it, '/^.+(.jpe?g|.png|.gif)$/i', RecursiveRegexIterator::GET_MATCH);
$limit = 5500000;
echo '<table cellpadding=3 border=1>';
//$regex = array(
//    './assets/template/smet/img/catalog/Иконка - Резка.png' => array('./assets/template/smet/img/catalog/Иконка%20-%20Резка.png','.png')
//    'опоры лэп.png' => array('опоры лэп.png','.png')
//);
foreach ($regex as $name => $r) {
    echo '<tr>';
    echo "<td>$name</td>";
    $bkfile = "$root/images_uncompressed/$name";
    @mkdir(dirname($bkfile), 0777, true);
    if (!file_exists($bkfile)) {
        copy($name, $bkfile);
    }
    $sizeBefore = round(filesize($name) / 1024, 2);
    echo "<td>$sizeBefore</td>";
    $resBefore = getimagesize($name);
    $ext = substr(strtolower($r[1]), 1);
    $resize = '';
    if ('png' == $ext || 'gif' == $ext) {
        $resize = '';//$resBefore[0] > 400 || $resBefore[0] < 170 ? '' : '-resize 170x170';
        exec("convert \"$name\" -strip $resize \"$name\"");
    }

    if ('jpeg' == $ext || 'jpg' == $ext) {
        exec("convert \"$name\" -sampling-factor 4:2:0 -strip -quality 85 -interlace JPEG -colorspace RGB \"$name\"");
    }
    usleep(10000);
    $sizeAfter = round(filesize($name) / 1024, 2);
    echo "<td>$sizeAfter</td>";
    $resizeText = '';
    if($resize){
        $resAfter = getimagesize($name);
        $resizeText = "{$resBefore[3]} &rarr; {$resAfter[3]}";
    }
    
    echo "<td><b>$resizeText</b></td>";
    echo '</tr>';
    if ($limit-- < 0) {
        break;
    }
}
echo '</table>';