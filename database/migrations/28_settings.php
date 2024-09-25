<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('settings', function (Schema\Table $c) {
            $c->id('setting_id');
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->string('alias');
            $c->string('value');
            $c->enum('type', ['int', 'string', 'bool', 'enum'])->setDefault('string');
            $c->string('enum_values');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('settings');
    }

    public function seed()
    {
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('1', '0', 'Название сайта', 'site_name', 'Simflex', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('6', '0', 'Email', 'email', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('7', '12', 'Телефон', 'phone', '8 800 555 35 35', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('9', '11', 'Часы работы', 'workhours', 'Пн-пт, с 10:00 до 19:00 ', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('18', '14', 'Email для получения заявок', 'form_email', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('23', '0', 'Адрес', 'address', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('24', '0', 'ВКонтакте', 'vk', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('25', '0', 'Телеграм', 'tg', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('26', '0', 'Инстаграм', 'inst', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('27', '13', 'WhatsApp', 'whats_app', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('28', '1', 'Организация', 'company', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('29', '1', 'Токен Telegram для получения заявок', 'form_tg_token', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('30', '1', 'Chat ID для получения заявок', 'form_tg_chat_id', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('32', '0', 'Одноклассники', 'ok', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('33', '0', 'Адрес офиса', 'office_address', '', 'string', '')");
        DB::query("INSERT INTO settings (setting_id, npp, name, alias, value, type, enum_values) VALUES ('34', '0', 'Ссылка на Яндекс карту', 'map', '', 'string', '')");
    }
};