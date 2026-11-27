<?php

namespace Tests\Acceptance;

use Facebook\WebDriver\WebDriverKeys;
use Tests\Support\AcceptanceTester;

/**
 * Поведение компонентов страницы Services на витрине /ui (этап 4 плана дизайн-системы): фокус карточки, период тарифов,
 * тариф в таблице на телефоне, галерея, слайдер шагов. Панель цены (Sticky) — в ServicesComponentsTest
 */
class ServicesBehaviorCest
{
    private function open(AcceptanceTester $I, string $component): void
    {
        $I->amOnPage('/ui?component=' . $component);
        $I->waitForJS('return !!(window.SfUi && window.SfUi.core) && document.readyState === "complete"', 10);
    }

    private function js(AcceptanceTester $I, string $code): mixed
    {
        return $I->executeJS($code);
    }

    public function cardFocusRingFromKeyboard(AcceptanceTester $I): void
    {
        $this->open($I, 'Services/Card/CardMain');
        // клик по подписи над первой карточкой, затем Tab: фокус на ссылке карточки, кольцо — у всей карточки
        $before = '.ui-showcase__subtitle:has(+ .ui-showcase__row .service-card)';
        $I->executeJS('document.querySelector(arguments[0]).scrollIntoView({block: "center", behavior: "instant"})', [$before]);
        $I->click($before);
        $I->executeInSelenium(static fn($driver) => $driver->getKeyboard()->sendKeys(WebDriverKeys::TAB));
        $I->waitForJS('return document.activeElement.classList.contains("service-card__title")', 5);
        $I->assertSame('solid', $this->js($I, 'return getComputedStyle(document.activeElement.closest(".service-card")).outlineStyle'));
        // клик мышью по другой карточке — без кольца
        $second = '.ui-showcase__row > div:nth-child(2) .service-card__title';
        $I->click($second);
        $I->assertSame('none', $this->js($I, 'return getComputedStyle(document.activeElement.closest(".service-card")).outlineStyle'));
        $I->dontSeeLogProblems();
    }

    public function tariffPeriodSwitchesAllPrices(AcceptanceTester $I): void
    {
        $this->open($I, 'Services/Tariff/TariffList');
        $list = '[data-sf-tariff-list].tariff-list_period_month';
        $I->executeJS('document.querySelector(arguments[0]).scrollIntoView({block: "center", behavior: "instant"})', [$list]);
        $I->click($list . ' .selector-item[data-value="year"]');
        $I->assertSame(
            ['48 000 ₽', '96 000 ₽', '192 000 ₽'],
            $this->js($I, 'return [...document.querySelector("[data-sf-tariff-list]").querySelectorAll(".tariff__price")].filter((p) => getComputedStyle(p).display !== "none").map((p) => p.querySelector(".tariff__value").textContent)')
        );
        // вид телефона — слайдер с точками
        $I->assertSame(3, $this->js($I, 'return document.querySelector(".tariff-list_view_mobile.tariff-list_slider").querySelectorAll(".swiper-pagination-bullet").length'));
        $I->dontSeeLogProblems();
    }

    public function tariffTablePlanOnPhone(AcceptanceTester $I): void
    {
        $this->open($I, 'Services/Tariff/TariffTable');
        // первая таблица вида телефона (тариф «All» — первый)
        $I->executeJS('const t = document.querySelector(".tariff-table_view_mobile"); t.setAttribute("data-test-table", ""); t.scrollIntoView({block: "center", behavior: "instant"})');
        $table = '[data-test-table]';
        $I->click($table . ' .selector-item[data-value="2"]');
        $I->seeElement($table . '.tariff-table_plan_2 .tariff-table__plan_2');
        $I->dontSeeElement($table . ' .tariff-table__plan_0');
        $I->dontSeeLogProblems();
    }

    public function galleryThumbsAndSteps(AcceptanceTester $I): void
    {
        $this->open($I, 'Services/Hero/HeroGallery');
        $gallery = '[data-sf-hero-gallery]:not(.hero-gallery_view_mobile)';
        $I->waitForJS('return !!document.querySelector(\'' . $gallery . '\').sfGallery', 10);
        $I->executeJS('document.querySelector(arguments[0]).scrollIntoView({block: "center", behavior: "instant"})', [$gallery]);
        $I->click($gallery . ' [data-sf-hero-gallery-thumb="2"]');
        $I->waitForJS('return document.querySelector(\'' . $gallery . '\').sfGallery.activeIndex === 2', 5);
        $I->seeElement($gallery . ' [data-sf-hero-gallery-thumb="2"][aria-pressed="true"]');

        $this->open($I, 'Services/Step/StepMain');
        $I->waitForJS('return !!document.querySelector(".steps_style_scroll [data-sf-slider]").sfSlider', 10);
        $I->dontSeeLogProblems();
    }
}
