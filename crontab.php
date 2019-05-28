<?php
/**
 * File: crontab.php.
 * User: yufewell
 * Date: 2019/3/8
 * Time: 10:57
 */

ini_set('max_execution_time', '0');

define('START_TIME_MS', intval(microtime(true) * 1000));
define('APPLICATION_PATH', dirname(__FILE__) . '/');
//define('APP_DEBUG', true);

require APPLICATION_PATH . 'lib/Function.php';

spl_autoload_register('autoload');

if (PHP_SAPI !== 'cli') {
    Log::warning('request not cli');
    die;
}

$params = $argv;
$params['method'] = $argv[1];

if (empty($params['method'])) {
    Log::warning('request method error', $params);
    die;
}

$class = ucfirst(strtolower($params['method']));

$classFile = APPLICATION_PATH . 'crontab/' . $class . '.php';

if (! file_exists($classFile)) {
    Log::warning('request crontab error', $params);
    die;
}

set_error_handler('ErrorHandler::userErrorHandler');
set_exception_handler('ErrorHandler::exceptionHandler');
register_shutdown_function('ErrorHandler::shutdownHandler');

require $classFile;

if ((new $class())->index()) {
    Log::info('request_log', $params);
}
