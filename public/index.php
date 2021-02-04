<?php
try {
    define('START_TIME_MS', microtime(true) * 1000);
    define('APP_PATH', dirname(__FILE__) . '/../');

    require APP_PATH . 'lib/function.php';

    spl_autoload_register('autoload');

    register_shutdown_function('\lib\ErrorHandler::shutdownHandler');
    set_error_handler('\lib\ErrorHandler::userErrorHandler');
//    set_exception_handler('\lib\ErrorHandler::exceptionHandler');

    if (strpos($_SERVER['REQUEST_URI'], '?') === false) {
        $uriArr = explode('/', trim(substr($_SERVER['REQUEST_URI'], 0), '/'));
    } else {
        $uriArr = explode('/', trim(substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')), '/'));
    }

    if (empty($uriArr[0]) || empty($uriArr[1])) {
        header('HTTP/1.1 404 Not Found');
        lib\Log::warning('request error', $_REQUEST);
        die;
    }

    $class = '\api\\' . $uriArr[0] . '\\' . ucfirst($uriArr[1]);

//    require APP_PATH . 'api/' . $uriArr[0] . '/' . $class . '.php';

//    if (!file_exists($classFile)) {
//        header('HTTP/1.1 404 Not Found');
//        lib\Log::warning('api not found', $_REQUEST);
//        die;
//    }
//
//    var_dump(config());
//    config('aaa', 'bbb');
//    var_dump(config('aaa'));
//    die;
//
//    require $classFile;

    (new $class)->controller();

//    lib\Log::info('request log', $_REQUEST);

} catch (Exception $exception) {
    header('HTTP/1.1 500 Service Error');
    lib\Log::warning($exception->getMessage() . ', File: ' . $exception->getFile() . ', Line: ' . $exception->getLine(), $_REQUEST);
    die;
}
