<?php /** @var array $data */ ?>

<div class="layouts-cards-products1">
    <div class="title"><?= $data['title'] ?></div>
    <div class="products">
        <?php foreach ($data['rows'] as $index => $row): ?>
            <?php \App\Layout\Cards\Product1\Layout::draw($row + ['index' => $index]) ?>
        <?php endforeach ?>
    </div>
</div>