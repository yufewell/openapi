<?php

if (! function_exists('autoload')) {

    function autoload($className)
    {
        require_once APP_PATH . str_replace('\\', '/', $className) . '.php';

//        if (strpos($className, 'service') !== false) {
//            require_once APP_PATH . $className . '.php';
//        } elseif (strpos($className, 'lib') !== false) {
//            require_once APP_PATH . $className . '.php';
//        } elseif (strpos($className, 'service') !== false) {
//            require_once APP_PATH . 'service/' . $className . '.php';
//        }  {
//            if (file_exists(APP_PATH . 'model/' . $className . '.php')) {
//                require_once APP_PATH . 'model/' . $className . '.php';
//            } else {
//                require_once APP_PATH . 'lib/' . $className . '.php';
//            }
//        }
    }
}


if (! function_exists('config')) {

    function config($key = '', $value = null)
    {
        static $config;

        if (empty($config)) {
            $config = require_once APP_PATH . 'config.php';
        }

        if ($value) {
            $config[$key] = $value;
            return true;
        }

        if ($key) {
            return $config[$key] ?? null;
        }

        return $config;
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