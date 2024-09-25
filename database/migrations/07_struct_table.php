<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('struct_table', function (Schema\Table $c) {
            $c->id('table_id');
            $c->string('name');
            $c->string('order_by');
            $c->integer('order_desc');
            $c->integer('priv_add');
            $c->integer('priv_edit');
            $c->integer('priv_delete');
            $c->string('class');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('struct_table');
    }

    public function seed()
    {
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('1', 'struct_table', '', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('2', 'struct_field', '', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('3', 'struct_data', 'npp', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('4', 'struct_param', '', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('5', 'admin_menu', 'npp', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('6', 'user_priv', '', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('7', 'user_role', '', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('8', 'user', '', '0', '0', '0', '0', ?)", ['\App\Extensions\Catalog2\Admin\User']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('9', 'user_role_priv', '', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('10', 'menu', 'npp', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('11', 'component', '', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('12', 'content', '', '0', '0', '0', '0', ?)", ['\Simflex\Extensions\Content\Admin\AdminContent']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('22', 'settings', 'npp', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('23', 'struct_field_param', '', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('24', 'module', '', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('25', 'module_item', 'npp', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('26', 'seo', '', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('28', 'module_param', 'npp', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('29', 'component_param', 'npp', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('30', 'slider', 'npp', '0', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('32', 'log', 'log_id', '1', '0', '0', '0', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('33', 'cron', '', '0', '1', '1', '1', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('34', 'user_priv_personal', '', '0', '1', '1', '1', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('35', 'content_template', '', '0', '1', '1', '1', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('36', 'content_template_param', '', '0', '1', '1', '1', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('37', 'catalog_category', 'npp', '1', '2', '2', '2', ?)", ['\App\Extensions\Catalog\Admin\Category']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('38', 'catalog_product', 'npp', '0', '2', '2', '2', ?)", ['\App\Extensions\Catalog2\Admin\Product']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('39', 'catalog_product_param', 'npp', '0', '2', '2', '2', ?)", ['\App\Extensions\Catalog\Admin\Params']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('40', 'callback', 'date', '1', '2', '2', '2', ?)", ['App\Extensions\Callback\Admin\Callback']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('41', 'catalog_transcomp', 'npp', '1', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('42', 'catalog_order', 'date', '1', '2', '2', '2', ?)", ['\App\Extensions\Catalog2\Admin\Order']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('44', 'catalog_sale', 'start', '1', '2', '2', '2', ?)", ['App\Extensions\Catalog\Admin\Sale']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('45', 'catalog_stock', '', '0', '2', '2', '2', ?)", ['App\Extensions\Catalog\Admin\Stock']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('46', 'catalog_analytics', '', '0', '2', '2', '2', ?)", ['\App\Extensions\Analytics\Admin\Analytics']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('48', 'catalog_param_category', 'npp', '1', '2', '2', '2', ?)", ['\App\Extensions\Catalog\Admin\ParamCategory']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('49', 'catalog_param', 'npp', '1', '2', '2', '2', ?)", ['\App\Extensions\Catalog\Admin\Params']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('50', 'catalog_brand', 'npp', '1', '2', '2', '2', ?)", ['\App\Extensions\Catalog\Admin\Brand']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('51', 'review', 'date', '1', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('52', 'question', 'question_id', '1', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('53', 'refund', 'date', '1', '2', '2', '2', ?)", ['\App\Extensions\Catalog\Admin\Refund']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('54', 'callback_email', 'email_id', '1', '2', '2', '2', ?)", ['\App\Extensions\Callback\Admin\CallbackEmail']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('56', 'blog', 'npp', '1', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('57', 'blog_category', 'npp', '1', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('58', 'info', 'npp', '0', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('59', 'info_qna_category', 'npp', '0', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('60', 'info_qna', 'npp', '0', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('61', 'catalog_price_level', '', '0', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('62', 'catalog_price', '', '0', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('63', 'catalog_payment', 'npp', '0', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('64', 'mp_mapping', '', '0', '1', '1', '1', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('65', 'mp_parser', '', '0', '1', '1', '1', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('66', 'mp_import', '', '0', '2', '2', '2', ?)", ['App\Extensions\Import\Admin\Import']);
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('67', 'catalog_sale_banner', 'npp', '0', '2', '2', '2', '')");
        DB::query("INSERT INTO struct_table (table_id, name, order_by, order_desc, priv_add, priv_edit, priv_delete, class) VALUES ('68', 'user_level', 'level_ord', '0', '2', '2', '2', '')");
    }
};