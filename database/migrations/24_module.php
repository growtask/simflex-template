<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('module', function (Schema\Table $c) {
            $c->id('module_id');
            $c->string('class');
            $c->string('name');
            $c->enum('type', ['site', 'admin'])->setDefault('site');
            $c->integer('postexec')->setDefault(0);
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('module');
    }

    public function seed()
    {
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('1', ?, 'Меню', 'site', '0')", ['\App\Extensions\Menu\Menu']);
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('2', ?, 'Список материалов', 'site', '0')", ['App\Extensions\Content\ModuleContent']);
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('3', ?, 'Хлебные крошки', 'site', '1')", ['\App\Extensions\Breadcrumbs\Breadcrumbs']);
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('4', ?, 'Текстовый блок', 'site', '0')", ['\App\Extensions\Block\Block']);
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('7', 'Breadcrumbs', 'Admin. Хлебные крошки', 'admin', '0')");
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('8', 'Menu', 'Admin. Меню', 'admin', '0')");
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('9', ?, 'Слайдер', 'site', '0')", ['\App\Extensions\Slider\ModuleSlider']);
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('11', 'Account', 'Admin. Аккаунт', 'admin', '0')");
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('12', 'Install', 'Admin. Репозиторий', 'admin', '0')");
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('13', ?, 'Код', 'site', '0')", ['\App\Extensions\Code\ModuleCode']);
        DB::query("INSERT INTO module (module_id, class, name, type, postexec) VALUES ('14', ?, 'Форма', 'site', '0')", ['\App\Extensions\Form\ModuleForm']);
    }
};