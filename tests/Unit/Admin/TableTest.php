<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Core\Admin\Page;
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
        $this->mockRows([]);
        $table = $this->makeTable(['title' => $this->makeField('title')]);
        $table->extraLeft = '<div id="extra-left"></div>';
        $table->extraRight = '<div id="extra-right"></div>';

        $html = $table->form();
        $this->assertStringContainsString('<div id="extra-left"></div>', $html);
        // правая колонка показывается для дополнительного HTML, даже если полей в ней нет
        $this->assertStringContainsString('content--right', $html);
        $this->assertStringContainsString('<div id="extra-right"></div>', $html);
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
