<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\DB\AQ;
use Simflex\DB\Expr;
use Simflex\DB\ExprOr;
use Simflex\DB\JoinClause;
use Simflex\DB\Where;
use Simflex\Models\ModuleItem;
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

    public function testGroupByHavingAndUnionAll(): void
    {
        $variants = (new AQ())->from('catalog_product_variant', 'pv')
            ->select('pv.product_id, COUNT(*) AS cnt')
            ->where(['pv.is_active' => 1])
            ->groupBy('pv.product_id');
        $q = (new AQ())->from('catalog_product', 't')
            ->select(['t.product_id', 'COUNT(*) AS cnt'])
            ->where(['t.is_active' => 1])
            ->groupBy(['t.product_id'])
            ->having('COUNT(*) > 1')
            ->unionAll($variants)
            ->orderBy('cnt DESC')
            ->limit(10);

        $sql = preg_replace('/\s+/', ' ', $q->build());
        $this->assertMatchesRegularExpression(
            '/^SELECT t.product_id, COUNT\(\*\) AS cnt FROM `catalog_product` t WHERE .+ GROUP BY t.product_id HAVING COUNT\(\*\) > 1 '
            . 'UNION ALL SELECT pv.product_id, COUNT\(\*\) AS cnt FROM `catalog_product_variant` pv WHERE .+ GROUP BY pv.product_id '
            . 'ORDER BY cnt DESC LIMIT 10 ?$/',
            $sql
        );
        // параметры условий обоих запросов
        $this->assertCount(2, $q->getBinds());
    }

    public function testUnionRemovesDuplicatesAndMixesWithUnionAll(): void
    {
        $q = (new AQ())->from('catalog_p2p', 'p')->select('p.right_id, p.left_id')->where(['p.left_id' => 1])
            ->union((new AQ())->from('catalog_p2p', 'p')->select('p.left_id, p.right_id')->where(['p.right_id' => 2]))
            ->unionAll((new AQ())->from('catalog_p2p', 'p')->select('p.left_id, p.right_id'));

        $sql = preg_replace('/\s+/', ' ', $q->build());
        $this->assertMatchesRegularExpression(
            '/^SELECT p.right_id, p.left_id FROM `catalog_p2p` p WHERE .+ UNION SELECT p.left_id, p.right_id FROM `catalog_p2p` p WHERE .+ '
            . 'UNION ALL SELECT p.left_id, p.right_id FROM `catalog_p2p` p ?$/',
            $sql
        );
        $this->assertSame([1, 2], array_values($q->getBinds()));
    }

    public function testInsertIntoSelect(): void
    {
        $db = $this->mockRows([], $queries);
        $db->method('affectedRows')->willReturn(3);

        $inserted = (new AQ())->from('catalog_p2c')
            ->select(['category_id', ':new_id'])
            ->where(['product_id' => 5])
            ->bind(['new_id' => 7])
            ->insertInto('catalog_p2c', ['category_id', 'product_id']);

        $this->assertSame(3, $inserted);
        $this->assertCount(1, $queries);
        [$sql, $params] = $queries[0];
        $this->assertMatchesRegularExpression(
            '/^INSERT INTO `catalog_p2c` \(`category_id`, `product_id`\) SELECT category_id, :new_id FROM `catalog_p2c` WHERE `product_id` = :(\w+) ?$/',
            trim($sql)
        );
        $this->assertSame(7, $params['new_id']);
        $this->assertContains(5, $params);
    }

    public function testInsertIntoFailureThrows(): void
    {
        $db = $this->mockDb();
        $db->method('query')->willReturn(false);
        $this->expectException(\RuntimeException::class);
        (new AQ())->from('a')->insertInto('b', ['id']);
    }
}
