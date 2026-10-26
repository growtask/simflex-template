<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\Presets\ActiveField;
use Simflex\Admin\Table\Presets\ForeignKeyField;
use Simflex\Admin\Table\Presets\IdField;
use Simflex\Admin\Table\Presets\NppField;
use Simflex\Admin\Table\Presets\ParentField;

/**
 * Пресеты полей — те же описания полей, только короче
 */
class FieldPresetsTest extends Unit
{
    public function testIdField(): void
    {
        $this->assertSame(
            (new FieldDefinition(
                name: 'blog_id',
                class: FieldInt::class,
                label: 'ID',
                pk: true,
                e2n: true,
                hidden: true,
                width: '54',
                filter: true,
            ))->toArray(),
            (new IdField(name: 'blog_id', width: '54', filter: true))->toArray(),
        );
        $this->assertSame('id', (new IdField())->name);
    }

    public function testActiveField(): void
    {
        $this->assertSame(
            (new FieldDefinition(
                name: 'active',
                class: FieldBool::class,
                label: 'Активно',
                width: '104',
                defaultValue: '1',
                filter: true,
            ))->toArray(),
            (new ActiveField())->toArray(),
        );
    }

    public function testNppField(): void
    {
        $this->assertSame(
            (new FieldDefinition(
                name: 'npp',
                class: FieldNPP::class,
                label: '№ п/п',
                width: '107',
                defaultValue: '0',
            ))->toArray(),
            (new NppField())->toArray(),
        );
    }

    public function testParentField(): void
    {
        $this->assertSame(
            (new FieldDefinition(
                name: 'pid',
                class: FieldInt::class,
                label: 'Родитель',
                e2n: true,
                isFk: true,
                fkTable: 'blog',
                fkKey: 'blog_id',
                fkLabel: 'title',
                fkIsPid: true,
            ))->toArray(),
            (new ParentField(name: 'pid', table: 'blog', key: 'blog_id', display: 'title'))->toArray(),
        );
    }

    public function testForeignKeyField(): void
    {
        $this->assertSame(
            (new FieldDefinition(
                name: 'author_id',
                class: FieldInt::class,
                label: 'Автор',
                width: '200',
                required: true,
                filter: true,
                isFk: true,
                fkTable: 'user',
                fkKey: 'user_id',
                fkLabel: 'login',
            ))->toArray(),
            (new ForeignKeyField(
                name: 'author_id',
                label: 'Автор',
                table: 'user',
                key: 'user_id',
                display: 'login',
                width: '200',
                required: true,
            ))->toArray(),
        );

        // необязательное может быть пустым, его ключ по умолчанию называется как поле
        $params = new ForeignKeyField(name: 'menu_id', label: 'Меню', table: 'menu');
        $this->assertTrue($params->e2n);
        $this->assertSame('menu_id', $params->fkKey);
        $this->assertSame('name', $params->fkLabel);
    }
}
