<?php


namespace App\Extensions\Upgrade;


class Config
{

    public $configPath;

    public $fromRoot;
    public $toRoot;

    public $fromDbHost;
    public $fromDbUser;
    public $fromDbPass;
    public $fromDbName;

    public $toDbHost;
    public $toDbUser;
    public $toDbPass;
    public $toDbName;

    public function __construct($file = null)
    {
        if ($file) {
            $this->load($file);
        }
    }

    public function loadDbCredentials($newDbName)
    {
        [
            'host' => $this->fromDbHost,
            'user' => $this->fromDbUser,
            'pass' => $this->fromDbPass,
            'db' => $this->fromDbName,
        ] = static::parseDbCredentials($this->fromRoot . '/config.php');
        $this->toDbHost = $this->fromDbHost;
        $this->toDbUser = $this->fromDbUser;
        $this->toDbPass = $this->fromDbPass;
        $this->toDbName = $newDbName ?? 'new' . $this->fromDbName;
//        [
//            'host' => $this->toDbHost,
//            'user' => $this->toDbUser,
//            'pass' => $this->toDbPass,
//            'db' => $this->toDbName,
//        ] = static::parseDbCredentials($this->toRoot . '/config.php');
    }

    public static function parseDbCredentials($file)
    {
        $raw = file_get_contents($file);
        $data = [];
        $matches = [];
        preg_match("@db_host = '(.+)';@U", $raw, $matches);
        $data['host'] = $matches[1];
        preg_match("@db_user = '(.+)';@U", $raw, $matches);
        $data['user'] = $matches[1];
        preg_match("@db_pass = '(.+)';@U", $raw, $matches);
        $data['pass'] = $matches[1];
        preg_match("@db_name = '(.+)';@U", $raw, $matches);
        $data['db'] = $matches[1];
        return $data;
    }

    public function getNewDb()
    {
        $db = new MySQL();
        $db->connect($this->toDbHost, $this->toDbUser, $this->toDbPass, $this->toDbName);
        return $db;
    }

    public function load($file)
    {
        $this->configPath = realpath($file);
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function save($file)
    {
        file_put_contents($file, $this->getSerialized());
    }

    public function getSerialized()
    {
        $data = [];
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties() as $property) {
            if ('configPath' == $property->name) {
                continue;
            }
            $data[$property->name] = $this->{$property->name};
        }
        $json = json_encode($data, JSON_PRETTY_PRINT);
        return $json;
    }
}