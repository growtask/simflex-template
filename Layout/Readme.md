## Пример подключения компонента

```php
// /Extensions/Content/tpl/mainPage.tpl
<?php \App\Layout\Cards\Products1\Layout::draw([
        'title' => 'Test title',
        'rows' => [
            ['title' => 'Product1', 'description' => 'Desc', 'big_image' => 'zz'],
            ['title' => 'Product2', 'description' => 'Desc2', 'big_image' => 'zz2']
        ]]
) ?>
```

Все данные в компонент передаем в виде ассоциативного массива.  
В примере выше title и rows должы формироваться в компоненте/модуле Simplex CMS,
а получать их нужно из БД. Здесь лишь для пример данные переданы в явном виде.

## Добавить новый компонент

### Структура

```
Layout - корневая директория для всех компонентов
  Cards - группирующая директория (берем из Figma), например Cards/Products или Info/Numbers
    Product1 - директория компонента
      Layout.php - обязательный файл, класс компонента. Через него подключаются все файлы компонента.
                   По умолчанию создаем пустой наследуемый класс, пример ниже
      layout.tpl - обязательный файл, верстка
      style.css - не обязательно. Если есть стили, нужно добавить в файл с таким названием
      style.scss - не обязательно. Если есть SCSS стили, нужно добавить в файл с таким названием. Такие файлы скомпилируются автоматически
      script.js - не обязательно. Если есть js-код, нужно добавить в файл с таким названием
```

### Класс по умолчанию

Обязательно! `namespace` должен совпадать со структурой директорий.

```php
<?php

namespace App\Layout\Cards\Products1;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static $extraAssets = [];
}
```

**Для создания нового компонента можно скопировать Layout\Cards\Products1, и удалить лишнее.**

## Подключить дополнительные файлы/либы

В класс Layout добавляем:

```php
<?php

namespace App\Layout\Cards\Products1;

use App\Layout\LayoutBase;

class Layout extends LayoutBase
{
    public static $extraAssets = [
        // Можно сразу указать файл
        'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css',
        // Если подключаемый файл без разрешения, можно явно указать
        ['file' => 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/somefile', 'type' => 'css'],
        // idx - приоритет в подключаемых файлах, когда нужно выдержать порядок, 0 - выше всех, 100 - ниже всех
        ['file' => 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/somefile2.css', 'idx' => 50],
    ];
}
```

## Простейшие компоненты

Самые базовые компоненты (например, кнопки) не обязательно оформлять отдельной директорией.  
Для этого есть глобальные стили и скрипты:

```
/Layout/assetsGlobal/style.css
/Layout/assetsGlobal/script.js
```

### Перечень простейших компонентов

```html
<button class="layouts-btn layouts-btn-primary">Основная кнопка</button>

<button class="layouts-btn layouts-btn-secondary">Вторая кнопка</button>

...
```

