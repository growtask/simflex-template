# Журнал изменений

Все заметные изменения шаблона сайта Simflex CMS (`simflex-template`). Формат —
[Keep a Changelog](https://keepachangelog.com/ru/1.1.0/), версии — [SemVer](https://semver.org/lang/ru/).
Журнал ведётся с версии 4.0. Изменения ядра — в `CHANGELOG.md` пакета `growtask/simflex-core`
(`vendor/growtask/simflex-core/CHANGELOG.md`).

## [4.0.0] — не выпущена

Изменения относительно 3.6.2. Шаблон переведён на ядро 4.0: структура админки, меню, привилегии, шаблоны страниц,
компоненты, модули и задачи cron объявляются в коде сайта, а не в базе.

### Несовместимые изменения

- **Ядро — пакет `growtask/simflex-core`** (было `growtask/simflex`): `"growtask/simflex-core": "4.0.x-dev"`
  в `composer.json`.
- **PHP 8.4:** образ `php:8.4-apache-bookworm`, `"php": "^8.4"` в `composer.json`.
- **Корень веб-сервера — `public/`:** единственная точка входа `public/index.php` (сайт, админка и файловый
  менеджер `{adminPath}/fm`), статика сайта — `public/assets`, загруженные файлы — `public/uf`. Удалены `index.php`,
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

### Изменено

- `./sf layout/generate` печатает код полей компонентов страницы для `provider/templates.php` вместо записи в БД.
- **Layout — в ядре** (`Simflex\Layout`): `Layout/LayoutBase.php`, `LayoutManager.php`, компоненты, `assetsGlobal`
  и команда `layout` (`Extensions/Layout`) удалены из шаблона. `Layout/` сайта — для своих компонентов (`App\Layout`)
  и переопределения глобальных стилей (`Layout/assetsGlobal`).
- `functions.php` — пустой файл для функций сайта; `url()`, `renderIcon()`, `buildAttrs()`, как и `env()`,
  `asset()`, `getSimflexVersion()`, — в ядре.
- `provider/commands.php` — только команды сайта: `migrate`, `layout` и `cron` объявляет ядро.
- **Загрузка сайта — в ядре:** `Core/Simflex.php`, `console.php`, `cron.php` удалены. `public/index.php` и консоль `sf`
  (теперь PHP-скрипт) подключают `constants.php`, автозагрузчик и вызывают `\Simflex\Application::runHttp()`
  / `runConsole()`. Cron: `./sf cron/run` вместо `php cron.php`, `--job=<key>` — одна задача сейчас.
- Тема сайта выводит заголовок, description и keywords через `Page::meta()` (`PageSeo`), настройки сайта —
  `SiteSettings::get()`; `config.php` не задаёт `files` (по умолчанию — `provider/*.php`).
- Сервис `cron` в `docker-compose.yml` (профиль `cron`): `./sf cron/run` в начале каждой минуты.
- Модуль «Меню» — из ядра: `App\Extensions\Menu\Menu` — пустой наследник `Simflex\Extensions\Menu\Menu` для своей
  вёрстки, добавлен в `provider/modules.php`.
- Пример контроллера `Extensions/Example` и маршрут `/example` удалены: как сделать компонент и контроллер —
  в документации («Компоненты и модули»).
- Модули «Код» и «Текстовый блок» — из ядра (`Simflex\Extensions\Code`, `Simflex\Extensions\Block`).
- Компоненты и модули сайта возвращают HTML строкой вместо `echo`.
- Комментарии в коде — на русском.
- `docker-compose.yml` без устаревшего `version`.
- `config.php` не собирает объявления расширений сам (`loadExtensions()`, `loadFiles()`, кэш `cache/extensions.php`,
  `cache/files.php`, `CONFIG_DEBUG` убраны): маршруты, события, сервисы и команды расширений объединяет
  `ConfigBase` ядра — сервисы расширения, которых нет у сайта, и слушатели одного события из расширения и сайта
  больше не теряются.

### Исправлено

- Имя класса `App\Extensions\Install\PackageInstaller`: composer-скрипты не находили его.
- Закрыта отдача каталогов `.git` и `.idea` по HTTP.
