<?php


namespace App\Extensions\Upgrade\Hooks;


use App\Extensions\Upgrade\UpFile;

class Hook
{

    /**
     * @var UpFile
     */
    protected $up;

    public function __construct($up)
    {
        $this->up = $up;
    }

    public function before($what)
    {

    }

    public function after($what)
    {

    }
}