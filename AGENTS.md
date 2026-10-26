# Шаблон сайта Simflex CMS — правила для агентов

Это шаблон сайта: заготовка, на которой стартует разработка каждого сайта. Здесь же docker-окружение и автотесты
ядра и шаблона. Общие правила разработки CMS и устройство остальных репозиториев — в `AGENTS.md` репозитория
`simflex-agents`, локальные пути — в `.agents/local.md` (не в git).

## Устройство

- Namespace `App\` — корень репозитория (PSR-4). `config.php` — конфиг сайта (`App\Config`), классы реестров сайта:
  `Core/Privilege.php` (`privilegeClass`), `Admin/Menu.php` (`adminMenuClass`).
- `provider/` — объявления сайта: `components.php`, `modules.php`, `templates.php` (шаблоны страниц), `cron.php`,
  `routes.php`, `events.php`, `services.php`, `commands.php`.
- `Admin/Handlers/` и `Admin/handlers.php` — разделы админки сайта (создаются, когда нужны).
- `database/migrations/` — миграции с начальными данными (`$s->seed()`), `./sf migrate/create` нумерует новые.
- `public/` — корень веб-сервера (`public/index.php` — единственная точка входа, `public/assets` — статика сайта,
  `public/uf` — загруженные файлы).
- `Extensions/`, `Layout/`, `theme/` — код и вёрстка сайта; `functions.php` — функции сайта.
- `CHANGELOG.md` — журнал изменений шаблона.

## Окружение и тесты

- `docker compose` (сервисы `php`, `db`, `selenium`); консоль — `docker compose exec php ./sf <команда>`.
- Тесты Codeception: `docker compose exec -T php php vendor/bin/codecept run` (или `run Unit`, `run Acceptance`,
  `run Acceptance Admin/MenuCest`). Структура и правила — `docs/tests.md` документации.
  - `tests/Acceptance/` — сценарии в браузере (`Admin/` — админка), `tests/Unit/` — по подсистемам
    (`Core`, `Admin`, `Extensions`, `App`), `tests/Support/` — общие шаги (`AcceptanceTester`), `MockDb`,
    тестовые данные `Support/Data`.
  - Тест убирает за собой то, что создал (записи, файлы), в `finally`.
- Стиль кода: `.php-cs-fixer.php` (проверяет шаблон и ядро), `make cs-fix`.
- Для разработки ядра `composer.json` (path-репозиторий) и `docker-compose.yml` (том ядра) локально связывают
  шаблон с клоном ядра — эти правки не коммитятся.
