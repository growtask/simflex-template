<?php
/**
 * @var array $data
 */
?>

<img class="<?= $data['className'] ?>"
     src="<?= $data['src'] ?>"
     alt="<?= $data['alt'] ?>"
     onerror="this.src='/assets/images/no-image.webp'"
     <?= buildAttrs($data['attributes'] ?? []) ?>
>