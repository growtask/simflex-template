<?php /** @var array $data */ ?>
<?php if ($data['index'] % 2): ?>
    <div class="layouts-cards-product1 left">
        <img class="left-image" src="<?= $data['big_image'] ?>" alt="<?= $data['title'] ?>"/>
        <div class="title"><?= $data['title'] ?></div>
        <div class="description"><?= $data['description'] ?></div>
    </div>
<?php else: ?>
    <div class="layouts-cards-product1 right">
        <div class="title"><?= $data['title'] ?></div>
        <div class="description"><?= $data['description'] ?></div>
        <img class="right-image" src="<?= $data['big_image'] ?>" alt="<?= $data['title'] ?>"/>
    </div>
<?php endif ?>
