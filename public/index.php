<?php

define('START_TIME_MS', intval(microtime(true) * 1000));
define('APPLICATION_PATH', dirname(__FILE__) . '/../');

//require APPLICATION_PATH . 'lib/Log.php';
require APPLICATION_PATH . 'lib/Function.php';

spl_autoload_register('autoload');

$params = get_params();

if (empty($params['method'])) {
	header('HTTP/1.1 404 Not Found');
	Log::warning('request method error', $params);
	die;
}

$class = ucfirst(strtolower($params['method']));

$classFile = APPLICATION_PATH . 'api/' . $class . '.php';

if (! file_exists($classFile)) {
	header('HTTP/1.1 404 Not Found');
	Log::warning('request api error', $params);
	die;
}

//require APPLICATION_PATH . 'lib/Base.php';
//require APPLICATION_PATH . 'lib/ErrorHandler.php';
//require APPLICATION_PATH . 'lib/DB.php';

set_error_handler('ErrorHandler::userErrorHandler');
set_exception_handler('ErrorHandler::exceptionHandler');
register_shutdown_function('ErrorHandler::shutdownHandler');

require $classFile;

if ((new $class())->index($params)) {
    Log::info('request_log', $params);
}
