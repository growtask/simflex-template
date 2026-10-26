<?php

/*
 * Функции сайта, подключаются при загрузке (Core/Simflex.php).
 * Функции ядра (env(), asset(), getSimflexVersion()) — в src/functions.php ядра
 */

/**
 * Полный URL
 * @param string $path Путь к ресурсу
 * @param array $get GET-параметры
 * @return string Полный URL
 */
function url(string $path, array $get = []): string
{
    $q = http_build_query($get);
    return ('http' . (!empty($_SERVER['HTTPS']) ? 's' : '')) . '://' . $_SERVER['HTTP_HOST'] . $path . ($q ? ('?' . $q) : '');
}

/**
 * Выводит SVG-иконку из спрайта
 * @param string $iconName Имя иконки
 * @param string $className Дополнительные CSS-классы
 * @return string HTML SVG со ссылкой на спрайт
 */
function renderIcon(string $iconName, string $className = ''): string
{
    return sprintf(
        '<svg class="%s" aria-hidden="true"><use href="/assets/icons/icons.svg#icon-%s"></use></svg>',
        htmlspecialchars($className),
        htmlspecialchars($iconName)
    );
}

/**
 * Собирает строку HTML-атрибутов из массива ключ => значение
 * @param array<string,mixed> $attributes HTML-атрибуты: ключ => значение
 * @return string Строка HTML-атрибутов с экранированными значениями
 */
function buildAttrs(array $attributes = []): string
{
    if (empty($attributes)) {
        return '';
    }

    return implode(
        ' ',
        array_map(
            fn($key, $value) => sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_QUOTES, 'UTF-8')),
            array_keys($attributes),
            $attributes
        )
    );
}
