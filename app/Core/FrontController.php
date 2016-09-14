<?php

namespace app\Core;

use app\Components\Logger;
use app\Exceptions\AppLogicException;
use app\Exceptions\DataBaseException;
use app\Exceptions\RoutException;
use app\Exceptions\ViewCreateException;

class FrontController
{
    use TSingletone;

    const DEFAULT_CONTROLLER = 'app\\Controllers\\IndexController';
    const DEFAULT_ACTION = "index";

    protected $controller = self::DEFAULT_CONTROLLER;
    protected $action = self::DEFAULT_ACTION;
    protected $params = [];

    protected function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }

    /**
     * Выполняет маршрутизацию
     * @throws RoutException (Обращение к некорректному Controller-у)
     */
    protected function route()
    {
        $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        @list($controller, $action, $params) = explode('/', $path, 3);
        if (isset($controller)) {
            $this->setController($controller);
        }
        if (isset($action)) {
            $this->action = $action;
        }
        if (isset($params)) {
            $this->params = explode('/', $params);
        }
    }

    /**
     * Проверка на корректность Controller-а
     * @param $controller (запрашиваемый контроллер)
     * @throws RoutException (Обращение к некорректному Controller-у)
     */
    protected function setController($controller)
    {
        if ($controller) {
            $controller =
                'app\\Controllers\\' .
                str_replace(' ', '', ucwords(str_replace('-', ' ', $controller))) .
                'Controller';
            if (!class_exists($controller)) {
                throw new RoutException(
                    "The action controller '$controller' has not been defined.");
            }
            $this->controller = $controller;
        }
    }

    /**
     * routeError хелпер
     * @param $controller
     * @param $method
     * @throws RoutException (Обращение к некорректному Controller-у)
     */
    protected function routeError($controller, $method)
    {
        $this->setController($controller);
        $cont = new $this->controller;
        $cont->$method([]);
    }

    /**
     * Запускает маршрутизацию
     */
    public function run()
    {
        try {
            $this->route();
            $cont = new $this->controller;
            $method = $this->action;
            $cont->$method($this->params);
        } catch (RoutException $e) {
            Logger::getInstance()->error($e);
            $this->routeError('error', 'error-404');
        } catch (ViewCreateException $e) {
            Logger::getInstance()->critical($e->getPrevious());
            header("HTTP/1.0 500 Internal Server Error");
            exit('Internal Server Error.');

        } catch (AppLogicException $e) {
            Logger::getInstance()->critical($e);
            if (!$this->isAjax()) {
                $this->routeError('error', 'error-500');
            } else {
                header("HTTP/1.0 500 Internal Server Error");
                exit('Internal Server Error.');
            }
        } catch (DataBaseException $e) {
            Logger::getInstance()->critical($e->getPrevious());
            if (!$this->isAjax()) {
                $this->routeError('error', 'error-500');
            } else {
                header("HTTP/1.0 500 Internal Server Error");
                exit('Database Server Error.');
            }
        } catch (\Exception $e) {
            Logger::getInstance()->critical($e->getPrevious());
            header("HTTP/1.0 500 Internal Server Error");
            exit('Internal Server Error.');
        }
    }
}