<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use DomainException;
use Simflex\Core\Auth\SessionStorage;
use Simflex\Core\Http\Csrf;
use Simflex\Core\Http\Request;
use Simflex\Core\System\Session;

final class CsrfTest extends Unit
{
    private mixed $savedToken;
    private mixed $savedUserId;

    protected function _before(): void
    {
        // Codeception уже вывел заголовок в CLI: подавляем только предупреждения PHP о сессии.
        set_error_handler(static fn(int $level, string $message) => str_starts_with($message, 'session_'));
        $this->savedToken = Session::get(Csrf::SESSION_KEY);
        $this->savedUserId = SessionStorage::get();
        Session::set(Csrf::SESSION_KEY, null);
    }

    protected function _after(): void
    {
        try {
            SessionStorage::set($this->savedUserId);
            Session::set(Csrf::SESSION_KEY, $this->savedToken);
        } finally {
            restore_error_handler();
        }
    }

    public function testTokenPersistsAndValidPostSucceeds(): void
    {
        $token = Csrf::token();
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
        $this->assertSame($token, Csrf::token());
        $this->assertTrue(Csrf::isValid(Request::create('/', post: ['csrf' => $token])));
        Csrf::check(Request::create('/', post: ['csrf' => $token]));
    }

    public function testOnlyPostWithTokenFromCurrentSessionIsAccepted(): void
    {
        $token = Csrf::token();
        foreach (['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD'] as $method) {
            $this->assertFalse(Csrf::isValid(Request::create('/', post: ['csrf' => $token], server: ['REQUEST_METHOD' => $method])));
        }
        foreach ([null, '', [], ['token' => $token], 123, str_repeat('a', 64)] as $invalid) {
            $this->assertFalse(Csrf::isValid(Request::create('/', post: ['csrf' => $invalid])));
        }
        $this->assertFalse(Csrf::isValid(Request::create('/?csrf=' . $token, server: ['REQUEST_METHOD' => 'POST'])));
        Session::set(Csrf::SESSION_KEY, null);
        $this->assertFalse(Csrf::isValid(Request::create('/', post: ['csrf' => $token])));
        $this->assertNotSame($token, Csrf::token());
    }

    public function testAjaxHeaderAndInvalidFieldPrecedence(): void
    {
        $token = Csrf::token();
        $server = ['REQUEST_METHOD' => 'POST', 'HTTP_X_CSRF_TOKEN' => $token];
        $this->assertTrue(Csrf::isValid(Request::create('/', server: $server)));
        $this->assertFalse(Csrf::isValid(Request::create('/', post: ['csrf' => 'wrong'], server: $server)));
    }

    public function testLoginAndLogoutInvalidatePreviousToken(): void
    {
        $beforeLogin = Csrf::token();
        SessionStorage::set(42);
        $afterLogin = Csrf::token();
        $this->assertNotSame($beforeLogin, $afterLogin);
        $this->assertFalse(Csrf::isValid(Request::create('/', post: ['csrf' => $beforeLogin])));
        SessionStorage::set(null);
        $this->assertNotSame($afterLogin, Csrf::token());
        $this->assertFalse(Csrf::isValid(Request::create('/', post: ['csrf' => $afterLogin])));
    }

    public function testCheckRejectsInvalidRequest(): void
    {
        $this->expectException(DomainException::class);
        Csrf::check(Request::create('/'));
    }

    public function testMutationNeedsJsonBodyOrToken(): void
    {
        $token = Csrf::token();

        $this->assertTrue(Csrf::isTrustedMutation(\Simflex\Core\Http\Api\JsonRequest::createJson('/cart', ['qty' => 1])));
        $this->assertTrue(Csrf::isTrustedMutation(Request::create('/cart', post: ['qty' => 1, Csrf::FIELD => $token])));
        $this->assertFalse(Csrf::isTrustedMutation(Request::create('/cart', post: ['qty' => 1])));
        // GET не изменяет данные, даже с JSON или токеном
        $this->assertFalse(Csrf::isTrustedMutation(Request::create('/cart?' . Csrf::FIELD . '=' . $token)));
        $this->assertFalse(Csrf::isTrustedMutation(
            \Simflex\Core\Http\Api\JsonRequest::createJson('/cart', ['qty' => 1], 'GET')
        ));

        try {
            Csrf::checkMutation(Request::create('/cart', post: ['qty' => 1]));
            $this->fail('Ожидалось ApiException');
        } catch (\Simflex\Core\Http\Api\ApiException $ex) {
            $this->assertSame(403, $ex->getHttpStatus());
        }
    }
}
