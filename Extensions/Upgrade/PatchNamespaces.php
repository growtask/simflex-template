<?php


namespace App\Extensions\Upgrade;


use Simflex\Admin\Plugins\Alert\Alert;

class PatchNamespaces
{
    use Configable;

    protected $classes = [];
    protected $files = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function upgrade()
    {
        $this->collectClasses();
        $this->collectToUpgrade();
        $this->upgradeInner();
    }

    protected function collectClasses()
    {
        ConsoleUpgrade::job('Collect new classes...', function () {
            $files = array_filter(explode("\n", shell_exec("find {$this->config->toRoot} -type f -not -path '*/vendor/*' -name '*.php'")));
            $this->classes = array_values(array_filter(array_map(function ($file) {
                $possible = substr(basename($file), 0, -4);
                $contents = file_get_contents($file);
                if (
                    strpos($contents, "class $possible")
                    || strpos($contents, "interface $possible")
                ) {
                    try {
                        $up = new UpClass($file, ['oldRoot' => $this->config->fromRoot, 'newRoot' => $this->config->toRoot]);
                        return $up->getFqn();
                    } catch (SkipFileException $e) {
                        return false;
                    }
                }
                return false;
            }, $files)));
            Alert::success('Found ' . count($this->classes));
            return true;
        });
    }

    protected function collectToUpgrade()
    {
        ConsoleUpgrade::job('Collect files to upgrade...', function () {
            $this->files = array_filter(explode("\n", shell_exec("find {$this->config->toRoot} -type f -name '*.php' \( -path '{$this->config->toRoot}/Extensions/*' -o -path '{$this->config->toRoot}/Plugins/*' \)")));
            Alert::success('Found ' . count($this->files));
            return true;
        });
    }

    protected function upgradeInner()
    {
        ConsoleUpgrade::job('Upgrading user defined classes...', function () {
            $classes = [];
            foreach ($this->classes as $c) {
                ['class' => $cn] = UpClass::classNameInfo($c);
                $classes[$cn] = $c;
            }
            foreach ($this->files as $file) {
                ConsoleUpgrade::job("Upgrade file $file...", function () use ($file, $classes) {
                    $from = $this->config->fromRoot;
                    $to = $this->config->toRoot;
                    try {
                        $fileHandler = (new UpFile($file, ['oldRoot' => $from, 'newRoot' => $to]));
                        $fileHandler->addKnownClasses($classes);
                        return $fileHandler->upgrade();
                    } catch (SkipFileException $e) {
                        return ['result' => 'Skip', 'message' => $e->getMessage()];
                    }
                });
            }
        });
    }

}