<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- Initial -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/assets/meta/favicon/apple-touch-icon.png">
    <link rel="icon" href="/assets/meta/favicon/favicon.ico" sizes="any">
    <link rel="icon" href="/assets/meta/favicon/icon.svg" type="image/svg+xml">

    <!-- Theme -->
    <meta name="theme-color" content="#1975F7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="#1975F7">

    <!-- Fonts preload -->
    <link rel="preload" href="/assets/fonts/Inter/InterVariable.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/assets/fonts/Inter/InterVariable-Italic.ttf" as="font" type="font/ttf" crossorigin>

    <!-- Info data -->
    <link rel="manifest" href="/assets/meta/manifest.webmanifest">
    <link rel="yandex-tableau-widget" href="/assets/meta/tableau.json">

    <!--  Scripts  -->

    <!-- Canonical URL -->
    <link rel="canonical" href="<?= url(Simflex\Core\Container::getRequest()->getPath()) ?>"/>

    <!-- Main meta data -->
    <?php
    $content = App\Extensions\Content\Content::getStatic(Simflex\Core\Container::getRequest()->getPath());

    Simflex\Core\Page::$override['title'] = Simflex\Core\Page::$override['title'] ?: ($content['params']['meta_title'] ?: '');
    if (Simflex\Core\Page::$override['title']) {
        Simflex\Core\Page::$override['uses_meta'] = true;
    }

    Simflex\Core\Page::meta(); ?>
    <meta name="description"
          content="<?= Simflex\Core\Page::$override['description'] ?: $content['params']['meta_de'] ?>"/>
    <meta name="keywords"
          content="<?= Simflex\Core\Page::$override['keywords'] ?: $content['params']['meta_kw'] ?>"/>
    <meta name="robots" content="index, follow">

    <!-- Structured data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebPage",
            "name": "{site_name}",
            "description": "<?= Simflex\Core\Page::$override['description'] ?>",
            "url": "<?= url('') ?>",
            "logo": "<?= url('/assets/meta/favicon/icon.svg') ?>",
            "sameAs": [
                "<?= Simflex\Core\Core::siteParam('tg') ?>",
                "<?= Simflex\Core\Core::siteParam('vk') ?>",
                "<?= Simflex\Core\Core::siteParam('whats_app') ?>"
            ],
            "telephone": "{phone}",
            "email": "{email}",
            "openingHours": "{workhours}"
        }
    </script>

    <!-- Open Graph data -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= Simflex\Core\Page::$override['title'] ?>">
    <meta property="og:url" content="<?= url(Simflex\Core\Container::getRequest()->getPath()) ?>">
    <meta property="og:description" content="<?= Simflex\Core\Page::$override['description'] ?>">
    <meta property="og:site_name" content="{site_name}">
    <meta property="og:logo" content="<?= url('/assets/meta/favicon/icon-192.png') ?>">
    <meta property="og:locale" content="ru_RU"/>
    <meta property="og:image" content="<?= url('/assets/meta/og-image.png') ?>">
    <meta property="vk:image" content="<?= url('/assets/meta/og-image.png') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?= url('/assets/meta/og-image.png') ?>">

    <!-- Analytics tools (Yandex.Metrika etc.) -->

</head>
<body>
<?php
\Simflex\Core\Page::content(); ?>

<?php
\Simflex\Core\Page::metaJS(); ?>
</body>
</html>
