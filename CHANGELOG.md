# Журнал изменений

Все заметные изменения шаблона сайта Simflex CMS (`simflex-template`). Формат —
[Keep a Changelog](https://keepachangelog.com/ru/1.1.0/), версии — [SemVer](https://semver.org/lang/ru/).
Журнал ведётся с версии 4.0. Изменения ядра — в `CHANGELOG.md` пакета `growtask/simflex`
(`vendor/growtask/simflex/CHANGELOG.md`).

## [4.0.0] — не выпущена

Изменения относительно 3.6.2. Шаблон переведён на ядро 4.0: структура админки, меню, привилегии, шаблоны страниц,
компоненты, модули и задачи cron объявляются в коде сайта, а не в базе.

### Несовместимые изменения

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

### Изменено

- `./sf layout/generate` печатает код полей компонентов страницы для `provider/templates.php` вместо записи в БД
  и создаёт компонент `Content` с зависимостями.
- `functions.php` — только функции сайта (`url()`, `renderIcon()`, `buildAttrs()`); `env()`, `asset()`,
  `getSimflexVersion()` — в ядре.
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
