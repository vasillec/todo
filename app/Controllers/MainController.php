<?php

namespace app\Controllers;

use app\Components\Logger;
use app\Core\Controller;
use app\Exceptions\ViewRenderException;

class MainController extends Controller
{
    /**
     * Выполняется перед вызовом запрашиваемого action-a
     * @param $method (запрашиваемый action)
     */
    protected function before($method)
    {
        if (!$this->isLoggedIn()) {
            header('Location: ' . '/');
            die();
        }
    }

    /**
     * Выполняет отрисовку главной страницы
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws \app\Exceptions\AppLogicException (Нарушение логики приложения)
     */
    protected function indexAction(array $params = [])
    {
        try {
            $mustacheTemplate = $this->view->bufferHtmlTemplate('template.html');
            $this->view->render('main.twig', [
                'isAuth' => $_SESSION['isAuth'],
                'userLogin' => $_SESSION['user']->login,
                'mustacheTemplate' => $mustacheTemplate
            ]);
        }catch (ViewRenderException $e){
            Logger::getInstance()->critical($e->getPrevious());
            header("HTTP/1.0 500 Internal Server Error");
            exit('Internal Server Error.');
        }
    }
}