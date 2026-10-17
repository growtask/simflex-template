<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use Simflex\Admin\Base;
use Simflex\Admin\Core as AdminCore;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Modules\Account\Account;
use Simflex\Admin\Page;
use Simflex\Admin\Plugins\Alert\Alert;
use Simflex\Admin\Plugins\Log\Log;
use Simflex\Admin\WidgetBase;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\DB\Adapter;
use Simflex\Core\Models\User;

class AdminBugfixTest extends Unit
{
    private ?Adapter $dbBackup = null;
    private array $alertBackup = [];
    private array $getBackup = [];
    private array $postBackup = [];
    private array $serverBackup = [];

    protected function _before(): void
    {
        $dbProp = new \ReflectionProperty(DB::class, 'db');
        $this->dbBackup = $dbProp->isInitialized() ? $dbProp->getValue() : null;
        $this->alertBackup = $this->alertState();
        $this->getBackup = $_GET;
        $this->postBackup = $_POST;
        $this->serverBackup = $_SERVER;
        $this->setAlerts([]);
        // Session::set() пытается стартовать сессию, а в CLI заголовки уже отправлены - глушим только это
        set_error_handler(
            static fn(int $no, string $msg) => str_contains($msg, 'ession') || str_contains($msg, 'headers'),
            E_WARNING | E_NOTICE
        );
    }

    protected function _after(): void
    {
        restore_error_handler();
        if ($this->dbBackup) {
            (new \ReflectionProperty(DB::class, 'db'))->setValue(null, $this->dbBackup);
        }
        $this->setAlerts($this->alertBackup);
        $_GET = $this->getBackup;
        $_POST = $this->postBackup;
        $_SERVER = $this->serverBackup;
        AdminCore::$ajax = AdminCore::$isAjax = false;
    }

    private function mockDb(): Adapter&\PHPUnit\Framework\MockObject\MockObject
    {
        $db = $this->createMock(Adapter::class);
        (new \ReflectionProperty(DB::class, 'db'))->setValue(null, $db);
        return $db;
    }

    private function alertState(): array
    {
        $p = new \ReflectionProperty(Alert::class, 's');
        return $p->isInitialized() ? $p->getValue() : [];
    }

    private function setAlerts(array $s): void
    {
        (new \ReflectionProperty(Alert::class, 's'))->setValue(null, $s);
        (new \ReflectionProperty(Alert::class, 'isInited'))->setValue(null, true);
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

    // 1. saveGroup: bool &= PDOStatement
    public function testSaveGroupAcceptsStatementResults(): void
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
        $this->assertNotEmpty($this->alertState()['']['success'] ?? null);
        $this->assertFalse(Alert::hasErrors());
    }

    public function testSaveGroupReportsFailedQuery(): void
    {
        $this->mockDb()->method('query')->willReturn(false);
        $_POST = ['group_ids' => '1', 'set' => ['title' => 1], 'title' => 'New'];

        $this->makeBase(['title' => $this->makeField('title')])->saveGroup();
        $this->assertTrue(Alert::hasErrors());
    }

    // 2. validate вне ajax
    public function testValidateNonAjaxUsesAllCollectedErrors(): void
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

    // 3. showDetailGetRow без динамического свойства
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

    // 4. Page::init не вызывает несуществующий Collection::ajaxData
    public function testPageDoesNotCallMissingNotifyMethod(): void
    {
        $this->assertFalse(method_exists(\Simflex\Admin\Notify\Collection::class, 'ajaxData'));
        $this->assertStringNotContainsString('ajaxData', file_get_contents((new \ReflectionClass(Page::class))->getFileName()));
    }

    // 5, 6. Alert
    public function testAlertDetectsErrorsAndRendersHtml(): void
    {
        $this->assertFalse((bool)Alert::has());
        $this->assertFalse(Alert::hasErrors());

        Alert::error('Something failed');
        $this->assertTrue((bool)Alert::has());
        $this->assertTrue(Alert::hasErrors());
        $this->assertFalse(Alert::hasErrors('other_page'));

        $html = Alert::getHTML();
        $this->assertStringContainsString('Something failed', $html);
        $this->assertFalse(Alert::hasErrors());

        Alert::success('Done');
        $this->assertTrue((bool)Alert::has());
        $this->assertFalse(Alert::hasErrors());
    }

    // 7. WidgetBase
    public function testWidgetBaseIsLoadableAndRenders(): void
    {
        $this->assertTrue(class_exists(WidgetBase::class));
        $widget = new class (['widget_id' => '7', 'name' => 'Sales']) extends WidgetBase {
            protected function content(): string
            {
                return 'widget-body';
            }
        };
        AdminCore::$ajax = true;
        $this->assertSame('widget-body', $widget->execute());
        AdminCore::$ajax = false;
        $html = $widget->execute();
        $this->assertStringContainsString('widget-body', $html);
        $this->assertStringContainsString('Sales', $html);
        $this->assertStringContainsString('value="7"', $html);
    }

    // 8. InstallBase (PlugFS), 11. print.tpl (SFAdminPage) - неиспользуемое наследие удалено
    public function testLegacyInstallBaseAndPrintTemplateRemoved(): void
    {
        $adminDir = dirname((new \ReflectionClass(Base::class))->getFileName());
        $this->assertFileDoesNotExist("$adminDir/InstallBase.php");
        $this->assertFileDoesNotExist("$adminDir/theme/tpl/print.tpl");
    }

    // 9, 10. Все типы в сигнатурах указывают на существующие классы
    public function testAdminSignaturesReferenceExistingClasses(): void
    {
        foreach ([Base::class, WidgetBase::class, Page::class, AdminCore::class, Alert::class] as $class) {
            foreach ((new \ReflectionClass($class))->getMethods() as $method) {
                $types = [$method->getReturnType()];
                foreach ($method->getParameters() as $param) {
                    $types[] = $param->getType();
                }
                foreach ($types as $type) {
                    $named = $type instanceof \ReflectionUnionType ? $type->getTypes() : [$type];
                    foreach ($named as $t) {
                        if ($t instanceof \ReflectionNamedType && !$t->isBuiltin() && !in_array($t->getName(), ['self', 'static'])) {
                            $this->assertTrue(
                                class_exists($t->getName()) || interface_exists($t->getName()),
                                "$class::{$method->getName()} uses missing type {$t->getName()}"
                            );
                        }
                    }
                }
            }
        }
    }

    // 12. Admin\Core::siteParam
    public function testAdminSiteParamReturnsStoredOrEmpty(): void
    {
        $prop = new \ReflectionProperty(AdminCore::class, 'siteParams');
        $backup = $prop->getValue();
        try {
            $prop->setValue(null, ['error404' => 'Not found']);
            $this->mockDb()->expects($this->never())->method('query');
            $this->assertSame('Not found', AdminCore::siteParam('error404'));
            $this->assertSame('', AdminCore::siteParam('unknown'));
        } finally {
            $prop->setValue(null, $backup);
        }
    }

    // 13. AdminContent::initTable - приоритет ?? и ==
    public function testAdminContentFormCheckPrecedence(): void
    {
        $src = file_get_contents((new \ReflectionClass(\Simflex\Extensions\Content\Admin\AdminContent::class))->getFileName());
        $this->assertStringContainsString("if ((\$_GET['action'] ?? '') == 'form')", $src);
    }

    // 14. Log::a - плейсхолдеры
    public function testLogUsesPlaceholders(): void
    {
        $_SERVER['REMOTE_ADDR'] = "1.2.3.4'";
        $_SERVER['HTTP_USER_AGENT'] = "agent', data='x";
        $hadEvents = Container::isSet('events');
        $oldEvents = $hadEvents ? Container::get('events') : null;
        Container::set('events', new \Simflex\Core\EventManager());
        $this->mockDb()->expects($this->once())->method('query')
            ->with(
                $this->stringContains('into `log` (`action`, `ip`, `browser`, `data`) values (?, ?, ?, ?)'),
                ['login_attempt', "1.2.3.4'", "agent', data='x", "Логин: a' OR 1=1"]
            )
            ->willReturn(false);
        try {
            Log::a('login_attempt', "Логин: a' OR 1=1");
        } finally {
            Container::set('events', $oldEvents);
        }
    }

    // 14. Account - текущий пользователь из Container, вывод экранируется
    public function testAccountRendersCurrentUserEscaped(): void
    {
        $hadUser = Container::isSet('user');
        $oldUser = $hadUser ? Container::get('user') : null;
        $user = new User();
        $user['user_id'] = 3;
        $user['login'] = 'admin';
        $user['name'] = '<b>Admin</b>';
        $user['email'] = 'a@b.c';
        $user['role'] = (object)['name' => 'Root'];
        Container::set('user', $user);
        $_POST = [];
        try {
            $account = new Account(['item_id' => 0, 'class' => Account::class, 'name' => '', 'posname' => '', 'params' => '']);
            $html = $account->content();
        } finally {
            Container::set('user', $oldUser);
        }
        $this->assertStringContainsString('value="&lt;b&gt;Admin&lt;/b&gt;"', $html);
        $this->assertStringContainsString('value="Root"', $html);
        $this->assertStringContainsString('value="a@b.c"', $html);
    }
}
