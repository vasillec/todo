<?php

namespace app\Core;

use app\Exceptions\AppLogicException;
use app\Exceptions\ViewCreateException;
use app\Exceptions\ViewRenderException;

class View
{
    protected $twig;

    public function __construct()
    {
        try {
            $loader = new \Twig_Loader_Filesystem('../app/Templates');
            $this->twig = new \Twig_Environment($loader, []);
        } catch (\Twig_Error $e) {
            throw new ViewCreateException('', null, $e);
        }

    }

    /**
     * Вывод обработанного шаблона пользователю
     * @param $tpl (название шаблона)
     * @param array $params (массив параметров для шаблона)
     * @throws ViewRenderException (Содержит информацию об ошибке шаблонизвтора)
     */
    public function render($tpl, array $params = [])
    {
        try {
            echo $this->twig->render($tpl, $params);
        } catch (\Twig_Error $e) {
            throw new ViewRenderException('', null, $e);
        }
    }

    /**
     * Отдает ответ в виде строки
     * @param string $response
     */
    public function response($response = '')
    {
        echo $response;
    }

    /**
     * Отдает ответ в виде json-а
     * @param $json
     */
    public function responseJson($json)
    {
        echo $json;
    }

    /**
     * Извлекает содержимое файла шаблона
     * @param $template (названиие файла шаблона)
     * @return mixed (содержимое файла шаблоном)
     * @throws AppLogicException (не найден файл шаблона)
     */
    public function bufferHtmlTemplate($template)
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/app/Templates/' . $template;
        ob_start();
        $success = include $file;
        if (!$success) {
            ob_end_clean();
            throw new AppLogicException("The file '$file' could not be included.");
        }
        return ob_get_clean();
    }
}