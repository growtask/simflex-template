<?php


namespace App\Extensions\Upgrade;


trait AccessDB
{

    protected function getNewDbCredentials()
    {
        return static::getDbCredentials($this->config->toRoot . '/config.php');
    }

    public static function getDbCredentials($file)
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

    protected function getNewDb()
    {
        $cred = $this->getNewDbCredentials();
        var_dump($cred);die;
        $db = new MySQL();
        $db->connect(...array_values($cred));
        return $db;
    }

}