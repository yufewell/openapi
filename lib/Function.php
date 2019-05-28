<?php

if (! function_exists('autoload')) {

    function autoload($className)
    {
        if (strpos($className, 'Service') !== false) {
            require_once APPLICATION_PATH . 'service/' . $className . '.php';
        } else {
            require_once APPLICATION_PATH . 'lib/' . $className . '.php';
        }
    }
}


if (! function_exists('get_config')) {

    function get_config()
    {
        return require APPLICATION_PATH . 'config.php';
    }
}


if (! function_exists('get_params')) {

    function get_params($params = [])
    {
        $params = empty($params) ? array_merge($_GET, $_POST) : $params;

        foreach ($params as &$param) {
            $param = addslashes(trim($param));
        }

        if (! empty($_SERVER['HTTP_TOKEN'])) {
            $params['token'] = trim($_SERVER['HTTP_TOKEN']);
//            $params['token'] = addslashes(trim($_SERVER['HTTP_TOKEN']));
        }

        return $params;
    }
}