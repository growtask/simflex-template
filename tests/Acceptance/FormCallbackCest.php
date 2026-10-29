<?php

namespace Tests\Acceptance;

use Facebook\WebDriver\WebDriverKeys;
use Tests\Support\AcceptanceTester;

/**
 * Окно и форма «Связаться с нами» на главной: блок .r-form с формой и окно #callback, которое открывает кнопка
 * data-modal="callback": открытие, Esc, фокус, проверка полей, отправка.
 *
 * Каналы уведомлений в тестовом окружении не настроены (form_email, form_tg_token пусты): заявка принимается
 * и пишется в журнал, поэтому отправка проходит; ответы сервера на ошибки и отправку по каналам
 * проверяют unit-тесты (CallbackHandlerTest) с подменой транспорта
 */
class FormCallbackCest
{
    private const PAGE = '/';
    private const OPENER = '.header__bar .header__cta:not(.header__cta_top)';
    private const FORM = '.r-form [data-callback-form]';
    private const RESULT = '.r-form [data-callback-result]';

    public function tryToOpenAndCloseModal(AcceptanceTester $I): void
    {
        $I->amOnPage(self::PAGE);
        $I->seeElementInDOM('#callback[hidden]');
        $I->click(self::OPENER);
        $I->waitForElementVisible('#callback .modal__dialog');
        $I->seeElement('#callback [role="dialog"][aria-modal="true"]');
        // страница под окном не прокручивается
        $I->seeElementInDOM('html.modal-open');
        // Esc закрывает окно
        $I->pressKey('#callback .modal__dialog', WebDriverKeys::ESCAPE);
        $I->waitForElementNotVisible('#callback .modal__dialog');
        $I->dontSeeElementInDOM('html.modal-open');
        $I->assertTrue($I->executeJS('return document.activeElement.matches(\'[data-modal="callback"]\')'), 'Фокус вернулся на кнопку');

        // закрытие кнопкой и по подложке
        $I->click(self::OPENER);
        $I->waitForElementVisible('#callback .modal__dialog');
        $I->click('#callback .modal__close');
        $I->waitForElementNotVisible('#callback .modal__dialog');
        $I->click(self::OPENER);
        $I->waitForElementVisible('#callback .modal__dialog');
        $I->executeJS("document.querySelector('#callback .modal__overlay').click()");
        $I->waitForElementNotVisible('#callback .modal__dialog');
        $I->dontSeeLogProblems();
    }

    public function tryToSeeClientValidation(AcceptanceTester $I): void
    {
        $I->amOnPage(self::PAGE);
        $I->executeJS('document.querySelector(\'' . self::FORM . ' .callback__submit\').scrollIntoView({block: "center", behavior: "instant"})');
        $I->click(self::FORM . ' .callback__submit');
        $I->seeElement(self::FORM . ' input[name="name"][aria-invalid="true"]');
        $I->seeElement(self::FORM . ' input[name="phone"][aria-invalid="true"]');
        $I->seeElementInDOM(self::FORM . ' input[name="consent"][aria-invalid="true"]');
        // фокус на первом поле с ошибкой
        $I->assertSame('name', $I->executeJS('return document.activeElement.name'));
        $I->see('Заполните поле', self::FORM . ' .callback__error');

        $I->fillField(self::FORM . ' input[name="name"]', 'Иван');
        $I->dontSeeElement(self::FORM . ' input[name="name"][aria-invalid="true"]');
        $I->fillField(self::FORM . ' input[name="phone"]', '12');
        $I->fillField(self::FORM . ' input[name="email"]', 'bad');
        $I->executeJS('document.querySelector(\'' . self::FORM . ' .callback__submit\').scrollIntoView({block: "center", behavior: "instant"})');
        $I->click(self::FORM . ' .callback__submit');
        $I->seeElement(self::FORM . ' input[name="phone"][aria-invalid="true"]');
        $I->seeElement(self::FORM . ' input[name="email"][aria-invalid="true"]');
        $I->see('Введите номер телефона', self::FORM);
        $I->dontSeeLogProblems();
    }

    public function tryToSendForm(AcceptanceTester $I): void
    {
        $I->amOnPage(self::PAGE);
        $I->fillField(self::FORM . ' input[name="name"]', 'Иван Тестов');
        $I->fillField(self::FORM . ' input[name="phone"]', '+7 999 123-45-67');
        $I->fillField(self::FORM . ' textarea[name="comment"]', 'Проверка формы');
        $I->see('14/300', self::FORM . ' .textarea__counter');
        $I->executeJS('document.querySelector(\'' . self::FORM . ' .checkbox__custom\').scrollIntoView({block: "center", behavior: "instant"})');
        $I->click(self::FORM . ' .checkbox__custom');
        // защита от ботов: форма не принимается быстрее трёх секунд после показа
        $I->wait(3);
        $I->executeJS('document.querySelector(\'' . self::FORM . ' .callback__submit\').scrollIntoView({block: "center", behavior: "instant"})');
        $I->click(self::FORM . ' .callback__submit');
        // результат встаёт на место формы
        $I->waitForElement(self::RESULT . ' .result-style_success', 10);
        $I->seeElement(self::RESULT . ' [role="status"]');
        $I->see('Заявка отправлена', self::RESULT);
        $I->dontSeeElement(self::FORM);

        // OK возвращает пустую форму
        $I->click(self::RESULT . ' .result__button');
        $I->seeElement(self::FORM);
        $I->seeInField(self::FORM . ' input[name="name"]', '');
        $I->dontSeeLogProblems();
    }
}
