<?php


namespace App\Extensions\Auth;


use Simflex\Auth\Bootstrap;
use Simflex\Auth\CookieTokenBag;
use Simflex\Auth\Models\UserAuth;
use Simflex\Auth\SessionStorage;
use Simflex\Core\Container;
use Simflex\Core\ControllerBase;
use Simflex\Core\DB;
use Simflex\Core\Models\User;

class Auth extends ControllerBase
{

    const REMEMBER_ME_INTERVAL = '1 WEEK';

    public function login()
    {
        $login = $_REQUEST['login'];
        $password = $_REQUEST['password'];
        $redirect = $_REQUEST['r'] ?? '/';
        $isRemember = !empty($_REQUEST['remember']);
        if (strpos($redirect, '//') !== false) {
            $redirect = '/';
        }
        if (preg_match('@^[0-9a-z\@\-\.]+$@i', $login)) {
            $q = "SELECT u.*
                    FROM user u
                    JOIN user_role r ON r.role_id=u.role_id
                    WHERE login=:login
                      AND u.active=1
                      AND r.active=1";
            $row = DB::result($q, '', ['login' => strtolower($login)]);
            if ($row) {
                if (md5($password) === $row['password']) {
                    SessionStorage::set($row['user_id']);
                    Bootstrap::authByUser((new User())->fill($row));
                    if ($isRemember) {
                        $auth = UserAuth::create($row['user_id'], static::REMEMBER_ME_INTERVAL);
                        $cookies = new CookieTokenBag(CookieTokenBag::defaultPrefix());
                        $cookies->set($auth->token, new \DateTime(static::REMEMBER_ME_INTERVAL));
                    }
                }
            }
        }
        header("Location: $redirect");
        exit;
    }

    public function logout()
    {
        $redirect = $_REQUEST['r'] ?? '/';
        if (strpos($redirect, '//') !== false) {
            $redirect = '/';
        }
        Bootstrap::signOut();
        header("Location: $redirect");
        exit;
    }

    public function test()
    {
        if (Container::getUser()) {
            print_r(Container::getUser()->toArray());
        }
        var_dump(Container::getUserLegacy()::$id);
    }

}