<?php


namespace App\Extensions\Upgrade;


class UpApi extends UpClass
{

    public function upgrade()
    {
        $this->upgradeNamespace();
        $this->upgradeClass();
        $this->upgradeExtends();
        $this->save();
//        $this->
        print_r($this->data);
        print_r($this->newData);
        die;
    }

    protected function upgradeClass()
    {
        $this->newData['class'] = substr($this->data['class'], 3);
//        $this->replacePart('class');
    }

}