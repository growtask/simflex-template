<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Admin\Base;
use Simflex\Admin\Core as AdminCore;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Plugins\Alert\Alert;
use Tests\Support\AlertState;
use Tests\Support\MockDb;

/**
 * The admin section handler: group saving, validation, the record info
 */
class BaseTest extends Unit
{
    use MockDb;

    private array $alertBackup = [];
    private array $getBackup = [];
    private array $postBackup = [];

    protected function _before(): void
    {
        $this->alertBackup = AlertState::get();
        $this->getBackup = $_GET;
        $this->postBackup = $_POST;
        AlertState::set([]);
        // Session::set() tries to start a session, but the headers are sent already in CLI: silence only that
        set_error_handler(
            static fn(int $no, string $msg) => str_contains($msg, 'ession') || str_contains($msg, 'headers'),
            E_WARNING | E_NOTICE
        );
    }

    protected function _after(): void
    {
        restore_error_handler();
        AlertState::set($this->alertBackup);
        $_GET = $this->getBackup;
        $_POST = $this->postBackup;
        AdminCore::$ajax = AdminCore::$isAjax = false;
    }

    public function testIdsAreNormalized(): void
    {
        $_GET['ids'] = '1,0,2,invalid,3';
        unset($_POST['group_ids']);
        $this->assertSame('1,2,3', $this->makeBase()->ids);
        $_POST['group_ids'] = '4,5';
        $this->assertSame('4,5', $this->makeBase()->ids);
        unset($_GET['ids'], $_POST['group_ids']);
        $this->assertSame('', $this->makeBase()->ids);
    }

    public function testSaveGroup(): void
    {
        $db = $this->mockDb();
        $db->expects($this->once())->method('query')
            ->with(
                $this->stringContains('UPDATE `test_table` SET `title` = ? WHERE'),
                $this->identicalTo(['New', 1, 2])
            )
            ->willReturn($this->createMock(\PDOStatement::class));
        $_POST = ['group_ids' => '1,2', 'set' => ['title' => 1], 'title' => 'New'];

        $this->assertSame('ids', $this->makeBase(['title' => $this->makeField('title')])->saveGroup());
        $this->assertNotEmpty(AlertState::get()['']['success'] ?? null);
        $this->assertFalse(Alert::hasErrors());
    }

    public function testSaveGroupReportsFailedQuery(): void
    {
        $this->mockDb()->method('query')->willReturn(false);
        $_POST = ['group_ids' => '1', 'set' => ['title' => 1], 'title' => 'New'];

        $this->makeBase(['title' => $this->makeField('title')])->saveGroup();
        $this->assertTrue(Alert::hasErrors());
    }

    public function testValidateWithoutAjax(): void
    {
        AdminCore::$isAjax = false;
        $_POST = ['first' => '', 'second' => 'ok'];
        $this->assertFalse($this->makeBase([
            'first' => $this->makeField('first', true),
            'second' => $this->makeField('second', true),
        ])->isValid());
        $this->assertTrue($this->makeBase(['second' => $this->makeField('second', true)])->isValid());
        $this->assertTrue($this->makeBase()->isValid());
    }

    public function testShowDetailGetRowDoesNotCreateDynamicProperty(): void
    {
        $db = $this->mockDb();
        $db->method('query')->willReturn(false);
        $db->method('result')->willReturn(['id' => 5]);
        $base = $this->makeBase();
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

    private function makeBase(array $fields = []): Base
    {
        $base = new class () extends Base {
            protected function initTableName(): void
            {
                $this->table = 'test_table';
            }

            protected function initTableData(): void
            {
            }
        };
        $set = function (array $fields) {
            $this->fields = $fields;
            $this->pk = new FieldInt(['name' => 'id', 'label' => 'ID', 'table' => 'test_table']);
        };
        \Closure::bind($set, $base, Base::class)($fields);
        return $base;
    }

    private function makeField(string $name, bool $required = false): FieldString
    {
        $field = new FieldString(['name' => $name, 'label' => ucfirst($name), 'table' => 'test_table']);
        $field->required = $required;
        return $field;
    }
}
