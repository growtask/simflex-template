<?php


namespace App\Extensions\Upgrade;


use Simflex\Core\DB;

class UpgradeDB
{
    use Configable;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function upgrade()
    {
        $this->upgradeModules();
        $this->addAuthComponent();
    }

    public function addAuthComponent()
    {
        ConsoleUpgrade::job("Add auth component...", function () {
            $db = $this->config->getNewDb();
            if ($db->result($db->query("SELECT * FROM component WHERE class='Auth'"))) {
                return ['result' => 'Skip'];
            }
            $success = $db->query("INSERT INTO component SET class='Auth', name='Авторизация'");
            $componentId = $db->insertID();
            $success &= $db->query("INSERT INTO menu SET component_id=$componentId, active=1, hidden=1, npp=0, name='Авторизация', link='/auth/'");
            return $success;
        });
    }

    public function upgradeConfig()
    {
        ConsoleUpgrade::job('Upgrading config...', function () {
            $to = $this->config->toRoot;
            $newConfig = file_get_contents($to . '/config.php');
            $newConfig = preg_replace("@(db_host = ')(.+)(';)@U", "$1{$this->config->toDbHost}$3", $newConfig);
            $newConfig = preg_replace("@(db_user = ')(.+)(';)@U", "$1{$this->config->toDbUser}$3", $newConfig);
            $newConfig = preg_replace("@(db_pass = ')(.+)(';)@U", "$1{$this->config->toDbPass}$3", $newConfig);
            $newConfig = preg_replace("@(db_name = ')(.+)(';)@U", "$1{$this->config->toDbName}$3", $newConfig);
            return file_put_contents($to . '/config.php', $newConfig);
        });
    }

    public function copyDb()
    {
        ConsoleUpgrade::job("Dump db {$this->config->fromDbName}...", function () {
            $login = $this->config->fromDbUser;
            $pass = $this->config->fromDbPass;
            $host = $this->config->fromDbHost;
            $db = $this->config->fromDbName;
            return shell_exec("mysqldump -u$login -p$pass -h $host $db > /tmp/1.sql") == '';
        });
        ConsoleUpgrade::job("Import db {$this->config->toDbName}...", function () {
            $login = $this->config->toDbUser;
            $pass = $this->config->toDbPass;
            $host = $this->config->toDbHost;
            $db = $this->config->toDbName;
            return shell_exec("mysql -u$login -p$pass -h $host -e 'create database $db'") == ''
                && shell_exec("mysql -u$login -p$pass -h $host -e 'use $db; \. /tmp/1.sql'") == '';
        });
    }

    public function upgradeModules()
    {
        ConsoleUpgrade::job("Upgrade db modules...", function () {
            $db = $this->config->getNewDb();
            $result = $db->query("SHOW COLUMNS FROM module LIKE 'type'");
            if (!$db->result($result)) {
                $q = "alter table module add type enum ('site', 'admin') default 'site' not null";
                $db->query($q);
                $q = "SELECT * FROM module WHERE type='admin'";
                $modules = DB::assoc($q);
                foreach ($modules as $module) {
                    $set = ['priv_id = 3'];
                    foreach ($module as $k => $v) {
                        if ($k == 'module_id') {
                            continue;
                        }
                        $set[] = "`$k` = '$v'";
                    }
                    $q = "INSERT INTO module SET " . implode(', ', $set);
//                echo $q;die;
                    $db->query($q);
                }
            }
            $result = $db->query("SHOW COLUMNS FROM module LIKE 'postexec'");
            if (!$db->result($result)) {
                $q = "alter table module add postexec int default 0 null comment 'Выполнять после контента'";
                $db->query($q);
                $q = "UPDATE module SET postexec=1 WHERE class LIKE '%Breadcrumbs%' and type='site'";
                $db->query($q);
            }
            $q = "alter table seo add metatags text null";
            @$db->query($q);
            return true;
        });
    }
}