<?php

namespace app\Controllers;

use app\Components\Logger;
use app\Core\Controller;
use app\Exceptions\ViewRenderException;

class IndexController extends Controller
{
    /**
     * Выполняется перед вызовом запрашиваемого action-a
     * @param $method (запрашиваемый action)
     */
    protected function before($method)
    {
        if ($this->isLoggedIn()) {
            header('Location: /main');
            die();
        }
    }

    /**
     * Выполняет отрисовку страницы авторизации
     * @param array $params (параметры переданные пользователем в адресной строке)
     */
    protected function indexAction(array $params = [])
    {
        try {
            $this->view->render('auth.twig', []);
        }catch (ViewRenderException $e){
            Logger::getInstance()->critical($e->getPrevious());
            header("HTTP/1.0 500 Internal Server Error");
            exit('Internal Server Error.');
        }
    }
}