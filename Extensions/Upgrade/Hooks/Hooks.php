<?php


namespace App\Extensions\Upgrade\Hooks;


class Hooks
{

    /**
     * @var Hook[]
     */
    protected $hooks = [];

    public function __construct($up)
    {
        $files = array_slice(scandir(__DIR__), 2);
        $files = array_filter($files, function ($file) {
            return $file != 'Hook.php'
                && $file != 'Hooks.php';
        });
        $classes = array_map(function ($file) {
            return substr($file, 0, -4);
        }, $files);
        $this->hooks = array_filter(array_map(function ($class) use ($up) {
            $fqn = "App\Extensions\Upgrade\Hooks\\$class";
            if (class_exists($fqn)) {
                return new $fqn($up);
            }
            return null;
        }, $classes));
    }

    public function before($what)
    {
        foreach ($this->hooks as $hook) {
            $hook->before($what);
        }
    }

    public function after($what)
    {
        foreach ($this->hooks as $hook) {
            $hook->after($what);
        }
    }

}