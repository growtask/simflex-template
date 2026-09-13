<?php

use Simflex\Admin\Fields\FieldAlias;
use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldCategorySelect;
use Simflex\Admin\Fields\FieldColor;
use Simflex\Admin\Fields\FieldDate;
use Simflex\Admin\Fields\FieldDateTime;
use Simflex\Admin\Fields\FieldDouble;
use Simflex\Admin\Fields\FieldEnum;
use Simflex\Admin\Fields\FieldFile;
use Simflex\Admin\Fields\FieldImage;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldMultiKey;
use Simflex\Admin\Fields\FieldNPP;
use Simflex\Admin\Fields\FieldPassword;
use Simflex\Admin\Fields\FieldPasswordVisible;
use Simflex\Admin\Fields\FieldPath;
use Simflex\Admin\Fields\FieldRelation;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldTable;
use Simflex\Admin\Fields\FieldTags;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Fields\FieldTime;
use Simflex\Admin\Fields\FieldVirtual;
use Simflex\Core\DB;
use Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    private const FIELD_TYPES = [
        1 => FieldString::class,
        2 => FieldBool::class,
        3 => FieldInt::class,
        4 => FieldAlias::class,
        5 => FieldPath::class,
        6 => FieldPassword::class,
        7 => FieldDate::class,
        8 => FieldDateTime::class,
        9 => FieldText::class,
        10 => FieldFile::class,
        11 => FieldImage::class,
        12 => FieldEnum::class,
        13 => FieldNPP::class,
        14 => FieldVirtual::class,
        15 => FieldTime::class,
        16 => FieldDouble::class,
        17 => FieldMultiKey::class,
        18 => FieldPasswordVisible::class,
        19 => FieldCategorySelect::class,
        20 => FieldTags::class,
        21 => FieldTable::class,
        22 => FieldRelation::class,
        23 => FieldColor::class,
    ];

    public function up(Schema $s)
    {
        foreach (['component_param', 'content_template_param', 'module_param'] as $table) {
            if (!$this->hasColumn($table, 'field_type')) {
                DB::query("ALTER TABLE `$table` ADD COLUMN `field_type` varchar(255) NOT NULL DEFAULT ''");
            }

            if ($this->hasColumn($table, 'field_id')) {
                foreach (self::FIELD_TYPES as $id => $class) {
                    DB::query("UPDATE `$table` SET `field_type` = ? WHERE `field_id` = ?", [$class, $id]);
                }
                DB::query("ALTER TABLE `$table` DROP COLUMN `field_id`");
            }
        }
    }

    public function down(Schema $s)
    {
    }

    private function hasColumn(string $table, string $column): bool
    {
        return (bool)DB::result(
            'SELECT COUNT(*) c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            'c',
            [$table, $column]
        );
    }
};
