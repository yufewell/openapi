<?php
/**
 * 错误处理类
 */
class ErrorHandler
{
    /**
     * 自定义错误处理
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public static function userErrorHandler($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
            case E_ERROR:               $level = "Error";                  break;
            case E_WARNING:             $level = "Warning";                break;
            case E_PARSE:               $level = "Parse Error";            break;
            case E_NOTICE:              $level = "Notice";                 break;
            case E_CORE_ERROR:          $level = "Core Error";             break;
            case E_CORE_WARNING:        $level = "Core Warning";           break;
            case E_COMPILE_ERROR:       $level = "Compile Error";          break;
            case E_COMPILE_WARNING:     $level = "Compile Warning";        break;
            case E_USER_ERROR:          $level = "User Error";             break;
            case E_USER_WARNING:        $level = "User Warning";           break;
            case E_USER_NOTICE:         $level = "User Notice";            break;
            case E_STRICT:              $level = "Strict Notice";          break;
            case E_RECOVERABLE_ERROR:   $level = "Recoverable Error";      break;
            default:                    $level = "Unknown error ($errno)"; break; 
        }
        $errorStr = "$errstr ".$errfile." 第 $errline 行";
        
        header('HTTP/1.1 500 Internal Server Error');

        Log::warning($level.$errorStr, $_REQUEST);
    }

    /**
     * php中止时执行的函数
     *
     */
    public static function shutdownHandler(){
        $e = error_get_last();
        switch ($e['type']) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                self::userErrorHandler($e['type'],$e['message'],$e['file'],$e['line']);
                break;         
        }
    }

    /**
     * 异常处理
     *
     * @param $e
     */
    public static function exceptionHandler($e) {
        self::userErrorHandler($e->getCode(),$e->getMessage(),$e->getFile(),$e->getLine());
    }
}
