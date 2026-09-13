<?php

namespace App\Extensions\Form\Model;

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
