<?php
/**
 * Уровень меню: пункты Tab, пункты с подпунктами - DropdownTab. Активный пункт (текущая страница или раздел)
 * отмечается состоянием active компонента. Подпункты выводит сам DropdownTab, поэтому рекурсии нет.
 *
 * @var \App\Extensions\Menu\Menu $this
 * @var int $pid
 * @var int $lvl
 */

use Simflex\Core\Layout\Components\Common\Header\Layout as Header;

?>
<?= Header::drawNav(Header::getMenuItems($this->menu, $pid, (int)$this->params['max_level']), 'menu-' . $pid) ?>
