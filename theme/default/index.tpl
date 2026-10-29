<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- Initial -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/assets/default/meta/favicon/apple-touch-icon.png">
    <link rel="icon" href="/assets/default/meta/favicon/favicon.ico" sizes="any">
    <link rel="icon" href="/assets/default/meta/favicon/icon.svg" type="image/svg+xml">

    <!-- Theme -->
    <meta name="theme-color" content="#1975F7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="#1975F7">

    <!-- Fonts preload -->
    <link rel="preload" href="/assets/default/fonts/Inter/InterVariable.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/assets/default/fonts/Inter/InterVariable-Italic.ttf" as="font" type="font/ttf" crossorigin>

    <!-- Info data -->
    <link rel="manifest" href="/assets/default/meta/manifest.webmanifest">
    <link rel="yandex-tableau-widget" href="/assets/default/meta/tableau.json">

    <!--  Scripts  -->

    <!-- Canonical URL -->
    <link rel="canonical" href="<?= url(Simflex\Core\System\Container::getRequest()->getPath()) ?>"/>

    <!-- Main meta data: title, description, keywords, стили -->
    <?= $page->getMeta() ?>
    <meta name="robots" content="index, follow">

    <!-- Structured data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebPage",
            "name": "{site_name}",
            "description": "<?= htmlspecialchars(Simflex\Core\Http\PageSeo::getDescription()) ?>",
            "url": "<?= url('') ?>",
            "logo": "<?= url('/assets/default/meta/favicon/icon.svg') ?>",
            "sameAs": [
                "<?= \Simflex\Core\System\Settings::get('tg') ?>",
                "<?= \Simflex\Core\System\Settings::get('vk') ?>",
                "<?= \Simflex\Core\System\Settings::get('whats_app') ?>"
            ],
            "telephone": "{phone}",
            "email": "{email}",
            "openingHours": "{workhours}"
        }
    </script>

    <!-- Open Graph data -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars(Simflex\Core\Http\PageSeo::getFullTitle()) ?>">
    <meta property="og:url" content="<?= url(Simflex\Core\System\Container::getRequest()->getPath()) ?>">
    <meta property="og:description" content="<?= htmlspecialchars(Simflex\Core\Http\PageSeo::getDescription()) ?>">
    <meta property="og:site_name" content="{site_name}">
    <meta property="og:logo" content="<?= url('/assets/default/meta/favicon/icon-192.png') ?>">
    <meta property="og:locale" content="ru_RU"/>
    <meta property="og:image" content="<?= url('/assets/default/meta/og-image.png') ?>">
    <meta property="vk:image" content="<?= url('/assets/default/meta/og-image.png') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?= url('/assets/default/meta/og-image.png') ?>">

    <!-- Analytics tools (Yandex.Metrika etc.) -->

</head>
<body>
<?= $page->getContent() ?>

<?= $page->getMetaJs() ?>
</body>
</html>
