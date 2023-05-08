<?php


namespace App\Extensions\Upgrade;


use App\Core\Console\Alert;

class UpClass extends UpEntity
{

    protected static $name = 'class';

    protected function upgradeDelegate()
    {
        if (get_class($this) == self::class) {
            if ($this->data['extends'] == 'SFComBase') {
                $upgrader = new UpComponent($this->path, $this->config);
                return $upgrader->upgrade();
            }
            if ($this->data['extends'] == 'SFModBase') {
                $upgrader = new UpModule($this->path, $this->config);
                return $upgrader->upgrade();
            }
        }
        return false;
    }

    protected function upgradeInner()
    {

        parent::upgradeInner();
        $this->upgradeClass();
        $this->upgradeExtends();
//        print_r($this->data);
//        print_r($this->newData);
//        die;
    }

    protected function upgradeClass()
    {
        if ($newClass = static::upgradeClassName($this->data['class'])) {
            ['class' => $this->newData['class']] = static::classNameInfo($newClass);
        }
    }

    public static function upgradeClassName($class)
    {
        if($class == 'PlugFrontEnd'){
            $class = 'PlugFrontend';
        }
        $classParts = static::splitCamelCase($class);
        $extName = $classParts[1] ?? null;
        if (empty($extName)) {
            return false;
        }
        if (strpos($class, 'API') === 0) {
            $cn = 'Api' . substr($class, 3);
            $ns = "App\Extensions\\$extName";
        } elseif ($classParts[0] == 'Com') {
            $cn = substr($class, 3);
            $ns = "App\Extensions\\$extName";
        } elseif ($classParts[0] == 'Mod') {
            $cn = 'Module' . substr($class, 3);
            $ns = "App\Extensions\\$extName";
        } elseif ($classParts[0] == 'Plug') {
            $cn = substr($class, 4);
            if($cn == 'JQuery'){
                $cn = 'Jquery';
            }
            $ns = "App\Plugins\\$extName";
        } elseif ($classParts[0] == 'Model') {
            $cn = $class;
            $ns = "App\\Extensions\\$extName\\Models";
        } else {
            return false;
        }

        return "$ns\\$cn";
    }

    public static function classNameInfo($class)
    {
        $fqnParts = explode('\\', $class);
        $cn = end($fqnParts);
        $ns = implode('\\', array_slice($fqnParts, 0, -1));
        return ['fqn' => $class, 'class' => $cn, 'namespace' => $ns];
    }

    protected function upgradeExtends()
    {
        if ($new = $this->knownClasses[$this->data['extends']] ?? null) {
            $new = $this->simplifyFqn($new);
            $this->newData['extends'] = $new;
            return;
        }
//        throw new \Exception("Unknown class {$this->data['extends']}");
    }

    protected function putEntity()
    {
        $stack = [];
        if (!empty($this->newData['modifiers']['abstract'])) {
            $stack[] = 'abstract';
        }
        $stack[] = static::$name;
        $stack[] = $this->newData[static::$name];
        return implode(' ', $stack);
    }

    protected static function parse($path)
    {
        $result = parent::parse($path);
        $contents = file_get_contents($path);
        $result['modifiers']['abstract'] = strpos($contents, 'abstract class') !== false;
        return $result;
    }


}