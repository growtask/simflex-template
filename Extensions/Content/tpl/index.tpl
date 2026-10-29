<?php

use Simflex\Core\Layout\Components\Common\Banner\Layout as Banner;
use Simflex\Core\Layout\Components\Common\Benefit\Layout as Benefit;
use Simflex\Core\Layout\Components\Common\Benefit\BenefitSize;
use Simflex\Core\Layout\Components\Common\Form\Callback\Layout as Callback;

/** @var \Simflex\Core\Extensions\Content\Models\Content $content */
$h = static fn($v) => htmlspecialchars((string)$v);
$slides = array_map(static fn($s) => [
    'title' => $s['title'] ?? '',
    'text' => $s['text'] ?? '',
    'image' => $s['image'] ?? '',
    'buttonText' => $h($s['buttonText'] ?? ''),
    'buttonLink' => $s['buttonLink'] ?? '',
], $content->getTable('slides'));
$benefits = $content->getTable('benefits');
$params = $content['params'];
?>
<div class="fs">
    <?= Banner::drawBanner($slides) ?>
</div>

<section class="features">
    <h2 class="features__title_h2"><?= $h($params['features_title'] ?? '') ?></h2>
    <?= Benefit::drawBenefitList($benefits, className: 'features__items_list', size: BenefitSize::Medium) ?>
</section>

<section class="r-form">
    <div class="r-form__container">
        <div class="r-form__form-block">
            <div class="r-form__text">
                <h3 class="r-form__title_h3"><?= $h($params['rform_title'] ?? '') ?></h3>
                <p class="r-form__desc"><?= $h($params['rform_desc'] ?? '') ?></p>
            </div>
            <?= Callback::drawCallback() ?>
        </div>
        <?php if (!empty($params['rform_image'])) { ?>
            <div class="r-form__img">
                <img src="<?= $h($params['rform_image']) ?>" alt="" loading="lazy">
            </div>
        <?php } ?>
    </div>
</section>
