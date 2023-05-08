<?php


namespace App\Extensions\Upgrade;


class UpModule extends UpClass
{


    protected function upgradeInner()
    {
        parent::upgradeInner();
        $db = $this->config->getNewDb();
        $fqnClass = $db->escape($this->newData['namespace'].'\\'.$this->newData['class']);
        $db->query("update module set class='$fqnClass' WHERE class='{$this->data['class']}'");
    }

}