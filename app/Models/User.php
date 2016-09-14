<?php

namespace app\Models;

use app\Components\Auth;
use app\Core\Model;
use app\Exceptions\AuthFailedException;
use app\Exceptions\InvalidParameterException;

class User extends Model
{
    const TABLE = 'users';

    public $id;
    public $login;
    protected $password;

    protected function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Проверяет занят ли login
     * @param string $login (проверяемый login)
     * @return bool (true - если логин не найден, false - если логин занят)
     * @throws InvalidParameterException (переданы невалидные параметры)
     */
    public static function findLogin($login = '')
    {
        $login = trim(strip_tags($login));
        if (!preg_match('/^[a-zа-яё0-9-_]{3,20}$/iu', $login)
        ) {
            throw new InvalidParameterException('Login does not comply with the limits.');
        }
        $res = self::find(['login' => $login]);
        return $res ? false : true;
    }

    /**
     * Проверяет соответствию пароль
     * @param $password (проверяемый пароль)
     * @return bool (true - если пароль совпал, false - не подошел)
     */
    public function comparePassword($password)
    {
        if ($password && password_verify($password, $this->password)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Регистрирует пользователя
     * @param string $login (логин пользователя)
     * @param string $pwd (пароль)
     * @param string $pwd_c (повторный пароль)
     * @return bool (true - успешная регистрация, false - если нет)
     * @throws InvalidParameterException (переданы невалидные параметры)
     */
    public static function registeredUser($login = '', $pwd = '', $pwd_c = '')
    {
        $login = trim(strip_tags($login));
        $pwd = trim(strip_tags($pwd));
        $pwd_c = trim(strip_tags($pwd_c));
        if (preg_match('/^[a-zа-яё0-9-_]{3,20}$/iu', $login) &&
            preg_match('/^[a-z0-9]{5,20}$/i', $pwd) &&
            preg_match('/^[a-z0-9]{5,20}$/i', $pwd_c) &&
            $pwd === $pwd_c
        ) {
            if (self::findLogin($login)) {
                $user = new self($login, $pwd);
                $user->save();
                return;
            }
            throw new InvalidParameterException('Login is already in use');
        }
        throw new InvalidParameterException('Parameters does not comply with the limits.');
    }

    /**
     * Аутентификация пользователя
     * @param string $login
     * @param string $pwd
     * @throws InvalidParameterException (переданы невалидные параметры)
     * @throws AuthFailedException (не удалось авторизовать пользователя с указаннми параметраим)
     */
    public static function authUser($login = '', $pwd = '')
    {
        $login = trim(strip_tags($login));
        $pwd = trim(strip_tags($pwd));
        if (preg_match('/^[a-zа-яё0-9-_]{3,20}$/iu', $login) &&
            preg_match('/^[a-z0-9]{5,20}$/i', $pwd)
        ) {
            $user = User::find(['login' => $login])[0];
            if ($user && $user->comparePassword($pwd)) {
                Auth::logIn($user);
                return;
            }
            throw new AuthFailedException('Wrong login or password.');
        }
        throw new InvalidParameterException('Parameters does not comply with the limits.');
    }
}