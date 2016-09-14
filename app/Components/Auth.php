<?php

namespace app\Components;

use app\Models\User;

class Auth
{
    /**
     * isLoggedIn() проверяет авторизирован ли пользователь
     * @return bool  (true - пользователь авторизирован, false - если нет)
     */
    public static function isLoggedIn()
    {
        session_start();
        return (isset($_SESSION['isAuth']) && $_SESSION['isAuth']) ? true : false;
    }

    /**
     * logOut() осуществляет процесс разлогинивания пользователя удяляя сессию
     */
    public static function logOut()
    {
        session_start();
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();
    }

    /**
     * @param User $user
     */
    public static function logIn($user)
    {
        $_SESSION['user'] = $user;
        $_SESSION['isAuth'] = true;
    }
}