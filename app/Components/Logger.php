<?php

namespace app\Components;

use app\Config;
use app\Core\TSingletone;
use app\Exceptions\AppLogicException;

class Logger
{
    use TSingletone;

    protected $logger;

    protected function __construct()
    {
        if (Config::$PROJECT_STATUS === Config::PROJECT_DEVELOPMENT) {
            $logger = new \Monolog\Logger('debug');
            $browserHanlder = new \Monolog\Handler\BrowserConsoleHandler(\Monolog\Logger::INFO);
            $browserHanlder->setFormatter(new \Monolog\Formatter\LineFormatter("[%datetime%] %channel%.%level_name%: %message% %extra% %context%\n"));
            $logger->pushHandler($browserHanlder);
            $this->logger = $logger;
        } else {
            $logger = new \Monolog\Logger('app');
            $streamHandler = new \Monolog\Handler\RotatingFileHandler($_SERVER['DOCUMENT_ROOT'] . '/app/Log/app.log', 0, \Monolog\Logger::WARNING);
            $streamHandler->setFormatter(new \Monolog\Formatter\LineFormatter("[%datetime%] %channel%.%level_name%: %message% %extra% %context%\n"));
            $logger->pushHandler($streamHandler);
            $logger->pushProcessor(new \Monolog\Processor\WebProcessor());
            $this->logger = $logger;
        }
    }

    protected function getMessage(\Exception $e)
    {
        return "{$e->getMessage()}" . ' | thrown in ' . $e->getFile() . ' on line ' . $e->getLine() . ' | ';
    }

    public function debug(\Exception $e)
    {
        $message = $this->getMessage($e);
        $this->logger->debug($message, ['user' => $_SESSION['user'] ? $_SESSION['user']->login : 'unauthorized',
            'user_ip' => $_SERVER['REMOTE_ADDR']]);
    }

    public function info(\Exception $e)
    {
        $message = $this->getMessage($e);
        $this->logger->info($message, ['user' => $_SESSION['user'] ? $_SESSION['user']->login : 'unauthorized',
            'user_ip' => $_SERVER['REMOTE_ADDR']]);
    }

    public function notice(\Exception $e)
    {
        $message = $this->getMessage($e);
        $this->logger->notice($message, ['user' => $_SESSION['user'] ? $_SESSION['user']->login : 'unauthorized',
            'user_ip' => $_SERVER['REMOTE_ADDR']]);
    }

    public function warning(\Exception $e)
    {
        $message = $this->getMessage($e);
        $this->logger->warning($message, ['user' => $_SESSION['user'] ? $_SESSION['user']->login : 'unauthorized',
            'user_ip' => $_SERVER['REMOTE_ADDR']]);
    }

    public function error(\Exception $e)
    {
        $message = $this->getMessage($e);
        $this->logger->error($message, ['user' => $_SESSION['user'] ? $_SESSION['user']->login : 'unauthorized',
            'user_ip' => $_SERVER['REMOTE_ADDR']]);
    }

    public function critical(\Exception $e)
    {
        $message = $this->getMessage($e);
        $this->logger->critical($message, ['user' => $_SESSION['user'] ? $_SESSION['user']->login : 'unauthorized',
            'user_ip' => $_SERVER['REMOTE_ADDR']]);
    }

    public function alert(\Exception $e)
    {
        $message = $this->getMessage($e);
        $this->logger->alert($message, ['user' => $_SESSION['user'] ? $_SESSION['user']->login : 'unauthorized',
            'user_ip' => $_SERVER['REMOTE_ADDR']]);
    }

    public function emergency(\Exception $e)
    {
        $message = $this->getMessage($e);
        $this->logger->emergency($message, ['user' => $_SESSION['user'] ? $_SESSION['user']->login : 'unauthorized',
            'user_ip' => $_SERVER['REMOTE_ADDR']]);
    }
}
