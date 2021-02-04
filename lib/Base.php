<?php

namespace lib;

class Base
{
    protected $request = [];
    protected $rules = [];
    protected $checkToken = true;

    public function __construct()
    {
        $this->initRequest();

        $this->checkToken();

        $this->checkRules();
    }

    protected function initRequest()
    {
        foreach ($_REQUEST as $key => $param) {
            $this->request[$key] = addslashes(trim($param));
        }
    }

    protected function checkToken()
    {
        if (!$this->checkToken) {
            return true;
        }

        if (empty($_SERVER['HTTP_TOKEN']) || strlen($_SERVER['HTTP_TOKEN']) < 32) {
            $this->error('auth error');
        }
    }

    protected function checkRules()
    {
        if (empty($this->rules)) {
            return true;
        }

        foreach ($this->rules as $param => $rule) {

            if (empty($rule) || !is_string($rule)) {
                continue;
            }

            $ruleArr = explode(',', trim($rule, ','));
            foreach ($ruleArr as $item) {

                $r = explode(':', trim($item, ':'));

                if ($r[0] == 'if') {
                    if (empty($r[1])) {
                        continue;
                    }

                    $rr = explode('=', $r[1]);
                    if (!isset($this->request[$rr[0]]) || $this->request[$rr[0]] != $rr[1]) {
                        break;
                    }

                } elseif ($r[0] == 'required') {
                    if (empty($this->request[$param])) {
                        $this->error("param {$param} required");
                    }

                } elseif ($r[0] == 'length') {
                    if (empty($this->request[$param])) {
                        continue;
                    }
                    if (empty($r[1])) {
                        continue;
                    }
                    if (strlen($this->request[$param]) != $r[1]) {
                        $this->error("param {$param} length must be {$r[1]}");
                    }

                } elseif ($r[0] == 'length_range') {
                    if (empty($this->request[$param])) {
                        continue;
                    }
                    if (empty($r[1])) {
                        continue;
                    }
                    $rr = explode('~', $r[1]);
                    if (count($rr) != 2 || $rr[0] >= $rr[1]) {
                        continue;
                    }
                    if (strlen($this->request[$param]) < $rr[0] || strlen($this->request[$param]) > $rr[1]) {
                        $this->error("param {$param} length must between {$rr[0]} and {$rr[1]}");
                    }
                }
            }
        }
    }

    /**
     * 返回json数据
     *
     * @param array $data
     * @return bool
     */
    protected function renderJson($data = []) {
        Log::info('request log', $_REQUEST);

        header('content-type: application/json');

        echo json_encode($data);

        return true;
    }

    protected function getRequestUri() {
        $request_uri_arr = explode('?', $_SERVER['REQUEST_URI']);

        return strtolower($request_uri_arr[0]);
    }

    protected function getAppPlatform() {
        return empty($_SERVER['HTTP_PLATFORM']) ? '' : strtolower($_SERVER['HTTP_PLATFORM']);
    }

    protected function getAppVersion() {
        return empty($_SERVER['HTTP_APPVERSION']) ? 0 : $_SERVER['HTTP_APPVERSION'];
    }

    /**
     * 输出json格式的错误信息
     *
     * @param string $msg
     * @param int $status
     * @return bool
     */
    protected function error($msg = 'server error', $status = 1)
    {
        Log::warning($msg, $_REQUEST);

        $result = [
            'status' => $status,
            'msg' => $msg,
            'data' => (object) []
        ];

        $this->renderJson($result);

        die;

//        return false;
    }

    /**
     * 返回成功的json数据
     *
     * @param array $data
     * @return bool
     */
    protected function success($data = [])
    {
        if (empty($data)) {
            $data = (object) [];
        }

        $result = [
            'status' => 0,
            'msg' => 'ok',
            'data' => $data
        ];

        $this->renderJson($result);

        die;
    }

    /**
     * html渲染
     *
     * @param $template
     * @param array $data
     * @return bool
     */
    protected function render($template, $data = [])
    {
        if (! empty($data)) {
            foreach ($data as $k => $d) {
                $$k = $d;
            }
        }

        header('content-type:text/html');

        require APP_PATH . 'view/'.$template.'.php';

        return true;
    }
}
