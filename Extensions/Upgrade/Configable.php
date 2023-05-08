<?php

namespace App\Extensions\Upgrade;

use App\Extensions\Upgrade\Config;

trait Configable {

    /** @var Config */
    protected $config;

    /**
     * @param Config $config
     */
    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

}