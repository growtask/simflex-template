<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Core\Admin\Page;
use Simflex\Core\Extensions\Alert\Alert;
use Simflex\Core\Admin\Plugins\Log\Log;
use Simflex\Core\Admin\Handlers\Account;
use Simflex\Core\Admin\Table\Table;
use Simflex\Core\Admin\WidgetBase;
use Simflex\Core\Http\Request;
use Simflex\Core\System\Container;
use Simflex\Core\System\EventManager;
use Simflex\Core\Models\User;
use Tests\Support\AlertState;
use Tests\Support\MockDb;

/**
 * Админка вокруг разделов: сообщения, виджеты, учётная запись, лог, параметры сайта
 */
class PanelTest extends Unit
{
    use MockDb;

    private array $alertBackup = [];

    protected function _before(): void
    {
        $this->alertBackup = AlertState::get();
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
        Container::set('page', new Page());
    }

    public function testAlert(): void
    {
        $this->assertFalse((bool)Alert::has());
        $this->assertFalse(Alert::hasErrors());

        Alert::error('Something failed');
        $this->assertTrue((bool)Alert::has());
        $this->assertTrue(Alert::hasErrors());
        $this->assertFalse(Alert::hasErrors('other_page'));

        $this->assertStringContainsString('Something failed', Alert::getHTML());
        $this->assertFalse(Alert::hasErrors(), 'shown messages are removed');

        Alert::success('Done');
        $this->assertTrue((bool)Alert::has());
        $this->assertFalse(Alert::hasErrors());
    }

    public function testWidget(): void
    {
        $widget = new class (['widget_id' => '7', 'name' => 'Sales']) extends WidgetBase {
            protected function content(): string
            {
                return 'widget-body';
            }
        };
        $page = Page::get();
        $ajax = new \ReflectionProperty($page, 'ajax');
        $ajax->setValue($page, true);
        $this->assertSame('widget-body', $widget->execute());
        $ajax->setValue($page, false);
        $html = $widget->execute();
        $this->assertStringContainsString('widget-body', $html);
        $this->assertStringContainsString('Sales', $html);
        $this->assertStringContainsString('value="7"', $html);
    }

    public function testAccountShowsCurrentUserEscaped(): void
    {
        $oldUser = Container::isSet('user') ? Container::get('user') : null;
        $user = new User();
        $user['user_id'] = 3;
        $user['login'] = 'admin';
        $user['name'] = '<b>Admin</b>';
        $user['email'] = 'a@b.c';
        $user['role'] = (object)['name' => 'Root'];
        Container::set('user', $user);
        try {
            $account = new Account();
            $html = $account->content();
        } finally {
            Container::set('user', $oldUser);
        }
        $this->assertStringContainsString('value="&lt;b&gt;Admin&lt;/b&gt;"', $html);
        $this->assertStringContainsString('value="Root"', $html);
        $this->assertStringContainsString('value="a@b.c"', $html);
    }

    public function testLogUsesPlaceholders(): void
    {
        $oldRequest = Container::getRequest();
        Container::set('request', Request::create(server: ['REMOTE_ADDR' => "1.2.3.4'", 'HTTP_USER_AGENT' => "agent', data='x"]));
        $oldEvents = Container::isSet('events') ? Container::get('events') : null;
        Container::set('events', new EventManager());
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
            Container::set('request', $oldRequest);
        }
    }

    /**
     * Тип параметра или результата проверяется только при вызове метода: отсутствующий класс до тех пор
     * остаётся незамеченным
     */
    public function testSignaturesReferenceExistingClasses(): void
    {
        foreach ([Table::class, WidgetBase::class, Page::class, Alert::class] as $class) {
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
}
