<?php
error_reporting( E_ERROR );
try{
    require $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'app/autoload.php';
    require $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

    AppAutoloader::register();
    $fk = \app\Core\FrontController::getInstance();
    $fk->run();
}catch (\Exception $e) {
    \app\Components\Logger::getInstance()->critical($e);
    header("HTTP/1.0 500 Internal Server Error");
    exit('Internal Server Error.');
}


