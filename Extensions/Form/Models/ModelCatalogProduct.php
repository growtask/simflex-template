<?php

namespace App\Extensions\Form\Models;

use Simflex\Core\ModelBase;

/**
 * @property int $product_id
 * @property string $name
 */
class ModelCatalogProduct extends ModelBase
{
    protected static string $table = 'catalog_product';
    protected static string $primaryKeyName = 'product_id';
}
