<?php
/**
 * File: BaseService.php.
 * User: yufewell
 * Date: 2019/3/19
 * Time: 16:28
 */

class BaseService
{
    protected $msg = '';

    protected static $instance = [];

    /**
     * 设置信息
     *
     * @param $msg
     * @return bool
     */
    public function setMessage($msg)
    {
        $this->msg = $msg;

        return true;
    }

    /**
     * 获取信息
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->msg;
    }

    /**
     * 获取Service单例
     *
     * @return static
     */
    public static function instance()
    {
        $className = get_called_class();

        if (! isset(self::$instance[$className])) {
            self::$instance[$className] = new static();
        }

        return self::$instance[$className];
    }
}