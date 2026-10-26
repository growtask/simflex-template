<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\DB\AQ;
use Simflex\Core\DB\Expr;
use Simflex\Core\DB\ExprOr;
use Simflex\Core\DB\JoinClause;
use Simflex\Core\DB\Where;
use Simflex\Core\Models\ModuleItem;
use Tests\Support\MockDb;

/**
 * Построитель запросов: Where, ExprOr, AQ, ModelBase::find()
 */
class QueryTest extends Unit
{
    use MockDb;

    public function testWhereInBindsEveryValueSeparately(): void
    {
        $where = new Where(['id' => [1, 2, 3]]);
        $sql = $where->toString();
        $binds = $where->getBinds();
        $this->assertSame([1, 2, 3], array_values($binds));
        foreach (array_keys($binds) as $name) {
            $this->assertStringContainsString(':' . $name, $sql);
        }
    }

    public function testExprOrAcceptsArrayOfConditions(): void
    {
        $this->assertSame('(`a` IS NULL OR b > 1)', (string)new ExprOr(['a' => null, new Expr('b > 1')]));
        $this->assertSame('(0)', (string)new ExprOr([]));

        $where = new Where(['c' => null, new ExprOr(['a' => null, 't.b' => null])]);
        $this->assertSame('WHERE `c` IS NULL AND (`a` IS NULL OR `t`.`b` IS NULL)', $where->toString());
    }

    public function testBuildWithoutOptionalClauses(): void
    {
        $this->assertSame('SELECT * FROM `content`', trim((new AQ())->from('content')->build()));
        $this->assertSame('INNER JOIN `category` USING(`category_id`)', (new JoinClause('category', 'category_id'))->toSql('content'));
    }

    public function testJoinAliasAndBoundOnConditions(): void
    {
        $this->assertSame(
            'LEFT JOIN `content` `parent` ON `content`.`pid` = `parent`.`content_id`',
            (new JoinClause('content', 'content.pid', '.content_id', 'LEFT', alias: 'parent'))->toSql('content')
        );

        // значения дополнительных условий ON привязываются запросом
        $query = (new AQ())->from('user')->leftJoin('user_role', '.role_id', '.role_id', ['user_role.active' => 1]);
        $sql = $query->build();
        $binds = $query->getBinds();
        $this->assertSame([1], array_values($binds));
        $this->assertStringContainsString('AND `user_role`.`active` = :' . array_key_first($binds), $sql);
    }

    public function testBuildWithoutTableFails(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bad from statement');
        (new AQ())->build();
    }

    public function testFindWithoutLimit(): void
    {
        $this->mockRows([['item_id' => 1, 'posname' => 'left']], $queries);
        $items = ModuleItem::find(['posname' => 'left']);
        $this->assertCount(1, $items);
        $this->assertInstanceOf(ModuleItem::class, $items[0]);
        $this->assertStringNotContainsString('LIMIT', $queries[0][0]);

        $this->mockRows([], $queries);
        ModuleItem::find(['posname' => 'left'], null, '5');
        $this->assertStringContainsString('LIMIT 5', $queries[0][0]);
    }

    public function testFetchColumnByName(): void
    {
        $this->mockRows([['id' => 1, 'name' => 'a'], ['id' => 2, 'name' => 'b']]);
        $this->assertSame(['a', 'b'], (new AQ())->from('t')->fetchColumn('name'));
    }

    public function testFetchColumnByIndex(): void
    {
        $this->mockRows([['id' => 1, 'name' => 'a'], ['id' => 2, 'name' => 'b']]);
        $this->assertSame([1, 2], (new AQ())->from('t')->fetchColumn());
    }

    public function testFetchColumnOnEmptyResult(): void
    {
        $this->mockRows([]);
        $this->assertSame([], (new AQ())->from('t')->fetchColumn('name'));
    }

    public function testFetchColumnThrowsOnMissingColumn(): void
    {
        $this->mockRows([['id' => 1]]);
        $this->expectException(\Exception::class);
        (new AQ())->from('t')->fetchColumn('name');
    }

    public function testFetchScalar(): void
    {
        $this->mockRows([['id' => 7, 'name' => 'a'], ['id' => 8, 'name' => 'b']]);
        $this->assertSame('a', (new AQ())->from('t')->fetchScalar('name'));

        $this->mockRows([]);
        $this->assertNull((new AQ())->from('t')->fetchScalar('name'));
    }
}
