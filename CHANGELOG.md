# Журнал изменений

Все заметные изменения шаблона сайта Simflex CMS (`simflex-template`). Формат —
[Keep a Changelog](https://keepachangelog.com/ru/1.1.0/), версии — [SemVer](https://semver.org/lang/ru/).
Журнал ведётся с версии 4.0. Изменения ядра — в `CHANGELOG.md` пакета `growtask/simflex-core`
(`vendor/growtask/simflex-core/CHANGELOG.md`).

## [4.0.0] — не выпущена

- Telegram API поддерживает необязательный прокси: `telegram['proxy']` (в шаблоне — `TELEGRAM_PROXY`). Без значения используется прямое подключение.

Изменения относительно 3.6.2. Шаблон переведён на ядро 4.0: структура админки, меню, привилегии, шаблоны страниц,
компоненты, модули и задачи cron объявляются в коде сайта, а не в базе.

### Несовместимые изменения

- **`ExtensionConfig`:** в `provider/extensions/*.php` свойство `$handlers` → `$adminHandlers`.
- **`time_create`, `time_update`:** миграции таблиц сущностей (пользователи, роли, меню, модули, страницы, настройки, задачи cron, SEO, виджеты) получили колонки автоматически (`createTable()`), таблицы связей, кэшей и журналов — `noTimestamps()`; в `user_auth` колонка `time_create` стала общей.
- **Пути без конечного «/»:** миграции страниц контента (`/catalog`, `/cart`, `/user`, `/blog` и др.) и тесты; адрес со слэшем на конце — 404.
- **Ядро — пакет `growtask/simflex-core`** (было `growtask/simflex`): `"growtask/simflex-core": "4.0.x-dev"`
  в `composer.json`.
- **PHP 8.4:** образ `php:8.4-apache-bookworm`, `"php": "^8.4"` в `composer.json`.
- **Корень веб-сервера — `public/`:** единственная точка входа `public/index.php` (сайт, админка и файловый
  менеджер `{adminPath}/fm`), статика сайта — `public/assets/<тема>` (`default`), загруженные файлы — `public/uf`. Удалены `index.php`,
  `Admin/index.php`, `fm.php` в корне; `.htaccess` перенесён в `public/`.
- **Миграции без структуры в базе:** удалены миграции `struct_*`, `component`, `component_param`, `module`,
  `module_param`, `admin_menu`, `content_template`, `content_template_param`.
- **Сидеров больше нет:** `database/seeders` удалён, начальные данные пишет `$s->seed()` в миграции, создающей
  таблицу (роли — `02_user_role`, их привилегии — `03_user_role_privilege` и т.д.); таблица `seeder` и запуск
  сидеров при установке и `composer update` убраны.
- **Настройки расширений** — `provider/extensions/` (было `provider/extension/`).
- **Привилегии по имени:** `user_role`, `user_role_privilege`, `user_privilege_personal`, `admin_widget` хранят
  имя привилегии (колонка `privilege`); у ролей нет `npp`.

### Добавлено

- Реальные ресурсы из Figma вместо заглушек: знаки бренда `public/assets/default/images/brand/` (`logo`, `made-in`, `made-by` и варианты `*-dark`), картинки баннера и блока r-form `public/assets/default/images/banner/` (webp до 10 КБ; сидер `19_content.php` ссылается на них; водяной знак макета в картинку формы не попал, взят его градиент), иконки `icon-error` и `icon-alert` в `icons.svg` выгружены из Figma.
- Иконки соцсетей в `public/assets/default/icons/icons.svg` по Figma (наборы `social`, `social-monochrome`): `icon-social-<сеть>`
  (цветные) и `icon-social-monochrome-<сеть>` (`currentColor`) для figma, google, instagram, max, rutube, telegram,
  vkontakte, whatsapp, yandex, yandex-zen, youtube; их использует компонент `Social`. Старые `icon-social-mono-*` оставлены.
- Шапка сайта: ключи настроек `header_banner_text`, `header_banner_button`, `header_banner_link` (строка над шапкой,
  `28_settings.php`), вёрстка модуля «Меню» (`Extensions/Menu/tpl`: `Tab` и `DropdownTab`), раздел `header`
  в `public/assets/default/css/site.css` (поля шапки 250 / 45 / 16).
- Стили блоков `fs` (баннер) и `features` (преимущества, сетка 4 / 4 / 2 / 1) и поля страницы `--page-gutter`
  в `public/assets/default/css/site.css`.
- Подвал: раздел `footer` в `public/assets/default/css/site.css` (поля 250 / 45 / 16 / 16, нижний отступ
  `--footer-indent-bottom`: 80 на fullhd и hd, 150 на tablet и mobile как в макете — место под нижнюю панель
  расширений каталога и магазина).
- Миграция `23_menu.php`: колонка `place` (`main` / `footer`) для меню подвала; шаблон сидирует только «Главная»,
  колонки подвала (Информация, FAQ, Правовая информация, Контакты) добавят страницы, когда они появятся в шаблоне,
  и расширения своими миграциями.
- Спрайт `public/assets/default/icons/icons.svg`: иконки `error` и `alert`. Отдельный css сайта
  `public/assets/default/css/site.css` (заготовка для расстановки блоков), подключён в `theme/default/index.tpl`
  после стилей компонентов.
- `provider/components.php`, `provider/modules.php` — компоненты и модули сайта; `provider/cron.php` — задачи cron;
  `provider/templates.php` — шаблоны страниц и их поля (шаблон «Главная»).
- `App\Core\Privilege` — привилегии сайта (`manager`), `config.php`: `privilegeClass`.
- `App\Admin\Menu` — меню админки сайта поверх меню ядра, `config.php`: `adminMenuClass`.
- Разделы админки сайта — классы в `Admin/Handlers/`, реестр — `Admin/handlers.php` (создаются, когда нужны;
  см. документацию «Новый раздел в админке», «Подмена обработчика раздела ядра»).
- Адрес админки из окружения: `ADMIN_PATH`.
- Модели формы обратной связи `ModelCallback`, `ModelCallbackEmail`, `ModelCatalogProduct`.
- Тесты Codeception по подсистемам: acceptance — админка (вход, адрес, меню, привилегии, роли и пользователи,
  CRUD, действия и фильтры списка, «Редактор страниц» с загрузкой изображения, модули и компоненты, cron), сайт,
  доступность только `public/`; unit — ядро (запросы, схема, хелперы, вывод, почта, sms, картинки), админка,
  расширения, код шаблона. Общие шаги в `AcceptanceTester`, `MockDb` для unit-тестов без БД.
- `.php-cs-fixer.php` и `make cs-fix` — проверка стиля ядра и шаблона (в том числе лишних `use`).
- PHPStan (`phpstan.neon`, `make phpstan`) — статический анализ шаблона, ядра и тестов.
- **Приём формы «Связаться с нами».** `POST /form/callback` (`provider/routes.php`, компонент
  `Extensions/Form/Components/CallbackForm`, обработчик `Extensions/Form/CallbackHandler`): проверка на сервере
  (имя, телефон, почта, комментарий до 300 знаков, согласие обязательно), защита от ботов (`FormGuard` ядра),
  отправка по настроенным каналам — почта `form_email` и Telegram `form_tg_token` + `form_tg_chat_id`
  (`Extensions/Form/Notifiers`, каналы подменяются в тестах). Без настроенных каналов заявка пишется в журнал.
- **Блок `r-form`** в `public/assets/default/css/site.css`: рамка, форма слева, картинка справа, вертикально
  на tablet и mobile.
- Тесты: `tests/Unit/App/CallbackHandlerTest.php`, `tests/Unit/Layout/FormComponentsTest.php`,
  `tests/Acceptance/FormCallbackCest.php` (работает на главной, когда в ней есть блок `.r-form` и окно `#callback`).

### Изменено

- Сверка главной с Figma: `site.css` — размеры mobile в `vw` пересчитаны на базу 414; `r-form` — две равные колонки
  (650 + 650), карточка и картинка высотой 638, на mobile рамка во всю ширину, заголовок tablet и mobile 24;
  окно «Связаться с нами» — размер `s` (560) с пояснением (`theme/default/index.tpl`); картинка `r-form` в обёртке.

- `./sf layout/generate` печатает код полей компонентов страницы для `provider/templates.php` вместо записи в БД.
- **Layout — в ядре** (`Simflex\Core\Layout`): `Layout/LayoutBase.php`, `LayoutManager.php`, компоненты, `assetsGlobal`
  и команда `layout` (`Extensions/Layout`) удалены из шаблона. `Layout/` сайта — для своих компонентов (`App\Layout`)
  и переопределения глобальных стилей (`Layout/assetsGlobal`).
- `functions.php` — пустой файл для функций сайта; `url()`, `renderIcon()`, `buildAttrs()`, как и `env()`,
  `asset()`, `getSimflexVersion()`, — в ядре.
- `provider/commands.php` — только команды сайта: `migrate`, `layout` и `cron` объявляет ядро.
- **Загрузка сайта — в ядре:** `Core/Simflex.php`, `console.php`, `cron.php`, `constants.php` удалены.
  `public/index.php` и консоль `sf` (теперь PHP-скрипт) подключают автозагрузчик и создают ядро с корнем сайта:
  `new \Simflex\Core\Kernel(dirname(__DIR__))->runHttp()`, `new \Simflex\Core\Kernel(__DIR__)->runConsole()`. Пути
  и окружение — `Container::getPaths()`, `Container::getLocation()` вместо `SF_*`. Cron: `./sf cron/run` вместо
  `php cron.php`, `--job=<key>` — одна задача сейчас.
- Тема сайта: страница — переменная `$page` (`$page->getMeta()` — заголовок, description, keywords и стили,
  `getContent()`, `getMetaJs()`), настройки сайта — `System\Settings::get()`; `config.php` не задаёт `files`
  (по умолчанию — `provider/*.php`).
- Модели формы без префикса `Model`: `Extensions\Form\Models\Callback`, `CallbackEmail`, `CatalogProduct`;
  `AjaxForm` читает заявку из `Request`.
- **Docker: сервис `php` → `app`** (`docker compose exec app ...`, адрес сайта для acceptance-тестов —
  `http://app/`); том `/var/www:/var/www` убран; порт сайта на хосте — `APP_PORT` в `.env` (по умолчанию 480).
  `db` и `selenium` — в профиле `dev`, его включает `COMPOSE_PROFILES=dev` в `.env`: на сервере (`COMPOSE_PROFILES=`
  или `cron`) `docker compose up -d` запускает только `app` и, по желанию, `cron`.
- Тесты задают запрос `Request::create()`, а не суперглобальными массивами; `ApplicationTest` → `KernelTest`.
- Сервис `cron` в `docker-compose.yml` (профиль `cron`): `./sf cron/run` в начале каждой минуты.
- Модуль «Меню» — из ядра: `App\Extensions\Menu\Menu` — пустой наследник `Simflex\Core\Extensions\Menu\Menu` для своей
  вёрстки, добавлен в `provider/modules.php`.
- Пример контроллера `Extensions/Example` и маршрут `/example` удалены: как сделать компонент и контроллер —
  в документации («Компоненты и модули»).
- Модули «Код» и «Текстовый блок» — из ядра (`Simflex\Core\Extensions\Code`, `Simflex\Core\Extensions\Block`).
- Компоненты и модули сайта возвращают HTML строкой вместо `echo`.
- Комментарии в коде — на русском.
- `docker-compose.yml` без устаревшего `version`.
- `config.php` не собирает объявления расширений сам (`loadExtensions()`, `loadFiles()`, кэш `cache/extensions.php`,
  `cache/files.php`, `CONFIG_DEBUG` убраны): маршруты, события, сервисы и команды расширений объединяет
  `ConfigBase` ядра — сервисы расширения, которых нет у сайта, и слушатели одного события из расширения и сайта
  больше не теряются.

### Исправлено

- Миграции `05_user_privilege_personal` и `06_user_auth`: внешний ключ на `user` с `ON DELETE CASCADE` — пользователь
  с токеном «запомнить меня» или личной привилегией не удалялся.
- Имя класса `App\Extensions\Install\PackageInstaller`: composer-скрипты не находили его.
- Закрыта отдача каталогов `.git` и `.idea` по HTTP.
