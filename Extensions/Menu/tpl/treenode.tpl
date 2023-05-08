<ul class="level-<?php echo $lvl ?>">
    <?php foreach ($this->menu[$pid] as $id => $item): ?>
        <?php if (empty($item['hidden'])): ?>
            <li>
                <a<?php echo count($item['class-a']) ? ' class="' . join(' ', $item['class-a']) . '"' : '' ?> href="<?php echo $item['link'] ?>"><?php echo $item['name'] ?></a>
                <?php if ($lvl < $this->params['max_level']): ?>
                    <?php $this->tree($id, $lvl + 1) ?>
                <?php endif ?>
            </li>
        <?php endif ?>
    <?php endforeach ?>
</ul>
