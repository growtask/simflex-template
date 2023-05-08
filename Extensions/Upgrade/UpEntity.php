<?php


namespace App\Extensions\Upgrade;


use App\Core\Console\Alert;

abstract class UpEntity extends UpFile
{

    protected static $name = '';

    public function upgrade()
    {
        if ($delegated = $this->upgradeDelegate()) {
            return $delegated;
        }
        $this->upgradeInner();
        $this->save();
        return true;
//        print_r($this->data);
//        print_r($this->newData);
//        die;
    }

    protected function upgradeDelegate()
    {

    }

    protected function upgradeInner()
    {
        $this->upgradeNamespace();
        $this->upgradeAnnotations();
        $this->replaceClasses();
//        print_r($this->data);
//        print_r($this->newData);
//        die;
    }

    protected function upgradeAnnotations()
    {
        $this->newData['annotations'] = str_replace(
            [
                '@author Evgeny Shilov <evgeny@internet-menu.ru>',
                '@version 1.0',
            ],
            '',
            $this->newData['annotations']
        );

        $this->newData['annotations'] = preg_replace('@(\s?\*[\r\n\s]+)+\*\/@s', "\n*/", $this->newData['annotations']);
    }


    protected function upgradeNamespace()
    {
        $p = $this->getPlace();
        $relPath = dirname(str_replace("{$this->config->fromRoot}/{$p['oldBase']}", '', $this->path));
        $relPathParts = array_filter(explode('/', $relPath));
        $ns = array_merge(['App', $p['newBase']], array_map('ucfirst', $relPathParts));
        $this->newData['namespace'] = implode('\\', $ns);
    }

    protected function extNamespace()
    {
        ['newPlace' => $extensionName] = $this->getPlace();
        return "App\Extensions\\$extensionName";
    }

    protected function simplifyFqn($class)
    {
        $fqnParts = array_filter(explode('\\', $class));
        if (count($fqnParts) > 1) {
            $newFull = $class;
            $class = end($fqnParts);
            $this->newData['use'][$newFull] = $newFull;
        }
        return $class;
    }


    public function save()
    {
        $contents = '<?php' . "\n\n";
        $contents .= "namespace {$this->newData['namespace']};\n\n";
        $contents .= $this->useToStr() . "\n\n";
        $contents .= $this->newData['annotations'] ? $this->newData['annotations'] . "\n" : '';
        $contents .= $this->putEntity();
        if ($this->newData['extends']) {
            $contents .= " extends {$this->newData['extends']}";
        }
        if ($this->newData['implements']) {
            $contents .= " implements {$this->newData['implements']}";
        }
        $contents .= "\n{\n";
        $contents .= $this->newData['classContents'] . "\n}\n";
        $this->newData['contents'] = $contents;
        $newPath = $this->findNewPath();
        if (!is_dir(dirname($newPath))) {
            mkdir(dirname($newPath), 0777, true);
        }
        file_put_contents($newPath, $contents);
    }

    protected function putEntity()
    {
        return static::$name . " {$this->newData[static::$name]}";
    }

    protected function findNewName()
    {
        return $this->newData[static::$name] . '.php';
    }

    protected function useToStr()
    {
        $str = [];
        foreach ($this->newData['use'] as $use) {
            $str[] = "use $use;";
        }
        return implode("\n", $str);
    }

    protected static function parse($path)
    {
        $contents = file_get_contents($path);
        $matches = [];
        preg_match('@' . static::$name . ' ([\w\d_]+)@', $contents, $matches);
        $class = $matches[1] ?? null;
        if (empty($class)) {
            throw new SkipFileException('Has no class');
        }
        preg_match('@extends ([\w\d_]+)@', $contents, $matches);
        $extends = $matches[1] ?? null;
        preg_match('@implements ([\w\d_\s\,]+)@', $contents, $matches);
        $implements = trim($matches[1] ?? '') ?: null;
        preg_match('@namespace ([\w\d_\\\]+)@', $contents, $matches);
        $namespace = $matches[1] ?? null;
        preg_match('@(\/\*\*.+\*\/)[\n\r\s]+' . static::$name . '@Uis', $contents, $matches);
        $annotations = $matches[1] ?? null;
        preg_match('@' . static::$name . ' [^\{]+\{(.*)\}@smi', $contents, $matches);
        $classContents = $matches[1] ?? null;
        if (empty($classContents)) {
            throw new \Exception("Cant detect class contents $path");
        }
        $result = [
            'contents' => $contents,
            'entityType' => static::$name,
            'entity' => $class,
            static::$name => $class,
            'extends' => $extends,
            'implements' => $implements,
            'namespace' => $namespace,
            'use' => [],
            'annotations' => $annotations,
            'classContents' => $classContents,
        ];
        return $result;
    }

    public function getFqn()
    {
        return implode('\\', array_filter([$this->newData['namespace'], $this->newData['entity']]));
    }

    protected function replacePart($partName)
    {
        $this->replace($this->data[$partName], $this->newData[$partName]);
    }

    public function replace($search, $replace)
    {
        $this->newData['classContents'] = str_replace($search, $replace, $this->newData['classContents']);
    }

    protected function replaceClasses()
    {
        foreach ($this->knownClasses as $from => $to) {
            if (strpos($this->newData['classContents'], $from)) {
                $this->replace($from, $this->simplifyFqn($to));
            }
        }
        $classes = static::findClassesInContent($this->newData['classContents']);
        foreach ($classes as $class) {
            $newClass = UpClass::upgradeClassName($class);
            if ($newClass) {
                ['namespace' => $ns, 'class' => $cn] = UpClass::classNameInfo($newClass);
                $this->newData['use'][$newClass] = $newClass;
                $this->replace($class, $cn);
            }
        }
    }

    protected static function splitCamelCase($input)
    {
        return preg_split(
            '/(^[^A-Z]+|[A-Z][^A-Z]+)/',
            $input,
            -1, /* no limit for replacement count */
            PREG_SPLIT_NO_EMPTY /*don't return empty elements*/
            | PREG_SPLIT_DELIM_CAPTURE /*don't strip anything from output array*/
        );
    }

}