<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php
        \App\Plugins\Jquery\Jquery::core();
        \Simflex\Core\Page::css('/theme/default/css/default.css');
        \Simflex\Core\Page::js('/theme/default/js/default.js');
        \Simflex\Core\Page::meta();
        ?>
    </head>
    <body>
        <?php \Simflex\Core\Page::position('content-before'); ?>
        <?php \Simflex\Core\Page::content(); ?>
        <?php \Simflex\Core\Page::position('content-after'); ?>

        <?php \Simflex\Core\Page::position('absolute'); ?>
    </body>
</html>
