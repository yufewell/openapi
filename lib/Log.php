<?php

namespace lib;

class Log
{
    /**
     * 记录日志
     *
     * @param string $msg
     * @param array $data
     * @param string $level
     * @return bool
     */
    public static function write($msg = 'System Log', $data = [], $level = '') {
        $now = date('Y-m-d H:i:s');

        $destination = self::getLogFile($level);
        $log_dir = dirname($destination);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }

        $dataStr = '';
        if (is_array($data) && !empty($data)) {
            $dataStr = json_encode($data);
        }
        
        $dataStr = rtrim($dataStr, ', ');
        $text = 'msg: ' .$msg;
        if (!empty($dataStr)) {
            $text .= ', data: ' . $dataStr;
        }

        $timeCost = sprintf("timeCost:%.4fms", microtime(true) * 1000 - START_TIME_MS);
        $ip = 'ip: '. (empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR']);
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $client = (empty($_SERVER['HTTP_PLATFORM']) ? '' : $_SERVER['HTTP_PLATFORM']);

        return self::writeFileLog($destination, "{$now} uri: " .$uri. " client: ".$client
            ." {$text} {$ip} {$timeCost}\r\n");
    }

    /**
     * 记录到文件
     *
     * @param string $destination
     * @param string $content
     * @return bool
     */
    public static function writeFileLog($destination = '', $content = 'default message') {
        return error_log($content, 3, $destination);
    }

    /**
     * 日志文件路径
     *
     * @param $level
     * @return string
     */
    private static function getLogFile($level) {
        $ext = '.log';

        if ($level === 'WARN') {
            $ext = '.wf.log';
        }

        return APP_PATH . 'log/' . date('/Y-m/') . date('dH') . $ext;
    }

    /**
     * info
     *
     * @param string $msg
     * @param array $data
     * @return bool
     */
    public static function info($msg = '', $data = [])
    {
        return self::write($msg, $data, 'INFO');
    }

    /**
     * notice
     *
     * @param string $msg
     * @param array $data
     * @return bool
     */
    public static function notice($msg = '', $data = [])
    {
        return self::write($msg, $data, 'NOTICE');
    }

    /**
     * warn
     *
     * @param string $msg
     * @param array $data
     * @return bool
     */
    public static function warning($msg = '', $data = [])
    {
        return self::write($msg, $data, 'WARN');
    }
}