<?php

namespace lib;

class Base
{
    /**
     * 返回json数据
     *
     * @param array $data
     * @return bool
     */
    protected function renderJson($data = []) {
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
            'data' => (object) [],
        ];

        $this->renderJson($result);

        return false;
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
            'msg' => 'success',
            'data' => $data,
        ];

        return $this->renderJson($result);
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
