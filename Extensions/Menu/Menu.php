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
    private array|false $menu = false;

    protected function content(): string
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
                    if ($item['link'] === mb_substr(Core::path(), 0, mb_strlen($item['link']))) {
                        $pid = $item['menu_id'];
                    }
                }
            }
        }

        return $this->render(__DIR__ . '/tpl/index.tpl', ['pid' => $pid]);
    }

    private function tree(int|string $pid = 0, int $lvl = 0): string
    {
        if (!isset($this->menu[$pid])) {
            return '';
        }

        foreach ($this->menu[$pid] as $id => $item) {
            $c = [];
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

        return $this->render(__DIR__ . '/tpl/treenode.tpl', ['pid' => $pid, 'lvl' => $lvl]);
    }
}
