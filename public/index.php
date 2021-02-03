<?php
try {
    define('START_TIME_MS', microtime(true) * 1000);
    define('APP_PATH', dirname(__FILE__) . '/../');

    require APP_PATH . 'lib/function.php';

    spl_autoload_register('autoload');

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

    $class = ucfirst($uriArr[1]);

    $classFile = APP_PATH . 'api/' . $uriArr[0] . '/' . $class . '.php';

    if (!file_exists($classFile)) {
        header('HTTP/1.1 404 Not Found');
        lib\Log::warning('api not found', $_REQUEST);
        die;
    }

    require $classFile;

    (new $class())->controller();

} catch (Exception $exception) {
    header('HTTP/1.1 500 Service Error');
    lib\Log::warning($exception->getMessage() . ', File: ' . $exception->getFile() . ', Line: ' . $exception->getLine(), $_REQUEST);
    die;
}
lib\Log::info('request log', $_REQUEST);