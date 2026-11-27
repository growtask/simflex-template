<?php

namespace Tests\Acceptance;

use Facebook\WebDriver\WebDriverKeys;
use Tests\Support\AcceptanceTester;

/**
 * Поведение UI-компонентов на страницах витрины /ui?component=… (план дизайн-системы, этап 2): фокус только с
 * клавиатуры, список, поле даты с календарём, аккордеон, вкладки, тосты, теги, сортировка, загрузка кнопки.
 * Скрипты — ванильные (assetsGlobal/script.js и script.js компонентов)
 */
class UiBehaviorCest
{
    private function open(AcceptanceTester $I, string $component): void
    {
        $I->amOnPage('/ui?component=' . $component);
        $I->waitForJS('return !!(window.SfUi && window.SfUi.core) && document.readyState === "complete"', 10);
    }

    /** Прокрутить к элементу и кликнуть мышью (WebDriver) */
    private function clickInView(AcceptanceTester $I, string $selector): void
    {
        $I->executeJS('document.querySelector(arguments[0]).scrollIntoView({block: "center", behavior: "instant"})', [$selector]);
        $I->click($selector);
    }

    /** Нажать клавишу в элементе, который сейчас в фокусе */
    private function keyOnActive(AcceptanceTester $I, string $key): void
    {
        $I->executeJS('document.querySelectorAll("[data-test-active]").forEach((el) => el.removeAttribute("data-test-active")); document.activeElement.setAttribute("data-test-active", "")');
        $I->pressKey('[data-test-active]', $key);
    }

    private function js(AcceptanceTester $I, string $code): mixed
    {
        return $I->executeJS($code);
    }

    public function focusRingOnlyFromKeyboard(AcceptanceTester $I): void
    {
        $this->open($I, 'UI/Input/InputMain');
        $field = '.text-input-size_m .text-input__field';
        $this->clickInView($I, $field);
        $I->assertSame('none', $this->js($I, 'return getComputedStyle(document.activeElement).outlineStyle'), 'По клику кольца нет');
        $I->assertFalse($this->js($I, 'return document.documentElement.classList.contains("sf-kbd")'));
        $I->pressKey($field, WebDriverKeys::TAB);
        $I->assertTrue($this->js($I, 'return document.documentElement.classList.contains("sf-kbd")'));
        $I->assertSame('solid', $this->js($I, 'return getComputedStyle(document.activeElement).outlineStyle'), 'С клавиатуры кольцо есть');
        $I->dontSeeLogProblems();
    }

    public function dropdownByMouseAndKeyboard(AcceptanceTester $I): void
    {
        $this->open($I, 'UI/Dropdown/DropdownMain');
        $field = '#sf-dropdown-2';
        $panel = '#sf-dropdown-2-panel';
        $this->clickInView($I, $field);
        $I->waitForElementVisible($panel);
        $I->seeElement($field . '[aria-expanded="true"]');
        // Esc закрывает, фокус возвращается на поле
        $this->keyOnActive($I, WebDriverKeys::ESCAPE);
        $I->waitForElementNotVisible($panel);
        $I->seeElement($field . '[aria-expanded="false"]');
        $I->assertSame('sf-dropdown-2', $this->js($I, 'return document.activeElement.id'));

        // выбор с клавиатуры: открыть, вниз, Enter
        $I->pressKey($field, WebDriverKeys::ENTER);
        $I->waitForElementVisible($panel);
        $before = $this->js($I, 'return document.activeElement.textContent.trim()');
        $this->keyOnActive($I, WebDriverKeys::ARROW_DOWN);
        $chosen = $this->js($I, 'return document.activeElement.textContent.trim()');
        $I->assertNotSame($before, $chosen, 'Стрелка переводит фокус на следующий вариант');
        $this->keyOnActive($I, WebDriverKeys::ENTER);
        $I->waitForElementNotVisible($panel);
        $I->see($chosen, $field);

        // клик снаружи закрывает
        $this->clickInView($I, $field);
        $I->waitForElementVisible($panel);
        $this->clickInView($I, 'h1');
        $I->waitForElementNotVisible($panel);
        $I->dontSeeLogProblems();
    }

    public function dateFieldPicksDayInCalendar(AcceptanceTester $I): void
    {
        $this->open($I, 'UI/Input/InputDate');
        $root = '.text-input:has([data-sf-date="date"])';
        $toggle = $root . ' [data-sf-date-toggle]';
        $this->clickInView($I, $toggle);
        $I->waitForElementVisible($root . ' .text-input__calendar');
        $I->seeElement($toggle . '[aria-expanded="true"]');
        // день 15 текущего месяца
        $I->executeJS('document.querySelector(arguments[0]).closest(".text-input").querySelectorAll(".text-input__calendar .calendar__day:not(.calendar__day_muted)")[14].click()', [$toggle]);
        $I->waitForElementNotVisible($root . ' .text-input__calendar');
        $value = $this->js($I, 'return document.querySelector(\'' . $root . ' [data-sf-date]\').value');
        $I->assertMatchesRegularExpression('/^15\.\d{2}\.\d{4}$/', $value);

        // Esc закрывает календарь
        $this->clickInView($I, $toggle);
        $I->waitForElementVisible($root . ' .text-input__calendar');
        $I->pressKey($root . ' .text-input__calendar [tabindex="0"]', WebDriverKeys::ESCAPE);
        $I->waitForElementNotVisible($root . ' .text-input__calendar');
        $I->dontSeeLogProblems();
    }

    public function accordionOpensOneItem(AcceptanceTester $I): void
    {
        $this->open($I, 'UI/Accordion');
        $first = '#sf-accordion-1-content';
        $I->seeElementInDOM($first . '[inert]');
        $this->clickInView($I, '[aria-controls="sf-accordion-1-content"]');
        $I->waitForElement('[aria-controls="sf-accordion-1-content"][aria-expanded="true"]');
        $I->dontSeeElementInDOM($first . '[inert]');
        $I->dontSeeLogProblems();
    }

    public function tabsSwitchAndGlide(AcceptanceTester $I): void
    {
        $this->open($I, 'UI/Tab/TabMain');
        $group = '[data-sf-tabs]';
        $this->clickInView($I, $group . ' .tab:nth-child(3)');
        $I->seeElement($group . ' .tab:nth-child(3).active[aria-selected="true"]');
        $I->seeElement($group . ' .tab:nth-child(1)[aria-selected="false"]');
        // подложка летит от прежней вкладки
        $I->assertTrue($this->js($I, 'return document.querySelector(\'' . $group . '\').hasAttribute("data-sf-glide")'));
        // стрелки: недоступная четвёртая пропускается, по кругу к первой
        $I->pressKey($group . ' .tab:nth-child(3)', WebDriverKeys::ARROW_RIGHT);
        $I->seeElement($group . ' .tab:nth-child(1).active');
        $I->assertSame('0', $this->js($I, 'return String(document.activeElement.tabIndex)'));
        $I->pressKey($group . ' .tab:nth-child(1)', WebDriverKeys::END);
        $I->seeElement($group . ' .tab:nth-child(3).active');
        $I->dontSeeLogProblems();
    }

    public function toastsStackAsDeck(AcceptanceTester $I): void
    {
        $this->open($I, 'UI/Feedback/FeedbackToast');
        foreach (['default', 'success', 'error', 'default'] as $type) {
            $this->clickInView($I, '[data-ui-toast="' . $type . '"]');
        }
        $I->waitForJS('return document.querySelectorAll("[data-sf-toast-stack] [data-sf-toast]").length === 3', 5);
        // новый — спереди, остальные позади уменьшены
        $I->assertSame(
            ['toast_style_success', 'toast_style_error', 'toast_style_default'],
            $this->js($I, 'return [...document.querySelectorAll("[data-sf-toast-stack] [data-sf-toast]")].map((t) => t.className.match(/toast_style_\w+/)[0])')
        );
        $I->assertSame([true, true, false], $this->js($I, 'return [...document.querySelectorAll("[data-sf-toast-stack] [data-sf-toast]")].map((t) => t.hasAttribute("data-toast-back"))'));
        $I->dontSeeLogProblems();
    }

    public function tagsInputAddsAndRemovesTags(AcceptanceTester $I): void
    {
        $this->open($I, 'UI/Input/InputTags');
        $root = '.tags-input[data-allow-new]';
        $I->seeElementInDOM($root);
        $text = $root . ' input.text-input__field';
        $this->clickInView($I, $text);
        $I->fillField($text, 'Новый тег');
        $I->pressKey($text, WebDriverKeys::ENTER);
        $I->waitForJS('return [...document.querySelectorAll(\'' . $root . ' .tags-input__tag\')].some((t) => t.dataset.value === "Новый тег")', 5);
        $I->assertStringContainsString('Новый тег', $this->js($I, 'return document.querySelector(\'' . $root . ' [data-sf-tags-value]\').value'));
        // Backspace в пустом поле удаляет последний тег
        $I->pressKey($text, WebDriverKeys::BACKSPACE);
        $I->waitForJS('return ![...document.querySelectorAll(\'' . $root . ' .tags-input__tag\')].some((t) => t.dataset.value === "Новый тег")', 5);
        $I->dontSeeLogProblems();
    }

    public function sortInPlaceAndButtonLoad(AcceptanceTester $I): void
    {
        $this->open($I, 'UI/Switch/SwitchSort');
        $box = '[data-sf-sort]:has(> button)';
        $this->clickInView($I, $box . ' > button:last-of-type');
        $I->assertSame('descending', $this->js($I, 'return document.querySelector(\'' . $box . ' > .sort__glide\').dataset.direction'));
        $this->clickInView($I, $box . ' > button:last-of-type');
        $I->assertSame('none', $this->js($I, 'return document.querySelector(\'' . $box . ' > .sort__glide\').dataset.direction'));

        $this->open($I, 'UI/Button/ButtonMain');
        $button = '[id^="ui-btn-morph"] .btn';
        $this->js($I, 'SfUi.buttonLoad(document.querySelector(\'' . $button . '\'), true)');
        $I->waitForElement($button . '.loading.btn_morph-shrink[aria-busy="true"]', 3);
        $this->js($I, 'SfUi.buttonResult(document.querySelector(\'' . $button . '\'), "success")');
        $I->seeElement($button . '.btn_result .btn__result svg');
        $I->waitForJS('return !document.querySelector(\'' . $button . '\').classList.contains("btn_result")', 3);
        $I->dontSeeElement($button . '[aria-busy]');
        $I->dontSeeLogProblems();
    }

    public function passwordCodeStepperAndTooltip(AcceptanceTester $I): void
    {
        // пароль: кнопка-глаз показывает и прячет текст
        $this->open($I, 'UI/Input/InputPassword');
        $toggle = '.text-input:has([data-sf-password-toggle]:not([disabled])) [data-sf-password-toggle]';
        $field = '.text-input:has([data-sf-password-toggle]:not([disabled])) .text-input__field';
        $this->clickInView($I, $toggle);
        $I->seeElement($toggle . '[aria-pressed="true"]');
        $I->assertSame('text', $this->js($I, 'return document.querySelector(\'' . $field . '\').type'));
        $this->clickInView($I, $toggle);
        $I->assertSame('password', $this->js($I, 'return document.querySelector(\'' . $field . '\').type'));

        // код: цифры переходят по ячейкам, значение собирается в скрытом поле
        $this->open($I, 'UI/Input/InputCode');
        $code = '#ui-code-empty-light';
        $this->clickInView($I, $code . ' .code-input__digit');
        $I->pressKey($code . ' .code-input__digit', '1', '2', '3', '4');
        $I->waitForJS('return document.querySelector(\'' . $code . ' [data-sf-code-value]\').value.startsWith("1234")', 3);

        // количество: «+» увеличивает, «−» на минимуме недоступна
        $this->open($I, 'UI/Stepper/StepperQuantity');
        $stepper = '[data-sf-stepper]:has([data-sf-stepper-step="-1"][disabled])';
        $input = $this->js($I, 'const s = document.querySelector(\'' . $stepper . '\'); s.id ||= "test-stepper"; return s.id');
        $before = (int)$this->js($I, 'return document.querySelector("#' . $input . ' input").value');
        $this->clickInView($I, '#' . $input . ' [data-sf-stepper-step="1"]');
        $I->assertSame($before + 1, (int)$this->js($I, 'return document.querySelector("#' . $input . ' input").value'));
        $I->dontSeeElement('#' . $input . ' [data-sf-stepper-step="-1"][disabled]');

        // подсказка: показ по фокусу, Esc прячет
        $this->open($I, 'UI/Tooltip/TooltipMain');
        $I->executeJS('document.querySelector("[data-sf-tooltip] button").scrollIntoView({block: "center", behavior: "instant"})');
        $I->pressKey('[data-sf-tooltip] button', WebDriverKeys::SHIFT);
        $this->js($I, 'document.querySelector("[data-sf-tooltip] button").focus()');
        $I->waitForElementVisible('#sf-tooltip-1', 3);
        $this->keyOnActive($I, WebDriverKeys::ESCAPE);
        $I->waitForElementNotVisible('#sf-tooltip-1', 3);
        $I->dontSeeLogProblems();
    }
}
