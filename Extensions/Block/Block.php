<?php

namespace App\Extensions\Block;

use Simflex\Core\ModuleBase;

/**
 *
 * Output html block in any place
 *
 */


class Block extends ModuleBase
{

    protected function content()
    {
        echo empty($this->params['content']) ? '' : $this->params['content'];
    }
}
