<?php

namespace App\Extensions\Menu;

use Simflex\Core\Core;
use Simflex\Core\ModuleBase;

/**
 *
 * Output site mainmenu
 *
 */
class Menu extends ModuleBase
{

    private $menu = false;

    protected function content()
    {
        $this->menu = Core::menu();

        $start_id = isset($this->params['start_id']) ? (int)$this->params['start_id'] : 0;
        $pid = $start_id;

        $this->params['max_level'] = isset($this->params['max_level']) ? (int)$this->params['max_level'] : 0;

        $is_child = isset($this->params['is_child']) ? (bool)$this->params['is_child'] : false;
        if ($is_child) {
            $pid = -1;
            if (isset($this->menu[$start_id])) {
                foreach ($this->menu[$start_id] as $id => $item) {
                    //echo $item['link'], ' ', mb_substr(SFCore::path(), 0, mb_strlen($item['link'])), '<br />';
                    if ($item['link'] === mb_substr(Core::path(), 0, mb_strlen($item['link']))) {
                        $pid = $item['menu_id'];
                    }
                }
            }
        }

        include 'tpl/index.tpl';

//        $this->tree($pid);
    }

    private function tree($pid = 0, $lvl = 0)
    {
        if (!isset($this->menu[$pid])) {
            return;
        }

        foreach ($this->menu[$pid] as $id => $item) {
            $c = array();
            if (empty($item['hidden'])) {
                $isActive = $item['link'] === '/' && Core::path() === '/';
                $isActive |= $item['link'] !== '/' && $item['link'] === mb_substr(Core::path(), 0, mb_strlen($item['link']));
                $isActive ? $c[] = 'active' : null;

                if ($lvl < $this->params['max_level'] && isset($this->menu[$id])) {
                    $c[] = 'has-children';
                }
            }
            $this->menu[$pid][$id]['class-a'] = $c;
        }

        include 'tpl/treenode.tpl';
    }

}
