<?php


namespace App\Extensions\Upgrade\Hooks;


class Auth extends Hook
{
    public function after($what)
    {
        if ('tpl' == $what) {
            $this->up->replace('/login.php', '/auth/login/');
        }
    }
}