<?php
namespace app\Controllers;

use app\Components\Logger;
use app\Core\Controller;
use app\Exceptions\ViewRenderException;

class ErrorController extends Controller
{
    /**
     * Выполняет отрисовку страницы ошибки 404
     * @param array $params (параметры переданные пользователем в адресной строке)
     */
    protected function error404Action(array $params = [])
    {
        header("HTTP/1.0 404 Not Found");
        $params = [
            'errorId' => '404',
            'errorText' => 'Page not found',
            'server_name' => $_SERVER['SERVER_NAME']
        ];
        try {
            $this->view->render('error.twig', $params);
        } catch (ViewRenderException $e) {
            Logger::getInstance()->critical($e->getPrevious());
            header("HTTP/1.0 500 Internal Server Error");
            exit('Internal Server Error.');
        }
    }

    /**
     * Выполняет отрисовку страницы ошибки 500
     * @param array $params (параметры переданные пользователем в адресной строке)
     */
    protected function error500Action(array $params = [])
    {
        header("HTTP/1.0 500 Internal Server Error");
        $params = [
            'errorId' => '500',
            'errorText' => 'Internal Server Error',
            'server_name' => $_SERVER['SERVER_NAME']
        ];
        try {
            $this->view->render('error.twig', $params);
        } catch (ViewRenderException $e) {
            Logger::getInstance()->critical($e->getPrevious());
            header("HTTP/1.0 500 Internal Server Error");
            exit('Internal Server Error.');
        }
    }
}