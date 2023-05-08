<?php


namespace App\Extensions\Upgrade;


use App\Extensions\Upgrade\Hooks\Hooks;

class UpFile
{

    use Configable;

    protected $path;
    protected $data;
    public $newData;

    protected $knownClasses = [
        'APIBase' => 'Simflex\Core\ApiBase',
        'SFDB' => 'Simflex\Core\DB',
        'SFDBWhere' => 'Simflex\Core\DB\Where',
        'SFModelBase' => 'Simflex\Core\ModelBase',
        'SFUser' => 'Simflex\Core\User',
        'SFConfig' => 'Simflex\Core\Container::getConfig()',
        'SFCore' => 'Simflex\Core\Core',
        'SFPage' => 'Simflex\Core\Page',
//        'SFComBase' => 'Simflex\Core\ComponentBase',
        'SFComBase' => 'Simflex\Core\ControllerBase',
        'SFModBase' => 'Simflex\Core\ModuleBase',
        'ComAction' => 'Simflex\Core\ControllerBase',
        'Notifier' => 'Simflex\Core\Alert\Site\Alert',
        'PlugMail' => 'Simflex\Core\Mail',
        'PlugSMS' => 'Simflex\Core\Sms',
        'Service' => 'Simflex\Core\Service',
        'PlugJQuery' => 'App\Plugins\Jquery\Jquery',
        'Time' => 'Simflex\Core\Time',
        'PlugTime' => 'Simflex\Core\Time',
    ];

    public function __construct($path, $config)
    {
        $this->path = $path;
        $this->config = $config;
        $this->newData = $this->data = static::parse($path);
    }

    public function addKnownClasses($classes)
    {
        $this->knownClasses += $classes;
    }

    protected function isClass()
    {
        return (bool)strpos($this->path, '.class.php');
    }

    protected function isInterface()
    {
        return (bool)strpos($this->path, '.interface.php');
    }

    protected function isTpl()
    {
        return (bool)strpos($this->path, '.tpl')
            || !$this->isClass() && strpos($this->path, '.php');
    }

    protected function isStatic()
    {
        return !$this->isClass() && !$this->isTpl() && !$this->isInterface();
    }

    protected function getHooks($up = null)
    {
        if (empty($up)) {
            $up = $this;
        }
        return new Hooks($up);
    }

    public function upgrade()
    {
        if ($this->isClass()) {
            $upgrader = new UpClass($this->path, $this->config);
            $hooks = $this->getHooks($upgrader);
            $hooks->before('class');
            $result = $upgrader->upgrade();
            $hooks->after('class');
            return $result;
        }
        if ($this->isInterface()) {
            $upgrader = new UpInterface($this->path, $this->config);
            $hooks = $this->getHooks($upgrader);
            $hooks->before('interface');
            $result = $upgrader->upgrade();
            $hooks->after('interface');
            return $result;
        }
        if ($this->isTpl()) {
            $hooks = $this->getHooks();
            $hooks->before('tpl');
            $this->replaceClasses();
            $hooks->after('tpl');
            return $this->save();
        }
        if ($this->isStatic()) {
            return $this->copy();
        }
        throw new \Exception("Unknown type of file $this->path");
    }

    protected function getPlace()
    {
        $getPlace = function ($what) {
            $pathParts = explode('/', $this->path);
            foreach ($pathParts as $index => $part) {
                if ($part == $what) {
                    break;
                }
            }
            $oldExtName = $pathParts[$index + 1];
            $extName = ucfirst($oldExtName);
            return ['oldPlace' => $oldExtName, 'newPlace' => $extName];
        };
        if (strpos($this->path, '/ext/') !== false) {
            return ['oldBase' => 'ext', 'newBase' => 'Extensions'] + $getPlace('ext');
        }
        if (strpos($this->path, '/plug/') !== false) {
            return ['oldBase' => 'plug', 'newBase' => 'Plugins'] + $getPlace('plug');
        }
        return null;
    }

    protected function findNewPath()
    {
        $p = $this->getPlace();
        if ($p) {
            $relPath = dirname(str_replace("{$this->config->fromRoot}/{$p['oldBase']}/{$p['oldPlace']}", '', $this->path));
            $path = rtrim("{$this->config->toRoot}/{$p['newBase']}/{$p['newPlace']}$relPath", '/') . '/' . $this->findNewName();
//            if(preg_match('@/(ext|plug)/[\w\d\_]+/models@'))
            $path = str_replace('/models/', '/Models/', $path);
            return $path;
        }
        return str_replace($this->config->fromRoot, $this->config->toRoot, $this->path);
    }

    protected function findNewName()
    {
        return basename($this->path);
    }

    public function save()
    {
        $newPath = $this->findNewPath();
        if (!is_dir(dirname($newPath))) {
            mkdir(dirname($newPath), 0755, true);
        }
        return file_put_contents($newPath, $this->newData['contents']);
    }

    protected function copy()
    {
        $newPath = $this->findNewPath();
        if (!is_dir(dirname($newPath))) {
            mkdir(dirname($newPath), 0755, true);
        }
        return copy($this->path, $newPath);
    }

    protected static function parse($path)
    {
        $contents = file_get_contents($path);
        $result = [
            'contents' => $contents,
        ];
        return $result;
    }

    public function replace($search, $replace)
    {
        $this->newData['contents'] = str_replace($search, $replace, $this->newData['contents']);
    }

    protected function replaceClasses()
    {
        $classes = static::findClassesInContent($this->newData['contents']);
        foreach ($classes as $class) {
            $newClass = $this->knownClasses[$class] ?? UpClass::upgradeClassName($class);
            if ($newClass) {
                $this->newData['contents'] = static::replaceClassInContent($this->newData['contents'], $class, $newClass);
            }
        }
    }

    protected static function replaceClassInContent($content, $old, $new)
    {
        $content = preg_replace('@' . $old . '::@', "$new::", $content);
        $content = preg_replace('@new ' . $old . '@', "new $new", $content);
        return $content;
    }

    protected static function findClassesInContent($content)
    {
        $matches = [];
        preg_match_all('@([\w\d\_]+)::@', $content, $matches);
        $classes = $matches[1] ?? [];
        preg_match_all('@new ([\w\d\_]+)@', $content, $matches);
        $classes = array_merge($classes, $matches[1] ?? []);
        $classes = array_filter(array_unique($classes));
        return $classes;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }


}