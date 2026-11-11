<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Core\Admin\Page;
use Simflex\Core\Admin\Fields\FieldBool;
use Simflex\Core\Admin\Fields\FieldDateTime;
use Simflex\Core\Admin\Fields\FieldInt;
use Simflex\Core\Admin\Fields\FieldMultiKey;
use Simflex\Core\Admin\Fields\FieldString;
use Simflex\Core\Extensions\Alert\Alert;
use Simflex\Core\Admin\Table\Presets\MultiKeyField;
use Simflex\Core\Admin\Table\Repository;
use Simflex\Core\Admin\Table\Table;
use Simflex\Core\Http\Request;
use Simflex\Core\System\Container;
use Simflex\Core\System\ModelBase;
use Tests\Support\AlertState;
use Tests\Support\MockDb;

/**
 * Раздел-таблица (Table\Table): групповое сохранение, валидация, сведения о записи
 */
class TableTest extends Unit
{
    use MockDb;

    private array $alertBackup = [];
    private mixed $requestBackup = null;

    protected function _before(): void
    {
        $this->alertBackup = AlertState::get();
        $this->requestBackup = Container::getRequest();
        $this->setRequest();
        AlertState::set([]);
        // Session::set() пытается начать сессию, но в CLI заголовки уже отправлены: глушим только это
        set_error_handler(
            static fn(int $no, string $msg) => str_contains($msg, 'ession') || str_contains($msg, 'headers'),
            E_WARNING | E_NOTICE
        );
    }

    protected function _after(): void
    {
        restore_error_handler();
        AlertState::set($this->alertBackup);
        Container::set('request', $this->requestBackup);
        Container::set('page', new Page());
    }

    /**
     * Запрос раздела: раздел читает параметры из запроса в контейнере при создании
     */
    private function setRequest(array $get = [], array $post = [], array $files = []): void
    {
        Container::set('request', Request::create('/admin/test', $get, $post, $files));
    }

    public function testIdsAreNormalized(): void
    {
        $this->setRequest(['ids' => '1,0,2,invalid,3']);
        $this->assertSame('1,2,3', $this->makeTable()->ids);
        $this->setRequest(['ids' => '1,0,2,invalid,3'], ['group_ids' => '4,5']);
        $this->assertSame('4,5', $this->makeTable()->ids);
        $this->setRequest();
        $this->assertSame('', $this->makeTable()->ids);
    }

    public function testSaveGroup(): void
    {
        $db = $this->mockDb();
        $db->expects($this->once())->method('query')
            ->with(
                // через модель: одно обновление всех выбранных записей, значения — параметрами запроса
                $this->stringContains('update `test_table` set `title` = :set_0 WHERE `id` IN ('),
                $this->callback(fn(array $binds) => array_values($binds) === ['New', 1, 2])
            )
            ->willReturn($this->createMock(\PDOStatement::class));
        $this->setRequest(post: ['group_ids' => '1,2', 'set' => ['title' => 1], 'title' => 'New']);

        $this->assertSame('ids', $this->makeTable(['title' => $this->makeField('title')])->saveGroup());
        $this->assertNotEmpty(AlertState::get()['']['success'] ?? null);
        $this->assertFalse(Alert::hasErrors());
    }

    public function testSaveGroupReportsFailedQuery(): void
    {
        $this->mockDb()->method('query')->willReturn(false);
        $this->setRequest(post: ['group_ids' => '1', 'set' => ['title' => 1], 'title' => 'New']);

        $this->makeTable(['title' => $this->makeField('title')])->saveGroup();
        $this->assertTrue(Alert::hasErrors());
    }

    public function testValidateWithoutAjax(): void
    {
        Container::set('page', new Page());
        $this->setRequest(post: ['first' => '', 'second' => 'ok']);
        $this->assertFalse($this->makeTable([
            'first' => $this->makeField('first', true),
            'second' => $this->makeField('second', true),
        ])->isValid());
        $this->assertTrue($this->makeTable(['second' => $this->makeField('second', true)])->isValid());
        $this->assertTrue($this->makeTable()->isValid());
    }

    public function testShowDetailGetRowDoesNotCreateDynamicProperty(): void
    {
        $db = $this->mockDb();
        $db->method('query')->willReturn(true);
        $db->method('fetch')->willReturn(['id' => 5]);
        $base = $this->makeTable();
        set_error_handler(static function (int $no, string $msg) {
            throw new \ErrorException($msg, 0, $no);
        }, E_DEPRECATED);
        try {
            $row = (new \ReflectionMethod($base, 'showDetailGetRow'))->invoke($base, 5);
        } finally {
            restore_error_handler();
        }
        $this->assertSame(['id' => 5], $row);
        $this->assertArrayNotHasKey('vfields', get_object_vars($base));
    }

    public function testFormShowsExtraHtml(): void
    {
        // форма выводит page-head: адреса админки и иконки берутся из конфига
        $configBackup = Container::get('config');
        Container::set('config', (object)['theme' => 'default', 'devMode' => false, 'adminPath' => '/admin']);
        $this->mockRows([]);
        $table = $this->makeTable(['title' => $this->makeField('title')]);
        $table->extraLeft = '<div id="extra-left"></div>';
        $table->extraRight = '<div id="extra-right"></div>';

        $html = $table->form();
        $this->assertStringContainsString('<div id="extra-left"></div>', $html);
        // правая колонка показывается для дополнительного HTML, даже если полей в ней нет
        $this->assertStringContainsString('edit-aside', $html);
        $this->assertStringContainsString('<div id="extra-right"></div>', $html);
        Container::set('config', $configBackup);
    }

    public function testFormTabsAndSections(): void
    {
        $configBackup = Container::get('config');
        Container::set('config', (object)['theme' => 'default', 'devMode' => false, 'adminPath' => '/admin']);
        try {
            $this->mockRows([]);
            $title = $this->makeField('title', true);
            $seo = $this->makeField('seo_title');
            $seo->params['tab'] = 'SEO';
            $seo->params['pos_group'] = 'Мета';
            $html = $this->makeTable(['title' => $title, 'seo_title' => $seo])->form();

            // поля без вкладки — «Основное», вкладки видны, когда их больше одной
            $this->assertStringContainsString('role="tablist"', $html);
            $this->assertStringContainsString('data-sf-tab-button="tab-osnovnoe"', $html);
            $this->assertStringContainsString('data-sf-tab-button="tab-seo"', $html);
            $this->assertMatchesRegularExpression('/data-sf-tab="tab-seo"[^>]*>.*Мета/s', $html);
            $this->assertStringContainsString('form-input__required', $html);
            $this->assertStringContainsString('class="edit-bar', $html);

            // одна вкладка — без переключателя
            $this->mockRows([]);
            $html = $this->makeTable(['title' => $this->makeField('title')])->form();
            $this->assertStringNotContainsString('role="tablist"', $html);
        } finally {
            Container::set('config', $configBackup);
        }
    }

    public function testInsertSavesRelationsAfterInsert(): void
    {
        $db = $this->mockRows([['id' => 7, 'title' => 'New']], $queries);
        $db->method('insertId')->willReturn('7');
        $this->setRequest(post: ['title' => 'New', 'tags' => '3']);
        $tags = new FieldMultiKey(Repository::toFieldRow(
            new MultiKeyField(name: 'tags', label: 'Теги', values: 'tag', relations: 'test_tag', key: 'tag_id'),
            'test_table',
        ));
        $tags->tablePk = 'id';

        $this->assertSame(7, $this->makeTable(['title' => $this->makeField('title'), 'tags' => $tags])->save());
        $sql = array_column($queries, 0);
        // связи пишутся только после вставки записи, по её id
        $this->assertStringContainsString('insert  into `test_table`', $sql[0]);
        $this->assertStringStartsWith('DELETE FROM `test_tag` WHERE `id` = :', $queries[2][0]);
        $this->assertSame([7], array_values($queries[2][1]));
        $this->assertSame(['INSERT INTO `test_tag` (`id`, `tag_id`) VALUES (?, ?)', [7, 3]], $queries[3]);
        $this->assertCount(4, $queries);
        $this->assertFalse(Alert::hasErrors());
    }

    public function testUploadTableFileRejectsOtherFiles(): void
    {
        $this->assertSame('', $this->makeTable()->uploadTableFile());
        // расширение не из белого списка — файл не сохраняется
        $this->setRequest(files: ['file' => ['name' => 'shell.php', 'tmp_name' => __FILE__, 'error' => UPLOAD_ERR_OK]]);
        $this->assertSame('', $this->makeTable()->uploadTableFile());
        $this->setRequest(files: ['file' => ['name' => 'photo.png', 'tmp_name' => '', 'error' => UPLOAD_ERR_NO_FILE]]);
        $this->assertSame('', $this->makeTable()->uploadTableFile());
    }

    public function testUploadPathIsSanitized(): void
    {
        $method = new \ReflectionMethod(Table::class, 'getUploadPath');
        $table = $this->makeTable();
        $this->assertSame('content/', $method->invoke($table, '/content/'));
        $this->assertSame('catalog/2024/', $method->invoke($table, 'catalog/2024'));
        $this->assertSame('', $method->invoke($table, ''));
        // выход из /uf и посторонние символы не принимаются
        $this->assertNull($method->invoke($table, '../../public'));
        $this->assertNull($method->invoke($table, 'content/../..'));
        $this->assertNull($method->invoke($table, 'a b'));
    }

    public function testForbiddenExtensionsAreNotSaved(): void
    {
        $this->assertContains('php', \Simflex\Core\Media\File::FORBIDDEN_EXTENSIONS);
        $this->assertContains('phtml', \Simflex\Core\Media\File::FORBIDDEN_EXTENSIONS);
        $this->assertContains('htaccess', \Simflex\Core\Media\File::FORBIDDEN_EXTENSIONS);
    }

    public function testListSearchAndAppliedFilters(): void
    {
        $title = $this->makeField('title');
        $title->filter = true;
        $title->isVisible = true;
        $hidden = $this->makeField('hidden');
        $hidden->filter = true;
        $hidden->isVisible = false;
        $table = $this->makeTable(['title' => $title, 'hidden' => $hidden]);

        // поиск — по видимым текстовым колонкам с фильтром
        $this->assertSame(['title'], array_keys($table->getSearchFields()));

        \Simflex\Core\System\Session::set('test_table', ['filter' => ['title' => 'abc', 'hidden' => '']]);
        $filters = $table->getAppliedFilters();
        $this->assertCount(1, $filters);
        $this->assertSame(['title', 'Title', 'abc'], [$filters[0]['name'], $filters[0]['label'], $filters[0]['value']]);
        $this->assertStringContainsString('filter%5Btitle%5D=', $filters[0]['reset']);
        \Simflex\Core\System\Session::set('test_table', []);
    }

    public function testListSearchScope(): void
    {
        $title = $this->makeField('title');
        $alias = $this->makeField('alias');
        foreach ([$title, $alias] as $field) {
            $field->filter = true;
            $field->isVisible = true;
        }
        $table = $this->makeTable(['title' => $title, 'alias' => $alias]);
        try {
            // «где искать»: колонка поиска из состояния списка, неизвестная колонка — везде
            \Simflex\Core\System\Session::set('test_table', ['qf' => 'alias']);
            $this->assertSame('alias', $table->getSearchScope());
            \Simflex\Core\System\Session::set('test_table', ['qf' => 'unknown']);
            $this->assertSame('', $table->getSearchScope());
        } finally {
            \Simflex\Core\System\Session::set('test_table', []);
        }
    }

    public function testListFilterMarkup(): void
    {
        $configBackup = Container::get('config');
        Container::set('config', (object)['theme' => 'default', 'devMode' => false, 'adminPath' => '/admin']);
        try {
            $this->mockRows([['cnt' => 1], ['id' => 1, 'title' => 'Первая', 'alias' => 'first', 'active' => 1]]);
            $title = $this->makeField('title');
            $alias = $this->makeField('alias');
            $active = new FieldBool(['name' => 'active', 'label' => 'Active', 'table' => 'test_table']);
            foreach ([$title, $alias, $active] as $field) {
                $field->filter = true;
                $field->isVisible = true;
            }
            $table = $this->makeTable(['title' => $title, 'alias' => $alias, 'active' => $active]);
            $setFilter = fn(Table $table) => $table->isFilter = true;
            \Closure::bind($setFilter, null, Table::class)($table);
            \Simflex\Core\System\Session::set('test_table', ['filter' => ['title' => 'Перв'], 'q' => 'x']);

            $html = $table->show();
            // меню фильтра колонки: у текста — поле поиска, у bool — переключатели; у заданного — бейдж
            $this->assertStringContainsString('id="sf-th-filter-title"', $html);
            $this->assertStringContainsString('th-filter th-filter_set', $html);
            $this->assertMatchesRegularExpression('/type="radio"[^>]*name="filter\[active\]"/', $html);
            // «Сбросить» у колонки с фильтром, «где искать» при нескольких колонках поиска
            $this->assertStringContainsString('action=columnReset&amp;column=title', $html);
            $this->assertStringNotContainsString('action=columnReset&amp;column=alias', $html);
            $this->assertStringContainsString(' table-scope"', $html);
            // теги: фильтр колонки и поиск, кнопка окна фильтров с числом заданных
            $this->assertStringContainsString('Title: Перв', $html);
            $this->assertStringContainsString('Поиск: x', $html);
            $this->assertStringContainsString('table-toolbar__filters-count', $html);
        } finally {
            \Simflex\Core\System\Session::set('test_table', []);
            Container::set('config', $configBackup);
        }
    }

    public function testListDateRangeFilter(): void
    {
        $configBackup = Container::get('config');
        Container::set('config', (object)['theme' => 'default', 'devMode' => false, 'adminPath' => '/admin']);
        try {
            $this->mockRows([['cnt' => 0]], $queries);
            $date = new FieldDateTime(['name' => 'date', 'label' => 'Дата', 'table' => 'test_table']);
            $date->filter = true;
            $date->isVisible = true;
            $table = $this->makeTable(['date' => $date]);
            \Simflex\Core\System\Session::set('test_table', ['filter' => ['date' => '2026-11-01|2026-11-30']]);

            $html = $table->show();
            // период включительно: до начала следующего дня после «до»
            $sql = implode("\n", array_column($queries, 0));
            $params = array_merge(...array_column($queries, 1));
            $this->assertStringContainsString(' >= :filter_date_from', $sql);
            $this->assertStringContainsString(' < :filter_date_to', $sql);
            $this->assertSame('2026-11-01', $params['filter_date_from']);
            $this->assertSame('2026-12-01', $params['filter_date_to']);
            // меню фильтра — «От» и «До» с календарём, тег — период
            $this->assertStringContainsString('data-sf-date-range="from"', $html);
            $this->assertStringContainsString('value="01.11.2026"', $html);
            $this->assertStringContainsString('Дата: 01.11.2026 — 30.11.2026', $html);
        } finally {
            \Simflex\Core\System\Session::set('test_table', []);
            Container::set('config', $configBackup);
        }
    }

    public function testListMarkup(): void
    {
        $configBackup = Container::get('config');
        Container::set('config', (object)['theme' => 'default', 'devMode' => false, 'adminPath' => '/admin']);
        try {
            $this->mockRows([['cnt' => 2], ['id' => 1, 'title' => 'Первая'], ['id' => 2, 'title' => 'Вторая']]);
            $title = $this->makeField('title');
            $title->isVisible = true;
            $table = $this->makeTable(['title' => $title]);
            $set = fn(Table $table) => [$table->canAdd = true, $table->canDelete = true, $table->canEdit = true];
            \Closure::bind($set, null, Table::class)($table);

            $html = $table->show();
            $this->assertStringContainsString('class="page-head', $html);
            $this->assertStringContainsString('data-sf="list"', $html);
            $this->assertStringContainsString('aria-sort="none"', $html);
            $this->assertStringContainsString('href="?o=title&amp;d=0"', $html);
            $this->assertStringContainsString('data-sf-row-delete="?action=delete&amp;rows[]=1"', $html);
            $this->assertStringContainsString('id="btn-control-delete"', $html);
            $this->assertStringContainsString('class="table-bar', $html);
            $this->assertStringNotContainsString('table-empty', $html);
        } finally {
            Container::set('config', $configBackup);
        }
    }

    private function makeTable(array $fields = []): Table
    {
        $base = new class () extends Table {
            protected static string $model = TestTableModel::class;

            public static function getFields(): array
            {
                return [];
            }
        };
        // защищённые свойства Table задаются в области видимости класса
        $set = function (Table $table) use ($fields) {
            $table->fields = $fields;
            $table->pk = new FieldInt(['name' => 'id', 'label' => 'ID', 'table' => 'test_table']);
        };
        \Closure::bind($set, null, Table::class)($base);
        return $base;
    }

    private function makeField(string $name, bool $required = false): FieldString
    {
        $field = new FieldString(['name' => $name, 'label' => ucfirst($name), 'table' => 'test_table']);
        $field->required = $required;
        return $field;
    }
}

class TestTableModel extends ModelBase
{
    protected static string $table = 'test_table';
    protected static string $primaryKeyName = 'id';

    public const ?string COLUMN_TIME_CREATE = null;
    public const ?string COLUMN_TIME_UPDATE = null;
}
