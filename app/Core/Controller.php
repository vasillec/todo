<?php

namespace app\Core;

use app\Components\Auth;
use app\Exceptions\RoutException;

abstract class Controller
{
    protected $view;

    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Проверяет аутентифицирован ли пользователь
     * @return bool
     */
    protected function isLoggedIn()
    {
        return Auth::isLoggedIn();
    }

    /**
     * Проверяет является ли транспортом доставки запроса XMLHttpRequest
     * @return bool
     */
    protected function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }

    /**
     * Метод который будет вызван перед выполнением Action-a
     * @param $method (вызываемый Action)
     */
    protected function before($method)
    {
    }

    /**
     * Метод который будет вызван после выполнением Action-a
     */
    protected function after()
    {
    }

    /**
     * Роутинг уровня контроллера (маршрутизация Action-а)
     * @param $method
     * @param $params
     * @throws RoutException (Обращение к некорректному Action-у)
     */
    public function __call($method, $params) 
    {
        $method = lcfirst(str_replace(' ','', ucwords(str_replace('-',' ', $method)))) . 'Action';
        if (method_exists($this, $method)) {
            $this->before($method);
            call_user_func_array([$this, $method], $params);
        } else {
            throw new RoutException("Call to undefined Action " . static::class . "::{$method}()");
        }
        $this->after();
    }
}