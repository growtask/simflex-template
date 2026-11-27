<?php

namespace Tests\Acceptance;

use Facebook\WebDriver\WebDriverKeys;
use Tests\Support\AcceptanceTester;

/**
 * Поведение компонентов каталога на витрине /ui (часть 5a плана дизайн-системы): окно поиска, «Показать все»
 * в карточке категории, мега-меню, уровни меню каталога на телефоне
 */
class CatalogBehaviorCest
{
    private function open(AcceptanceTester $I, string $component): void
    {
        $I->amOnPage('/ui?component=' . $component);
        $I->waitForJS('return !!(window.SfUi && window.SfUi.core) && document.readyState === "complete"', 10);
    }

    private function key(AcceptanceTester $I, string $key): void
    {
        $I->executeInSelenium(static fn($driver) => $driver->getKeyboard()->sendKeys($key));
    }

    public function searchOpensByFocusAndClosesByEscape(AcceptanceTester $I): void
    {
        $this->open($I, 'Catalog/Search/SearchMain');
        $input = '#search-demo-light-input';
        $panel = '#search-demo-light-panel';
        $I->executeJS('localStorage.removeItem("sf-search-recent"); document.querySelector("#search-demo-light").scrollIntoView({block: "center", behavior: "instant"})');
        $I->dontSeeElement($panel);
        $I->click($input);
        $I->waitForElementVisible($panel, 5);
        $I->seeElement($input . '[aria-expanded="true"]');
        // ↓ — к первой строке (недавний запрос), Esc — закрыть и вернуть фокус в поле
        $this->key($I, WebDriverKeys::ARROW_DOWN);
        $I->assertTrue($I->executeJS('return document.activeElement.classList.contains("search-query__text")'));
        $this->key($I, WebDriverKeys::ESCAPE);
        $I->waitForElementNotVisible($panel, 5);
        $I->assertSame('search-demo-light-input', $I->executeJS('return document.activeElement.id'));
        // крестик убирает недавний запрос
        $I->click($input);
        $I->waitForElementVisible($panel, 5);
        $count = $I->executeJS('return document.querySelectorAll("#search-demo-light-panel [data-sf-search-remove]").length');
        $I->click($panel . ' [data-sf-search-remove]');
        $I->assertSame($count - 1, $I->executeJS('return document.querySelectorAll("#search-demo-light-panel [data-sf-search-remove]").length'));
        $I->dontSeeLogProblems();
    }

    public function categoryCardShowsAllSubcategories(AcceptanceTester $I): void
    {
        $this->open($I, 'Catalog/Card/CardCategory');
        $I->executeJS('const b = document.querySelector("[data-sf-category-card-more]"); b.setAttribute("data-test-more", ""); b.scrollIntoView({block: "center", behavior: "instant"})');
        $I->assertSame(0, $I->executeJS('return [...document.getElementById(document.querySelector("[data-test-more]").getAttribute("aria-controls")).querySelectorAll("[data-sf-category-card-extra]")].filter((li) => !li.hidden).length'));
        $I->click('[data-test-more]');
        $I->seeElement('[data-test-more][aria-expanded="true"]');
        $I->assertSame(2, $I->executeJS('return [...document.getElementById(document.querySelector("[data-test-more]").getAttribute("aria-controls")).querySelectorAll("[data-sf-category-card-extra]")].filter((li) => !li.hidden).length'));
        $I->dontSeeLogProblems();
    }

    public function megaMenuSwitchesColumnsByHoverAndKeys(AcceptanceTester $I): void
    {
        $this->open($I, 'Catalog/Nav/NavMega');
        $menu = '#nav-mega-light';
        $I->executeJS('document.querySelector("#nav-mega-light").scrollIntoView({block: "start", behavior: "instant"})');
        $I->seeElement($menu . ' [data-sf-nav-panel="c0"]');
        $I->moveMouseOver($menu . ' [data-sf-nav-item="c1"]');
        $I->waitForElementVisible($menu . ' [data-sf-nav-panel="c1"]', 5);
        $I->dontSeeElement($menu . ' [data-sf-nav-panel="c0"]');
        $I->seeElement($menu . ' [data-sf-nav-panel="c1-0"]');
        // → с категории — к первой подкатегории, ← — обратно
        $I->executeJS('document.querySelector("#nav-mega-light [data-sf-nav-item=\'c0\']").focus()');
        $I->waitForElementVisible($menu . ' [data-sf-nav-panel="c0"]', 5);
        $this->key($I, WebDriverKeys::ARROW_RIGHT);
        $I->assertSame('c0', $I->executeJS('return document.activeElement.closest("[data-sf-nav-panel]")?.dataset.sfNavPanel'));
        $this->key($I, WebDriverKeys::ARROW_LEFT);
        $I->assertSame('c0', $I->executeJS('return document.activeElement.dataset.sfNavItem'));
        $I->dontSeeLogProblems();
    }

    public function phoneCatalogOpensLevels(AcceptanceTester $I): void
    {
        $this->open($I, 'Catalog/Nav/NavCatalog');
        $dialog = '#nav-catalog-light';
        $I->executeJS('document.querySelector("#nav-catalog-light").scrollIntoView({block: "start", behavior: "instant"})');
        $I->click($dialog . ' [data-sf-nav-item="n-0"]');
        $I->waitForElementVisible($dialog . ' [data-sf-nav-panel="n-0"]', 5);
        $I->waitForElementNotVisible($dialog . ' [data-sf-nav-panel="n"]', 5);
        $I->click($dialog . ' [data-sf-nav-panel="n-0"] [data-sf-nav-back]');
        $I->waitForElementVisible($dialog . ' [data-sf-nav-panel="n"]', 5);
        $I->dontSeeLogProblems();
    }
}
