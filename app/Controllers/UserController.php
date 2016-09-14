<?php

namespace app\Controllers;

use app\Components\Auth;
use app\Components\Logger;
use app\Core\Controller;
use app\Exceptions\AppLogicException;
use app\Exceptions\AuthFailedException;
use app\Exceptions\DataBaseException;
use app\Exceptions\InvalidParameterException;
use app\Exceptions\ARSaveException;
use app\Models\User;

class UserController extends Controller
{
    /**
     * Выполняется перед вызовом запрашиваемого action-a
     * @param $method (запрашиваемый action)
     * @throws AppLogicException (Нарушение логики приложения)
     */
    protected function before($method)
    {
        if (!$this->isAjax()) {
            throw new AppLogicException("Request is not XmlHttpRequest");
        } else if ('POST' != $_SERVER['REQUEST_METHOD']) {
            throw new AppLogicException("Incorrect request method: {$_SERVER['REQUEST_METHOD']}.");
        } else if ($this->isLoggedIn() && $method != 'logoutAction') {
            throw new AppLogicException('Trying authenticated user access is not relevant to his status methods.');
        }
    }

    /**
     * Проверяет не занят ли логин
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function checkLoginAction(array $params = [])
    {
        try {
            if (!isset($_POST['login'])) {
                throw new InvalidParameterException('It was not passed argument login.');
            }
            $res = User::findLogin($_POST['login']);
            $this->view->responseJson(json_encode($res));
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
        }
    }

    /**
     * Аутентификация пользователя
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function authAction(array $params = [])
    {
        try {
            if (isset($_POST['login']) && isset($_POST['pwd'])) {
                User::authUser($_POST['login'], $_POST['pwd']);
                $this->view->responseJson(json_encode(true));
            } else {
                throw new InvalidParameterException('It was not passed arguments.');
            }
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
        } catch (AuthFailedException $e){
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
        }
    }

    /**
     * Выход пользователя из системы и завершение сеанса
     * @param array $params (параметры переданные пользователем в адресной строке)
     */
    protected function logoutAction(array $params = [])
    {
        Auth::logOut();
        $this->view->responseJson(json_encode(true));
    }

    /**
     * Регистрация пользователя
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function regAction(array $params = [])
    {
        try {
            if (!isset($_POST['login']) &&
                !isset($_POST['pwd']) &&
                !isset($_POST['pwd_c'])
            ) {
                throw new InvalidParameterException('It was not passed arguments.');
            }
            User::registeredUser($_POST['login'], $_POST['pwd'], $_POST['pwd_c']);
            $this->view->responseJson(json_encode(true));
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
        } catch (ARSaveException $e){
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to register user.']));
        }
    }
}