<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use ReflectionMethod;
use ReflectionProperty;
use Simflex\Core\Cache;
use Simflex\Core\DB\Expr;
use Simflex\Core\DB\ExprOr;
use Simflex\Core\DB\Schema\Column;
use Simflex\Core\DB\Where;
use Simflex\Core\ModelBase;
use Simflex\Core\Models\ModuleItem;

class CoreDbBugfixTest extends Unit
{
    public function testFindPassesEmptyLimitWhenNotGiven(): void
    {
        $param = (new ReflectionMethod(ModelBase::class, 'find'))->getParameters()[2];
        $this->assertTrue($param->allowsNull());
        $source = file_get_contents((new \ReflectionClass(ModelBase::class))->getFileName());
        $this->assertStringContainsString("->limit(\$limit ?? '')", $source);
    }

    public function testWhereInBindsEveryValueSeparately(): void
    {
        $where = new Where(['id' => [1, 2, 3]]);
        $sql = $where->toString();
        $binds = $where->getBinds();
        $this->assertCount(3, $binds);
        $this->assertSame([1, 2, 3], array_values($binds));
        foreach (array_keys($binds) as $name) {
            $this->assertStringContainsString(':' . $name, $sql);
        }
    }

    public function testExprOrAcceptsArrayOfConditions(): void
    {
        $expr = new ExprOr(['a' => null, new Expr('b > 1')]);
        $this->assertSame('(`a` IS NULL OR b > 1)', (string)$expr);
        $this->assertSame('(0)', (string)new ExprOr([]));

        $where = new Where(['c' => null, new ExprOr(['a' => null, 't.b' => null])]);
        $this->assertSame('WHERE `c` IS NULL AND (`a` IS NULL OR `t`.`b` IS NULL)', $where->toString());
    }

    public function testCacheStoresObjects(): void
    {
        $dirProp = new ReflectionProperty(Cache::class, 'dir');
        $oldDir = $dirProp->isInitialized() ? $dirProp->getValue() : '';
        $dir = sys_get_temp_dir() . '/sf_cache_test_' . uniqid();
        mkdir($dir);
        $dirProp->setValue(null, $dir);
        try {
            $obj = new \ArrayObject(['x' => 1]);
            Cache::set('bugfix_obj', $obj);
            $this->assertEquals($obj, Cache::get('bugfix_obj'));
        } finally {
            array_map('unlink', glob("$dir/*"));
            rmdir($dir);
            $dirProp->setValue(null, $oldDir);
        }
    }

    public function testEnumValuesUsesBuffer(): void
    {
        $source = file_get_contents((new \ReflectionClass(ModelBase::class))->getFileName());
        $this->assertStringNotContainsString("class_exists(' Buffer')", $source);
    }

    public function testColumnDefaults(): void
    {
        $sql = static fn ($default) => (new Column('c'))->dataType('int')->setDefault($default)->autoIncrement()->toString();
        $this->assertStringContainsString('DEFAULT NULL AUTO_INCREMENT', $sql(null));
        $this->assertStringContainsString('DEFAULT 0 AUTO_INCREMENT', $sql(false));
        $this->assertStringContainsString('DEFAULT 1 AUTO_INCREMENT', $sql(true));
        $this->assertStringContainsString('DEFAULT 5 AUTO_INCREMENT', $sql(5));
        $this->assertStringContainsString('DEFAULT 1.5 AUTO_INCREMENT', $sql(1.5));
    }

    public function testModuleItemParamsOnBrokenData(): void
    {
        $item = new ModuleItem();
        $item->params = 'not serialized';
        $this->assertSame([], $item->getParams());
        $item->params = '';
        $this->assertSame([], $item->getParams());
        $item->setParams(['a' => 1]);
        $this->assertSame(['a' => 1], $item->getParams());
    }

    public function testCronClassLoads(): void
    {
        $this->assertTrue(class_exists(\Simflex\Core\Cron\Cron::class));
    }
}
