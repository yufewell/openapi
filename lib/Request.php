<?php
/**
 * File: Request.php.
 * User: yufewell
 * Date: 2020/12/6
 * Time: 16:37
 */

namespace lib;

class Request
{
    public $get = [];
    public $post = [];

    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->get = $this->initGet();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->post = $this->initPost();
        }
    }

    public function initGet()
    {
        $params = [];
        foreach ($_GET as $key => $param) {
            $params[$key] = addslashes(trim($param));
        }

//        if (! empty($_SERVER['HTTP_TOKEN'])) {
//            $params['token'] = trim($_SERVER['HTTP_TOKEN']);
//            $params['token'] = addslashes(trim($_SERVER['HTTP_TOKEN']));
//        }

        return $params;
    }

    public function initPost()
    {
        $params = [];
        foreach ($_POST as $key => $param) {
            $params[$key] = addslashes(trim($param));
        }

        return $params;
    }

    public function get($key = '')
    {
        if ($key == '') {
            return $this->get;
        }

        return $this->get[$key];
    }

    public function post($key = '')
    {
        if ($key == '') {
            return $this->post;
        }

        return $this->post[$key];
    }
}